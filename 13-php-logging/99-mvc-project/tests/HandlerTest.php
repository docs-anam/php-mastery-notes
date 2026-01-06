<?php

namespace Mukhoiran\MVCProject\Tests;

use PHPUnit\Framework\TestCase;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class HandlerTest extends TestCase
{
    public function testHandler(): void
    {
        $logger = new Logger(HandlerTest::class);

        $logger->pushHandler(new StreamHandler("php://stderr"));
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../application.log'));
        //$logger->pushHandler(new SlackHandler());
        //$logger->pushHandler(new ElasticSearchHandler());

        self::assertNotNull($logger);
        self::assertCount(2, $logger->getHandlers());
    }
}