<?php
/**
 * PHPUnit Assertions Detailed Summary
 *
 * Assertions are core methods in PHPUnit used to verify that code behaves as expected.
 * They are called within test methods and help ensure code correctness, stability, and reliability.
 * If an assertion fails, PHPUnit marks the test as failed and provides diagnostic information.
 *
 * Common Assertions (with details and examples):
 *
 * 1. assertEquals($expected, $actual[, $message = ''])
 *    - Checks if two values are equal using ==.
 *    - Useful for comparing numbers, strings, arrays, etc.
 *    - Example: $this->assertEquals(4, 2 + 2);
 *
 * 2. assertSame($expected, $actual[, $message = ''])
 *    - Checks if two values are identical using === (same type and value).
 *    - Example: $this->assertSame(4, 4); // true
 *              $this->assertSame('4', 4); // false
 *
 * 3. assertTrue($condition[, $message = ''])
 *    - Checks if a condition evaluates to true.
 *    - Example: $this->assertTrue(is_array([]));
 *
 * 4. assertFalse($condition[, $message = ''])
 *    - Checks if a condition evaluates to false.
 *    - Example: $this->assertFalse(empty([1]));
 *
 * 5. assertNull($variable[, $message = ''])
 *    - Checks if a variable is null.
 *    - Example: $this->assertNull($result);
 *
 * 6. assertEmpty($variable[, $message = ''])
 *    - Checks if a variable is empty (empty string, array, etc.).
 *    - Example: $this->assertEmpty('');
 *
 * 7. assertCount($expectedCount, $arrayOrCountable[, $message = ''])
 *    - Checks the number of elements in an array or Countable object.
 *    - Example: $this->assertCount(3, [1,2,3]);
 *
 * 8. assertInstanceOf($class, $object[, $message = ''])
 *    - Checks if an object is an instance of a given class/interface.
 *    - Example: $this->assertInstanceOf(DateTime::class, $dt);
 *
 * 9. assertArrayHasKey($key, $array[, $message = ''])
 *    - Checks if an array contains a specific key.
 *    - Example: $this->assertArrayHasKey('name', ['name' => 'John']);
 *
 * 10. assertContains($needle, $haystack[, $message = ''])
 *     - Checks if a value exists in an array or substring in a string.
 *     - Example: $this->assertContains('apple', ['apple', 'banana']);
 *               $this->assertContains('foo', 'foobar');
 *
 * 11. assertGreaterThan($expected, $actual[, $message = ''])
 *     - Checks if actual is greater than expected.
 *     - Example: $this->assertGreaterThan(10, 15);
 *
 * 12. assertLessThan($expected, $actual[, $message = ''])
 *     - Checks if actual is less than expected.
 *     - Example: $this->assertLessThan(10, 5);
 *
 * 13. assertThrows(Exception::class, function() { ... }[, $message = ''])
 *     - Checks if a specific exception is thrown (PHPUnit 10+).
 *     - Example:
 *         $this->assertThrows(InvalidArgumentException::class, function() {
 *             throw new InvalidArgumentException();
 *         });
 *
 * Additional Useful Assertions:
 * - assertNotEquals($expected, $actual): Checks values are not equal.
 * - assertNotSame($expected, $actual): Checks values are not identical.
 * - assertNotNull($variable): Checks variable is not null.
 * - assertNotEmpty($variable): Checks variable is not empty.
 * - assertArrayNotHasKey($key, $array): Checks array does not have key.
 * - assertStringStartsWith($prefix, $string): Checks string starts with prefix.
 * - assertStringEndsWith($suffix, $string): Checks string ends with suffix.
 *
 * Usage Example:
 *   public function testAddition() {
 *       $result = 2 + 2;
 *       $this->assertEquals(4, $result, 'Addition should result in 4');
 *   }
 *
 * Assertions are the foundation of automated testing in PHPUnit.
 * They help catch regressions, verify logic, and document expected behavior.
 * 
 */

/** 
 * Continuing usage of assertions based on the previous code in the 2-create-unit-test.php notes:
 * * 1. In the `CounterTest` class, assertions can be used to verify the behavior of the `Counter` class.
 *  <?php
 *      namespace Mukhoiran\Test;
 *      use PHPUnit\Framework\TestCase;
 *      use PHPUnit\Framework\Assert;
 *      
 *      class CounterTest extends TestCase {
 *          public function testIncrement() {
 *              $counter = new Counter();
 *
 *              $counter->increment();
 *              Assert::assertEquals(1, $counter->getCount());
 *
 *              $counter->increment();
 *              $this->assertEquals(2, $counter->getCount());
 *
 *              $counter->increment();
 *              self::assertEquals(3, $counter->getCount());
 *          }
 *
 *          public function testOther(){
 *              $this->assertTrue(true);
 *          }
 *      }
 * * 2. Run the tests using PHPUnit.
 *   You can do this from the command line:
 *   ```
 *   ./vendor/bin/phpunit tests
 *   ```
 *   You can also run a specific test file:
 *   ```
 *   ./vendor/bin/phpunit tests/CounterTest.php
 *   ```
 * * 3. See the results in the terminal.
 *   You should see output indicating whether the tests passed or failed.
 *
 */