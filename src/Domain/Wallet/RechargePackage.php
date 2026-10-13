<?php
declare(strict_types=1);

namespace App\Domain\Wallet;

class RechargePackage {
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly float  $zenAmount,
        public readonly float  $pricePen,
        public readonly float  $bonusZen,
        public readonly bool   $isActive,
        public readonly bool   $isFeatured,
        public readonly int    $sortOrder,
    ) {}

    public function totalZen(): float {
        return $this->zenAmount + $this->bonusZen;
    }
}
