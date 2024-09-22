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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('url')->comment('Destination');
            $table->boolean('new_tab')->comment('open in new tab');
            $table->unsignedInteger('sort')->index();
            $table->boolean('displayed')->comment('displey/hide');
            $table->foreignId('placement_id')
                ->comment('Display place of banner')
                ->index()
                ->constrained('banner_placements')
                ->cascadeOnDelete();
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
        Schema::dropIfExists('banners');
    }
};
