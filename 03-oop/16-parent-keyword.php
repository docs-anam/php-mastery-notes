<?php

/**
 * Summary: The `parent` Keyword in PHP OOP
 *
 * In PHP Object-Oriented Programming, the `parent` keyword is used to access methods or properties
 * from a parent (or base) class within a child (or derived) class. This is especially useful when
 * you override a method in the child class but still want to call the original implementation from
 * the parent class.
 *
 * Common uses:
 * - Calling a parent class's constructor: parent::__construct()
 * - Calling a parent class's method: parent::methodName()
 * - Accessing a parent class's static property or method: parent::$property or parent::staticMethod()
 *
 * Example:
 */

class Animal {
    public function speak() {
        echo "Animal speaks\n";
    }
}

class Dog extends Animal {
    public function speak() {
        parent::speak(); // Calls Animal's speak()
        echo "Dog barks\n";
    }
}

$dog = new Dog();
$dog->speak(); // Output: Animal speaks
               //         Dog barks