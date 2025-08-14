<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    public function index()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json;charset=utf-8;version=2.0',
            'User-Agent' => 'MyShop/1.0',
        ])->get('https://api.myparcel.nl/pickup_locations', [
            'platform' => 'myparcel',
            'carrier' => 'postnl',
            'cc' => 'nl',
            'postal_code' => '2132JE',
            'number' => '31',
        ]);

        $locations = optional($response->json())['data']['pickup_locations'] ?? [];

        return response()->json($locations);
    }
}
