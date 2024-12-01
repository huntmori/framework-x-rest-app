<?php
require __DIR__ . '/../vendor/autoload.php';

use Damoyo\Api\Common\Routing\AttributeRouter;
use Damoyo\Api\Domain\User\Controller\UserController;
use Damoyo\Api\Domain\User\Service\UserService;
use Damoyo\Api\Domain\User\Service\UserServiceImpl;
use Damoyo\Api\Domain\User\Repository\UserRepository;
use Damoyo\Api\Domain\User\Repository\UserRepositoryImpl;
use Damoyo\Api\Common\Database\DatabaseService;
use Damoyo\Api\Common\Exception\Handler;
use Damoyo\Api\Common\Logger\AppLogger;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Loop;
use React\Promise\PromiseInterface;
use React\Http\Message\Response;
use Psr\Log\LoggerInterface;

$logger = AppLogger::getInstance();
$logger->info('Application starting...');

$container = new DI\Container([
    UserRepository::class => \DI\create(UserRepositoryImpl::class)
        ->constructor(\DI\get(DatabaseService::class)),
    UserService::class => \DI\create(UserServiceImpl::class)
        ->constructor(\DI\get(UserRepository::class)),
    Handler::class => \DI\create(Handler::class),
    LoggerInterface::class => \DI\factory([AppLogger::class, 'getInstance'])
]);

$app = new FrameworkX\App(
    new FrameworkX\Container(($container))
    // function (ServerRequestInterface $request, callable $next) use ($container) {
    //     try {
    //         $response = $next($request);
            
    //         if ($response instanceof PromiseInterface) {
    //             return $response->then(null, function (Throwable $e) use ($container) {
    //                 return $container->get(Handler::class)->handle($e);
    //             });
    //         }
            
    //         return $response;
    //     } catch (Throwable $e) {
    //         return $container->get(Handler::class)->handle($e);
    //     }
    // }
);

// 404 처리를 위한 미들웨어
$app->any('*', function (ServerRequestInterface $request) {
    return new Response(
        404,
        ['Content-Type' => 'application/json'],
        json_encode([
            'status' => 'error',
            'message' => 'Resource not found',
            'code' => 404,
            'path' => $request->getUri()->getPath()
        ])
    );
});

// Register all controllers using AttributeRouter
$router = new AttributeRouter($app, $container);
$router->registerControllersFromDirectory(__DIR__ . '/../src/Domain');

// 메모리 사용량 추적 함수
function trackMemoryUsage() {
    $logger = AppLogger::getInstance();
    $memoryUsage = memory_get_usage(true);
    $memoryPeakUsage = memory_get_peak_usage(true);
    
    $memoryUsageMB = round($memoryUsage / 1024 / 1024, 2);
    $memoryPeakUsageMB = round($memoryPeakUsage / 1024 / 1024, 2);
    
    $logger->info("Memory Usage: {$memoryUsageMB}MB, Peak: {$memoryPeakUsageMB}MB");
}

// ReactPHP 이벤트 루프를 사용하여 60초마다 메모리 사용량 추적
$loop = Loop::get();
$loop->addPeriodicTimer(60.0, 'trackMemoryUsage');

$logger->info('Server starting on http://127.0.0.1:8080');
$app->run();