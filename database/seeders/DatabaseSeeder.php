<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\CgySeeder;
use Database\Seeders\ItemSeeder;
use Database\Seeders\ItemTagSeeder;
use Database\Seeders\TagSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $this->call(CgySeeder::class);
        $this->call(ItemSeeder::class);
        $this->call(ItemTagSeeder::class);
        $this->call(TagSeeder::class);
        $this->call(UserSeeder::class);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

    }
}