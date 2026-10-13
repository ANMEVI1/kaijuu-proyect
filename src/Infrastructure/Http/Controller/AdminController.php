<?php
declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use App\Application\UseCase\Product\GetAllProducts;
use App\Application\UseCase\Product\CreateProduct;
use App\Application\UseCase\Product\UpdateProduct;
use App\Application\UseCase\Product\DeleteProduct;
use App\Application\UseCase\Product\GetProductBySlug;
use App\Application\UseCase\Product\GetProductById;
use App\Application\UseCase\Category\GetAllCategories;
use App\Application\UseCase\User\GetAllUsers;
use App\Application\UseCase\User\CreateUser;
use App\Application\UseCase\User\UpdateUser;
use App\Application\UseCase\User\DeleteUser;
use App\Application\UseCase\User\GetUserById;
use App\Application\UseCase\Order\ListOrders;

class AdminController
{
    public function __construct(
        private Twig $twig,
        private GetAllProducts $getAllProducts,
        private CreateProduct $createProduct,
        private UpdateProduct $updateProduct,
        private DeleteProduct $deleteProduct,
        private GetProductBySlug $getProductBySlug,
        private GetAllCategories $getAllCategories,
        private GetAllUsers $getAllUsers,
        private CreateUser $createUser,
        private UpdateUser $updateUser,
        private DeleteUser $deleteUser,
        private GetUserById $getUserById,
        private ListOrders $listOrders,
        private GetProductById $getProductById
    ) {}

    public function dashboard(Request $request, Response $response): Response
    {
        $products = $this->getAllProducts->execute();
        $users = $this->getAllUsers->execute();
        $orders = $this->listOrders->execute();
        
        $totalSales = array_reduce($orders, fn($carry, $o) => $carry + $o->total, 0);

        return $this->twig->render($response, 'pages/admin/dashboard.twig', [
            'title' => 'Panel de Control - Kaijuu Store',
            'stats' => [
                'products_count' => count($products),
                'users_count' => count($users),
                'orders_count' => count($orders),
                'total_sales' => $totalSales
            ]
        ]);
    }

    public function products(Request $request, Response $response): Response
    {
        $products = $this->getAllProducts->execute();
        $page = max(1, (int)($request->getQueryParams()['page'] ?? 1));
        $perPage = 15;
        $total = count($products);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $pagedProducts = array_slice($products, ($page - 1) * $perPage, $perPage);

        return $this->twig->render($response, 'pages/admin/products.twig', [
            'title' => 'Gestión de Productos - Kaijuu Store',
            'products' => $pagedProducts,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function createProductForm(Request $request, Response $response): Response
    {
        $categories = $this->getAllCategories->execute();
        return $this->twig->render($response, 'pages/admin/product-form.twig', [
            'title' => 'Nuevo Producto - Kaijuu Store',
            'categories' => $categories
        ]);
    }

    public function processCreateProduct(Request $request, Response $response): Response
    {
        $data = (array)$request->getParsedBody();
        $this->createProduct->execute([
            'name' => $data['name'],
            'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name']))),
            'description' => $data['description'] ?? '',
            'price' => (float)$data['price'],
            'stock' => (int)$data['stock'],
            'category_id' => $data['category_id'],
            'image_url' => $data['image_url']
        ]);
        return $response->withHeader('Location', '/admin/productos')->withStatus(302);
    }

    public function editProductForm(Request $request, Response $response, array $args): Response
    {
        $product = $this->getProductById->execute($args['id']);
        if (!$product) {
            return $response->withHeader('Location', '/admin/productos')->withStatus(302);
        }
        
        $categories = $this->getAllCategories->execute();
        
        return $this->twig->render($response, 'pages/admin/product-form.twig', [
            'title' => 'Editar Producto - Kaijuu Store',
            'product' => $product,
            'categories' => $categories
        ]);
    }

    public function processEditProduct(Request $request, Response $response, array $args): Response
    {
        $data = (array)$request->getParsedBody();
        $this->updateProduct->execute($args['id'], [
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'price' => (float)$data['price'],
            'stock' => (int)$data['stock'],
            'category_id' => $data['category_id'],
            'image_url' => $data['image_url']
        ]);
        return $response->withHeader('Location', '/admin/productos')->withStatus(302);
    }

    public function processDeleteProduct(Request $request, Response $response, array $args): Response
    {
        $this->deleteProduct->execute($args['id']);
        return $response->withHeader('Location', '/admin/productos')->withStatus(302);
    }

    public function users(Request $request, Response $response): Response
    {
        $users = $this->getAllUsers->execute();
        $page = max(1, (int)($request->getQueryParams()['page'] ?? 1));
        $perPage = 15;
        $total = count($users);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $pagedUsers = array_slice($users, ($page - 1) * $perPage, $perPage);

        return $this->twig->render($response, 'pages/admin/users.twig', [
            'title' => 'Gestión de Usuarios - Kaijuu Store',
            'users' => $pagedUsers,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function createUserForm(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'pages/admin/user-form.twig', [
            'title' => 'Nuevo Usuario - Kaijuu Store'
        ]);
    }

    public function processCreateUser(Request $request, Response $response): Response
    {
        $data = (array)$request->getParsedBody();
        $this->createUser->execute([
            'email' => $data['email'] ?? '',
            'hashed_password' => password_hash($data['password'] ?? '', PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 'customer'
        ]);
        return $response->withHeader('Location', '/admin/usuarios')->withStatus(302);
    }

    public function editUserForm(Request $request, Response $response, array $args): Response
    {
        $user = $this->getUserById->execute($args['id']);
        if (!$user) {
            return $response->withHeader('Location', '/admin/usuarios')->withStatus(302);
        }
        return $this->twig->render($response, 'pages/admin/user-form.twig', [
            'title' => 'Editar Usuario - Kaijuu Store',
            'user' => $user
        ]);
    }

    public function processEditUser(Request $request, Response $response, array $args): Response
    {
        $data = (array)$request->getParsedBody();
        $updateData = ['email' => $data['email']];
        if (!empty($data['password'])) {
            $updateData['hashed_password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (!empty($data['role'])) {
            $updateData['role'] = $data['role'];
        }
        $this->updateUser->execute($args['id'], $updateData);
        return $response->withHeader('Location', '/admin/usuarios')->withStatus(302);
    }

    public function processDeleteUser(Request $request, Response $response, array $args): Response
    {
        $this->deleteUser->execute($args['id']);
        return $response->withHeader('Location', '/admin/usuarios')->withStatus(302);
    }

    public function orders(Request $request, Response $response): Response
    {
        $orders = $this->listOrders->execute();
        $page = max(1, (int)($request->getQueryParams()['page'] ?? 1));
        $perPage = 15;
        $total = count($orders);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $pagedOrders = array_slice($orders, ($page - 1) * $perPage, $perPage);

        return $this->twig->render($response, 'pages/admin/orders.twig', [
            'title' => 'Gestión de Pedidos - Kaijuu Store',
            'orders' => $pagedOrders,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }
}
