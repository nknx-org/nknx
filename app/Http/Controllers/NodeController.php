<?php

namespace App\Http\Controllers;

use App\Models\Node;
use App\Models\GenIdJob;

use App\Jobs\SnapshotNode;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;

use Inertia\Inertia;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Client\Pool;

use Illuminate\Support\Facades\Cache;
use DB;

use Illuminate\Validation\ValidationException;



class NodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sort = Request::get('sort', 'relayMessageCount');
        $order = Request::get('order', 'desc');
        if ($sort == 'label') {
            $sort = 'node_user.label';
        }

        $paginate = Request::get('per_page', 25);
        $aggregate = Request::get('aggregate', 'month');
        $pageSize = min($paginate, 500);

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

        $blockchainSummary = Cache::remember('blockchain-summary', 60, function () {

            $apiRequest = Http::get('https://openapi.nkn.org/api/v1/statistics/counts');
            $response = json_decode($apiRequest->body(), true, 512, JSON_BIGINT_AS_STRING);

            return [
                'blockchainSummary'      => $response
            ];
        });



        return Inertia::render('Nodes/Index', array_merge([
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
                ->with('node_snapshots')
                ->orderBy($sort, $order)
                ->filter(Request::only('search', 'syncState'))
                ->paginate($pageSize)
                ->withQueryString()
                ->through(function ($node) {
                    return [
                        'id' => $node->id,
                        'label' => $node->pivot->label,
                        'syncState' => filter_var($node->addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? 'INVALID_IPV6' : $node->syncState,
                        'addr' => $node->addr,
                        'height' => $node->height,
                        'publicKey' => $node->publicKey,
                        'relayPerHour' => $node->relayPerHour,
                        'relayMessageCount' => $node->relayMessageCount,
                        'version' => $node->version,
                        'walletAddress' => $node->walletAddress,
                        'snapshots' => $node->node_snapshots ? $node->node_snapshots : null,
                    ];
                }),
        ],$blockchainSummary));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
                $alias = gethostbyname($hostname);
                if ($alias == $hostname) {
                    throw ValidationException::withMessages(
                        [
                            'ip' => [
                                'DNS resolve failed. Please check your input.',
                            ]
                        ]
                    );
                }
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
                            $uptimeHours = $response["result"]["uptime"] / 60 / 60;
                            $node->relayPerHour = round($response["result"]["relayMessageCount"] / $uptimeHours);
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
            if (count($savedAliases) + count($multiAliases) > 1) {
                return Redirect::back()->with('success', 'Nodes added');
            } else {
                return Redirect::back()->with('success', 'Node added');
            }
        } else {

            throw ValidationException::withMessages(
                [
                    'ip' => [
                        'Failed to add some nodes because they are not reachable - please check your input.',
                    ],
                    'failed' => $failedAliases
                ]
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Node  $node
     * @return \Illuminate\Http\Response
     */
    public function show(Node $node)
    {
        $nodeObj = Auth::user()->nodes()->where('nodes.id', $node->id)->with('node_snapshots')->get();
        if ($nodeObj) {
            return Inertia::render('Nodes/Show', [
                'node' =>  $nodeObj->transform(function ($node) {
                    return [
                        'id' => $node->id,
                        'label' => $node->pivot->label,
                        'syncState' => filter_var($node->addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? 'INVALID' : $node->syncState,
                        'addr' => $node->addr,
                        'height' => $node->height,
                        'publicKey' => $node->publicKey,
                        'relayMessageCount' => $node->relayMessageCount,
                        'version' => $node->version,
                        'walletAddress' => $node->walletAddress,
                        'snapshots' => $node->node_snapshots ? $node->node_snapshots : null,
                    ];
                })->first(),
            ]);
        } else {
            return redirect('nodes.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Node  $node
     * @return \Illuminate\Http\Response
     */
    public function edit(Node $node)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Node  $node
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Node $node)
    {
        $node = Auth::user()->nodes()->find($node);
        $label = Request::get('label', '');
        if ($node) {
            Auth::user()->nodes()->updateExistingPivot($node->id, [
                'label' => $label
            ]);
            Cache::forget('nodes-user-' . Auth::user()->id);
            return Redirect::back()->with('success', 'Node updated.');
        } else {
            return Redirect::back()->with('error', 'Resource not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Node  $node
     * @return \Illuminate\Http\Response
     */
    public function destroy(Node $node)
    {
        $nodeObj = Auth::user()->nodes()->find($node->id);
        if ($nodeObj) {

            Cache::forget('nodes-user-' . Auth::user()->id);
            Cache::forget('node-mined-history-user-' . Auth::user()->id);

            Auth::user()->nodes()->detach($nodeObj->id);
            if (count($nodeObj->users) == 0) {
                $nodeObj->delete();
            }
            return Redirect::back()->with('success', 'Node deleted.');
        } else {
            return Redirect::back()->with('error', 'Resource not found');
        }
    }


    /**
     * Remove all user-nodes
     * Remove all user-nodes from the database
     *
     * @authenticated
     *
     * @response {
     *  null
     * }
     */
    public function destroyAll(Request $request)
    {

        if (Request::input('ips')) {
            Auth::user()->nodes()->detach(Request::input('ips'));
        } else (Auth::user()->nodes()->detach());

        Cache::forget('nodes-user-' . Auth::user()->id);
        Cache::forget('node-mined-history-user-' . Auth::user()->id);

        return Redirect::back()->with('success', 'Nodes deleted.');
    }
}
