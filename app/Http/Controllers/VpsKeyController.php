<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

use App\Models\VpsKey;
use Auth;
use DB;

use Vultr\VultrClient;
use Vultr\Adapter\CurlAdapter;

use LKDev\HetznerCloud\HetznerAPIClient;

use GuzzleHttp\Client as GuzzleHttpClient;

use Aws\Ec2\Ec2Client;
use Aws\Exception\AwsException;
use Aws\Credentials\Credentials;

use App\FdConfiguration;

use Inertia\Inertia;


class VpsKeyController extends Controller
{
    public function index(Request $request)
    {
        $paginate = Request::get('per_page', 10);
        $pageSize = min($paginate, 100);

        return Inertia::render('VpsKeys/Index', [
            'vps-keys' => Auth::user()->vps_keys()
                ->paginate($pageSize)
                ->withQueryString()
                ->through(function ($vps_key) {
                    return [
                        'id' => $vps_key->id,
                        'provider' => $vps_key->provider,
                        'profile_name' => $vps_key->profile_name,
                        'api_token' => $vps_key->api_token,
                        'api_secret' => $vps_key->api_secret,
                        'created_at' => $vps_key->created_at,
                    ];
                }),
        ]);
    }

    public function store(Request $request)
    {
        Request::validate([
            'provider' => ['required', 'in:DigitalOcean,AWS,Vultr,Hetzner'],
            'profile_name' => ['required'],
            'api_token' => ['required'],
            'api_secret' => ['required_if:provider,AWS']
        ]);

        $provider = Request::input('provider');
        $api_token = Request::input('api_token');
        $api_secret = Request::input('api_secret');


        switch ($provider) {
            case "DigitalOcean":
                try{
                    $client = new \DigitalOceanV2\Client();
                    $client->authenticate($api_token);

                    $account = $client->account();
                    $userInformation = $account->getUserInformation();
                }
                catch(\Exception $e){
                    return Redirect::back()->with('error', 'VPS Key invalid');
                }
            break;
            case "Vultr":
                try{
                    $client = new VultrClient(
                        new CurlAdapter($api_token)
                    );
                    $result = $client->metaData()->getAccountInfo();
                }
                catch(\Exception $e){
                    return Redirect::back()->with('error', 'VPS Key invalid');
                }
            break;
            case "Hetzner":
                try{
                    $hetznerClient = new HetznerAPIClient($api_token);
                    $actions = $hetznerClient->actions()->all();
                }
                catch(\Exception $e){

                    return Redirect::back()->with('error', 'VPS Key invalid');
                }
            break;
            case "AWS":
                try{
                    $credentials = new Credentials($api_token, $api_secret);

                    $ec2Client = new Ec2Client(array(
                        'credentials' => $credentials,
                        'region' => 'us-east-1',
                        'version' => 'latest'
                    ));
                    $ec2Client->DescribeImages([
                        'Filters' => [
                            [
                                'Name' => 'name',
                                'Values' => ['ubuntu/images/hvm-ssd/ubuntu-focal-20.04-amd64-server-20200625']
                            ],
                            [
                                'Name' => 'state',
                                'Values' => ['available']
                            ],
                        ]
                    ]);
                }
                catch(\Exception $e){
                    return Redirect::back()->with('error', 'VPS Key invalid');
                }
            break;
        }

            Auth::user()->vps_keys()->save(new VpsKey(Request::all()));

            return Redirect::back()->with('success', 'VPS key created.');

    }

