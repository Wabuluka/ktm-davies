<?php

use App\Models\LabelType;
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
        LabelType::insert([
            ['sort' => 1, 'name' => 'Manga', 'is_default' => true],
            ['sort' => 2, 'name' => 'Magazine', 'is_default' => false],
            ['sort' => 3, 'name' => 'Goods', 'is_default' => false],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        LabelType::truncate();
    }
};
