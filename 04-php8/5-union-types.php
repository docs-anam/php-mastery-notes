<?php
/**
 * PHP 8.0: Union Types Summary
 *
 * Union types allow a function, method, or property to accept multiple types for a single parameter, return type, or property declaration.
 * This feature increases type safety and flexibility in PHP code.
 *
 * Syntax:
 *   - Use the pipe `|` to separate types.
 *   - Example: function foo(int|string $value) {}
 *
 * Supported Types:
 *   - All built-in types (int, float, string, bool, array, object, etc.)
 *   - Class/interface names
 *   - Nullable types (use `|null`)
 *
 * Not Supported:
 *   - `void` and `callable` cannot be part of a union type.
 *   - Duplicate types are not allowed.
 *   - `mixed` cannot be combined with other types.
 *
 * Examples:
 */

// Function with union type parameter
function printValue(int|string $value): void {
    echo $value;
}

// Function with union type return
function getId(): int|string {
    return rand(0, 1) ? 42 : "unknown";
}

// Property with union type
class User {
    public int|string|null $id;
}

// Nullable union type
function process(?int|string $value): void {
    // Equivalent to int|string|null
}

// TypeError on invalid type
try {
    printValue([1, 2, 3]); // Throws TypeError
} catch (TypeError $e) {
    echo $e->getMessage();
}

/**
 * Benefits:
 *   - More precise type declarations.
 *   - Improved static analysis and IDE support.
 *   - Backward compatibility for APIs that previously accepted multiple types.
 *
 * Reference: https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.union
 */
?>