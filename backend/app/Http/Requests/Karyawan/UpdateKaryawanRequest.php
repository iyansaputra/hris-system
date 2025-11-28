<?php

namespace App\Http\Requests\Karyawan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Karyawans;

class UpdateKaryawanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $karyawanId = $this->route('karyawan'); 
        $karyawan = Karyawans::findOrFail($karyawanId);

        return [
            //Table User
            'nama'      => 'sometimes|string|max:255',
            'email'     => ['sometimes', 'email', Rule::unique('users')->ignore($karyawan->user_id)],
            'password'  => 'nullable|string|min:6',
            'role'      => 'sometimes|in:HRD,Karyawan',

            //Table Karyawan
            'divisi_id'      => 'sometimes|exists:divisions,id',
            'no_hp'          => 'sometimes|string|max:15',
            'jenis_karyawan' => 'sometimes|in:tetap,kontrak,magang,pkl',
        ];
    }
}