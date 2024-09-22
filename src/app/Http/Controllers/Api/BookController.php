<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\IndexBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\SearchBookService;

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

        return BookResource::collection($query->paginate(10));
    }

    public function show(Book $book)
    {
        return new BookResource($book);
    }
}
