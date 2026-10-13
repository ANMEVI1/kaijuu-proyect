<?php
declare(strict_types=1);

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

// ── Cargar variables de entorno ──────────────────────────────────────────────
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

// ── Construir contenedor de dependencias (PHP-DI) ────────────────────────────
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(BASE_PATH . '/src/Infrastructure/DI/dependencies.php');
$container = $containerBuilder->build();

// ── Crear aplicación Slim ────────────────────────────────────────────────────
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addErrorMiddleware((bool)($_ENV['APP_DEBUG'] ?? false), true, true);
$app->addBodyParsingMiddleware();

// ── Cargar rutas ─────────────────────────────────────────────────────────────
(require BASE_PATH . '/src/Infrastructure/Router/routes.php')($app);

$app->run();
