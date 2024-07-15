<?php
namespace Src\common;

use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class WebSocketServer implements MessageComponentInterface
{

    function onOpen(ConnectionInterface $conn)
    {
        echo "New Connection ({$conn->resourceId})".PHP_EOL;
    }

    function onClose(ConnectionInterface $conn)
    {
        echo "Connection {$conn->resourceId} has disconnected.".PHP_EOL;
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    public function onMessage(ConnectionInterface $conn, MessageInterface $msg)
    {
        echo "New message: $msg\n";
        $conn->send("Received: $msg");
    }
}