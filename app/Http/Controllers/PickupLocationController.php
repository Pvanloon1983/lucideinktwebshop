<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PickupLocationController extends Controller
{
    // Public endpoint proxy: /pickup-locations?cc=BE&postalCode=2000&number=5[&carrier=postnl|bpost|dpd][&limit=50]
    public function index(Request $request)
    {
        $cc = strtoupper(trim((string) $request->input('cc', 'NL')));
        $postal = strtoupper(preg_replace('/\s+/', '', (string) $request->input('postalCode', '')));
        $number = trim((string) $request->input('number', ''));
        $limit = (int) $request->input('limit', 60);
        if ($limit <= 0) {
            $limit = 60;
        } elseif ($limit > 200) {
            $limit = 200;
        }

        // Map numeric carrier ids to names
        $rawCarrier = trim((string) $request->input('carrier', ''));
        $carrierMap = ['1' => 'postnl', '2' => 'bpost', '4' => 'dpd'];
        if (isset($carrierMap[$rawCarrier])) {
            $rawCarrier = $carrierMap[$rawCarrier];
        }
        $requestedCarrier = strtolower($rawCarrier);
        if (!in_array($requestedCarrier, ['postnl', 'bpost', 'dpd'])) {
            $requestedCarrier = '';
        }

        if ($postal === '' || $number === '') {
            return response()->json(['locations' => []], 200);
        }

        // Build carrier attempt order
        if ($requestedCarrier) {
            $carriers = [$requestedCarrier];
        } else {
            // Country–specific preference ordering; fallback: try all
            if ($cc === 'BE') {
                $carriers = ['bpost', 'postnl', 'dpd'];
            } elseif ($cc === 'NL') {
                $carriers = ['postnl', 'dpd', 'bpost'];
            } else {
                $carriers = ['postnl', 'bpost', 'dpd'];
            }
        }

        $seen = [];
        $all = [];
        foreach ($carriers as $carrier) {
            $params = [
                'platform' => ($cc === 'BE') ? 'sendmyparcel' : 'myparcel',
                'carrier' => $carrier,
                'cc' => strtoupper($cc),
                'postal_code' => $postal,
                'number' => $number,
            ];
            try {
                $resp = Http::withHeaders([
                    'Accept' => 'application/json;charset=utf-8;version=2.0',
                    'User-Agent' => 'LucideInktWebshop/1.0',
                ])->get('https://api.myparcel.nl/pickup_locations', $params);

                if (!$resp->ok()) {
                    Log::warning('Pickup locations non-200', [
                        'carrier' => $carrier,
                        'cc' => $cc,
                        'status' => $resp->status(),
                        'body' => substr($resp->body(), 0, 400)
                    ]);
                    continue;
                }
                $json = $resp->json();
                $raw = $json['data']['pickup_locations']
                    ?? $json['data']['pickup']
                    ?? $json['pickup_locations']
                    ?? $json['pickup']
                    ?? [];

                foreach ((array) $raw as $p) {
                    $addr = $p['address'] ?? [];
                    $loc = $p['location'] ?? [];
                    $locationCode = (string) ($loc['location_code'] ?? $p['location_code'] ?? '');
                    $retailNet = (string) ($loc['retail_network_id'] ?? $p['retail_network_id'] ?? '');
                    $key = strtolower($carrier.'|'.$locationCode.'|'.$retailNet);
                    if ($key === '||') {
                        continue;
                    } // skip empties
                    if (isset($seen[$key])) {
                        continue;
                    }
                    $seen[$key] = true;
                    $all[] = [
                        'locationName' => $loc['location_name'] ?? $loc['locationName'] ?? $p['locationName'] ?? $p['name'] ?? '',
                        'street' => $addr['street'] ?? $p['street'] ?? '',
                        'number' => (string) ($addr['number'] ?? $p['number'] ?? ''),
                        'numberSuffix' => (string) ($addr['number_suffix'] ?? $p['numberSuffix'] ?? ''),
                        'postalCode' => strtoupper(preg_replace('/\s+/', '',
                            (string) ($addr['postal_code'] ?? $p['postalCode'] ?? ''))),
                        'city' => $addr['city'] ?? $p['city'] ?? '',
                        'cc' => strtoupper((string) ($addr['cc'] ?? $p['cc'] ?? $cc)),
                        'retail_network_id' => $retailNet,
                        'location_code' => $locationCode,
                        'carrier' => $carrier,
                    ];
                    if (count($all) >= $limit) {
                        break 2;
                    } // stop if reached limit
                }
            } catch (\Throwable $e) {
                Log::error('Pickup locations fetch exception', [
                    'carrier' => $carrier,
                    'cc' => $cc,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }
        }

        if (empty($all)) {
            Log::info('Pickup locations: zero results', ['cc' => $cc, 'postal' => $postal, 'number' => $number]);
        }

        return response()->json(['locations' => array_values($all)], 200);
    }
}
