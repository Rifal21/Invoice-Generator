<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Services\GeolocationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function publicScan()
    {
        $settings = AttendanceSetting::first() ?? new AttendanceSetting([
            'check_in_time' => '08:00',
            'check_out_time' => '17:00',
            'require_photo' => true,
            'require_location' => false,
            'strict_time' => true,
            'allowed_radius' => 100
        ]);
        return view('attendance.public', compact('settings'));
    }

    public function checkStatus(Request $request)
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
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan absensi hari ini.',
            ]);
        }

        $distanceText = null;
        if ($attendance->check_in_distance) {
            $distanceText = number_format($attendance->check_in_distance, 0) . ' meter dari kantor';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user_name' => $user->name,
                'user_role' => $user->role,
                'date' => Carbon::parse($attendance->date)->isoFormat('dddd, D MMMM Y'),
                'check_in' => Carbon::parse($attendance->check_in)->format('H:i'),
                'check_out' => $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : null,
                'status' => $attendance->status,
                'distance' => $distanceText,
            ],
        ]);
    }

    public function scan(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|max:5120', // 5MB max
            'device_id' => 'nullable|string',
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
            'check_out_time' => '17:00',
            'allowed_radius' => 100,
            'require_photo' => true,
            'require_location' => false,
            'strict_time' => true,
        ]);

        // Validate location if required
        if ($settings->require_location) {
            if (!$request->filled('latitude') || !$request->filled('longitude')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lokasi GPS diperlukan! Aktifkan GPS Anda.',
                ], 422);
            }

            if (!$settings->office_latitude || !$settings->office_longitude) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lokasi kantor belum dikonfigurasi. Hubungi admin.',
                ], 422);
            }

            $distance = GeolocationService::calculateDistance(
                $request->latitude,
                $request->longitude,
                $settings->office_latitude,
                $settings->office_longitude
            );

            if ($distance > $settings->allowed_radius) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda terlalu jauh dari kantor! Jarak: ' . GeolocationService::formatDistance($distance),
                    'distance' => $distance,
                    'allowed_radius' => $settings->allowed_radius,
                ], 422);
            }
        } else {
            $distance = null;
        }

        // Validate photo if required
        if ($settings->require_photo && !$request->hasFile('photo')) {
            return response()->json([
                'success' => false,
                'message' => 'Foto selfie diperlukan untuk absensi!',
            ], 422);
        }

        // Get client information
        $clientIp = $request->ip();
        $userAgent = $request->userAgent();
        $deviceId = $request->device_id ?? $this->generateDeviceFingerprint($request);

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            // Check In Logic
            $checkInTime = Carbon::parse($settings->check_in_time);

            // Strict time: Can't check-in more than 1 hour before
            if ($settings->strict_time && $now->lt($checkInTime->copy()->subMinutes(60))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum waktunya absen masuk. Silakan kembali pada jam ' . $checkInTime->copy()->subMinutes(60)->format('H:i'),
                ], 422);
            }

            $status = $now->gt(Carbon::parse($settings->check_in_time)->addMinutes(15)) ? 'late' : 'present';

            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoName = 'check_in_' . $user->id . '_' . now()->format('Y-m-d_His') . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('attendance/photos', $photoName, 'public');
            }

            Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'check_in' => $now->format('H:i:s'),
                'status' => $status,
                'check_in_latitude' => $request->latitude,
                'check_in_longitude' => $request->longitude,
                'check_in_distance' => $distance,
                'check_in_photo' => $photoPath,
                'check_in_ip' => $clientIp,
                'check_in_user_agent' => $userAgent,
                'check_in_device_id' => $deviceId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Absen Masuk. Selamat bekerja, ' . $user->name . '!',
                'user' => $user->name,
                'type' => 'check_in',
                'time' => $now->format('H:i'),
                'status' => $status,
                'distance' => $distance ? GeolocationService::formatDistance($distance) : null,
            ]);
        } else {
            // Check Out Logic
            if ($attendance->check_out) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen pulang hari ini.',
                ], 422);
            }

            // Validate it's the same device
            if ($attendance->check_in_device_id && $attendance->check_in_device_id !== $deviceId) {
                Log::warning('Different device detected for check-out', [
                    'user_id' => $user->id,
                    'check_in_device' => $attendance->check_in_device_id,
                    'check_out_device' => $deviceId,
                ]);
            }

            $checkOutTime = Carbon::parse($settings->check_out_time);
            if ($settings->strict_time && $now->lt($checkOutTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum waktunya absen pulang. Jam pulang: ' . $settings->check_out_time,
                ], 422);
            }

            // Handle photo upload for check out
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoName = 'check_out_' . $user->id . '_' . now()->format('Y-m-d_His') . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('attendance/photos', $photoName, 'public');
            }

            $attendance->update([
                'check_out' => $now->format('H:i:s'),
                'check_out_latitude' => $request->latitude,
                'check_out_longitude' => $request->longitude,
                'check_out_distance' => $distance,
                'check_out_photo' => $photoPath,
                'check_out_ip' => $clientIp,
                'check_out_user_agent' => $userAgent,
                'check_out_device_id' => $deviceId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Absen Pulang. Hati-hati di jalan, ' . $user->name . '!',
                'user' => $user->name,
                'type' => 'check_out',
                'time' => $now->format('H:i'),
                'distance' => $distance ? GeolocationService::formatDistance($distance) : null,
            ]);
        }
    }

    /**
     * Generate device fingerprint from request
     */
    private function generateDeviceFingerprint(Request $request): string
    {
        $fingerprint = implode('|', [
            $request->userAgent() ?? '',
            $request->ip() ?? '',
            $request->header('Accept-Language') ?? '',
        ]);

        return hash('sha256', $fingerprint);
    }

    public function settings()
    {
        $settings = AttendanceSetting::first() ?? AttendanceSetting::create([
            'check_in_time' => '08:00',
            'check_out_time' => '17:00',
            'require_photo' => true,
            'require_location' => false,
            'strict_time' => true,
            'allowed_radius' => 100
        ]);
        return view('attendance.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'check_in_time' => 'required',
            'check_out_time' => 'required',
            'office_latitude' => 'nullable|numeric',
            'office_longitude' => 'nullable|numeric',
            'allowed_radius' => 'nullable|integer|min:10|max:10000',
            'require_photo' => 'boolean',
            'require_location' => 'boolean',
            'strict_time' => 'boolean',
        ]);

        $settings = AttendanceSetting::first() ?? new AttendanceSetting();

        $data = $request->all();
        $data['require_photo'] = $request->has('require_photo');
        $data['require_location'] = $request->has('require_location');
        $data['strict_time'] = $request->has('strict_time');

        $settings->fill($data);
        $settings->save();

        return redirect()->back()->with('success', 'Pengaturan absensi berhasil diperbarui.');
    }

    public function report(Request $request)
    {
        $type = $request->get('type', 'daily'); // daily or rekap

        if ($type === 'rekap') {
            $month = $request->get('month', now()->format('Y-m'));
            $startOfMonth = Carbon::parse($month)->startOfMonth();
            $endOfMonth = Carbon::parse($month)->endOfMonth();

            // Get all days in month
            $dates = [];
            $current = $startOfMonth->copy();
            while ($current <= $endOfMonth) {
                $dates[] = $current->copy();
                $current->addDay();
            }

            $users = User::orderBy('name')->get();
            $attendances = Attendance::whereBetween('date', [$startOfMonth, $endOfMonth])->get();

            $recapData = [];
            foreach ($users as $user) {
                $userAttendances = $attendances->where('user_id', $user->id);

                $summary = [
                    'present' => $userAttendances->where('status', 'present')->count(),
                    'late' => $userAttendances->where('status', 'late')->count(),
                    'absent' => $userAttendances->where('status', 'absent')->count(), // Explicit absent status
                    'alpha' => 0, // No record found
                ];

                // Calculate daily status map
                $daily = [];
                foreach ($dates as $date) {
                    $att = $userAttendances->first(function ($item) use ($date) {
                        return Carbon::parse($item->date)->isSameDay($date);
                    });

                    if ($att) {
                        $daily[$date->format('Y-m-d')] = [
                            'status' => $att->status,
                            'in' => $att->check_in ? Carbon::parse($att->check_in)->format('H:i') : '-',
                            'out' => $att->check_out ? Carbon::parse($att->check_out)->format('H:i') : '-',
                            'notes' => $att->notes
                        ];
                    } else {
                        // If no record and date is past, count as Alpha (unless simple 'absent' status is used manually)
                        if ($date->lt(now())) {
                            $daily[$date->format('Y-m-d')] = ['status' => 'alpha', 'in' => '-', 'out' => '-'];
                            $summary['alpha']++;
                        } else {
                            $daily[$date->format('Y-m-d')] = ['status' => 'future', 'in' => '-', 'out' => '-'];
                        }
                    }
                }

                $recapData[] = [
                    'user' => $user,
                    'summary' => $summary,
                    'daily' => $daily
                ];
            }

            return view('attendance.report', compact('recapData', 'dates', 'month', 'type'));
        } else {
            // Existing Daily Logic
            $query = Attendance::with(['user', 'approver'])->orderBy('date', 'desc')->orderBy('check_in', 'desc');

            if ($request->date) {
                $query->where('date', $request->date);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $attendances = $query->paginate(20);
            $users = User::orderBy('name')->get();

            return view('attendance.report', compact('attendances', 'users', 'type'));
        }
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
            ->whereIn('status', ['present', 'late'])
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    public function getPresentDates(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $dates = Attendance::where('user_id', $request->user_id)
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->whereIn('status', ['present', 'late'])
            ->pluck('date');

        return response()->json([
            'success' => true,
            'dates' => $dates,
        ]);
    }

    /**
     * Manual entry by admin with approval
     */
    public function manualEntry(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in' => 'required',
            'check_out' => 'nullable',
            'status' => 'required|in:present,late,absent',
            'correction_reason' => 'required|string|max:500',
        ]);

        // Check if already exists
        $existing = Attendance::where('user_id', $request->user_id)
            ->where('date', $request->date)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Absensi untuk tanggal tersebut sudah ada.');
        }

        Attendance::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
            'is_manual_entry' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'correction_reason' => $request->correction_reason,
        ]);

        return redirect()->back()->with('success', 'Absensi manual berhasil ditambahkan.');
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'check_in' => 'required',
            'check_out' => 'nullable',
            'status' => 'required|in:present,late,absent',
            'date' => 'required|date',
        ]);

        $attendance->update([
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
            'date' => $request->date,
        ]);

        return redirect()->back()->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function destroy(Attendance $attendance)
    {
        // Delete photos if exist
        if ($attendance->check_in_photo) {
            Storage::disk('public')->delete($attendance->check_in_photo);
        }
        if ($attendance->check_out_photo) {
            Storage::disk('public')->delete($attendance->check_out_photo);
        }

        $attendance->delete();

        return redirect()->back()->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Show attendance detail with security info
     */
    public function show(Attendance $attendance)
    {
        $attendance->load(['user', 'approver']);
        return view('attendance.show', compact('attendance'));
    }

    public function bulkCreate()
    {
        $users = User::orderBy('name')->get();
        return view('attendance.bulk', compact('users'));
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'check_in' => 'required',
            'check_out' => 'nullable',
            'status' => 'required|in:present,late,absent,sick,permit', // sick, permit added for bulk convenience
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'correction_reason' => 'required|string|max:500',
        ]);

        $count = 0;
        foreach ($request->user_ids as $userId) {
            // Check if already exists
            $existing = Attendance::where('user_id', $userId)
                ->where('date', $request->date)
                ->first();

            if ($existing) {
                // Determine if we should update or skip? Use update for bulk action usually
                $existing->update([
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'status' => $request->status,
                    'is_manual_entry' => true,
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'correction_reason' => $request->correction_reason . ' (Bulk Update)',
                ]);
                $count++;
            } else {
                Attendance::create([
                    'user_id' => $userId,
                    'date' => $request->date,
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'status' => $request->status,
                    'is_manual_entry' => true,
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'correction_reason' => $request->correction_reason . ' (Bulk Create)',
                ]);
                $count++;
            }
        }

        return redirect()->route('attendance.report')->with('success', "Berhasil memproses absensi untuk $count user.");
    }
}
