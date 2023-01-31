<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;


use Inertia\Inertia;

use Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return Inertia::render('Notifications/Index', [
            'notifications' => Auth::user()->notifications()->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString()
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $notification)
    {
        Auth::user()->notifications
            ->where('id', $notification) // and/or ->where('type', $notificationType)
            ->first()
            ->markAsRead();
        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy( $notification)
    {

        Auth::user()->notifications
            ->where('id', $notification) // and/or ->where('type', $notificationType)
            ->first()
            ->delete();

        return Redirect::back();
    }
}
