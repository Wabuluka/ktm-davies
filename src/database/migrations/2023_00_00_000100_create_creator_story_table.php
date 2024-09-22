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
        Schema::create('creator_story', function (Blueprint $table) {
            $table->foreignId('story_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('creator_id')
                ->constrained();
            $table->unsignedInteger('sort')->index();

            $table->unique(['story_id', 'creator_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('creator_story');
    }
};
