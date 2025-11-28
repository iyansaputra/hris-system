<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\KaryawanService;
use App\Http\Requests\Karyawan\StoreKaryawanRequest;
use App\Http\Requests\Karyawan\UpdateKaryawanRequest;
use Illuminate\Http\JsonResponse;

class KaryawanController extends Controller
{
    protected $karyawanService;

    public function __construct(KaryawanService $karyawanService)
    {
        $this->karyawanService = $karyawanService;
    }

    public function index(): JsonResponse
    {
        $karyawans = $this->karyawanService->getAllKaryawan();
        
        return response()->json([
            'status' => 'success',
            'data'   => $karyawans
        ]);
    }

    public function store(StoreKaryawanRequest $request): JsonResponse
    {
        try {
            $karyawan = $this->karyawanService->store($request->validated());

            return response()->json([
                'status'  => 'success',
                'message' => 'Karyawan berhasil ditambahkan',
                'data'    => $karyawan
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $karyawan = \App\Models\Karyawans::with(['user', 'divisions'])->find($id);

        if (!$karyawan) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $karyawan
        ]);
    }

    public function update(UpdateKaryawanRequest $request, $id): JsonResponse
    {
        try {
            $karyawan = $this->karyawanService->update($id, $request->validated());

            return response()->json([
                'status'  => 'success',
                'message' => 'Data karyawan berhasil diperbarui',
                'data'    => $karyawan
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->karyawanService->delete($id);

            return response()->json([
                'status'  => 'success',
                'message' => 'Karyawan dan Akun User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}