<?php

namespace KikiCourier\Service;

use KikiCourier\Domain\Package;
use KikiCourier\Domain\VehicleConfig;

class DeliveryScheduler
{
    public function __construct(
        private readonly VehicleConfig $vehicleConfig
    ) {}

    /**
     * @param Package[] $packages (pricing must already be set)
     */
    public function schedule(array $packages): void
    {
        $remaining = array_values($packages);
        $vehicleTimes = array_fill(0, $this->vehicleConfig->numVehicles, 0.0);

        while (!empty($remaining)) {
            $vehicleIndex = $this->findEarliestAvailableVehicle($vehicleTimes);
            $currentTime = $vehicleTimes[$vehicleIndex];

            // build a simpler array for combination calculation
            $shipmentIndices = $this->chooseBestShipment($remaining);

            $maxDistance = 0.0;

            // deliver packages in this shipment
            // sort indices descending to safely unset
            rsort($shipmentIndices);
            foreach ($shipmentIndices as $idx) {
                /** @var Package $pkg */
                $pkg = $remaining[$idx];
                $oneWay = $this->truncateTo2Decimals($pkg->distanceKm / $this->vehicleConfig->maxSpeedKmph);
                $deliveryTime = $currentTime + $oneWay;
                $pkg->setEstimatedDeliveryTime(round($deliveryTime, 2));

                $maxDistance = max($maxDistance, $pkg->distanceKm);

                unset($remaining[$idx]);
            }

            $remaining = array_values($remaining);

            $maxOneWay = $this->truncateTo2Decimals($maxDistance / $this->vehicleConfig->maxSpeedKmph);
            $vehicleTimes[$vehicleIndex] = $currentTime + 2 * $maxOneWay;
        }
    }

    /**
     * @param Package[] $packages
     * @return int[] indices of selected packages in $packages
     */
    private function chooseBestShipment(array $packages): array
    {
        $n = count($packages);
        $bestIndices = [];
        $bestCount = 0;
        $bestTotalWeight = 0.0;
        $bestMaxDistance = PHP_FLOAT_MAX;

        // brute force all non-empty subsets (n is small in challenge)
        // bit mask from 1 .. (2^n - 1)
        $limit = 1 << $n;

        for ($mask = 1; $mask < $limit; $mask++) {
            $totalWeight = 0.0;
            $count = 0;
            $maxDistance = 0.0;
            $indices = [];

            for ($i = 0; $i < $n; $i++) {
                if ($mask & (1 << $i)) {
                    /** @var Package $pkg */
                    $pkg = $packages[$i];
                    $totalWeight += $pkg->weightKg;
                    if ($totalWeight > $this->vehicleConfig->maxCarriableWeightKg) {
                        // invalid shipment
                        $indices = [];
                        break;
                    }
                    $count++;
                    $indices[] = $i;
                    $maxDistance = max($maxDistance, $pkg->distanceKm);
                }
            }

            if (empty($indices)) {
                continue;
            }

            if ($count > $bestCount) {
                $bestCount = $count;
                $bestTotalWeight = $totalWeight;
                $bestMaxDistance = $maxDistance;
                $bestIndices = $indices;
            } elseif ($count === $bestCount) {
                if ($totalWeight > $bestTotalWeight) {
                    $bestTotalWeight = $totalWeight;
                    $bestMaxDistance = $maxDistance;
                    $bestIndices = $indices;
                } elseif ($totalWeight === $bestTotalWeight && $maxDistance < $bestMaxDistance) {
                    $bestMaxDistance = $maxDistance;
                    $bestIndices = $indices;
                }
            }
        }

        return $bestIndices;
    }

    /**
     * Truncate (not round) to 2 decimal places, as per sample explanation.
     */
    private function truncateTo2Decimals(float $value): float
    {
        return floor($value * 100) / 100;
    }

    /**
     * @param float[] $vehicleTimes
     */
    private function findEarliestAvailableVehicle(array $vehicleTimes): int
    {
        $minIndex = 0;
        $minTime = $vehicleTimes[0];

        foreach ($vehicleTimes as $i => $time) {
            if ($time < $minTime) {
                $minTime = $time;
                $minIndex = $i;
            }
        }

        return $minIndex;
    }
}
