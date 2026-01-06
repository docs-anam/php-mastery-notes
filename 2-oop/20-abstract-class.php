<?php
/**
 * Abstract Classes in PHP OOP - Detailed Summary
 *
 * 1. Definition:
 *    - An abstract class is a class that cannot be instantiated directly.
 *    - It is declared using the `abstract` keyword.
 *    - Abstract classes are meant to be extended by other classes.
 *
 * 2. Abstract Methods:
 *    - An abstract class can have abstract methods.
 *    - Abstract methods are declared, but not implemented in the abstract class.
 *    - Any class extending the abstract class must implement all its abstract methods.
 *
 * 3. Usage:
 *    - Abstract classes are used to provide a common base and enforce a contract for subclasses.
 *    - They can contain both abstract methods (without body) and concrete methods (with body).
 *
 * 4. Syntax Example:
 */

abstract class Animal {
    // Abstract method (must be implemented by child classes)
    abstract public function makeSound();

    // Concrete method (can be used as is or overridden)
    public function eat() {
        echo "This animal is eating.\n";
    }
}

class Dog extends Animal {
    public function makeSound() {
        echo "Woof!\n";
    }
}

class Cat extends Animal {
    public function makeSound() {
        echo "Meow!\n";
    }
}

// $animal = new Animal(); // This will cause an error because you cannot instantiate an abstract class.
// Usage
$dog = new Dog();
$dog->makeSound(); // Output: Woof!
$dog->eat();       // Output: This animal is eating.

$cat = new Cat();
$cat->makeSound(); // Output: Meow!
$cat->eat();       // Output: This animal is eating.

/**
 * 5. Key Points:
 *    - You cannot create an instance of an abstract class.
 *    - Child classes must implement all abstract methods.
 *    - Abstract classes can have properties and concrete methods.
 *    - Abstract methods cannot have a body.
 *    - Abstract classes help in defining a template for other classes.
 */
?>