<?php

namespace Mukhoiran\MVCProject\Tests;

use PHPUnit\Framework\TestCase;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ContextTest extends TestCase
{
    public function testContext(): void
    {
        $logger = new Logger(ContextTest::class);
        $logger->pushHandler(new StreamHandler("php://stderr"));

        $logger->info("User {username} logged in", ['username' => 'anam']);
        $logger->error("Error processing order {orderId}", ['orderId' => 12345, 'amount' => 250.75]);

        self::assertNotNull($logger);
    }
}