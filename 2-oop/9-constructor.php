<?php
// Summary: Constructors in PHP OOP

/*
A constructor is a special method in a PHP class that is automatically called when a new object is created from the class.

Key points:
- The constructor method is named __construct().
- It is used to initialize object properties or execute startup code.
- Constructors can accept parameters to set initial values.
- If a parent class has a constructor, the child class can call it using parent::__construct().

Example:
*/

class User {
    public $name;

    // Constructor
    public function __construct($name) {
        $this->name = $name;
    }
}

$user = new User("Alice");
echo $user->name; // Output: Alice

/*
Summary:
- Use __construct() to set up your objects.
- Constructors improve code readability and maintainability.
*/
?>