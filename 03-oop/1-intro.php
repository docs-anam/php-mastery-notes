<?php
// Introduction to Object-Oriented Programming (OOP) in PHP

/*
Object-Oriented Programming (OOP) is a programming paradigm that organizes code into objects, which are instances of classes.
OOP in PHP allows you to model real-world entities using classes, encapsulate data, and define behaviors through methods.

Key OOP Concepts in PHP:
1. Class: A blueprint for creating objects.
2. Object: An instance of a class.
3. Property: A variable inside a class.
4. Method: A function inside a class.
5. Inheritance: Allows a class to inherit properties and methods from another class.
6. Encapsulation: Restricts direct access to object data and methods.
7. Polymorphism: Allows objects to be treated as instances of their parent class.

Example:
*/

class Car {
    public $brand;
    public $color;

    public function drive() {
        echo "Driving a $this->color $this->brand car.";
    }
}

$myCar = new Car();
$myCar->brand = "Toyota";
$myCar->color = "red";
$myCar->drive(); // Output: Driving a red Toyota car.