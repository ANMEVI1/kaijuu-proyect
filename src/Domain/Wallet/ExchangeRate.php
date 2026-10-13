<?php
declare(strict_types=1);

namespace App\Domain\Wallet;

class ExchangeRate {
    public function __construct(
        public readonly string $id,
        public readonly string $currency,
        public readonly float  $rate,       // soles por 1 ZEN
        public readonly bool   $isActive,
        public readonly string $createdAt,
    ) {}

    public function fiatToZen(float $fiatAmount): float {
        return round($fiatAmount / $this->rate, 2);
    }

    public function zenToFiat(float $zenAmount): float {
        return round($zenAmount * $this->rate, 2);
    }
}
