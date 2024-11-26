<?php
require __DIR__ . '/../vendor/autoload.php';

use Damoyo\Api\Common\Routing\AttributeRouter;
use Damoyo\Api\Domain\User\Controller\UserController;
use Damoyo\Api\Domain\User\Service\UserService;
use Damoyo\Api\Domain\User\Service\UserServiceImpl;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

$container = new DI\Container([
    UserService::class => \DI\create(UserServiceImpl::class)
]);

$app = new FrameworkX\App(new FrameworkX\Container(($container)));

// Register all controllers using AttributeRouter
$router = new AttributeRouter($app, $container);
$router->registerControllersFromDirectory(__DIR__ . '/../src/Domain');

$app->run();