<?php

namespace App\Http\Controllers;

use App\Helpers\PubKey2Wallet;
use App\Models\Node;
use App\Models\GenIdJob;

use App\Jobs\SnapshotNode;
use Exception;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Client\Pool;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class ApiController extends Controller
{
    public function nodesGet()
    {

        if (Auth::user()->tokenCan('read')) {
            $sort = Request::get('sort', 'relayMessageCount');
            $order = Request::get('order', 'desc');
            $paginate = Request::get('per_page', 25);
            $aggregate = Request::get('aggregate', 'month');
            $searchstring = Request::get('search');
            $syncStateString = Request::get('syncState');

            $pageSize = min($paginate, 5000);


            $node_ids = Auth::user()->nodes()->get()->pluck('id')->toArray();
            $counts = Node::select(DB::raw("COUNT(id) as sumall, COUNT(id) filter (where \"syncState\" = 'WAIT_FOR_SYNCING') as sumsyncing, COUNT(id) filter (where \"syncState\" = 'GENERATE_ID') as sumgenerating, COUNT(id) filter (where \"syncState\" = 'PRUNING_DB') as sumpruning, COUNT(id) filter (where \"syncState\" = 'SYNC_STARTED') as sumstarted, COUNT(id) filter (where \"syncState\" = 'SYNC_FINISHED') as sumfinished, COUNT(id) filter (where \"syncState\" = 'PERSIST_FINISHED') as sumpersistent, COUNT(id) filter (where \"syncState\" = 'OFFLINE') as sumoffline"))
                ->whereIn('id', $node_ids)
                ->get();

            $sumNodeSnapshots = [];

            if ($node_ids) {
                $rawQuery = "SELECT  date_trunc('" . $aggregate . "', created_at) as date, sum(mined) sum_mined, sum(mined_amount) sum_mined_amount ";
                $rawQuery .= "from node_snapshots ";
                $rawQuery .= "where node_id in (" . implode(",", $node_ids) . ") ";
                $rawQuery .= "group by 1 ";
                $rawQuery .= "order by 1 desc ";
                $rawQuery .= "limit 72 ";
                $sumNodeSnapshots = DB::select(DB::raw($rawQuery));
            }


            return response()->json([
                'filters' => Request::all('search', 'syncState'),
                'sumNodeSnapshots' => $sumNodeSnapshots,
                'sums' => [
                    'sumall' => $counts[0]->sumall ? $counts[0]->sumall : 0,
                    'sumgenerating' => $counts[0]->sumgenerating ? $counts[0]->sumgenerating : 0,
                    'sumpruning' => $counts[0]->sumpruning ? $counts[0]->sumpruning : 0,
                    'sumsyncing' => $counts[0]->sumsyncing ? $counts[0]->sumsyncing : 0,
                    'sumstarted' => $counts[0]->sumstarted ? $counts[0]->sumstarted : 0,
                    'sumfinished' => $counts[0]->sumfinished ? $counts[0]->sumfinished : 0,
                    'sumpersistent' => $counts[0]->sumpersistent ? $counts[0]->sumpersistent : 0,
                    'sumoffline' => $counts[0]->sumoffline ? $counts[0]->sumoffline : 0,
                ],
                'nodes' => Auth::user()->nodes()
                    ->orderBy($sort, $order)
                    ->filter(Request::only('search', 'syncState'))
                    ->paginate($pageSize)
                    ->withQueryString()
                    ->through(function ($node) {
                        return [
                            'id' => $node->id,
                            'label' => $node->pivot->label,
                            'syncState' => $node->syncState,
                            'addr' => $node->addr,
                            'height' => $node->height,
                            'publicKey' => $node->publicKey,
                            'relayPerHour' => $node->relayPerHour,
                            'relayMessageCount' => $node->relayMessageCount,
                            'version' => $node->version,
                            'walletAddress' => $node->walletAddress
                        ];
                    }),
            ]);
        } else {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
    }

    public function nodesCreate()
    {

        if (Auth::user()->tokenCan('create')) {
            Request::validate([
                'ip' => ['required'],
            ]);
            $user = Auth::user();
            $ip = str_replace(' ', '', Request::input('ip'));
            $aliases = explode(',', $ip);

            $label = Request::input('label', '');
            $multiAliases = [];
            $failedAliases = [];
            $savedAliases = [];


            foreach ($aliases as $alias) {

                if (substr($alias, -1) == '/') {
                    $alias = substr($alias, 0, -1);
                }

                //check if it is an ip
                if (filter_var($alias, FILTER_VALIDATE_IP) !== false) {
                    // is an ip
                    $hostname = '';
                } else {
                    // is not an ip
                    $hostname = $alias;
                }

                try {
                    $node = Node::where('addr', 'tcp://' . $alias . ':30001')->first();
                    if ($node) {
                        //check if Node-ID is already attached to user
                        if (!$user->nodes->contains($node->id)) {
                            $user->nodes()->attach($node->id, ['hostname' => $hostname, 'label' => $label, 'validHostname' => true]);
                            array_push($savedAliases, $node);
                        } else {
                            array_push($multiAliases, $alias);
                        }
                    } else {
                        //it is a new node!
                        $apiRequest = Http::timeout(3)->withHeaders([
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json'
                        ])->post($alias . ':30003', [
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
                                if (filter_var($alias, FILTER_VALIDATE_IP) !== false) {
                                } else {
                                    // is not an ip
                                    $alias = gethostbyname($alias);
                                }

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
                            $user->nodes()->attach($node->id, ['hostname' => $hostname, 'label' => $label, 'validHostname' => true]);


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



                            array_push($savedAliases, $node);
                        } else {
                            array_push($failedAliases, $alias);
                        }
                    }
                } catch (\Exception $e) {
                    array_push($failedAliases, $alias);
                }
            }

            Cache::forget('nodes-user-' . Auth::user()->id);
            Cache::forget('node-mined-history-user-' . Auth::user()->id);

            if ((count($savedAliases) || count($multiAliases)) && !count($failedAliases)) {

                return response()->json($savedAliases);
            } else {


                return response()->json(
                    [
                        'ip' => [
                            'Failed to add some nodes because they are not reachable - please check your input.',
                        ],
                        'failed' => $failedAliases
                    ]
                );
            }
        } else {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function nodeGet($id)
    {

        if (Auth::user()->tokenCan('read')) {
            $nodeObj = Auth::user()->nodes()->where('nodes.id', $id)->with('node_snapshots')->get();

            if ($nodeObj) {
                return response()->json(
                    $nodeObj->transform(function ($node) {
                        return [
                            'id' => $node->id,
                            'label' => $node->pivot->label,
                            'syncState' => $node->syncState,
                            'addr' => $node->addr,
                            'height' => $node->height,
                            'publicKey' => $node->publicKey,
                            'relayMessageCount' => $node->relayMessageCount,
                            'version' => $node->version,
                            'walletAddress' => $node->walletAddress,
                            'snapshots' => $node->node_snapshots ? $node->node_snapshots : null,
                        ];
                    })->first(),
                );
            } else {
                return response()->json(['error', 'Resource not found'], 400);
            }
        } else {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function nodesDestroy()
    {
        if (Auth::user()->tokenCan('delete')) {
            $nodesId = null;

            if (Request::get('ids')) {
                $nodesId = Auth::user()->nodes()->whereIn('nodes.id', explode(',', Request::get('ids')))->pluck('nodes.id');
            }
            if (Request::get('ips')) {
                $ips = explode(',', Request::get('ips'));
                $nodesId = Auth::user()->nodes()->Where(function ($query) use ($ips) {
                    for ($i = 0; $i < count($ips); $i++) {
                        $query->orwhere('addr', 'like',  '%' . $ips[$i] . '%');
                    }
                })->pluck('nodes.id');
            }
            if (Request::get('labels')) {
                $nodesId = Auth::user()->nodes()->wherePivotIn('label', explode(',', Request::get('labels')))->pluck('nodes.id');
            }

            if ($nodesId) {

                Cache::forget('nodes-user-' . Auth::user()->id);
                Cache::forget('node-mined-history-user-' . Auth::user()->id);

                Auth::user()->nodes()->detach($nodesId);

                $nodes = Node::whereIn('id', $nodesId)->get();

                foreach ($nodes as $node) {
                    if (count($node->users) == 0) {
                        $node->delete();
                    }
                }


                return response()->json(null, 200);
            } else {
                return response()->json(['error', 'Resource not found'], 400);
            }
        } else {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getWallet()
    {

        try {
            $apiRequest = Http::timeout(3)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post(Request::get('ip') . ':30003', [
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

                    return response()->json(PubKey2Wallet::encode($response["result"]["publicKey"]), 200);
                } else {
                    if (array_key_exists('error', $response) && isset($response["error"])) {

                        if ($response["error"]["code"] == -45022) {
                            return response()->json(['status' => 'GENERATE_ID']);
                        } else if ($response["error"]["code"] == -45024) {
                            return response()->json(['status' => 'PRUNING_DB']);
                        } else {
                            return response()->json(['status' => 'GENERATE_ID']);
                        }
                    } else {
                        return response()->json(['status' => 'GENERATE_ID']);
                    }
                }
            } else {
                return response()->json(['status' => 'OFFLINE'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'OFFLINE'], 400);
        }
    }
}
