<?php

namespace KikiCourier\Domain\Offer;

use KikiCourier\Domain\Package;

class OfferOFR001 implements OfferRule
{
    public function getCode(): string
    {
        return 'OFR001';
    }

    public function getDiscountPercent(): float
    {
        return 10.0;
    }

    public function isApplicable(Package $package): bool
    {
        return $package->distanceKm > 0
            && $package->distanceKm <= 200
            && $package->weightKg >= 70
            && $package->weightKg <= 200;
    }
}
