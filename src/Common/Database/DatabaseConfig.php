<?php
namespace Damoyo\Api\Common\Database;

class DatabaseConfig {
    public string $host;
    public string $username;
    public string $password;
    public int $port;
    public string $database;

    public function __construct(
        string $host = 'localhost', 
        string $username = 'username', 
        string $password = 'password', 
        int $port = 3306,
        string $database = 'damoyo'
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
        $this->database = $database;
    }

    public function getDsn(): string {
        return "{$this->username}:{$this->password}@{$this->host}:{$this->port}/{$this->database}";
    }
}