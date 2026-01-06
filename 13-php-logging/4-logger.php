<?php

/**
 * MONOLOG LOGGER - SUMMARY
 * 
 * LOGGER
 *    - The main entry point for logging
 *    - Created with a channel name to identify the logging context
 *    - Accepts handlers and processors
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
 * use Monolog\Logger;
 * 
 * class LoggerTest extends TestCase
 * {
 *     public function testLogger(): void
 *     {
 *         $logger = new Logger("Mukhoiran");
 *
 *         self::assertNotNull($logger);
 *     }
 *
 *     public function testLoggerWithName(): void
 *     {
 *         $logger = new Logger(LoggerTest::class);
 *
 *         self::assertNotNull($logger);
 *     }
 * }
 * ?>
 * 
 * 2. Run PHPUnit to test logger creation
 * vendor/bin/phpunit tests/LoggerTest.php
 */