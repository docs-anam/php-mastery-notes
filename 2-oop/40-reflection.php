<?php
/**
 * PHP Reflection in OOP - Detailed Summary
 *
 * Reflection is a powerful feature in PHP that allows you to inspect and manipulate classes,
 * interfaces, functions, methods, and extensions at runtime. It is part of the Reflection API,
 * which is especially useful for debugging, documentation, testing, and building frameworks.
 *
 * Key Concepts:
 * 1. Reflection Classes:
 *    - ReflectionClass: Inspect classes, their properties, methods, constants, etc.
 *    - ReflectionMethod: Inspect and invoke class methods.
 *    - ReflectionProperty: Inspect and manipulate class properties.
 *    - ReflectionFunction: Inspect functions.
 *    - ReflectionParameter: Inspect function/method parameters.
 *    - ReflectionObject: Like ReflectionClass, but for instantiated objects.
 *
 * 2. Common Use Cases:
 *    - Getting class/method/property information at runtime.
 *    - Instantiating objects dynamically.
 *    - Accessing private/protected members.
 *    - Generating documentation or code analysis tools.
 *    - Building dependency injection containers.
 *
 * 3. Example Usage:
 */

// Example class
class User {
    private $name;
    public function __construct($name) { $this->name = $name; }
    public function getName() { return $this->name; }
}

// Inspecting the class
$reflection = new ReflectionClass('User');

// Get class name
echo "Class: " . $reflection->getName() . PHP_EOL;

// Get constructor
$constructor = $reflection->getConstructor();
echo "Constructor: " . $constructor->getName() . PHP_EOL;

// Get properties
$properties = $reflection->getProperties();
echo "Properties: ";
foreach ($properties as $property) {
    echo $property->getName() . " ";
}
echo PHP_EOL;

// Get methods
$methods = $reflection->getMethods();
echo "Methods: ";
foreach ($methods as $method) {
    echo $method->getName() . " ";
}
echo PHP_EOL;

// Instantiating an object dynamically
$instance = $reflection->newInstance('Alice');
echo "Instance name: " . $instance->getName() . PHP_EOL;

// Accessing private property
$prop = $reflection->getProperty('name');
$prop->setAccessible(true);
$prop->setValue($instance, 'Bob');
echo "Modified name: " . $instance->getName() . PHP_EOL;

/**
 * 4. Security Note:
 *    - Reflection can bypass visibility (private/protected), so use it carefully.
 *
 * 5. Documentation:
 *    - Official: https://www.php.net/manual/en/book.reflection.php
 */
?>