<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Supabase;

use App\Domain\Wallet\ExchangeRate;
use App\Domain\Wallet\ExchangeRateRepositoryInterface;

class SupabaseExchangeRateRepository implements ExchangeRateRepositoryInterface {
    public function __construct(private SupabaseClient $client) {}

    public function getActive(string $currency = 'PEN'): ?ExchangeRate {
        $rows = $this->client->get('zen_exchange_rates', [
            'currency' => "eq.$currency",
            'is_active' => 'eq.true',
            'select' => '*'
        ]);
        return isset($rows[0]) ? $this->hydrate($rows[0]) : null;
    }

    public function getHistory(string $currency = 'PEN'): array {
        $rows = $this->client->get('zen_exchange_rates', [
            'currency' => "eq.$currency",
            'order' => 'created_at.desc'
        ]);
        return array_map(fn($r) => $this->hydrate($r), $rows);
    }

    public function setRate(string $currency, float $rate, string $setBy): ExchangeRate {
        $active = $this->getActive($currency);
        if ($active) {
            $this->client->patch('zen_exchange_rates', $active->id, ['is_active' => false]);
        }
        
        $payload = [
            'currency' => $currency,
            'rate' => $rate,
            'is_active' => true
        ];

        if (preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $setBy)) {
            $payload['set_by'] = $setBy;
        }
        
        $row = $this->client->post('zen_exchange_rates', $payload);
        return $this->hydrate($row);
    }

    private function hydrate(array $r): ExchangeRate {
        return new ExchangeRate(
            id: $r['id'],
            currency: $r['currency'],
            rate: (float)$r['rate'],
            isActive: (bool)$r['is_active'],
            createdAt: $r['created_at']
        );
    }
}
