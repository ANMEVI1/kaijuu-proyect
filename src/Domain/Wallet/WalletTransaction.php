<?php
declare(strict_types=1);

namespace App\Domain\Wallet;

class WalletTransaction {
    public function __construct(
        public readonly string  $id,
        public readonly string  $walletId,
        public readonly string  $userId,
        public readonly string  $type,       // recarga|compra|reembolso|ajuste_admin|bono
        public readonly float   $amountZen,
        public readonly string  $direction,  // credito|debito
        public readonly float   $balanceBefore,
        public readonly float   $balanceAfter,
        public readonly ?string $exchangeRateId,
        public readonly ?float  $amountFiat,
        public readonly string  $currency,
        public readonly ?string $orderId,
        public readonly ?string $referenceCode,
        public readonly string  $status,
        public readonly ?string $notes,
        public readonly string  $createdAt,
    ) {}
}
