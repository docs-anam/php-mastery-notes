<?php
/**
 * PHPUnit Test Dependencies Summary (using attributes)
 *
 * In PHPUnit 10+, you can use PHP attributes instead of annotations to declare dependencies between test methods.
 * The #[Depends] attribute specifies that a test method depends on the result of another.
 *
 * How #[Depends] Works:
 * - The dependent test will only run if the test it depends on passes.
 * - The result (return value) of the depended-on test is passed as an argument to the dependent test.
 * - If the depended-on test fails, is skipped, or is marked incomplete, the dependent test is skipped.
 *
 * Example Usage:
 * 
 * use PHPUnit\Framework\Attributes\Depends;
 * use PHPUnit\Framework\TestCase;
 * 
 * class ExampleTest extends TestCase
 * {
 *     public function testA()
 *     {
 *         // Some setup or calculation
 *         return 'foo';
 *     }
 *
 *     #[Depends('testA')]
 *     public function testB($valueFromA)
 *     {
 *         $this->assertEquals('foo', $valueFromA);
 *     }
 * }
 *
 * Multiple Dependencies:
 * - You can depend on multiple tests by specifying multiple #[Depends] attributes.
 * - The dependent test will receive multiple arguments, in the order of the dependencies.
 * 
 *     #[Depends('testA')]
 *     #[Depends('testC')]
 *     public function testD($valueFromA, $valueFromC) { ... }
 *
 * Limitations:
 * - Dependencies only work within the same test class.
 * - The depended-on test must return a value; otherwise, null is passed.
 * - Circular dependencies are not allowed.
 *
 * Use Cases:
 * - Chained setup and validation.
 * - Sharing expensive setup between tests.
 * - Ensuring logical test order when required.
 */

/**
 * Continuing usage of attributes based on the previous code in the 4-attributes.php notes:
 *
 * 1. In the `CounterTest` class, attributes can be used to provide metadata about the tests.
 * <?php
 *  namespace Mukhoiran\Test;
 * 
 *  use PHPUnit\Framework\Attributes\Test;
 *  use PHPUnit\Framework\Attributes\Depends;
 *  use PHPUnit\Framework\TestCase;
 *  use PHPUnit\Framework\Assert;
 *  
 *  class CounterTest extends TestCase {
 *      .......
 *      #[Depends('testFirst')]
 *      public function testSecond(Counter $counter): void {
 *          $counter->increment();
 *          $this->assertEquals(2, $counter->getCount());
 *      }
 *  }
 * 2. Run the tests using the PHPUnit command-line tool.
 *    vendor/bin/phpunit tests/CounterTest.php
 * 3. Review the test output for results.
 */