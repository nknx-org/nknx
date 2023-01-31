<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Auth;

class EasyTransferConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $easyTransferConnections = Auth::user()->easy_transfer_connections()->get();
        return response()->json($easyTransferConnections);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make(Request::all(), [
            'endpoint_addr' => ['required'],
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }


        Auth::user()->easy_transfer_connections()->updateOrCreate(
            ['endpoint_addr' => Request::input('endpoint_addr'), 'ip' => Request::ip()],
            ["last_active" => new \DateTime()]
        );

        return response()->json('Success');

    }

}
