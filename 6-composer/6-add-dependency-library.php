<?php
// Summary Notes:
// 1. Add Dependency with Composer:
//    - Run: composer require monolog/monolog
//    - This updates composer.json and installs Monolog in the vendor directory.
//
// 2. Install Dependencies:
//    - If composer.json already lists Monolog, run: composer install
//    - This installs all dependencies.
//
// 3. Use Dependency in Your PHP File:
//    - Require Composer’s autoloader: require __DIR__ . '/vendor/autoload.php';
//    - Use Monolog classes: use Monolog\Logger; use Monolog\Handler\StreamHandler;
//    - Create a logger instance and write log messages.
//
// Additional Notes:
//    - Run composer update to update dependencies.
//    - Check composer.json and composer.lock for details.
//    - Monolog is a popular PHP logging library.

require __DIR__ . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Create a logger instance
$log = new Logger('my_logger');
$log->pushHandler(new StreamHandler(__DIR__ . '/app.log', Logger::INFO));

// Add log records
$log->info('This is an info log message.');
$log->error('This is an error log message.');

echo "Log messages written to app.log\n";
