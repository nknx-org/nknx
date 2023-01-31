<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletSnapshot;
use App\Models\BlockchainTransaction;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;

use Inertia\Inertia;

use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\ValidationException;

use Session;
use Illuminate\Support\Facades\Cache;
use DB;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $paginate = Request::get('per_page', 25);
        $pageSize = min($paginate, 100);
        $aggregate = Request::get('aggregate', 'hour');

        $wallet_ids = Auth::user()->wallets()->pluck('wallets.id')->toArray();

        $sumWalletSnapshots = [];

        if ($wallet_ids) {
            $rawQuery = "SELECT  date_trunc('" . $aggregate . "', s.created_at) as date, sum(CAST (s.balance AS DOUBLE PRECISION)) as balance ";
            $rawQuery .= "from wallet_snapshots s inner join ( ";
            $rawQuery .= "   select wallet_id, max(created_at) maxdate ";
            $rawQuery .= "   from wallet_snapshots ";
            $rawQuery .= "   where wallet_id in (" . implode(",", $wallet_ids) . ") ";
            $rawQuery .= "   group by wallet_id, date_trunc('" . $aggregate . "', created_at) ";
            $rawQuery .= "    ) g on g.wallet_id = s.wallet_id and g.maxdate = s.created_at ";
            $rawQuery .= "group by 1 ";
            $rawQuery .= "order by 1 ";
            $rawQuery .= "limit 72 ";
            $sumWalletSnapshots = DB::select(DB::raw($rawQuery));
        }

        return Inertia::render('Wallets/Index', [
            'filters' => Request::all('search'),
            'sumWalletSnapshots' => $sumWalletSnapshots,
            'wallets' => Auth::user()->wallets()
                ->orderBy('created_at', 'desc')
                ->filter(Request::only('search'))
                ->paginate($pageSize)
                ->withQueryString()
                ->through(function ($wallet) {
                    return [
                        'id' => $wallet->id,
                        'label' => $wallet->pivot->label,
                        'address' => $wallet->address,
                        'balance' => $wallet->balance,
                        'snapshots' => $wallet->wallet_snapshots ? $wallet->wallet_snapshots : null,
                    ];
                }),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Request::validate([
            'address' => ['required'],
        ]);

        $user = Auth::user();

        $address = Request::get('address');
        $walletAddresses = explode(',', $address);

        $label = Request::input('label', '');
        $multiWalletAdresses = [];
        $failedWalletAdresses = [];
        $savedWalletAdresses = [];

        foreach ($walletAddresses as $walletAddress) {

            //check if Wallet is already in Database
            $wallet = Wallet::where('address', $walletAddress)->first();

            if ($wallet) {
                //check if Wallet-ID is already attached to user
                if (!$user->wallets->contains($wallet->id)) {
                    $user->wallets()->attach($wallet->id, ['label' => $label]);
                    array_push($savedWalletAdresses, $wallet);
                } else {
                    array_push($multiWalletAdresses, $walletAddress);
                }
            } else {
                try {
                    $apiRequest = Http::timeout(3)->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ])->post(config('nkn.remote-node.address') . ':' . config('nkn.remote-node.port'),[
                        "id" => 1,
                        "method" => "getbalancebyaddr",
                        "params" => [
                            "address" => $walletAddress,
                        ],
                        "jsonrpc" => "2.0"
                    ]);
                    $response = json_decode($apiRequest->body(), true, 512, JSON_BIGINT_AS_STRING);

                    if (!array_key_exists("error", $response)) {
                        $wallet = new Wallet([
                            "address" => $walletAddress,
                            "balance" => $response["result"]["amount"],
                        ]);
                        $wallet->save();
                        $user->wallets()->attach($wallet->id, ['label' => $label]);

                        $date = new \DateTime();

                        WalletSnapshot::updateOrCreate(
                            ['wallet_id' => $wallet->id, 'created_at' => $date->format("Y-m-d")],
                            ["balance" => $response["result"]["amount"]]
                        );



                        array_push($savedWalletAdresses, $walletAddress);
                    } else {

                        array_push($failedWalletAdresses, $walletAddress);
                    }
                } catch (\Exception $e) {
                    array_push($failedWalletAdresses, $walletAddress);
                }
            }
        }

        if((count($savedWalletAdresses) || count($multiWalletAdresses)) && !count($failedWalletAdresses)){
            Cache::forget('wallet-sum-history-user-' . Auth::user()->id);
            if(count($savedWalletAdresses) + count($multiWalletAdresses) >1){
                return Redirect::back()->with('success', 'Wallets added');
            } else{
                return Redirect::back()->with('success', 'Wallet added');
            }

        } else{
            throw ValidationException::withMessages([
                'address' => [
                    'Failed to add some wallets - please check your input.',
                ],
                'failed' => implode(',',$failedWalletAdresses)
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function show(Wallet $wallet)
    {
        $walletObj = Auth::user()->wallets()->where('wallets.id',$wallet->id)->with('wallet_snapshots')->first();
        $paginate = Request::get('per_page', 25);
        $pageSize = min($paginate, 100);


        if ($walletObj){
            return Inertia::render('Wallets/Show',[
                'wallet' =>  [
                        'id' => $wallet->id,
                        'address' => $wallet->address,
                        'balance' => $wallet->balance,
                        'label' => $wallet->pivot_label,
                        'snapshots' =>  $wallet->wallet_snapshots ? $wallet->wallet_snapshots : null,
                ],
                'transactions' => BlockchainTransaction::where('recipientWallet', $walletObj->address)->orWhere('senderWallet', $walletObj->address)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10,['*'], 'tx_page')
                    ->through(function ($tx) {
                        return [
                            'hash' => $tx->hash,
                            'block_height' => $tx->block_height,
                            'txType' => $tx->txType,
                            'created_at' => $tx->created_at,
                        ];
                    }),
                'wallets' => Auth::user()->wallets()
                    ->with('wallet_snapshots')
                    ->orderBy('created_at', 'desc')
                    ->filter(Request::only('search'))
                    ->paginate($pageSize)
                    ->withQueryString()
                    ->through(function ($wallet) {
                        return [
                            'id' => $wallet->id,
                            'label' => $wallet->pivot->label,
                            'address' => $wallet->address,
                            'balance' => $wallet->balance,
                            'snapshots' => $wallet->wallet_snapshots ? $wallet->wallet_snapshots : null,
                        ];
                    }),
            ]);
        } else{
            return redirect('wallets.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function edit(Wallet $wallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Wallet $wallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wallet $wallet)
    {
        $walletObj = Auth::user()->wallets()->find($wallet);

        if ($walletObj) {
            Cache::forget('wallet-sum-history-user-' . Auth::user()->id);
            Auth::user()->wallets()->detach($walletObj->id);
            if (count($walletObj->users) == 0) {
                $walletObj->delete();
            }
            return Redirect::back()->with('success', 'Wallet deleted.');
        } else {
            return Redirect::back()->with('error', 'Resource not found');
        }
    }


    /**
     * Remove all wallets from the database.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyAll()
    {
        Cache::forget('wallet-sum-history-user-' . Auth::user()->id);
        Auth::user()->wallets()->detach();
        return Redirect::back()->with('success', 'Wallets deleted.');
    }
}
