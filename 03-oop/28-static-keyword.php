<?php
/**
 * Summary: The static Keyword in PHP
 *
 * 1. Definition:
 *    - The static keyword in PHP is used to declare class properties and methods that belong to the class itself, rather than to any specific object instance.
 *    - Static members are shared among all instances of a class.
 *
 * 2. Static Properties:
 *    - Declared with the static keyword inside a class.
 *    - Accessed using ClassName::$property or self::$property (within the class).
 *    - Cannot use $this to access static properties.
 *    - Useful for storing class-wide information.
 */
class Counter {
    public static $count = 0;
    public function increment() {
        self::$count++;
    }
}
$c1 = new Counter();
$c2 = new Counter();
$c1->increment();
$c2->increment();
echo "Counter::\$count: " . Counter::$count . PHP_EOL; // 2

/**
 * 3. Static Methods:
 *    - Declared with the static keyword.
 *    - Can be called without creating an instance: ClassName::method().
 *    - Cannot access $this or non-static properties/methods.
 *    - Useful for utility functions or factory methods.
 */
class MathHelper {
    public static function add($a, $b) {
        return $a + $b;
    }
}
echo "MathHelper::add(2, 3): " . MathHelper::add(2, 3) . PHP_EOL; // 5

/**
 * 4. Late Static Binding:
 *    - Use static:: instead of self:: to refer to the called class in a static context.
 *    - Enables correct behavior in inheritance hierarchies.
 */
class Base {
    public static function who() {
        echo "Base::who(): " . static::class . PHP_EOL;
    }
}
class Child extends Base {}
Child::who(); // Outputs "Child"

class Animal {
    public static function create() {
        return new static();
    }
}
class Dog extends Animal {}
$dog = Dog::create();
echo "get_class(\$dog): " . get_class($dog) . PHP_EOL; // Dog

/**
 * 5. Static Variables in Functions:
 *    - The static keyword can also be used inside functions to preserve variable values between calls.
 */
function counter() {
    static $count = 0;
    $count++;
    return $count;
}
echo "counter(): " . counter() . PHP_EOL; // 1
echo "counter(): " . counter() . PHP_EOL; // 2

/**
 * 6. Limitations:
 *    - Static properties cannot be accessed via $this.
 *    - Static methods cannot access non-static properties or $this.
 *    - Static properties and methods are not inherited by objects, but by classes.
 */
class Example {
    public static $value = 10;
    public $nonStatic = 20;
    public static function show() {
        // echo $this->nonStatic; // Error!
        echo "Example::\$value: " . self::$value . PHP_EOL; // OK
    }
}
Example::show();

/**
 * 7. Use Cases:
 *    - Utility/helper methods.
 *    - Shared counters or configuration.
 *    - Singleton pattern implementation.
 */
class Singleton {
    private static $instance = null;
    private function __construct() {}
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
$s1 = Singleton::getInstance();
$s2 = Singleton::getInstance();
echo "Singleton instances are identical: " . (var_export($s1 === $s2, true)) . PHP_EOL; // true

// Reference: https://www.php.net/manual/en/language.oop5.static.php
