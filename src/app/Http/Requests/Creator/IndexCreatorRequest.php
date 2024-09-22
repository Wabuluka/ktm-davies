<?php

namespace App\Http\Requests\Creator;

use App\Traits\Http\HandleRequestParameters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class IndexCreatorRequest extends FormRequest
{
    use HandleRequestParameters;

    public function rules(): array
    {
        return [
            'keyword' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function toParameteres(): Collection
    {
        return collect([
            'keywords' => $this->keyword
                ? $this->parseSpaceSeparatedKeywords($this->keyword)
                : null,
        ]);
    }
}
