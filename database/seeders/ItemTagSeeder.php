<?php

namespace Database\Seeders;

use App\Models\ItemTag;
use Illuminate\Database\Seeder;

class ItemTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ItemTag::truncate();

        ItemTag::factory()->times(100)->create();
    }
}