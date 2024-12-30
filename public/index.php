<?php
require __DIR__ . '/../vendor/autoload.php';
\Sentry\init([
  'dsn' => 'https://9f6e3a7ae4e9bf1b0ca0f68f4bfc9e8f@o4508475231961088.ingest.us.sentry.io/4508475233992704',
  // Specify a fixed sample rate
  'traces_sample_rate' => 1.0,
  // Set a sampling rate for profiling - this is relative to traces_sample_rate
  'profiles_sample_rate' => 1.0,
]);
// Set timezone
date_default_timezone_set('Asia/Seoul');

use Damoyo\Api\Common\Routing\AttributeRouter;
use Damoyo\Api\Domain\User\Mapper\UserMapper;
use Damoyo\Api\Domain\User\Service\UserService;
use Damoyo\Api\Domain\User\Service\UserServiceImpl;
use Damoyo\Api\Domain\User\Repository\UserRepository;
use Damoyo\Api\Domain\User\Repository\UserRepositoryImpl;
use Damoyo\Api\Common\Database\DatabaseService;
use Damoyo\Api\Common\Exception\GlobalExceptionHandler;
use Damoyo\Api\Common\Logger\AppLogger;
use Damoyo\Api\Common\Middleware\ErrorHandlerMiddleware;
use React\EventLoop\Loop;
use Monolog\Handler\Handler AS MonoLogHandler;

$logger = AppLogger::getInstance();
$logger->info('Application starting...');

$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->useAutowiring(true);

$containerBuilder->addDefinitions([
    UserMapper::class => \DI\create(UserMapper::class),
    UserRepository::class => \DI\create(UserRepositoryImpl::class)
        ->constructor(
            \DI\get(DatabaseService::class),
            \DI\get(UserMapper::class)
        ),
    UserService::class => \DI\create(UserServiceImpl::class)
        ->constructor(\DI\get(UserRepository::class)),
    MonoLogHandler::class => \DI\create(MonoLogHandler::class),
    GlobalExceptionHandler::class => \DI\create(GlobalExceptionHandler::class)
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
function trackMemoryUsage() {
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
