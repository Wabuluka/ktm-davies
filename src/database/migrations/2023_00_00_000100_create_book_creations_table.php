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
        Schema::create('book_creations', function (Blueprint $table) {
            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('creator_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('creation_type')
                ->comment('作家区分名');
            $table->foreign('creation_type')
                ->references('name')
                ->on('creation_types')
                ->constrained()
                ->cascadeOnUpdate();
            $table->unsignedInteger('sort')->index();
            $table->string('displayed_type')
                ->nullable()
                ->comment('作家区分名を上書き');

            $table->unique(['book_id', 'creator_id', 'creation_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_creations');
    }
};
