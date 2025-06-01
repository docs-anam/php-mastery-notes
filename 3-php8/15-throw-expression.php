<?php
/**
 * PHP 8.0: Throw Expression Summary
 *
 * In PHP 8.0, the `throw` statement was enhanced to become an expression.
 * This allows `throw` to be used in places where only expressions are allowed,
 * such as in arrow functions, the null coalescing operator, and ternary expressions.
 *
 * Key Points:
 * 1. Prior to PHP 8.0, `throw` could only be used as a statement.
 * 2. In PHP 8.0+, `throw` can be used as an expression, enabling more concise and expressive code.
 *
 * Examples:
 */

// 1. Throw in a ternary expression
$value = $input !== null ? $input : throw new InvalidArgumentException('Input required');

// 2. Throw in a null coalescing operator
$username = $data['username'] ?? throw new Exception('Username missing');

// 3. Throw in arrow functions
$parseInt = fn($value) => is_numeric($value) ? (int)$value : throw new InvalidArgumentException('Not a number');

// 4. Throw in short-circuiting expressions
function getConfig(array $config) {
    return $config['host'] ?? throw new Exception('Host not set');
}

/**
 * Benefits:
 * - Enables more concise code by allowing exceptions to be thrown directly in expressions.
 * - Improves readability and reduces boilerplate code.
 *
 * Note:
 * - The thrown exception must still be an instance of Throwable (Exception or Error).
 * - This feature is available only in PHP 8.0 and later.
 */