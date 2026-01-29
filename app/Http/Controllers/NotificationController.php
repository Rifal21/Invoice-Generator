<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->unreadNotifications;
        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications
        ]);
    }

    public function markAsRead(Request $request)
    {
        if ($request->id) {
            auth()->user()->notifications()->where('id', $request->id)->first()->markAsRead();
        } else {
            auth()->user()->unreadNotifications->markAsRead();
        }

        return response()->json(['status' => 'success']);
    }
}
