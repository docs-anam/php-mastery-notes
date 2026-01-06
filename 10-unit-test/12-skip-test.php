<?php
/*
 * Summary: Skipping Tests in PHPUnit
 *
 * PHPUnit provides mechanisms to skip tests when certain conditions are not met, ensuring that test results remain accurate and meaningful.
 *
 * 1. Dynamic Skipping with markTestSkipped():
 *    - You can skip a test during execution by calling $this->markTestSkipped('Reason').
 *    - Common use cases include missing PHP extensions, unavailable external services, or specific environment configurations.
 *    - Skipped tests are reported as "skipped" rather than "failed", avoiding false negatives.
 *
 * 2. Skipping via Attributes (PHPUnit 10+):
 *    - PHPUnit supports attributes such as #[RequiresPhp], #[RequiresPhpExtension], #[RequiresOperatingSystem], and #[RequiresFunction].
 *    - These attributes allow you to declaratively specify prerequisites for tests.
 *    - If the requirements are not met, PHPUnit automatically skips the test.
 *    - Example:
 *      #[RequiresPhp('8.1')]
 *      #[RequiresPhpExtension('curl')]
 *      public function testFeature() { ... }
 *
 * Example:
 * public function testFeature()
 * {
 *     if (!extension_loaded('curl')) {
 *         $this->markTestSkipped('cURL extension is not available.');
 *     }
 *     // Test code here...
 * }
 * 
 * Benefits:
 * - Skipping tests helps maintain reliable test suites by preventing failures due to unmet prerequisites.
 * - It provides clear feedback about why a test was not executed.
 * - Supports both dynamic and declarative approaches for flexibility.
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
 *     //Skip test marking
 *     public function testFourth(){
 *         self::markTestSkipped('This test is skipped');
 *
 *         //code after mark will not be executed
 *     }
 *
 *     //Skip test based on condition with attribute RequiresPhp
 *     #[RequiresPhp('>= 8.0')]
 *     public function testFifth(){
 *         self::assertTrue(true, 'Only for PHP >= 8.0');
 *     }
 * }
 * 2. Run the tests using the PHPUnit command-line tool.
 *    vendor/bin/phpunit tests/CounterTest.php
 * 3. Review the test output for results.
 */

