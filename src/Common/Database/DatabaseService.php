<?php
namespace Damoyo\Api\Common\Database;

use React\MySQL\ConnectionInterface;
use React\Mysql\Io\Factory;
use React\Mysql\MysqlClient;

class DatabaseService {
    public ConnectionInterface $client {
        get {
            return $this->client;
        }
    }
    private DatabaseConfig $config;
    private Factory $factory;

    public function __construct(DatabaseConfig $config) {
        $this->config = $config;
        $this->factory = new \React\MySQL\Factory();
        $this->client = $this->factory->createLazyConnection($config->getDsn());
        // 명시적으로 데이터베이스 선택
        $this->client->query("USE {$this->config->database}");
    }

}