<?php

/**
 * Monolog Log Levels - Detailed Summary
 * 
 * Monolog follows the RFC 5424 logging standard with 8 severity levels.
 * Each level has a numeric value and specific use case.
 * 
 * LOG LEVELS (from lowest to highest severity):
 * ============================================
 * 
 * 1. DEBUG (100)
 *    - Detailed debugging information
 *    - Use for: Variable dumps, detailed flow information
 *    - Example: "User input received: array(...)"
 * 
 * 2. INFO (200)
 *    - Informational messages
 *    - Use for: Normal application flow, user actions
 *    - Example: "User logged in successfully"
 * 
 * 3. NOTICE (250)
 *    - Normal but significant events
 *    - Use for: Important events that are not errors
 *    - Example: "User changed password"
 * 
 * 4. WARNING (300)
 *    - Exceptional occurrences that are not errors
 *    - Use for: Deprecated features, poor API usage
 *    - Example: "API response time exceeded 2 seconds"
 * 
 * 5. ERROR (400)
 *    - Runtime errors that don't require immediate action
 *    - Use for: Exceptions caught and handled
 *    - Example: "Database query failed, using cache"
 * 
 * 6. CRITICAL (500)
 *    - Critical conditions
 *    - Use for: Application component unavailable
 *    - Example: "Payment gateway is down"
 * 
 * 7. ALERT (550)
 *    - Action must be taken immediately
 *    - Use for: Database unavailable, website down
 *    - Example: "Redis server is not responding"
 * 
 * 8. EMERGENCY (600)
 *    - System is unusable
 *    - Use for: Complete system failure
 *    - Example: "Entire application crashed"
 */

/**
 * Example
 * 
 * 1. Create a LevelTest.php in tests directory
 * <?php
 * 
 * namespace Mukhoiran\MVCProject\Tests;
 * 
 * use PHPUnit\Framework\TestCase;
 * use Monolog\Handler\StreamHandler;
 * use Monolog\Logger;
 * 
 * class LevelTest extends TestCase
 * {
 *     public function testLevel(): void
 *     {
 *         $logger = new Logger(LevelTest::class);
 * 
 *         $logger->pushHandler(new StreamHandler("php://stderr"));
 *         $logger->pushHandler(new StreamHandler(__DIR__ . '/../application.log'));
 *         $logger->pushHandler(new StreamHandler(__DIR__ . '/../error.log', Logger::ERROR));
 * 
 *         $logger->debug("This is a debug message");
 *         $logger->info("This is an info message");
 *         $logger->notice("This is a notice message");
 *         $logger->warning("This is a warning message");
 *         $logger->error("This is an error message");
 *         $logger->critical("This is a critical message");
 *         $logger->alert("This is an alert message");
 *         $logger->emergency("This is an emergency message");
 * 
 *         self::assertNotNull($logger);
 *     }
 * }
 * ?>
 * 
 * 2. Run the test to verify log levels
 * vendor/bin/phpunit tests/LevelTest.php
 */


/**
 * BEST PRACTICES:
 * ===============
 * - Use DEBUG only in development
 * - Use INFO for tracking user actions
 * - Use WARNING for recoverable issues
 * - Use ERROR for caught exceptions
 * - Use CRITICAL and above sparingly
 * - Set appropriate minimum level per environment
 * - Production: WARNING or ERROR
 * - Development: DEBUG
 * - Staging: INFO or NOTICE
 */