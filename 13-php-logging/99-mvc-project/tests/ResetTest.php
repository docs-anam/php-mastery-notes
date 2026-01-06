<?php

namespace Mukhoiran\MVCProject\Tests;

use PHPUnit\Framework\TestCase;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\GitProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\HostnameProcessor;

class ResetTest extends TestCase
{
    public function testReset(): void
    {
        $logger = new Logger(ResetTest::class);
        $logger->pushHandler(new StreamHandler("php://stderr"));
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../application.log'));

        $logger->pushProcessor(new GitProcessor());
        $logger->pushProcessor(new MemoryUsageProcessor());
        $logger->pushProcessor(new HostnameProcessor());
        self::assertNotNull($logger);
        self::assertCount(2, $logger->getHandlers());
        self::assertCount(3, $logger->getProcessors());

        for ($i = 0; $i < 1000; $i++){
            $logger->info("Loop $i");
            if($i % 100 == 0){
                $logger->reset();
            }
        }

        self::assertNotNull($logger);
    }
}