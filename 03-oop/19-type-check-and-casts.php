<?php
/**
 * Type Check and Casts in PHP OOP - Summary
 *
 * 1. Type Checking in PHP OOP:
 *    - PHP supports type declarations for function/method parameters, return types, and properties.
 *    - Types include scalar types (int, float, string, bool), class/interface names, array, callable, iterable, object, mixed, and union types.
 *    - Use `instanceof` to check if an object is an instance of a class or implements an interface.
 *      Example:
 *        if ($obj instanceof MyClass) { ... }
 *    - Use `is_*` functions for scalar types:
 *      - is_int(), is_string(), is_bool(), is_array(), is_object(), etc.
 *    - Type declarations enforce types at runtime (with strict_types=1, errors are thrown for mismatches).
 *
 * 2. Type Casting in PHP OOP:
 *    - Type casting converts a value from one type to another.
 *    - Scalar casting: (int), (float), (string), (bool), (array), (object)
 *      Example:
 *        $num = (int) "123";
 *        $obj = (object) ['a' => 1];
 *    - Objects can be cast to arrays and vice versa, but structure may change.
 *    - No direct casting between unrelated classes; use conversion methods instead.
 *    - Magic methods like __toString() allow objects to be cast to string.
 *
 * 3. Best Practices:
 *    - Use type declarations for clarity and safety.
 *    - Use type checks (`instanceof`, `is_*`) before casting.
 *    - Prefer explicit conversion methods for complex objects.
 *    - Avoid unnecessary casting; rely on type declarations and PHP's type system.
 *
 * 4. Example:
 */

declare(strict_types=1);

class Animal {}
class Dog extends Animal {}

function feedAnimal(Animal $animal): void {
    if ($animal instanceof Dog) {
        echo "Feeding a dog.\n";
    } else {
        echo "Feeding an animal.\n";
    }
}

$dog = new Dog();
feedAnimal($dog); // Feeding a dog.

$value = "42";
$intValue = (int) $value; // Type cast string to int

if (is_int($intValue)) {
    echo "It's an integer.\n";
}