<?php

namespace App\Http\Requests\ExternalLink;

use App\Models\ExternalLink;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read \App\Models\ExternalLink|null $external_link
 */
class ExternalLinkRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => [
                'required',
                'max:255',
                Rule::unique('external_links')->ignore($this->external_link),
            ],
            'url' => [
                'required',
                'url',
                'max:2048',
                Rule::unique('external_links')->ignore($this->external_link),
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
                'int',
            ],
        ];
    }

    public function toModel(ExternalLink $base = null): ExternalLink
    {
        $link = $base ?? new ExternalLink();
        $link->fill($this->safe()->except('thumbnail'));

        return $link;
    }
}
