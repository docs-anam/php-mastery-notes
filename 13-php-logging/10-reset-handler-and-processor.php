<?php

/**
 * MONOLOG: RESET HANDLER AND PROCESSOR
 * =====================================
 * 
 * The reset() method is crucial in long-running PHP processes (daemons, workers, etc.)
 * to prevent memory leaks and ensure clean state between operations.
 * 
 * WHY RESET?
 * ----------
 * - Handlers and processors can accumulate state (buffers, connections, resources)
 * - Long-running processes need to clear this state periodically
 * - Prevents memory leaks in queue workers, cron jobs, and daemons
 * 
 * RESETTABLE INTERFACE
 * -------------------
 * Handlers/Processors implementing Monolog\ResettableInterface must provide reset()
 * 
 * WHAT GETS RESET?
 * ---------------
 * 1. HANDLERS:
 *    - BufferHandler: Clears buffered log records
 *    - FingersCrossedHandler: Resets activation state
 *    - GroupHandler: Resets all nested handlers
 *    - StreamHandler: Can flush and clear internal buffers
 * 
 * 2. PROCESSORS:
 *    - UidProcessor: Generates new UIDs
 *    - MemoryProcessor: Updates memory readings
 *    - Custom processors: Clear any accumulated state
 * 
 * COMMON USE CASES
 * ---------------
 * - After processing each message in a queue worker
 * - Between test cases in unit tests
 * - After handling each request in async servers
 * - Periodic cleanup in long-running daemons
 */

/**
 * EXAMPLE USAGE:
 * 
 * 1. Create a ResetTest in tests directory
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
 * 
 * class ResetTest extends TestCase
 * {
 *     public function testReset(): void
 *     {
 *         $logger = new Logger(ResetTest::class);
 *         $logger->pushHandler(new StreamHandler("php://stderr"));
 *         $logger->pushHandler(new StreamHandler(__DIR__ . '/../application.log'));
 *     
 *         $logger->pushProcessor(new GitProcessor());
 *         $logger->pushProcessor(new MemoryUsageProcessor());
 *         $logger->pushProcessor(new HostnameProcessor());
 *         self::assertNotNull($logger);
 *         self::assertCount(2, $logger->getHandlers());
 *         self::assertCount(3, $logger->getProcessors());
 *
 *         for ($i = 0; $i < 1000; $i++){
 *             $logger->info("Loop $i");
 *             if($i % 100 == 0){
 *                 $logger->reset();
 *             }
 *         }
 *
 *         self::assertNotNull($logger);
 *     }
 * }
 * ?>
 * 
 * 2. Run the test to verify reset functionality
 * vendor/bin/phpunit tests/ResetTest.php
 */

/**
 * BEST PRACTICES
 * ==============
 * 
 * 1. Always reset in long-running processes after completing logical units of work
 * 2. Reset between test cases to ensure isolation
 * 3. Implement ResettableInterface in custom handlers/processors
 * 4. Reset doesn't close handlers - use close() for that
 * 5. Reset is safe to call multiple times
 * 
 * COMMON PITFALLS
 * ==============
 * 
 * 1. Forgetting to reset in workers can cause memory leaks
 * 2. Not all handlers implement reset (check documentation)
 * 3. Reset clears state but doesn't flush - flush manually if needed
 * 4. Reset affects all handlers/processors - be aware of side effects
 */
