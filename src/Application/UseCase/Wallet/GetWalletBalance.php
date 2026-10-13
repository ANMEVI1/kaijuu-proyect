<?php
declare(strict_types=1);

namespace App\Application\UseCase\Wallet;

use App\Domain\Wallet\Wallet;
use App\Domain\Wallet\WalletRepositoryInterface;

class GetWalletBalance {
    public function __construct(private WalletRepositoryInterface $walletRepo) {}

    public function execute(string $userId): Wallet {
        $wallet = $this->walletRepo->findByUserId($userId);
        if (!$wallet) {
            $wallet = $this->walletRepo->createForUser($userId);
        }
        return $wallet;
    }
}
