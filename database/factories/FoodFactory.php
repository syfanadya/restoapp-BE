<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Food>
 */
class FoodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['appetizers', 'main_course', 'desserts', 'beverages', 'salads'];
        return [
            'name' => $this->faker->word(),
            'category' => $this->faker->randomElement($categories),
            'price' => $this->faker->numberBetween(5, 25) * 1000,
        ];
    }
}
