<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;


use Inertia\Inertia;

use Auth;

class NotificationSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return Inertia::render('NotificationSettings/Index', []);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        Auth::user()->notificationPreferences = Request::all();
        Auth::user()->save();
        return Redirect::back()->with('success', 'Configuration saved');
    }
}
