<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('creators')->ignore($this->creator),
            ],
            'name_kana' => 'max:255',
        ];
    }

    public function attributes()
    {
        return [
            'name' => '作家名',
            'name_kana' => '作家名カナ',
        ];
    }
}
