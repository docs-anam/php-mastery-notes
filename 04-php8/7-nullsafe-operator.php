<?php
/**
 * PHP 8.0 Nullsafe Operator (?->) — Detailed Summary
 *
 * The nullsafe operator (?->) is a feature introduced in PHP 8.0 that allows you to safely access properties or call methods on an object that might be null, without causing a fatal error.
 *
 * Why is it useful?
 * -----------------
 * In earlier PHP versions, attempting to access a property or method on a null object would result in a fatal error:
 *
 *     $user = null;
 *     $name = $user->profile->name; // Fatal error: Attempt to access property on null
 *
 * To avoid this, developers had to write verbose and repetitive code:
 *
 *     $name = null;
 *     if ($user !== null && $user->profile !== null) {
 *         $name = $user->profile->name;
 *     }
 *
 * Or use the null coalescing operator (??) with intermediate checks:
 *
 *     $name = $user && $user->profile ? $user->profile->name : null;
 *
 * How does the nullsafe operator work?
 * ------------------------------------
 * The nullsafe operator (?->) allows you to chain property or method accesses, and if any part of the chain is null, the entire expression evaluates to null instead of throwing an error.
 *
 *     $name = $user?->profile?->name;
 *
 * This is equivalent to the verbose checks above, but much more concise and readable.
 *
 * Chaining:
 * ---------
 * You can chain multiple nullsafe operators:
 *
 *     $result = $obj?->foo()?->bar()?->baz;
 *
 * If $obj is null, $result is null. If $obj->foo() returns null, $result is null, and so on.
 *
 * Usage with Methods:
 * -------------------
 * The nullsafe operator works with both properties and methods:
 *
 *     $email = $user?->getProfile()?->getEmail();
 *
 * If $user is null or getProfile() returns null, $email will be null.
 *
 * Real-world Example:
 * -------------------
 * Suppose you have nested objects:
 */
class Address {
    public ?string $city = null;
}

class Profile {
    public ?Address $address = null;
    public string $name = 'Alice';
}

class User {
    public ?Profile $profile = null;
}

$user = new User();

// Accessing nested property safely:
$city = $user?->profile?->address?->city; // null, no error

// Assigning nested objects:
$user->profile = new Profile();
$user->profile->address = new Address();
$user->profile->address->city = 'London';

$city = $user?->profile?->address?->city; // 'London'

/**
 * Limitations:
 * ------------
 * - Only works with objects, not arrays:
 *     $arr = null;
 *     $value = $arr?->key; // Invalid, will not work
 *
 * - Cannot be used for assignments:
 *     $user?->profile?->name = 'Bob'; // Invalid syntax
 *
 * - Does not suppress errors from inside methods:
 *     $user?->getProfile()?->methodThatThrows(); // If methodThatThrows() throws, the exception is not suppressed
 *
 * Summary:
 * --------
 * The nullsafe operator (?->) makes code safer and more concise when dealing with objects that may be null, especially in deeply nested structures.
 */