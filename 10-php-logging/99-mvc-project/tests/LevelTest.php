<?php

namespace Mukhoiran\MVCProject\Tests;

use PHPUnit\Framework\TestCase;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LevelTest extends TestCase
{
    public function testLevel(): void
    {
        $logger = new Logger(LevelTest::class);

        $logger->pushHandler(new StreamHandler("php://stderr"));
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../application.log'));
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../error.log', Logger::ERROR));

        $logger->debug("This is a debug message");
        $logger->info("This is an info message");
        $logger->notice("This is a notice message");
        $logger->warning("This is a warning message");
        $logger->error("This is an error message");
        $logger->critical("This is a critical message");
        $logger->alert("This is an alert message");
        $logger->emergency("This is an emergency message");

        self::assertNotNull($logger);
    }
}