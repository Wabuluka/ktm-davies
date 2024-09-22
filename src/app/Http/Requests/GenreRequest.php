<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $genre = $this->genre;

        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('genres')->ignore($genre),
            ],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'ジャンル名',
        ];
    }
}
