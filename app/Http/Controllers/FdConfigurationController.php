<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str;

use App\Models\FdConfiguration;

class FdConfigurationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Request::validate([
            'beneficiary_addr' => ['required', 'max:50'],
        ]);

        $syncMode = Request::get('sync_mode', 'fast_sync');
        $fdConfiguration = new FdConfiguration(
            array_merge(
                [
                    "uuid" => Str::uuid()->toString()
                ],Request::all()
            )
            );

        switch ($syncMode) {
            case 'fast_sync':
                $fdConfiguration->fast_sync = true;
                $fdConfiguration->light_sync = false;
                $fdConfiguration->download_chain = false;
                break;
            case 'light_sync':
                $fdConfiguration->fast_sync = false;
                $fdConfiguration->light_sync = true;
                $fdConfiguration->download_chain = false;
                break;

            case 'download_chain':
                $fdConfiguration->fast_sync = false;
                $fdConfiguration->light_sync = false;
                $fdConfiguration->download_chain = true;
                break;
        }


        Auth::user()->fd_configurations()->save($fdConfiguration);

        return Redirect::back()->with('success', 'FastDeploy Configuration created.');

    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(FdConfiguration $configuration)
    {
        if ($configuration->user->id == Auth::user()->id) {
            $configuration->delete();
            return Redirect::back()->with('success', 'FastDeploy Configuration deleted.');
        } else {
            return Redirect::back()->with('error', 'Resource not found');
        }
    }
}
