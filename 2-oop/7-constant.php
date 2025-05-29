<?php

/*
Summary: Constants in PHP OOP

- Constants are class properties whose values cannot be changed once defined.
- They are declared using the `const` keyword inside a class.
- Constants are always public and accessed using the scope resolution operator (::).
- They do not require a `$` sign before their name.
- Constants are shared across all instances of the class (static).
- Example:
*/

class MyClass {
    const MY_CONSTANT = 'Hello, World!';
}

// Accessing the constant
echo MyClass::MY_CONSTANT; // Outputs: Hello, World!

//- Constants can also be inherited by child classes.
