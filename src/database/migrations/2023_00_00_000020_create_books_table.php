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
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table->boolean('is_draft')
                ->default(true);

            $table->dateTimeTz('published_at')
                ->default(DB::raw('CURRENT_TIMESTAMP'))
                ->comment('Publication date and time (valid only if is_draft = false)');

            $table->string('title')->index();

            $table->string('title_kana')
                ->nullable();

            $table->string('volume')
                ->nullable();

            $table->string('caption')
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->text('keywords')
                ->nullable()
                ->comment('String for keyword search matching');

            $table->string('isbn13', 13)
                ->nullable()
                ->comment('13digits without hyphen');

            $table->unsignedInteger('price')
                ->nullable()
                ->comment('price (excluding tax)');

            $table->dateTimeTz('release_date')
                ->nullable();

            $table->boolean('ebook_only')
                ->default(false);

            $table->boolean('special_edition')
                ->default(false);

            $table->boolean('limited_edition')
                ->default(false);

            $table->boolean('adult')
                ->default(true);

            $table->text('trial_url')
                ->nullable();

            $table->text('survey_url')
                ->nullable();

            $table->foreignId('format_id')
                ->comment('distribution format')
                ->nullable()
                ->constrained('book_formats');

            $table->foreignId('size_id')
                ->comment('size')
                ->nullable()
                ->constrained('book_sizes');

            $table->foreignId('label_id')
                ->comment('Lable')
                ->nullable()
                ->constrained();

            $table->foreignId('genre_id')
                ->comment('Genre')
                ->nullable()
                ->constrained();

            $table->foreignId('series_id')
                ->comment('Series')
                ->nullable()
                ->constrained();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->comment('last updated by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
};
