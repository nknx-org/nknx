<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\WalletSnapshot;
use App\Models\NodeSnapshot;

use Inertia\Inertia;

use Illuminate\Support\Facades\Cache;
use Auth;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $mediumNews = Cache::remember('medium-news', 21600, function () {
            $apiRequest = Http::get('https://api.rss2json.com/v1/api.json?rss_url=https://medium.com/feed/nknetwork');
            return json_decode($apiRequest->body(), true);
        });


        $blockchainSummary = Cache::remember('blockchain-summary', 60, function () {

            $apiRequest = Http::get('https://openapi.nkn.org/api/v1/statistics/counts');
            $response = json_decode($apiRequest->body(), true, 512, JSON_BIGINT_AS_STRING);

            return [
                'blockchainSummary'      => $response
            ];
        });


        $historyData = Cache::remember('history-data', 10800, function () {

            $apiRequest = Http::get('https://api.coingecko.com/api/v3/coins/nkn/market_chart?vs_currency=usd&days=30&interval=daily');
            $response = json_decode($apiRequest->body(), true, 512, JSON_BIGINT_AS_STRING);
            return $response;
        });

        $walletSumHistory = [];
        $nodeMinedHistory = [];

        $walletSumHistory = Cache::remember('wallet-sum-history-user-' . Auth::user()->id, 60, function () {
            $wallet_ids = Auth::user()->wallets()->get()->pluck('id')->toArray();
            return WalletSnapshot::groupBy('created_at')
                ->whereIn('wallet_id', $wallet_ids)
                ->selectRaw('sum(balance) as sum, created_at')
                ->orderBy('created_at', 'asc')
                ->get();
        });


        $nodeMinedHistory = Cache::remember('node-mined-history-user-' . Auth::user()->id, 60, function () {
            $wallet_ids = Auth::user()->nodes()->get()->pluck('id')->toArray();
            return NodeSnapshot::selectRaw('sum(mined) as sum, sum(mined_amount) as sum_mined_amount, date_trunc(\'day\',created_at) as created_at')
                ->groupByRaw('3')
                ->whereIn('node_id', $wallet_ids)
                ->orderBy('created_at', 'asc')
                ->get();
        });







        return Inertia::render('Dashboard', array_merge([
            'mediumNews' => isset($mediumNews['items']) ? $mediumNews['items'] : null,
            'userPerformanceData'   => [
                'activeNodes'       =>  Auth::user()->nodes()->count()
            ],
            'historyData'           => $historyData,
            'walletSumHistory'      => $walletSumHistory,
            'nodeMinedHistory'      => $nodeMinedHistory,
        ], $blockchainSummary));
    }
}
