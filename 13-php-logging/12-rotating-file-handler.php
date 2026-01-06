<?php

/**
 * Rotating File Handler in Monolog
 * 
 * The RotatingFileHandler automatically creates new log files based on time intervals,
 * helping manage log file sizes and organize logs by date.
 * 
 * Key Features:
 * - Automatically rotates log files daily, weekly, monthly, etc.
 * - Creates files with date suffixes (e.g., app-2024-01-15.log)
 * - Maintains a maximum number of log files (older files are deleted)
 * - Helps prevent disk space issues from growing log files
 * - Makes log analysis easier by organizing entries by date
 * 
 * Common Use Cases:
 * - Production applications with high log volume
 * - Applications requiring log retention policies
 * - Systems needing organized historical logs
 * - Preventing single large log files
 * 
 * Parameters:
 * - filename: Base path and name for log files
 * - maxFiles: Maximum number of files to keep (default: 0 = unlimited)
 * - level: Minimum logging level (default: DEBUG)
 * - bubble: Whether messages bubble to other handlers (default: true)
 * - filePermission: File permissions for created files (default: null)
 * - useLocking: Whether to lock files during writes (default: false)
 */

/**
 * EXAMPLE USAGE:
 * 
 * 1. Create a FormatterTest in tests directory
 * <?php
 * 
 * namespace Mukhoiran\MVCProject\Tests;
 * 
 * use PHPUnit\Framework\TestCase;
 * use Monolog\Handler\RotatingFileHandler;
 * use Monolog\Handler\StreamHandler;
 * use Monolog\Logger;
 * use Monolog\Level;
 * 
 * class RotatingFileTest extends TestCase
 * {
 *     public function testRotating()
 *     {
 *         $logger = new Logger(RotatingFileTest::class);
 *         $logger->pushHandler(new StreamHandler("php://stderr"));
 *         $logger->pushHandler(new RotatingFileHandler(__DIR__ . '/../app.log', 10, Level::Info));
 * 
 *         $logger->info("learn PHP");
 *         $logger->info("learn PHP OOP");
 *         $logger->info("learn PHP Web");
 *         $logger->info("learn PHP Database");
 * 
 *         self::assertNotNull($logger);
 *     }
 * 
 * }
 * ?>
 * 
 * 2. Run the test to verify rotating file handler functionality
 * vendor/bin/phpunit tests/RotatingFileTest.php
 */