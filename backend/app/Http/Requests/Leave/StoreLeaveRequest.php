<?php

namespace App\Http\Requests\Leave;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
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
            'type'       => 'required|in:cuti,sakit,izin',
            'start_date' => 'required|date', 
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'end_date.after_or_equal'   => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ];
    }
}
