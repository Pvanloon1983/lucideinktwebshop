<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Support\Str;
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
            'title' => $title = $this->faker->words(3, true),
            'slug' => Str::slug($title),
            'short_description' => $this->faker->sentence(),
            'long_description' => $this->faker->paragraph(),
            'weight' => $this->faker->randomFloat(2, 0.1, 10),
            'height' => $this->faker->randomFloat(2, 1, 100),
            'width' => $this->faker->randomFloat(2, 1, 100),
            'depth' => $this->faker->randomFloat(2, 1, 100),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'is_published' => $this->faker->numberBetween(0, 1),
            'image_1' => 'https://picsum.photos/1920/1080?random=' . fake()->unique()->numberBetween(1, 10000),
            'image_2' => 'https://picsum.photos/1920/1080?random=' . fake()->unique()->numberBetween(1, 10000),
            'image_3' => 'https://picsum.photos/1920/1080?random=' . fake()->unique()->numberBetween(1, 10000),
            'image_4' => 'https://picsum.photos/1920/1080?random=' . fake()->unique()->numberBetween(1, 10000),
            'created_by' => 1, // or use User::factory()
            'updated_by' => 1, // or null
        ];
    }
}
