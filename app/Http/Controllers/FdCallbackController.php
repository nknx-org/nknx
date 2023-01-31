<?php

namespace App\Http\Controllers;

use App\Models\FdConfiguration;
use App\Models\FdDeployment;
use App\Models\FdEvent;

use App\Jobs\CreateNodeFromFdDeployment;
use App\Notifications\NodeProvisioned;
use App\Notifications\NodeProvisionedError;
use App\Notifications\NodeProvisionStarted;


use Illuminate\Http\Request;

class FdCallbackController extends Controller
{
    public function created(Request $request){

        $ip = $request->ip();
        $secret = $request->input('secret');
        $fdDeployment = FdDeployment::where("secret", $secret)->first();
        $fdDeployment->ip = $ip;
        $fdDeployment->save();

        $fdDeployment->fd_events()->save(new FdEvent([
            "event_code" => 11,
            "user_id" => $fdDeployment->fd_configuration->user_id
        ]));

        $user = $fdDeployment->fd_configuration->user;
        $user->notify(new NodeProvisionStarted($ip));


    }

    public function finished_installing(Request $request){

        $ip = $request->ip();
        $secret = $request->input('secret');
        $password = $request->input('password');
        $fdDeployment = FdDeployment::where("secret", $secret)->first();



        $fdDeployment->fd_events()->save(new FdEvent([
            "event_code" => 12,
            "user_id" => $fdDeployment->fd_configuration->user_id
        ]));

        $user = $fdDeployment->fd_configuration->user;


            $user->notify(new NodeProvisioned($ip, $password));

            CreateNodeFromFdDeployment::dispatch($fdDeployment)
            ->delay(now()->addMinutes(1))->onQueue('nodeCreator');

    }


    public function downloading_snapshot(Request $request){

        $ip = $request->ip();
        $secret = $request->input('secret');
        $fdDeployment = FdDeployment::where("secret", $secret)->first();
        $fdDeployment->save();

        $fdDeployment->fd_events()->save(new FdEvent([
            "event_code" => 30,
            "user_id" => $fdDeployment->fd_configuration->user_id
        ]));

    }

}
