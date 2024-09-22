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
        Schema::create('banner_placements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('site_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedInteger('max_banner_count')
                ->nullable()
                ->comment('Max number of registerable banner count(no limit if null)');
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
        Schema::dropIfExists('banner_placements');
    }
};
