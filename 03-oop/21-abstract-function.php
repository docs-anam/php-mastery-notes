<?php
// Abstract Function in PHP OOP - Detailed Example

// Abstract classes are base classes that cannot be instantiated directly.
// They are designed to be inherited by other classes.
// Abstract classes can contain both abstract methods (without implementation)
// and concrete methods (with implementation).

// Define an abstract class called Animal
abstract class Animal {
    // Abstract method: must be implemented by any subclass
    // No method body here
    abstract public function makeSound();

    // Abstract method with parameters
    abstract public function move($distance);

    // Concrete method: can be used by subclasses as is
    public function eat() {
        echo get_class($this) . " is eating.\n";
    }

    // Concrete method: can be overridden by subclasses
    public function sleep() {
        echo get_class($this) . " is sleeping.\n";
    }
} 

// Dog class extends Animal and must implement all abstract methods
class Dog extends Animal {
    // Implement makeSound() method
    public function makeSound() {
        echo "Dog says: Woof!\n";
    }

    // Implement move() method
    public function move($distance) {
        echo "Dog runs {$distance} meters.\n";
    }

    // Optionally override sleep() method
    public function sleep() {
        echo "Dog curls up and sleeps.\n";
    }
}

// Cat class extends Animal and must implement all abstract methods
class Cat extends Animal {
    public function makeSound() {
        echo "Cat says: Meow!\n";
    }

    public function move($distance) {
        echo "Cat walks {$distance} meters gracefully.\n";
    }
    // Inherits sleep() and eat() from Animal
}

// Usage examples

// $animal = new Animal(); // Error: Cannot instantiate abstract class

$dog = new Dog();
$dog->makeSound(); // Output: Dog says: Woof!
$dog->move(10);    // Output: Dog runs 10 meters.
$dog->eat();       // Output: Dog is eating.
$dog->sleep();     // Output: Dog curls up and sleeps.

$cat = new Cat();
$cat->makeSound(); // Output: Cat says: Meow!
$cat->move(5);     // Output: Cat walks 5 meters gracefully.
$cat->eat();       // Output: Cat is eating.
$cat->sleep();     // Output: Cat is sleeping.