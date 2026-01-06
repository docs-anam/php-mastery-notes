<?php

/**
 * 
 * Summary: Incomplete Test (PHP)
 * 
 * Marking a test as incomplete in unit testing (such as with PHPUnit in PHP) is useful when a test cannot be finished
 * due to missing functionality, external dependencies, or other reasons. It signals that the test is intentionally left
 * unfinished and should not be counted as a failure or success.
 *
 * Purpose:
 * - Indicates that the test is not fully implemented or cannot be executed under current conditions.
 * - Helps developers track unfinished tests and prevents false negatives in test reports.
 *
 * How to Mark Incomplete in PHPUnit:
 * - Use the $this->markTestIncomplete() method inside your test method.
 * - This will stop the test execution and mark it as incomplete.
 *
 * Typical Use Cases:
 * - Awaiting implementation of a feature.
 * - External service or dependency is unavailable.
 * - Test logic is pending clarification.
 *
 * Effect on Test Results:
 * - Incomplete tests are reported separately in the test summary.
 * - They do not count as passed or failed, but as incomplete.
 *
 * Best Practices:
 * - Always provide a meaningful message explaining why the test is incomplete.
 * - Review incomplete tests regularly and update them as the codebase evolves.
 *
 * Example:
 * <?php
 * use PHPUnit\Framework\TestCase;
 *
 * class MyTest extends TestCase
 * {
 *         public function testFeature()
 *         {
 *                 // Feature not implemented yet
 *                 $this->markTestIncomplete('This test has not been implemented yet.');
 *         }
 * }
 * 
 * Marking tests as incomplete helps maintain transparency in the development process and ensures that unfinished tests
 * are not overlooked.
 */

/**
 * Working with markTestIncomplete in the existing file 'CounterTest.php':
 *
 * 1. In the `CounterTest` class, add new function with marked as incomplete.
 * <?php
 * 
 * 
 * namespace Mukhoiran\Test;
 * 
 * use PHPUnit\Framework\Attributes\Test;
 * use PHPUnit\Framework\Attributes\Depends;
 * use PHPUnit\Framework\TestCase;
 * use PHPUnit\Framework\Assert;
 *
 * class CounterTest extends TestCase {
 *   
 *    .....
 * 
 *     //Incomplete test marking
 *     public function testThird(){
 *         $counter = new Counter();
 *         self::assertEquals(0, $counter->getCount());
 *         self::markTestIncomplete('This test is not completed yet');
 *     }
 * }
 * 2. Run the tests using the PHPUnit command-line tool.
 *    vendor/bin/phpunit tests/CounterTest.php
 * 3. Review the test output for results.
 */

