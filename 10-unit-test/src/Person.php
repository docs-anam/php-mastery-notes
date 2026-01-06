<?php

namespace Mukhoiran\Test;

class Person
{
    public function __construct(private string $name)
    {
    }

    public function sayHello(?string $name)
    {
        if ($name == null) throw new \Exception("Name is null");

        return "Hello $name, my name is $this->name";
    }

    public function sayGoodBye(?string $name)
    {
        echo "Goodbye $name" . PHP_EOL;
    }
}