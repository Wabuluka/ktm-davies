<?php

namespace App\Http\Requests;

use App\Models\Character;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CharacterRequest extends FormRequest
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
        $character = $this->character;

        return [
            'name' => [
                'required',
                'max:255',
                Rule::when(
                    $this->series_id,
                    fn ($params) => Rule::unique('characters')->where('series_id', $params->series_id)->ignore($this->character),
                ),
            ],
            'description' => [
                'max:255',
            ],
            'series_id' => [
                'numeric',
                'nullable',
            ],
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

    public function toModel(Character $base = null): Character
    {
        $character = $base ?? new Character();
        $character->fill($this->safe()->except('thumbnail'));

        return $character;
    }

    public function attributes()
    {
        return [
            'name' => 'キャラクター名',
            'description' => 'キャラクター説明',
            'series_id' => '関連シリーズ',
        ];
    }
}
