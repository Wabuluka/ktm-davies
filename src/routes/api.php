<?php

use App\Http\Controllers\Api\BenefitController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CharacterController;
use App\Http\Controllers\Api\CreationTypeController;
use App\Http\Controllers\Api\CreatorController;
use App\Http\Controllers\Api\ExternalLinkController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\LabelController;
use App\Http\Controllers\Api\Preview\PreviewBookController;
use App\Http\Controllers\Api\Preview\PreviewNewsController;
use App\Http\Controllers\Api\Preview\PreviewPageController;
use App\Http\Controllers\Api\SeriesController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\StoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::name('api')->apiResource('books', BookController::class)->only(['index', 'show']);
    Route::name('api.')->post('books/preview/{book?}', PreviewBookController::class)->name('books.preview');
    Route::name('api.')->post('sites/{site}/news/preview/{news?}', PreviewNewsController::class)->name('sites.news.preview');
    Route::name('api.')->post('sites/{site}/pages/preview/{page?}', PreviewPageController::class)->name('sites.pages.preview');
    Route::name('api')->apiResource('creators', CreatorController::class)->except(['edit']);
    Route::name('api')->apiResource('creation-types', CreationTypeController::class)->except(['edit']);
    Route::patch('creation-types/{creation_type}/sort/move-up', [CreationTypeController::class, 'moveUp'])->name('api.creation-types.move_up');
    Route::patch('creation-types/{creation_type}/sort/move-down', [CreationTypeController::class, 'moveDown'])->name('api.creation-types.move_down');

    Route::apiResource('folders', FolderController::class)->except(['destroy']);
    Route::delete('folders', [FolderController::class, 'destroyMany'])->name('folders.destroy-many');

    Route::apiResource('folders.files', FileController::class)->only(['store', 'update'])->shallow();
    Route::delete('files', [FileController::class, 'destroyMany'])->name('files.destroy-many');

    Route::apiResource('series', SeriesController::class);
    Route::patch('series/{series}/sort/move-up', [SeriesController::class, 'moveUp'])->name('series.move_up');
    Route::patch('series/{series}/sort/move-down', [SeriesController::class, 'moveDown'])->name('series.move_down');

    Route::apiResource('label', LabelController::class);
    Route::patch('label/{label}/sort/move-up', [LabelController::class, 'moveUp'])->name('label.move_up');
    Route::patch('label/{label}/sort/move-down', [LabelController::class, 'moveDown'])->name('label.move_down');
    // refcatory
    Route::patch('label/sort', [LabelController::class, 'sort'])->name('label.sort');

    Route::apiResource('genre', GenreController::class);
    Route::patch('genre/{genre}/sort/move-up', [GenreController::class, 'moveUp'])->name('genre.move_up');
    Route::patch('genre/{genre}/sort/move-down', [GenreController::class, 'moveDown'])->name('genre.move_down');

    Route::post('genre/sort', [GenreController::class, 'sort'])->name('genre.sort');



    Route::apiResource('stories', StoryController::class);
    Route::apiResource('characters', CharacterController::class);
    Route::apiResource('stores', StoreController::class)->only(['index', 'show'])->names([
        'index' => 'stores_api.index',
        'show' => 'stores_api.show',
    ]);
    Route::apiResource('benefits', BenefitController::class);
    Route::apiResource('external-links', ExternalLinkController::class);
});
