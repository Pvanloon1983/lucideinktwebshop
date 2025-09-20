<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $customers = Customer::paginate(10);

        return view('customers.index', ['customers' => $customers]);
    }

    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);

        return view('customers.show', ['customer' => $customer]);
    }
}
