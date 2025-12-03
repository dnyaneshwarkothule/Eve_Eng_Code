<?php

use KikiCourier\Domain\Offer\OfferOFR001;
use KikiCourier\Domain\Offer\OfferOFR002;
use KikiCourier\Domain\Offer\OfferOFR003;
use KikiCourier\Domain\Offer\OfferRegistry;
use KikiCourier\Domain\Package;
use KikiCourier\Service\CostEstimator;
use PHPUnit\Framework\TestCase;

class CostEstimatorTest extends TestCase
{
    public function testSample1Package3()
    {
        $registry = new OfferRegistry([
            new OfferOFR001(),
            new OfferOFR002(),
            new OfferOFR003(),
        ]);

        $estimator = new CostEstimator(100, $registry);
        $pkg = new Package('PKG3', 10, 100, 'OFR003');

        $estimator->estimate($pkg);

        $this->assertSame(700.0, $pkg->getDeliveryCost());
        $this->assertSame(35.0, $pkg->getDiscount());
        $this->assertSame(665.0, $pkg->getTotalCost());
    }
}
