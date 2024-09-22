<?php

namespace App\Http\Controllers\Api\Preview;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Models\Benefit;
use App\Models\Block;
use App\Models\Book;
use App\Models\BookPreview;
use App\Models\BookStore;
use App\Models\Character;
use App\Models\Creator;
use App\Models\EbookStore;
use App\Models\ExternalLink;
use App\Models\RelatedItem;
use App\Models\Site;
use App\Models\Story;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PreviewBookController extends Controller
{
    use HandleRequestParameters;

    public function __invoke(BookRequest $request, Book $book)
    {
        $book->id ??= 0;
        $book = $request->toModel($book)->load([
            // belongsTo
            'label', 'genre', 'series', 'format', 'size',
            // belongsToMany
            'sites', 'creators', 'bookstores', 'ebookstores', 'benefits', 'stories', 'characters',
            // hasMany
            'relatedItems', 'blocks',
            // morphMany
            'media',
        ]);
        $this->setCreators(
            $book, $request->makeCreatorsSyncData()
        );
        $this->setBookStores(
            $book, $request->makeBookStoresSyncData()
        );
        $this->setEbookStores(
            $book, $request->makeEbookStoresSyncData()
        );
        $this->setBenefits(
            $book, $request->makeBenefitsSyncData()
        );
        $this->setStories(
            $book, $request->makeStoriesSyncData()
        );
        $this->setCharacters(
            $book, $request->makeCharactersSyncData()
        );
        $this->setRelatedItems(
            $book,
            $request->makeNewRelatedItemsData(),
            $request->makeToUpdateRelatedItemsData(),
            $request->getToDeleteRelatedItems(),
        );
        $this->setBlocks(
            $book,
            $request->makeNewBlocksData(),
            $request->makeToUpdateBlocksData(),
            $request->getToDeleteBlocks(),
        );
        $coverPath = $this->setAndStorePreviewCover(
            $book, $request->makeCoverData()
        );

        BookPreview::create([
            'token' => $token = Str::uuid(),
            'model' => $book,
            'file_paths' => Arr::wrap($coverPath),
        ]);

        return response()->json([
            'previews' => $this->generatePreviews($token),
        ]);
    }

    private function setAndStorePreviewCover(Book $book, array $coverData = null): ?string
    {
        if (! $coverData) {
            $book->preview_cover = $book->cover;

            return null;
        }

        $cover = $coverData['cover'];

        if (! $cover) {
            $book->preview_cover = null;

            return null;
        }

        $path = $cover->store('tmp/preview/book/cover', 'public');
        $book->preview_cover = [
            'original_url' => Storage::disk('public')->url($path),
            'custom_properties' => $this->getImageDimensions($cover),
        ];

        return $path;
    }

    private function setCreators(Book $book, array $creatorData = null): void
    {
        if ($creatorData !== null) {
            $creatorIds = array_keys($creatorData);
            $creators = Creator::find($creatorIds)
                ->each(fn (Creator $creator) => $creator
                    ->setRelation('creation', $book->creators()->newPivot($creatorData[$creator->id]))
                )
                ->sortBy(fn (Creator $creator) => $creator->creation->sort)
                ->values();
            $book->setRelation('creators', $creators);
        }
    }

    private function setBookStores(Book $book, array $bookStoreData = null): void
    {
        if ($bookStoreData !== null) {
            $bookStoreIds = array_keys($bookStoreData);
            $bookStores = BookStore::with('media')->find($bookStoreIds)
                ->each(fn (BookStore $bookStore) => $bookStore
                    ->setRelation('pivot', $book->bookstores()->newPivot($bookStoreData[$bookStore->id]))
                )
                ->sortBy(fn (BookStore $bookStore) => $bookStore->pivot->sort)
                ->values();
            $book->setRelation('bookstores', $bookStores);
        }
    }

    private function setEbookStores(Book $book, array $ebookStoreData = null): void
    {
        if ($ebookStoreData !== null) {
            $ebookStoreIds = array_keys($ebookStoreData);
            $ebookStores = EbookStore::with('media')->find($ebookStoreIds)
                ->each(fn (EbookStore $ebookStore) => $ebookStore
                    ->setRelation('pivot', $book->ebookstores()->newPivot($ebookStoreData[$ebookStore->id]))
                )
                ->sortBy(fn (EbookStore $ebookStore) => $ebookStore->pivot->sort)
                ->values();
            $book->setRelation('ebookstores', $ebookStores);
        }
    }

    private function setBenefits(Book $book, array $benefitSyncData = null): void
    {
        if ($benefitSyncData !== null) {
            $benefits = Benefit::find($benefitSyncData);
            $book->setRelation('benefits', $benefits);
        }
    }

    private function setStories(Book $book, array $storySyncData = null): void
    {
        if ($storySyncData !== null) {
            $stories = Story::find($storySyncData);
            $book->setRelation('stories', $stories);
        }
    }

    private function setCharacters(Book $book, array $characterSyncData = null): void
    {
        if ($characterSyncData !== null) {
            $characters = Character::find($characterSyncData);
            $book->setRelation('characters', $characters);
        }
    }

    private function setRelatedItems(Book $book, array $new = null, Collection $update = null, HasMany $delete = null): void
    {
        $relatedItems = $book->relatedItems;
        if ($delete !== null) {
            $expected = $delete->pluck('id')->all();
            $relatedItems = $relatedItems->except($expected);
        }
        if ($new !== null) {
            foreach ($new as $attributes) {
                $newRelatedItem = new RelatedItem(Arr::except($attributes, 'id'));
                $newRelatedItem->id = 0;
                $relatedItems->push($newRelatedItem);
            }
        }
        if ($update !== null) {
            foreach ($update as $attributes) {
                $relatedItem = $relatedItems->find($attributes['id']);
                $relatedItem->fill(Arr::except($attributes, 'id'));
            }
        }
        $bookIds = $relatedItems->filter(fn ($item) => $item->relatable_type === 'book')->map->relatable_id;
        $linkIds = $relatedItems->filter(fn ($item) => $item->relatable_type === 'externalLink')->map->relatable_id;
        $books = Book::with('media', 'sites')->find($bookIds);
        $links = ExternalLink::with('media')->find($linkIds);
        $relatedItems->each(fn (RelatedItem $item) => $item->setRelation('relatable', match ($item->relatable_type) {
            'book' => $books->find($item->relatable_id),
            'externalLink' => $links->find($item->relatable_id),
        }));
        $book->setRelation(
            'relatedItems',
            $relatedItems->sortBy(fn (RelatedItem $relatedItem) => $relatedItem->sort)->values(),
        );
    }

    private function setBlocks(Book $book, array $new = null, Collection $update = null, HasMany $delete = null): void
    {
        $blocks = $book->blocks;
        if ($delete !== null) {
            $expected = $delete->pluck('id')->all();
            $blocks = $blocks->except($expected);
        }
        if ($new !== null) {
            foreach ($new as $attributes) {
                $newBlock = new Block(Arr::except($attributes, 'id'));
                $newBlock->id = 0;
                $blocks->push($newBlock);
            }
        }
        if ($update !== null) {
            foreach ($update as $attributes) {
                $block = $blocks->find($attributes['id']);
                $block->fill(Arr::except($attributes, 'id'));
            }
        }
        $book->setRelation(
            'blocks',
            $blocks->filter->displayed->sortBy(fn (Block $block) => $block->sort)->values(),
        );
    }

    private function generatePreviews(string $token): Collection
    {
        return Site::all()
            ->filter->book_preview_path
            ->map(function (Site $site) use ($token) {
                $baseUrl
                    = config("preview.{$site->code}.base_url") ?: $site->url;
                $path
                    = str($site->book_preview_path)->replace('[token]', $token);
                $previewUrl
                    = str($baseUrl)->finish('/')->append($path);

                return ['site' => $site, 'url' => $previewUrl];
            });
    }
}
