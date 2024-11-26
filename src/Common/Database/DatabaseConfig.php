<?php
namespace Damoyo\Api\Common\Database;

class DatabaseConfig {
    public string $host;
    public string $username;
    public string $password;
    public int $port;

    public function __construct(
        string $host = 'localhost', 
        string $username = 'username', 
        string $password = 'password', 
        int $port = 3306
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
    }

    public function getDsn(): string {
        return "{$this->username}:{$this->password}@{$this->host}:{$this->port}";
    }
}