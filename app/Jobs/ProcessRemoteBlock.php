<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\BlockchainTransaction;

use App\Helpers\PubKey2Wallet;

use App\Models\Node;
use App\Models\NodeSnapshot;

use App\Models\Wallet;
use App\Jobs\SnapshotWallet;
use App\Jobs\UpdateWallet;

use Carbon\Carbon;

use App\Notifications\NodeMined;
use App\Notifications\IdGenerated;
use App\Notifications\ReceiveNkn;
use App\Notifications\SendNkn;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class ProcessRemoteBlock implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $blockheight;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($blockheight)
    {
        $this->blockheight = $blockheight;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        try {
            // Try to get the whole block with all data
            $apiRequest = Http::timeout(3)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post(config('nkn.remote-node.address') . ':' . config('nkn.remote-node.port'), [
                "id" => 1,
                "method" => "getblock",
                "params" => [
                    "height" => (int) $this->blockheight,
                ],
                "jsonrpc" => "2.0"
            ]);


            $response = json_decode($apiRequest->body(), true, 512, JSON_BIGINT_AS_STRING);


            // Global data
            $wallet = PubKey2Wallet::encode($response["result"]["header"]["signerPk"]);

            foreach ($response["result"]["transactions"] as $transaction) {

                // Now we will deserialise the payload...
                // Depending on the payload-type we will store and extract different attributes...
                // For more check https://github.com/nknorg/nkn/blob/master/pb/transaction.proto

                switch ($transaction["txType"]) {
                    case "COINBASE_TYPE":
                        $protoCoinbase = new \Protos\Coinbase;
                        try {
                            $protoCoinbase->mergeFromString(hex2bin($transaction["payloadData"]));

                            $blockchainTransactionObj = new BlockchainTransaction([
                                'hash'              => $transaction["hash"],
                                'txType'            => $transaction["txType"],
                                'block_height'      => $response["result"]["header"]["height"],
                                'senderWallet'      => $wallet,
                                'recipientWallet'   => PubKey2Wallet::programHashToAddress(bin2hex($protoCoinbase->getRecipient())),
                                'reward'            => $protoCoinbase->getAmount(),
                                'signerPk'          => $response["result"]["header"]["signerPk"],
                                'created_at'        => $response["result"]["header"]["timestamp"],
                            ]);
                            $blockchainTransactionObj->save();

                            // Events when mined

                            // 1. increment mined counter
                            $node = Node::where('publicKey', $response["result"]["header"]["signerPk"])->first();
                            if ($node) {

                                //check each user if we need to do a snapshot at all
                                foreach ($node->users()->get() as $user) {

                                    $user->notify(new NodeMined($node));
                                }

                                //do snapshot
                                $snapshot = NodeSnapshot::firstOrCreate(
                                    ['node_id' => $node->id, 'created_at' => Carbon::createFromTimestamp($response["result"]["header"]["timestamp"])->toDateTimeString()],
                                    ["mined" => 1, "mined_amount" => $protoCoinbase->getAmount()]
                                );
                                if (!$snapshot->wasRecentlyCreated) {
                                    // "firstOrCreate" found the user in the DB and fetched it.
                                    $snapshot->mined_amount = $snapshot->mined_amount + $protoCoinbase->getAmount();
                                    $snapshot->mined = $snapshot->mined + 1;
                                    $snapshot->save();
                                }
                            }

                            $wallets = Wallet::where('address', PubKey2Wallet::programHashToAddress(bin2hex($protoCoinbase->getRecipient())))
                                ->orWhere('address', $wallet)
                                ->get();
                            foreach ($wallets as $wallet) {
                                UpdateWallet::dispatch($wallet);
                                SnapshotWallet::dispatch($wallet);
                            }

                            //
                            //
                            //
                            //

                        } catch (\Exception $e) {
                            Log::channel('syncWithBlockchain')->error("Error processing [COINBASE_TYPE] payloadData: " . $transaction["payloadData"] . ": " . $e);
                        }

                        break;

                    case "TRANSFER_ASSET_TYPE":
                        $protoTransferAsset = new \Protos\TransferAsset;
                        try {
                            $protoTransferAsset->mergeFromString(hex2bin($transaction["payloadData"]));

                            $blockchainTransactionObj = new BlockchainTransaction([
                                'hash'              => $transaction["hash"],
                                'txType'            => $transaction["txType"],
                                'block_height'      => $response["result"]["header"]["height"],
                                'senderWallet'      => PubKey2Wallet::programHashToAddress(bin2hex($protoTransferAsset->getSender())),
                                'recipientWallet'   => PubKey2Wallet::programHashToAddress(bin2hex($protoTransferAsset->getRecipient())),
                                'reward'            => null,
                                'signerPk'          => null,
                                'created_at'        => $response["result"]["header"]["timestamp"],
                            ]);
                            $blockchainTransactionObj->save();

                            // Events when mined
                            //
                            $wallets = Wallet::where('address', PubKey2Wallet::programHashToAddress(bin2hex($protoTransferAsset->getSender())))
                                ->orWhere('address', PubKey2Wallet::programHashToAddress(bin2hex($protoTransferAsset->getRecipient())))
                                ->get();
                            foreach ($wallets as $wallet) {
                                UpdateWallet::dispatch($wallet);
                                SnapshotWallet::dispatch($wallet);

                                foreach ($wallet->users()->get() as $user) {
                                    if ($wallet->addr == PubKey2Wallet::programHashToAddress(bin2hex($protoTransferAsset->getRecipient()))) {
                                        $user->notify(new ReceiveNkn($wallet, $protoTransferAsset->getAmount(), PubKey2Wallet::programHashToAddress(bin2hex($protoTransferAsset->getSender()))));
                                    }
                                    if ($wallet->addr == PubKey2Wallet::programHashToAddress(bin2hex($protoTransferAsset->getSender()))) {
                                        $user->notify(new SendNkn($wallet, $protoTransferAsset->getAmount(), PubKey2Wallet::programHashToAddress(bin2hex($protoTransferAsset->getRecipient()))));
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            Log::channel('syncWithBlockchain')->error("Error processing [TRANSFER_ASSET_TYPE] payloadData: " . $transaction["payloadData"] . ": " . $e);
                        }
                        break;


                    case "REGISTER_NAME_TYPE":
                        $protoRegisterName = new \Protos\RegisterName;
                        try {
                            $protoRegisterName->mergeFromString(hex2bin($transaction["payloadData"]));

                            $blockchainTransactionObj = new BlockchainTransaction([
                                'hash'              => $transaction["hash"],
                                'txType'            => $transaction["txType"],
                                'block_height'      => $response["result"]["header"]["height"],
                                'senderWallet'      => PubKey2Wallet::encode(bin2hex($protoRegisterName->getRegistrant())),
                                'recipientWallet'   => null,
                                'reward'            => null,
                                'signerPk'          => null,
                                'created_at'        => $response["result"]["header"]["timestamp"],
                            ]);
                            $blockchainTransactionObj->save();

                            // Events when mined
                            //
                            $wallets = Wallet::where('address', PubKey2Wallet::encode(bin2hex($protoRegisterName->getRegistrant())))
                                ->get();
                            foreach ($wallets as $wallet) {
                                UpdateWallet::dispatch($wallet);
                                SnapshotWallet::dispatch($wallet);
                            }
                        } catch (\Exception $e) {
                            Log::channel('syncWithBlockchain')->error("Error processing [REGISTER_NAME_TYPE] payloadData: " . $transaction["payloadData"] . ": " . $e);
                        }

                        break;

                    case "TRANSFER_NAME_TYPE":
                        $protoTransferName = new \Protos\TransferName;
                        try {
                            $protoTransferName->mergeFromString(hex2bin($transaction["payloadData"]));

                            $blockchainTransactionObj = new BlockchainTransaction([
                                'hash'              => $transaction["hash"],
                                'txType'            => $transaction["txType"],
                                'block_height'      => $response["result"]["header"]["height"],
                                'senderWallet'      => PubKey2Wallet::encode(bin2hex($protoTransferName->getRegistrant())),
                                'recipientWallet'   => PubKey2Wallet::encode($protoTransferName->getRecipient()),
                                'reward'            => null,
                                'signerPk'          => null,
                                'created_at'        => $response["result"]["header"]["timestamp"],
                            ]);
                            $blockchainTransactionObj->save();

                            // Events when mined
                            //
                            //
                            //
                            //

                        } catch (\Exception $e) {
                            Log::channel('syncWithBlockchain')->error("Error processing [TRANSFER_NAME_TYPE] payloadData: " . $transaction["payloadData"] . ": " . $e);
                        }
                        break;

                    case "DELETE_NAME_TYPE":
                        $protoDeleteName = new \Protos\DeleteName;
                        try {
                            $protoDeleteName->mergeFromString(hex2bin($transaction["payloadData"]));

                            $blockchainTransactionObj = new BlockchainTransaction([
                                'hash'              => $transaction["hash"],
                                'txType'            => $transaction["txType"],
                                'block_height'      => $response["result"]["header"]["height"],
                                'senderWallet'      => PubKey2Wallet::encode(bin2hex($protoDeleteName->getRegistrant())),
                                'recipientWallet'   => null,
                                'reward'            => null,
                                'signerPk'          => null,
                                'created_at'        => $response["result"]["header"]["timestamp"],
                            ]);
                            $blockchainTransactionObj->save();

                            // Events when mined
                            //
                            //
                            //
                            //

                        } catch (\Exception $e) {
                            Log::channel('syncWithBlockchain')->error("Error processing [DELETE_NAME_TYPE] payloadData: " . $transaction["payloadData"] . ": " . $e);
                        }

                        break;

                    case "SUBSCRIBE_TYPE":
                        $protoSubscribe = new \Protos\Subscribe;

                        try {
                            $protoSubscribe->mergeFromString(hex2bin($transaction["payloadData"]));

                            $blockchainTransactionObj = new BlockchainTransaction([

                                'hash'              => $transaction["hash"],
                                'txType'            => $transaction["txType"],
                                'block_height'      => $response["result"]["header"]["height"],
                                'senderWallet'      => PubKey2Wallet::encode(bin2hex($protoSubscribe->getSubscriber())),
                                'recipientWallet'   => null,
                                'reward'            => null,
                                'signerPk'          => null,
                                'created_at'        => $response["result"]["header"]["timestamp"],
                            ]);
                            $blockchainTransactionObj->save();
                        } catch (\Exception $e) {
                            Log::channel('syncWithBlockchain')->error("Error processing [SUBSCRIBE_TYPE] payloadData: " . $transaction["payloadData"] . ": " . $e);
                        }

                        break;

                    case "UNSUBSCRIBE_TYPE":
                        $protoUnsubscribe = new \Protos\Unsubscribe;

                        try {
                            $protoUnsubscribe->mergeFromString(hex2bin($transaction["payloadData"]));

                            $blockchainTransactionObj = new BlockchainTransaction([

                                'hash'              => $transaction["hash"],
                                'txType'            => $transaction["txType"],
                                'block_height'      => $response["result"]["header"]["height"],
                                'senderWallet'      => PubKey2Wallet::encode(bin2hex($protoUnsubscribe->getSubscriber())),
                                'recipientWallet'   => null,
                                'reward'            => null,
                                'signerPk'          => null,
                                'created_at'        => $response["result"]["header"]["timestamp"],
                            ]);
                            $blockchainTransactionObj->save();
                        } catch (\Exception $e) {
                            Log::channel('syncWithBlockchain')->error("Error processing [UNSUBSCRIBE_TYPE] payloadData: " . $transaction["payloadData"] . ": " . $e);
                        }
                        break;

                    case "GENERATE_ID_TYPE":
                        $protoGenerateID = new \Protos\GenerateID;
                        try {
                            $protoGenerateID->mergeFromString(hex2bin($transaction["payloadData"]));
                            $publicKey = bin2hex($protoGenerateID->getPublicKey());

                            $blockchainTransactionObj = new BlockchainTransaction([
                                'hash'              => $transaction["hash"],
                                'txType'            => $transaction["txType"],
                                'block_height'      => $response["result"]["header"]["height"],
                                'senderWallet'      => PubKey2Wallet::programHashToAddress(bin2hex($protoGenerateID->getSender())),
                                'recipientWallet'   => PubKey2Wallet::encode($publicKey),
                                'reward'            => null,
                                'signerPk'          => null,
                                'created_at'        => $response["result"]["header"]["timestamp"],
                            ]);
                            $blockchainTransactionObj->save();

                            // Events when generated ID
                            //
                            $wallets = Wallet::where('address', PubKey2Wallet::programHashToAddress(bin2hex($protoGenerateID->getSender())))
                                ->orWhere('address', PubKey2Wallet::encode($publicKey))
                                ->get();
                            foreach ($wallets as $wallet) {
                                UpdateWallet::dispatch($wallet);
                                SnapshotWallet::dispatch($wallet);
                            }

                            $node = Node::where('walletAddress', PubKey2Wallet::encode($publicKey))->first();
                            if ($node) {
                                foreach ($node->users()->get() as $user) {
                                    $user->notify(new IdGenerated($node));
                                }
                            }
                            //


                        } catch (\Exception $e) {
                            Log::channel('syncWithBlockchain')->error("Error processing [GENERATE_ID_TYPE] payloadData: " . $transaction["payloadData"] . ": " . $e);
                        }
                        break;

                    case "NANO_PAY_TYPE":
                        $protoNanoPay = new \Protos\NanoPay;
                        try {
                            $protoNanoPay->mergeFromString(hex2bin($transaction["payloadData"]));

                            $blockchainTransactionObj = new BlockchainTransaction([
                                'hash'              => $transaction["hash"],
                                'txType'            => $transaction["txType"],
                                'block_height'      => $response["result"]["header"]["height"],
                                'senderWallet'      => PubKey2Wallet::programHashToAddress(bin2hex($protoNanoPay->getSender())),
                                'recipientWallet'   => PubKey2Wallet::programHashToAddress(bin2hex($protoNanoPay->getRecipient())),
                                'reward'            => null,
                                'signerPk'          => null,
                                'created_at'        => $response["result"]["header"]["timestamp"],
                            ]);
                            $blockchainTransactionObj->save();

                            // Events when mined
                            //
                            $wallets = Wallet::where('address', PubKey2Wallet::programHashToAddress(bin2hex($protoNanoPay->getSender())))
                                ->orWhere('address', PubKey2Wallet::programHashToAddress(bin2hex($protoNanoPay->getRecipient())))
                                ->get();
                            foreach ($wallets as $wallet) {
                                UpdateWallet::dispatch($wallet);
                                SnapshotWallet::dispatch($wallet);
                            }
                        } catch (\Exception $e) {
                            Log::channel('syncWithBlockchain')->error("Error processing [NANO_PAY_TYPE] payloadData: " . $transaction["payloadData"] . ": " . $e);
                        }
                        break;

                    case "ISSUE_ASSET_TYPE":
                        $protoIssueAsset = new \Protos\IssueAsset;
                        try {
                            $protoIssueAsset->mergeFromString(hex2bin($transaction["payloadData"]));

                            $blockchainTransactionObj = new BlockchainTransaction([
                                'hash'              => $transaction["hash"],
                                'txType'            => $transaction["txType"],
                                'block_height'      => $response["result"]["header"]["height"],
                                'senderWallet'      => PubKey2Wallet::programHashToAddress(bin2hex($protoIssueAsset->getSender())),
                                'recipientWallet'   => null,
                                'reward'            => null,
                                'signerPk'          => null,
                                'created_at'        => $response["result"]["header"]["timestamp"],
                            ]);
                            $blockchainTransactionObj->save();

                            // Events when mined
                            //
                            //
                            //
                            //



                        } catch (\Exception $e) {
                            Log::channel('syncWithBlockchain')->error("Error processing payloadData: " . $transaction["payloadData"] . ": " . $e);
                        }
                        break;
                }
            }
        } catch (\Exception $e) {
            Log::channel('syncWithBlockchain')->error("ProcessRemoteBlock: Can't connect to testnet-node!");
            throw $e;
        }
    }
    public function tags()
    {
        return ['ProcessRemoteBlock', $this->blockheight];
    }
}
