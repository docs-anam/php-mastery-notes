<?php

namespace Mukhoiran\MVCProject\Tests;

use PHPUnit\Framework\TestCase;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\GitProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\HostnameProcessor;

class ProcessorTest extends TestCase
{
    public function testProcessor(): void
    {
        $logger = new Logger(ProcessorTest::class);
        $logger->pushHandler(new StreamHandler("php://stderr"));

        // Adding Processors
        $logger->pushProcessor(new MemoryUsageProcessor());
        $logger->pushProcessor(new HostnameProcessor());
        $logger->pushProcessor(new GitProcessor());
        $logger->pushProcessor(function ($record) {
            $record['extra']['custom_info'] = 'Custom Processor Info';
            return $record;
        });

        $logger->info("Testing processors in Monolog");

        self::assertNotNull($logger);
        self::assertCount(4, $logger->getProcessors());
    }
}