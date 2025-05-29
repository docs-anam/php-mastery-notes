<?php
// Summary: Visibility in PHP OOP

/*
 * In PHP Object-Oriented Programming, visibility (or access modifiers) controls
 * how and where class properties and methods can be accessed.
 *
 * There are three visibility types:
 *
 * 1. public    - Accessible from anywhere (inside or outside the class).
 * 2. protected - Accessible only within the class itself and by inheriting (child) classes.
 * 3. private   - Accessible only within the class that defines it.
 *
 * Example:
 */

class Example {
    public $publicVar = 'Public';
    protected $protectedVar = 'Protected';
    private $privateVar = 'Private';

    public function showVars() {
        echo $this->publicVar;    // OK
        echo $this->protectedVar; // OK
        echo $this->privateVar;   // OK
    }
}

class ChildExample extends Example {
    public function showProtected() {
        echo $this->protectedVar; // OK
        // echo $this->privateVar; // Error: privateVar is not accessible
    }
}

$obj = new Example();
echo $obj->publicVar;    // OK
// echo $obj->protectedVar; // Error
// echo $obj->privateVar;   // Error

/*
 * Summary:
 * - Use public for members that should be accessible everywhere.
 * - Use protected for members that should be accessible in the class and subclasses.
 * - Use private for members that should only be accessible in the defining class.
 */
?>