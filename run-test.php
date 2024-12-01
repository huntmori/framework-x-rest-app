<?php
require_once __DIR__ . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Tests\Domain\User\Mapper\UserMapperTest;

class TestRunner {
    public function run() {
        
    }
}

$runner = new TestRunner();
try {
    $runner->run();
    echo "Test completed successfully!\n";
} catch (Exception $e) {
    echo "Test failed: " . $e->getMessage() . "\n";
}
