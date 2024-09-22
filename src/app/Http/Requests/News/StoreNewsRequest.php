<?php

namespace App\Http\Requests\News;

use App\Enums\NewsStatus;
use App\Models\News;
use App\Models\NewsCategory;
use App\Rules\Slug;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

/**
 * @property-read \App\Models\Site $site
 */
class StoreNewsRequest extends BaseNewsRequest
{
    public function rules(): array
    {
        $categoryIdExists = Rule::exists(NewsCategory::class, 'id')
            ->where('site_id', $this->site->id);

        $uniqueSlug = Rule::unique(News::class, 'slug')
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

    public function makeNews(): News
    {
        return new News($this->safeAttributes());
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
                    'published_at' => now(),
                ],
                NewsStatus::WillBePublished => [
                    'is_draft' => false,
                    'published_at' => $this->safe()->published_at,
                ],
                NewsStatus::Published => [
                    'is_draft' => false,
                    'published_at' => now(),
                ],
            };

        return $attributes;
    }
}
