<?php

namespace App\Http\Requests;

use App\Models\Store;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read \App\Models\Store|null $external_link
 */
class StoreRequest extends FormRequest
{
    use HandleRequestParameters;

    /**
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('stores')->ignore($this->store),
            ],
            'url' => [
                'required',
                'url',
                'max:2048',
                Rule::unique('stores')->ignore($this->store),
            ],
            'types.*' => [
                'required',
                'exists:store_types,id',
            ],
        ];
    }

    public function makeTypesSyncData(): ?array
    {
        if (! $this->safe()->has('types')) {
            return null;
        }

        return $this->safe()['types'] ?? [];
    }

    public function toModel(Store $base = null): Store
    {
        $store = $base ?? new Store();
        $store->fill($this->safe()->except('types'));

        return $store;
    }
}
