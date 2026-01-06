<?php
/**
 * PHP 8.0: Trailing Comma in Parameter List
 *
 * PHP 8.0 introduced support for trailing commas in function and method parameter lists.
 * This feature allows you to add a comma after the last parameter, which can make
 * version control diffs cleaner and code maintenance easier, especially when adding
 * or removing parameters.
 *
 * Example:
 */

function exampleFunction(
    $param1,
    $param2,
    $param3, // Trailing comma allowed in PHP 8.0+
) {
    // Function body
    return [$param1, $param2, $param3];
}

$result = exampleFunction(1, 2, 3);
print_r($result);

/**
 * Benefits:
 * - Cleaner diffs when adding/removing parameters.
 * - Consistency with arrays and other language constructs that allow trailing commas.
 *
 * Notes:
 * - Trailing commas are only allowed in parameter lists in PHP 8.0 and later.
 * - This applies to functions, methods, closures, and arrow functions.
 */

// Example with a closure
$closure = function(
    $a,
    $b,
    $c,
) {
    return $a + $b + $c;
};

echo $closure(1, 2, 3); // Outputs: 6

?>