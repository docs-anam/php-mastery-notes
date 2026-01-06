<?php
/**
 * PHP 8.0 Match Expression - In-Depth Summary
 *
 * The match expression, introduced in PHP 8.0, is a modern alternative to the traditional switch statement.
 * It provides a concise, expressive, and safer way to perform conditional branching based on a value.
 *
 * Key Features:
 * 1. Expression, not a statement: Returns a value, so it can be assigned, returned, or used directly.
 * 2. Strict comparison: Uses === for comparisons (no type coercion), making it safer and more predictable.
 * 3. No fall-through: Each arm is independent; break statements are not needed.
 * 4. Multiple conditions per arm: You can match several values in a single arm.
 * 5. Exception on no match: If no arm matches and no default is provided, an UnhandledMatchError is thrown.
 * 6. Supports function calls and expressions: You can call functions or use expressions in both the match subject and arms.
 *
 * Syntax:
 * $result = match($value) {
 *     'a' => 'Result A',
 *     'b', 'c' => 'Result B or C',
 *     default => 'Default Result',
 * };
 *
 * Example 1: Basic Usage
 */
$paymentStatus = 'pending';

$message = match ($paymentStatus) {
    'paid' => 'Payment received.',
    'pending' => 'Payment is pending.',
    'failed', 'declined' => 'Payment failed or was declined.',
    default => 'Unknown payment status.',
};

echo $message; // Output: Payment is pending.

/**
 * Differences from switch:
 * - Match is an expression (returns a value), switch is a statement.
 * - Match uses strict comparison (===), switch uses loose comparison (==).
 * - No fall-through in match; each arm is isolated.
 * - All possible cases must be covered or a default provided, otherwise UnhandledMatchError is thrown.
 *
 * Example 2: Using Expressions and Function Calls
 */

// Example function to get user role
function getUserRole($userId) {
    $roles = [
        1 => 'admin',
        2 => 'editor',
        3 => 'subscriber',
    ];
    return $roles[$userId] ?? 'guest';
}

$userId = 2;

$accessLevel = match (getUserRole($userId)) {
    'admin' => getAdminAccessMessage(),
    'editor' => getEditorAccessMessage(),
    'subscriber', 'guest' => getSubscriberAccessMessage(),
    default => 'No access.',
};

echo $accessLevel; // Output: Editor access granted.

function getAdminAccessMessage() {
    return 'Admin access granted.';
}

function getEditorAccessMessage() {
    return 'Editor access granted.';
}

function getSubscriberAccessMessage() {
    return 'Subscriber or guest access granted.';
}

/**
 * Example 3: Match with Complex Expressions
 */
$score = 85;

$grade = match (true) {
    $score >= 90 => calculateGrade('A'),
    $score >= 80 => calculateGrade('B'),
    $score >= 70 => calculateGrade('C'),
    $score >= 60 => calculateGrade('D'),
    default => calculateGrade('F'),
};

echo $grade; // Output: Grade: B

function calculateGrade($grade) {
    return "Grade: $grade";
}

/**
 * Summary:
 * The match expression in PHP 8.0 is a powerful, concise, and safer alternative to switch.
 * It supports strict comparison, multiple conditions per arm, and can include function calls and expressions,
 * making code more readable, maintainable, and less error-prone.
 */