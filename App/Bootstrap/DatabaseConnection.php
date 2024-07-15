<?php
namespace App\Bootstrap;

use League\Container\Container as Container;
use React\MySQL\ConnectionInterface;

return function(Container $container)
{
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];
    $host = $_ENV['DB_HOST'];
    $dbName = $_ENV['DB_NAME'];

    $credentials = "{$user}:{$pass}@{$host}/{$dbName}";

    $db = (new React\MySQL\Factory())->createLazyConnection($credentials);
    $container->add(ConnectionInterface::class, $db);

};