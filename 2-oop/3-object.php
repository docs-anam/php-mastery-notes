<?php
// Summary: Objects in PHP OOP

/*
An object is an instance of a class. It represents a real-world entity with properties (attributes) and behaviors (methods).
Objects are created from classes, which act as blueprints.

Key Points:
- Objects encapsulate data and functions.
- Each object has its own copy of properties.
- Methods can manipulate object data.
- Objects enable code reusability and modularity.

Rules of Objects:
1. Objects are always created from a class using the new keyword.
2. Each object has its own set of properties (unless static).
3. Objects can access their properties and methods using the -> operator.
4. Objects can interact with other objects.
5. Objects are assigned by reference by default in PHP.
6. Constructors (__construct) are used to initialize object properties.
7. Object properties and methods can have different visibility: public, protected, or private.

Example:
*/

// Define a class (blueprint)
class Animal {
    public $name;

    public function makeSound() {
        echo "{$this->name} makes a sound.";
    }
}

// Create an object of Animal
$myAnimal = new Animal();
$myAnimal->name = "Leo";

// Access object property and method
echo $myAnimal->name; // Output: Leo
echo "\n";
$myAnimal->makeSound(); // Output: Leo makes a sound.

/*
Summary:
- $myAnimal is an object of the Animal class.
- It has its own name property.
- The makeSound() method uses the object's data.
*/
?>