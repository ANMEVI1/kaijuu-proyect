<?php
declare(strict_types=1);

use App\Infrastructure\Http\Controller\HomeController;
use App\Infrastructure\Http\Controller\ProductController;
use App\Infrastructure\Http\Controller\CartController;
use App\Infrastructure\Http\Controller\CheckoutController;
use App\Infrastructure\Http\Controller\WalletController;
use App\Infrastructure\Http\Controller\AdminWalletController;
use App\Infrastructure\Http\Controller\WalletWebhookController;
use App\Infrastructure\Http\Controller\AdminController;
use Slim\App;

return function (App $app): void {

    // ── Página principal ─────────────────────────────────────────────────────
    $app->get('/', [HomeController::class, 'index'])->setName('home');

    // ── Catálogo ─────────────────────────────────────────────────────────────
    $app->get('/catalogo', [ProductController::class, 'catalog'])->setName('catalog');
    $app->get('/catalogo/{slug}', [ProductController::class, 'detail'])->setName('product.detail');
    $app->get('/buscar', [ProductController::class, 'search'])->setName('search');
    $app->get('/categoria/{slug}', [ProductController::class, 'byCategory'])->setName('category');

    // ── Carrito ──────────────────────────────────────────────────────────────
    $app->get('/carrito', [CartController::class, 'index'])->setName('cart');

    // ── Checkout ─────────────────────────────────────────────────────────────
    $app->get('/checkout', [CheckoutController::class, 'index'])->setName('checkout');
    $app->post('/checkout/procesar', [CheckoutController::class, 'process'])->setName('checkout.process');
    $app->get('/checkout/confirmacion/{id}', [CheckoutController::class, 'confirmation'])->setName('checkout.confirmation');

    // ── API JSON (para Anime.js / fetch del frontend) ────────────────────────
    $app->group('/api/v1', function ($group) {
        $group->get('/productos', [ProductController::class, 'apiList']);
        $group->get('/productos/{slug}', [ProductController::class, 'apiDetail']);
        $group->get('/buscar', [ProductController::class, 'apiSearch']);
        $group->post('/pedidos', [CheckoutController::class, 'apiCreate']);
        
        // Monedero APIs
        $group->get('/monedero/saldo', [WalletController::class, 'apiGetBalance']);
        $group->get('/monedero/paquetes', [WalletController::class, 'apiGetPackages']);
        $group->get('/monedero/tipo-cambio', [WalletController::class, 'apiGetExchangeRate']);
    });
    
    // ── Monedero ZEN ─────────────────────────────────────────────────────────
    $app->get('/monedero', [WalletController::class, 'index'])->setName('wallet.index');
    $app->get('/monedero/recargar', [WalletController::class, 'rechargeForm'])->setName('wallet.recharge');
    $app->post('/monedero/recargar', [WalletController::class, 'initiateRecharge'])->setName('wallet.initiate');
    $app->get('/monedero/recargar/exitoso', [WalletController::class, 'rechargeSuccess'])->setName('wallet.success');
    $app->get('/monedero/historial', [WalletController::class, 'history'])->setName('wallet.history');

    // ── Webhooks (Pagos) ─────────────────────────────────────────────────────
    $app->post('/webhook/paypal/wallet', [WalletWebhookController::class, 'paypal']);
    $app->post('/webhook/mercadopago/wallet', [WalletWebhookController::class, 'mercadopago']);

    // ── Panel de Administración ──────────────────────────────────────────────
    $app->get('/admin', [AdminController::class, 'dashboard'])->setName('admin.dashboard');
    $app->get('/admin/productos', [AdminController::class, 'products'])->setName('admin.products');
    
    // CRUD Productos
    $app->get('/admin/productos/nuevo', [AdminController::class, 'createProductForm'])->setName('admin.products.create');
    $app->post('/admin/productos/nuevo', [AdminController::class, 'processCreateProduct']);
    $app->get('/admin/productos/{id}/editar', [AdminController::class, 'editProductForm'])->setName('admin.products.edit');
    $app->post('/admin/productos/{id}/editar', [AdminController::class, 'processEditProduct']);
    $app->post('/admin/productos/{id}/eliminar', [AdminController::class, 'processDeleteProduct'])->setName('admin.products.delete');

    // CRUD Usuarios
    $app->get('/admin/usuarios', [AdminController::class, 'users'])->setName('admin.users');
    $app->get('/admin/usuarios/nuevo', [AdminController::class, 'createUserForm'])->setName('admin.users.create');
    $app->post('/admin/usuarios/nuevo', [AdminController::class, 'processCreateUser']);
    $app->get('/admin/usuarios/{id}/editar', [AdminController::class, 'editUserForm'])->setName('admin.users.edit');
    $app->post('/admin/usuarios/{id}/editar', [AdminController::class, 'processEditUser']);
    $app->post('/admin/usuarios/{id}/eliminar', [AdminController::class, 'processDeleteUser'])->setName('admin.users.delete');

    // CRUD Categorías
    $app->get('/admin/categorias', [\App\Infrastructure\Http\Controller\AdminCategoryController::class, 'index'])->setName('admin.categories');
    $app->get('/admin/categorias/nueva', [\App\Infrastructure\Http\Controller\AdminCategoryController::class, 'createForm'])->setName('admin.categories.create');
    $app->post('/admin/categorias/nueva', [\App\Infrastructure\Http\Controller\AdminCategoryController::class, 'processCreate']);
    $app->get('/admin/categorias/{id}/editar', [\App\Infrastructure\Http\Controller\AdminCategoryController::class, 'editForm'])->setName('admin.categories.edit');
    $app->post('/admin/categorias/{id}/editar', [\App\Infrastructure\Http\Controller\AdminCategoryController::class, 'processEdit']);
    $app->post('/admin/categorias/{id}/eliminar', [\App\Infrastructure\Http\Controller\AdminCategoryController::class, 'processDelete'])->setName('admin.categories.delete');

    // CRUD Pedidos
    $app->get('/admin/pedidos', [AdminController::class, 'orders'])->setName('admin.orders');

    // ── Admin (Monedero ZEN) ─────────────────────────────────────────────────
    $app->get('/admin/monedero', [AdminWalletController::class, 'index'])->setName('admin.wallet');
    $app->post('/admin/monedero/tipo-cambio', [AdminWalletController::class, 'updateRate'])->setName('admin.wallet.rate');
    $app->post('/admin/monedero/ajuste', [AdminWalletController::class, 'adjustBalance'])->setName('admin.wallet.adjust');
};
