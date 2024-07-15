<?php
namespace App\Bootstrap;

use League\Container\Container as Container;
use React\MySQL\ConnectionInterface;
use Src\User\UserRepository;

return function (Container $container)
{
    $container->add(
        UserRepository::class,
        new UserRepository($container->get(ConnectionInterface::class))
    );

};

