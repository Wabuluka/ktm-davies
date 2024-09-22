<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_draft')
                ->default(true)
                ->comment('It indicates this data is in draft');
            $table->dateTimeTz('published_at')
                ->default(DB::raw('CURRENT_TIMESTAMP'))
                ->comment('Publication time and date (valid only if is_draft = false)');
            $table->string('title')->index();
            $table->string('slug')->index();
            $table->text('content')
                ->comment('content');
            $table->foreignId('category_id')
                ->constrained('news_categories');
            $table->timestamps();

            $table->unique(['category_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
};
