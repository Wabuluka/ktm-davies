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
        Schema::create('label_label_type', function (Blueprint $table) {
            $table->foreignId('label_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('label_type_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unique(['label_id', 'label_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('label_label_type');
    }
};
