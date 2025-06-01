<?php
/*
Summary: "Allow ::class on objects" in PHP 8.0

In PHP 8.0, the ::class syntax was enhanced to allow its use on objects, not just class names. 
Previously, you could only use ::class with a class name (e.g., Foo::class), which would return 
the fully qualified class name as a string.

New in PHP 8.0:
- You can now use $object::class to get the class name of the object.
- This is equivalent to using get_class($object), but is more concise and readable.

Detailed Example:
*/

namespace MyApp;

class Animal {}
class Dog extends Animal {}

$animal = new Animal();
$dog = new Dog();

// Using ::class on class names (works in PHP 5.5+)
echo "Animal class name: " . Animal::class . PHP_EOL; // Outputs: MyApp\Animal
echo "Dog class name: " . Dog::class . PHP_EOL;       // Outputs: MyApp\Dog

// Using ::class on objects (PHP 8.0+)
echo "Class of \$animal: " . $animal::class . PHP_EOL; // Outputs: MyApp\Animal
echo "Class of \$dog: " . $dog::class . PHP_EOL;       // Outputs: MyApp\Dog

// Equivalent using get_class()
echo "Class of \$animal (get_class): " . get_class($animal) . PHP_EOL;
echo "Class of \$dog (get_class): " . get_class($dog) . PHP_EOL;

// Demonstrating with anonymous classes
$anon = new class extends Animal {};
echo "Anonymous class: " . $anon::class . PHP_EOL;

// Benefits:
// - Improved readability and consistency with static class references.
// - Useful for type checks, logging, and debugging.

// Backward Compatibility:
// - Using ::class on objects in PHP versions before 8.0 will result in a parse error.

// References:
// - RFC: https://wiki.php.net/rfc/class_name_literal_on_object
// - PHP Manual: https://www.php.net/manual/en/language.oop5.basic.php#language.oop5.basic.class.class
?>