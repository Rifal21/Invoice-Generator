<?php

namespace App\Http\Controllers;

use App\Models\SongRequest;
use App\Models\RadioMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RadioController extends Controller
{
    public function index()
    {
        $current = SongRequest::where('status', 'playing')->first();

        if (!$current) {
            $current = SongRequest::where('status', 'pending')->orderBy('created_at', 'asc')->first();
            if ($current) {
                $current->update(['status' => 'playing', 'started_at' => now()]);
            }
        }

        return view('radio.index', compact('current'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        if (!$query) return response()->json([]);

        $apiKey = config('services.youtube.api_key');

        if (!$apiKey) {
            return response()->json([
                [
                    'id' => 'dQw4w9WgXcQ',
                    'title' => '[DEMO] Rick Astley - Never Gonna Give You Up',
                    'thumbnail' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg'
                ]
            ]);
        }

        try {
            $response = Http::get("https://www.googleapis.com/youtube/v3/search", [
                'part' => 'snippet',
                'q' => $query,
                'type' => 'video',
                'maxResults' => 10,
                'key' => $apiKey,
            ]);

            if ($response->successful()) {
                $items = collect($response->json()['items'] ?? [])
                    ->filter(fn($item) => isset($item['id']['videoId']))
                    ->map(function ($item) {
                        return [
                            'id' => $item['id']['videoId'],
                            'title' => $item['snippet']['title'],
                            'thumbnail' => $item['snippet']['thumbnails']['default']['url'] ?? '',
                        ];
                    })->values();
                return response()->json($items);
            }
        } catch (\Exception $e) {
            Log::error("YouTube API Error: " . $e->getMessage());
        }

        return response()->json([], 500);
    }

    public function requestSong(Request $request)
    {
        $request->validate([
            'video_id' => 'required',
            'title' => 'required',
            'thumbnail' => 'nullable'
        ]);

        SongRequest::create([
            'video_id' => $request->video_id,
            'title' => $request->title,
            'thumbnail' => $request->thumbnail,
            'requested_by' => auth()->user()->name,
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Lagu berhasil ditambahkan ke antrean!']);
    }

    public function getCurrentStatus()
    {
        $current = SongRequest::where('status', 'playing')->first();

        // If nothing playing, check if there's any pending
        if (!$current) {
            $next = SongRequest::where('status', 'pending')->orderBy('created_at', 'asc')->first();
            if ($next) {
                $next->update([
                    'status' => 'playing',
                    'started_at' => now()
                ]);
                $current = $next;
            }
        }

        // Get queue
        $queue = SongRequest::where('status', 'pending')->orderBy('created_at', 'asc')->get();
        // Get recent chat
        $messages = RadioMessage::orderBy('created_at', 'desc')->take(20)->get()->reverse()->values();

        return response()->json([
            'current' => $current,
            'queue' => $queue,
            'messages' => $messages,
            'server_time' => now()->toIso8601String()
        ]);
    }

    public function postMessage(Request $request)
    {
        $request->validate(['message' => 'required']);

        RadioMessage::create([
            'user_name' => auth()->user()->name,
            'message' => $request->message
        ]);

        return response()->json(['status' => 'success']);
    }

    public function skipCurrent()
    {
        $current = SongRequest::where('status', 'playing')->first();
        if ($current) {
            $current->update(['status' => 'completed']);
        }
        return response()->json(['status' => 'success']);
    }
}
