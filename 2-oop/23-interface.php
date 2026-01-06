<?php
/**
 * PHP OOP: Interface Summary
 *
 * 1. What is an Interface?
 *    - An interface defines a contract for classes.
 *    - It contains method signatures (no implementation).
 *    - Classes that implement an interface must define all its methods.
 *
 * 2. Syntax:
 *    interface InterfaceName {
 *        public function methodName1();
 *        public function methodName2($param);
 *    }
 *
 *    class MyClass implements InterfaceName {
 *        public function methodName1() {  ...  }
 *        public function methodName2($param) {  ...  }
 *    }
 *
 * 3. Key Points:
 *    - Interfaces cannot have properties (except constants).
 *    - All methods are public and abstract by default.
 *    - A class can implement multiple interfaces (comma-separated).
 *    - Interfaces can extend other interfaces.
 *    - Cannot instantiate an interface directly.
 *
 * 4. Why Use Interfaces?
 *    - Enforce consistency across classes.
 *    - Enable polymorphism (code to an interface, not an implementation).
 *    - Useful for dependency injection and mocking in tests.
 *
 * 5. Example:
 */

interface Logger {
    public function log(string $message);
}

class FileLogger implements Logger {
    public function log(string $message) {
        // Implementation to log to a file
        echo "Logging to file: $message";
    }
}

class DatabaseLogger implements Logger {
    public function log(string $message) {
        // Implementation to log to a database
        echo "Logging to database: $message";
    }
}

// Usage
function writeLog(Logger $logger, $msg) {
    $logger->log($msg);
}

writeLog(new FileLogger(), "Hello Interface!");
writeLog(new DatabaseLogger(), "Hello Interface!");

/**
 * 6. Interface Constants:
 *    - Interfaces can define constants.
 *    - Example:
 *        interface MyInterface {
 *            const VERSION = '1.0';
 *        }
 */
?>