<?php

namespace KikiCourier\Service;

use KikiCourier\Domain\Offer\OfferRegistry;
use KikiCourier\Domain\Package;

class CostEstimator
{
    public function __construct(
        private readonly float $baseDeliveryCost,
        private readonly OfferRegistry $offerRegistry,
    ) {
    }

    public function estimate(Package $package): void
    {
        // base_delivery_cost + weight * 10 + distance * 5
        $deliveryCost = $this->baseDeliveryCost
            + $package->weightKg * 10
            + $package->distanceKm * 5;

        $offer = $this->offerRegistry->findApplicableOffer($package->offerCode, $package);

        $discount = 0.0;

        if ($offer !== null) {
            $discount = ($offer->getDiscountPercent() / 100.0) * $deliveryCost;
        }

        $discount = round($discount, 2);
        $total = round($deliveryCost - $discount, 2);

        $package->setPricing($deliveryCost, $discount, $total);
    }
}
