<?php

namespace App\Http\Requests\Book;

use App\Enums\BookStatus;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Enum;

class IndexBookRequest extends FormRequest
{
    use HandleRequestParameters;

    // 無限ループ回避のため、バリデーションエラー発生時は URL からクエリパラメータを削除する
    protected $redirectRoute = 'books.index';

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'sites.*' => ['required', 'integer'],
            'statuses.*' => new Enum(BookStatus::class),
        ];
    }

    public function toParameters(): Collection
    {
        $keyword = $this->safe()->collect()->get('keyword');
        $statuses = $this->safe()->collect()->get('statuses');

        return $this->safe()->collect()
            ->except('keyword')
            ->when($keyword, fn ($params, $keyword) => $params->merge([
                'keywords' => $this->parseSpaceSeparatedKeywords($keyword, 10),
            ]))
            ->when($statuses, fn ($params, $statuses) => $params->merge([
                'statuses' => array_map(fn ($status) => BookStatus::from($status), $statuses),
            ]))
            ->map(fn ($value) => collect($value));
    }
}
