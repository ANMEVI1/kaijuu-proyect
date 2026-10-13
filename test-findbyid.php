<?php
declare(strict_types=1);

use DI\ContainerBuilder;

define('BASE_PATH', __DIR__);

require BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(BASE_PATH . '/src/Infrastructure/DI/dependencies.php');
$container = $containerBuilder->build();

$repo = $container->get(\App\Domain\User\UserRepositoryInterface::class);

$users = $repo->findAll();
if (empty($users)) {
    echo "No users found.\n";
    exit;
}

$firstUser = $users[0];
echo "Testing findById with ID: " . $firstUser->id . "\n";

$userById = $repo->findById($firstUser->id);

if ($userById) {
    echo "Success! Found user: " . $userById->email . "\n";
} else {
    echo "Failed! Could not find user by ID.\n";
}
