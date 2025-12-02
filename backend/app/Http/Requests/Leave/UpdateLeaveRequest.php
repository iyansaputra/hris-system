<?php

namespace App\Http\Requests\Leave;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check() && Auth::user()->role === 'HRD';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status'         => 'required|in:approved,rejected',
            'rejection_note' => 'required_if:status,rejected|string|nullable',
        ];
    }

    public function messages()
    {
        return [
            'rejection_note.required_if' => 'Alasan penolakan wajib diisi jika status ditolak.',
        ];
    }
}
