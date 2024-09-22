<?php

namespace App\Http\Controllers;

use App\Http\Requests\Book\IndexBookRequest;
use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\SearchBookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BookController extends Controller
{
    public function index(
        IndexBookRequest $request,
        SearchBookService $searchBookService,
    ) {
        $params = $request->toParameters();
        $query = $searchBookService->searchAsManager(
            $params->get('keywords'),
            $params->get('sites'),
            $params->get('statuses'),
            Book::with('label', 'sites.media', 'media', 'updatedBy'),
        );
        $books = BookResource::collection($query->paginate(10));

        return Inertia::render('Books/Index', [
            'books' => $books,
        ]);
    }

    public function create(Request $request)
    {
        $bookId = $request->query('from');
        $book = is_string($bookId)
            ? new BookResource(
                Book::findOrFail($bookId)->load(
                    'label',
                    'genre',
                    'series',
                    'ebookstores',
                    'creators',
                    'characters',
                    'sites.media',
                    'benefits',
                    'stories.creators',
                    'relatedItems.relatable',
                    'blocks',
                    'updatedBy',
                    'bookstores',
                )
            )
            : null;

        return Inertia::render('Books/Create', ['book' => $book]);
    }

    public function edit(Book $book)
    {
        $book->load(
            'label',
            'genre',
            'series',
            'ebookstores',
            'creators',
            'characters',
            'sites.media',
            'media',
            'benefits',
            'stories.creators',
            'relatedItems.relatable',
            'blocks',
            'updatedBy',
            'bookstores',
        );

        return Inertia::render('Books/Edit', [
            'book' => new BookResource($book),
        ]);
    }

    public function store(BookRequest $request)
    {
        DB::transaction(function () use ($request) {
            $book = $request->toModel();
            $book->save();

            $coverData =
                $request->makeCoverData();
            $sitesSyncData =
                $request->makeSitesSyncData();
            $creatorsSyncData =
                $request->makeCreatorsSyncData();
            $bookStoresSyncData =
                $request->makeBookStoresSyncData();
            $ebookStoresSyncData =
                $request->makeEbookStoresSyncData();
            $benefitsSyncData =
                $request->makeBenefitsSyncData();
            $storiesSyncData =
                $request->makeStoriesSyncData();
            $charactersSyncData =
                $request->makeCharactersSyncData();
            $newRelatedItemsData =
                $request->makeNewRelatedItemsData();
            $newBlocksData =
                $request->makeNewBlocksData();

            if ($sitesSyncData !== null) {
                $book->sites()->sync($sitesSyncData);
            }
            if ($creatorsSyncData !== null) {
                $book->creators()->sync($creatorsSyncData);
            }
            if ($bookStoresSyncData !== null) {
                $book->bookstores()->sync($bookStoresSyncData);
            }
            if ($ebookStoresSyncData !== null) {
                $book->ebookstores()->sync($ebookStoresSyncData);
            }
            if ($benefitsSyncData !== null) {
                $book->benefits()->sync($benefitsSyncData);
            }
            if ($storiesSyncData !== null) {
                $book->stories()->sync($storiesSyncData);
            }
            if ($charactersSyncData !== null) {
                $book->characters()->sync($charactersSyncData);
            }
            if ($newRelatedItemsData) {
                $book->relatedItems()->createMany($newRelatedItemsData);
            }
            if ($newBlocksData) {
                $book->blocks()->createMany($newBlocksData);
            }
            if ($coverData['cover'] ?? null) {
                $book->setCover($coverData['cover'], $coverData['dimensions']);
            }
        });

        return to_route('books.index');
    }

    public function update(BookRequest $request, Book $book)
    {
        DB::transaction(function () use ($request, $book) {
            $book = $request->toModel($book);
            $book->save();

            $coverData =
                $request->makeCoverData();
            $sitesSyncData =
                $request->makeSitesSyncData();
            $creatorsSyncData =
                $request->makeCreatorsSyncData();
            $bookStoresSyncData =
                $request->makeBookStoresSyncData();
            $ebookStoresSyncData =
                $request->makeEbookStoresSyncData();
            $benefitsSyncData =
                $request->makeBenefitsSyncData();
            $storiesSyncData =
                $request->makeStoriesSyncData();
            $charactersSyncData =
                $request->makeCharactersSyncData();
            $toDeleteRelatedItems =
                $request->getToDeleteRelatedItems();
            $newRelatedItemsData =
                $request->makeNewRelatedItemsData();
            $toUpdateRelatedItemsData =
                $request->makeToUpdateRelatedItemsData();
            $toDeleteBlocks =
                $request->getToDeleteBlocks();
            $newBlocksData =
                $request->makeNewBlocksData();
            $toUpdateBlocksData =
                $request->makeToUpdateBlocksData();

            if ($sitesSyncData !== null) {
                $book->sites()->sync($sitesSyncData);
            }
            if ($creatorsSyncData !== null) {
                $book->creators()->sync($creatorsSyncData);
            }
            if ($bookStoresSyncData !== null) {
                $book->bookstores()->sync($bookStoresSyncData);
            }
            if ($ebookStoresSyncData !== null) {
                $book->ebookstores()->sync($ebookStoresSyncData);
            }
            if ($benefitsSyncData !== null) {
                $book->benefits()->sync($benefitsSyncData);
            }
            if ($storiesSyncData !== null) {
                $book->stories()->sync($storiesSyncData);
            }
            if ($charactersSyncData !== null) {
                $book->characters()->sync($charactersSyncData);
            }
            if ($toDeleteRelatedItems) {
                $toDeleteRelatedItems->delete();
            }
            if ($newRelatedItemsData) {
                $book->relatedItems()->createMany($newRelatedItemsData);
            }
            if ($toUpdateRelatedItemsData) {
                $toUpdateRelatedItemsData?->each(fn ($item) => $book->relatedItems()->where('id', $item['id'])->update($item));
            }
            if ($toDeleteBlocks) {
                $toDeleteBlocks->delete();
            }
            if ($newBlocksData) {
                $book->blocks()->createMany($newBlocksData);
            }
            if ($toUpdateBlocksData) {
                $toUpdateBlocksData?->each(fn ($item) => $book->blocks()->where('id', $item['id'])->update($item));
            }
            if ($coverData !== null) {
                $coverData['cover']
                    ? $book->setCover($coverData['cover'], $coverData['dimensions'])
                    : $book->deleteCover();
            }
        });

        return to_route('books.index');
    }

    public function publish(Book $book, Request $request)
    {
        $book->update(['is_draft' => false, 'published_at' => now()]);
        $searchParams = $request->headers->get('searchParams');

        return to_route('books.index', $searchParams);
    }

    public function publishMany(Request $request)
    {
        $ids = $request->input('ids');
        Book::whereIn('id', $ids)->update(['is_draft' => false, 'published_at' => now()]);
        $searchParams = $request->headers->get('searchParams');

        return to_route('books.index', $searchParams);
    }

    public function destroy(Book $book, Request $request)
    {
        $book->delete();
        $searchParams = $request->headers->get('searchParams');

        return to_route('books.index', $searchParams);
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->input('ids');
        Book::whereIn('id', $ids)->delete();
        $searchParams = $request->headers->get('searchParams');

        return to_route('books.index', $searchParams);
    }
}
