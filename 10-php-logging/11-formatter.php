<?php

/**
 * MONOLOG FORMATTERS - SUMMARY
 * 
 * Formatters in Monolog control how log records are formatted before being written to handlers.
 * Each handler can have its own formatter to customize the output format.
 * 
 * COMMON FORMATTERS:
 * 
 * 1. LineFormatter (default for StreamHandler)
 *    - Formats logs as single-line or multi-line text
 *    - Customizable format string with placeholders
 *    - Example: "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
 * 
 * 2. JsonFormatter
 *    - Formats logs as JSON objects
 *    - Useful for log aggregation systems and structured logging
 *    - Each log entry becomes a JSON document
 * 
 * 3. HtmlFormatter
 *    - Formats logs as HTML tables
 *    - Useful for email handlers or web-based log viewers
 * 
 * 4. NormalizerFormatter
 *    - Base formatter that normalizes data structures
 *    - Converts objects, resources, and exceptions to arrays
 * 
 * 5. ScalarFormatter
 *    - Formats logs as simple scalar values
 *    - Removes context and extra data
 * 
 * 6. LogstashFormatter
 *    - Formats logs for Logstash (ELK stack)
 *    - JSON format with specific field names
 * 
 * 7. GelfMessageFormatter
 *    - Formats logs for Graylog Extended Log Format (GELF)
 * 
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
 * use Monolog\Formatter\JsonFormatter;
 * use Monolog\Handler\StreamHandler;
 * use Monolog\Logger;
 * use Monolog\Processor\GitProcessor;
 * use Monolog\Processor\HostnameProcessor;
 * use Monolog\Processor\MemoryUsageProcessor;
 * 
 * class FormatterTest extends TestCase
 * {
 *     public function testFormatter()
 *     {
 *         $logger = new Logger(FormatterTest::class);
 * 
 *         $handler = new StreamHandler("php://stderr");
 *         $handler->setFormatter(new JsonFormatter());
 * 
 *         $logger->pushHandler($handler);
 *         $logger->pushProcessor(new GitProcessor());
 *         $logger->pushProcessor(new MemoryUsageProcessor());
 *         $logger->pushProcessor(new HostnameProcessor());
 * 
 *         $logger->info("Learn PHP Logging", ["username" => "anam"]);
 *         $logger->info("Learn PHP Logging Again", ["username" => "anam", "module" => "logging"]);
 * 
 *         self::assertNotNull($logger);
 *     }
 * }
 * ?>
 * 
 * 2. Run the test to verify formatter functionality
 * vendor/bin/phpunit tests/FormatterTest.php
 */

/**
 * KEY FEATURES:
 * 
 * - Each handler can have a different formatter
 * - Formatters are chainable and reusable
 * - Custom formatters can be created by extending FormatterInterface
 * - Formatters can include/exclude context and extra data
 * - Date/time formatting is customizable
 * 
 * BEST PRACTICES:
 * 
 * - Use JsonFormatter for centralized logging systems
 * - Use LineFormatter for human-readable logs
 * - Use HtmlFormatter for email notifications
 * - Customize format strings to match your logging standards
 * - Keep formats consistent across your application
 */