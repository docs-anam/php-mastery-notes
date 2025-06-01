<?php
/**
 * Consistent Type Errors in PHP 8.0
 *
 * PHP 8.0 introduced more consistent and predictable error handling for type-related issues.
 * Previously, PHP sometimes emitted warnings or notices for type mismatches, and in other cases,
 * it threw TypeError exceptions. This inconsistency could lead to bugs that were hard to track.
 *
 * Key Improvements in PHP 8.0:
 * 1. All internal functions now throw TypeError for argument type mismatches.
 *    - Before PHP 8.0, some internal functions emitted warnings and returned null or false.
 *    - Now, they throw a TypeError, just like user-defined functions with type declarations.
 *
 * 2. Return type mismatches also throw TypeError.
 *    - If a function declares a return type and returns an incompatible value, a TypeError is thrown.
 *
 * 3. Consistency between user-defined and internal functions.
 *    - Both now behave the same way regarding type errors.
 *
 * Example:
 */

function add(int $a, int $b): int {
    return $a + $b;
}

try {
    echo add("5", 10); // This will throw a TypeError in PHP 8.0
} catch (TypeError $e) {
    echo "Caught TypeError: " . $e->getMessage();
}

/**
 * Internal function example:
 */
try {
    strlen([]); // Throws TypeError in PHP 8.0
} catch (TypeError $e) {
    echo "\nCaught TypeError: " . $e->getMessage();
}

/**
 * Summary:
 * - PHP 8.0 enforces strict type checking for both user-defined and internal functions.
 * - Type mismatches now consistently throw TypeError exceptions.
 * - This leads to more predictable and safer code.
 */
?>