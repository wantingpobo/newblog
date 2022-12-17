<?php

namespace Database\Seeders;

use App\Models\Cgy;
use Illuminate\Database\Seeder;

class CgySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cgy::truncate();

        Cgy::factory()->times(20)->create();
    }
}