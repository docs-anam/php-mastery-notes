<?php
/*
 * Summary: Setting up Composer and Running Basic Unit Tests in PHP
 *
 * 1. Install Composer:
 *    - Download Composer from https://getcomposer.org/download/
 *    - Install globally or locally as per instructions.
 *    - Verify installation: `composer --version`
 *
 * 2. Initialize Composer in your project:
 *    - Navigate to your project directory.
 *    - Run: `composer init`
 *    - Follow prompts to set up composer.json.
 *
 * 3. Require PHPUnit (Unit Testing Framework):
 *    - Run: `composer require --dev phpunit/phpunit`
 *    - This installs PHPUnit as a development dependency.
 *
 * 4. Setup Autoloading:
 *    - Update `composer.json` to include autoloading:
 *      ```json
 *      {
 *          "autoload": {
 *              "psr-4": {
 *                  "Mukhoiran\\Test\\": "src/"
 *              }
 *          },
 *          "autoload-dev": {
 *              "psr-4": {
 *                  "Mukhoiran\\Test\\": "tests/"
 *              }
 *          }
 *      }
 *      ```
 *    - Run: `composer dump-autoload`
 *
 * 5. Create folder src and tests:
 *    - Run: `mkdir src tests`
 *
 * 6. Create a Simple Class to Test:
 *    - Example: Create `src/Counter.php`
 *      <?php
 *      namespace Mukhoiran\Test;
 *
 *      class Counter {
 *          private int $count = 0;
 *
 *          public function increment() {
 *              $this->count++;
 *          }
 *      }

 *      public function getCount(): int {
 *          return $this->count;
 *      }
 *  }
 *
 * 7. Create a Test Class:
 *    - Example: Create `tests/CounterTest.php`
 *      <?php
 *          namespace Mukhoiran\Test;
 *
 *           use PHPUnit\Framework\TestCase;
 *
 *           class CounterTest extends TestCase {
 *               public function testIncrement() {
 *                   $counter = new Counter();
 *                   $counter->increment();
 *                   $counter->increment();
 *                   echo $counter->getCount();
 *               }
 *
 *               public function testOther(){
 *                   echo "Other";
 *               }
 *           }
 *
 * 8. Run PHPUnit Tests:
 *    - Run: `vendor/bin/phpunit tests/CounterTest.php`
 *    - PHPUnit will execute all test cases in the `CounterTest` class.
 *    - You should see output indicating the number of tests run and their status.
 *
 * 9. Run PHPUnit Tests per Method
 *    - Run: `vendor/bin/phpunit --filter 'CounterTest::testOther' tests/CounterTest.php`
 *    - This will only run the `testOther` method in the `CounterTest` class.
 *    - You can similarly run other test methods by changing the filter.
 *
 * This process allows you to set up Composer, install PHPUnit, write a basic test, and run it to verify your code.
 */