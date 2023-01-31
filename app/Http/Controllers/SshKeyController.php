<?php

namespace App\Http\Controllers;

use App\Models\SshKey;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Rules\SshKey as SshKeyRule;

use Illuminate\Support\Facades\Validator;

use Inertia\Inertia;

use Auth;


class SshKeyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('SshKeys/Index', [
            'ssh-keys' => Auth::user()->ssh_keys()
                ->get()
                ->transform(function ($ssh_key) {
                    return [
                        'id' => $ssh_key->id,
                        'name' => $ssh_key->name,
                        'fingerprint' => $ssh_key->fingerprint,
                        'created_at' => $ssh_key->created_at,
                    ];
                }),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {

        $validator = Validator::make(Request::all(), [
            'name' => ['required', 'max:50'],
            'pubkey' => ['required', new SshKeyRule],
        ]);

        if ($validator->fails()) {
            return Redirect::back()->with('error', 'SSH key invalid');
        }



        $keyWithoutPemWrapper = preg_replace(
            '/^-----BEGIN (?:[A-Z]+ )?PUBLIC KEY-----([A-Za-z0-9\\/\\+\\s=]+)-----END (?:[A-Z]+ )?PUBLIC KEY-----$/ms',
            '\\1',
            Request::get('pubkey')
        );
        $keyDataWithoutSpaces = preg_replace('/\\s+/', '', $keyWithoutPemWrapper);

        $binaryKey = base64_decode($keyDataWithoutSpaces);

        $fingerprint = hash('sha256', $binaryKey);

        Auth::user()->ssh_keys()->create([
            'name' => Request::get('name'),
            'pubkey' => Request::get('pubkey'),
            'fingerprint' => $fingerprint,
        ]);

        return Redirect::back()->with('success', 'SSH key created.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SshKey  $sshKey
     * @return \Illuminate\Http\Response
     */
    public function destroy(SshKey $sshKey)
    {
        $ssh_key = Auth::user()->ssh_keys()->find($sshKey->id);

        if ($ssh_key) {
            $ssh_key->delete();
            return Redirect::back()->with('success', 'SSH key deleted.');
        } else {
            return Redirect::back()->with('error', 'Resource not found');
        }
    }
}
