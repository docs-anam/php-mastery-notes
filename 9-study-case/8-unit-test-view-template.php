<?php
/**
 * Testing View Template Rendering
 * 
 * This test case verifies that the View class correctly renders templates with dynamic content.
 * It checks for the presence of specific strings in the output to ensure proper rendering.
 * 
 * 1. Create the test file in tests/App/ViewTest.php.
 * <?php
 *
 * namespace Mukhoiran\LoginManagement\Tests\App;
 *
 * use Mukhoiran\LoginManagement\App\View;
 * use PHPUnit\Framework\TestCase;
 * 
 * class ViewTest extends TestCase
 * {
 *     public function testRender()
 *     {
 *         View::render('Home/index', [
 *             "PHP Login Management"
 *         ]);
 *
 *         $this->expectOutputRegex('[PHP Login Management]');
 *         $this->expectOutputRegex('[html]');
 *         $this->expectOutputRegex('[body]');
 *         $this->expectOutputRegex('[Login Management]');
 *         $this->expectOutputRegex('[Login]');
 *         $this->expectOutputRegex('[Register]');
 *     }
 * }
 * ?>
 *
 * 2. To run this test, use PHPUnit in the terminal:
 * vendor/bin/phpunit tests/App/ViewTest.php
 * 
 * 3. Ensure that the View class and templates are set up correctly to pass this test.
 * 
 */