<?php


namespace Mukhoiran\Test;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\After;

class PersonTest extends TestCase
{
    private Person $person;

    //======= Fixture setup options
    // option 1
    protected function setUp(): void
    {}

    // option 2
    #[Before]
    public function createPerson()
    {
        $this->person = new Person("Anam");
    }


    public function testSuccess()
    {
        self::assertEquals("Hello Jhon, my name is Anam", $this->person->sayHello("Jhon"));
    }

    public function testException()
    {
        $this->expectException(\Exception::class);
        $this->person->sayHello(null);
    }

    public function testGoodbyeSuccess()
    {
        $this->expectOutputString("Goodbye Jhon" . PHP_EOL);
        $this->person->sayGoodbye("Jhon");
    }

    // ======= Fixture teardown options
    // option 1
    protected function tearDown(): void
    {}

    // option 2
    #[After]
    public function cleanup()
    {
        unset($this->person);
    }
}