<?php

require __DIR__ . '/../vendor/autoload.php';

$client = new Clue\React\Docker\Client();

$list = $client->containerList();

var_dump($list);