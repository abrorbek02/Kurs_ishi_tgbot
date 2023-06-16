<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
if (getenv('APP_ENV') === 'production') {
    //todo: add production env
} else {
    // Load environment variables from .env file using Dotenv
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}