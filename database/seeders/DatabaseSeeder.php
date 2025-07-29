<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
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
        // Maak users aan
        User::create([
            'first_name' => 'Bilal',
            'last_name' => 'van Loon',
            'email' => 'bilalvanloon@gmail.com',
            'role' => 'admin',
            'password' => static::$password ??= Hash::make('12345678'),
        ]);

        User::create([
            'first_name' => 'Pascal',
            'last_name' => 'van Loon',
            'email' => 'vanloon_1983@hotmail.com',
            'role' => 'user',
            'password' => static::$password ??= Hash::make('12345678'),
        ]);

        // Maak categorieën aan
        
        // ProductCategory::factory(10)->create();

        // $categories = ProductCategory::all();

        // Maak producten aan, elk met één random category_id

        // Product::factory(20)->make()->each(function ($product) use ($categories) {
        //     $product->category_id = $categories->random()->id;
        //     $product->save();
        // });

        // Parent-logica: wijs willekeurig een parent toe (niet zichzelf)

        // $products = Product::all();
        // foreach ($products as $product) {
        //     $possibleParents = $products->where('id', '!=', $product->id);
        //     if ($possibleParents->count() && rand(0, 1)) {
        //         $product->parent_id = $possibleParents->random()->id;
        //         $product->save();
        //     }
        // }
    }
}