<?php

namespace App\Services;

use App\Models\User;
use App\Models\Karyawans;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthService
{
    public function registerUser(array $data)
    {
        return DB::transaction(function () use ($data){
            $user = User::create([
                'nama' => $data['nama'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
            ]);

            Karyawans::create([
                'user_id' => $user->id,
                'divisi_id' => $data['divisi_id'],
                'nama' => $data['nama'],
                'no_hp' => $data['no_hp'],
                'jenis_karyawan' => $data['jenis_karyawan'],
            ]);

            return $user;
        });
    }

    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // if ($user->role !== 'HRD') {
        //     throw ValidationException::withMessages([
        //         'email' => ['Akses ditolak. Hanya akun HRD yang bisa login di sini.'],
        //     ]);
        // }
        
        $token = $user->createToken('auth_token')->plainTextToken;
        $karyawan = Karyawans::where('user_id', $user->id)->first();

        return [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
            'karyawan'     => $karyawan
        ];
    }
}