<?php
/**
 * Detailed Summary: Validation for Function Overriding in PHP 8.0
 *
 * PHP 8.0 enforces stricter validation rules for method overriding in class inheritance and interface implementation.
 * This ensures code consistency, type safety, and reduces runtime errors.
 *
 * Key Points with Executable Examples:
 */

// 1. Signature Compatibility
class ParentA {
    public function foo(int $a, string $b): bool {
        return true;
    }
}
class ChildA extends ParentA {
    // OK: Signature matches exactly
    public function foo(int $a, string $b): bool {
        return $a > 0 && $b !== '';
    }
    // Uncommenting below will cause a fatal error in PHP 8.0:
    // public function foo(string $a, string $b): bool {}
}

// 2. Parameter Types (No Contravariance)
class ParentB {
    public function bar(float $x) {}
}
class ChildB extends ParentB {
    // OK: Same parameter type
    public function bar(float $x) {}
    // Error: Changing parameter type is not allowed
    // public function bar(int $x) {} // Fatal error in PHP 8.0
}

// 3. Return Types (Covariance allowed)
class Animal {}
class Dog extends Animal {}
class ParentC {
    public function getAnimal(): Animal {
        return new Animal();
    }
}
class ChildC extends ParentC {
    // OK: More specific return type (covariant)
    public function getAnimal(): Dog {
        return new Dog();
    }
}

// 4. Static vs Non-static
class ParentD {
    public function test() {}
}
class ChildD extends ParentD {
    // Error: Cannot change non-static to static
    // public static function test() {} // Fatal error in PHP 8.0
}

// 5. Visibility
class ParentE {
    protected function hello() {}
}
class ChildE extends ParentE {
    // OK: Less restrictive (protected -> public)
    public function hello() {}
    // Error: More restrictive (protected -> private)
    // private function hello() {} // Fatal error in PHP 8.0
}

// 6. Abstract Methods
abstract class ParentF {
    abstract public function doSomething(int $x): string;
}
class ChildF extends ParentF {
    // OK: Signature matches exactly
    public function doSomething(int $x): string {
        return "Value: $x";
    }
    // Error: Signature mismatch
    // public function doSomething(string $x): int {} // Fatal error in PHP 8.0
}

// 7. Final Methods
class ParentG {
    final public function cannotOverride() {}
}
class ChildG extends ParentG {
    // Error: Cannot override final method
    // public function cannotOverride() {} // Fatal error in PHP 8.0
}

// 8. Variadic Parameters and Default Values
class ParentH {
    public function sum(int $a, int ...$numbers): int {
        return array_sum($numbers) + $a;
    }
}
class ChildH extends ParentH {
    // OK: Compatible variadic parameters
    public function sum(int $a, int ...$numbers): int {
        return parent::sum($a, ...$numbers) * 2;
    }
    // Error: Incompatible signature
    // public function sum(int ...$numbers): int {} // Fatal error in PHP 8.0
}

// 9. Named Arguments (Parameter names matter for forward compatibility)
class ParentI {
    public function greet(string $name, string $message = "Hello") {
        echo "$message, $name\n";
    }
}
class ChildI extends ParentI {
    // OK: Parameter names match
    public function greet(string $name, string $message = "Hello") {
        echo "$message, $name (child)\n";
    }
    // Not enforced in PHP 8.0, but recommended for named arguments
}

// Example usage:
$childA = new ChildA();
var_dump($childA->foo(1, "test")); // true

$childC = new ChildC();
var_dump($childC->getAnimal() instanceof Dog); // true

$childE = new ChildE();
$childE->hello(); // OK

$childF = new ChildF();
echo $childF->doSomething(42) . "\n"; // Value: 42

$childH = new ChildH();
echo $childH->sum(1, 2, 3) . "\n"; // 12

$childI = new ChildI();
$childI->greet("Alice"); // Hello, Alice (child)
$childI->greet(name: "Bob", message: "Hi"); // Hi, Bob (child)