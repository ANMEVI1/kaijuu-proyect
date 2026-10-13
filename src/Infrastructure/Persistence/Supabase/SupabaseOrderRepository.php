<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Supabase;

use App\Domain\Order\Order;
use App\Domain\Order\OrderRepositoryInterface;

class SupabaseOrderRepository implements OrderRepositoryInterface
{
    public function __construct(private SupabaseClient $client) {}

    public function create(array $data): Order
    {
        $row = $this->client->post('orders', $data);
        if (empty($row) || !isset($row['id'])) {
            throw new \RuntimeException("Failed to insert order: " . json_encode($row));
        }
        return $this->hydrate($row);
    }

    public function findById(string $id): ?Order
    {
        $rows = $this->client->get('orders', ['select' => '*', 'id' => "eq.$id"]);
        return isset($rows[0]) ? $this->hydrate($rows[0]) : null;
    }

    public function findAll(): array
    {
        $rows = $this->client->get('orders', [
            'select' => '*',
            'order' => 'created_at.desc'
        ]);
        return array_map(fn($r) => $this->hydrate($r), $rows);
    }

    public function findByUser(string $userId): array
    {
        $rows = $this->client->get('orders', [
            'select'  => '*',
            'user_id' => "eq.$userId",
            'order'   => 'created_at.desc',
        ]);
        return array_map(fn($r) => $this->hydrate($r), $rows);
    }

    public function updateStatus(string $id, string $status): void
    {
        $this->client->patch('orders', $id, ['status' => $status]);
    }

    public function addItems(array $items): void
    {
        if (empty($items)) return;
        
        foreach ($items as $item) {
            // Insert item
            $this->client->post('order_items', $item);
            
            // Get current stock
            $productRows = $this->client->get('products', ['select' => 'stock', 'id' => 'eq.' . $item['product_id']]);
            if (!empty($productRows[0])) {
                $newStock = max(0, (int)$productRows[0]['stock'] - (int)$item['quantity']);
                $this->client->patch('products', $item['product_id'], ['stock' => $newStock]);
                
                // Record inventory movement
                $this->client->post('inventory_movements', [
                    'product_id' => $item['product_id'],
                    'type' => 'salida',
                    'quantity' => $item['quantity'],
                    'reason' => 'compra'
                ]);
            }
        }
    }

    private function hydrate(array $r): Order
    {
        return new Order(
            id:        $r['id'],
            userId:    $r['user_id'] ?? '',
            status:    $r['status'],
            total:     (float)$r['total'],
            createdAt: $r['created_at'],
        );
    }
}
