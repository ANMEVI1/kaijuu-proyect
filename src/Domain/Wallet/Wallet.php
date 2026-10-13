<?php
declare(strict_types=1);

namespace App\Domain\Wallet;

class Wallet {
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly float  $balanceZen,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    public function canAfford(float $amount): bool {
        return $this->balanceZen >= $amount;
    }
}
