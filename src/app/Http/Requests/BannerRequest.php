<?php

namespace App\Http\Requests;

use App\Models\Banner;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BannerRequest extends FormRequest
{
    use HandleRequestParameters;

    protected function prepareForValidation()
    {
        if ($image = $this->file('image.file')) {
            $this->merge([
                'image' => [
                    ...$this->image,
                    'dimensions' => $this->getImageDimensions($image),
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
            'url' => ['required', 'url', 'max:2048'],
            'new_tab' => ['required', 'boolean'],
            'displayed' => ['required', 'boolean'],
            'image.operation' => [
                'required',
                Rule::in(['stay', 'set']),
            ],
            'image.file' => [
                Rule::requiredIf(fn () => $this->get('image.operation') === 'set'),
                'file',
                'image',
                'max:2048',
            ],
            'image.dimensions' => [
                Rule::requiredIf(fn () => $this->get('image.operation') === 'set'),
                'array',
            ],
            'image.dimensions.width' => [
                'nullable',
                'int',
            ],
            'image.dimensions.height' => [
                'nullable',
            ],
        ];
    }

    public function toModel(Banner $base = null): Banner
    {
        $banner = $base ?? new Banner();
        $banner->fill($this->safe()->except('image'));

        $placementId = $this->route('banner_placement')?->id;
        if ($placementId) {
            $banner->placement_id = $placementId;
        }

        return $banner;
    }

    public function attributes()
    {
        return [
            'name' => '名前',
            'url' => 'URL',
            'new_tab' => '新規タブで開く',
            'displayed' => '表示',
            'image' => '画像',
        ];
    }
}
