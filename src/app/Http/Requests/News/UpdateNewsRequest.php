<?php

namespace App\Http\Requests\News;

use App\Enums\NewsStatus;
use App\Models\News;
use App\Models\NewsCategory;
use App\Rules\Slug;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

/**
 * @property-read \App\Models\News $news
 */
class UpdateNewsRequest extends BaseNewsRequest
{
    public function rules(): array
    {
        $siteId = $this->news->load('category.site')->category->site->id;

        $categoryIdExists = Rule::exists(NewsCategory::class, 'id')
            ->where('site_id', $siteId);

        $uniqueSlug = Rule::unique(News::class, 'slug')
            ->ignore($this->news)
            ->where(fn (Builder $query) => $query
                ->whereExists(fn (Builder $query) => $query
                    ->select()->from('news_categories')
                    ->whereColumn('news_categories.id', 'news.category_id')
                    ->where('news_categories.site_id', $siteId)));

        return parent::rules() + [
            'slug' => [
                'required', 'string', 'max:255', $uniqueSlug, new Slug,
            ],
            'category_id' => [
                'required', $categoryIdExists,
            ],
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'site' => $this->news->loadMissing('category.site')->category->site,
        ]);
    }

    public function fillNews(News $news): News
    {
        return $news->fill($this->safeAttributes());
    }

    private function safeAttributes(): array
    {
        $status = NewsStatus::from($this->safe()->status);
        $attributes =
            $this->safe()->except(['status', 'published_at', 'eyecatch'])
                +
            match ($status) {
                NewsStatus::Draft => [
                    'is_draft' => true,
                ],
                NewsStatus::WillBePublished => [
                    'is_draft' => false,
                    'published_at' => $this->safe()->published_at,
                ],
                NewsStatus::Published => [
                    'is_draft' => false,
                    'published_at' => $this->news->published_at <= now()
                        ? $this->news->published_at
                        : now(),
                ],
            };

        return $attributes;
    }
}
