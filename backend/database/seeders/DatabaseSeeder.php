<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karyawans;
use App\Models\Divisions; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //Input Table Divisi
        $divIT = Divisions::create(['nama' => 'IT Department']);
        $divHR = Divisions::create(['nama' => 'HR Department']);
        $divFinance = Divisions::create(['nama' => 'Finance']);

        //Input Table User
        $hrdUser = User::create([
            'nama' => 'Super HRD',
            'email' => 'hrd@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'HRD'
        ]);

        Karyawans::create([
            'user_id' => $hrdUser -> id,
            'divisi_id' => $divHR -> id,
            'nama' => 'Super HRD',
            'no_hp' => '081234567890',
            'jenis_karyawan' => 'tetap'
        ]);

        $karyawanUser = User::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'Karyawan',
        ]);

        Karyawans::create([
            'user_id' => $karyawanUser->id,  
            'divisi_id' => $divIT->id,       
            'nama' => 'Budi Santoso',
            'no_hp' => '089876543210',
            'jenis_karyawan' => 'kontrak',
        ]);

        $magangUser = User::create([
            'nama' => 'Siti Magang',
            'email' => 'siti@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'Karyawan',
        ]);

        Karyawans::create([
            'user_id' => $magangUser->id,
            'divisi_id' => $divFinance->id,
            'nama' => 'Siti Magang',
            'no_hp' => '08555555555',
            'jenis_karyawan' => 'magang',
        ]);
    }
}
