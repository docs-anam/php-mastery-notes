<?php
/**
 * Comparing Objects in PHP OOP
 *
 * In PHP, objects can be compared using two main operators: `==` (equality) and `===` (identity).
 *
 * 1. Equality Operator (==)
 *    - Two objects are considered equal if they are instances of the same class and have the same values for all properties.
 *    - The comparison does not check if they are the same instance (reference).
 */

// Example for Equality Operator (==)
class MyClass {
    public $prop;
}

$a = new MyClass();
$b = new MyClass();
$a->prop = 1;
$b->prop = 1;
echo "Equality (==): ";
var_dump($a == $b); // true

/**
 * 2. Identity Operator (===)
 *    - Two objects are identical if and only if they refer to the exact same instance.
 */

// Example for Identity Operator (===)
$a = new MyClass();
$b = $a;
echo "Identity (===) with same instance: ";
var_dump($a === $b); // true

$c = new MyClass();
echo "Identity (===) with different instance: ";
var_dump($a === $c); // false

/**
 * 3. Custom Comparison
 *    - You can define your own comparison logic by implementing a custom method.
 */

class User {
    public $id;
    public function __construct($id) {
        $this->id = $id;
    }
    public function equals(User $other) {
        return $this->id === $other->id;
    }
}

$user1 = new User(1);
$user2 = new User(1);
$user3 = new User(2);

echo "Custom equals method (user1 vs user2): ";
var_dump($user1->equals($user2)); // true

echo "Custom equals method (user1 vs user3): ";
var_dump($user1->equals($user3)); // false

/**
 * 4. Serialization and Comparison
 *    - Sometimes, objects are compared by serializing them and comparing the serialized strings.
 *    - This is not recommended for complex objects or those with resources.
 */

$serializedA = serialize($a);
$serializedC = serialize($c);
echo "Serialization comparison: ";
var_dump($serializedA === $serializedC); // false

/**
 * 5. Caveats
 *    - Private and protected properties are also compared by `==`.
 *    - Static properties are not considered in object comparison.
 *    - Circular references can cause issues in comparison.
 *
 * Summary Table:
 * | Operator | Checks Same Instance | Checks Property Values | Checks Class Type |
 * |----------|---------------------|-----------------------|-------------------|
 * | ==       | No                  | Yes                   | Yes               |
 * | ===      | Yes                 | Yes                   | Yes               |
 */
?>