<?php

namespace App\Http\Requests\Absen;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAbsenRequest extends FormRequest
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
            'user_id' => [
                function ($attribute, $value, $fail) {
                    $attendance = Attendances::where('user_id', Auth::id())
                        ->where('date', now()->toDateString())
                        ->first();

                    if (!$attendance) {
                        $fail('Anda belum absen masuk hari ini.');
                    }

                    if ($attendance && $attendance->time_out !== null) {
                        $fail('Anda sudah absen pulang hari ini.');
                    }
                },
            ],
        ];
    }
}
