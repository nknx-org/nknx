<?php

namespace App\Console\Commands;

use Carbon\Carbon;

use Illuminate\Console\Command;
use App\Jobs\ProcessRemoteBlock;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Http;
use App\Models\BlockchainTransaction;
use Illuminate\Support\Facades\Queue;

class SyncWithBlockChain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blockchain:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes database with blockchain';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $seconds = config('nkn.update-interval');
        $dt = Carbon::now();
        $x=60/$seconds;

        do{

            // If there is no other blockchainCrawler-job active
            if (Queue::size('blockchainCrawler') == 0) {

                    $currentBlockchainHeight = 0;

                    try {

                        $apiRequest = Http::timeout(3)->withHeaders([
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json'
                        ])->post(config('nkn.remote-node.address') . ':' . config('nkn.remote-node.port'),[
                            "id" => 1,
                            "method" => "getlatestblockheight",
                            "params" => [
                                "height" => "0",
                            ],
                            "jsonrpc" => "2.0"
                        ]);



                        // get the latest block height from the configured remote node
                        $response = json_decode($apiRequest->body());
                        $currentBlockchainHeight = $response->result;
                    } catch (\Exception $e) {
                        Log::channel('syncWithBlockchain')->error("Can't connect to mainnet-node!");
                        throw $e;
                    }

                    // get latest blockheight in the database
                    $localBlockHeight = BlockchainTransaction::select('block_height')->orderBy('block_height', 'desc')->first();
                    if ($localBlockHeight) {
                        $localBlockHeight = $localBlockHeight->block_height;
                    } else {
                        $localBlockHeight = -1;
                    }

                    //push only the new Blocks to the processing queue
                    for ($i = $localBlockHeight + 1; $i <= $currentBlockchainHeight; $i++) {
                        ProcessRemoteBlock::dispatch($i,true)->onQueue('blockchainCrawler');
                    }
            }
            time_sleep_until($dt->addSeconds($seconds)->timestamp);

        } while($x-- > 1);
    }
}
