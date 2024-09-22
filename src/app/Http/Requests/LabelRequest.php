<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read ?\App\Models\Label $label
 * @property-read array<string> $typeIds
 * @property-read array<string> $hasTypeIds
 */
class LabelRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required', 'max:255', Rule::unique('labels')->ignore($this->label),
            ],
            'url' => [
                'nullable', 'url', 'max:2048',
            ],
            'genre_id' => [
                'required', 'exists:genres,id',
            ],
            'type_ids' => [
                'nullable', 'array',
            ],
            'type_ids.*' => [
                'exists:label_types,id',
            ],
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'hasTypeIds' => $this->has('type_ids'),
            'typeIds' => $this->type_ids ? array_values($this->type_ids) : [],
        ]);
    }

    public function attributes()
    {
        return [
            'name' => 'レーベル名',
            'url' => 'URL',
            'genre_id' => 'ジャンル',
            'type_ids' => '種別',
        ];
    }
}
