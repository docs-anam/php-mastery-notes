<?php
/**
 * Test database connection for a Login Management System
 *
 * Testing and verifying the database connection setup in a login management system.
 *
 * 1. Create a test script to verify the database connection in app/Tests/Config/DatabaseConnectionTest.php.
 * 2. Use the Database class to get a connection.
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Tests;
 * 
 * use PHPUnit\Framework\TestCase;
 * use Mukhoiran\LoginManagement\Config\Database;
 *
 * class DatabaseConnectionTest extends TestCase
 * {
 *     public function testGetConnection()
 *     {
 *         $connection = Database::getConnection();
 *         self::assertNotNull($connection);
 *     }
 * 
 *     public function testGetConnectionSingleton()
 *     {
 *         $connection1 = Database::getConnection();
 *         $connection2 = Database::getConnection();
 *         self::assertSame($connection1, $connection2);
 *     }
 * }
 * 
 * 3. Run the tests using PHPUnit.
 *   phpunit --bootstrap vendor/autoload.php tests/Config/DatabaseConnectionTest.php
 * 4. Verify that the tests pass, confirming the database connection is correctly set up.
 * This ensures that the database connection is reliable and ready for use in the login management system.
 */