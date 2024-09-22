<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->text('url')->unique();
            $table->string('book_preview_path')
                ->nullable()
                ->comment('Path for the book preview URL');
            $table->string('news_preview_path')
                ->nullable()
                ->comment('Path for the news preview URL');
            $table->string('page_preview_path')
                ->nullable()
                ->comment('Path for the site page preview URL');
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
        Schema::dropIfExists('sites');
    }
};
