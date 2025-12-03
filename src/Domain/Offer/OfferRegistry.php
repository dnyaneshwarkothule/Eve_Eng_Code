<?php

namespace KikiCourier\Domain\Offer;

use KikiCourier\Domain\Package;

class OfferRegistry
{
    /** @var array<string, OfferRule> */
    private array $offers = [];

    /**
     * @param OfferRule[] $rules
     */
    public function __construct(array $rules)
    {
        foreach ($rules as $rule) {
            $this->offers[$rule->getCode()] = $rule;
        }
    }

    public function findApplicableOffer(?string $code, Package $package): ?OfferRule
    {
        if ($code === null || $code === '' || $code === 'NA') {
            return null;
        }

        $rule = $this->offers[$code] ?? null;

        if ($rule === null) {
            return null;
        }

        return $rule->isApplicable($package) ? $rule : null;
    }
}
