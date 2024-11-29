<?php
namespace Tests\Domain\User\Repository;

// Autoloader for project and vendor libraries
spl_autoload_register(function($className) {
    // Project source autoloading
    $baseDir = '/workspace/framework-x-rest-app/src/';
    $projectPath = $baseDir . str_replace('Damoyo\\Api\\', '', $className) . '.php';
    
    if (file_exists($projectPath)) {
        require_once $projectPath;
        return true;
    }

    // Vendor autoloading
    $vendorPath = '/workspace/framework-x-rest-app/vendor/' . str_replace('\\', '/', $className) . '.php';
    
    if (file_exists($vendorPath)) {
        require_once $vendorPath;   
        return true;
    }

    return false;
});

// Require Composer's autoloader as a fallback
require_once '/workspace/framework-x-rest-app/vendor/autoload.php';

use Damoyo\Api\Common\Database\DatabaseConfig;
use Damoyo\Api\Common\Database\DatabaseService;
use Damoyo\Api\Domain\User\Entity\User;
use Damoyo\Api\Domain\User\Repository\UserRepositoryImpl;
use React\Mysql\MysqlClient;
use React\Mysql\MysqlResult;
use React\Promise\Promise;

class UserRepositoryImplTest 
{
    private $mockDatabaseService;
    private $userRepository;

    public function setUp() 
    {
        // Create a mock DatabaseService
        $this->mockDatabaseService = $this->createMockDatabaseService();

        // Create the repository with the mock database service
        $this->userRepository = new UserRepositoryImpl($this->mockDatabaseService);
    }

    private function createMockDatabaseService() 
    {
        $mockResult = new class {
            public $resultRows = [
                [
                    'seq' => 1,
                    'uid' => 'test_uid',
                    'id' => 'user123',
                    'email' => 'test@example.com',
                    'password' => 'hashed_password',
                    'created_at' => '2023-01-01 00:00:00',
                    'updated_at' => '2023-01-01 00:00:00'
                ]
            ];
        };

        $mockClient = new class($mockResult) implements MysqlClient {
            private $mockResult;

            public function __construct($mockResult) {
                $this->mockResult = $mockResult;
            }

            public function query($query, $params = []) {
                return new Promise(function($resolve) {
                    $resolve($this->mockResult);
                });
            }

            public function close() {}
            public function ping() {}
            public function quit() {}
            public function selectDb($database) {}
        };

        $mockConfig = new DatabaseConfig(
            host: 'localhost', 
            username: 'testuser', 
            password: 'testpass', 
            port: 3306
        );

        $mockDatabaseService = new class($mockConfig, $mockClient) extends DatabaseService {
            private $mockClient;

            public function __construct($config, $mockClient) {
                parent::__construct($config);
                $this->mockClient = $mockClient;
            }

            public function getClient(): MysqlClient {
                return $this->mockClient;
            }
        };

        return $mockDatabaseService;
    }

    public function testFindOneByIdReturnsUser() 
    {
        $this->setUp();
        $user = $this->userRepository->findOneById('user123');
        
        if (!$user instanceof User) {
            throw new \Exception('User should be an instance of User');
        }
        if ($user->getId() !== 'user123') {
            throw new \Exception('User ID should match');
        }
        if ($user->getEmail() !== 'test@example.com') {
            throw new \Exception('User email should match');
        }
        echo "Test passed successfully!\n";
    }
}

// Run test
$test = new UserRepositoryImplTest();
$test->testFindOneByIdReturnsUser();
