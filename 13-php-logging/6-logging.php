<?php

/**
 * MONOLOG LOGGING - SUMMARY
 * 
 * Logging is used to record application events, errors, and other information for debugging and monitoring.
 * 
 */

/**
 * Example
 * 
 * 1. Create LoggerTest.php in tests directory
 * <?php
 *
 * namespace Mukhoiran\MVCProject\Tests;
 * 
 * use PHPUnit\Framework\TestCase;
 * use Monolog\Handler\StreamHandler;
 * use Monolog\Logger;
 * 
 * class LoggingTest extends TestCase
 * {
 *     public function testLogging()
 *     {
 *         $logger = new Logger(HandlerTest::class);
 * 
 *         $logger->pushHandler(new StreamHandler("php://stderr"));
 *         $logger->pushHandler(new StreamHandler(__DIR__ . "/../application.log"));
 * 
 *         $logger->info("Learning PHP Logging");
 *         $logger->info("Welcome to Mukhoiran Docs");
 *         $logger->info("This is Application Logging Information");
 * 
 *         self::assertNotNull($logger);
 *     }
 * }
 * ?>
 * 
 * 2. Run PHPUnit to test logging functionality
 * vendor/bin/phpunit tests/LoggingTest.php
 */