<?php
declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CartController
{
    public function __construct(private Twig $twig) {}

    public function index(Request $request, Response $response): Response
    {
        // El carrito vive en localStorage — esta vista sólo monta el shell HTML
        return $this->twig->render($response, 'pages/cart.twig', [
            'title' => 'Mi Carrito — KAIJUU STORE',
        ]);
    }
}
