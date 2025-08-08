<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'title' => 'Afwegingen van Geloof & Ongeloof',
            'slug' => Str::slug('Afwegingen van Geloof & Ongeloof'),
            'short_description' => 'In dit boek wordt het verschil tussen de waarnemingen en vruchten van een gelovige visie en een ongelovige visie behandeld.',
            'long_description' => 'In dit boek wordt het verschil tussen de waarnemingen en vruchten van een gelovige visie en een ongelovige visie behandeld. Zodoende wordt de lezer in staat gesteld om af te wegen welke weg beter voor hem is. De logische, rationele en feitelijke bevindingen in dit boek maken duidelijk dat de ene visie op aarde al helse folteringen veroorzaakt, terwijl de andere visie op aarde al paradijselijke geneugten oplevert. Een objectieve lezer zal ervaren dat dit boek zal bijdragen aan het scherpstellen van zijn levensbeschouwing.',
            'price' => 15.00,
            'is_published' => 1,
            'stock' => 100,
            'image_1' => 'images/books/afwegingen.jpg',
            'created_by' => 1,
            'updated_by' => 1,
          ],
          [
            'title' => 'De Mirakelen van Ahmed',
            'slug' => Str::slug('De Mirakelen van Ahmed'),
            'short_description' => 'In dit boek worden er in eerste instantie de wijsheden achter de mirakelen van de profeet Mohammed verklaard.',
            'long_description' => 'In dit boek worden er in eerste instantie de wijsheden achter de mirakelen van de profeet Mohammed verklaard. Daarna worden de verschillende varianten van zijn mirakelen beschreven. Vervolgens worden er van elke variant een aantal voorbeelden genoemd die volgens de authentiekste overleveringen absoluut hebben plaatsgevonden. Bovendien beschrijft dit boek de geestelijke persoonlijkheid van Mohammed. Iemand die dit boek begrijpend uitleest, zal niet meer in staat zijn om het profeetschap van Mohammed te verloochenen.',
            'price' => 10.00,
            'is_published' => 1,
            'stock' => 100,
            'image_1' => 'images/books/mirakelen.jpg',
            'created_by' => 1,
            'updated_by' => 1,
          ],
          [
            'title' => 'Geloofswaarheden',
            'slug' => Str::slug('Geloofswaarheden'),
            'short_description' => 'In dit boek worden verscheidene geloofskwesties behandeld.',
            'long_description' => 'In dit boek worden verscheidene geloofskwesties behandeld. Waarheden achter bepaalde Islamitische geloofsfundamenten worden verhelderd, waaronder de wijsheid achter de dagelijkse vijf tijdstippen van de geboden gebeden en de wijsheid achter de schepping van de duivel. Dit boek zal voor de lezer vele controversiële punten ontwarren.',
            'price' => 5.00,
            'is_published' => 1,
            'stock' => 100,
            'image_1' => 'images/books/geloofwaarheden.jpg',
            'created_by' => 1,
            'updated_by' => 1,
          ],
          [
            'title' => 'Het Traktaat over de Natuur',
            'slug' => Str::slug('Het Traktaat over de Natuur'),
            'short_description' => 'In dit boek wordt vanuit velerlei verscheidene natuurwetenschappelijke gezichtspunten aangetoond dat het onbestaan van een Opperwezen onmogelijk is.',
            'long_description' => 'Terwijl tegenwoordig vaak de natuur wordt aangekaart om het onbestaan van een God aan te tonen, wordt er in dit boek vanuit velerlei verscheidene natuurwetenschappelijke gezichtspunten aangetoond dat het onbestaan van een Opperwezen juist onmogelijk is. Iemand die dit boek begrijpend leest, kan in geen enkel rationeel opzicht het bestaan en de eenheid van een God verloochenen.',
            'price' => 2.50,
            'is_published' => 1,
            'stock' => 100,
            'image_1' => 'images/books/natuur.jpg',
            'created_by' => 1,
            'updated_by' => 1,
          ],
          [
            'title' => 'Broederschap en Oprechtheid',
            'slug' => Str::slug('Broederschap en Oprechtheid'),
            'short_description' => 'Dit boek beschrijft hoe Islamitische broederschap en oprechtheid in de huidige tijden kunnen worden betracht.',
            'long_description' => 'Dit boek beschrijft hoe Islamitische broederschap en oprechtheid in de huidige tijden kunnen worden betracht. Oorzaken die broederschap en oprechtheid ondermijnen worden uiteengezet, waarnaast Qur’anische principes worden omschreven die resulteren in de totstandbrenging en handhaving van ware Islamitische broederschap en oprechtheid.',
            'price' => 7.50,
            'is_published' => 1,
            'stock' => 100,
            'image_1' => 'images/books/broederschap.jpg',
            'created_by' => 1,
            'updated_by' => 1,
          ],
          [
            'title' => 'Het Traktaat Voor de Zieken',
            'slug' => Str::slug('Het Traktaat Voor de Zieken'),
            'short_description' => 'In dit boek worden vijfentwintig genezingen behandeld.',
            'long_description' => 'In dit boek worden vijfentwintig genezingen behandeld. Deze genezingen zijn als een zalf, een troost en een spiritueel recept voor zieken geschreven. Daarbij is er een condoleance in verband met het verlies van een kind, een traktaat betreffende de profeet Eyyoub en een brief aan een dokter gevoegd. Dit traktaat zal de lezer erbij helpen om alle soorten ziektes en calamiteiten te boven te komen.',
            'price' => 5.50,
            'is_published' => 1,
            'stock' => 100,
            'image_1' => 'images/books/zieken.jpg',
            'created_by' => 1,
            'updated_by' => 1,
          ]
        ];

        foreach ($books as $book) {
          Product::create([
            'title' => $book['title'],
            'slug' => $book['slug'],
            'short_description' => $book['short_description'],
            'long_description' => $book['long_description'],
            'price' => $book['price'],
            'is_published' => $book['is_published'],
            'stock' => $book['stock'],
            'image_1' => $book['image_1'],
            'created_by' => 1,
            'updated_by' => 1,
            'category_id' => $category->id,
          ]);
        }


        // Maak categorieën aan

      /*
         ProductCategory::factory(10)->create();

         $categories = ProductCategory::all();
      */

        // Maak producten aan, elk met één random category_id

      /*
         Product::factory(20)->make()->each(function ($product) use ($categories) {
             $product->category_id = $categories->random()->id;
             $product->save();
         });
      */

        // Parent-logica: wijs willekeurig een parent toe (niet zichzelf)

      /*
         $products = Product::all();
         foreach ($products as $product) {
             $possibleParents = $products->where('id', '!=', $product->id);
             if ($possibleParents->count() && rand(0, 1)) {
                 $product->parent_id = $possibleParents->random()->id;
                 $product->save();
             }
         }
      */
    }
}