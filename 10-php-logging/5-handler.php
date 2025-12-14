<?php

/**
 * MONOLOG HANDLERS - DETAILED SUMMARY
 * 
 * Handlers in Monolog determine WHERE and HOW log messages are processed and stored.
 * Each handler is responsible for writing log records to a specific destination.
 * 
 * ============================================================================
 * KEY CONCEPTS:
 * ============================================================================
 * 
 * 1. HANDLER HIERARCHY
 *    - Handlers are stacked (multiple handlers can be attached to one logger)
 *    - Each handler has a minimum log level
 *    - Messages cascade through handlers based on their level
 * 
 * 2. BUILT-IN HANDLERS (Common ones):
 * 
 *    a) StreamHandler
 *       - Writes to any PHP stream (files, php://stdout, php://stderr)
 *       - Most commonly used for file logging
 *       - Example: new StreamHandler('app.log', Logger::WARNING)
 * 
 *    b) RotatingFileHandler
 *       - Automatically rotates log files daily
 *       - Keeps a maximum number of files
 *       - Example: new RotatingFileHandler('app.log', 30, Logger::DEBUG)
 * 
 *    c) ErrorLogHandler
 *       - Uses PHP's error_log() function
 *       - Good for simple logging needs
 * 
 *    d) SyslogHandler
 *       - Logs to system's syslog
 *       - For Unix/Linux systems
 * 
 *    e) NativeMailerHandler
 *       - Sends log messages via email
 *       - Useful for critical errors
 * 
 *    f) FirePHPHandler / ChromePHPHandler
 *       - Sends logs to browser console
 *       - For development debugging
 * 
 *    g) SlackHandler / TelegramBotHandler
 *       - Sends notifications to Slack/Telegram
 *       - For team alerting
 * 
 *    h) DatabaseHandler (various)
 *       - PDOHandler, MongoDBHandler, RedisHandler
 *       - Stores logs in databases
 * 
 * 3. HANDLER CONFIGURATION:
 * 
 *    Each handler accepts:
 *    - Minimum log level (DEBUG, INFO, WARNING, ERROR, CRITICAL, etc.)
 *    - Bubble parameter (whether to pass to next handler)
 *    - Formatters (how to format the output)
 *    - Processors (add extra information)
 * 
 * 4. BUBBLING:
 *    - When bubble = true (default): message continues to next handler
 *    - When bubble = false: message stops at this handler
 * 
 * ============================================================================
 * PRACTICAL EXAMPLES:
 * ============================================================================
 * 
 * 1. Create a HandlerTest.php in tests directory
 * <?php
 * 
 * namespace Mukhoiran\MVCProject\Tests;
 * 
 * use Monolog\Handler\Handler;
 * use PHPUnit\Framework\TestCase;
 * use Monolog\Handler\StreamHandler;
 * use Monolog\Logger;
 * 
 * class HandlerTest extends TestCase
 * {
 *     public function testHandler(): void
 *     {
 *         $logger = new Logger(HandlerTest::class);
 * 
 *         $logger->pushHandler(new StreamHandler("php://stderr"));
 *         $logger->pushHandler(new StreamHandler(__DIR__ . '/../application.log'));
 *         //$logger->pushHandler(new SlackHandler());
 *         //$logger->pushHandler(new ElasticSearchHandler());
 * 
 *         self::assertNotNull($logger);
 *         self::assertCount(2, $logger->getHandlers());
 *     }
 * }
 * ?>
 * 
 * 2. Run the test to verify handlers are added correctly
 * vendor/bin/phpunit tests/HandlerTest.php
 * 
 */

/**
 * ============================================================================
 * HANDLER BEST PRACTICES:
 * ============================================================================
 * 
 * 1. Use appropriate log levels for each handler
 * 2. Keep production logs separate from debug logs
 * 3. Use RotatingFileHandler to prevent disk space issues
 * 4. Send critical errors to email/Slack for immediate attention
 * 5. Use different handlers for different environments (dev/staging/prod)
 * 6. Consider performance - file handlers are fast, email handlers are slow
 * 7. Set bubble=false when you want to prevent log duplication
 * 8. Use formatters to match your log aggregation tools
 * 
 * ============================================================================
 * COMMON HANDLER STACK PATTERN:
 * ============================================================================
 */