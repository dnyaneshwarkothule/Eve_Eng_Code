<?php

namespace KikiCourier\Domain\Offer;

use KikiCourier\Domain\Package;

class OfferOFR003 implements OfferRule
{
    public function getCode(): string
    {
        return 'OFR003';
    }

    public function getDiscountPercent(): float
    {
        return 5.0;
    }

    public function isApplicable(Package $package): bool
    {
        return $package->distanceKm >= 50
            && $package->distanceKm <= 250
            && $package->weightKg >= 10
            && $package->weightKg <= 150;
    }
}
