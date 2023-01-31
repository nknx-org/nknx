<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;

class NetworkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $geoSummary = Cache::remember('node-geo-summary',300,function(){
            try {
                $apiRequest = Http::get('https://api.nkn.org/v1/geo/summary');
                return json_decode($apiRequest->body(), true, 512, JSON_BIGINT_AS_STRING);
            } catch (\Exception $e) {
                $returnarray = [];
                $returnarray["Payload"]['totalCount'] = 0;
                return $returnarray;
            }
        });
        return Inertia::render('Network/Index', []);
    }
}
