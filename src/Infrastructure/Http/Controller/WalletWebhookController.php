<?php
declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class WalletWebhookController {
    public function paypal(Request $request, Response $response): Response {
        // Webhook logic
        return $response->withStatus(200);
    }

    public function mercadopago(Request $request, Response $response): Response {
        // Webhook logic
        return $response->withStatus(200);
    }
}
