<?php
// Basic Summary: Classes in PHP

/*
A class in PHP is a blueprint for objects. It groups related data (properties) and actions (methods).

- Properties: Variables inside a class.
- Methods: Functions inside a class.
- Objects: Created from classes using 'new'.

Class Rules:
- Class names are case-insensitive.
- Class names cannot start with a number.
- Use 'public', 'protected', or 'private' to set visibility of properties and methods.
- Use $this to access properties and methods inside the class.
- Use 'new' keyword to create an object from a class.
*/

// Define a simple class
class Animal {
    public $name;

    // Method to set the name
    public function setName($name) {
        $this->name = $name;
    }

    // Method to get the name
    public function getName() {
        return $this->name;
    }
}

// Create an object
$dog = new Animal();
$dog->setName("Jerry"); // Change the name
// Output
echo $dog->getName(); // Jerry
?>
