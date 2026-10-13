<?php
declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use App\Domain\Category\CategoryRepositoryInterface;

class AdminCategoryController
{
    public function __construct(
        private Twig $twig,
        private CategoryRepositoryInterface $categoryRepo
    ) {}

    public function index(Request $request, Response $response): Response
    {
        $categories = $this->categoryRepo->findAll();
        return $this->twig->render($response, 'pages/admin/categories.twig', [
            'title' => 'Gestión de Categorías - Kaijuu Store',
            'categories' => $categories
        ]);
    }

    public function createForm(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'pages/admin/category-form.twig', [
            'title' => 'Nueva Categoría - Kaijuu Store'
        ]);
    }

    public function processCreate(Request $request, Response $response): Response
    {
        $data = (array)$request->getParsedBody();
        $name = trim($data['name'] ?? '');
        $slug = trim($data['slug'] ?? '');
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        }

        $this->categoryRepo->create([
            'name' => $name,
            'slug' => $slug
        ]);

        return $response->withHeader('Location', '/admin/categorias')->withStatus(302);
    }

    public function editForm(Request $request, Response $response, array $args): Response
    {
        $category = $this->categoryRepo->findById($args['id']);
        if (!$category) {
            return $response->withHeader('Location', '/admin/categorias')->withStatus(302);
        }

        return $this->twig->render($response, 'pages/admin/category-form.twig', [
            'title' => 'Editar Categoría - Kaijuu Store',
            'category' => $category
        ]);
    }

    public function processEdit(Request $request, Response $response, array $args): Response
    {
        $data = (array)$request->getParsedBody();
        $name = trim($data['name'] ?? '');
        $slug = trim($data['slug'] ?? '');
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        }

        $this->categoryRepo->update($args['id'], [
            'name' => $name,
            'slug' => $slug
        ]);

        return $response->withHeader('Location', '/admin/categorias')->withStatus(302);
    }

    public function processDelete(Request $request, Response $response, array $args): Response
    {
        $this->categoryRepo->delete($args['id']);
        return $response->withHeader('Location', '/admin/categorias')->withStatus(302);
    }
}
