<?php
/**
 * PHP 8.0 Mixed Type Summary
 *
 * The `mixed` type was introduced in PHP 8.0 as a union type that can accept multiple types of values.
 * It is useful for functions or methods that can accept or return different types of data.
 *
 * Details:
 * - The `mixed` type includes the following types: `array`, `bool`, `callable`, `int`, `float`, `null`, `object`, `resource`, and `string`.
 * - When a parameter or return type is declared as `mixed`, it means it can accept or return any type except `void`.
 * - `mixed` is especially useful for APIs, libraries, or legacy code where strict typing is not possible or practical.
 * - It is not possible to combine `mixed` with other types in a union (e.g., `mixed|int` is not allowed).
 * - `mixed` can be used for both parameter and return type declarations.
 * - When a function does not declare a return type, it is implicitly considered as returning `mixed`.
 *
 * Example Usage:
 */

function processValue(mixed $value): mixed {
    // The function can accept and return any type of value.
    if (is_array($value)) {
        return count($value);
    } elseif (is_string($value)) {
        return strtoupper($value);
    }
    return $value;
}

// Usage examples:
var_dump(processValue([1, 2, 3])); // int(3)
var_dump(processValue("hello"));   // string(5) "HELLO"
var_dump(processValue(42));        // int(42)
var_dump(processValue(null));      // NULL

/**
 * Summary:
 * - The `mixed` type increases flexibility in type declarations.
 * - It improves code documentation and static analysis.
 * - Use `mixed` when a function or method can handle multiple types and strict typing is not feasible.
 */
?>