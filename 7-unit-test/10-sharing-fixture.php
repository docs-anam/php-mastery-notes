<?php
/**
 * 
 * Summary: Fixture in Unit Testing (PHP)
 * 
 * Sharing fixture in unit testing refers to the practice of reusing setup code (test fixtures)
 * across multiple test methods or classes. This approach helps avoid duplication, ensures consistency,
 * and improves test performance by reducing repeated setup and teardown operations.
 *
 * Detailed Summary:
 * 1. What is a Fixture?
 *    - A fixture is the fixed state of a set of objects used as a baseline for running tests.
 *      It typically involves creating objects, setting up databases, or configuring environments.
 *
 * 2. Why Share Fixtures?
 *    - Reduces code duplication.
 *    - Ensures all tests use the same initial state.
 *    - Speeds up tests by avoiding repeated setup.
 *
 * 3. How to Share Fixtures in PHPUnit?
 *    - Use setUp() and tearDown() methods for per-test setup/teardown.
 *    - Use setUpBeforeClass() and tearDownAfterClass() for class-wide setup/teardown.
 *    - Use data providers for parameterized tests.
 *
 * 4. Example Usage:
 *    - Shared fixtures can be implemented by creating resources (e.g., database connections)
 *      in setUpBeforeClass(), and cleaning up or resetting state in setUp().
 *      
 *      <?php
 *      class UserTest extends TestCase
 *      {
 *         protected static $db;
 *
 *         public static function setUpBeforeClass(): void
 *         {
 *              // Shared fixture: create a database connection once for all tests
 *              self::$db = new PDO('sqlite::memory:');
 *              self::$db->exec("CREATE TABLE users (id INT, name TEXT)");
 *         }
 *
 *         public function setUp(): void
 *         {
 *              // Per-test fixture: clean up table before each test
 *              self::$db->exec("DELETE FROM users");
 *         }
 *
 *         public function testAddUser()
 *         {
 *              self::$db->exec("INSERT INTO users VALUES (1, 'Alice')");
 *              $stmt = self::$db->query("SELECT COUNT(*) FROM users");
 *              $this->assertEquals(1, $stmt->fetchColumn());
 *         }
 *
 *         public function testNoUsersInitially()
 *         {
 *              $stmt = self::$db->query("SELECT COUNT(*) FROM users");
 *              $this->assertEquals(0, $stmt->fetchColumn());
 *         }
 *      }
 *
 * 5. Best Practices:
 *    - Keep shared fixtures immutable if possible.
 *    - Clean up shared resources to avoid side effects.
 *    - Use shared fixtures only when setup is expensive or complex.
 *
 * Conclusion:
 * Sharing fixtures makes tests faster, cleaner, and more maintainable, but requires careful management
 * to avoid unwanted interactions between tests.
 */

/**
 * Working with setUpBeforeClass and tearDownAfterClass() based on the previous code in the 9-fixture.php notes:
 *
 * 1. In the tests folder, create new class with name `CounterStaticTest.php`.
 * <?php
 * 
 * namespace Mukhoiran\Test;
 * 
 * use PHPUnit\Framework\TestCase;
 * 
 * class CounterStaticTest extends TestCase
 * {
 * 
 *     public static Counter $counter;
 *
 *     public static function setUpBeforeClass(): void
 *     {
 *         self::$counter = new Counter();
 *     }
 *
 *     public function testFirst()
 *     {
 *         self::$counter->increment();
 *         self::assertEquals(1, self::$counter->getCount());
 *     }
 *
 *     public function testSecond()
 *     {
 *         self::$counter->increment();
 *         self::assertEquals(2, self::$counter->getCount());
 *     }
 *
 *     public static function tearDownAfterClass(): void
 *     {
 *         echo "Unit Test Finish" . PHP_EOL;
 *     }
 * 
 * }
 * 2. Run the tests using the PHPUnit command-line tool.
 *    vendor/bin/phpunit tests/CounterStaticTest.php
 * 3. Review the test output for results.
 */
