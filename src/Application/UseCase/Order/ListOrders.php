<?php
declare(strict_types=1);

namespace App\Application\UseCase\Order;

use App\Domain\Order\OrderRepositoryInterface;

class ListOrders
{
    public function __construct(private OrderRepositoryInterface $repository) {}

    public function execute(): array
    {
        // SupabaseOrderRepository already has findByUser(), but we need findAll() for the admin dashboard.
        return $this->repository->findAll();
    }
}
