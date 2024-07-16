<?php
require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use App\Runner\AppRunner;

$app = new Application("Server runner", "1.0.0");
$app->add(new AppRunner(__DIR__ ));
$app->run();