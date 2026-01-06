<?php
/**
 * PHP 8.0 introduced the Stringable interface.
 *
 * Summary:
 * - The Stringable interface is a built-in interface in PHP 8.0.
 * - It is automatically implemented by any class that defines a __toString() method.
 * - The interface has a single method: public function __toString(): string;
 * - It allows type-hinting for objects that can be converted to strings.
 * - Useful for enforcing that an object can be represented as a string.
 *
 * Example:
 */

class User implements Stringable {
    private string $name;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function __toString(): string {
        return $this->name;
    }
}

function printString(Stringable $obj) {
    echo $obj;
}

$user = new User("Alice");
printString($user); // Outputs: Alice

/**
 * Notes:
 * - You do not need to explicitly implement Stringable; any class with __toString() is considered Stringable.
 * - Stringable is useful for type declarations and static analysis.
 * - If a class does not have __toString(), it cannot be used where Stringable is required.
 */
?>