<?php

use App\Models\BookFormat;
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
        BookFormat::insert([
            ['sort' => 1, 'name' => 'Manga'],
            ['sort' => 2, 'name' => 'Single Episode'],
            ['sort' => 3, 'name' => 'Anthology'],
            ['sort' => 4, 'name' => 'Tapestry'],
            ['sort' => 5, 'name' => 'Body Pillow'],
            ['sort' => 6, 'name' => 'Mouse Pad'],
            ['sort' => 7, 'name' => 'Figure'],
            ['sort' => 8, 'name' => 'Towel'],
            ['sort' => 9, 'name' => 'Audio Drama'],
            ['sort' => 10, 'name' => 'Other'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        BookFormat::truncate();
    }
};
