<?php
declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Application\UseCase\Product\GetAllProducts;
use App\Application\UseCase\Category\GetAllCategories;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController
{
    public function __construct(
        private Twig           $twig,
        private GetAllProducts $getAllProducts,
        private GetAllCategories $getAllCategories,
    ) {}

    public function index(Request $request, Response $response): Response
    {
        $products = $this->getAllProducts->execute();
        $categories = $this->getAllCategories->execute();
        // sólo mostramos 8 destacados en el hero
        $featured = array_slice($products, 0, 8);

        return $this->twig->render($response, 'pages/home.twig', [
            'title'      => 'KAIJUU STORE — Viste tu fandom. Domina tu colección.',
            'featured'   => $featured,
            'categories' => $categories,
        ]);
    }
}
