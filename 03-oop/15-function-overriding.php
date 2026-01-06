<?php
// Function Overriding in PHP OOP

/**
 * Function overriding occurs when a child class defines a method with the same name,
 * parameters, and signature as a method in its parent class.
 * The child class's method "overrides" the parent class's method.
 */

// Parent class
class Animal {
    public function makeSound() {
        echo "The animal makes a sound.\n";
    }
}

// Child class overriding the makeSound() method
class Dog extends Animal {
    public function makeSound() {
        echo "The dog barks.\n";
    }
}

// Another child class
class Cat extends Animal {
    public function makeSound() {
        echo "The cat meows.\n";
    }
}

// Usage
$animal = new Animal();
$animal->makeSound(); // Output: The animal makes a sound.

$dog = new Dog();
$dog->makeSound(); // Output: The dog barks.

$cat = new Cat();
$cat->makeSound(); // Output: The cat meows.

/**
 * Notes:
 * - The method signature (name and parameters) must be the same in both parent and child.
 * - The overridden method in the child class replaces the parent's implementation.
 * - You can still call the parent method using parent::methodName().
 */

class Bird extends Animal {
    public function makeSound() {
        parent::makeSound(); // Call parent method
        echo "The bird chirps.\n";
    }
}

$bird = new Bird();
$bird->makeSound(); 
// Output:
// The animal makes a sound.
// The bird chirps.

/**
 * Function overriding is a key concept in achieving polymorphism in OOP.
 */
?>