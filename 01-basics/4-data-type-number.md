# Data Types - Numbers (Integers and Floats)

## Table of Contents
1. [Overview](#overview)
2. [Integers](#integers)
3. [Floats](#floats)
4. [Type Checking](#type-checking)
5. [Numeric Functions](#numeric-functions)
6. [Common Mistakes](#common-mistakes)

---

## Overview

PHP supports two main numeric data types:

| Type | Description | Range | Example |
|------|-------------|-------|---------|
| **Integer** | Whole numbers | -2,147,483,648 to 2,147,483,647 | `42`, `-100` |
| **Float** | Decimal numbers | Up to 1.7976931348623E+308 | `3.14`, `-0.5` |

### Key Characteristics

```php
<?php
// Integers - no decimal point
$int = 42;
$negative = -100;
$zero = 0;

// Floats - with decimal point
$float = 3.14159;
$decimal = -0.5;
$scientific = 1.23e-4;  // Scientific notation

// Type checking
var_dump($int);      // int(42)
var_dump($float);    // float(3.14159)
?>
```

---

## Integers

### Creating Integers

```php
<?php
// Decimal (base 10)
$decimal = 42;
$negative = -100;

// Hexadecimal (base 16) - prefix with 0x
$hex = 0x2A;         // 42 in decimal
$hex_color = 0xFF6633;

// Octal (base 8) - prefix with 0
$octal = 052;        // 42 in decimal

// Binary (base 2) - prefix with 0b
$binary = 0b101010;  // 42 in decimal

// All equal to 42
var_dump($decimal, $hex, $octal, $binary);
// All show: int(42)
?>
```

### Integer Range

```php
<?php
// Check integer boundaries
echo PHP_INT_MAX;     // 9223372036854775807
echo PHP_INT_MIN;     // -9223372036854775808
echo PHP_INT_SIZE;    // 8 (bytes)

// If a number exceeds integer range, it becomes a float
$big = 9223372036854775808;  // Exceeds INT_MAX
var_dump($big);              // float(9.2233720368548E+18)
?>
```

### Integer Operations

```php
<?php
// Basic arithmetic
$a = 10;
$b = 3;

$sum = $a + $b;        // 13
$difference = $a - $b; // 7
$product = $a * $b;    // 30
$quotient = $a / $b;   // 3.333...
$remainder = $a % $b;  // 1
$power = $a ** $b;     // 1000 (10^3)

// Auto-convert if needed
$result = $a / $b;     // Float (3.333...)
var_dump($result);     // float(3.3333...)
?>
```

### Integer Division

```php
<?php
// Regular division (returns float)
$result = 10 / 3;
var_dump($result);  // float(3.3333...)

// Integer division (PHP 7+)
$result = intdiv(10, 3);
var_dump($result);  // int(3)

// Modulo (remainder)
$remainder = 10 % 3;
var_dump($remainder);  // int(1)
?>
```

---

## Floats

### Creating Floats

```php
<?php
// Decimal notation
$pi = 3.14159;
$gravity = 9.81;
$negative = -2.5;

// Scientific notation
$scientific1 = 1.23e-4;  // 0.000123
$scientific2 = 5E+2;     // 500
$very_small = 2e-300;

// Float from division
$result = 10 / 3;  // 3.3333...
?>
```

### Float Precision

```php
<?php
// Floats have limited precision (15-17 significant digits)
$pi = 3.141592653589793;
var_dump($pi);  // float(3.1415926535898)

// Precision issues
$a = 0.1;
$b = 0.2;
$c = $a + $b;
var_dump($c);           // float(0.30000000000001) - Not exactly 0.3!

// Check for "close enough" equality
if (abs($c - 0.3) < 0.0001) {
    echo "Close enough to 0.3";
}

// Use decimal arithmetic for money
// Better: use integers (cents) then convert
$price = 1999;  // 19.99 dollars
$total = $price / 100;  // Convert to dollars
?>
```

### Float Functions

```php
<?php
// Check if float
is_float(3.14);      // true
is_float(3);         // false

// Check if integer
is_int(3);           // true
is_int(3.14);        // false

// Check if numeric
is_numeric("3.14");  // true
is_numeric("3.14x"); // false

// Rounding
round(3.14159, 2);      // 3.14
ceil(3.2);              // 4 (round up)
floor(3.9);             // 3 (round down)

// Absolute value
abs(-3.14);             // 3.14
abs(3.14);              // 3.14
?>
```

---

## Type Checking

### Checking Number Types

```php
<?php
$int = 42;
$float = 3.14;
$string = "42";

// Check type
var_dump(is_int($int));        // true
var_dump(is_int($float));      // false

var_dump(is_float($float));    // true
var_dump(is_float($int));      // false

is_numeric($string);           // true (string "42")
is_numeric("42.5");            // true
is_numeric("42x");             // false

// Check if integer or float
is_numeric($int);              // true
is_numeric($float);            // true
?>
```

### Type Casting

```php
<?php
// Cast to integer
$float = 3.99;
$int = (int)$float;
var_dump($int);  // int(3) - truncated, not rounded

// Cast to float
$int = 42;
$float = (float)$int;
var_dump($float);  // float(42)

// Cast string to number
$str = "123";
$num = (int)$str;
var_dump($num);  // int(123)

$str = "3.14";
$num = (float)$str;
var_dump($num);  // float(3.14)

// Invalid cast
$str = "42x";
$num = (int)$str;
var_dump($num);  // int(42) - reads the number, ignores "x"
?>
```

---

## Numeric Functions

### Common Functions

```php
<?php
// Absolute value
abs(-42);           // 42
abs(3.14);          // 3.14

// Rounding
round(3.4);         // 3
round(3.5);         // 4
round(3.14159, 2);  // 3.14

ceil(3.2);          // 4 (round up)
floor(3.9);         // 3 (round down)

// Min and max
min(5, 2, 8, 1);    // 1
max(5, 2, 8, 1);    // 8
min([5, 2, 8, 1]);  // 1 (with array)

// Power and square root
pow(2, 3);          // 8 (2^3)
sqrt(16);           // 4
sqrt(2);            // 1.4142135...

// Trigonometric
sin(1.5708);        // ~1.0 (sin of pi/2)
cos(0);             // 1.0
tan(0.7854);        // ~1.0 (tan of pi/4)

// Logarithmic
log(2.718);         // 1.0 (natural log of e)
log10(100);         // 2.0
exp(1);             // 2.7182818... (e)

// Random
rand();              // Random integer
rand(1, 100);        // Random between 1-100
mt_rand();           // Better randomness (Mersenne Twister)
random_int(1, 100);  // Cryptographically secure (PHP 7+)
?>
```

### Number Formatting

```php
<?php
// Format number with decimals
number_format(1234.56, 2);        // "1,234.56"
number_format(1234.56, 2, '.', ',');  // "1,234.56"

// European format
number_format(1234.56, 2, ',', '.');  // "1.234,56"

// Currency formatting
$price = 19.99;
echo "$" . number_format($price, 2);  // $19.99

// Significant figures
round(1234.56, -2);         // 1200 (round to hundreds)
round(1234.56, -1);         // 1230 (round to tens)
?>
```

---

## Common Mistakes

### 1. Integer Overflow

```php
<?php
// When number exceeds INT_MAX, it becomes float
$big = 999999999999999999;
var_dump($big);  // float, not int!

// Use is_int() to verify
var_dump(is_int($big));  // false
?>
```

### 2. Float Precision Issues

```php
<?php
// Problem: Floating point precision
if (0.1 + 0.2 == 0.3) {
    echo "Equal";
} else {
    echo "Not equal";  // This runs!
}

// Solution: Compare with tolerance
if (abs((0.1 + 0.2) - 0.3) < 0.0001) {
    echo "Equal (enough)";
}

// For money: use integers (cents)
$price_cents = 1999;  // $19.99
$total_cents = 999;
$payment = $price_cents + $total_cents;
echo "$" . ($payment / 100);  // $29.98
?>
```

### 3. String to Number Conversion

```php
<?php
$str = "42 apples";
$num = (int)$str;
var_dump($num);  // int(42) - reads the number part

$str = "apple 42";
$num = (int)$str;
var_dump($num);  // int(0) - doesn't start with number

// Use is_numeric() first
if (is_numeric($str)) {
    $num = (int)$str;
} else {
    echo "Not a valid number";
}
?>
```

### 4. Integer Division

```php
<?php
// Wrong: regular division
$result = 10 / 3;
var_dump($result);  // float(3.3333...)

// Correct: use intdiv() for integer division
$result = intdiv(10, 3);
var_dump($result);  // int(3)
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

// Calculate circle properties
$radius = 5.5;

// Area: π * r²
$pi = 3.14159;
$area = $pi * pow($radius, 2);

// Circumference: 2π * r
$circumference = 2 * $pi * $radius;

// Format output
echo "Circle with radius: $radius\n";
echo "Area: " . round($area, 2) . " units²\n";
echo "Circumference: " . round($circumference, 2) . " units\n";

// Type checking
echo "Radius type: " . (is_int($radius) ? "Integer" : "Float") . "\n";
echo "Area type: " . (is_float($area) ? "Float" : "Integer") . "\n";
?>
```

**Output:**
```
Circle with radius: 5.5
Area: 95.03 units²
Circumference: 34.56 units
Radius type: Float
Area type: Float
```

---

## Next Steps

✅ Understand integers and floats  
→ Learn [booleans](4-data-type-boolean.md)  
→ Study [strings](5-data-type-string.md)  
→ Practice [operators](10-operators-arithmetic.md)
