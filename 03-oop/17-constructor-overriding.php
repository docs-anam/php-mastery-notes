<?php
/*
Summary: Constructor Overriding in PHP OOP

In PHP, constructor overriding occurs when a child class defines its own __construct() method, replacing the parent class's constructor.
When an object of the child class is created, the child’s constructor is called instead of the parent’s.
If you want to call the parent’s constructor as well, you must explicitly invoke it using parent::__construct().

Example:
*/
class ParentClass {
    public function __construct() {
        echo "Parent constructor\n";
    }
}

class ChildClass extends ParentClass {
    public function __construct() {
        parent::__construct(); // Optional: calls the parent constructor
        echo "Child constructor\n";
    }
}

$obj = new ChildClass();
// Output:
// Parent constructor
// Child constructor

// Key Points:
// - Child constructors override parent constructors.
// - Use parent::__construct() to call the parent’s constructor from the child.
// - If the child does not define a constructor, the parent’s constructor is used.

//Another Example with Parameters:
class Animal {
    public function __construct($name) {
        echo "Animal: $name\n";
    }
}

class Dog extends Animal {
    public function __construct($name, $breed) {
        parent::__construct($name); // Call parent constructor with $name
        echo "Dog breed: $breed\n";
    }
}

$dog = new Dog("Buddy", "Golden Retriever");
// Output:
// Animal: Buddy
// Dog breed: Golden Retriever