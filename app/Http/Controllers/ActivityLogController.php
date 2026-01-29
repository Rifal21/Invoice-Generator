<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        if (auth()->user()->name !== 'Rifal Kurniawan') {
            abort(403, 'Unauthorized action.');
        }

        $logs = \App\Models\ActivityLog::latest()->paginate(20);

        return view('activity-logs.index', compact('logs'));
    }
}
