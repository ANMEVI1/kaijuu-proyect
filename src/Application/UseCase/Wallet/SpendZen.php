<?php
declare(strict_types=1);

namespace App\Application\UseCase\Wallet;

use App\Domain\Wallet\WalletRepositoryInterface;

class SpendZen {
    public function __construct(
        private WalletRepositoryInterface $walletRepo
    ) {}

    public function execute(string $userId, float $amountZen, ?string $orderId = null): void {
        $wallet = $this->walletRepo->findByUserId($userId);
        if (!$wallet || !$wallet->canAfford($amountZen)) {
            throw new \RuntimeException("Saldo insuficiente.");
        }

        $balanceBefore = $wallet->balanceZen;
        $wallet = $this->walletRepo->debit($wallet->id, $amountZen);

        $this->walletRepo->recordTransaction([
            'wallet_id' => $wallet->id,
            'user_id' => $userId,
            'type' => 'compra',
            'amount_zen' => $amountZen,
            'direction' => 'debito',
            'balance_before' => $balanceBefore,
            'balance_after' => $wallet->balanceZen,
            'order_id' => $orderId,
            'status' => 'completado',
            'notes' => 'Pago de orden'
        ]);
    }
}
