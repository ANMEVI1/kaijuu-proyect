<?php
declare(strict_types=1);

namespace App\Application\UseCase\Wallet;

use App\Domain\Wallet\WalletRepositoryInterface;
use App\Domain\Wallet\ExchangeRateRepositoryInterface;
use App\Domain\Wallet\RechargePackageRepositoryInterface;

class InitiateRecharge {
    public function __construct(
        private WalletRepositoryInterface $walletRepo,
        private ExchangeRateRepositoryInterface $rateRepo,
        private RechargePackageRepositoryInterface $packageRepo
    ) {}

    public function execute(string $userId, string $packageId, string $paymentMethod): array {
        $wallet = $this->walletRepo->findByUserId($userId);
        if (!$wallet) {
            $wallet = $this->walletRepo->createForUser($userId);
        }

        $package = $this->packageRepo->findById($packageId);
        if (!$package || !$package->isActive) {
            throw new \RuntimeException("Paquete no disponible.");
        }

        $rate = $this->rateRepo->getActive();
        if (!$rate) {
            throw new \RuntimeException("Tipo de cambio no configurado.");
        }

        // Crear transacción pendiente
        $tx = $this->walletRepo->recordTransaction([
            'wallet_id' => $wallet->id,
            'user_id' => $userId,
            'type' => 'recarga',
            'amount_zen' => $package->totalZen(),
            'direction' => 'credito',
            'balance_before' => $wallet->balanceZen,
            'balance_after' => $wallet->balanceZen, // Pendiente, no suma aún
            'exchange_rate_id' => $rate->id,
            'amount_fiat' => $package->pricePen,
            'currency' => 'PEN',
            'reference_code' => uniqid('rech_'),
            'status' => 'pendiente',
            'notes' => "Iniciado recarga paquete: {$package->name}"
        ]);

        return [
            'transaction_id' => $tx->id,
            'reference_code' => $tx->referenceCode,
            'amount_fiat' => $tx->amountFiat,
            'currency' => $tx->currency,
            'payment_method' => $paymentMethod
        ];
    }
}
