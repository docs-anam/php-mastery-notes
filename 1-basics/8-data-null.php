<?php
// Summary: Data Null in PHP

// In PHP, 'null' is a special data type representing a variable with no value.
// A variable is considered null if:
// 1. It has been assigned the constant NULL.
// 2. It has not been set yet.
// 3. It has been unset().

$a = null; // Explicitly set to null
$b;        // Not set, considered null
$c = 10;
unset($c); // $c is now null

// Checking for null
var_dump(is_null($a)); // true
var_dump(is_null($b)); // true (generates a notice if error reporting is on)
var_dump(isset($c));   // false

// Null is case-insensitive
$d = NULL;
var_dump($d); // NULL

// Null is often used to represent optional or missing values.
?>