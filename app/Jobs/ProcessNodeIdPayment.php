<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;


class ProcessNodeIdPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $gen_id_job;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($gen_id_job)
    {
        $this->gen_id_job = $gen_id_job;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{

            $apiRequest = Http::timeout(10)->post(config('nkn.faucet.address') . ':' . config('nkn.faucet.port') . '/send',[
                "address" => $this->gen_id_job->address,
                "amount"  => 10.01
            ]);

            $response = json_decode($apiRequest->body(), true);

            if (array_key_exists('tx', $response)  && isset($response["tx"])) {
                $this->gen_id_job->status = 'TOKENS_SENT';
                $this->gen_id_job->tx = $response["tx"];
                $this->gen_id_job->save();
            };

            //buy on binance

        } catch (\Exception $e) {
            $this->gen_id_job->status = 'TOKENS_FAILED';
            $this->gen_id_job->save();
        }

    }

    public function tags()
    {
        return ['ProcessNodeIdPayment', $this->gen_id_job->address];
    }
}
