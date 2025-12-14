<?php

namespace Mukhoiran\MVCProject\Tests;

use PHPUnit\Framework\TestCase;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Level;

class RotatingFileTest extends TestCase
{
    public function testRotating()
    {
        $logger = new Logger(RotatingFileTest::class);
        $logger->pushHandler(new StreamHandler("php://stderr"));
        $logger->pushHandler(new RotatingFileHandler(__DIR__ . '/../app.log', 10, Level::Info));

        $logger->info("learn PHP");
        $logger->info("learn PHP OOP");
        $logger->info("learn PHP Web");
        $logger->info("learn PHP Database");

        self::assertNotNull($logger);
    }

}