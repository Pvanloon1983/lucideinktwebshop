<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductCopy;
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

        $category = ProductCategory::create([
              'name' => 'Risale-i Nur',
              'slug' => Str::slug('Risale-i Nur'),
              'created_by' => '1',
              'updated_by' => '1',
              'is_published' => '1'
          ]);

  $books = [
          [
            'title' => 'Afwegingen van Geloof & Ongeloof - Nederlands',
            'slug' => Str::slug('Afwegingen van Geloof & Ongeloof - Nederlands'),
            'short_description' => 'In dit boek wordt het verschil tussen de waarnemingen en vruchten van een gelovige visie en een ongelovige visie behandeld. Zodoende wordt de lezer in staat gesteld om af te wegen welke weg beter voor hem is. De logische, rationele en feitelijke bevindingen in dit boek maken duidelijk dat de ene visie op aarde al helse folteringen veroorzaakt, terwijl de andere visie op aarde al paradijselijke geneugten oplevert. Een objectieve lezer zal ervaren dat dit boek zal bijdragen aan het scherpstellen van zijn levensbeschouwing.',
            'long_description' => 'In dit boek wordt het verschil tussen de waarnemingen en vruchten van een gelovige visie en een ongelovige visie behandeld. Zodoende wordt de lezer in staat gesteld om af te wegen welke weg beter voor hem is. De logische, rationele en feitelijke bevindingen in dit boek maken duidelijk dat de ene visie op aarde al helse folteringen veroorzaakt, terwijl de andere visie op aarde al paradijselijke geneugten oplevert. Een objectieve lezer zal ervaren dat dit boek zal bijdragen aan het scherpstellen van zijn levensbeschouwing.',
            'price' => 15.00,
            'is_published' => 1,
            'stock' => 100,
            'image_1' => 'images/books/afwegingen_nl/afwegingen_front_NL_WEB.webp',
            'image_2' => 'images/books/afwegingen_nl/afwegingen_inside_NL_WEB.webp',
            'created_by' => 1,
            'updated_by' => 1,
          ],
          [
            'title' => 'Het Traktaat Voor de Zieken - Nederlands',
            'slug' => Str::slug('Het Traktaat Voor de Zieken - Nederlands'),
            'short_description' => 'In dit boek worden vijfentwintig genezingen behandeld. Deze genezingen zijn als een zalf, een troost en een spiritueel recept voor zieken geschreven. Daarbij is er een condoleance in verband met het verlies van een kind, een traktaat betreffende de profeet Eyyoub en een brief aan een dokter gevoegd. Dit traktaat zal de lezer erbij helpen om alle soorten ziektes en calamiteiten te boven te komen.',
            'long_description' => 'In dit boek worden vijfentwintig genezingen behandeld. Deze genezingen zijn als een zalf, een troost en een spiritueel recept voor zieken geschreven. Daarbij is er een condoleance in verband met het verlies van een kind, een traktaat betreffende de profeet Eyyoub en een brief aan een dokter gevoegd. Dit traktaat zal de lezer erbij helpen om alle soorten ziektes en calamiteiten te boven te komen.',
            'price' => 5.00,
            'is_published' => 1,
            'stock' => 100,
            'image_1' => 'images/books/zieken_nl/zieken_front_NL_WEB.webp',
            'image_2' => 'images/books/zieken_nl/zieken_inside_NL_WEB.webp',
            'created_by' => 1,
            'updated_by' => 1,
          ],
          [
              'title' => 'Het Traktaat Voor de Zieken - Nederlands-Turks',
              'slug' => Str::slug('Het Traktaat Voor de Zieken - Nederlands-Turks'),
              'short_description' => 'In dit boek worden vijfentwintig genezingen behandeld. Deze genezingen zijn als een zalf, een troost en een spiritueel recept voor zieken geschreven. Daarbij is er een condoleance in verband met het verlies van een kind, een traktaat betreffende de profeet Eyyoub en een brief aan een dokter gevoegd. Dit traktaat zal de lezer erbij helpen om alle soorten ziektes en calamiteiten te boven te komen.',
              'long_description' => 'In dit boek worden vijfentwintig genezingen behandeld. Deze genezingen zijn als een zalf, een troost en een spiritueel recept voor zieken geschreven. Daarbij is er een condoleance in verband met het verlies van een kind, een traktaat betreffende de profeet Eyyoub en een brief aan een dokter gevoegd. Dit traktaat zal de lezer erbij helpen om alle soorten ziektes en calamiteiten te boven te komen.',
              'price' => 7.50,
              'is_published' => 1,
              'stock' => 100,
              'image_1' => 'images/books/zieken_nl_tr/zieken_front_light_NL-TR_WEB.webp',
              'image_2' => 'images/books/zieken_nl_tr/zieken_inside_NL-TR_WEB.webp',
              'created_by' => 1,
              'updated_by' => 1,
          ]
        ];

        foreach ($books as $book) {
            $book['slug'] = $book['slug'] ?? Str::slug($book['title']);
            $book['category_id'] = $category->id;
            Product::create($book);
        }

      // Nederlands
      $nlCopy = ProductCopy::create([
        'name' => 'Nederlands',
        'is_published' => 1,
        'created_by' => 1,
        'updated_by' => 1,
      ]);

      // Nederlands-Turks
      $nlTrCopy = ProductCopy::create([
        'name' => 'Nederlands-Turks',
        'is_published' => 1,
        'created_by' => 1,
        'updated_by' => 1,
      ]);

     // Engels
     $enCopy = ProductCopy::create([
        'name' => 'Engels',
        'is_published' => 1,
        'created_by' => 1,
        'updated_by' => 1,
     ]);

    // Engels-Turks
    $enTrCopy = ProductCopy::create([
        'name' => 'Engels-Turks',
        'is_published' => 1,
        'created_by' => 1,
        'updated_by' => 1,
    ]);

    }
}
