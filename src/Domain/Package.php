<?php

namespace KikiCourier\Domain;

class Package
{
    public function __construct(
        public readonly string $id,
        public readonly float $weightKg,
        public readonly float $distanceKm,
        public readonly ?string $offerCode,
    ) {}

    private float $deliveryCost = 0.0;
    private float $discount = 0.0;
    private float $totalCost = 0.0;
    private ?float $estimatedDeliveryTimeHours = null;

    public function setPricing(float $deliveryCost, float $discount, float $totalCost): void
    {
        $this->deliveryCost = $deliveryCost;
        $this->discount = $discount;
        $this->totalCost = $totalCost;
    }

    public function setEstimatedDeliveryTime(float $hours): void
    {
        $this->estimatedDeliveryTimeHours = $hours;
    }

    public function getDeliveryCost(): float
    {
        return $this->deliveryCost;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function getTotalCost(): float
    {
        return $this->totalCost;
    }

    public function getEstimatedDeliveryTime(): ?float
    {
        return $this->estimatedDeliveryTimeHours;
    }
}
