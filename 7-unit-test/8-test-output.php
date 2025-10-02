<?php
/**
 * Summary: Test Output in PHPUnit
 *
 * `expectOutputString()` is a PHPUnit method used in unit testing to verify that the output produced by PHP code matches an expected string.
 *
 * Purpose:
 *   Ensures that the output generated during the test matches the string you specify.
 *
 * Usage:
 *   Call `$this->expectOutputString('expected output');` before executing the code that produces output.
 *
 * How it works:
 *   PHPUnit captures all output (e.g., from `echo`, `print`, etc.) during the test. After the test runs, it compares the captured output to the expected string.
 *
 * Example:
 *   $this->expectOutputString('Hello, World!');
 *   echo 'Hello, World!';
 *
 * Benefits:
 *   - Useful for testing output of controllers, views, or any code that prints directly.
 *   - Helps ensure that the output is exactly as expected, including whitespace and formatting.
 *
 * Limitations:
 *   - Only works for output generated during the test method.
 *   - Does not check for output before or after the test method.
 *
 * Related Methods:
 *   - `expectOutputRegex($pattern)` — Asserts that the output matches a regular expression.
 *
 * Best Practices:
 *   - Use for testing output, not return values.
 *   - Keep expected output strings precise to avoid false negatives due to formatting differences.
 *
 * Summary:
 *   `expectOutputString()` is a simple yet powerful tool in PHPUnit for asserting that your code produces the correct output, making it ideal for testing scripts, templates, and CLI tools.
 */

/**
 * Continuing usage of expectOutputString based on the previous code in the 7-test-exception.php notes:
 *
 * 1. In the `PersonTest` class, add new function 'testGoodbyeSuccess()'.
 * <?php
 * namespace Mukhoiran\Test;
 *
 * use PHPUnit\Framework\TestCase;
 *
 * class PersonTest extends TestCase
 * {
 *    public function testSuccess()
 *    {
 *        $person = new Person("Anam");
 *        self::assertEquals("Hello Jhon, my name is Anam", $person->sayHello("Jhon"));
 *    }
 *
 *    public function testException()
 *    {
 *        $person = new Person("Anam");
 *        $this->expectException(\Exception::class);
 *        $person->sayHello(null);
 *    }
 *
 *    public function testGoodbyeSuccess()
 *    {
 *        $person = new Person("Anam");
 *        $this::expectOutputString("Goodbye Jhon" . PHP_EOL);
 *        $person->sayGoodbye("Jhon");
 *    }
 * }
 * 2. Run the tests using the PHPUnit command-line tool.
 *    vendor/bin/phpunit tests/PersonTest.php
 * 3. Review the test output for results.
 */