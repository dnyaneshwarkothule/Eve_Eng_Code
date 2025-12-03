<?php

namespace KikiCourier\Domain\Offer;

use KikiCourier\Domain\Package;

interface OfferRule
{
    public function getCode(): string;
    public function getDiscountPercent(): float;
    public function isApplicable(Package $package): bool;
}
