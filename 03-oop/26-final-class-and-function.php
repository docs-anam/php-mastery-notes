<?php
/**
 * Summary: Final Class in PHP OOP
 *
 * In PHP, the `final` keyword is used to prevent class inheritance or method overriding.
 *
 * 1. Final Class:
 *    - Declaring a class as `final` means it cannot be extended (inherited) by any other class.
 *    - Syntax:
 *        final class ClassName {
 *            // class body
 *        }
 *    - Attempting to extend a final class will result in a fatal error.
 *
 * 2. Final Methods:
 *    - You can also declare individual methods as `final` to prevent them from being overridden in child classes.
 *    - Syntax:
 *        class ParentClass {
 *            final public function methodName() {
 *                // method body
 *            }
 *        }
 *
 * 3. Use Cases:
 *    - Use `final` when you want to prevent further modification of a class or method, ensuring its behavior remains unchanged.
 *    - Common in frameworks or libraries to protect core functionality.
 *
 * 4. Example:
 *    final class Logger {
 *        public function log($message) {
 *            echo $message;
 *        }
 *    }
 *
 *    // This will cause an error:
 *    // class FileLogger extends Logger {}
 *
 * 5. Notes:
 *    - Interfaces cannot be declared as final.
 *    - Properties cannot be declared as final.
 */

// Executable Example:

final class Logger {
    public function log($message) {
        echo "Log: $message\n";
    }
}

// This will work:
$logger = new Logger();
$logger->log("Hello, world!");

// Uncommenting the following code will cause a fatal error:
// class FileLogger extends Logger {}
// $fileLogger = new FileLogger();
// $fileLogger->log("This will not work.");

// Example of final method:
class Base {
    final public function sayHello() {
        echo "Hello from Base\n";
    }
}

class Child extends Base {
    // Uncommenting the following will cause a fatal error:
    // public function sayHello() {
    //     echo "Hello from Child\n";
    // }
}

$child = new Child();
$child->sayHello();
?>