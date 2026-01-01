# Numeric Data Types

## Overview

PHP supports two main numeric data types:
1. **Integer** - Whole numbers without decimal points
2. **Float** (Double) - Numbers with decimal points or exponential notation

Both are used for mathematical operations and numeric calculations.

## Integers

### Definition

An integer is a whole number with no decimal point. It can be either positive, negative, or zero.

```php
<?php
$age = 25;              // Positive integer
$temperature = -5;      // Negative integer
$count = 0;             // Zero

var_dump($age);         // int(25)
var_dump($temperature); // int(-5)
var_dump($count);       // int(0)
?>
```

### Integer Rules & Constraints

```php
<?php
// 1. Integer must have at least one digit
$valid = 100;           // ✓ Valid
// $invalid = ;         // ✗ Invalid

// 2. Integer must NOT have a decimal point
$valid = 50;            // ✓ Valid
// $invalid = 50.5;     // ✗ Invalid (becomes float)

// 3. Integer is either positive or negative
$positive = 100;        // ✓ Valid
$negative = -100;       // ✓ Valid
$zero = 0;              // ✓ Valid

// 4. Integers can be decimal (base 10), hexadecimal (base 16), or octal (base 8)
$decimal = 42;          // Base 10 (normal)
$hexadecimal = 0x2A;    // Base 16 (0x prefix)
$octal = 0o52;          // Base 8 (0o prefix)
$binary = 0b101010;     // Base 2 (0b prefix)

echo $decimal;          // 42
echo $hexadecimal;      // 42
echo $octal;            // 42
echo $binary;           // 42
?>
```

### Checking for Integer

```php
<?php
$age = 25;

// Check if variable is integer
if (is_int($age)) {
    echo "$age is an integer";
}

// is_integer() is an alias for is_int()
if (is_integer($age)) {
    echo "$age is an integer";
}

// Check if variable is numeric (integer or float)
if (is_numeric($age)) {
    echo "$age is numeric";
}
?>
```

## Floats (Doubles)

### Definition

A float (also called a double) is a number with a decimal point or in exponential form.

```php
<?php
$price = 19.99;         // Float with decimal point
$pi = 3.14159;          // Mathematical constant
$negative = -2.5;       // Negative float
$very_small = 1.2e-5;   // Exponential notation (0.000012)
$very_large = 1.5e3;    // Exponential notation (1500)

var_dump($price);       // float(19.99)
var_dump($pi);          // float(3.14159)
?>
```

### Checking for Float

```php
<?php
$price = 19.99;

// Check if variable is float
if (is_float($price)) {
    echo "$price is a float";
}

// is_double() is an alias
if (is_double($price)) {
    echo "$price is a float";
}

// Check if numeric
if (is_numeric($price)) {
    echo "$price is numeric";
}
?>
```

## Type Juggling (Automatic Conversion)

PHP automatically converts between integers and floats as needed:

```php
<?php
// Addition of integer + float = float
$int = 10;              // integer
$float = 2.5;           // float
$result = $int + $float;// float(12.5)

var_dump($result);      // float(12.5)

// Integer division resulting in float
$division = 10 / 4;     // float(2.5) - not 2!
var_dump($division);    // float(2.5)

// Integer division (PHP 7+)
$int_division = intdiv(10, 4); // int(2)
var_dump($int_division); // int(2)
?>
```

## Numeric Operations

### Arithmetic Operations

```php
<?php
$a = 10;
$b = 3;

// Addition
echo $a + $b;          // 13

// Subtraction
echo $a - $b;          // 7

// Multiplication
echo $a * $b;          // 30

// Division
echo $a / $b;          // 3.3333...

// Modulus (remainder)
echo $a % $b;          // 1

// Exponentiation
echo $a ** $b;         // 1000 (10^3)

// Integer division (PHP 7+)
echo intdiv($a, $b);   // 3
?>
```

### Numeric Functions

```php
<?php
// Rounding
echo round(3.4);       // 3
echo round(3.5);       // 4
echo round(3.14159, 2);// 3.14

// Ceiling and floor
echo ceil(3.1);        // 4 (round up)
echo floor(3.9);       // 3 (round down)

// Absolute value
echo abs(-10);         // 10

// Min and max
echo min(3, 5, 1);     // 1
echo max(3, 5, 1);     // 5

// Power and square root
echo pow(2, 3);        // 8 (2^3)
echo sqrt(16);         // 4

// Random numbers
echo rand();           // Random integer
echo rand(1, 10);      // Random between 1-10
?>
```

## Number Formatting

```php
<?php
// Format number with thousand separators
$number = 1234567.89;
echo number_format($number);           // 1,234,568
echo number_format($number, 2);        // 1,234,567.89
echo number_format($number, 2, '.', ','); // 1,234,567.89

// Currency formatting
$price = 19.99;
echo "Price: \$" . number_format($price, 2); // Price: $19.99
?>
```

## Practical Examples

### Calculate Average

```php
<?php
$scores = [85, 90, 78, 92];
$total = 0;

foreach ($scores as $score) {
    $total += $score;
}

$average = $total / count($scores);
echo "Average: " . round($average, 2); // Average: 86.25
?>
```

### Simple Interest Calculator

```php
<?php
// Simple Interest = (Principal × Rate × Time) / 100
$principal = 1000;      // Starting amount
$rate = 5;              // Interest rate (5%)
$time = 2;              // Years

$interest = ($principal * $rate * $time) / 100;
$total = $principal + $interest;

echo "Principal: \$" . $principal . "\n";
echo "Interest: \$" . number_format($interest, 2) . "\n";
echo "Total: \$" . number_format($total, 2) . "\n";
?>
```

## Key Takeaways

✓ **Integer**: Whole numbers without decimal points
✓ **Float**: Numbers with decimal points or exponential notation
✓ PHP automatically converts between integers and floats
✓ Use `is_int()` and `is_float()` to check types
✓ Use `number_format()` for displaying numbers to users
✓ Use numeric functions for rounding, min/max, etc.
✓ Integers and floats are essential for mathematical operations

