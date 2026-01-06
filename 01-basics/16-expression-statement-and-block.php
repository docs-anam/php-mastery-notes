<?php
// Expression: Any combination of values, variables, and operators that results in a value.
$a = 5 + 3; // 5 + 3 is an expression. $a = 8 is an assignment expression.

// Statement: A complete instruction that performs an action. Ends with a semicolon.
echo $a; // This is a statement (outputs 8).

// Block: A group of statements enclosed in curly braces {}. Used in control structures, functions, classes, etc.
if ($a > 5) {
    // This is a block containing two statements:
    echo "a is greater than 5";
    $b = $a * 2;
}

// Summary:
// - Expressions produce values (e.g., 2 + 2, $x, $y = 3).
// - Statements perform actions (e.g., assignments, function calls, control structures).
// - Blocks group multiple statements together, usually within curly braces.
?>