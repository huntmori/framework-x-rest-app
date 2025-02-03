<?php
namespace Damoyo\Api\Common\Database;

use PDO;
use Medoo\Medoo;

class DatabaseService {
    public Medoo $client {
        get {
            return $this->client;
        }
    }
    private DatabaseConfig $config;

    public function __construct(DatabaseConfig $config) {
        $this->config = $config;
        $this->createConnection($config);
    }

    public function createConnection(DatabaseConfig $config): void
    {
        $this->client = new Medoo([
            // [required]
            'type' => 'mysql',
            'host' => $config->host,
            'database' => $config->database,
            'username' => $config->username,
            'password' => $config->password,

            // [optional]
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'port' => 3306,

            // [optional] The table prefix. All table names will be prefixed as PREFIX_table.
            //'prefix' => 'PREFIX_',

            // [optional] To enable logging. It is disabled by default for better performance.
            'logging' => true,

            // [optional]
            // Error mode
            // Error handling strategies when the error has occurred.
            // PDO::ERRMODE_SILENT (default) | PDO::ERRMODE_WARNING | PDO::ERRMODE_EXCEPTION
            // Read more from https://www.php.net/manual/en/pdo.error-handling.php.
            'error' => PDO::ERRMODE_SILENT,

            // [optional]
            // The driver_option for connection.
            // Read more from http://www.php.net/manual/en/pdo.setattribute.php.
            'option' => [
                PDO::ATTR_CASE => PDO::CASE_NATURAL
            ],

            // [optional] Medoo will execute those commands after the database is connected.
            'command' => [
                'use '.$config->database,
            ]
        ]);
    }
}