<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $name = $this->faker->words(2, true),
            'slug' => Str::slug($name),
            'created_by' => '1',
            'updated_by' => '1',
            'is_published' => $this->faker->numberBetween(0, 1)
        ];
    }
}
