<?php
require __DIR__ . '/../vendor/autoload.php';
use Thruway\Peer\Router;
use Thruway\Transport\RatchetTransportProvider;

$router = new Router();

$transportProvider = new RatchetTransportProvider("127.0.0.1", 9090);

$router->addTransportProvider($transportProvider);

$router->getLoop()->addPeriodicTimer(10.0, function () use ($router) {
    var_dump($router->managerGetSessionCount());
});

$router->start();