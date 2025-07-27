<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    protected static ?string $password;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'first_name' => 'Bilal',
            'last_name' => 'van Loon',
            'email' => 'bilalvanloon@gmail.com',
            'role' => 'admin',
            // 'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('12345678'),
            // 'remember_token' => Str::random(10),
        ]);

        User::create([
            'first_name' => 'Pascal',
            'last_name' => 'van Loon',
            'email' => 'vanloon_1983@hotmail.com',
            'role' => 'user',
            // 'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('12345678'),
            // 'remember_token' => Str::random(10),
        ]);

        // Create categories
        ProductCategory::factory(10)->create();

        // Create products
        Product::factory(20)->create();

        // Attach categories to products
        $categories = ProductCategory::all();
        Product::all()->each(function ($product) use ($categories) {
            $product->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );
        });

        $products = Product::all();
        foreach ($products as $product) {
            // Exclude self as parent, and optionally only assign to some products
            $possibleParents = $products->where('id', '!=', $product->id);
            if ($possibleParents->count() && rand(0, 1)) {
                $product->parent_id = $possibleParents->random()->id;
                $product->save();
            }
        }
    }
}
