<?php
declare(strict_types=1);

use App\Domain\Product\ProductRepositoryInterface;
use App\Domain\Order\OrderRepositoryInterface;
use App\Infrastructure\Persistence\Supabase\SupabaseClient;
use App\Infrastructure\Persistence\Supabase\SupabaseProductRepository;
use App\Infrastructure\Persistence\Supabase\SupabaseOrderRepository;
use App\Domain\Category\CategoryRepositoryInterface;
use App\Infrastructure\Persistence\Supabase\SupabaseCategoryRepository;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Persistence\Supabase\SupabaseUserRepository;
use App\Infrastructure\Http\Controller\HomeController;
use App\Infrastructure\Http\Controller\ProductController;
use App\Infrastructure\Http\Controller\CartController;
use App\Infrastructure\Http\Controller\CheckoutController;
use App\Infrastructure\Http\Controller\WalletController;
use App\Infrastructure\Http\Controller\AdminWalletController;
use App\Infrastructure\Http\Controller\WalletWebhookController;
use App\Domain\Wallet\WalletRepositoryInterface;
use App\Domain\Wallet\ExchangeRateRepositoryInterface;
use App\Domain\Wallet\RechargePackageRepositoryInterface;
use App\Infrastructure\Persistence\Supabase\SupabaseWalletRepository;
use App\Infrastructure\Persistence\Supabase\SupabaseExchangeRateRepository;
use App\Infrastructure\Persistence\Supabase\SupabaseRechargePackageRepository;
use Slim\Views\Twig;
use DI\ContainerBuilder;

return [
    // ── Supabase client (singleton) ──────────────────────────────────────────
    SupabaseClient::class => \DI\create(SupabaseClient::class),

    // ── Repositorios (puertos → adaptadores) ────────────────────────────────
    ProductRepositoryInterface::class => \DI\autowire(SupabaseProductRepository::class),
    OrderRepositoryInterface::class   => \DI\autowire(SupabaseOrderRepository::class),
    CategoryRepositoryInterface::class=> \DI\autowire(SupabaseCategoryRepository::class),
    UserRepositoryInterface::class    => \DI\autowire(SupabaseUserRepository::class),
    WalletRepositoryInterface::class  => \DI\autowire(SupabaseWalletRepository::class),
    ExchangeRateRepositoryInterface::class   => \DI\autowire(SupabaseExchangeRateRepository::class),
    RechargePackageRepositoryInterface::class => \DI\autowire(SupabaseRechargePackageRepository::class),

    // ── Twig (sistema de plantillas) ─────────────────────────────────────────
    Twig::class => function () {
        $twig = Twig::create(BASE_PATH . '/src/Infrastructure/Views', [
            'cache' => ($_ENV['APP_ENV'] === 'production')
                ? BASE_PATH . '/var/cache/twig'
                : false,
        ]);
        $twig->getEnvironment()->addGlobal('APP_BASE_URL', $_ENV['APP_BASE_URL'] ?? '');
        return $twig;
    },

    // ── Controladores ────────────────────────────────────────────────────────
    HomeController::class     => \DI\autowire(HomeController::class),
    ProductController::class  => \DI\autowire(ProductController::class),
    CartController::class     => \DI\autowire(CartController::class),
    CheckoutController::class => \DI\autowire(CheckoutController::class),
    \App\Infrastructure\Http\Controller\AdminController::class => \DI\autowire(\App\Infrastructure\Http\Controller\AdminController::class),
    \App\Infrastructure\Http\Controller\AdminCategoryController::class => \DI\autowire(\App\Infrastructure\Http\Controller\AdminCategoryController::class),
    WalletController::class   => \DI\autowire(WalletController::class),
    AdminWalletController::class => \DI\autowire(AdminWalletController::class),
    WalletWebhookController::class => \DI\autowire(WalletWebhookController::class),
];
