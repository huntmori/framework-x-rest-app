<?php

namespace Src\common;

use React\MySQL\ConnectionInterface;
use React\MySQL\Io\LazyConnection;

class BaseRepository
{
    private LazyConnection $connection;

    public function __construct(LazyConnection $connection)
    {
        $this->connection = $connection;
    }

    private function getConnection(): LazyConnection
    {
        return $this->conneciton;
    }

    private function query(string $sql, array $params = [])
    {
        return  await($this->conneciton->query($sql,  $params));
    }
}