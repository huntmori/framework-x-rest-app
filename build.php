<?php
require 'vendor/autoload.php';


$pharFile = 'app.phar';
$buildRoot = __DIR__;

// Create a new Phar archive
$phar = new Phar($pharFile);

// Start buffering. Mandatory to modify Phar
$phar->startBuffering();

// Build the Phar
$phar->buildFromDirectory(__DIR__, '/\.*/');

// Add the .env file manually
$phar->addFile('envs/local/.env', 'envs/local/.env');

// Stop buffering
$phar->stopBuffering();

// Set the Phar stub
$phar->setStub($phar->createDefaultStub('main.php'));

echo "Phar archive created successfully!\n";
