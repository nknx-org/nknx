<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {


        $currentVersion = Cache::remember('current-version', 3600, function(){

            $apiRequest = Http::get('https://api.github.com/repos/nknorg/nkn/releases');
            $response = json_decode($apiRequest->body(), true, 512, JSON_BIGINT_AS_STRING);

            return $response[0]["name"];
        });

        $nodeCounts = Cache::remember('node-counts', 300, function(){


            $geoSummary = Cache::remember('node-geo-summary',300,function(){
                try {
                    $apiRequest = Http::get('https://api.nkn.org/v1/geo/summary');
                    return json_decode($apiRequest->body(), true, 512, JSON_BIGINT_AS_STRING);
                } catch (\Exception $e) {
                    $dummyArray = [];
                    $dummyArray["Payload"]['totalCount'] = 0;
                    return $dummyArray;
                }
            });

            if(!$geoSummary || $geoSummary["Payload"]['totalCount']==0){
                $geoSummary = Cache::get('node-geo-summary-forever');
            } else {
                Cache::put('node-geo-summary-forever', $geoSummary);
            }


            $nodecount = 0;
            $countrycount = 0;
            foreach ($geoSummary["Payload"]['summary'] as $country) {
                $nodecount += $country["Count"];
                $countrycount++;
            }

            return [
                'networkNodes'      => $nodecount,
                'countries'         => $countrycount
            ];
        });



        $prices = Cache::remember('prices', 60, function(){

            $apiRequest = Http::get('https://api.coingecko.com/api/v3/simple/token_price/ethereum?contract_addresses=0x5cf04716ba20127f1e2297addcf4b5035000c9eb&vs_currencies=usd%2Ceth&include_market_cap=false&include_24hr_vol=true&include_24hr_change=true&include_last_updated_at=false');
            $response = json_decode($apiRequest->body(), true, 512, JSON_BIGINT_AS_STRING);

            return $response['0x5cf04716ba20127f1e2297addcf4b5035000c9eb'];
        });

        return array_merge(parent::share($request), [
            'newNotifications' => function () use ($request) {
                if ($request->user()){
                    return count($request->user()->unreadNotifications);
                }
            },
            'prices' => function () use ($request, $prices) {
                return $prices;
            },
            'networkStatus' => function () use ($currentVersion){
                return [
                    'syncState' =>  'PERSIST_FINISHED',
                    'networkVersion' => $currentVersion

                    ];
            },
            'networkStats' => function () use ($nodeCounts) {
                return $nodeCounts;
            },
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            },
        ]);
    }
}
