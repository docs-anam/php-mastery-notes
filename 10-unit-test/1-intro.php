<?php
/**
 * Introduction to PHP Unit Testing
 *
 * Unit testing is a software testing technique where individual units or components of a program are tested in isolation.
 * In PHP, the most popular framework for unit testing is PHPUnit.
 *
 * Why Unit Test?
 * - Ensures code correctness by verifying that each part works as intended.
 * - Helps catch bugs early in the development process.
 * - Facilitates code refactoring and maintenance.
 * - Provides documentation for how code is expected to behave.
 *
 * What is PHPUnit?
 * - PHPUnit is a programmer-oriented testing framework for PHP.
 * - It allows you to write test cases as PHP classes that extend PHPUnit\Framework\TestCase.
 * - Each test method typically tests a single aspect of your code.
 *
 * Basic Concepts:
 * - Test Case: A class containing test methods.
 * - Assertion: A statement that checks if a condition is true (e.g., assertEquals, assertTrue).
 * - Test Suite: A collection of test cases.
 *
 * Example Workflow:
 * 1. Install PHPUnit (commonly via Composer).
 * 2. Write test classes in a `tests` directory.
 * 3. Run tests using the PHPUnit command-line tool.
 * 4. Review results and fix any failing tests.
 *
 * Example Test:
 *   class CalculatorTest extends PHPUnit\Framework\TestCase {
 *       public function testAdd() {
 *           $calc = new Calculator();
 *           $this->assertEquals(4, $calc->add(2, 2));
 *       }
 *   }
 *
 * Summary:
 * - Unit testing with PHPUnit is essential for robust PHP applications.
 * - It improves code quality, reliability, and maintainability.
 * - Start small, write tests for critical code, and expand coverage over time.
 *
 *
 * Setting Up PHPUnit with Composer:
 * 1. Initialize Composer in your project directory:
 *    composer init
 *
 * 2. Require PHPUnit as a development dependency: 
 *    composer require --dev phpunit/phpunit
 *
 * 3. Your composer.json will look like this:
 */
// {
//     "name": "your-vendor/php-mastery-notes",
//     "description": "PHP Unit Test Example Project",
//     "require-dev": {
//         "phpunit/phpunit": "^10.0"
//     },
//     "autoload": {
//         "psr-4": {
//             "App\\": "src/"
//         }
//     },
//     "autoload-dev": {
//         "psr-4": {
//             "Tests\\": "tests/"
//         }
//     }
// }