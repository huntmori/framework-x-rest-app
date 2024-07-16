<?php

require __DIR__ . '/../vendor/autoload.php';

use React\Http\Message\Response as Response;
use Psr\Http\Message\ServerRequestInterface as ServerRequestInterface;
use League\Container\Container as Container;
use React\MySQL\ConnectionInterface;

use function React\Async\await;

$profile = "local";
$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../envs/{$profile}");
$dotEnv->load();

putenv("X_LISTEN=127.0.0.1:9090");

$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$host = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];

$credentials = "{$user}:{$pass}@{$host}/{$dbName}";

$db = (new React\MySQL\Factory())->createLazyConnection($credentials);

$container = new Container();
$container->add(ConnectionInterface::class, $db);

$app = new FrameworkX\App(new \FrameworkX\Container($container));

$app->get('/', function () use ($db) {
    $result = await($db->query("SELECT NOW() as now "));
    $data = $result->resultRows[0];
    var_dump($data);
    echo "select test : {$data['now']}";
    echo PHP_EOL;
    return Response::plaintext(
        "Hello world!\n"
    );
});

$app->get('/users/{name}', function (ServerRequestInterface $request) {
    return Response::plaintext(
        "Hello " . $request->getAttribute('name') . "!\n"
    );
});

$app->run();