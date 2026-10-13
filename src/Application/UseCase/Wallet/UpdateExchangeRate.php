<?php
declare(strict_types=1);

namespace App\Application\UseCase\Wallet;

use App\Domain\Wallet\ExchangeRateRepositoryInterface;

class UpdateExchangeRate {
    public function __construct(private ExchangeRateRepositoryInterface $rateRepo) {}

    public function execute(string $currency, float $rate, string $setByUserId): void {
        $this->rateRepo->setRate($currency, $rate, $setByUserId);
    }
}
