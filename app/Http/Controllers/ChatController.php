<?php

namespace App\Http\Controllers;

use App\Models\RadioMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat.index');
    }

    public function getMessages()
    {
        $messages = RadioMessage::orderBy('created_at', 'desc')->take(50)->get()->reverse()->values();
        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate(['message' => 'required']);

        $message = RadioMessage::create([
            'user_name' => auth()->user()->name,
            'message' => $request->message
        ]);

        // Send notification to other users
        $recipients = \App\Models\User::where('id', '!=', auth()->id())->get();
        \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\NewChatMessage($message));

        return response()->json(['status' => 'success', 'message' => $message]);
    }

    public function latestMessage()
    {
        $latest = RadioMessage::latest()->first();
        return response()->json([
            'id' => $latest ? $latest->id : 0,
            'user_name' => $latest ? $latest->user_name : '',
            'message' => $latest ? $latest->message : '',
            'created_at' => $latest ? $latest->created_at : null
        ]);
    }

    public function clearChat()
    {
        if (auth()->user()->name !== 'Rifal Kurniawan') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        RadioMessage::truncate();
        return response()->json(['status' => 'success']);
    }

    public function getUsers()
    {
        $users = \App\Models\User::select('id', 'name')->get();
        return response()->json($users);
    }
}
