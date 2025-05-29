<?php
/**
 * PHP Traits - Detailed Summary
 *
 * Traits are a mechanism for code reuse in single inheritance languages like PHP.
 * They allow you to include methods in multiple classes, avoiding code duplication.
 *
 * Key Features:
 * 1. Traits are declared with the `trait` keyword.
 * 2. Classes use traits via the `use` keyword.
 * 3. Traits can have methods and properties.
 * 4. Traits cannot be instantiated on their own.
 * 5. Traits support method overriding and conflict resolution.
 * 6. Traits can use other traits (trait inheritance).
 */

// 1. Basic Trait Usage
trait Logger {
    public function log($msg) {
        echo "Log: $msg\n";
    }
}

class User {
     use Logger;
}

$user = new User();
$user->log("User created"); // Output: Log: User created

// 2. Trait Overriding
trait Hello {
    public function sayHello() {
        echo "Hello from trait\n";
    }
}

class Greeter {
    use Hello;
    
    // This method overrides the trait's method
    public function sayHello() {
        echo "Hello from class\n";
    }
}

$g = new Greeter();
$g->sayHello(); // Output: Hello from class

// 3. Trait Conflict Resolution
trait A {
    public function doSomething() {
        echo "A\n";
    }
}
trait B {
    public function doSomething() {
        echo "B\n";
    }
}

class Test {
    use A, B {
        B::doSomething insteadof A; // Use B's method instead of A's
        A::doSomething as doA;      // Alias A's method as doA
    }
}

$t = new Test();
$t->doSomething(); // Output: B
$t->doA();         // Output: A

// 4. Trait Inheritance (Traits using other traits)
trait BaseTrait {
    public function baseMethod() {
        echo "BaseTrait method\n";
    }
}

trait ChildTrait {
    public function childMethod() {
        echo "ChildTrait method\n";
    }
}

class Demo {
    use BaseTrait;
    use ChildTrait;
}

$d = new Demo();
$d->baseMethod();  // Output: BaseTrait method
$d->childMethod(); // Output: ChildTrait method

// 5. Properties in Traits
trait Counter {
    protected $count = 0;
    public function increment() {
        $this->count++;
    }
    public function getCount() {
        return $this->count;
    }
}

class CounterUser {
    use Counter;
}

$c = new CounterUser();
$c->increment();
echo $c->getCount(); // Output: 1

/**
 * Other Details:
 * - Traits can define abstract methods that must be implemented by the class.
 * - Traits cannot have constructors, but can define methods called from the class constructor.
 * - Traits can be used in multiple classes, promoting code reuse.
 * - Traits do not support static variables shared across classes.
 */
?>