<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = new Dotenv();
if (!isset($_SERVER['APP_ENV'])) {
    $dotenv->bootEnv(dirname(__DIR__) . '/.env');
} elseif (!isset($_SERVER['DATABASE_URL'])) {
    $dotenv->loadEnv(dirname(__DIR__) . '/.env');
}
