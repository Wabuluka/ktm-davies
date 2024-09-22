<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();

        Relation::enforceMorphMap([
            'banner' => 'App\Models\Banner',
            'benefit' => 'App\Models\Benefit',
            'book' => 'App\Models\Book',
            'character' => 'App\Models\Character',
            'folder' => 'App\Models\Folder',
            'externalLink' => 'App\Models\ExternalLink',
            'news' => 'App\Models\News',
            'site' => 'App\Models\Site',
            'store' => 'App\Models\Store',
            'bookStore' => 'App\Models\BookStore',
            'ebookStore' => 'App\Models\EbookStore',
            'story' => 'App\Models\Story',
            'user' => 'App\Models\User',
        ]);
    }
}
