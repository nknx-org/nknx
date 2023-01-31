<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\BlockchainTransaction;

use App\Models\NodeSnapshot;

use Illuminate\Support\Facades\Http;

class SnapshotNode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $node;
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($node, $date)
    {
        $this->node = $node;
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->node->publicKey){

            $data = BlockchainTransaction::selectRaw('count(*), sum(reward) as sum')->where('signerPk',$this->node->publicKey)->where('txType', 'COINBASE_TYPE')
            ->whereRaw('"created_at" > \''. $this->date->format("Y-m-d") .'\'::date')
            ->whereRaw('"created_at" < \''. $this->date->format("Y-m-d") .'\'::date +1')
            ->get();

            NodeSnapshot::updateOrCreate(
                ['node_id' => $this->node->id, 'created_at' => $this->date->format("Y-m-d")],
                ["mined" => $data[0]['count'], "mined_amount" => $data[0]['sum'] ? $data[0]['sum'] : 0,]
            );

        }
        \DB::disconnect();
    }

    public function tags()
    {
        return ['SnapshotNode', $this->node->id];
    }

}

