<?php
declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Application\UseCase\Product\GetAllProducts;
use App\Application\UseCase\Product\GetProductBySlug;
use App\Application\UseCase\Product\SearchProducts;
use App\Application\UseCase\Product\GetProductsByCategory;
use App\Application\UseCase\Category\GetAllCategories;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ProductController
{
    public function __construct(
        private Twig             $twig,
        private GetAllProducts   $getAll,
        private GetProductBySlug $getBySlug,
        private SearchProducts   $search,
        private GetProductsByCategory $getByCategory,
        private GetAllCategories $getAllCategories,
    ) {}

    // ── Web views ────────────────────────────────────────────────────────────

    public function catalog(Request $request, Response $response): Response
    {
        $products = $this->getAll->execute();
        $categories = $this->getAllCategories->execute();

        $page = max(1, (int)($request->getQueryParams()['page'] ?? 1));
        $perPage = 12;
        $totalProducts = count($products);
        $totalPages = max(1, (int)ceil($totalProducts / $perPage));
        $pagedProducts = array_slice($products, ($page - 1) * $perPage, $perPage);

        return $this->twig->render($response, 'pages/catalog.twig', [
            'title'        => 'Catálogo — KAIJUU STORE',
            'products'     => $pagedProducts,
            'categories'   => $categories,
            'currentPage'  => $page,
            'totalPages'   => $totalPages
        ]);
    }

    public function detail(Request $request, Response $response, array $args): Response
    {
        $product = $this->getBySlug->execute($args['slug']);
        if (!$product) {
            return $response->withStatus(404);
        }
        $categoryName = 'Categoría Desconocida';
        $categories = $this->getAllCategories->execute();
        foreach ($categories as $cat) {
            $catId = is_array($cat) ? $cat['id'] : $cat->id;
            $catName = is_array($cat) ? $cat['name'] : $cat->name;
            if ($catId === $product->categoryId) {
                $categoryName = $catName;
                break;
            }
        }
        
        return $this->twig->render($response, 'pages/product-detail.twig', [
            'title'        => $product->name . ' — KAIJUU STORE',
            'product'      => $product,
            'categoryName' => $categoryName
        ]);
    }

    public function search(Request $request, Response $response): Response
    {
        $q        = $request->getQueryParams()['q'] ?? '';
        $products = $this->search->execute($q);
        $categories = $this->getAllCategories->execute();
        return $this->twig->render($response, 'pages/search.twig', [
            'title'      => "Búsqueda: $q — KAIJUU STORE",
            'products'   => $products,
            'categories' => $categories,
            'query'      => $q,
        ]);
    }

    public function byCategory(Request $request, Response $response, array $args): Response
    {
        $categorySlug = $args['slug'];
        $products = $this->getByCategory->execute($categorySlug);
        $categories = $this->getAllCategories->execute();

        $categoryName = ucwords(str_replace('-', ' ', $categorySlug));
        foreach ($categories as $cat) {
            $slug = is_array($cat) ? $cat['slug'] : $cat->slug;
            if ($slug === $categorySlug) {
                $categoryName = is_array($cat) ? $cat['name'] : $cat->name;
                break;
            }
        }

        $page = max(1, (int)($request->getQueryParams()['page'] ?? 1));
        $perPage = 12;
        $totalProducts = count($products);
        $totalPages = max(1, (int)ceil($totalProducts / $perPage));
        $pagedProducts = array_slice($products, ($page - 1) * $perPage, $perPage);
        
        return $this->twig->render($response, 'pages/catalog.twig', [
            'title'        => "Categoría: " . $categoryName . " — KAIJUU STORE",
            'products'     => $pagedProducts,
            'categories'   => $categories,
            'categoryName' => $categoryName,
            'currentSlug'  => $categorySlug,
            'currentPage'  => $page,
            'totalPages'   => $totalPages
        ]);
    }

    // ── JSON API (consumed by Anime.js frontend) ─────────────────────────────

    public function apiList(Request $request, Response $response): Response
    {
        $products = $this->getAll->execute();
        $response->getBody()->write(json_encode($products));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function apiDetail(Request $request, Response $response, array $args): Response
    {
        $product = $this->getBySlug->execute($args['slug']);
        $response->getBody()->write(json_encode($product ?? []));
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus($product ? 200 : 404);
    }

    public function apiSearch(Request $request, Response $response): Response
    {
        $q        = $request->getQueryParams()['q'] ?? '';
        $products = $this->search->execute($q);
        $response->getBody()->write(json_encode($products));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
