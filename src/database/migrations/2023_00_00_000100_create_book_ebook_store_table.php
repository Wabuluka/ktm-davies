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
        Schema::create('book_ebook_store', function (Blueprint $table) {
            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('ebook_store_id')
                ->constrained();
            $table->text('url')
                ->nullable()
                ->comment('URL of purchase page');
            $table->boolean('is_primary')
                ->default(false)
                ->comment('一つだけ購入先情報を表示可能な箇所で表示するためのフラグ');

            $table->unique(['book_id', 'ebook_store_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_ebook_store');
    }
};
