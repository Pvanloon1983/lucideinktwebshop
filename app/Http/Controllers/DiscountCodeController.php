<?php

namespace App\Http\Controllers;

use App\Models\DiscountCode;
use Illuminate\Http\Request;

class DiscountCodeController extends Controller
{
    public function __construct()
    {
      $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', DiscountCode::class);

        $discountCodes = DiscountCode::orderBy('created_at', 'desc')
        ->paginate(10);

        return view('discountcodes.index', ['discountCodes' => $discountCodes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      $this->authorize('create', DiscountCode::class);

      return view('discountcodes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', DiscountCode::class);

        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:discount_codes,code',
            'description' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percent,amount',
            'discount' => 'required|numeric|min:0.01',
            'expiration_date' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_customer' => 'nullable|integer|min:1',
            'is_published' => 'required|boolean',
        ], [
            'code.required' => 'De kortingscode is verplicht.',
            'code.unique' => 'Deze kortingscode bestaat al.',
            'discount_type.required' => 'Selecteer een kortingstype.',
            'discount_type.in' => 'Kies een geldig kortingstype.',
            'discount.required' => 'Vul een kortingsbedrag in.',
            'discount.numeric' => 'De korting moet een getal zijn.',
            'discount.min' => 'De korting moet minimaal 0,01 zijn.',
            'expiration_date.date' => 'Vul een geldige datum in.',
            'usage_limit.integer' => 'De gebruikslimiet moet een geheel getal zijn.',
            'usage_limit.min' => 'De gebruikslimiet moet minimaal 1 zijn.',
            'usage_limit_per_customer.integer' => 'De gebruikslimiet moet een geheel getal zijn.',
            'usage_limit_per_customer.min' => 'De gebruikslimiet moet minimaal 1 zijn.',
            'is_published.required' => 'Geef aan of de code gepubliceerd moet worden.',
            'is_published.boolean' => 'Ongeldige waarde voor publiceren.',
        ]);

        // Ensure correct formatting for discount value
        if ($validated['discount_type'] === 'percent') {
            $validated['discount'] = (int) $validated['discount'];
        } else {
            $validated['discount'] = number_format($validated['discount'], 2, '.', '');
        }

        $discountCode = DiscountCode::create($validated);

        return redirect()->route('discountCreate')
            ->with('success', 'Kortingscode succesvol aangemaakt!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
      $discountCode = DiscountCode::findOrFail($id);
      $this->authorize('update', $discountCode);
      return view('discountcodes.edit', ['discountCode' => $discountCode]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
      $discountCode = DiscountCode::findOrFail($id);
      $this->authorize('update', $discountCode);

      $validated = $request->validate([
        'code' => 'required|string|max:255|unique:discount_codes,code,' . $discountCode->id,
        'description' => 'nullable|string|max:255',
        'discount_type' => 'required|in:percent,amount',
        'discount' => 'required|numeric|min:0.01',
        'expiration_date' => 'nullable|date',
        'usage_limit' => 'nullable|integer|min:1',
        'usage_limit_per_customer' => 'nullable|integer|min:1',
        'is_published' => 'required|boolean',
      ], [
        'code.required' => 'De kortingscode is verplicht.',
        'code.unique' => 'Deze kortingscode bestaat al.',
        'discount_type.required' => 'Selecteer een kortingstype.',
        'discount_type.in' => 'Kies een geldig kortingstype.',
        'discount.required' => 'Vul een kortingsbedrag in.',
        'discount.numeric' => 'De korting moet een getal zijn.',
        'discount.min' => 'De korting moet minimaal 0,01 zijn.',
        'expiration_date.date' => 'Vul een geldige datum in.',
        'usage_limit.integer' => 'De gebruikslimiet moet een geheel getal zijn.',
        'usage_limit.min' => 'De gebruikslimiet moet minimaal 1 zijn.',
        'usage_limit_per_customer.integer' => 'De gebruikslimiet moet een geheel getal zijn.',
        'usage_limit_per_customer.min' => 'De gebruikslimiet moet minimaal 1 zijn.',
        'is_published.required' => 'Geef aan of de code gepubliceerd moet worden.',
        'is_published.boolean' => 'Ongeldige waarde voor publiceren.',
      ]);

      if ($validated['discount_type'] === 'percent') {
        $validated['discount'] = (int) $validated['discount'];
      } else {
        $validated['discount'] = number_format($validated['discount'], 2, '.', '');
      }

      $discountCode->update($validated);

      return redirect()->route('discountEdit', $discountCode->id)
        ->with('success', 'Kortingscode succesvol bijgewerkt!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      $discountCode = DiscountCode::findOrFail($id);
      $this->authorize('delete', $discountCode);
      $discountCode->delete();

      return redirect()->route('discountIndex')
        ->with('success', 'Kortingscode succesvol verwijderd!');
    }

    public function get () {
      return redirect()->route('dashboard');
    }
}
