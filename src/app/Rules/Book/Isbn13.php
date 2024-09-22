<?php

namespace App\Rules\Book;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Isbn13 implements DataAwareRule, ValidationRule
{
    /**
     * @var array<string, mixed>
     */
    protected $data = [];

    public function __construct(private string $bookstoreKey = 'bookstores')
    {
    }

    /**
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $bookStores = Arr::wrap(
            Arr::get($this->data, $this->bookstoreKey)
        );
        $emptyUrlBookStoreExists = Collection::make(
            Arr::pluck($bookStores, 'url')
        )->reject()->isNotEmpty();

        if ($emptyUrlBookStoreExists && ! $value) {
            $fail('購入URLが未入力の購入先情報を登録する際は、必ずISBNを入力してください');
        }
        if ((bool) $value) {
            if (! is_numeric($value) || mb_strlen($value) !== 13) {
                $fail('ISBNを正しい形式で入力してください');
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
