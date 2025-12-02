<?php

namespace App\Http\Requests\Absen;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbsenRequest extends FormRequest
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
                function ($attribute, $value, $fail){
                    $exists = Attendances::where('user_id', Auth::id())
                        ->where('date', now()->toDateString())
                        ->exists();

                    if ($exists) {
                        $fail('Anda sudah melakukan absen masuk hari ini.');
                    }
                },
            ],
        ];
    }
}
