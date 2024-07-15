<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Loop::get();
$webSocketHost = "0.0.0.0:8081";
$socket = new React\Socket\SocketServer($webSocketHost, [], $loop);

$websocket = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
            new App\common\WebSocketServer()
        )
    ),
    $socket,
    $loop
);

echo "web socket server on {$webSocketHost}".PHP_EOL;

$loop->run();