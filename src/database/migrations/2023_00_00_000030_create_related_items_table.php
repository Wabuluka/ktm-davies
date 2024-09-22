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
        Schema::create('related_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->morphs('relatable');
            $table->text('description')->comment('The reason why this is related');
            $table->unsignedInteger('sort')->index();
            $table->timestamps();

            $table->unique(['book_id', 'relatable_id', 'relatable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_items');
    }
};
