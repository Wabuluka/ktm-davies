<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book_stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')
                ->constrained();
            $table->boolean('is_purchase_url_required')
                ->default(false)
                ->comment('It indicates whether purchase option URL is required');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_stores');
    }
};
