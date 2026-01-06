<?php
/*
Summary: Destructor in PHP OOP

- A destructor is a special method in a class that is automatically called when an object is destroyed or goes out of scope (e.g., at the end of a script or when unset).
- In PHP, the destructor method is named __destruct().
- Destructors are commonly used to perform cleanup tasks, such as:
    - Closing file handles or database connections
    - Releasing system resources (memory, sockets, etc.)
    - Saving object state or logging information before the object is removed from memory
- Only one destructor is allowed per class, and it cannot accept any arguments.
- If a parent class defines a destructor, a child class can override it. To ensure the parent destructor is also executed, call parent::__destruct() inside the child’s destructor.
- Destructors are called in the reverse order of object creation.
- If an object is part of a circular reference, the destructor may not be called immediately, but will be called when the garbage collector removes the object.

Example:
*/

class MyClass {
    private $resource;

    public function __construct() {
        $this->resource = fopen('php://memory', 'r+');
        echo "Object created. Resource opened.\n";
    }

    public function __destruct() {
        if (is_resource($this->resource)) {
            fclose($this->resource);
            echo "Resource closed. ";
        }
        echo "Object destroyed.\n";
    }
}

$obj = new MyClass();
var_dump($obj); // Display object details
// When the script ends or $obj is unset, __destruct() is called automatically.
// unset($obj); // Uncomment to trigger destructor immediately