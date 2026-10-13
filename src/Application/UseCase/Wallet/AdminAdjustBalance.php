<?php
declare(strict_types=1);

namespace App\Application\UseCase\Wallet;

use App\Domain\Wallet\WalletRepositoryInterface;

class AdminAdjustBalance {
    public function __construct(private WalletRepositoryInterface $walletRepo) {}

    public function execute(string $userId, float $amountZen, string $direction, string $notes): void {
        $wallet = $this->walletRepo->findByUserId($userId);
        if (!$wallet) {
            $wallet = $this->walletRepo->createForUser($userId);
        }

        $balanceBefore = $wallet->balanceZen;

        if ($direction === 'credito') {
            $wallet = $this->walletRepo->credit($wallet->id, $amountZen);
        } else {
            if (!$wallet->canAfford($amountZen)) {
                throw new \RuntimeException("Saldo insuficiente para debitar.");
            }
            $wallet = $this->walletRepo->debit($wallet->id, $amountZen);
        }

        $this->walletRepo->recordTransaction([
            'wallet_id' => $wallet->id,
            'user_id' => $userId,
            'type' => 'ajuste_admin',
            'amount_zen' => $amountZen,
            'direction' => $direction,
            'balance_before' => $balanceBefore,
            'balance_after' => $wallet->balanceZen,
            'status' => 'completado',
            'notes' => $notes
        ]);
    }
}
