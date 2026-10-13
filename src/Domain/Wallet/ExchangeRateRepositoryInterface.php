<?php
declare(strict_types=1);

namespace App\Domain\Wallet;

interface ExchangeRateRepositoryInterface {
    public function getActive(string $currency = 'PEN'): ?ExchangeRate;
    public function getHistory(string $currency = 'PEN'): array;
    public function setRate(string $currency, float $rate, string $setBy): ExchangeRate;
}
