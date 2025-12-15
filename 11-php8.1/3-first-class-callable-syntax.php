<?php

/**
 * First-Class Callable Syntax in PHP 8.1
 * 
 * PHP 8.1 introduced a cleaner syntax for creating closures from callables
 * using the first-class callable syntax with the (...) operator.
 * 
 * This provides a more concise way to create Closure objects from callables
 * without using Closure::fromCallable() or creating wrapper closures.
 */

// ============================================================================
// BASIC SYNTAX
// ============================================================================

// Before PHP 8.1 - using Closure::fromCallable()
function myFunction($x) {
    return $x * 2;
}

$oldWay = Closure::fromCallable('myFunction');
echo $oldWay(5) . PHP_EOL; // 10

// PHP 8.1+ - First-class callable syntax
$newWay = myFunction(...);
echo $newWay(5) . PHP_EOL; // 10

// ============================================================================
// WITH BUILT-IN FUNCTIONS
// ============================================================================

// Before PHP 8.1
$oldStrlen = Closure::fromCallable('strlen');

// PHP 8.1+
$newStrlen = strlen(...);
echo $newStrlen('Hello') . PHP_EOL; // 5

$upper = strtoupper(...);
echo $upper('hello') . PHP_EOL; // HELLO

// ============================================================================
// WITH OBJECT METHODS
// ============================================================================

class Calculator {
    public function add($a, $b) {
        return $a + $b;
    }
    
    public static function multiply($a, $b) {
        return $a * $b;
    }
}

$calc = new Calculator();

// Instance methods
$addMethod = $calc->add(...);
echo $addMethod(5, 3) . PHP_EOL; // 8

// Static methods
$multiplyMethod = Calculator::multiply(...);
echo $multiplyMethod(5, 3) . PHP_EOL; // 15

// ============================================================================
// WITH ARRAY FUNCTIONS
// ============================================================================

$numbers = [1, 2, 3, 4, 5];

// Before PHP 8.1
$doubled = array_map(function($n) { return $n * 2; }, $numbers);

// PHP 8.1+ with first-class callable
function double($n) {
    return $n * 2;
}
$doubled = array_map(double(...), $numbers);
print_r($doubled);

// Using built-in functions
$strings = ['hello', 'world', 'php'];
$uppercased = array_map(strtoupper(...), $strings);
print_r($uppercased);

// ============================================================================
// WITH INVOKABLE OBJECTS
// ============================================================================

class Multiplier {
    public function __construct(private int $factor) {}
    
    public function __invoke($value) {
        return $value * $this->factor;
    }
}

$times3 = new Multiplier(3);
$callable = $times3(...);
echo $callable(10) . PHP_EOL; // 30

// ============================================================================
// SCOPE AND BINDING
// ============================================================================

class Example {
    private $value = 42;
    
    public function getValue() {
        return $this->value;
    }
    
    public function getCallable() {
        // The callable retains the binding to $this
        return $this->getValue(...);
    }
}

$obj = new Example();
$getter = $obj->getCallable();
echo $getter() . PHP_EOL; // 42

// ============================================================================
// BENEFITS
// ============================================================================

/**
 * 1. More Concise: Shorter syntax compared to Closure::fromCallable()
 * 2. More Readable: Intent is clearer
 * 3. Consistent: Works with all callable types
 * 4. Performance: Creates actual Closure objects efficiently
 * 5. Scope Preservation: Maintains proper scope binding for methods
 */

// ============================================================================
// COMPARISON TABLE
// ============================================================================

/**
 * | Callable Type          | Old Way                              | New Way (PHP 8.1)      |
 * |------------------------|--------------------------------------|------------------------|
 * | Function               | Closure::fromCallable('func')        | func(...)              |
 * | Static Method          | Closure::fromCallable([Class, 'method']) | Class::method(...)     |
 * | Instance Method        | Closure::fromCallable([$obj, 'method'])  | $obj->method(...)      |
 * | Invokable Object       | Closure::fromCallable($obj)          | $obj(...)              |
 */

// ============================================================================
// PRACTICAL EXAMPLE: EVENT LISTENERS
// ============================================================================

class EventDispatcher {
    private array $listeners = [];
    
    public function on(string $event, callable $callback) {
        $this->listeners[$event][] = $callback;
    }
    
    public function dispatch(string $event, ...$args) {
        foreach ($this->listeners[$event] ?? [] as $listener) {
            $listener(...$args);
        }
    }
}

class Logger {
    public function logError($message) {
        echo "[ERROR] $message" . PHP_EOL;
    }
    
    public function logInfo($message) {
        echo "[INFO] $message" . PHP_EOL;
    }
}

$dispatcher = new EventDispatcher();
$logger = new Logger();

// Clean syntax for registering callbacks
$dispatcher->on('error', $logger->logError(...));
$dispatcher->on('info', $logger->logInfo(...));

$dispatcher->dispatch('error', 'Something went wrong');
$dispatcher->dispatch('info', 'Operation completed');
