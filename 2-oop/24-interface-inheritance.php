<?php
/**
 * Interface Inheritance in PHP OOP - Detailed Summary
 *
 * 1. What is Interface Inheritance?
 *    - In PHP, interfaces can inherit from other interfaces using the `extends` keyword.
 *    - This allows you to build complex contracts by combining multiple interfaces.
 *
 * 2. Syntax Example:
 *    interface A {
 *        public function foo();
 *    }
 *
 *    interface B extends A {
 *        public function bar();
 *    }
 *
 *    class MyClass implements B {
 *        public function foo() {  ...  }
 *        public function bar() {  ...  }
 *    }
 *
 * 3. Multiple Interface Inheritance:
 *    - An interface can extend multiple interfaces, separated by commas.
 *    interface C extends A, B {
 *        public function baz();
 *    }
 *
 * 4. Rules:
 *    - Interfaces can only extend other interfaces, not classes.
 *    - A class implementing an interface that extends others must implement all methods from the entire hierarchy.
 *
 * 5. Benefits:
 *    - Promotes code reusability and modular design.
 *    - Enables polymorphism and flexible architecture.
 *
 * 6. Example:
 */

interface Logger {
    public function log(string $message);
}

interface FileLogger extends Logger {
    public function setLogFile(string $filePath);
}

class MyFileLogger implements FileLogger {
    private $filePath;

    public function log(string $message) {
        // Implementation of log
    }

    public function setLogFile(string $filePath) {
        $this->filePath = $filePath;
    }
}

// MyFileLogger must implement both log() and setLogFile() methods.