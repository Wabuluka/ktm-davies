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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('type_id')
                ->comment('type of custom block (for purchase option, story, etc.)')
                ->constrained('block_types');
            $table->string('custom_title')
                ->comment('Title of custom block')
                ->nullable();
            $table->text('custom_content')
                ->comment('content of custom block')
                ->nullable();
            $table->unsignedInteger('sort')->index();
            $table->boolean('displayed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blocks');
    }
};
