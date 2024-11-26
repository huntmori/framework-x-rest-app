<?php
namespace Damoyo\Api\Common\Database;

use React\Mysql\MysqlClient;

class DatabaseService {
    private MysqlClient $client;
    private DatabaseConfig $config;

    public function __construct(DatabaseConfig $config) {
        $this->config = $config;
        $this->client = new MysqlClient($this->config->getDsn());
    }

    public function getClient(): MysqlClient {
        return $this->client;
    }

    public function getCurrentTime() {
        return $this->client->query("SELECT NOW() as now");
    }
}