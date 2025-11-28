<?php

namespace App\Services;

use App\Models\User;
use App\Models\Karyawans;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class KaryawanService
{
    public function getAllKaryawan()
    {
        return Karyawans::with(['user', 'divisions'])->latest()->paginate(10);
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'nama'     => $data['nama'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => $data['role'],
            ]);

            return Karyawans::create([
                'user_id'        => $user->id,
                'divisi_id'      => $data['divisi_id'],
                'nama'           => $data['nama'],
                'no_hp'          => $data['no_hp'],
                'jenis_karyawan' => $data['jenis_karyawan'],
            ]);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $karyawan = Karyawans::findOrFail($id);
            $user = $karyawan->user; 

            if (isset($data['nama'])) { 
                $user->nama = $data['nama']; 
            }

            if (isset($data['email'])) {
                $user->email = $data['email'];
            }

            if (isset($data['role'])) {
                $user->role = $data['role'];
            }

            if (isset($data['password']) && !empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $user->save();

            if (isset($data['nama'])) {
                $karyawan->nama = $data['nama'];
            }

            if (isset($data['divisi_id'])) {
                $karyawan->divisi_id = $data['divisi_id'];
            }

            if (isset($data['no_hp'])) {
                $karyawan->no_hp = $data['no_hp'];
            }

            if (isset($data['jenis_karyawan'])) {
                $karyawan->jenis_karyawan = $data['jenis_karyawan'];
            }

            $karyawan->save();

            return $karyawan->fresh(['user', 'divisions']);
        });
    }

    public function delete($id)
    {
        $karyawan = Karyawans::findOrFail($id);
        
        return $karyawan->user->delete();
    }
}