<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use MyParcelNL\Sdk\Helper\MyParcelCollection;
use MyParcelNL\Sdk\Factory\ConsignmentFactory;

class MyParcelService
{
    private string $apiKey;

    // Gebruik numerieke ID's (werken in alle SDK versies)
    private const CARRIER_IDS = [
        'postnl' => 1,
        'bpost'  => 2,
        'dpd'    => 4,
    ];

    private const DELIVERY_TYPE_IDS = [
        'morning'  => 1,
        'standard' => 2,
        'evening'  => 3,
        'pickup'   => 4,
    ];

    public function __construct()
    {
        $this->apiKey = (string) config('myparcel.api_key');
    }

    /**
     * $shipping:
     * - carrier: postnl|bpost|dpd
     * - address: cc, name, company, email, phone, fullStreet, postalCode, city
     * - delivery: packageTypeId, onlyRecipient, signature, insurance,
     *             deliveryType: standard|morning|evening|pickup,
     *             is_pickup, pickup{...}
     */
    public function createShipment(array $shipping): array
    {
        if (empty($this->apiKey)) {
            Log::error('MyParcel API key missing; cannot create shipment');
            return ['error' => 'missing_api_key'];
        }

        Log::info('MyParcel fullStreet debug', [
            'fullStreet' => $shipping['address']['fullStreet'] ?? null,
            'address'    => $shipping['address'] ?? [],
        ]);

        $addr  = $shipping['address']  ?? [];
        $deliv = $shipping['delivery'] ?? [];

        // --- Carrier kiezen m.b.v. numerieke ID's
        $carrierName = strtolower((string)($shipping['carrier'] ?? $deliv['carrier'] ?? 'postnl'));
        $carrierId   = self::CARRIER_IDS[$carrierName] ?? self::CARRIER_IDS['postnl'];

        $postal     = preg_replace('/\s+/', '', (string) ($addr['postalCode'] ?? ''));
        $fullStreet = trim((string) ($addr['fullStreet'] ?? ''));

        try {
            Log::info('MyParcel SDK: create consignment', [
                'carrier' => $carrierName,
                'carrier_id' => $carrierId,
                'reference' => $shipping['reference'] ?? null,
                'country' => $addr['cc'] ?? null,
                'postal' => $postal,
                'city' => $addr['city'] ?? null,
                'delivery' => $deliv,
            ]);

            // 1) Consignment
            $consignment = (ConsignmentFactory::createByCarrierId($carrierId))
                ->setApiKey($this->apiKey)
                ->setReferenceIdentifier($shipping['reference'] ?? ('order-' . ($shipping['order_id'] ?? '')))
                ->setCountry((string) ($addr['cc'] ?? 'NL'))
                ->setPerson((string) ($addr['name'] ?? ''))
                ->setCompany($addr['company'] ?? null)
                ->setEmail($addr['email'] ?? null)
                ->setPhone($addr['phone'] ?? null)
                ->setFullStreet($fullStreet)
                ->setPostalCode($postal)
                ->setCity((string) ($addr['city'] ?? ''))
                ->setLabelDescription('Bestelling nr: ' . ($shipping['order_id'] ?? ''));

            // 2) Verzendopties
            if (!empty($deliv['packageTypeId'])) {
                // 1=package, 2=mailbox, 3=letter, 4=digital_stamp
                $consignment->setPackageType((int) $deliv['packageTypeId']);
            }
            if (!empty($deliv['onlyRecipient'])) {
                $consignment->setOnlyRecipient(true);
            }
            if (!empty($deliv['signature'])) {
                $consignment->setSignature(true);
            }
            if (!empty($deliv['insurance'])) {
                $consignment->setOnlyRecipient(true)
                            ->setSignature(true)
                            ->setInsurance((int) $deliv['insurance']);
            }

            // 3) Delivery type + Pickup
            $deliveryTypeName = strtolower((string) ($deliv['deliveryType'] ?? 'standard'));
            $deliveryTypeId   = self::DELIVERY_TYPE_IDS[$deliveryTypeName] ?? self::DELIVERY_TYPE_IDS['standard'];
            $consignment->setDeliveryType($deliveryTypeId);

            if (!empty($deliv['is_pickup']) && !empty($deliv['pickup']) && is_array($deliv['pickup'])) {
                $p = $deliv['pickup'];
                $consignment
                    ->setPickupLocationName($p['locationName'] ?? $p['name'] ?? '')
                    ->setPickupStreet($p['street'] ?? '')
                    ->setPickupNumber((string)($p['number'] ?? ''))
                    ->setPickupPostalCode(preg_replace('/\s+/', '', (string)($p['postalCode'] ?? '')))
                    ->setPickupCity($p['city'] ?? '')
                    ->setSignature(true)
                    ->setDeliveryType(self::DELIVERY_TYPE_IDS['pickup']);

                // Optional but required by API for pickup consignments: pickup country and retail network id
                // Some SDK versions expose setters; guard their existence
                $pickupCc = (string) ($p['cc'] ?? $addr['cc'] ?? 'NL');
                $retailNetworkId = (string) ($p['retail_network_id'] ?? (($carrierName === 'postnl') ? 'PNPNL-01' : ''));
                try {
                    if (!empty($pickupCc) && method_exists($consignment, 'setPickupCountry')) {
                        $consignment->setPickupCountry($pickupCc);
                    }
                } catch (\Throwable $e) {
                    Log::debug('MyParcel SDK: setPickupCountry not available or failed', ['e' => $e->getMessage()]);
                }
                try {
                    if (!empty($retailNetworkId) && method_exists($consignment, 'setRetailNetworkId')) {
                        $consignment->setRetailNetworkId($retailNetworkId);
                    }
                } catch (\Throwable $e) {
                    Log::debug('MyParcel SDK: setRetailNetworkId not available or failed', ['e' => $e->getMessage()]);
                }
                try {
                    if (!empty($p['location_code']) && method_exists($consignment, 'setPickupLocationCode')) {
                        $consignment->setPickupLocationCode((string) $p['location_code']);
                    }
                } catch (\Throwable $e) {
                    Log::debug('MyParcel SDK: setPickupLocationCode not available or failed', ['e' => $e->getMessage()]);
                }
            }

            // 4) Aanmaken + data ophalen
            $collection = (new MyParcelCollection())
                ->addConsignment($consignment)
                ->createConcepts();

            $first          = $collection->first();
            $consignmentId  = $first->getConsignmentId();

            $collection->fetchTrackTraceData();
            $trackTraceUrl  = $first->getTrackTraceUrl();

            Log::info('MyParcel SDK: concept created', [
                'consignment_id' => $consignmentId,
                'track_trace_url' => $trackTraceUrl,
            ]);

            // Geen label aanmaken: zending blijft concept in MyParcel
            return [
                'consignment_id'  => $consignmentId,
                'track_trace_url' => $trackTraceUrl,
                'label_link'      => null,
            ];
        } catch (\Throwable $e) {
            Log::error('MyParcel SDK error', [
                'message' => $e->getMessage(),
                'trace'   => substr($e->getTraceAsString(), 0, 2000),
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    public function saveLabelPdf(string $labelLink, ?string $path = null): string
    {
        $path ??= 'labels/' . date('Y/m') . '/label_' . uniqid() . '.pdf';
        $pdf = Http::get($labelLink)->body();
        Storage::put($path, $pdf);
        return $path;
    }
}
