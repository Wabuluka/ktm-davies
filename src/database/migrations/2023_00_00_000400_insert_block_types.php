<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('block_types')->insert([
            ['name' => 'Item Itself'],
            ['name' => 'Purchase Option(Physical Books)'],
            ['name' => 'Purchase Option (eBooks)'],
            ['name' => 'Store Benefits'],
            ['name' => 'Series List'],
            ['name' => 'Related Work'],
            ['name' => 'Story'],
            ['name' => 'Character List'],
            ['name' => 'Custom Block'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('block_types')->truncate();
    }
};
