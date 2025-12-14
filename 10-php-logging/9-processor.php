<?php

/**
 * MONOLOG PROCESSORS - DETAILED SUMMARY
 * 
 * Processors in Monolog are callables that add extra information to log records.
 * They modify the log record array before it's passed to handlers.
 * 
 * KEY CONCEPTS:
 * 
 * 1. WHAT ARE PROCESSORS?
 *    - Functions or classes that enrich log records with additional context
 *    - Execute BEFORE handlers process the record
 *    - Receive the entire log record array and return a modified version
 *    - Can add memory usage, user info, request data, tags, etc.
 * 
 * 2. HOW PROCESSORS WORK:
 *    - They receive the log record as an array
 *    - Add or modify data in the 'extra' key
 *    - Return the modified record
 *    - Execution order: Record created → Processors → Handlers → Formatters
 * 
 * 3. BUILT-IN PROCESSORS:
 *    - IntrospectionProcessor: Adds file, line, class, and function info
 *    - WebProcessor: Adds web server and request info (URL, IP, method)
 *    - MemoryUsageProcessor: Adds current memory usage
 *    - MemoryPeakUsageProcessor: Adds peak memory usage
 *    - ProcessIdProcessor: Adds process ID
 *    - UidProcessor: Adds a unique identifier to each log entry
 *    - GitProcessor: Adds git branch and commit info
 *    - HostnameProcessor: Adds hostname
 *    - TagProcessor: Adds custom tags
 * 
 * 4. PROCESSOR LEVELS:
 *    - Can be attached to Logger (affects ALL handlers)
 *    - Can be attached to specific Handler (affects only that handler)
 * 
 * 5. CUSTOM PROCESSORS:
 *    - Can be a closure/function
 *    - Can be an invokable class with __invoke method
 *    - Must accept array $record and return array
 */

/**
 * EXAMPLE USAGE:
 * 
 * 1. Create a ProcessorTest in tests directory
 * <?php
 * 
 * namespace Mukhoiran\MVCProject\Tests;
 * 
 * use PHPUnit\Framework\TestCase;
 * use Monolog\Handler\StreamHandler;
 * use Monolog\Logger;
 * use Monolog\Processor\GitProcessor;
 * use Monolog\Processor\MemoryUsageProcessor;
 * use Monolog\Processor\HostnameProcessor;
 * 
 * class ProcessorTest extends TestCase
 * {
 *     public function testProcessor(): void
 *     {
 *         $logger = new Logger(ProcessorTest::class);
 *         $logger->pushHandler(new StreamHandler("php://stderr"));
 * 
 *         // Adding Processors
 *         $logger->pushProcessor(new MemoryUsageProcessor());
 *         $logger->pushProcessor(new HostnameProcessor());
 *         $logger->pushProcessor(new GitProcessor());
 *         $logger->pushProcessor(function ($record) {
 *             $record['extra']['custom_info'] = 'Custom Processor Info';
 *             return $record;
 *         });
 * 
 *         $logger->info("Testing processors in Monolog");
 * 
 *         self::assertNotNull($logger);
 *         self::assertCount(4, $logger->getProcessors());
 *     }
 * }
 * ?>
 * 
 * 2. Run the test to verify processors functionality
 * vendor/bin/phpunit tests/ProcessorTest.php
 */

/**
 * 
 * BEST PRACTICES:
 * 
 * 1. Use processors for automatic context addition
 * 2. Keep processors lightweight (they run on every log)
 * 3. Attach heavy processors only to specific handlers
 * 4. Use built-in processors when possible
 * 5. Custom processors should focus on single responsibility
 * 6. Be mindful of processor order (LIFO execution)
 * 7. Use processors for cross-cutting concerns (user tracking, performance)
 */