<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Http;

class UpdateWallet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $wallet;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($wallet)
    {
        $this->wallet = $wallet;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->wallet->address !== 'NKNL7HQk4LuU7Zcuwa9rpHAxCzuM2owuQ5xT'){
            try {
                $apiRequest = Http::timeout(3)->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])->post(config('nkn.remote-node.address') . ':' . config('nkn.remote-node.port'),[
                    "id" => 1,
                    "method" => "getbalancebyaddr",
                    "params" => [
                        "address" => $this->wallet->address,
                    ],
                    "jsonrpc" => "2.0"
                ]);
                $response = json_decode($apiRequest->body(), true, 512, JSON_BIGINT_AS_STRING);
                $this->wallet->balance = $response["result"]["amount"];
                $this->wallet->save();
            } catch (\Exception $e) { }
        }
    }

    public function tags()
    {
        return ['UpdateWallet', $this->wallet->id];
    }
}
