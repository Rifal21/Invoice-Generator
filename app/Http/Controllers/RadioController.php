<?php

namespace App\Http\Controllers;

use App\Models\SongRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RadioController extends Controller
{
    private $azuraBaseUrl = 'https://radio.fkstudio.my.id/api';
    private $stationShortCode = 'radio_fkstudio';

    public function index()
    {
        // Restriction kept as per original file, though it might be worth removing for a real app
        if (auth()->user()->name !== 'Rifal Kurniawan') {
            abort(403, 'Hanya Rifal yang boleh mengakses radio ini!');
        }

        return view('radio.index');
    }

    public function search(Request $request)
    {
        if (auth()->user()->name !== 'Rifal Kurniawan') abort(403);

        $query = $request->get('q');
        if (!$query) return response()->json([]);

        try {
            $response = Http::timeout(5)->get("{$this->azuraBaseUrl}/station/{$this->stationShortCode}/requests", [
                'search' => $query
            ]);

            if ($response->successful()) {
                $items = collect($response->json())
                    ->map(function ($item) {
                        return [
                            'id' => $item['request_id'], // Map request_id to id
                            'title' => $item['song']['text'] ?? ($item['song']['title'] . ' - ' . $item['song']['artist']),
                            'artist' => $item['song']['artist'] ?? '',
                            'thumbnail' => $item['song']['art'] ?? '',
                            'url' => $item['request_url'] ?? ''
                        ];
                    })->values();
                return response()->json($items);
            }
        } catch (\Exception $e) {
            Log::error("AzuraCast Search Error: " . $e->getMessage());
        }

        return response()->json([], 500);
    }

    public function requestSong(Request $request)
    {
        if (auth()->user()->name !== 'Rifal Kurniawan') abort(403);

        $request->validate([
            'video_id' => 'required', // This will now hold the request_id
        ]);

        try {
            $requestId = $request->video_id;
            $response = Http::timeout(5)->post("{$this->azuraBaseUrl}/station/{$this->stationShortCode}/request/{$requestId}");

            if ($response->successful()) {
                // Log locally just for record
                SongRequest::create([
                    'video_id' => $requestId,
                    'title' => $request->title ?? 'Unknown Song',
                    'thumbnail' => $request->thumbnail,
                    'requested_by' => auth()->user()->name,
                    'status' => 'pending'
                ]);

                return response()->json(['message' => 'Lagu berhasil direquest ke Radio FKStudio!']);
            } else {
                // Get actual error message from API
                $errorMsg = $response->json()['message'] ?? 'Gagal request lagu. Mungkin sudah direquest atau ada batasan waktu.';
                return response()->json(['message' => $errorMsg], 400);
            }
        } catch (\Exception $e) {
            Log::error("AzuraCast Request Error: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan koneksi ke server radio.'], 500);
        }
    }

    public function getCurrentStatus()
    {
        if (auth()->user()->name !== 'Rifal Kurniawan') abort(403);

        try {
            $response = Http::timeout(3)->get("{$this->azuraBaseUrl}/nowplaying/{$this->stationShortCode}");

            if ($response->successful()) {
                $data = $response->json();

                $nowPlaying = [
                    'title' => $data['now_playing']['song']['title'] ?? 'Unknown Title',
                    'artist' => $data['now_playing']['song']['artist'] ?? 'Unknown Artist',
                    'text' => $data['now_playing']['song']['text'] ?? '',
                    'art' => $data['now_playing']['song']['art'] ?? '',
                    'started_at' => $data['now_playing']['played_at'] ?? 0,
                    'duration' => $data['now_playing']['duration'] ?? 0,
                    'is_live' => $data['live']['is_live'] ?? false,
                    'listeners' => $data['listeners']['current'] ?? 0
                ];

                return response()->json([
                    'current' => $nowPlaying,
                    // Queue data isn't easily mapped from "next_playing" singular, but we can pass it if needed
                    // For now, let's just pass empty queue or simulate it
                    'queue' => []
                ]);
            }
        } catch (\Exception $e) {
            Log::error("AzuraCast Status Error" . $e->getMessage());
        }

        return response()->json([
            'current' => null,
            'queue' => []
        ]);
    }

    public function skipCurrent()
    {
        // Not supported in public API usually
        return response()->json(['status' => 'error', 'message' => 'Skip tidak tersedia di live radio.']);
    }
}
