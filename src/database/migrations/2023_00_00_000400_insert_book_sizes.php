<?php

use App\Models\BookSize;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        BookSize::insert([
            ['sort' => 1, 'name' => 'B6'],
            ['sort' => 2, 'name' => 'A5'],
            ['sort' => 3, 'name' => 'Magazine'],
            ['sort' => 4, 'name' => 'bunko'],
            ['sort' => 5, 'name' => 'Other'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        BookSize::truncate();
    }
};
