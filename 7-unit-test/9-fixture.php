<?php
/**
 * Summary: Fixture in Unit Testing (PHP)
 *
 * In unit testing, a "fixture" refers to the fixed state of a set of objects used as a baseline for running tests.
 * Fixtures help ensure tests are repeatable and reliable by providing known inputs and environments.
 *
 * Detailed Points:
 * 1. Purpose:
 *    - To set up the environment required for a test (e.g., database records, files, objects).
 *    - To ensure tests are isolated and not affected by external factors.
 *
 * 2. Types:
 *    - Setup Fixtures: Prepare the environment before each test (e.g., create objects, seed database).
 *    - Tear Down Fixtures: Clean up after each test (e.g., delete files, remove database records).
 *
 * 3. Usage in PHP:
 *    - PHPUnit uses setUp() and tearDown() methods for fixtures.
 *    - setUp(): Called before each test method to initialize the test environment.
 *    - tearDown(): Called after each test method to clean up.
 *
 * 4. Example:
 *    class MyTest extends PHPUnit\Framework\TestCase {
 *        protected $object;
 *
 *        protected function setUp(): void {
 *            $this->object = new MyClass();
 *        }
 *
 *        protected function tearDown(): void {
 *            unset($this->object);
 *        }
 *    }
 *
 * 5. Benefits:
 *    - Consistency: Tests run with predictable data.
 *    - Isolation: Each test runs independently.
 *    - Maintainability: Easier to manage test data and environment.
 *
 * 6. Advanced Fixtures:
 *    - Data Providers: Supply multiple sets of data to a test.
 *    - Database Fixtures: Use transactions or seeders to manage test data.
 *
 * In summary, fixtures are essential for reliable, repeatable, and maintainable unit tests in PHP.
 */

/**
 * Continuing usage of expectOutputString based on the previous code in the 7-test-exception.php notes:
 *
 * 1. In the `PersonTest` class, modify the function by using fixtures.
 * <?php
 *
 * namespace Mukhoiran\Test;
 *
 * use PHPUnit\Framework\TestCase;
 * use PHPUnit\Framework\Attributes\Before;
 * use PHPUnit\Framework\Attributes\After;
 *
 * class PersonTest extends TestCase
 * {
 *    private Person $person;
 *
 *    //======= Fixture setup options
 *    // option 1
 *    protected function setUp(): void
 *    {}
 *
 *    // option 2
 *    #[Before]
 *    public function createPerson()
 *    {
 *        $this->person = new Person("Anam");
 *    }
 *
 *    public function testSuccess()
 *    {
 *        self::assertEquals("Hello Jhon, my name is Anam", $this->person->sayHello("Jhon"));
 *    }
 * 
 *    public function testException()
 *    {
 *        $this->expectException(\Exception::class);
 *        $this->person->sayHello(null);
 *    }
 *
 *    public function testGoodbyeSuccess()
 *    {
 *        $this->expectOutputString("Goodbye Jhon" . PHP_EOL);
 *        $this->person->sayGoodbye("Jhon");
 *    }
 *
 *    // ======= Fixture teardown options
 *    // option 1
 *    protected function tearDown(): void
 *    {}
 *
 *    // option 2
 *    #[After]
 *    public function cleanup()
 *    {
 *        unset($this->person);
 *    }
 *}
 * 2. Run the tests using the PHPUnit command-line tool.
 *    vendor/bin/phpunit tests/PersonTest.php
 * 3. Review the test output for results.
 */