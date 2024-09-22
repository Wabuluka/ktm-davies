<?php

namespace App\Http\Requests;

use App\Models\Creator;
use App\Models\Story;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class StoryRequest extends FormRequest
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
        $story = $this->story;

        return [
            'title' => [
                'required', 'max:255', Rule::unique('stories')->ignore($story, 'title'),
            ],
            'trial_url' => [
                'nullable', 'url', 'max:2048',
            ],
            'thumbnail.operation' => [
                'required', Rule::in(['stay', 'set', 'delete']),
            ],
            'thumbnail.file' => [
                Rule::requiredIf(fn () => $this->get('thumbnail.operation') === 'set'),
                'file', 'image', 'max:2048',
            ],
            'thumbnail.dimensions' => [
                Rule::requiredIf(fn () => $this->get('thumbnail.operation') === 'set'),
                'array',
            ],
            'thumbnail.dimensions.width' => [
                'nullable', 'int',
            ],
            'thumbnail.dimensions.height' => [
                'nullable',
            ],
            'creators' => [
                'nullable', 'array',
            ],
            'creators.*.id' => [
                'required', Rule::exists(Creator::class),
            ],
            'creators.*.sort' => [
                'required', 'int', 'min:0',
            ],
        ];
    }

    public function toModel(Story $base = null): Story
    {
        $story = $base ?? new Story();
        $story->fill($this->safe()->except('thumbnail', 'creators'));

        return $story;
    }

    public function shouldSyncCreators(): bool
    {
        return $this->safe()->has('creators');
    }

    public function makeCreatorsSyncData(): ?array
    {
        return Arr::mapWithKeys($this->safe()->creators, fn ($creator) => [
            $creator['id'] => [
                'sort' => $creator['sort'],
            ],
        ]);
    }

    public function attributes()
    {
        return [
            'title' => '収録作品',
            'trial_url' => 'URL',
        ];
    }
}
