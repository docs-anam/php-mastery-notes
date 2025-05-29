<?php
// Summary: Functions in Object-Oriented Programming (OOP)

// In OOP, functions defined inside classes are called "methods".
// Methods define the behavior of objects created from a class.

// Example class with methods:
class Car {
    public $color;

    // Regular method
    public function drive() {
        echo "The {$this->color} car is driving.";
    }

    // Static method (can be called without creating an object)
    public static function honk() {
        echo "Beep beep!";
    }
}

// Creating an object and setting its property
$myCar = new Car();
$myCar->color = 'red';
echo $myCar->drive(); // Output: The red car is driving.
echo "\n";
// Calling a static method
echo Car::honk(); // Output: Beep beep!

/*
Key Points:
- Methods are functions inside classes.
- Use $this to access properties and other methods.
- Methods can have different visibility: public, protected, private.
- Static methods belong to the class, not to any object.
*/
?>
