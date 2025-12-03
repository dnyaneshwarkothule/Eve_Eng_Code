#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use KikiCourier\Domain\Offer\OfferOFR001;
use KikiCourier\Domain\Offer\OfferOFR002;
use KikiCourier\Domain\Offer\OfferOFR003;
use KikiCourier\Domain\Offer\OfferRegistry;
use KikiCourier\Domain\Package;
use KikiCourier\Domain\VehicleConfig;
use KikiCourier\Service\CostEstimator;
use KikiCourier\Service\DeliveryScheduler;

/**
 * Format numbers:
 * - no trailing .00 if it's an integer
 * - otherwise up to 2 decimals
 */
function formatNumber(float $n): string
{
    if (abs($n - (int) $n) < 0.00001) {
        return (string) (int) $n;
    }

    return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
}

// ---------- Read input from STDIN ----------

$lines = [];
while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line !== '') {
        $lines[] = $line;
    }
}

if (count($lines) < 2) {
    fwrite(STDERR, "Insufficient input\n");
    exit(1);
}

// ---------- First line: base_delivery_cost no_of_packages ----------

[$baseCostStr, $numPackagesStr] = preg_split('/\s+/', $lines[0]);
$baseCost = (float) $baseCostStr;
$numPackages = (int) $numPackagesStr;

// ---------- Next N lines: packages ----------

$packages = [];
for ($i = 0; $i < $numPackages; $i++) {
    $parts = preg_split('/\s+/', $lines[$i + 1]);

    // Expect: pkg_id weight distance offer_code
    [$id, $w, $d, $code] = $parts;

    $packages[] = new Package(
        $id,
        (float) $w,
        (float) $d,
        $code === 'NA' ? null : $code
    );
}

// ---------- Offers & cost estimation ----------

$offerRegistry = new OfferRegistry([
    new OfferOFR001(),
    new OfferOFR002(),
    new OfferOFR003(),
]);

$costEstimator = new CostEstimator($baseCost, $offerRegistry);

foreach ($packages as $pkg) {
    $costEstimator->estimate($pkg);
}

// ---------- Problem 2: vehicle line (if present) ----------

$hasDeliveryTime = false;
$vehicleConfig = null;

if (count($lines) > $numPackages + 1) {
    $lastLine = $lines[$numPackages + 1];
    $parts = preg_split('/\s+/', $lastLine);

    if (count($parts) === 3) {
        $hasDeliveryTime = true;
        [$numVehiclesStr, $speedStr, $maxWeightStr] = $parts;

        $vehicleConfig = new VehicleConfig(
            (int) $numVehiclesStr,
            (float) $speedStr,
            (float) $maxWeightStr
        );
    }
}

if ($hasDeliveryTime && $vehicleConfig !== null) {
    $scheduler = new DeliveryScheduler($vehicleConfig);
    $scheduler->schedule($packages);
}

// ---------- Output ----------

foreach ($packages as $pkg) {
    $discount = formatNumber($pkg->getDiscount());
    $total = formatNumber($pkg->getTotalCost());

    if ($hasDeliveryTime) {
        $time = number_format($pkg->getEstimatedDeliveryTime() ?? 0.0, 2, '.', '');
        echo "{$pkg->id} {$discount} {$total} {$time}\n";
    } else {
        echo "{$pkg->id} {$discount} {$total}\n";
    }
}
