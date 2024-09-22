<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookStoreController;
use App\Http\Controllers\BookTypeController;
use App\Http\Controllers\EbookStoreController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\GoodsStoreController;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::name('books.')->prefix('books')->group(function () {
        Route::delete('/destroy-many', [BookController::class, 'destroyMany'])->name('destroy-many');
        Route::patch('/publish/{book}', [BookController::class, 'publish'])->name('publish');
        Route::patch('/publish-many', [BookController::class, 'publishMany'])->name('publish-many');
        Route::patch('bulk-update', [BookController::class, 'bulkUpdate'])->name('bulk-update');
        Route::name('csv.')->prefix('csv')->group(function () {
            Route::get('/download', [BookController::class, 'csvDownload'])->name('download');
            Route::post('/import', [BookController::class, 'csvImport'])->name('import');
            Route::post('/export', [BookController::class, 'csvExport'])->name('export');
        });
    });
    Route::resource('books', BookController::class)->except('show');
    Route::resource('book-types', BookTypeController::class)->except('show');

    Route::resource('genres', GenreController::class)->except('show');
    Route::patch('genres/{genre}/sort/move-up', [GenreController::class, 'moveUp'])
        ->name('genres.sort.move-up');
    Route::patch('genres/{genre}/sort/move-down', [GenreController::class, 'moveDown'])
        ->name('genres.sort.move-down');

    Route::resource('book-stores', BookStoreController::class)->only('index');
    Route::resource('ebook-stores', EbookStoreController::class)->only('index');
    Route::resource('goods-stores', GoodsStoreController::class)->only('index');

    Route::resource('sites.pages', PageController::class)->only(['edit', 'update']);
    Route::resource('sites.news', NewsController::class)->except('show')->shallow();
    Route::resource('sites.news-categories', NewsCategoryController::class)->except('show')->shallow();
    Route::resource('banner-placements.banners', BannerController::class)->except('show')->shallow();
    Route::patch('banners/{banner}/sort/move-up', [BannerController::class, 'moveUp'])
        ->name('banners.sort.move-up');
    Route::patch('banners/{banner}/sort/move-down', [BannerController::class, 'moveDown'])
        ->name('banners.sort.move-down');

    Route::resource('/users', RegisteredUserController::class)->except('show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
