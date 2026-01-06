<?php
/**
 * Summary: is_* Functions in PHP
 *
 * PHP provides a set of "is_*" functions to check the type or state of a variable.
 * These functions return a boolean value (true or false) based on the check.
 * They are useful for type checking and validation in your code.
 *
 * Common is_* functions:
 * - is_int()      : Checks if a variable is of type integer.
 * - is_float()    : Checks if a variable is of type float (double).
 * - is_string()   : Checks if a variable is of type string.
 * - is_bool()     : Checks if a variable is of type boolean.
 * - is_array()    : Checks if a variable is an array.
 * - is_object()   : Checks if a variable is an object.
 * - is_null()     : Checks if a variable is null.
 * - is_numeric()  : Checks if a variable is a number or a numeric string.
 * - is_callable() : Checks if a variable can be called as a function.
 * - is_resource() : Checks if a variable is a resource.
 *
 * Example usage:
 */

$var1 = 42;
$var2 = 3.14;
$var3 = "hello";
$var4 = [1, 2, 3];
$var5 = null;
$var6 = "123";
$var7 = function() { return true; };

echo "is_int(\$var1): " . (is_int($var1) ? 'true' : 'false') . PHP_EOL;         // true
echo "is_float(\$var2): " . (is_float($var2) ? 'true' : 'false') . PHP_EOL;     // true
echo "is_string(\$var3): " . (is_string($var3) ? 'true' : 'false') . PHP_EOL;   // true
echo "is_array(\$var4): " . (is_array($var4) ? 'true' : 'false') . PHP_EOL;     // true
echo "is_null(\$var5): " . (is_null($var5) ? 'true' : 'false') . PHP_EOL;       // true
echo "is_numeric(\$var6): " . (is_numeric($var6) ? 'true' : 'false') . PHP_EOL; // true
echo "is_callable(\$var7): " . (is_callable($var7) ? 'true' : 'false') . PHP_EOL; // true

/**
 * Output:
 * is_int($var1): true
 * is_float($var2): true
 * is_string($var3): true
 * is_array($var4): true
 * is_null($var5): true
 * is_numeric($var6): true
 * is_callable($var7): true
 *
 * These functions help ensure your variables are of the expected type before performing operations.
 */
?>