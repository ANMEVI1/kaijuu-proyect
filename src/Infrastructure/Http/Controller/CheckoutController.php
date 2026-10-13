<?php
declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Application\UseCase\Order\CreateOrder;
use App\Application\UseCase\Wallet\GetWalletBalance;
use App\Application\UseCase\Wallet\SpendZen;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CheckoutController
{
    public function __construct(
        private Twig        $twig,
        private CreateOrder $createOrder,
        private GetWalletBalance $getWalletBalance,
        private SpendZen $spendZen,
    ) {}

    public function index(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'pages/checkout.twig', [
            'title' => 'Checkout — KAIJUU STORE',
        ]);
    }

    public function process(Request $request, Response $response): Response
    {
        try {
            $body  = (array)$request->getParsedBody();
            $order = $this->createOrder->execute($body);
            return $response
                ->withHeader('Location', '/checkout/confirmacion/' . $order['id'])
                ->withStatus(302);
        } catch (\Exception $e) {
            // Log error in a real app, redirect back to checkout on failure
            return $response
                ->withHeader('Location', '/checkout?error=1')
                ->withStatus(302);
        }
    }

    public function confirmation(Request $request, Response $response, array $args): Response
    {
        return $this->twig->render($response, 'pages/confirmation.twig', [
            'title'   => 'Pedido confirmado — KAIJUU STORE',
            'orderId' => $args['id'],
        ]);
    }

    // ── API JSON ─────────────────────────────────────────────────────────────
    public function apiCreate(Request $request, Response $response): Response
    {
        $body  = (array)$request->getParsedBody();
        $total = (float)($body['total'] ?? 0);
        
        $session = json_decode($_COOKIE['kaijuu_session'] ?? '[]', true);
        $userId = $session['id'] ?? null;
        
        if ($userId) {
            $wallet = $this->getWalletBalance->execute($userId);
            if (!$wallet->canAfford($total)) {
                $response->getBody()->write(json_encode(['error' => 'Saldo insuficiente en monedero ZEN.']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            
            // Add wallet info to body
            $body['user_id'] = $userId;
            $body['payment_method'] = 'zen_wallet';
            $body['zen_used'] = $total;
            $body['fiat_used'] = 0;
            
            $order = $this->createOrder->execute($body);
            
            // Deduct Zen
            $this->spendZen->execute($userId, $total, $order['id']);
        } else {
            // Guest order or no ZEN used (optional depending on rules)
            $order = $this->createOrder->execute($body);
        }
        
        $response->getBody()->write(json_encode($order));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
