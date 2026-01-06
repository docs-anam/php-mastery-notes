<?php
/**
 * Anonymous Classes in PHP OOP - Detailed Summary
 *
 * Anonymous classes are classes without a name, introduced in PHP 7.
 * They are useful for simple, one-off objects, especially when implementing interfaces or extending classes.
 *
 * Key Points:
 * 1. Syntax:
 *    $object = new class([constructor args]) [extends BaseClass] [implements Interface1, Interface2] {
 *        // class body
 *    };
 *
 * 2. Use Cases:
 *    - Quick, one-time-use objects.
 *    - Dependency injection for testing.
 *    - Implementing simple interfaces without creating a named class.
 *
 * 3. Features:
 *    - Can extend other classes and implement interfaces.
 *    - Can have properties, methods, and even constructors.
 *    - Scope: Can access variables from the parent scope using the 'use' keyword (like closures).
 *
 * 4. Limitations:
 *    - Cannot be type-hinted directly (except as 'object' or via interface/base class).
 *    - No reusability (since they are anonymous).
 *
 * Example:
 */

interface Logger {
    public function log(string $msg);
}

function process(Logger $logger) {
    $logger->log("Processing started.");
}

process(new class implements Logger {
    public function log(string $msg) {
        echo $msg . PHP_EOL;
    }
});

// Using variables from parent scope
$message = "Hello from anonymous class!";
$obj = new class($message) {
    private $msg;
    public function __construct($msg) {
        $this->msg = $msg;
    }
    public function show() {
        echo $this->msg . PHP_EOL;
    }
};
$obj->show();

/**
 * Summary:
 * - Anonymous classes provide a concise way to define classes for immediate use.
 * - They are ideal for simple, throwaway implementations, especially in OOP patterns.
 * - They help keep code clean and focused when a full class definition is unnecessary.
 */