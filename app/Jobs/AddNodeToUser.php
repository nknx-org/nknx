<?php

namespace App\Jobs;

use App\Models\Node;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class AddNodeToUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $nodeIP;
    protected $label;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $nodeIP, $label)
    {
        $this->user = $user;
        $this->nodeIP = $nodeIP;
        $this->label = $label;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            if (filter_var($this->nodeIP, FILTER_VALIDATE_IP) !== false) {
                // is an ip
                $hostname = '';
            } else {
                // is not an ip
                $hostname = $this->nodeIP;
            }



            $node = Node::where('addr', 'tcp://' . $this->nodeIP . ':30001')->first();
            if ($node) {
                //check if Node-ID is already attached to user
                if (!$this->user->nodes->contains($node->id)) {
                    $this->user->nodes()->attach($node->id, ['hostname' => $hostname, 'label' => $this->label, 'validHostname' => true]);
                    array_push($savedAliases, $node);
                }
            } else {
                //it is a new node!
                $apiRequest = Http::timeout(3)->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])->post($this->nodeIP . ':30003', [
                    "id" => 1,
                    "method" => "getnodestate",
                    "params" => [
                        "provider" => "nknx",
                    ],
                    "jsonrpc" => "2.0"
                ]);

                $response = json_decode($apiRequest->body(), true);
                if ($response) {

                    if (array_key_exists('result', $response)  && isset($response["result"])) {
                        $node = new Node($response["result"]);
                        $node->nodeId = $response["result"]["id"];
                        //get version
                        $sversion = substr($response["result"]["version"], (strpos($response["result"]["version"], 'v') + 1), strpos($response["result"]["version"], '-') - (strpos($response["result"]["version"], 'v') + 1));
                        $node->sversion = (int) str_replace('.', '', $sversion);
                    } else {
                        //it is generating an ID or Pruning DB

                        //check if it is an ip
                        if (filter_var($this->nodeIP, FILTER_VALIDATE_IP) !== false) {
                        } else {
                            // is not an ip
                            $this->nodeIP = gethostbyname($this->nodeIP);
                        }

                        if (array_key_exists('error', $response)  && isset($response["error"])) {
                            if ($response["error"]["code"] == -45022) {
                                $node = new Node([
                                    'addr' => 'tcp://' . $this->nodeIP . ':30001',
                                    'syncState' => 'GENERATE_ID',
                                    'walletAddress' => $response["error"]["walletAddress"]
                                ]);
                            } else if ($response["error"]["code"] == -45024) {
                                $node = new Node([
                                    'addr' => 'tcp://' . $this->nodeIP . ':30001',
                                    'syncState' => 'PRUNING_DB'
                                ]);
                            } else {
                                $node = new Node([
                                    'addr' => 'tcp://' . $this->nodeIP . ':30001',
                                    'syncState' => 'GENERATE_ID',
                                    'walletAddress' => $response["error"]["walletAddress"]
                                ]);
                            }
                        } else {
                            $node = new Node([
                                'addr' => 'tcp://' . $this->nodeIP . ':30001',
                                'syncState' => 'GENERATE_ID',
                                'walletAddress' => $response["error"]["walletAddress"]
                            ]);
                        }
                    }

                    $node->save();
                    $this->user->nodes()->attach($node->id, ['hostname' => $hostname, 'label' => $this->label, 'validHostname' => true]);


                    //Create snapshot of the last 90 days
                    $end = new \DateTime();
                    $begin = new \DateTime();
                    date_sub($begin, date_interval_create_from_date_string('90 days'));
                    date_add($end, date_interval_create_from_date_string('1 day'));

                    $interval = \DateInterval::createFromDateString('1 day');
                    $period = new \DatePeriod($begin, $interval, $end);

                    foreach ($period as $dt) {
                        SnapshotNode::dispatch($node, $dt);
                    }
                } else {
                    $node = new Node([
                        'addr' => 'tcp://' . $this->nodeIP . ':30001',
                        'syncState' => 'OFFLINE',
                    ]);
                    $node->save();
                    $this->user->nodes()->attach($node->id, ['hostname' => $hostname, 'label' => $this->label, 'validHostname' => true]);
                }
            }
        } catch (\Exception $e) {
            $node = new Node([
                'addr' => 'tcp://' . $this->nodeIP . ':30001',
                'syncState' => 'OFFLINE',
            ]);
            $node->save();
            $this->user->nodes()->attach($node->id, ['hostname' => $hostname, 'label' => $this->label, 'validHostname' => true]);
        }
    }
}
