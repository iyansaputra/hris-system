<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Leave\StoreLeaveRequest;
use App\Http\Requests\Leave\UpdateLeaveRequest;

class LeaveController extends Controller
{
    // Funsgsi Mengajukan Izin
    public function store(StoreLeaveRequest $request)
    {
        $leave = Leave::create([
            'user_id'    => Auth::id(),
            'type'       => $request->type,       
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'reason'     => $request->reason,
            'status'     => 'pending'          
        ]);

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dikirim. Menunggu persetujuan HRD.',
            'data'    => $leave
        ]);
    }

    // Lihat Riwayat Cuti
    public function myLeaves()
    {
        $leaves = Leave::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $leaves]);
    }

    // List Semua Pengajuan
    public function index()
    {
        $leaves = Leave::with('user')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')") 
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $leaves]);
    }

    // Fungsi Approve dan Reject HRD
    public function updateStatus(UpdateLeaveRequest $request, $id)
    {
        $leave = Leave::findOrFail($id);
        
        $leave->update([
            'status'         => $request->status,
            'rejection_note' => $request->status == 'rejected' ? $request->rejection_note : null
        ]);

        return response()->json([
            'message' => 'Status pengajuan berhasil diperbarui.',
            'data'    => $leave
        ]);
    }
}