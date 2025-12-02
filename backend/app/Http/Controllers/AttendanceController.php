<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendances;
use Illuminate\Http\Request;
use App\Http\Requests\Absen\StoreAbsenRequest;
use App\Http\Requests\Absen\UpdateAbsenRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function clockIn(StoreAbsenRequest $request)
    {
        $user = Auth::user();

        $now = Carbon::now('Asia/Jakarta');
        $jamMasuk = Carbon::createFromTime(8, 0, 0, 'Asia/Jakarta');

        $status = $now->gt($jamMasuk) ? 'terlambat' : 'hadir';

        $attendance = Attendances::create([
            'user_id' => Auth::id(),
            'date' => now()->toDateString(),
            'time_in' => now()->toTimeString(),
            'status' => $status, 
        ]);

        return response()->json([
            'message' => $status == 'terlambat' ? 'Absen berhasil (Anda Terlambat)' : 'Absen masuk berhasil (Tepat Waktu)',
            'data' => $attendance
        ]);
    }

    public function clockOut(UpdateAbsenRequest $request)
    {
        $now = Carbon::now('Asia/Jakarta');
        $jamPulang = Carbon::createFromTime(17, 0, 0, 'Asia/Jakarta');

        $attendance = Attendances::where('user_id', Auth::id())
            ->where('date', $now->toDateString())
            ->first();

        $updateData = [
            'time_out' => $now->toTimeString()
        ];

        if ($now->lt($jamPulang) && $attendance->status == 'hadir') {
            $updateData['status'] = 'pulang_cepat';
        }

        $attendance->update($updateData);

        $message = 'Hati-hati di jalan!';
        if ($attendance->status == 'terlambat') {
             $message = 'Absen pulang berhasil (Status tetap Terlambat).';
        } elseif (($updateData['status'] ?? '') == 'pulang_cepat') {
             $message = 'Anda pulang lebih awal dari jam 17:00.';
        }

        return response()->json([
            'message' => $message,
            'data' => $attendance
        ]);
    }

    public function status()
    {
        $attendance = Attendances::where('user_id', Auth::id())
            ->where('date', now()->toDateString())
            ->first();

        if (!$attendance) {
            return response()->json(['status' => 'not_present', 'data' => null]);
        }

        if ($attendance->time_out === null) {
            return response()->json(['status' => 'checked_in', 'data' => $attendance]);
        }

        return response()->json(['status' => 'checked_out', 'data' => $attendance]);
    }

    //Tampil data harian
    public function todayRecap()
    {
        $today = Attendances::with('user')
            ->where('date', Carbon::now('Asia/Jakarta')->toDateString()) 
            ->orderBy('time_in', 'desc')
            ->get();

        return response()->json([
            'data' => $today
        ]);
    }

    public function index()
    {
        $history = Attendances::with('user')
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $history
        ]);
    }
}