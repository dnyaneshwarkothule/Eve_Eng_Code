<?php

use KikiCourier\Domain\Package;
use KikiCourier\Domain\VehicleConfig;
use KikiCourier\Service\DeliveryScheduler;
use PHPUnit\Framework\TestCase;

class DeliverySchedulerTest extends TestCase
{
    public function testSampleScheduleTimes()
    {
        $packages = [
            new Package('PKG1', 50, 30, null),
            new Package('PKG2', 75, 125, null),
            new Package('PKG3', 175, 100, null),
            new Package('PKG4', 110, 60, null),
            new Package('PKG5', 155, 95, null),
        ];

        $config = new VehicleConfig(2, 70, 200);
        $scheduler = new DeliveryScheduler($config);
        $scheduler->schedule($packages);

        // map id->time
        $times = [];
        foreach ($packages as $p) {
            $times[$p->id] = $p->getEstimatedDeliveryTime();
        }

        $this->assertEquals(3.98, $times['PKG1']);
        $this->assertEquals(1.78, $times['PKG2']);
        $this->assertEquals(1.42, $times['PKG3']);
        $this->assertEquals(0.85, $times['PKG4']);
        $this->assertEquals(4.19, $times['PKG5']);
    }
}
