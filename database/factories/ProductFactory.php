<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'detail' => random_int(1, 5) > 2 ? fake()->sentence(10) : null,
            'original_filename' => null,
            'mime_type' => null,
            'stored_filename' => null,
        ];
    }
}
