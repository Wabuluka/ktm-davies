<?php

namespace App\Providers;

use App\Enums\BlockType;
use App\Enums\LabelType;
use App\GraphQL\Enums\AdultScopeType;
use GraphQL\Type\Definition\PhpEnumType;
use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Schema\TypeRegistry;

class GraphQLServiceProvider extends ServiceProvider
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
        $typeRegistry = app(TypeRegistry::class);
        $typeRegistry->register(new PhpEnumType(BlockType::class));
        $typeRegistry->register(new PhpEnumType(LabelType::class));
        $typeRegistry->register(new PhpEnumType(AdultScopeType::class));
    }
}
