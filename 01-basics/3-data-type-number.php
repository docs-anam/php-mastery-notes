<?php

// PHP supports two main numeric data types: integers and floats (doubles).

// 1. Integers
// Whole numbers without a decimal point.
$intVar = 42; // Example of integer

// 2. Floats (Doubles)
// Numbers with a decimal point or in exponential form.
$floatVar = 3.14159; // Example of float

// Numeric operations
$sum = $intVar + $floatVar; // Addition
$product = $intVar * 2;     // Multiplication

// PHP automatically converts between integers and floats as needed.
var_dump($intVar);    // int(42)
var_dump($floatVar);  // float(3.14159)
var_dump($sum);       // float(45.14159)
var_dump($product);   // int(84)

// Numeric values can be positive or negative.
// PHP also supports numeric literals in hexadecimal, octal, and binary notation.
$hex = 0x2A;   // Hexadecimal (42)
$oct = 052;    // Octal (42)
$bin = 0b101010; // Binary (42)

var_dump($hex, $oct, $bin);
?>