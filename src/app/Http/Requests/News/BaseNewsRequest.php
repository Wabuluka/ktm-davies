<?php

namespace App\Http\Requests\News;

use App\Enums\NewsStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class BaseNewsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => [
                'required', 'string', 'max:255',
            ],
            'content' => [
                'required', 'string',
            ],
            'eyecatch' => [
                'nullable', 'file', 'image', 'max:2048',
            ],
            'status' => new Enum(NewsStatus::class),
            'published_at' => Rule::when(
                NewsStatus::from($this->status) === NewsStatus::WillBePublished,
                ['required', 'date', 'after:now'],
                ['missing'],
            ),
        ];
    }
}
