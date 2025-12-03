<?php

namespace KikiCourier\Domain;

class VehicleConfig
{
    public function __construct(
        public readonly int $numVehicles,
        public readonly float $maxSpeedKmph,
        public readonly float $maxCarriableWeightKg,
    ) {
    }
}
