<?php
declare(strict_types=1);

namespace App\Domain\Order;

class Order
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public string          $status, // pendiente | pagado | preparando | enviado | entregado | cancelado | devuelto
        public readonly float  $total,
        public readonly string $createdAt,
    ) {}
}
