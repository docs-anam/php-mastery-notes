<?php
/**
 * String to Number Comparison in PHP 8.0 - Detailed Summary
 *
 * In PHP, comparing strings to numbers can yield surprising results due to type juggling.
 * PHP 8.0 introduced changes to make these comparisons more predictable and less error-prone.
 *
 * 1. Loose Comparison (==)
 *    - When using ==, PHP tries to convert the string to a number if one of the operands is a number.
 *    - If the string starts with numeric data, it is converted to that number; otherwise, it becomes 0.
 *    - Example:
 *        "123abc" == 123   // true (string "123abc" becomes 123)
 *        "abc" == 0        // true (string "abc" becomes 0)
 *
 * 2. Strict Comparison (===)
 *    - No type juggling occurs. Both value and type must match.
 *    - Example:
 *        "123" === 123     // false (string vs integer)
 *
 * 3. PHP 8.0 Changes
 *    - Prior to PHP 8.0, when comparing a numeric string to a non-numeric string using ==, both were converted to 0.
 *    - In PHP 8.0, if one operand is a number and the other is a non-numeric string, the string is always converted to 0.
 *    - If both operands are strings, they are compared as strings.
 *    - Example:
 *        0 == "not a number"   // true (string becomes 0)
 *        "123" == "123abc"     // false (both are strings, compared as strings)
 *
 * 4. Best Practices
 *    - Use strict comparison (===) to avoid unexpected results.
 *    - Validate and sanitize input before comparison.
 *    - Use functions like is_numeric() to check if a string is numeric.
 *
 * 5. Examples
 */
var_dump("123abc" == 123);      // true
var_dump("abc" == 0);           // true
var_dump("123" === 123);        // false
var_dump(0 == "not a number");  // true
var_dump("123" == "123abc");    // false

// Checking if a string is numeric
var_dump(is_numeric("123abc")); // false
var_dump(is_numeric("123"));    // true