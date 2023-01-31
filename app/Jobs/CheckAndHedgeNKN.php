<?php

namespace App\Jobs;

use App\Models\Counter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Binance;

class CheckAndHedgeNKN implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $binanceApiKey = config('app.binance-api-key');
        $binanceApiSecret = config('app.binance-api-secret');

        if($binanceApiKey && $binanceApiSecret){
            $counter = Counter::first();
            if($counter->consecutiveGenIds >= 3){
                $api = new Binance\API($binanceApiKey,$binanceApiSecret);
                $api->marketBuy('NKNBTC', $counter->consecutiveGenIds*10);
                $counter->consecutiveGenIds = 0;
                $counter->save();
            }
        }

    }
}
