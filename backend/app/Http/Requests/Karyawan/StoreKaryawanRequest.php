<?php

namespace App\Http\Requests\Karyawan;

use Illuminate\Foundation\Http\FormRequest;

class StoreKaryawanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //Validasi akun user
            'nama' => 'required|string|max:225',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:HRD,Karyawan',

            //Validasi profil
            'divisi_id' => 'required|exists:divisions,id',
            'no_hp' => 'required|string|max:15',
            'jenis_karyawan' => 'required|in:tetap,kontrak,magang,pkl',
        ];
    }
}
