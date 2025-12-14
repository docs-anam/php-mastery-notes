<?php

/**
 * CONTEXT IN MONOLOG - DETAILED SUMMARY
 * 
 * Context is additional data passed to log methods as an associative array.
 * It provides extra information about the event being logged without cluttering
 * the main message.
 * 
 * KEY CONCEPTS:
 * 
 * 1. BASIC CONTEXT USAGE
 *    - Second parameter to all log methods (debug, info, warning, error, etc.)
 *    - Associative array of key-value pairs
 *    - Can contain any serializable data
 * 
 * 2. CONTEXT BENEFITS
 *    - Structured logging for better searchability
 *    - Separate data from message for clarity
 *    - Enable filtering and analysis in log management systems
 *    - Machine-readable alongside human-readable messages
 * 
 * 3. CONTEXT PLACEHOLDERS
 *    - Use {key} syntax in messages
 *    - Automatically replaced with context values
 *    - Follows PSR-3 standard
 * 
 * 4. COMMON CONTEXT DATA
 *    - User information (user_id, username, email)
 *    - Request data (ip_address, url, method)
 *    - Application state (environment, version)
 *    - Business logic data (order_id, transaction_id)
 *    - Exception objects (special 'exception' key)
 * 
 * 5. PROCESSORS FOR CONTEXT
 *    - Automatically add context to all records
 *    - IntrospectionProcessor: file, line, class, function
 *    - WebProcessor: url, ip, http_method, server, referrer
 *    - GitProcessor: branch, commit
 *    - Custom processors for application-specific data
 * 
 * 6. FORMATTERS AND CONTEXT
 *    - LineFormatter: includes context in brackets
 *    - JsonFormatter: context as separate JSON field
 *    - HtmlFormatter: context in HTML table
 */

/**
 * Example
 * 
 * 1. Create a ContextTest.php in tests directory
 * <?php
 * 
 * namespace Mukhoiran\MVCProject\Tests;
 * 
 * use PHPUnit\Framework\TestCase;
 * use Monolog\Handler\StreamHandler;
 * use Monolog\Logger;
 * 
 * class ContextTest extends TestCase
 * {
 *     public function testContext(): void
 *     {
 *         $logger = new Logger(ContextTest::class);
 *         $logger->pushHandler(new StreamHandler("php://stderr"));
 * 
 *         $logger->info("User {username} logged in", ['username' => 'anam']);
 *         $logger->error("Error processing order {orderId}", ['orderId' => 12345, 'amount' => 250.75]);
 * 
 *         self::assertNotNull($logger);
 *     }
 * }
 * ?>
 * 
 * 2. Run the test to verify logging with context
 * vendor/bin/phpunit tests/ContextTest.php
 */