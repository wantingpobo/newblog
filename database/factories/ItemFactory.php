<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->realText(20),
            'pic' => $this->faker->imageUrl,
            'price' => rand(50, 3000),
            'enabled' => $this->faker->randomElement([true, false]),
            'desc' => $this->faker->realText,
            'enabled_at' => Carbon::now()->addDays(rand(0, 20)),
            'cgy_id' => rand(1, 20),

        ];
    }
}