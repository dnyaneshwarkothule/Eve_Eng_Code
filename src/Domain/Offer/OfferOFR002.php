<?php

namespace KikiCourier\Domain\Offer;

use KikiCourier\Domain\Package;

class OfferOFR002 implements OfferRule
{
    public function getCode(): string
    {
        return 'OFR002';
    }

    public function getDiscountPercent(): float
    {
        return 7.0;
    }

    public function isApplicable(Package $package): bool
    {
        return $package->distanceKm >= 50
            && $package->distanceKm <= 150
            && $package->weightKg >= 100
            && $package->weightKg <= 250;
    }
}
