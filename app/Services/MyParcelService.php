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
   * Create a shipment concept in MyParcel
   */
  public function createShipment(array $shipping): array
  {
    if (empty($this->apiKey)) {
      Log::error('MyParcel API key missing; cannot create shipment');
      throw new \RuntimeException('MyParcel API key ontbreekt');
    }

    $addr  = $shipping['address']  ?? [];
    $deliv = $shipping['delivery'] ?? [];

    $carrierName = strtolower((string)($shipping['carrier'] ?? $deliv['carrier'] ?? 'postnl'));
    $carrierId   = self::CARRIER_IDS[$carrierName] ?? self::CARRIER_IDS['postnl'];

    $postal = preg_replace('/\s+/', '', (string) ($addr['postalCode'] ?? ''));

    // ✅ Build fullStreet safely
    $street = trim((string)($addr['street'] ?? ''));
    $nr     = trim((string)($addr['number'] ?? ''));
    $add    = trim((string)($addr['addition'] ?? ''));

    if ($street === '' || $nr === '') {
      throw new \RuntimeException('Adres onvolledig: straat en huisnummer zijn verplicht voor MyParcel');
    }

    $fullStreet = trim($street . ' ' . $nr . ($add ? ' ' . $add : ''));

    try {
      // --- Build consignment
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

      // --- Shipping options
      $consignment->setPackageType((int) ($deliv['packageTypeId'] ?? 1));
      $consignment->setOnlyRecipient(!empty($deliv['onlyRecipient']));
      $consignment->setSignature(!empty($deliv['signature']));
      if (!empty($deliv['insurance'])) {
        $consignment->setInsurance((int) $deliv['insurance']);
      }

      // --- Delivery type
      $deliveryTypeName = strtolower((string) ($deliv['deliveryType'] ?? 'standard'));
      $deliveryTypeId   = self::DELIVERY_TYPE_IDS[$deliveryTypeName] ?? self::DELIVERY_TYPE_IDS['standard'];
      $consignment->setDeliveryType($deliveryTypeId);

      // --- Handle pickup (if present)
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

      // --- Create concept
      $collection = (new MyParcelCollection())
        ->addConsignment($consignment)
        ->createConcepts();

      $first = $collection->first();
      $consignmentId = $first ? $first->getConsignmentId() : null;

      Log::debug('MyParcel raw response', [
        'consignment_id' => $consignmentId,
        'collection'     => $collection->toArray(),
      ]);

      if (!$consignmentId) {
        throw new \RuntimeException('MyParcel kon geen consignment_id aanmaken. Zie logs voor details.');
      }

      // Fetch track & trace
      $collection->fetchTrackTraceData();
      $trackTraceUrl = $first->getTrackTraceUrl();

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
      throw $e;
    }
  }

  /**
   * Save a PDF label from MyParcel
   */
  public function saveLabelPdf(string $labelLink, ?string $path = null): string
  {
    $path ??= 'labels/' . date('Y/m') . '/label_' . uniqid() . '.pdf';
    $pdf = Http::get($labelLink)->body();
    Storage::put($path, $pdf);
    return $path;
  }
}
