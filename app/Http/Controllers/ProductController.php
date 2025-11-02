<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCopy;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    // Helper to compress and store an uploaded image so it's <= 1 MB when possible
    private function compressAndStore($file)
    {
        if (!$file) {
            return null;
        }

        // Preserve SVGs as-is (Intervention rasterizes vector images)
        $mime = $file->getMimeType();
        if ($mime === 'image/svg+xml') {
            return $file->store('product_images', 'public');
        }

        try {
            // Instantiate ImageManager (prefer imagick if available)
            if (extension_loaded('imagick')) {
                $manager = ImageManager::imagick();
            } else {
                $manager = ImageManager::gd();
            }

            // Use v3 API: read() to create image instance and orient() to fix rotation
            $image = $manager->read($file->getRealPath())->orient();
        } catch (\Exception $e) {
            // If processing fails, fallback to storing original upload
            return $file->store('product_images', 'public');
        }

        $targetMaxBytes = 1024 * 1024; // 1 MB
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        $scale = 1.0;
        $quality = 90;
        $finalEncoded = null;
        $usedEncoder = 'jpg';

        // If PNG, try to preserve transparency: prefer WebP (if supported) or PNG indexed encoding
        $isPng = in_array($mime, ['image/png'], true);
        $canUseWebp = extension_loaded('imagick') || function_exists('imagewebp');

        // Progressive strategy: reduce quality first (for lossy encoders), then dimensions
        while (true) {
            $tmp = clone $image;
            $newWidth = (int) max(1, $originalWidth * $scale);
            $newHeight = (int) max(1, $originalHeight * $scale);

            $tmp->resize($newWidth, $newHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            if ($isPng) {
                // Prefer WebP for smaller size when available (WebP supports alpha)
                if ($canUseWebp) {
                    $encoded = (string) $tmp->encode(new WebpEncoder($quality));
                    $usedEncoder = 'webp';
                } else {
                    // Use indexed PNG to try to reduce size while preserving alpha
                    $encoded = (string) $tmp->encode(new PngEncoder(true, true));
                    $usedEncoder = 'png';
                }
            } else {
                // Encode as JPEG for strong compression (most uploads are photos)
                $encoded = (string) $tmp->encode(new JpegEncoder($quality));
                $usedEncoder = 'jpg';
            }

            $size = strlen($encoded);

            if ($size <= $targetMaxBytes) {
                $finalEncoded = $encoded;
                break;
            }

            if (!$isPng && $quality > 30) {
                $quality -= 5;
                continue;
            }

            if ($scale > 0.5) {
                $scale -= 0.1; // reduce dimensions by 10%
                $quality = 80; // restore quality for the new size
                continue;
            }

            // If we can't get under 1MB, accept the best effort
            $finalEncoded = $encoded;
            break;
        }

        // Save with a unique filename and correct extension
        $ext = $usedEncoder === 'webp' ? 'webp' : ($usedEncoder === 'png' ? 'png' : 'jpg');
        $filename = 'product_images/' . time() . '_' . Str::random(8) . '.' . $ext;
        Storage::disk('public')->put($filename, $finalEncoded);

        return $filename;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Product::class);

        $products = Product::with(['category', 'productCopy'])
            ->orderBy('title', 'desc')
            ->paginate(10);

        return view('products.index', ['products' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Product::class);
        $products = Product::orderBy('title', 'asc')->get();
        $categories = ProductCategory::orderBy('name', 'asc')->get();
        $productCopies = ProductCopy::orderBy('name', 'asc')->get();

        return view('products.create', [
            'products' => $products,
            'categories' => $categories,
            'productCopies' => $productCopies
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $messages = [
            'title.required' => 'De productnaam is verplicht.',
            'title.string' => 'De productnaam moet tekst zijn.',
            'title.max' => 'De productnaam mag maximaal 255 tekens zijn.',
            'is_published.required' => 'Geef aan of het product gepubliceerd is.',
            'is_published.boolean' => 'Ongeldige waarde voor gepubliceerd.',
            'short_description.string' => 'Korte omschrijving moet tekst zijn.',
            'long_description.string' => 'Lange omschrijving moet tekst zijn.',
            'price.numeric' => 'Prijs moet een getal zijn.',
            'price.min' => 'Prijs mag niet negatief zijn.',
            'stock.numeric' => 'Voorraad moet een getal zijn.',
            'stock.min' => 'Voorraad mag niet negatief zijn.',
            'category_id.exists' => 'Ongeldige categorie.',
            'product_copy_id.exists' => 'Ongeldige kopie.',
            'weight.numeric' => 'Gewicht moet een getal zijn.',
            'weight.min' => 'Gewicht mag niet negatief zijn.',
            'height.numeric' => 'Hoogte moet een getal zijn.',
            'height.min' => 'Hoogte mag niet negatief zijn.',
            'width.numeric' => 'Breedte moet een getal zijn.',
            'width.min' => 'Breedte mag niet negatief zijn.',
            'depth.numeric' => 'Diepte moet een getal zijn.',
            'depth.min' => 'Diepte mag niet negatief zijn.',
            'image_1.image' => 'Afbeelding 1 moet een afbeelding zijn.',
            'image_1.mimes' => 'Afbeelding 1 moet jpeg, png, jpg, gif of svg zijn.',
            'image_1.max' => 'Afbeelding 1 mag maximaal 2MB zijn.',
            'image_2.image' => 'Afbeelding 2 moet een afbeelding zijn.',
            'image_2.mimes' => 'Afbeelding 2 moet jpeg, png, jpg, gif of svg zijn.',
            'image_2.max' => 'Afbeelding 2 mag maximaal 2MB zijn.',
            'image_3.image' => 'Afbeelding 3 moet een afbeelding zijn.',
            'image_3.mimes' => 'Afbeelding 3 moet jpeg, png, jpg, gif of svg zijn.',
            'image_3.max' => 'Afbeelding 3 mag maximaal 2MB zijn.',
            'image_4.image' => 'Afbeelding 4 moet een afbeelding zijn.',
            'image_4.mimes' => 'Afbeelding 4 moet jpeg, png, jpg, gif of svg zijn.',
            'image_4.max' => 'Afbeelding 4 mag maximaal 2MB zijn.',
        ];

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_published' => 'required|boolean',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
            'product_copy_id' => 'nullable|integer|exists:product_copies,id',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'depth' => 'nullable|numeric|min:0',
            'image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:30720',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:30720',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:30720',
            'image_4' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:30720',
        ], $messages);

        $copy = !empty($validated['product_copy_id'])
            ? ProductCopy::find($validated['product_copy_id'])
            : null;

        $title = trim($validated['title']);

        // base_title altijd zonder exemplaar
        if ($copy && $copy->name) {
            $baseTitle = preg_replace('/\s*-\s*'.preg_quote($copy->name, '/').'$/iu', '', $title);
        } else {
            $baseTitle = $title;
        }
        $baseSlug = Str::slug($baseTitle);

        // title = base_title + exemplaar (indien aanwezig)
        if ($copy && $copy->name) {
            $title = $baseTitle.' - '.$copy->name;
        }

        // Uniekheid check
        /*
         * This regex removes a suffix from $title that matches " - <copy name>" (with optional spaces around the dash), where \<copy name\> is the value of $copy->name.
          It matches any whitespace, a dash, more whitespace, then the copy name at the end of the string (case-insensitive, Unicode).
          For example, if $title is Book - Special Edition and $copy->name is Special Edition, it will return Book.
         * */
        if (Product::where('title', $title)->whereNull('deleted_at')->exists()) {
            return back()->withInput()->withErrors(['title' => 'Deze producttitel bestaat al.']);
        }

        // Slug genereren
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->whereNull('deleted_at')->exists()) {
            $slug = $originalSlug.'-'.$counter++;
        }

        // Afbeeldingen verwerken
        for ($i = 1; $i <= 4; $i++) {
            $imageField = 'image_'.$i;
            if ($request->hasFile($imageField)) {
                $validated[$imageField] = $this->compressAndStore($request->file($imageField));
            }
        }

        $product = Product::create([
            'title' => $title,
            'base_title' => $baseTitle,
            'slug' => $slug,
            'base_slug' => $baseSlug,
            'category_id' => $validated['category_id'],
            'is_published' => $validated['is_published'],
            'short_description' => $validated['short_description'] ?? null,
            'long_description' => $validated['long_description'] ?? null,
            'price' => $validated['price'] ?? null,
            'stock' => $validated['stock'] ?? 0,
            'product_copy_id' => $validated['product_copy_id'] ?? null,
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

        return redirect()->route('productIndex')->with('success',
            'Product met ID: '.$product->id.' succesvol aangemaakt.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);

        $products = Product::orderBy('title', 'asc')->get();
        $categories = ProductCategory::orderBy('name', 'asc')->get();
        $productCopies = ProductCopy::orderBy('name', 'asc')->get();

        return view('products.edit', [
            'product' => $product,
            'products' => $products,
            'categories' => $categories,
            'productCopies' => $productCopies
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);

        $messages = [
            'title.required' => 'De productnaam is verplicht.',
            'title.string' => 'De productnaam moet tekst zijn.',
            'title.max' => 'De productnaam mag maximaal 255 tekens zijn.',
            'is_published.required' => 'Geef aan of het product gepubliceerd is.',
            'is_published.boolean' => 'Ongeldige waarde voor gepubliceerd.',
            'short_description.string' => 'Korte omschrijving moet tekst zijn.',
            'long_description.string' => 'Lange omschrijving moet tekst zijn.',
            'price.numeric' => 'Prijs moet een getal zijn.',
            'price.min' => 'Prijs mag niet negatief zijn.',
            'stock.numeric' => 'Voorraad moet een getal zijn.',
            'stock.min' => 'Voorraad mag niet negatief zijn.',
            'category_id.exists' => 'Ongeldige categorie.',
            'product_copy_id.exists' => 'Ongeldige kopie.',
            'weight.numeric' => 'Gewicht moet een getal zijn.',
            'weight.min' => 'Gewicht mag niet negatief zijn.',
            'height.numeric' => 'Hoogte moet een getal zijn.',
            'height.min' => 'Hoogte mag niet negatief zijn.',
            'width.numeric' => 'Breedte moet een getal zijn.',
            'width.min' => 'Breedte mag niet negatief zijn.',
            'depth.numeric' => 'Diepte moet een getal zijn.',
            'depth.min' => 'Diepte mag niet negatief zijn.',
            'image_1.image' => 'Afbeelding 1 moet een afbeelding zijn.',
            'image_1.mimes' => 'Afbeelding 1 moet jpeg, png, jpg, gif of svg zijn.',
            'image_1.max' => 'Afbeelding 1 mag maximaal 2MB zijn.',
            'image_2.image' => 'Afbeelding 2 moet een afbeelding zijn.',
            'image_2.mimes' => 'Afbeelding 2 moet jpeg, png, jpg, gif of svg zijn.',
            'image_2.max' => 'Afbeelding 2 mag maximaal 2MB zijn.',
            'image_3.image' => 'Afbeelding 3 moet een afbeelding zijn.',
            'image_3.mimes' => 'Afbeelding 3 moet jpeg, png, jpg, gif of svg zijn.',
            'image_3.max' => 'Afbeelding 3 mag maximaal 2MB zijn.',
            'image_4.image' => 'Afbeelding 4 moet een afbeelding zijn.',
            'image_4.mimes' => 'Afbeelding 4 moet jpeg, png, jpg, gif of svg zijn.',
            'image_4.max' => 'Afbeelding 4 mag maximaal 2MB zijn.',
        ];

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_published' => 'required|boolean',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|numeric|min:0',
            'product_copy_id' => 'nullable|integer|exists:product_copies,id',
            'category_id' => 'nullable|exists:product_categories,id',
            'parent_id' => 'nullable|exists:products,id',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'depth' => 'nullable|numeric|min:0',
            'image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:30720',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:30720',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:30720',
            'image_4' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:30720',
        ], $messages);

        $copy = !empty($validated['product_copy_id'])
            ? ProductCopy::find($validated['product_copy_id'])
            : null;

        $title = trim($validated['title']);

        // base_title altijd zonder exemplaar
        if ($copy && $copy->name) {
            $baseTitle = preg_replace('/\s*-\s*'.preg_quote($copy->name, '/').'$/iu', '', $title);
        } else {
            $baseTitle = $title;
        }
        $baseSlug = Str::slug($baseTitle);

        // title = base_title + exemplaar (indien aanwezig)
        if ($copy && $copy->name) {
            $title = $baseTitle.' - '.$copy->name;
        }

        // Uniekheid check
        if (Product::where('title', $title)->whereNull('deleted_at')->where('id', '!=', $product->id)->exists()) {
            return back()->withInput()->withErrors(['title' => 'Deze producttitel bestaat al.']);
        }

        // Slug genereren
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->whereNull('deleted_at')->where('id', '!=', $product->id)->exists()) {
            $slug = $originalSlug.'-'.$counter++;
        }

        // Afbeeldingen verwerken
        for ($i = 1; $i <= 4; $i++) {
            $imageField = 'image_'.$i;
            $deleteField = 'delete_image_'.$i;

            if ($request->has($deleteField) && $product->$imageField) {
                if (Storage::disk('public')->exists($product->$imageField)) {
                    Storage::disk('public')->delete($product->$imageField);
                }
                $validated[$imageField] = null;
            } elseif ($request->hasFile($imageField)) {
                if (!empty($product->$imageField) && Storage::disk('public')->exists($product->$imageField)) {
                    Storage::disk('public')->delete($product->$imageField);
                }
                $validated[$imageField] = $this->compressAndStore($request->file($imageField));
            } else {
                $validated[$imageField] = $product->$imageField;
            }
        }

        $product->update([
            'title' => $title,
            'base_title' => $baseTitle,
            'slug' => $slug,
            'base_slug' => $baseSlug,
            'category_id' => $validated['category_id'],
            'is_published' => $validated['is_published'],
            'short_description' => $validated['short_description'] ?? null,
            'long_description' => $validated['long_description'] ?? null,
            'price' => $validated['price'] ?? null,
            'stock' => $validated['stock'] ?? null,
            'product_copy_id' => $validated['product_copy_id'] ?? null,
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
        $this->authorize('delete', $product);

        // Verwijder afbeeldingen uit storage
        for ($i = 1; $i <= 4; $i++) {
            $imageField = 'image_'.$i;
            if (!empty($product->$imageField) && Storage::disk('public')->exists($product->$imageField)) {
                Storage::disk('public')->delete($product->$imageField);
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

    public function get()
    {
        return redirect()->route('dashboard');
    }
}
