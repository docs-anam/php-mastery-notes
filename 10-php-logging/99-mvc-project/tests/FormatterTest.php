<?php

namespace Mukhoiran\MVCProject\Tests;

use PHPUnit\Framework\TestCase;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\GitProcessor;
use Monolog\Processor\HostnameProcessor;
use Monolog\Processor\MemoryUsageProcessor;

class FormatterTest extends TestCase
{
    public function testFormatter()
    {
        $logger = new Logger(FormatterTest::class);

        $handler = new StreamHandler("php://stderr");
        $handler->setFormatter(new JsonFormatter());

        $logger->pushHandler($handler);
        $logger->pushProcessor(new GitProcessor());
        $logger->pushProcessor(new MemoryUsageProcessor());
        $logger->pushProcessor(new HostnameProcessor());

        $logger->info("Learn PHP Logging", ["username" => "anam"]);
        $logger->info("Learn PHP Logging Again", ["username" => "anam", "module" => "logging"]);

        self::assertNotNull($logger);
    }
}