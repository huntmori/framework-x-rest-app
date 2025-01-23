<?php
require __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('Asia/Seoul');

use Damoyo\Api\Common\Database\DatabaseService;
use Damoyo\Api\Common\Exception\GlobalExceptionHandler;
use Damoyo\Api\Common\Logger\AppLogger;
use Damoyo\Api\Common\Middleware\ErrorHandlerMiddleware;
use Damoyo\Api\Common\Routing\AttributeRouter;
use Damoyo\Api\Domain\User\Mapper\UserMapper;
use Damoyo\Api\Domain\User\Repository\UserRepository;
use Damoyo\Api\Domain\User\Repository\UserRepositoryImpl;
use Damoyo\Api\Domain\User\Service\UserService;
use Damoyo\Api\Domain\User\Service\UserServiceImpl;
use Monolog\Handler\Handler as MonoLogHandler;
use React\EventLoop\Loop;
use function DI\create;
use function DI\get;

$logger = AppLogger::getInstance();
$logger->info('Application starting...');

$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->useAutowiring(true);

$containerBuilder->addDefinitions([
    UserMapper::class => create(UserMapper::class),
    UserRepository::class => create(UserRepositoryImpl::class)
        ->constructor(
            get(DatabaseService::class),
            get(UserMapper::class)
        ),
    UserService::class => create(UserServiceImpl::class)
        ->constructor(get(UserRepository::class)),
    MonoLogHandler::class => create(MonoLogHandler::class),
    GlobalExceptionHandler::class => create(GlobalExceptionHandler::class)
]);

$container = $containerBuilder->build();

$app = new FrameworkX\App(
    new FrameworkX\Container($container),
    new ErrorHandlerMiddleware()
);

// Register all controllers using AttributeRouter
$router = new AttributeRouter($app, $container);
$router->registerControllersFromDirectory(__DIR__ . '/../src/Domain');

// 메모리 사용량 추적 함수
function trackMemoryUsage(): void
{
    $logger = AppLogger::getInstance();
    $memoryUsage = memory_get_usage(true);
    $formattedMemory = round($memoryUsage / 1024 / 1024, 2);
    $logger->info("Memory Usage: {$formattedMemory}MB");
}

// ReactPHP 이벤트 루프를 사용하여 60초마다 메모리 사용량 추적
$loop = Loop::get();
$loop->addPeriodicTimer(10.0, 'trackMemoryUsage');

$logger->info('Server starting on http://127.0.0.1:8080');
$app->run();
