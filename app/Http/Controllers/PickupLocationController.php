<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PickupLocationController extends Controller
{
    // Simple proxy to MyParcel public pickup_locations: expects cc, postalCode, number
    public function index(Request $request)
    {
        $cc     = strtoupper(trim((string) $request->input('cc', 'NL')));
        $postal = strtoupper(preg_replace('/\s+/', '', (string) $request->input('postalCode', '')));
        $number = trim((string) $request->input('number', ''));

        if ($postal === '' || $number === '') {
            return response()->json(['locations' => []], 200);
        }

        $params = [
            'platform'    => $cc === 'BE' ? 'sendmyparcel' : 'myparcel',
            'carrier'     => 'postnl',
            'cc'          => strtolower($cc),
            'postal_code' => $postal,
            'number'      => $number,
        ];

        try {
            $resp = Http::withHeaders([
                'Accept'     => 'application/json;charset=utf-8;version=2.0',
                'User-Agent' => 'LucideInktWebshop/1.0',
            ])->get('https://api.myparcel.nl/pickup_locations', $params);

            if (!$resp->ok()) {
                Log::warning('MyParcel pickup simple non-200', ['status' => $resp->status(), 'body' => $resp->body()]);
                return response()->json(['locations' => []], 200);
            }

            $json = $resp->json();
            $raw = $json['data']['pickup_locations']
                ?? $json['data']['pickup']
                ?? $json['pickup_locations']
                ?? $json['pickup']
                ?? [];

            $locations = [];
            foreach ((array) $raw as $p) {
                $addr = $p['address'] ?? [];
                $loc  = $p['location'] ?? [];
                $locations[] = [
                    'locationName'     => $loc['location_name'] ?? $loc['locationName'] ?? $p['locationName'] ?? $p['name'] ?? '',
                    'street'           => $addr['street'] ?? $p['street'] ?? '',
                    'number'           => (string) ($addr['number'] ?? $p['number'] ?? ''),
                    'numberSuffix'     => (string) ($addr['number_suffix'] ?? $p['numberSuffix'] ?? ''),
                    'postalCode'       => strtoupper(preg_replace('/\s+/', '', (string) ($addr['postal_code'] ?? $p['postalCode'] ?? ''))),
                    'city'             => $addr['city'] ?? $p['city'] ?? '',
                    // Extra fields required by MyParcel SDK for pickup shipments
                    'cc'               => strtoupper((string)($addr['cc'] ?? $p['cc'] ?? $cc)),
                    'retail_network_id'=> (string)($loc['retail_network_id'] ?? $p['retail_network_id'] ?? ''),
                    'location_code'    => (string)($loc['location_code'] ?? $p['location_code'] ?? ''),
                ];
            }

            return response()->json(['locations' => $locations], 200);
        } catch (\Throwable $e) {
            Log::error('MyParcel pickup simple error', ['e' => $e->getMessage()]);
            return response()->json(['locations' => []], 200);
        }
    }
}
