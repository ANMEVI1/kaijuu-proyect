<?php
declare(strict_types=1);

namespace App\Application\UseCase\Wallet;

use App\Domain\Wallet\WalletRepositoryInterface;

class ConfirmRecharge {
    public function __construct(private WalletRepositoryInterface $walletRepo) {}

    public function execute(string $referenceCode, string $status = 'completado'): void {
        $tx = $this->walletRepo->findTransactionByReferenceCode($referenceCode);
        if (!$tx) {
            throw new \RuntimeException("Transacción no encontrada con referencia: $referenceCode");
        }
        
        if ($tx->status === 'completado') {
            return; // Ya acreditada
        }

        if ($status === 'completado') {
            $this->walletRepo->credit($tx->walletId, $tx->amountZen);
        }

        $this->walletRepo->updateTransactionStatus($tx->id, $status);
    }
}
