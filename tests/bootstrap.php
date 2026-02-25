<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

foreach ($_ENV as $k => $v) {
    if (is_string($v)) {
        putenv($k . '=' . $v);
    }
}