<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$c = new App\Infrastructure\Persistence\Supabase\SupabaseClient();
$users = $c->get('users', ['select' => '*']);
var_dump($users);
