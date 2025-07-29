<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productCategories = ProductCategory::orderBy('created_at', 'desc')
        ->paginate(10);

        return view('productcategories.index', ['productCategories' => $productCategories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('productcategories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name',
            'is_published' => 'required|boolean',
        ], [
            'name.required' => 'Naam is verplicht.',
            'name.unique' => 'Deze naam is al in gebruik.',
            'name.max' => 'De lengte mag niet meer dan 255 karakters zijn',
            'is_published.required' => 'Publicatie status is verplicht.',
            'is_published.boolean' => 'Publicatie status moet een geldige waarde zijn.',
        ]);

        $name = $validated['name'];
        $slug = Str::slug($name);

        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (ProductCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $productCategory = ProductCategory::create([
            'name' => $name,
            'slug' => $slug,
            'is_published' => $validated['is_published'],
            'created_by' => auth()->id(),
        ]);

        // Redirect to login page
        return redirect()->route('productCategoryIndex')->with('success', 'Productcategorie is succesvol toegevoegd.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = ProductCategory::findOrFail($id);
        return view('productcategories.edit', ['category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = ProductCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name,' . $category->id,
            'is_published' => 'required|boolean',
        ], [
            'name.required' => 'Naam is verplicht.',
            'name.unique' => 'Deze naam is al in gebruik.',
            'name.max' => 'De lengte mag niet meer dan 255 karakters zijn',
            'is_published.required' => 'Publicatie status is verplicht.',
            'is_published.boolean' => 'Publicatie status moet een geldige waarde zijn.',
        ]);

        $name = $validated['name'];
        $slug = Str::slug($name);

        // Ensure slug is unique, except for current category
        $originalSlug = $slug;
        $counter = 1;
        while (
            ProductCategory::where('slug', $slug)
                ->where('id', '!=', $category->id)
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $category->update([
            'name' => $name,
            'slug' => $slug,
            'is_published' => $validated['is_published'],
        ]);

        return redirect()->route('productCategoryIndex')->with('success', 'Productcategorie met ID: '.$category->id.' is succesvol bijgewerkt.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = ProductCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('productCategoryIndex')->with('success', 'Productcategorie is succesvol verwijderd.');
    }
}
