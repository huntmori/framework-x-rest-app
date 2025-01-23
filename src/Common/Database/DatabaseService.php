<?php
namespace Damoyo\Api\Common\Database;

use React\Mysql\MysqlClient;

class DatabaseService {
    private MysqlClient $client;
    private DatabaseConfig $config;

    public function __construct(DatabaseConfig $config) {
        $this->config = $config;
        $this->client = new MysqlClient($this->config->getDsn());
        
        // 명시적으로 데이터베이스 선택
        $this->client->query("USE {$this->config->database}");
    }

    public function getClient(): MysqlClient {
        return $this->client;
    }

    public function getCurrentTime() {
        return $this->client->query("SELECT NOW() as now");
    }
}