    public function show(VpsKey $vps_key)
    {

        $vpsKeyObj = Auth::user()->vps_keys()->find($vps_key);

        if ($vpsKeyObj){
            return Inertia::render('VpsKeys/Show',[
                'vps-key' =>  $vpsKeyObj->transform(function ($vps_key) {
                    return [
                        'id' => $vps_key->id,
                        'address' => $vps_key->address,
                        'balance' => $vps_key->balance,
                        'label' => $vps_key->pivot->label,
                    ];
                })->first()
            ]
            );
        }
        else{
            return redirect('vps-keys.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VpsKey  $vpsKey
     * @return \Illuminate\Http\Response
     */
    public function destroy(VpsKey $vpsKey)
    {
        $vps_key = Auth::user()->vps_keys()->find($vpsKey->id);

        if ($vps_key) {
            $vps_key->delete();
            return Redirect::back()->with('success', 'SSH key deleted.');
        } else {
            return Redirect::back()->with('error', 'Resource not found');
        }
    }

    /**
     * getDoSizes
     * Gets available Droplet sizes on DigitalOcean
     *
     * @authenticated
     *
     *
     * @response {
     *  null
     * }
     */
    public function getDoSizes()
    {
        $user = Auth::user();
        $api_key_obj = $user->vps_keys()->where('provider', "DigitalOcean")->first();
        $api_key = $api_key_obj->api_token;

        $sizes_unified = [];

        if ($api_key) {
            $client = new \DigitalOceanV2\Client();
            $client->authenticate($api_key);

            $size = $client->size();
            $sizes = $size->getAll();

            $region = $client->region();
            $regions = $region->getAll();

            $filteredSizes = array_filter($sizes, function ($var) {
                return $var->available == true;
            });

            foreach ($filteredSizes as $size) {
                //because regions in the DO size object suck
                $size->regions = [];
            }

            foreach($regions as $region){
                foreach($region->sizes as $size){
                    foreach($filteredSizes as $mainSize){

                        $slug = $mainSize->slug;

                        if($slug === $size){

                            $filteredRegion = [
                                'slug' => $region->slug,
                                'name' => $region->name
                            ];
                            array_push($mainSize->regions,$filteredRegion);
                        }
                    }
                }
            }

            foreach ($filteredSizes as $size) {
                array_push($sizes_unified,array(
                    'slug' => $size->slug,
                    'vpsId' => null,
                    'memory' => (int) $size->memory,
                    'vcpus' => (int) $size->vcpus,
                    'disk' => (int) $size->disk,
                    'priceMonthly' => $size->priceMonthly,
                    'regions' => $size->regions,
                    'description' => $size->description
                ));
            }
            return response()->json($sizes_unified);
        } else {
            return response([
                'status' => 'error',
                'error' => 'vpsKey.notFound',
                'msg' => 'VpsKeyNotFound'
            ], 400);
        }
    }

    /**
     * getVultrSizes
     * Gets available Droplet sizes on Vultr
     *
     * @authenticated
     *
     *
     * @response {
     *  null
     * }
     */
    public function getVultrSizes()
    {
        $user = Auth::user();
        $api_key_obj = $user->vps_keys()->where('provider', "Vultr")->first();
        $api_key = $api_key_obj->api_token;

        if ($api_key) {
            // Using Guzzle 5 or 6...
            $client = new VultrClient(
                new CurlAdapter($api_key)
            );

            $sizes = $client->metaData()->getPlansList();
            $result = $client->region()->getList();

            $filteredSizes = [];

            foreach ($sizes as &$size) {
                foreach ($size['available_locations'] as &$location) {
                    $location = $result[$location];
                };
                array_push($filteredSizes, $size);
            }

            $filteredSizes = array_map(function ($tag) {
                return array(
                    'slug' => $tag['name'],
                    'vpsId' => (int) $tag['VPSPLANID'],
                    'memory' => (int) $tag['ram'],
                    'vcpus' => (int) $tag['vcpu_count'],
                    'disk' => (int) $tag['disk'],
                    'priceMonthly' => (float) $tag['price_per_month'],
                    'regions' => $tag['available_locations'],
                );
            }, $filteredSizes);


            return response()->json($filteredSizes);

        } else {
            return response([
                'status' => 'error',
                'error' => 'vpsKey.notFound',
                'msg' => 'VpsKeyNotFound'
            ], 400);
        }
    }

    /**
     * getAWSSizes
     * Gets available Droplet sizes on AWS
     *
     * @authenticated
     *
     *
     * @response {
     *  null
     * }
     */
    public function getAWSSizes()
    {

        $obj = '[
                {
                    "slug":"t2.micro",
                    "vpsId":null,
                    "memory":1024,
                    "vcpus":1,
                    "disk":50,
                    "priceMonthly":5,
                    "regions":[
                        {"slug":"eu-central-1","name":"eu-central-1"},
                        {"slug":"us-east-1","name":"us-east-1"},
                        {"slug":"us-east-2","name":"us-east-2"},
                        {"slug":"eu-north-1","name":"eu-north-1"},
                        {"slug":"ap-south-1","name":"ap-south-1"},
                        {"slug":"eu-west-3","name":"eu-west-3"},
                        {"slug":"eu-west-2","name":"eu-west-2"},
                        {"slug":"eu-west-1","name":"eu-west-1"},
                        {"slug":"ap-northeast-2","name":"ap-northeast-2"},
                        {"slug":"ap-northeast-1","name":"ap-northeast-1"},
                        {"slug":"sa-east-1","name":"sa-east-1"},
                        {"slug":"ca-central-1","name":"ca-central-1"},
                        {"slug":"ap-southeast-1","name":"ap-southeast-1"},
                        {"slug":"ap-southeast-2","name":"ap-southeast-2"},
                        {"slug":"us-west-1","name":"us-west-1"},
                        {"slug":"us-west-2","name":"us-west-2"}
                    ]
                },
                {
                    "slug":"t1.micro",
                    "vpsId":null,
                    "memory":627,
                    "vcpus":1,
                    "disk":50,
                    "priceMonthly":5,
                    "regions":[
                        {"slug":"eu-central-1","name":"eu-central-1"},
                        {"slug":"us-east-1","name":"us-east-1"},
                        {"slug":"us-east-2","name":"us-east-2"},
                        {"slug":"eu-north-1","name":"eu-north-1"},
                        {"slug":"ap-south-1","name":"ap-south-1"},
                        {"slug":"eu-west-3","name":"eu-west-3"},
                        {"slug":"eu-west-2","name":"eu-west-2"},
                        {"slug":"eu-west-1","name":"eu-west-1"},
                        {"slug":"ap-northeast-2","name":"ap-northeast-2"},
                        {"slug":"ap-northeast-1","name":"ap-northeast-1"},
                        {"slug":"sa-east-1","name":"sa-east-1"},
                        {"slug":"ca-central-1","name":"ca-central-1"},
                        {"slug":"ap-southeast-1","name":"ap-southeast-1"},
                        {"slug":"ap-southeast-2","name":"ap-southeast-2"},
                        {"slug":"us-west-1","name":"us-west-1"},
                        {"slug":"us-west-2","name":"us-west-2"}
                    ]
                },
                {
                    "slug":"t2.nano",
                    "vpsId":null,
                    "memory":512,
                    "vcpus":1,
                    "disk":50,
                    "priceMonthly":5,
                    "regions":[
                        {"slug":"eu-central-1","name":"eu-central-1"},
                        {"slug":"us-east-1","name":"us-east-1"},
                        {"slug":"us-east-2","name":"us-east-2"},
                        {"slug":"eu-north-1","name":"eu-north-1"},
                        {"slug":"ap-south-1","name":"ap-south-1"},
                        {"slug":"eu-west-3","name":"eu-west-3"},
                        {"slug":"eu-west-2","name":"eu-west-2"},
                        {"slug":"eu-west-1","name":"eu-west-1"},
                        {"slug":"ap-northeast-2","name":"ap-northeast-2"},
                        {"slug":"ap-northeast-1","name":"ap-northeast-1"},
                        {"slug":"sa-east-1","name":"sa-east-1"},
                        {"slug":"ca-central-1","name":"ca-central-1"},
                        {"slug":"ap-southeast-1","name":"ap-southeast-1"},
                        {"slug":"ap-southeast-2","name":"ap-southeast-2"},
                        {"slug":"us-west-1","name":"us-west-1"},
                        {"slug":"us-west-2","name":"us-west-2"}
                    ]
                },
                {
                    "slug":"t2.small",
                    "vpsId":null,
                    "memory":2048,
                    "vcpus":1,
                    "disk":50,
                    "priceMonthly":5,
                    "regions":[
                        {"slug":"eu-central-1","name":"eu-central-1"},
                        {"slug":"us-east-1","name":"us-east-1"},
                        {"slug":"us-east-2","name":"us-east-2"},
                        {"slug":"eu-north-1","name":"eu-north-1"},
                        {"slug":"ap-south-1","name":"ap-south-1"},
                        {"slug":"eu-west-3","name":"eu-west-3"},
                        {"slug":"eu-west-2","name":"eu-west-2"},
                        {"slug":"eu-west-1","name":"eu-west-1"},
                        {"slug":"ap-northeast-2","name":"ap-northeast-2"},
                        {"slug":"ap-northeast-1","name":"ap-northeast-1"},
                        {"slug":"sa-east-1","name":"sa-east-1"},
                        {"slug":"ca-central-1","name":"ca-central-1"},
                        {"slug":"ap-southeast-1","name":"ap-southeast-1"},
                        {"slug":"ap-southeast-2","name":"ap-southeast-2"},
                        {"slug":"us-west-1","name":"us-west-1"},
                        {"slug":"us-west-2","name":"us-west-2"}
                    ]
                },
                {
                    "slug":"t2.medium",
                    "vpsId":null,
                    "memory":4096,
                    "vcpus":2,
                    "disk":50,
                    "priceMonthly":5,
                    "regions":[
                        {"slug":"eu-central-1","name":"eu-central-1"},
                        {"slug":"us-east-1","name":"us-east-1"},
                        {"slug":"us-east-2","name":"us-east-2"},
                        {"slug":"eu-north-1","name":"eu-north-1"},
                        {"slug":"ap-south-1","name":"ap-south-1"},
                        {"slug":"eu-west-3","name":"eu-west-3"},
                        {"slug":"eu-west-2","name":"eu-west-2"},
                        {"slug":"eu-west-1","name":"eu-west-1"},
                        {"slug":"ap-northeast-2","name":"ap-northeast-2"},
                        {"slug":"ap-northeast-1","name":"ap-northeast-1"},
                        {"slug":"sa-east-1","name":"sa-east-1"},
                        {"slug":"ca-central-1","name":"ca-central-1"},
                        {"slug":"ap-southeast-1","name":"ap-southeast-1"},
                        {"slug":"ap-southeast-2","name":"ap-southeast-2"},
                        {"slug":"us-west-1","name":"us-west-1"},
                        {"slug":"us-west-2","name":"us-west-2"}
                    ]
                },
                {
                    "slug":"t2.large",
                    "vpsId":null,
                    "memory":8192,
                    "vcpus":2,
                    "disk":50,
                    "priceMonthly":5,
                    "regions":[
                        {"slug":"eu-central-1","name":"eu-central-1"},
                        {"slug":"us-east-1","name":"us-east-1"},
                        {"slug":"us-east-2","name":"us-east-2"},
                        {"slug":"eu-north-1","name":"eu-north-1"},
                        {"slug":"ap-south-1","name":"ap-south-1"},
                        {"slug":"eu-west-3","name":"eu-west-3"},
                        {"slug":"eu-west-2","name":"eu-west-2"},
                        {"slug":"eu-west-1","name":"eu-west-1"},
                        {"slug":"ap-northeast-2","name":"ap-northeast-2"},
                        {"slug":"ap-northeast-1","name":"ap-northeast-1"},
                        {"slug":"sa-east-1","name":"sa-east-1"},
                        {"slug":"ca-central-1","name":"ca-central-1"},
                        {"slug":"ap-southeast-1","name":"ap-southeast-1"},
                        {"slug":"ap-southeast-2","name":"ap-southeast-2"},
                        {"slug":"us-west-1","name":"us-west-1"},
                        {"slug":"us-west-2","name":"us-west-2"}
                    ]
                },
                {
                    "slug":"t2.xlarge",
                    "vpsId":null,
                    "memory":16384,
                    "vcpus":4,
                    "disk":50,
                    "priceMonthly":5,
                    "regions":[
                        {"slug":"eu-central-1","name":"eu-central-1"},
                        {"slug":"us-east-1","name":"us-east-1"},
                        {"slug":"us-east-2","name":"us-east-2"},
                        {"slug":"eu-north-1","name":"eu-north-1"},
                        {"slug":"ap-south-1","name":"ap-south-1"},
                        {"slug":"eu-west-3","name":"eu-west-3"},
                        {"slug":"eu-west-2","name":"eu-west-2"},
                        {"slug":"eu-west-1","name":"eu-west-1"},
                        {"slug":"ap-northeast-2","name":"ap-northeast-2"},
                        {"slug":"ap-northeast-1","name":"ap-northeast-1"},
                        {"slug":"sa-east-1","name":"sa-east-1"},
                        {"slug":"ca-central-1","name":"ca-central-1"},
                        {"slug":"ap-southeast-1","name":"ap-southeast-1"},
                        {"slug":"ap-southeast-2","name":"ap-southeast-2"},
                        {"slug":"us-west-1","name":"us-west-1"},
                        {"slug":"us-west-2","name":"us-west-2"}
                    ]
                },
                {
                    "slug":"t3.nano",
                    "vpsId":null,
                    "memory":512,
                    "vcpus":1,
                    "disk":50,
                    "priceMonthly":5,
                    "regions":[
                        {"slug":"eu-central-1","name":"eu-central-1"},
                        {"slug":"us-east-1","name":"us-east-1"},
                        {"slug":"us-east-2","name":"us-east-2"},
                        {"slug":"eu-north-1","name":"eu-north-1"},
                        {"slug":"ap-south-1","name":"ap-south-1"},
                        {"slug":"eu-west-3","name":"eu-west-3"},
                        {"slug":"eu-west-2","name":"eu-west-2"},
                        {"slug":"eu-west-1","name":"eu-west-1"},
                        {"slug":"ap-northeast-2","name":"ap-northeast-2"},
                        {"slug":"ap-northeast-1","name":"ap-northeast-1"},
                        {"slug":"sa-east-1","name":"sa-east-1"},
                        {"slug":"ca-central-1","name":"ca-central-1"},
                        {"slug":"ap-southeast-1","name":"ap-southeast-1"},
                        {"slug":"ap-southeast-2","name":"ap-southeast-2"},
                        {"slug":"us-west-1","name":"us-west-1"},
                        {"slug":"us-west-2","name":"us-west-2"}
                    ]
                },
                {
                    "slug":"t3.small",
                    "vpsId":null,
                    "memory":2048,
                    "vcpus":1,
                    "disk":50,
                    "priceMonthly":5,
                    "regions":[
                        {"slug":"eu-central-1","name":"eu-central-1"},
                        {"slug":"us-east-1","name":"us-east-1"},
                        {"slug":"us-east-2","name":"us-east-2"},
                        {"slug":"eu-north-1","name":"eu-north-1"},
                        {"slug":"ap-south-1","name":"ap-south-1"},
                        {"slug":"eu-west-3","name":"eu-west-3"},
                        {"slug":"eu-west-2","name":"eu-west-2"},
                        {"slug":"eu-west-1","name":"eu-west-1"},
                        {"slug":"ap-northeast-2","name":"ap-northeast-2"},
                        {"slug":"ap-northeast-1","name":"ap-northeast-1"},
                        {"slug":"sa-east-1","name":"sa-east-1"},
                        {"slug":"ca-central-1","name":"ca-central-1"},
                        {"slug":"ap-southeast-1","name":"ap-southeast-1"},
                        {"slug":"ap-southeast-2","name":"ap-southeast-2"},
                        {"slug":"us-west-1","name":"us-west-1"},
                        {"slug":"us-west-2","name":"us-west-2"}
                    ]
                },
                {
                    "slug":"t3.medium",
                    "vpsId":null,
                    "memory":4096,
                    "vcpus":2,
                    "disk":50,
                    "priceMonthly":5,
                    "regions":[
                        {"slug":"eu-central-1","name":"eu-central-1"},
                        {"slug":"us-east-1","name":"us-east-1"},
                        {"slug":"us-east-2","name":"us-east-2"},
                        {"slug":"eu-north-1","name":"eu-north-1"},
                        {"slug":"ap-south-1","name":"ap-south-1"},
                        {"slug":"eu-west-3","name":"eu-west-3"},
                        {"slug":"eu-west-2","name":"eu-west-2"},
                        {"slug":"eu-west-1","name":"eu-west-1"},
                        {"slug":"ap-northeast-2","name":"ap-northeast-2"},
                        {"slug":"ap-northeast-1","name":"ap-northeast-1"},
                        {"slug":"sa-east-1","name":"sa-east-1"},
                        {"slug":"ca-central-1","name":"ca-central-1"},
                        {"slug":"ap-southeast-1","name":"ap-southeast-1"},
                        {"slug":"ap-southeast-2","name":"ap-southeast-2"},
                        {"slug":"us-west-1","name":"us-west-1"},
                        {"slug":"us-west-2","name":"us-west-2"}
                    ]
                },
                {
                    "slug":"t3.large",
                    "vpsId":null,
                    "memory":8192,
                    "vcpus":2,
                    "disk":50,
                    "priceMonthly":5,
                    "regions":[
                        {"slug":"eu-central-1","name":"eu-central-1"},
                        {"slug":"us-east-1","name":"us-east-1"},
                        {"slug":"us-east-2","name":"us-east-2"},
                        {"slug":"eu-north-1","name":"eu-north-1"},
                        {"slug":"ap-south-1","name":"ap-south-1"},
                        {"slug":"eu-west-3","name":"eu-west-3"},
                        {"slug":"eu-west-2","name":"eu-west-2"},
                        {"slug":"eu-west-1","name":"eu-west-1"},
                        {"slug":"ap-northeast-2","name":"ap-northeast-2"},
                        {"slug":"ap-northeast-1","name":"ap-northeast-1"},
                        {"slug":"sa-east-1","name":"sa-east-1"},
                        {"slug":"ca-central-1","name":"ca-central-1"},
                        {"slug":"ap-southeast-1","name":"ap-southeast-1"},
                        {"slug":"ap-southeast-2","name":"ap-southeast-2"},
                        {"slug":"us-west-1","name":"us-west-1"},
                        {"slug":"us-west-2","name":"us-west-2"}
                    ]
                },
                {
                    "slug":"t3.xlarge",
                    "vpsId":null,
                    "memory":16384,
                    "vcpus":4,
                    "disk":50,
                    "priceMonthly":5,
                    "regions":[
                        {"slug":"eu-central-1","name":"eu-central-1"},
                        {"slug":"us-east-1","name":"us-east-1"},
                        {"slug":"us-east-2","name":"us-east-2"},
                        {"slug":"eu-north-1","name":"eu-north-1"},
                        {"slug":"ap-south-1","name":"ap-south-1"},
                        {"slug":"eu-west-3","name":"eu-west-3"},
                        {"slug":"eu-west-2","name":"eu-west-2"},
                        {"slug":"eu-west-1","name":"eu-west-1"},
                        {"slug":"ap-northeast-2","name":"ap-northeast-2"},
                        {"slug":"ap-northeast-1","name":"ap-northeast-1"},
                        {"slug":"sa-east-1","name":"sa-east-1"},
                        {"slug":"ca-central-1","name":"ca-central-1"},
                        {"slug":"ap-southeast-1","name":"ap-southeast-1"},
                        {"slug":"ap-southeast-2","name":"ap-southeast-2"},
                        {"slug":"us-west-1","name":"us-west-1"},
                        {"slug":"us-west-2","name":"us-west-2"}
                    ]
                },
                {
                    "slug":"t3.2xlarge",
                    "vpsId":null,
                    "memory":32768,
                    "vcpus":8,
                    "disk":50,
                    "priceMonthly":5,
                    "regions":[
                        {"slug":"eu-central-1","name":"eu-central-1"},
                        {"slug":"us-east-1","name":"us-east-1"},
                        {"slug":"us-east-2","name":"us-east-2"},
                        {"slug":"eu-north-1","name":"eu-north-1"},
                        {"slug":"ap-south-1","name":"ap-south-1"},
                        {"slug":"eu-west-3","name":"eu-west-3"},
                        {"slug":"eu-west-2","name":"eu-west-2"},
                        {"slug":"eu-west-1","name":"eu-west-1"},
                        {"slug":"ap-northeast-2","name":"ap-northeast-2"},
                        {"slug":"ap-northeast-1","name":"ap-northeast-1"},
                        {"slug":"sa-east-1","name":"sa-east-1"},
                        {"slug":"ca-central-1","name":"ca-central-1"},
                        {"slug":"ap-southeast-1","name":"ap-southeast-1"},
                        {"slug":"ap-southeast-2","name":"ap-southeast-2"},
                        {"slug":"us-west-1","name":"us-west-1"},
                        {"slug":"us-west-2","name":"us-west-2"}
                    ]
                }

            ]';

            return response($obj);
    }

    /**
     * getHetznerSizes
     * Gets available Droplet sizes on Hetzner
     *
     * @authenticated
     *
     *
     * @response {
     *  null
     * }
     */
    public function getHetznerSizes()
    {
        $user = Auth::user();
        $api_key_obj = $user->vps_keys()->where('provider', "Hetzner")->first();
        $api_key = $api_key_obj->api_token;

        if ($api_key) {
            $hetznerClient = new HetznerAPIClient($api_key);
            $sizes = $hetznerClient->serverTypes()->all();
            //$locations = $hetznerClient->locations()->all();


            foreach ($sizes as &$server_type) {
                $server_type = (array) $server_type;
                $server_type['memory'] = $server_type['memory']*1024;
                $server_type["regions"] = [];
                foreach ($server_type["prices"] as &$price) {
                    array_push($server_type["regions"], [
                        "slug" => $price->location,
                        "name" => $price->location]);
                    $server_type["priceMonthly"] = (float) $price->price_monthly->gross;
                }
            }

            $sizes = array_map(function ($tag) {
                return array(
                    'slug' => $tag['name'],
                    'vpsId' => (int) $tag['id'],
                    'memory' => (int) $tag['memory'],
                    'vcpus' => (int) $tag['cores'],
                    'disk' => (int) $tag['disk'],
                    'priceMonthly' => (float) $tag['priceMonthly'],
                    'regions' => $tag['regions'],
                );
            }, $sizes);

            return response()->json($sizes);
        } else {
            return response([
                'status' => 'error',
                'error' => 'vpsKey.notFound',
                'msg' => 'VpsKeyNotFound'
            ], 400);
        }
    }

}
