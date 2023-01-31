<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Inertia\Inertia;

use Auth;

class FastDeployController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('FastDeploy/Index', [
            'vps_keys' => Auth::user()->vps_keys()
                ->orderBy('created_at', 'desc')
                ->get()
                ->transform(function ($vps_key) {
                    return [
                        'provider' => $vps_key->provider,
                        'profile_name' => $vps_key->profile_name,
                        'id' => $vps_key->id,
                    ];
                }
            ),
            'ssh_keys' => Auth::user()->ssh_keys()
                ->orderBy('created_at', 'desc')
                ->get()
                ->transform(function ($ssh_key) {
                    return [
                        'id' => $ssh_key->id,
                        'name' => $ssh_key->name,
                        'fingerprint' => $ssh_key->fingerprint,
                        'created_at' => $ssh_key->created_at,
                    ];
                }
            ),
            'fd_configurations' => Auth::user()->fd_configurations()
                ->with('fd_deployments')
                ->orderBy('created_at', 'desc')
                ->paginate(10,['*'], 'fd_configuration_page')
                ->withQueryString()
                ->through(function ($fd_configuration) {
                    return [
                        'id' => $fd_configuration->id,
                        'uuid' => $fd_configuration->uuid,
                        'label' => $fd_configuration->label,
                        'beneficiary_addr' => $fd_configuration->beneficiary_addr,
                        'sync_mode' => $fd_configuration->fast_sync ? 'Fast Sync' : ($fd_configuration->light_sync ? 'Lite Sync' : 'Foreign Chain'),
                        'enable_web_ui' => $fd_configuration->enable_web_ui,
                        'disable_ufw' => $fd_configuration->disable_ufw,
                        'send_email' => $fd_configuration->send_email,
                        'num_deployments' => $fd_configuration->fd_deployments ? count($fd_configuration->fd_deployments) : 0,
                    ];
                }),
            'fd_events' => Auth::user()->fd_events()
                ->with('fd_deployment')
                ->orderBy('created_at', 'desc')
                ->paginate(10,['*'], 'fd_events_page')
                ->withQueryString()
                ->through(function ($fd_event) {
                    return [
                        'event_code' => $fd_event->event_code,
                        'created_at' => $fd_event->created_at,
                        'ip' => $fd_event->fd_deployment->ip,
                        'provider' => $fd_event->fd_deployment->provider,
                        'label' => $fd_event->fd_deployment->label,
                    ];
                }),


        ]);
    }
}
