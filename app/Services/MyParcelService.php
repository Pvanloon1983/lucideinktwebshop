<?php

namespace App\Services;

use Illuminate\Support\Facades\{Http, Log, Storage};
use MyParcelNL\Sdk\Helper\MyParcelCollection;
use MyParcelNL\Sdk\Factory\ConsignmentFactory;

class MyParcelService
{
    private string $apiKey;

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
     * Haal consignment collectie op via consignment_id
     */
    public function findConsignmentById(int $consignmentId): ?MyParcelCollection
    {
        if (empty($this->apiKey)) {
            Log::error('MyParcel: API key ontbreekt');
            return null;
        }

        $collection = new MyParcelCollection();

        $consignment = ConsignmentFactory::createByCarrierId(self::CARRIER_IDS['postnl'])
            ->setApiKey($this->apiKey)
            ->setConsignmentId($consignmentId);

        $collection->addConsignment($consignment);
        $collection->getConsignments();

        return $collection;
    }

    /**
     * Verwijder consignment bij MyParcel (alleen als het een concept is)
     */
    public function deleteConsignmentById(int $consignmentId): bool
    {
        if (empty($this->apiKey)) {
            Log::error('MyParcel: API key ontbreekt; kan consignment niet verwijderen');
            return false;
        }

        try {
            // Maak collectie met dit consignment
            $collection = (new MyParcelCollection())
                ->addConsignmentByConsignmentIds([$consignmentId], $this->apiKey);

            // Alleen concepten verwijderen (geen barcode == concept)
            foreach ($collection as $consignment) {
                if (empty($consignment->getBarcode())) {
                    $collection->deleteConcepts();

                    Log::info("MyParcel: consignment {$consignmentId} verwijderd (concept)");
                    return true;
                }
            }

            Log::warning("MyParcel: consignment {$consignmentId} niet verwijderd (heeft al barcode)");
            return false;

        } catch (\Throwable $e) {
            Log::error("MyParcel: exception bij verwijderen consignment {$consignmentId}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function generateLabel(int $consignmentId): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('MyParcel API key ontbreekt');
        }

        try {
            $collection = (new MyParcelCollection())
                ->addConsignmentByConsignmentIds([$consignmentId], $this->apiKey)
                ->setLinkOfLabels()
                ->fetchTrackTraceData(); // ⬅️ barcode + tracktrace ophalen

            $consignment = $collection->getOneConsignment();

            $labelLink = $collection->getLinkOfLabels();
            $trackTraceUrl = $consignment->getTrackTraceUrl();
            $barcode = $consignment->getBarcode();

            return [
                'label_link'      => $labelLink,
                'track_trace_url' => $trackTraceUrl,
                'barcode'         => $barcode,
            ];
        } catch (\Throwable $e) {
            \Log::error("MyParcel: label genereren mislukt", [
                'consignment_id' => $consignmentId,
                'error'          => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Maak een nieuwe shipment (concept) aan
     */
    public function createShipment(array $shipping): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('MyParcel API key ontbreekt');
        }

        $addr  = $shipping['address']  ?? [];
        $deliv = $shipping['delivery'] ?? [];

        $carrierName = strtolower((string)($shipping['carrier'] ?? $deliv['carrier'] ?? 'postnl'));
        $carrierId   = self::CARRIER_IDS[$carrierName] ?? self::CARRIER_IDS['postnl'];

        $postal = preg_replace('/\s+/', '', (string) ($addr['postalCode'] ?? ''));
        $street = trim((string)($addr['street'] ?? ''));
        $nr     = trim((string)($addr['number'] ?? ''));
        $add    = trim((string)($addr['addition'] ?? ''));

        if ($street === '' || $nr === '') {
            throw new \RuntimeException('Adres onvolledig: straat en huisnummer verplicht');
        }

        $fullStreet = trim($street . ' ' . $nr . ($add ? ' ' . $add : ''));

        $consignment = ConsignmentFactory::createByCarrierId($carrierId)
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

        // Shipping options
        $consignment->setPackageType((int) ($deliv['packageTypeId'] ?? 1));
        $consignment->setOnlyRecipient(!empty($deliv['onlyRecipient']));
        $consignment->setSignature(!empty($deliv['signature']));
        if (!empty($deliv['insurance'])) {
            $consignment->setInsurance((int) $deliv['insurance']);
        }

        // Delivery type
        $deliveryTypeName = strtolower((string) ($deliv['deliveryType'] ?? 'standard'));
        $deliveryTypeId   = self::DELIVERY_TYPE_IDS[$deliveryTypeName] ?? self::DELIVERY_TYPE_IDS['standard'];
        $consignment->setDeliveryType($deliveryTypeId);

        // Pickup
        $isPickup   = !empty($deliv['is_pickup']) || !empty($deliv['isPickup']) || $deliveryTypeName === 'pickup';
        $pickupData = $deliv['pickup'] ?? $deliv['pickupLocation'] ?? null;

        if ($isPickup && is_array($pickupData)) {
            $consignment
                ->setPickupLocationName($pickupData['locationName'] ?? $pickupData['name'] ?? '')
                ->setPickupStreet($pickupData['street'] ?? '')
                ->setPickupNumber((string)($pickupData['number'] ?? ''))
                ->setPickupPostalCode(preg_replace('/\s+/', '', (string)($pickupData['postalCode'] ?? '')))
                ->setPickupCity($pickupData['city'] ?? '')
                ->setPickupCountry($pickupData['cc'] ?? $pickupData['country'] ?? 'NL')
                ->setRetailNetworkId($pickupData['retail_network_id'] ?? $pickupData['retailNetworkId'] ?? 'PNPNL-01')
                ->setPickupLocationCode($pickupData['location_code'] ?? $pickupData['locationCode'] ?? '')
                ->setDeliveryType(self::DELIVERY_TYPE_IDS['pickup']);
        }

        $collection = (new MyParcelCollection())
            ->addConsignment($consignment)
            ->createConcepts();

        $first = $collection->first();
        $consignmentId = $first ? $first->getConsignmentId() : null;

        if (!$consignmentId) {
            throw new \RuntimeException('MyParcel kon geen consignment_id aanmaken');
        }

        $collection->fetchTrackTraceData();
        $trackTraceUrl = $first->getTrackTraceUrl();

        return [
            'consignment_id'  => $consignmentId,
            'track_trace_url' => $trackTraceUrl,
            'label_link'      => null,
        ];
    }

    /**
     * Label downloaden en opslaan
     */
    public function saveLabelPdf(string $labelLink, ?string $path = null): string
    {
        $path ??= 'labels/' . date('Y/m') . '/label_' . uniqid() . '.pdf';
        $pdf = Http::get($labelLink)->body();
        Storage::put($path, $pdf);
        return $path;
    }
}
