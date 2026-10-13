<?php
declare(strict_types=1);

namespace App\Application\UseCase\Order;

use App\Domain\Order\OrderRepositoryInterface;

class CreateOrder
{
    public function __construct(
        private readonly OrderRepositoryInterface $repo
    ) {}

    public function execute(array $payload): array
    {
        $orderData = [
            'total' => $payload['total'] ?? 0,
            'status' => 'pendiente',
        ];
        if (!empty($payload['user_id'])) {
            $orderData['user_id'] = $payload['user_id'];
        }
        if (!empty($payload['shipping_address'])) {
            $orderData['shipping_address'] = $payload['shipping_address'];
        }
        if (!empty($payload['payment_method'])) {
            $orderData['payment_method'] = $payload['payment_method'];
        }
        if (isset($payload['zen_used'])) {
            $orderData['zen_used'] = (float)$payload['zen_used'];
        }
        if (isset($payload['fiat_used'])) {
            $orderData['fiat_used'] = (float)$payload['fiat_used'];
        }

        $order = $this->repo->create($orderData);
        
        if (!empty($payload['cart_items'])) {
            $cartItems = is_string($payload['cart_items']) ? json_decode($payload['cart_items'], true) : $payload['cart_items'];
            if (is_array($cartItems)) {
                $orderItems = [];
                foreach ($cartItems as $item) {
                    $orderItems[] = [
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'quantity' => $item['qty'],
                        'price_at_purchase' => $item['price']
                    ];
                }
                $this->repo->addItems($orderItems);
            }
        }

        return (array)$order;
    }
}
