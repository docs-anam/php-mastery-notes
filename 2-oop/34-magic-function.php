<?php
/**
 * Summary: Magic Functions in PHP OOP
 *
 * Magic functions (also called magic methods) in PHP are special methods that start with double underscores (__).
 * They are automatically called in response to certain events, such as object creation, property access, or method calls.
 * Magic functions allow you to customize the behavior of your objects.
 *
 * Common Magic Functions:
 * 1. __construct()   - Called when an object is created.
 * 2. __destruct()    - Called when an object is destroyed.
 * 3. __get()         - Called when reading inaccessible or non-existent properties.
 * 4. __set()         - Called when writing to inaccessible or non-existent properties.
 * 5. __call()        - Called when invoking inaccessible or non-existent methods.
 * 6. __callStatic()  - Called for inaccessible or non-existent static methods.
 * 7. __isset()       - Called when isset() or empty() is used on inaccessible properties.
 * 8. __unset()       - Called when unset() is used on inaccessible properties.
 * 9. __toString()    - Called when an object is treated as a string.
 * 10. __invoke()     - Called when an object is called as a function.
 * 11. __clone()      - Called when an object is cloned.
 * 12. __sleep() / __wakeup() - Called during serialization/unserialization.
 */

// Example class demonstrating several magic functions
class MagicDemo
{
    private $data = [];

    public function __construct()
    {
        echo "__construct called\n";
    }

    public function __destruct()
    {
        echo "__destruct called\n";
    }

    public function __get($name)
    {
        echo "__get called for '$name'\n";
        return $this->data[$name] ?? null;
    }

    public function __set($name, $value)
    {
        echo "__set called for '$name' with value '$value'\n";
        $this->data[$name] = $value;
    }

    public function __call($name, $arguments)
    {
        echo "__call called for method '$name' with arguments: " . implode(', ', $arguments) . "\n";
    }

    public static function __callStatic($name, $arguments)
    {
        echo "__callStatic called for static method '$name' with arguments: " . implode(', ', $arguments) . "\n";
    }

    public function __toString()
    {
        return "__toString called\n";
    }

    public function __invoke($arg)
    {
        echo "__invoke called with argument '$arg'\n";
    }

    public function __clone()
    {
        echo "__clone called\n";
    }
}

// Executable examples:
echo "Creating object:\n";
$obj = new MagicDemo();

echo "\nSetting property:\n";
$obj->foo = 'bar';

echo "\nGetting property:\n";
echo $obj->foo . "\n";

echo "\nCalling undefined method:\n";
$obj->undefinedMethod('arg1', 'arg2');

echo "\nCalling undefined static method:\n";
MagicDemo::undefinedStaticMethod('staticArg');

echo "\nUsing object as string:\n";
echo $obj;

echo "\nInvoking object as function:\n";
$obj('argument');

echo "\nCloning object:\n";
$clone = clone $obj;

echo "\nScript end (destructor will be called):\n";
?>