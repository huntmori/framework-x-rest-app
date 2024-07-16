<?php

use React\MySQL\ConnectionInterface;

require __DIR__ . '/../vendor/autoload.php';

$container = new \League\Container\Container();


$profile = "local";
$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../envs/{$profile}");
$dotEnv->load();


$container->delegate(
    new \League\Container\ReflectionContainer()
);
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$host = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];

$credentials = "{$user}:{$pass}@{$host}/{$dbName}";

$db = (new React\MySQL\Factory())->createLazyConnection($credentials);
$container->add(ConnectionInterface::class, $db);
//$container->add('connection', $db);

$userRepository = $container->get(\Src\User\Repository\UserRepository::class);

var_dump(gettype($userRepository));

var_dump(get_class($userRepository));

var_dump(get_class($container->get(ConnectionInterface::class)));