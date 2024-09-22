<?php

namespace App\Http\Requests;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read \App\Models\Site $site
 * @property-read ?\App\Models\Page $page
 */
class PageRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $uniqueSlug = Rule::unique('pages')->ignore($this?->page)->where('site_id', $this->site->id);

        return [
            'title' => [
                'required', 'string', 'max:255',
            ],
            'slug' => [
                'required', 'string', 'max:255', $uniqueSlug,
            ],
            'content' => [
                'required', 'string', 'max:10000',
            ],
        ];
    }

    public function makePage(Page $page = null): Page
    {
        $page ??= new Page();

        return $page->fill($this->validated());
    }
}
