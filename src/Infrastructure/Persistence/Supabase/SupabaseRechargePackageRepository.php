<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Supabase;

use App\Domain\Wallet\RechargePackage;
use App\Domain\Wallet\RechargePackageRepositoryInterface;

class SupabaseRechargePackageRepository implements RechargePackageRepositoryInterface {
    public function __construct(private SupabaseClient $client) {}

    public function findAllActive(): array {
        $rows = $this->client->get('recharge_packages', [
            'is_active' => 'eq.true',
            'order' => 'sort_order.asc',
            'select' => '*'
        ]);
        return array_map(fn($r) => $this->hydrate($r), $rows);
    }

    public function findById(string $id): ?RechargePackage {
        $rows = $this->client->get('recharge_packages', ['id' => "eq.$id"]);
        return isset($rows[0]) ? $this->hydrate($rows[0]) : null;
    }

    public function create(array $data): RechargePackage {
        $row = $this->client->post('recharge_packages', $data);
        return $this->hydrate($row);
    }

    public function update(string $id, array $data): void {
        $this->client->patch('recharge_packages', $id, $data);
    }

    public function delete(string $id): void {
        $this->client->delete('recharge_packages', $id);
    }

    private function hydrate(array $r): RechargePackage {
        return new RechargePackage(
            id: $r['id'],
            name: $r['name'],
            zenAmount: (float)$r['zen_amount'],
            pricePen: (float)$r['price_pen'],
            bonusZen: (float)($r['bonus_zen'] ?? 0),
            isActive: (bool)$r['is_active'],
            isFeatured: (bool)($r['is_featured'] ?? false),
            sortOrder: (int)($r['sort_order'] ?? 0)
        );
    }
}
