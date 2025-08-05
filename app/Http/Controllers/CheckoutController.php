<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function create()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect('/winkel')->with('error', 'Je winkelwagen is leeg.');
        }

        return view('checkout.index', ['cart' => $cart]);
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect('/winkel')->with('error', 'Je winkelwagen is leeg.');
        }

        // Validate billing and shipping fields
        $rules = [
            'billing-email' => 'required|email',
            'billing-first_name' => 'required|string',
            'billing-last_name' => 'required|string',
            'billing-street' => 'required|string',
            'billing-housenumber' => 'required|numeric',
            'billing-housenumber-add' => 'nullable',
            'billing-postal-zip-code' => 'required|string',
            'billing-city' => 'required|string',
            'billing-country' => 'required|string',
            'billing-phone' => 'nullable|string',
            'billing-company' => 'nullable|string',
            // Shipping (only if alt-shipping is checked)
            'shipping_first_name' => 'required_with:alt-shipping|string|nullable',
            'shipping_last_name' => 'required_with:alt-shipping|string|nullable',
            'shipping_street' => 'required_with:alt-shipping|string|nullable',
            'shipping_housenumber' => 'required_with:alt-shipping|nullable',
            'shipping_housenumber_addition' => 'nullable',
            'shipping_postal-zip-code' => 'required_with:alt-shipping|string|nullable',
            'shipping_city' => 'required_with:alt-shipping|string|nullable',
            'shipping_country' => 'required_with:alt-shipping|string|nullable',
            'shipping_phone' => 'nullable|string',
            'shipping_company' => 'nullable|string',
        ];


        $messages = [
            // Billing
            'billing-email.required' => 'E-mailadres is verplicht.',
            'billing-email.email' => 'Vul een geldig e-mailadres in.',
            'billing-first_name.required' => 'Voornaam is verplicht.',
            'billing-last_name.required' => 'Achternaam is verplicht.',
            'billing-street.required' => 'Straatnaam is verplicht.',
            'billing-housenumber.required' => 'Huisnummer is verplicht.',
            'billing-housenumber.numeric' => 'Huisnummer moet een getal zijn.',
            'billing-postal-zip-code.required' => 'Postcode is verplicht.',
            'billing-city.required' => 'Plaats is verplicht.',
            'billing-country.required' => 'Land is verplicht.',
            // Shipping
            'shipping_first_name.required_with' => 'Voornaam is verplicht.',
            'shipping_last_name.required_with' => 'Achternaam is verplicht.',
            'shipping_street.required_with' => 'Straatnaam is verplicht.',
            'shipping_housenumber.required_with' => 'Huisnummer is verplicht.',
            'shipping_postal-zip-code.required_with' => 'Postcode is verplicht.',
            'shipping_city.required_with' => 'Plaats is verplicht.',
            'shipping_country.required_with' => 'Land is verplicht.',
            'shipping_phone.required_with' => 'Telefoonnummer voor verzending is verplicht.',
        ];
        $validated = $request->validate($rules, $messages);

        // Prepare customer data
        $customerData = [
            'billing_first_name' => $request->input('billing-first_name'),
            'billing_last_name' => $request->input('billing-last_name'),
            'billing_email' => $request->input('billing-email'),
            'billing_company' => $request->input('billing-company'),
            'billing_street' => $request->input('billing-street'),
            'billing_house_number' => $request->input('billing-housenumber'),
            'billing_house_number_addition' => $request->input('billing-housenumber-add'),
            'billing_postal_code' => $request->input('billing-postal-zip-code'),
            'billing_city' => $request->input('billing-city'),
            'billing_country' => $request->input('billing-country'),
            'billing_phone' => $request->input('billing-phone'),
        ];

        // If alternate shipping is checked, add shipping fields
        if ($request->has('alt-shipping')) {
            $customerData = array_merge($customerData, [
                'shipping_first_name' => $request->input('shipping_first_name'),
                'shipping_last_name' => $request->input('shipping_last_name'),
                'shipping_company' => $request->input('shipping_company'),
                'shipping_street' => $request->input('shipping_street'),
                'shipping_house_number' => $request->input('shipping_housenumber'),
                'shipping_house_number_addition' => $request->input('shipping_housenumber_addition'),
                'shipping_postal_code' => $request->input('shipping_postal-zip-code'),
                'shipping_city' => $request->input('shipping_city'),
                'shipping_country' => $request->input('shipping_country'),
                'shipping_phone' => $request->input('shipping_phone'),
            ]);
        }

        // Create customer
       $customer = Customer::where('billing_email', $request->input('billing-email'))->first();
        if ($customer) {
            $customer->update($customerData);
        } else {
            $customer = Customer::create($customerData);
        }

        // Calculate order total
        $total = collect($cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });

        // Create order
        $order = $customer->orders()->create([
            'total' => $total,
            'status' => 'pending',
        ]);

        // Create order items
        foreach ($cart as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'product_name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        // Clear cart
        session()->forget('cart');

        return redirect()->route('shop')->with('success', 'Je bestelling is geplaatst!');
    }
}
