<?php

namespace Src\common;

use React\MySQL\ConnectionInterface;

class BaseRepository
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    private function getConnection(): ConnectionInterface
    {
        return $this->conneciton;
    }

    private function query(string $sql, array $params = [])
    {
        return  await($this->conneciton->query($sql,  $params));
    }
}