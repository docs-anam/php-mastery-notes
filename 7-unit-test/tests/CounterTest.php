<?php

namespace Mukhoiran\Test;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\RequiresPhp;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Assert;

class CounterTest extends TestCase {
    /**
     * Test the increment method of the Counter class.
     */
    public function testIncrement() {
        $counter = new Counter();

        $counter->increment();
        Assert::assertEquals(1, $counter->getCount());

        $counter->increment();
        $this->assertEquals(2, $counter->getCount());

        $counter->increment();
        self::assertEquals(3, $counter->getCount());
    }

    public function testOther(){
        $this->assertTrue(true);
    }

    #[Test]
    public function decrement_works_as_expected() {
        $counter = new Counter();
        $counter->increment();
        $counter->increment();
        $counter->decrement();
        $this->assertEquals(1, $counter->getCount());
    }

    public function testFirst(): Counter {
        $counter = new Counter();
        $counter->increment();
        $this->assertEquals(1, $counter->getCount());
        return $counter; // Return the counter instance for dependency
    }

    #[Depends('testFirst')]
    public function testSecond(Counter $counter): void {
        $counter->increment();
        $this->assertEquals(2, $counter->getCount());
    }

    //Incomplete test marking
    public function testThird(){
        $counter = new Counter();
        self::assertEquals(0, $counter->getCount());
        self::markTestIncomplete('This test is not completed yet');

        //code after mark will not be executed
    }

    //Skip test marking
    public function testFourth(){
        self::markTestSkipped('This test is skipped');

        //code after mark will not be executed
    }

    //Skip test based on condition with attribute RequiresPhp
    #[RequiresPhp('>= 8.0')]
    public function testFifth(){
        self::assertTrue(true, 'Only for PHP >= 8.0');
    }
}