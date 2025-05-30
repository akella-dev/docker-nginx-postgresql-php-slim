<?php

use Slim\App;

$container = require dirname(__DIR__) . '/boot.php';
$container->get(App::class)->run();
