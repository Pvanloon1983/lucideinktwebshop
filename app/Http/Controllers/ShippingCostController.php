<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShippingCost;
use Illuminate\Validation\Rule;

class ShippingCostController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin'])->except('getCost');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', ShippingCost::class);
        $shippingCosts = ShippingCost::orderBy('created_at', 'desc')->paginate(10);
        return view('shippingcosts.index', ['shippingCosts' => $shippingCosts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', ShippingCost::class);
        return view('shippingcosts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', ShippingCost::class);
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'country' => 'required|string|max:255',
            'is_published' => 'required|boolean',
        ], [
            'amount.required' => 'Bedrag is verplicht.',
            'amount.numeric' => 'Bedrag moet een getal zijn.',
            'amount.min' => 'Bedrag moet minimaal 0 zijn.',
            'country.required' => 'Land is verplicht.',
            'country.max' => 'Land mag niet meer dan 255 karakters zijn.',
            'is_published.required' => 'Publicatie status is verplicht.',
            'is_published.boolean' => 'Publicatie status moet een geldige waarde zijn.',
        ]);

        $shippingCost = ShippingCost::create([
            'amount' => $validated['amount'],
            'country' => $validated['country'],
            'is_published' => $validated['is_published'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('shippingCostIndex')->with('success', 'Verzendkosten zijn succesvol toegevoegd.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $shippingCost = ShippingCost::findOrFail($id);
        $this->authorize('update', $shippingCost);
        return view('shippingcosts.edit', ['shippingCost' => $shippingCost]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shippingCost = ShippingCost::findOrFail($id);
        $this->authorize('update', $shippingCost);
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'country' => 'required|string|max:255',
            'is_published' => 'required|boolean',
        ], [
            'amount.required' => 'Bedrag is verplicht.',
            'amount.numeric' => 'Bedrag moet een getal zijn.',
            'amount.min' => 'Bedrag moet minimaal 0 zijn.',
            'country.required' => 'Land is verplicht.',
            'country.max' => 'Land mag niet meer dan 255 karakters zijn.',
            'is_published.required' => 'Publicatie status is verplicht.',
            'is_published.boolean' => 'Publicatie status moet een geldige waarde zijn.',
        ]);

        $shippingCost->update([
            'amount' => $validated['amount'],
            'country' => $validated['country'],
            'is_published' => $validated['is_published'],
        ]);

        return back()->with('success', 'Verzendkosten zijn succesvol bijgewerkt.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shippingCost = ShippingCost::findOrFail($id);
        $this->authorize('delete', $shippingCost);
        $shippingCost->update([
            'updated_by' => auth()->id(),
            'deleted_by' => auth()->id(),
        ]);
        $shippingCost->delete();
        return back()->with('success', 'Verzendkosten zijn succesvol verwijderd.');
    }

    public function getCost(Request $request)
    {
        $country = $request->query('country');
        $shippingCost = \App\Models\ShippingCost::where('country', $country)
            ->where('is_published', 1)
            ->orderByDesc('amount')
            ->first();

        $cost = $shippingCost ? $shippingCost->amount : 0;
        return response()->json(['cost' => $cost]);
    }

    public function get()
    {
        return redirect()->route('dashboard');
    }
}
