<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function publicScan()
    {
        return view('attendance.public');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = User::where('unique_code', $request->code)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kode unik tidak valid!',
            ], 404);
        }

        $today = Carbon::today();
        $now = Carbon::now();
        $settings = AttendanceSetting::first() ?? AttendanceSetting::create([
            'check_in_time' => '08:00',
            'check_out_time' => '17:00'
        ]);

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            // Check In Logic
            $checkInTime = Carbon::parse($settings->check_in_time);

            // Cant check-in 1 hour before scheduled time
            if ($now->lt($checkInTime->subMinutes(60))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum waktunya absen masuk. Silakan kembali pada jam ' . $settings->check_in_time,
                ], 422);
            }

            $status = $now->gt(Carbon::parse($settings->check_in_time)->addMinutes(15)) ? 'late' : 'present';

            Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'check_in' => $now->format('H:i:s'),
                'status' => $status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Absen Masuk. Selamat bekerja, ' . $user->name . '!',
                'user' => $user->name,
                'type' => 'check_in',
                'time' => $now->format('H:i'),
                'status' => $status
            ]);
        } else {
            // Check Out Logic
            if ($attendance->check_out) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen pulang hari ini.',
                ], 422);
            }

            $checkOutTime = Carbon::parse($settings->check_out_time);
            if ($now->lt($checkOutTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum waktunya absen pulang. Jam pulang: ' . $settings->check_out_time,
                ], 422);
            }

            $attendance->update([
                'check_out' => $now->format('H:i:s'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Absen Pulang. Hati-hati di jalan, ' . $user->name . '!',
                'user' => $user->name,
                'type' => 'check_out',
                'time' => $now->format('H:i')
            ]);
        }
    }

    public function settings()
    {
        $settings = AttendanceSetting::first() ?? AttendanceSetting::create();
        return view('attendance.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'check_in_time' => 'required',
            'check_out_time' => 'required',
        ]);

        $settings = AttendanceSetting::first() ?? new AttendanceSetting();
        $settings->check_in_time = $request->check_in_time;
        $settings->check_out_time = $request->check_out_time;
        $settings->save();

        return redirect()->back()->with('success', 'Pengaturan absensi berhasil diperbarui.');
    }

    public function report(Request $request)
    {
        $query = Attendance::with('user')->orderBy('date', 'desc');

        if ($request->date) {
            $query->where('date', $request->date);
        }

        $attendances = $query->paginate(20);
        return view('attendance.report', compact('attendances'));
    }

    public function getAttendanceCount(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $count = Attendance::where('user_id', $request->user_id)
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }
}
