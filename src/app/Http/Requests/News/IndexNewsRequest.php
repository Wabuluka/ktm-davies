<?php

namespace App\Http\Requests\News;

use App\Enums\NewsStatus;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

class IndexNewsRequest extends FormRequest
{
    use HandleRequestParameters;

    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'statuses.*' => new Enum(NewsStatus::class),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $keys = array_keys($validator->errors()->messages());
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->fullUrlWithoutQuery($keys));
    }

    public function makeParameters(): Collection
    {
        $safe = $this->safe()->collect();

        return collect()
            ->when($safe->get('keyword'), fn ($params, $keyword) => $params
                ->merge([
                    'keywords' => $this->parseSpaceSeparatedKeywords($keyword),
                ])
            )
            ->when($safe->get('statuses'), fn ($params, $statuses) => $params
                ->merge([
                    'statuses' => array_map(fn ($status) => NewsStatus::from($status), $statuses),
                ])
            );
    }
}
