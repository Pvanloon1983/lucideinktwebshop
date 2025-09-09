<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductCopyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }
}
