<?php
declare(strict_types=1);

namespace App\Application\UseCase\Wallet;

use App\Domain\Wallet\ExchangeRate;
use App\Domain\Wallet\ExchangeRateRepositoryInterface;

class GetActiveExchangeRate {
    public function __construct(private ExchangeRateRepositoryInterface $rateRepo) {}

    public function execute(string $currency = 'PEN'): ?ExchangeRate {
        return $this->rateRepo->getActive($currency);
    }
}
