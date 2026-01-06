<?php
/**
 * Overloading in PHP OOP - Detailed and Executable Summary
 *
 * In PHP, "overloading" refers to the dynamic creation and handling of properties and methods at runtime.
 * Unlike languages such as Java or C++, PHP does not support traditional method overloading
 * (i.e., multiple methods with the same name but different parameter lists).
 * Instead, PHP provides "magic methods" that allow you to intercept and define behavior for
 * accessing or modifying undefined or inaccessible properties and methods.
 *
 * Key Magic Methods for Overloading:
 *
 * 1. __get($name)
 *    - Invoked when reading data from inaccessible or non-existing properties.
 *    - Signature: public function __get(string $name): mixed
 *
 * 2. __set($name, $value)
 *    - Invoked when writing data to inaccessible or non-existing properties.
 *    - Signature: public function __set(string $name, mixed $value): void
 *
 * 3. __isset($name)
 *    - Invoked when isset() or empty() is called on inaccessible or non-existing properties.
 *    - Signature: public function __isset(string $name): bool
 *
 * 4. __unset($name)
 *    - Invoked when unset() is used on inaccessible or non-existing properties.
 *    - Signature: public function __unset(string $name): void
 *
 * 5. __call($name, $arguments)
 *    - Invoked when calling inaccessible or non-existing object methods.
 *    - Signature: public function __call(string $name, array $arguments): mixed
 *
 * 6. __callStatic($name, $arguments)
 *    - Invoked when calling inaccessible or non-existing static methods.
 *    - Signature: public static function __callStatic(string $name, array $arguments): mixed
 *
 * Example Implementation:
 */

class Demo
{
    private $data = [];

    // Handle setting inaccessible or undefined properties
    public function __set($name, $value)
    {
        echo "__set called: Setting '$name' to '$value'\n";
        $this->data[$name] = $value;
    }

    // Handle getting inaccessible or undefined properties
    public function __get($name)
    {
        echo "__get called: Getting '$name'\n";
        return $this->data[$name] ?? null;
    }

    // Handle isset() on inaccessible or undefined properties
    public function __isset($name)
    {
        echo "__isset called: Checking if '$name' is set\n";
        return isset($this->data[$name]);
    }

    // Handle unset() on inaccessible or undefined properties
    public function __unset($name)
    {
        echo "__unset called: Unsetting '$name'\n";
        unset($this->data[$name]);
    }

    // Handle calling inaccessible or undefined object methods
    public function __call($name, $arguments)
    {
        echo "__call called: Method '$name' with arguments: " . implode(', ', $arguments) . "\n";
        return "Dynamic method '$name' executed.";
    }

    // Handle calling inaccessible or undefined static methods
    public static function __callStatic($name, $arguments)
    {
        echo "__callStatic called: Static method '$name' with arguments: " . implode(', ', $arguments) . "\n";
        return "Dynamic static method '$name' executed.";
    }
}

// Demonstration
$obj = new Demo();

// Property overloading
$obj->foo = 'bar';         // __set triggered
echo $obj->foo . "\n";     // __get triggered

// isset and unset overloading
isset($obj->foo);          // __isset triggered
unset($obj->foo);          // __unset triggered
isset($obj->foo);          // __isset triggered again

// Method overloading
echo $obj->doSomething('a', 'b') . "\n"; // __call triggered

// Static method overloading
echo Demo::staticMethod('x', 'y') . "\n"; // __callStatic triggered

/**
 * Notes:
 * - Overloading in PHP is only possible with object context, except for __callStatic.
 * - It is useful for implementing dynamic properties, proxies, delegating calls, or lazy loading.
 * - Overuse can make code harder to understand, debug, and maintain.
 * - Magic methods must be public.
 * - Only properties and methods that are inaccessible (private/protected) or undefined will trigger these magic methods.
 */
?>