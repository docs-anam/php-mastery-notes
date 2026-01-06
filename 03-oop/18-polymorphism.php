<?php
/**
 * Polymorphism in PHP OOP - Detailed Summary
 *
 * Polymorphism is a core concept in Object-Oriented Programming (OOP) that allows objects of different classes
 * to be treated as objects of a common parent class. It enables a single interface to represent different underlying forms (data types).
 *
 * In PHP, polymorphism is typically achieved through:
 *   1. Inheritance
 *   2. Interfaces
 *   3. Abstract Classes
 *
 * Key Points:
 * - Polymorphism allows you to write code that works on the superclass/interface, but can use any subclass implementation.
 * - It promotes code reusability and flexibility.
 * - Methods with the same name can behave differently in different classes.
 *
 * Example using inheritance and method overriding:
 */

class Animal {
    public function makeSound() {
        echo "Some generic animal sound\n";
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

// Polymorphic function
function animalSound(Animal $animal) {
    $animal->makeSound();
}

$dog = new Dog();
$cat = new Cat();

animalSound($dog); // Output: Woof!
animalSound($cat); // Output: Meow!

// Using polymorphism with an array of different animal types
$animals = [new Dog(), new Cat(), new Animal()];

foreach ($animals as $animal) {
    $animal->makeSound();
}
// Output:
// Woof!
// Meow!
// Some generic animal sound

/**
 * Example using interfaces:
 */

interface Shape {
    public function draw();
}

class Circle implements Shape {
    public function draw() {
        echo "Drawing a circle\n";
    }
}

class Square implements Shape {
    public function draw() {
        echo "Drawing a square\n";
    }
}

function drawShape(Shape $shape) {
    $shape->draw();
}

$circle = new Circle();
$square = new Square();

drawShape($circle); // Output: Drawing a circle
drawShape($square); // Output: Drawing a square

/**
 * Summary:
 * - Polymorphism allows objects of different classes to be treated as instances of the same parent class/interface.
 * - It is achieved via method overriding (inheritance) or implementing interfaces.
 * - It enables flexible and maintainable code.
 */
?>