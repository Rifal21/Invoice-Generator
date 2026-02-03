<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitorController extends Controller
{
    public function index()
    {
        $sessions = DB::table('sessions')
            ->whereNotNull('user_id')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select('sessions.*', 'users.name as user_name', 'users.email')
            ->orderBy('sessions.last_activity', 'desc')
            ->get();

        $activeUsers = $sessions->map(function ($session) {
            return [
                'name' => $session->user_name,
                'email' => $session->email,
                'ip_address' => $session->ip_address,
                'device' => $this->parseUserAgent($session->user_agent),
                'last_activity' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                'is_current_device' => $session->id === session()->getId(),
            ];
        });

        return view('monitor.index', compact('activeUsers'));
    }

    private function parseUserAgent($userAgent)
    {
        $platform = 'Unknown OS';
        $browser = 'Unknown Browser';

        // Simple Platform Detection
        if (preg_match('/windows|win32/i', $userAgent)) $platform = 'Windows';
        elseif (preg_match('/macintosh|mac os x/i', $userAgent)) $platform = 'macOS';
        elseif (preg_match('/linux/i', $userAgent)) $platform = 'Linux';
        elseif (preg_match('/android/i', $userAgent)) $platform = 'Android';
        elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) $platform = 'iOS';

        // Simple Browser Detection
        if (preg_match('/MSIE/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) $browser = 'Internet Explorer';
        elseif (preg_match('/Firefox/i', $userAgent)) $browser = 'Firefox';
        elseif (preg_match('/Chrome/i', $userAgent)) $browser = 'Chrome';
        elseif (preg_match('/Safari/i', $userAgent)) $browser = 'Safari';
        elseif (preg_match('/Opera/i', $userAgent)) $browser = 'Opera';
        elseif (preg_match('/Netscape/i', $userAgent)) $browser = 'Netscape';
        elseif (preg_match('/Edge/i', $userAgent)) $browser = 'Edge';

        return [
            'platform' => $platform,
            'browser' => $browser,
            'string' => $userAgent
        ];
    }
}
