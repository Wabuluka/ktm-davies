<?php

namespace App\Http\Requests\ExternalLink;

use App\Traits\Http\HandleRequestParameters;
use Illuminate\Foundation\Http\FormRequest;

class IndexExternalLinkRequest extends FormRequest
{
    use HandleRequestParameters;

    /**
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'keyword' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    protected function passedValidation()
    {
        if ($keyword = $this->keyword) {
            $this->merge([
                'keywords' => $this->parseSpaceSeparatedKeywords($keyword),
            ]);
        }
    }
}
