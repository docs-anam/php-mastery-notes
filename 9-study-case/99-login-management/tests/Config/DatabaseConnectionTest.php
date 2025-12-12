<?php

namespace Mukhoiran\LoginManagement\Tests;

use PHPUnit\Framework\TestCase;
use Mukhoiran\LoginManagement\Config\Database;

class DatabaseConnectionTest extends TestCase
{
    public function testGetConnection()
    {
        $connection = Database::getConnection();
        self::assertNotNull($connection);
    }

    public function testGetConnectionSingleton()
    {
        $connection1 = Database::getConnection();
        $connection2 = Database::getConnection();
        self::assertSame($connection1, $connection2);
    }
}