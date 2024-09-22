<?php

namespace App\Http\Requests\News;

use App\Enums\NewsStatus;
use App\Models\News;
use App\Models\NewsCategory;
use App\Rules\Slug;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Date;
use Illuminate\Validation\Rule;

/**
 * @property-read  \App\Models\Site $site
 * @property-read ?\App\Models\News $news
 */
class PreviewNewsRequest extends BaseNewsRequest
{
    public function rules(): array
    {
        $categoryIdExists = Rule::exists(NewsCategory::class, 'id')
            ->where('site_id', $this->site->id);

        $uniqueSlug = Rule::unique(News::class, 'slug')
            ->ignore($this?->news)
            ->where(fn (Builder $query) => $query
                ->whereExists(fn (Builder $query) => $query
                    ->select()->from('news_categories')
                    ->whereColumn('news_categories.id', 'news.category_id')
                    ->where('news_categories.site_id', $this->site->id)));

        return parent::rules() + [
            'slug' => [
                'required', 'string', 'max:255', $uniqueSlug, new Slug,
            ],
            'category_id' => [
                'required', $categoryIdExists,
            ],
        ];
    }

    public function makeNews(News $news): News
    {
        return $news
            ? $news->fill($this->safeAttributes())
            : new News($this->safeAttributes());
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
                    'published_at' => $this->news?->published_at && $this->news->published_at <= now()
                        ? $this->news->published_at
                        : now(),
                ],
                NewsStatus::WillBePublished => [
                    'is_draft' => false,
                    'published_at' => Date::parse($this->safe()->published_at),
                ],
                NewsStatus::Published => [
                    'is_draft' => false,
                    'published_at' => $this->news?->published_at && $this->news->published_at <= now()
                        ? $this->news->published_at
                        : now(),
                ],
            };

        return $attributes;
    }
}
