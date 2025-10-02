<?php
/**
 * PHPUnit Attributes Summary
 *
 * PHPUnit attributes are a modern way to configure tests using PHP's native attribute syntax (introduced in PHP 8).
 * They replace traditional docblock annotations, providing type safety and better IDE support.
 *
 * Common PHPUnit Attributes:
 *
 * 1. #[Test]
 *    Marks a method as a test case.
 *    Example:
 *      #[Test]
 *      public function testSomething() { ... }
 *
 * 2. #[DataProvider]
 *    Specifies a method that provides data sets for parameterized tests.
 *    Example:
 *      #[DataProvider('providerMethod')]
 *      public function testWithData($input, $expected) { ... }
 *
 * 3. #[Depends]
 *    Indicates that a test depends on the result of another test.
 *    Example:
 *      #[Depends('testSomething')]
 *      public function testDependent() { ... }
 *
 * 4. #[Covers]
 *    Specifies which code is covered by the test.
 *    Example:
 *      #[Covers(SomeClass::someMethod)]
 *      public function testCoverage() { ... }
 *
 * 5. #[Group]
 *    Assigns a test to one or more groups for selective execution.
 *    Example:
 *      #[Group('database')]
 *      public function testDatabase() { ... }
 *
 * 6. #[Requires]
 *    Sets requirements for the test (PHP version, extensions, OS, etc.).
 *    Example:
 *      #[RequiresPhp('8.1')]
 *      public function testPhp81Feature() { ... }
 *
 * Usage Notes:
 * - Attributes are placed directly above methods or classes.
 * - They must be imported via `use` statements if not in the global namespace.
 * - PHPUnit will automatically detect and apply attributes during test execution.
 *
 * Benefits:
 * - Improved readability and maintainability.
 * - IDEs can provide better autocomplete and error checking.
 * - Attributes are part of the language, not comments.
 */

/**
 * Continuing usage of attributes based on the previous code in the 3-assertions.php notes:
 *
 * 1. In the `CounterTest` class, attributes can be used to provide metadata about the tests.
 * <?php
 *  namespace Mukhoiran\Test;
 * 
 *  use PHPUnit\Framework\Attributes\Test;
 *  use PHPUnit\Framework\TestCase;
 *  use PHPUnit\Framework\Assert;
 *  
 *  class CounterTest extends TestCase {
 *      .......
 *      // Example of using an attribute for a test method
 *      #[Test]
 *      public function decrement_works_as_expected() {
 *          $counter = new Counter();
 *          $counter->increment();
 *          $counter->increment();
 *          $counter->decrement();
 *          $this->assertEquals(1, $counter->getCount());
 *      }
 *  }
 * 2. Run the tests using the PHPUnit command-line tool.
 *    vendor/bin/phpunit --filter 'CounterTest::decrement_works_as_expected' tests/CounterTest.php
 * 3. Review the test output for results.
 */