<?php
require __DIR__ . '/../vendor/autoload.php';

use Damoyo\Api\Common\Dto\ResponseDto;
use Damoyo\Api\Common\Routing\Router;
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

// User routes
$app->get('/user', function (ServerRequestInterface $request) use ($container) {
    $data = $container->get(UserController::class)->listUsers($request);
    
    return ResponseDto::toResponse($data);
});

$app->post('/user/create', function (ServerRequestInterface $request) use ($container) {
    return $container->get(UserController::class)->createUser($request);
});

$app->run();