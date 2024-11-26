<?php
require_once __DIR__ . '/../vendor/autoload.php';

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
    try {
    $data = $container->get(UserController::class)->listUsers($request);
    } catch(Exception $e) {
        $data = ResponseDto::init()
            ->code(500)
            ->result(false)
            ->message('사용자 목록 조회 실패')
            ->data([]);
    }
    
    return ResponseDto::toResponse($data);
});

$app->post('/user/create', function (ServerRequestInterface $request) use ($container) {
    return $container->get(UserController::class)->createUser($request);
});

$app->run();