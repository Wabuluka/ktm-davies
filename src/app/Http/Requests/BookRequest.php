<?php

namespace App\Http\Requests;

use App\Enums\BlockType;
use App\Enums\BookStatus;
use App\Models\Block;
use App\Models\Book;
use App\Models\BookStore;
use App\Models\EbookStore;
use App\Models\RelatedItem;
use App\Rules\Book\Isbn13;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * @property-read Book|null $book
 * @property-read \Illuminate\Http\UploadedFile|null $cover
 */
class BookRequest extends FormRequest
{
    use HandleRequestParameters;

    protected function prepareForValidation()
    {
        $relatedItems = $this->collect('related_items');
        [$toInsertRelatedItems, $toUpdateRelatedItems] = collect($relatedItems->get('upsert'))
            ->partition(fn ($item) => str_starts_with($item['id'], '+'));

        $blocks = $this->collect('blocks');
        [$toInsertBlocks, $toUpdateBlocks] = collect($blocks->get('upsert'))
            ->partition(fn ($item) => str_starts_with($item['id'], '+'));

        $this->merge([
            'related_items' => [
                'insert' => $toInsertRelatedItems
                    ->map(fn ($item) => collect($item)->except('id')->toArray())
                    ->values()
                    ->toArray(),
                'update' => $toUpdateRelatedItems
                    ->values()
                    ->toArray(),
                'deleteIds' => $relatedItems->get('deleteIds'),
            ],
            'blocks' => [
                'insert' => $toInsertBlocks
                    ->map(fn ($item) => collect($item)->except('id')->toArray())
                    ->values()
                    ->toArray(),
                'update' => $toUpdateBlocks
                    ->values()
                    ->toArray(),
                'deleteIds' => $blocks->get('deleteIds'),
            ],
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // Book Title Setting
            'title' => ['required', 'string', 'max:255'],
            'title_kana' => ['nullable', 'string', 'max:255'],
            'volume' => ['nullable', 'string', 'max:255'],

            // Publication Setting
            'status' => new Enum(BookStatus::class),
            'published_at' => Rule::when(
                BookStatus::from($this->status) === BookStatus::WillBePublished,
                ['required', 'date', 'after:now'],
                ['missing'],
            ),
            'sites' => ['nullable', 'array'],
            'sites.*' => ['required', 'integer'],

            // Work Information
            'cover' => ['nullable', 'file', 'image', 'max:2048'],

            'label_id' => ['nullable', 'integer'],
            'genre_id' => ['nullable', 'integer'],
            'series_id' => ['nullable', 'integer'],

            'creations' => ['nullable', 'array'],
            'creations.*.creator_id' => ['required', 'numeric'],
            'creations.*.creation_type' => ['required', 'string'],
            'creations.*.sort' => ['required', 'numeric'],
            'creations.*.displayed_type' => ['nullable', 'string'],

            'isbn13' => [new Isbn13()],
            'release_date' => ['nullable', 'date'],
            'price' => ['nullable', 'numeric', 'between:0,2147483647'],
            'format_id' => ['nullable', 'numeric'],
            'size_id' => ['nullable', 'numeric'],
            'ebook_only' => ['nullable', 'boolean'],
            'special_edition' => ['nullable', 'boolean'],
            'limited_edition' => ['nullable', 'boolean'],
            'adult' => ['nullable', 'boolean'],

            // Detailed Information
            'caption' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'trial_url' => ['nullable', 'url'],
            'survey_url' => ['nullable', 'url'],

            // Displey Setting

            ///////// Stores /////////
            'bookstores' => ['nullable', 'array'],
            'bookstores.*.id' => Rule::forEach(function (?string $_value, string $attribute, array $data) {
                $prefix = preg_replace('/.id$/', '', $attribute);
                $purchaseUrl = Arr::get($data, "{$prefix}.url");

                return $purchaseUrl
                    ? ['required', Rule::exists(BookStore::class)]
                    : ['required', Rule::exists(BookStore::class)->where('is_purchase_url_required', false)];
            }),
            'bookstores.*.url' => ['nullable', 'url', 'max:2048'],
            'bookstores.*.is_primary' => ['required', 'boolean'],

            ///////// Stores (online) /////////
            'ebookstores' => ['nullable', 'array'],
            'ebookstores.*.id' => Rule::forEach(function (?string $_value, string $attribute, array $data) {
                $prefix = preg_replace('/.id$/', '', $attribute);
                $purchaseUrl = Arr::get($data, "{$prefix}.url");

                return $purchaseUrl
                    ? ['required', Rule::exists(EbookStore::class)]
                    : ['required', Rule::exists(EbookStore::class)->where('is_purchase_url_required', false)];
            }),
            'ebookstores.*.url' => ['nullable', 'url', 'max:2048'],
            'ebookstores.*.is_primary' => ['required', 'boolean'],

            ///////// Store Benefits /////////
            'benefits' => ['nullable', 'array'],
            'benefits.*.id' => ['required', 'numeric'],

            ///////// Stories /////////
            'stories' => ['nullable', 'array'],
            'stories.*.id' => ['required', 'numeric'],

            ///////// Related Items /////////
            'related_items' => ['required', 'array'],
            'related_items.insert.*.relatable_id' => ['required', 'numeric'],
            'related_items.insert.*.relatable_type' => ['required', Rule::in(['book', 'externalLink'])],
            'related_items.insert.*.description' => ['required', 'string', 'max:2048'],
            'related_items.insert.*.sort' => ['required', 'numeric'],
            'related_items.update.*.id' => [
                'required',
                Rule::exists(RelatedItem::class, 'id')
                    ->when($this->book, fn ($query, $book) => $query->whereIn('id', $book->relatedItems->pluck('id')->toArray())),
            ],
            'related_items.update.*.relatable_id' => ['required', 'numeric'],
            'related_items.update.*.relatable_type' => ['required', Rule::in(['book', 'externalLink'])],
            'related_items.update.*.description' => ['required', 'string', 'max:2048'],
            'related_items.update.*.sort' => ['required', 'numeric'],
            'related_items.deleteIds.*' => [
                'required',
                Rule::exists(RelatedItem::class, 'id')
                    ->when($this->book, fn ($query, $book) => $query->whereIn('id', $book->relatedItems->pluck('id')->toArray())),
            ],

            ///////// Character Profiles/////////
            'characters' => ['nullable', 'array'],
            'characters.*.id' => ['required', 'numeric'],

            ///////// Blocks /////////
            'blocks' => ['required', 'array'],
            'blocks.insert.*.type_id' => ['required', 'numeric'],
            'blocks.insert.*.custom_title' => ['nullable', 'string', 'max:255'],
            'blocks.insert.*.custom_content' => ['nullable', 'string', 'max:2048'],
            'blocks.insert.*.sort' => ['required', 'numeric'],
            'blocks.insert.*.displayed' => ['required', 'boolean'],
            'blocks.update.*.id' => [
                'required',
                Rule::exists(Block::class, 'id')
                    ->when($this->book, fn ($query, $book) => $query
                        ->whereIn('id', $book->blocks->pluck('id')->toArray())),
            ],
            'blocks.update.*.type_id' => ['required', 'numeric'],
            'blocks.update.*.custom_title' => ['nullable', 'string', 'max:255'],
            'blocks.update.*.custom_content' => ['nullable', 'string', 'max:2048'],
            'blocks.update.*.sort' => ['required', 'numeric'],
            'blocks.update.*.displayed' => ['required', 'boolean'],
            'blocks.deleteIds.*' => [
                'required',
                Rule::exists(Block::class, 'id')
                    ->when($this->book, fn ($query, $book) => $query
                        ->whereIn('id', $book->blocks->pluck('id')->toArray())
                        ->where('type_id', BlockType::Custom->value)),
            ],
        ];
    }

    public function toModel(Book $base = null): Book
    {
        return $base ? $this->fillBook($base) : $this->makeBook();
    }

    public function makeCoverData(): ?array
    {
        if (! $this->safe()->has('cover')) {
            return null;
        }

        if ($cover = $this->safe()['cover']) {
            return [
                'cover' => $cover,
                'dimensions' => $this->getImageDimensions($cover),
            ];
        }

        return ['cover' => null];
    }

    public function makeSitesSyncData(): ?array
    {
        if (! $this->safe()->has('sites')) {
            return null;
        }

        return $this->safe()['sites'] ?? [];
    }

    public function makeCreatorsSyncData(): ?array
    {
        if (! $this->safe()->has('creations')) {
            return null;
        }

        $creations = $this->safe()['creations'] ?? [];

        return array_reduce(
            $creations,
            fn ($syncData, $creation) => $syncData + [
                $creation['creator_id'] => [
                    'creation_type' => $creation['creation_type'],
                    'displayed_type' => $creation['displayed_type'] ?? null,
                    'sort' => $creation['sort'],
                ],
            ],
            []
        );
    }

    public function makeBookStoresSyncData(): ?array
    {
        if (! $this->safe()->has('bookstores')) {
            return null;
        }

        $bookStores = $this->safe()['bookstores'] ?? [];

        return array_reduce(
            $bookStores,
            fn ($syncData, $bookStores) => $syncData + [
                $bookStores['id'] => [
                    'url' => $bookStores['url'] ?? null,
                    'is_primary' => $bookStores['is_primary'],
                ],
            ],
            []
        );
    }

    public function makeEbookStoresSyncData(): ?array
    {
        if (! $this->safe()->has('ebookstores')) {
            return null;
        }

        $ebookStores = $this->safe()['ebookstores'] ?? [];

        return array_reduce(
            $ebookStores,
            fn ($syncData, $ebookStores) => $syncData + [
                $ebookStores['id'] => [
                    'url' => $ebookStores['url'] ?? null,
                    'is_primary' => $ebookStores['is_primary'],
                ],
            ],
            []
        );
    }

    public function makeBenefitsSyncData(): ?array
    {
        if (! $this->safe()->has('benefits')) {
            return null;
        }

        $benefits = $this->safe()['benefits'] ?? [];

        return array_reduce($benefits, function (array $syncData, array $benefit) {
            $syncData[] = $benefit['id'];

            return $syncData;
        }, []);
    }

    public function makeStoriesSyncData(): ?array
    {
        if (! $this->safe()->has('stories')) {
            return null;
        }

        $stories = $this->safe()['stories'] ?? [];

        return array_reduce($stories, function (array $syncData, array $story) {
            $syncData[] = $story['id'];

            return $syncData;
        }, []);
    }

    public function makeNewRelatedItemsData(): ?array
    {
        return $this->safe()['related_items']['insert'] ?? null;
    }

    public function makeToUpdateRelatedItemsData(): ?Collection
    {
        $data = $this->safe()['related_items']['update'] ?? null;

        if (! $data) {
            return null;
        }

        return collect($data);
    }

    public function getToDeleteRelatedItems(): ?HasMany
    {
        $ids = $this->safe()['related_items']['deleteIds'] ?? null;

        if (! $ids) {
            return null;
        }

        return $this?->book->relatedItems()->whereIn('id', $ids);
    }

    public function makeCharactersSyncData(): ?array
    {
        if (! $this->safe()->has('characters')) {
            return null;
        }

        $characters = $this->safe()['characters'] ?? [];

        return array_reduce($characters, function (array $syncData, array $character) {
            $syncData[] = $character['id'];

            return $syncData;
        }, []);
    }

    public function makeNewBlocksData(): ?array
    {
        return $this->safe()['blocks']['insert'] ?? null;
    }

    public function makeToUpdateBlocksData(): ?Collection
    {
        $data = $this->safe()['blocks']['update'] ?? null;

        if (! $data) {
            return null;
        }

        return collect($data);
    }

    public function getToDeleteBlocks(): ?HasMany
    {
        $ids = $this->safe()['blocks']['deleteIds'] ?? null;

        if (! $ids) {
            return null;
        }

        return $this?->book->blocks()->whereIn('id', $ids);
    }

    private function toAttributes(): array
    {
        return $this->safe()->merge([
            'is_draft' => BookStatus::from($this->status) === BookStatus::Draft,
        ])->except([
            'cover',
            'status',
            'sites',
            'characters',
            'creations',
            'bookstores',
            'ebookstores',
            'benefits',
            'stories',
            'related_items',
            'blocks',
        ]);
    }

    private function makeBook(): Book
    {
        $book = new Book($this->toAttributes());
        $book->fill(['published_at' => $this->published_at ?? now()]);
        $book->created_by = $this->user()->id;
        $book->updated_by = $this->user()->id;

        return $book;
    }

    private function fillBook(Book $book): Book
    {
        $book->fill($this->toAttributes());
        if (BookStatus::from($this->status) === BookStatus::Published) {
            if ($book->published_at > now()) {
                $book->published_at = now();
            }
        }
        $book->updated_by = $this->user()->id;

        return $book;
    }
}
