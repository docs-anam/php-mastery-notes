<?php

namespace Mukhoiran\MVCProject\Tests;

use PHPUnit\Framework\TestCase;
use Monolog\Logger;

class LoggerTest extends TestCase
{
    public function testLogger(): void
    {
        $logger = new Logger("Mukhoiran");

        self::assertNotNull($logger);
    }

    public function testLoggerWithName(): void
    {
        $logger = new Logger(LoggerTest::class);

        self::assertNotNull($logger);
    }
}