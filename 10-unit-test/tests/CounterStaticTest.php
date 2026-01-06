<?php

namespace Mukhoiran\Test;

use PHPUnit\Framework\TestCase;

class CounterStaticTest extends TestCase
{

    public static Counter $counter;

    public static function setUpBeforeClass(): void
    {
        self::$counter = new Counter();
    }

    public function testFirst()
    {
        self::$counter->increment();
        self::assertEquals(1, self::$counter->getCount());
    }

    public function testSecond()
    {
        self::$counter->increment();
        self::assertEquals(2, self::$counter->getCount());
    }

    public static function tearDownAfterClass(): void
    {
        echo "Unit Test Finish" . PHP_EOL;
    }

}