<?php
declare(strict_types=1);

namespace App\Domain\Order;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;
    public function findById(string $id): ?Order;
    /** @return Order[] */
    public function findByUser(string $userId): array;
    public function updateStatus(string $id, string $status): void;
    
    /** @return Order[] */
    public function findAll(): array;
    
    public function addItems(array $items): void;
}
