<?php

namespace App\Http\Requests;

use App\Models\Benefit;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BenefitRequest extends FormRequest
{
    use HandleRequestParameters;

    protected function prepareForValidation()
    {
        if ($thumbnail = $this->file('thumbnail.file')) {
            $this->merge([
                'thumbnail' => [
                    ...$this->thumbnail,
                    'dimensions' => $this->getImageDimensions($thumbnail),
                ],
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'max:255'],
            'paid' => ['required', 'boolean'],
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'thumbnail.operation' => [
                'required',
                Rule::in(['stay', 'set', 'delete']),
            ],
            'thumbnail.file' => [
                Rule::requiredIf(fn () => $this->get('thumbnail.operation') === 'set'),
                'file',
                'image',
                'max:2048',
            ],
            'thumbnail.dimensions' => [
                Rule::requiredIf(fn () => $this->get('thumbnail.operation') === 'set'),
                'array',
            ],
            'thumbnail.dimensions.width' => [
                'nullable',
                'int',
            ],
            'thumbnail.dimensions.height' => [
                'nullable',
            ],
        ];
    }

    public function toModel(Benefit $base = null): Benefit
    {
        $story = $base ?? new Benefit();
        $story->fill($this->safe()->except('thumbnail'));

        return $story;
    }

    public function attributes()
    {
        return [
            'name' => '特典名',
            'paid' => '有償/無償',
            'store_id' => '店舗',
            'thumbnail' => 'サムネイル',
        ];
    }
}
