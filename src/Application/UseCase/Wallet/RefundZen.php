<?php
declare(strict_types=1);

namespace App\Application\UseCase\Wallet;

use App\Domain\Wallet\WalletRepositoryInterface;

class RefundZen {
    public function __construct(private WalletRepositoryInterface $walletRepo) {}

    public function execute(string $orderId): void {
        throw new \RuntimeException("Refund logic not fully implemented yet");
    }
}
