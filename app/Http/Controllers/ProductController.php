<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(10); // 10 products per page

        return view('products.index', ['products' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::orderBy('title', 'asc')->get();
        $categories = ProductCategory::orderBy('name', 'asc')->get();

        return view('products.create', ['products' => $products, 'categories' => $categories]);  
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'title')->whereNull('deleted_at'),
            ],
            'is_published' => 'required|boolean',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
            'parent_id' => 'nullable|exists:products,id',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'depth' => 'nullable|numeric|min:0',
            'image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_4' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'title.required' => 'De producttitel is verplicht.',
            'title.unique' => 'Deze producttitel bestaat al.',
            'is_published.required' => 'Geef aan of het product gepubliceerd is.',
            'is_published.boolean' => 'Het veld gepubliceerd moet waar of onwaar zijn.',
            'price.numeric' => 'De prijs moet een getal zijn.',
            'price.min' => 'De prijs moet minimaal 0 zijn.',
            'stock.numeric' => 'De voorraad moet een getal zijn.',
            'stock.min' => 'De voorraad moet minimaal 0 zijn.',
            'category_id.exists' => 'De geselecteerde categorie bestaat niet.',
            'parent_id.exists' => 'Het geselecteerde hoofdproduct bestaat niet.',
            'weight.numeric' => 'Het gewicht moet een getal zijn.',
            'height.numeric' => 'De hoogte moet een getal zijn.',
            'width.numeric' => 'De breedte moet een getal zijn.',
            'depth.numeric' => 'De diepte moet een getal zijn.',
            'image_1.image' => 'Afbeelding 1 moet een afbeelding zijn.',
            'image_1.mimes' => 'Afbeelding 1 moet een bestand zijn van het type: jpeg, png, jpg, gif, svg.',
            'image_1.max' => 'Afbeelding 1 mag niet groter zijn dan 2MB.',
            'image_2.image' => 'Afbeelding 2 moet een afbeelding zijn.',
            'image_2.mimes' => 'Afbeelding 2 moet een bestand zijn van het type: jpeg, png, jpg, gif, svg.',
            'image_2.max' => 'Afbeelding 2 mag niet groter zijn dan 2MB.',
            'image_3.image' => 'Afbeelding 3 moet een afbeelding zijn.',
            'image_3.mimes' => 'Afbeelding 3 moet een bestand zijn van het type: jpeg, png, jpg, gif, svg.',
            'image_3.max' => 'Afbeelding 3 mag niet groter zijn dan 2MB.',
            'image_4.image' => 'Afbeelding 4 moet een afbeelding zijn.',
            'image_4.mimes' => 'Afbeelding 4 moet een bestand zijn van het type: jpeg, png, jpg, gif, svg.',
            'image_4.max' => 'Afbeelding 4 mag niet groter zijn dan 2MB.',
        ]);

        $title= $validated['title'];
        $slug = Str::slug($title);

        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        // Handle image uploads
        for ($i = 1; $i <= 4; $i++) {
            $imageField = 'image_' . $i;
            if ($request->hasFile($imageField)) {
            $validated[$imageField] = $request->file($imageField)->store('product_images', 'public');
            }
        }

        // Create product
        $product = Product::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'category_id' => $validated['category_id'],
            'is_published' => $validated['is_published'],
            'short_description' => $validated['short_description'] ?? null,
            'long_description' => $validated['long_description'] ?? null,
            'price' => $validated['price'] ?? null,
            'stock' => $validated['stock'] ?? 0,
            'parent_id' => $validated['parent_id'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'height' => $validated['height'] ?? null,
            'width' => $validated['width'] ?? null,
            'depth' => $validated['depth'] ?? null,
            'image_1' => $validated['image_1'] ?? null,
            'image_2' => $validated['image_2'] ?? null,
            'image_3' => $validated['image_3'] ?? null,
            'image_4' => $validated['image_4'] ?? null,
            'created_by' => auth()->id(),
        ]);


        return redirect()->route('productIndex')->with('success', 'Product met ID: '.$product->id.' succesvol aangemaakt.');

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $products = Product::orderBy('title', 'asc')->get();
        $categories = ProductCategory::orderBy('name', 'asc')->get();

        $product = Product::findOrFail($id);
        return view('products.edit', ['product' => $product, 'products' => $products, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'title')->whereNull('deleted_at')->ignore($product->id),
            ],
            'is_published' => 'required|boolean',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
            'parent_id' => 'nullable|exists:products,id',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'depth' => 'nullable|numeric|min:0',
            'image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_4' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'hidden_image' => 'nullable|string',
        ], [
            'title.required' => 'De producttitel is verplicht.',
            'title.unique' => 'Deze producttitel bestaat al.',
            'is_published.required' => 'Geef aan of het product gepubliceerd is.',
            'is_published.boolean' => 'Het veld gepubliceerd moet waar of onwaar zijn.',
            'price.numeric' => 'De prijs moet een getal zijn.',
            'price.min' => 'De prijs moet minimaal 0 zijn.',
            'stock.numeric' => 'De voorraad moet een getal zijn.',
            'stock.min' => 'De voorraad moet minimaal 0 zijn.',
            'category_id.exists' => 'De geselecteerde categorie bestaat niet.',
            'parent_id.exists' => 'Het geselecteerde hoofdproduct bestaat niet.',
            'weight.numeric' => 'Het gewicht moet een getal zijn.',
            'height.numeric' => 'De hoogte moet een getal zijn.',
            'width.numeric' => 'De breedte moet een getal zijn.',
            'depth.numeric' => 'De diepte moet een getal zijn.',
            'image_1.image' => 'Afbeelding 1 moet een afbeelding zijn.',
            'image_1.mimes' => 'Afbeelding 1 moet een bestand zijn van het type: jpeg, png, jpg, gif, svg.',
            'image_1.max' => 'Afbeelding 1 mag niet groter zijn dan 2MB.',
            'image_2.image' => 'Afbeelding 2 moet een afbeelding zijn.',
            'image_2.mimes' => 'Afbeelding 2 moet een bestand zijn van het type: jpeg, png, jpg, gif, svg.',
            'image_2.max' => 'Afbeelding 2 mag niet groter zijn dan 2MB.',
            'image_3.image' => 'Afbeelding 3 moet een afbeelding zijn.',
            'image_3.mimes' => 'Afbeelding 3 moet een bestand zijn van het type: jpeg, png, jpg, gif, svg.',
            'image_3.max' => 'Afbeelding 3 mag niet groter zijn dan 2MB.',
            'image_4.image' => 'Afbeelding 4 moet een afbeelding zijn.',
            'image_4.mimes' => 'Afbeelding 4 moet een bestand zijn van het type: jpeg, png, jpg, gif, svg.',
            'image_4.max' => 'Afbeelding 4 mag niet groter zijn dan 2MB.',
        ]);

        $title = $validated['title'];
        $slug = Str::slug($title);

        // Ensure slug is unique, except for current category
        $originalSlug = $slug;
        $counter = 1;
        while (
            ProductCategory::where('slug', $slug)
                ->where('id', '!=', $product->id)
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter++;
        }

        // Handle image uploads
        for ($i = 1; $i <= 4; $i++) {
            $imageField = 'image_' . $i;
            $deleteField = 'delete_image_' . $i;

            // Verwijder afbeelding als checkbox is aangevinkt
            if ($request->has($deleteField) && $product->$imageField) {
                if (\Storage::disk('public')->exists($product->$imageField)) {
                    \Storage::disk('public')->delete($product->$imageField);
                }
                $validated[$imageField] = null;
            }
            // Anders: upload nieuwe afbeelding of behoud oude
            elseif ($request->hasFile($imageField)) {
                if (!empty($product->$imageField) && \Storage::disk('public')->exists($product->$imageField)) {
                    \Storage::disk('public')->delete($product->$imageField);
                }
                $validated[$imageField] = $request->file($imageField)->store('product_images', 'public');
            } else {
                $validated[$imageField] = $product->$imageField;
            }
        }

        $product->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'category_id' => $validated['category_id'],
            'is_published' => $validated['is_published'],
            'short_description' => $validated['short_description'] ?? null,
            'long_description' => $validated['long_description'] ?? null,
            'price' => $validated['price'] ?? null,
            'stock' => $validated['stock'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'height' => $validated['height'] ?? null,
            'width' => $validated['width'] ?? null,
            'depth' => $validated['depth'] ?? null,
            'image_1' => $validated['image_1'] ?? null,
            'image_2' => $validated['image_2'] ?? null,
            'image_3' => $validated['image_3'] ?? null,
            'image_4' => $validated['image_4'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Het product is succesvol bijgewerkt.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        // Verwijder afbeeldingen uit storage
        for ($i = 1; $i <= 4; $i++) {
            $imageField = 'image_' . $i;
            if (!empty($product->$imageField) && \Storage::disk('public')->exists($product->$imageField)) {
                \Storage::disk('public')->delete($product->$imageField);
            }
        }

        $product->update([
        'updated_by' => auth()->id(),
        'deleted_by' => auth()->id(),
        'image_1' => '',
        'image_2' => '',
        'image_3' => '',
        'image_4' => '',
        ]);
        $product->delete();

        return redirect()->route('productIndex')->with('success', 'Het product is succesvol verwijderd.');
    }
}
