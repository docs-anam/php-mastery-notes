<?php
/**
 * PHPUnit Data Provider Attribute Documentation
 *
 * Data providers in PHPUnit allow a single test method to be executed multiple times with different input data sets.
 * As of PHPUnit 10, the #[DataProvider] attribute is used to specify the data provider method for a test.
 *
 * Usage:
 * - Annotate your test method with #[DataProvider('providerMethodName')].
 * - The provider method returns an iterable (array or Traversable) of data sets.
 * - Each data set is passed as arguments to the test method.
 * - The test method runs once for each data set.
 *
 * Example:
 * #[DataProvider('additionProvider')]
 * public function testAdd($a, $b, $expected) { ... }
 *
 * Data Provider Method:
 * - Can be static or non-static.
 * - Returns an array of arrays, each representing a set of arguments for the test.
 *
 * Notes:
 * - The #[DataProvider] attribute replaces the legacy @dataProvider annotation.
 * - Multiple data providers can be used by stacking multiple attributes.
 * - Improves code clarity, IDE support, and static analysis.
 *
 * TestWidth Attribute:
 * - #[TestWith] is another PHPUnit attribute for providing inline data sets directly to a test method.
 * - Use #[TestWith([arg1, arg2, ...])] above the test method to specify arguments.
 * - Useful for simple cases where data sets are small and do not require a separate provider method.
 *
 * Benefits:
 * - Cleaner and more maintainable test code.
 * - Enhanced support for modern PHP features.
 * - Easier refactoring and better tooling integration.
 */

/**
 * Sample usage of attributes DataProvider and TestWith:
 *
 * 1. In the src folder, create a file `Math` to store class of Math.
 * <?php
 *  namespace Mukhoiran\Test;
 *
 *  class Math
 *  {
 *      public static function add($a, $b)
 *      {
 *          return $a + $b;
 *      }
 *
 *      public static function subtract($a, $b)
 *      {
 *          return $a - $b;
 *      }
 *
 *      public static function sum(array $numbers): int
 *      {
 *          return array_sum($numbers);
 *      }
 *  }
 * 2. In the tests folder, create a file 'MathTest.php' to store test cases for the Math class.
 * <?php
 * namespace Mukhoiran\Test;
 * 
 * use PHPUnit\Framework\Attributes\DataProvider;
 * use PHPUnit\Framework\Attributes\Test;
 * use PHPUnit\Framework\Attributes\TestWith;
 * use PHPUnit\Framework\TestCase;
 * 
 * class MathTest extends TestCase
 * {
 *     //without dataProvider
 *     public function testAddition()
 *     {
 *         $this->assertEquals(4, Math::add(2, 2));
 *         $this->assertEquals(0, Math::add(-1, 1));
 *         $this->assertEquals(-3, Math::add(-1, -2));
 *     }
 * }
 *
 * //without dataProvider
 * public function testSubtraction()
 * {
 *     $this->assertEquals(0, Math::subtract(2, 2));
 *     $this->assertEquals(-2, Math::subtract(-1, 1));
 *     $this->assertEquals(1, Math::subtract(-1, -2));
 * }
 *
 * //============ with DataProvider
 * #[DataProvider('mathSumData')]
 *     public function testDataProvider(array $values, int $expected)
 *     {
 *         self::assertEquals($expected, Math::sum($values));
 *     }

 *     public static function mathSumData(): array
 *     {
 *         return [
 *             [[1, 2, 3], 6],
 *             [[-1, 1], 0],
 *             [[5, 5, 5, 5], 20],
 *             [[0, 0, 0], 0],
 *         ];
 *     }
 *     //============ End

 *     //=========== with TestWith
 *     #[TestWith([[1, 2, 3], 6])]
 *     #[TestWith([[-1, 1], 0])]
 *     #[TestWith([[5, 5, 5, 5], 20])]
 *     #[TestWith([[0, 0, 0], 0])]
 *     public function testWith(array $values, int $expected): void
 *     {
 *         self::assertEquals($expected, Math::sum($values));
 *     }
 * }
 * 3. Run the tests using the PHPUnit command-line tool.
 *    vendor/bin/phpunit tests/MathTest.php
 * 4. Review the test output for results.
 */