<?php
/**
 * Inheritance in PHP OOP - Detailed Summary
 *
 * Inheritance is a fundamental concept in Object-Oriented Programming (OOP) that allows a class (child or subclass)
 * to inherit properties and methods from another class (parent or superclass). This promotes code reuse and establishes
 * a relationship between classes.
 *
 * Key Points:
 * 1. The 'extends' keyword is used for inheritance.
 * 2. The child class inherits all public and protected properties and methods from the parent.
 * 3. Private members of the parent are not accessible directly in the child.
 * 4. The child class can override parent methods to provide specific implementations.
 * 5. The 'parent::' keyword allows access to overridden parent methods or constructors.
 * 6. PHP supports only single inheritance (a class can extend only one parent).
 * 7. Abstract and final classes/methods can control inheritance behavior.
 *
 * Example:
 */

class Animal {
    public $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function speak() {
        echo "{$this->name} makes a sound.\n";
    }
}

class Dog extends Animal {
    public function speak() {
        echo "{$this->name} barks.\n";
    }
}

$animal = new Animal("Generic Animal");
$animal->speak(); // Output: Generic Animal makes a sound.

$dog = new Dog("Buddy");
$dog->speak(); // Output: Buddy barks.

/**
 * - The Dog class inherits the $name property and the constructor from Animal.
 * - Dog overrides the speak() method to provide its own implementation.
 * - If Dog did not override speak(), it would inherit Animal's version.
 *
 * Access Modifiers:
 * - public: accessible everywhere.
 * - protected: accessible in the class and its children.
 * - private: accessible only within the class itself.
 *
 * Abstract Classes:
 * - Cannot be instantiated.
 * - Can define abstract methods that must be implemented by child classes.
 *
 * Final Keyword:
 * - final class: cannot be extended.
 * - final method: cannot be overridden.
 */
?>