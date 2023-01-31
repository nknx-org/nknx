<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;

use App\Models\Node;
use App\Models\User;
use App\Models\FdEvent;

use App\Jobs\SnapshotNode;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CreateNodeFromFdDeployment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $fdDeployment;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fdDeployment)
    {
        $this->fdDeployment = $fdDeployment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $user = $this->fdDeployment->fd_configuration->user;


        try {
            $alias = $this->fdDeployment->ip;
            $label = $this->fdDeployment->label;
            $apiRequest = Http::timeout(3)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post($alias  . ':30003', [
                "id" => 1,
                "method" => "getnodestate",
                "params" => [
                    "provider" => "nknx",
                ],
                "jsonrpc" => "2.0"
            ]);
            $response = json_decode($apiRequest->body(), true);

            if (array_key_exists('result', $response)  && isset($response["result"])) {

                $node = new Node($response["result"]);
                $node->nodeId = $response["result"]["id"];
                //get version
                $sversion = substr($response["result"]["version"], (strpos($response["result"]["version"], 'v') + 1), strpos($response["result"]["version"], '-') - (strpos($response["result"]["version"], 'v') + 1));
                $node->sversion = (int) str_replace('.', '', $sversion);
            } else {

                if (array_key_exists('error', $response)  && isset($response["error"])) {

                    if ($response["error"]["code"] == -45022) {
                        $node = new Node([
                            'addr' => 'tcp://' . $alias . ':30001',
                            'syncState' => 'GENERATE_ID',
                            'walletAddress' => $response["error"]["walletAddress"]
                        ]);
                    } else if ($response["error"]["code"] == -45024) {
                        $node = new Node([
                            'addr' => 'tcp://' . $alias . ':30001',
                            'syncState' => 'PRUNING_DB'
                        ]);
                    } else {
                        $node = new Node([
                            'addr' => 'tcp://' . $alias . ':30001',
                            'syncState' => 'GENERATE_ID',
                            'walletAddress' => $response["error"]["walletAddress"]
                        ]);
                    }
                } else {

                    $node = new Node([
                        'addr' => 'tcp://' . $alias . ':30001',
                        'syncState' => 'GENERATE_ID',
                        'walletAddress' => $response["error"]["walletAddress"]
                    ]);
                }
            }

            $node->save();
            $this->fdDeployment->fd_events()->save(new FdEvent([
                "event_code" => 20,
                "user_id" => $user->id
            ]));
            $user->nodes()->attach($node->id, ['label' => $label]);


            SnapshotNode::dispatch($node, new \DateTime());


            //maybe eMail?!

        } catch (\Exception $e) {
            $node = new Node([
                'addr' => 'tcp://' . $alias . ':30001',
                'syncState' => 'OFFLINE'
            ]);
            $node->save();
            $this->fdDeployment->fd_events()->save(new FdEvent([
                "event_code" => 20,
                "user_id" => $user->id
            ]));
            $user->nodes()->attach($node->id, ['label' => $label]);
        } finally {
            \DB::disconnect();
        }
    }
    public function tags()
    {
        return ['CreateNodeFromFdDeployment', $this->fdDeployment->id];
    }
}
