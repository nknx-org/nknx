<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Notifications\NodeOnline;
use App\Notifications\NodeOffline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;

use App\Notifications\NeedsIdGeneration;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UpdateNode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $node;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($node)
    {
        $this->node = $node;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //store old values
        $oldSyncState = $this->node->syncState;
        $oldSversion = $this->node->sversion;

        try {
            $apiRequest = Http::timeout(3)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post($this->node->addr  . ':30003', [
                "id" => 1,
                "method" => "getnodestate",
                "params" => [
                    "provider" => "nknx",
                ],
                "jsonrpc" => "2.0"
            ]);

            $response = json_decode($apiRequest->body(), true);

            if (array_key_exists('result', $response) && isset($response["result"])) {

                $this->node->nodeId = $response["result"]["id"];
                $this->node->fill($response["result"]);
                $sversion = substr($response["result"]["version"], (strpos($response["result"]["version"], 'v') + 1), strpos($response["result"]["version"], '-') - (strpos($response["result"]["version"], 'v') + 1));
                $this->node->sversion = (int) str_replace('.', '', $sversion);
                $uptimeHours = $response["result"]["uptime"] / 60 / 60;
                $this->node->relayPerHour = round($response["result"]["relayMessageCount"] / $uptimeHours);
            } else {
                if (array_key_exists('error', $response) && isset($response["error"])) {

                    if ($response["error"]["code"] == -45022) {
                        $this->node->syncState = 'GENERATE_ID';
                        $this->node->walletAddress = $response["error"]["walletAddress"];
                    } else if ($response["error"]["code"] == -45024) {
                        $this->node->syncState = 'PRUNING_DB';
                    } else {
                        $this->node->syncState = 'GENERATE_ID';
                        $this->node->walletAddress = $response["error"]["walletAddress"];
                        foreach ($this->node->users()->get() as $user) {
                            if ($oldSyncState != 'GENERATE_ID') {
                                $user->notify(new NeedsIdGeneration($this->node));
                            }
                        }
                    }
                } else {
                    $this->node->syncState = 'GENERATE_ID';
                    $this->node->walletAddress = $response["error"]["walletAddress"];
                    foreach ($this->node->users()->get() as $user) {
                        if ($oldSyncState != 'GENERATE_ID') {
                            $user->notify(new NeedsIdGeneration($this->node));
                        }
                    }
                }
            }
            if ($oldSyncState == 'OFFLINE') {
                $this->node->daily_changes = $this->node->daily_changes + 1;
                foreach ($this->node->users()->get() as $user) {
                    if ($this->node->daily_changes <= 2) {
                        $user->notify(new NodeOnline($this->node));
                    }
                }
            }
            $this->node->save();
        } catch (\Exception $e) {
            $resolved = false;
            //whoops, seems like node is offline
            //let's first check if the node has any hostnames that are valid
            $users = $this->node
                ->users()
                ->wherePivot('hostname', '<>', '')
                ->wherePivot('validHostname', '=', true)
                ->get();
            $hostnames = [];
            foreach ($users as $user) {
                if ($user->pivot->hostname !== $this->node->addr) {
                    array_push($hostnames, $user->pivot->hostname);
                }
            }
            if ($hostnames) {
                foreach ($hostnames as $hostname) {
                    //try get a new IP
                    $newIP = gethostbyname($hostname);
                    if ($newIP != $hostname) {
                        //if node has a new IP
                        if ($this->node->addr != $newIP) {
                            //update node entry
                            $this->node->addr = "tcp://" . $newIP . ":30001";
                            $this->node->save();
                            $resolved = true;
                        }
                    } else {
                        //invalidate hostname
                        DB::statement("UPDATE node_user SET \"validHostname\" = true where hostname = '" . $hostname . "'");
                    }
                }
            }

            if (!$resolved) {
                $this->node->syncState = "OFFLINE";

                if ($oldSyncState != 'SYNC_STARTED' && $oldSyncState != 'WAIT_FOR_SYNCING') {
                    foreach ($this->node->users()->get() as $user) {

                        $this->node->daily_changes = $this->node->daily_changes + 1;
                        if ($this->node->daily_changes <= 2) {
                            $user->notify(new NodeOffline($this->node));
                        }
                    }
                }

                $this->node->save();
            }
        }
    }

    public function tags()
    {
        return ['UpdateNode', $this->node->id];
    }
}
