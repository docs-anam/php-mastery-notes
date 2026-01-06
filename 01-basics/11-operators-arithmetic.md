# Operators - Arithmetic Operations

## Table of Contents
1. [Overview](#overview)
2. [Basic Operators](#basic-operators)
3. [Operator Precedence](#operator-precedence)
4. [Combining Operations](#combining-operations)
5. [Common Patterns](#common-patterns)
6. [Common Mistakes](#common-mistakes)

---

## Overview

Arithmetic operators perform mathematical calculations on numbers.

| Operator | Name | Example | Result |
|----------|------|---------|--------|
| `+` | Addition | `5 + 3` | 8 |
| `-` | Subtraction | `5 - 3` | 2 |
| `*` | Multiplication | `5 * 3` | 15 |
| `/` | Division | `15 / 3` | 5 |
| `%` | Modulo (Remainder) | `17 % 5` | 2 |
| `**` | Exponentiation | `2 ** 3` | 8 |

---

## Basic Operators

### Addition (+)

```php
<?php
$a = 10;
$b = 5;

$sum = $a + $b;  // 15

// With floats
$price1 = 19.99;
$price2 = 9.99;
$total = $price1 + $price2;  // 29.98

// With strings (type juggling - careful!)
$result = "5" + 3;  // 8 (string converted to number)
?>
```

### Subtraction (-)

```php
<?php
$total = 100;
$spent = 35;

$remaining = $total - $spent;  // 65

// Negative results
$balance = 50;
$withdrawal = 75;
$newBalance = $balance - $withdrawal;  // -25
?>
```

### Multiplication (*)

```php
<?php
$quantity = 5;
$price = 19.99;

$totalCost = $quantity * $price;  // 99.95

// Area calculation
$width = 10;
$height = 20;
$area = $width * $height;  // 200
?>
```

### Division (/)

```php
<?php
// Simple division
$total = 100;
$people = 3;
$perPerson = $total / $people;  // 33.333...

// Integer division (PHP 7+)
$quotient = intdiv(10, 3);  // 3 (integer only)

// Regular division always returns float if needed
var_dump(10 / 2);       // float(5.0)
var_dump(10 / 3);       // float(3.3333...)
?>
```

### Modulo (%)

Remainder after division:

```php
<?php
// Check if even or odd
$num = 17;
if ($num % 2 == 0) {
    echo "Even";
} else {
    echo "Odd";  // 17 % 2 = 1, so odd
}

// Every nth item
for ($i = 1; $i <= 10; $i++) {
    if ($i % 3 == 0) {
        echo "$i is divisible by 3\n";  // 3, 6, 9
    }
}

// Cycle through values
$index = 7;
$color_index = $index % 3;  // Wraps 0-2
?>
```

### Exponentiation (**)

```php
<?php
// Squares
$base = 5;
$squared = $base ** 2;  // 25

// Cubes
$cubed = $base ** 3;    // 125

// Powers
2 ** 3;   // 8
2 ** 10;  // 1024

// Fractional exponents
4 ** 0.5;  // 2.0 (square root)
8 ** (1/3);  // 2.0 (cube root)
?>
```

---

## Operator Precedence

Operations follow a specific order (PEMDAS/BODMAS):

| Order | Operators |
|-------|-----------|
| 1st | `**` (Exponentiation) |
| 2nd | `*`, `/`, `%` (Multiplication, Division, Modulo) |
| 3rd | `+`, `-` (Addition, Subtraction) |
| 4th | Left to right |

### Examples

```php
<?php
// Without understanding precedence
$result = 2 + 3 * 4;  // 14, not 20
// Multiply first (3 * 4 = 12), then add (2 + 12 = 14)

// Use parentheses for clarity
$result = (2 + 3) * 4;  // 20
$result = 2 + (3 * 4);  // 14

// Complex calculation
$total = 10 + 5 * 2 - 3 / 2;
// = 10 + 10 - 1.5 = 18.5

// With exponentiation
$result = 2 + 3 ** 2;  // 11, not 25
// Exponent first (3 ** 2 = 9), then add (2 + 9 = 11)
?>
```

---

## Combining Operations

### Chaining Operations

```php
<?php
$a = 10;
$b = 5;
$c = 2;

// Multiple operations
$result = $a + $b - $c;        // 13
$result = $a * $b / $c;        // 25
$result = $a + $b * $c;        // 20

// With parentheses for clarity
$result = ($a + $b) * $c;      // 30
$result = $a + ($b * $c);      // 20
?>
```

### Building Expressions

```php
<?php
// Calculate sales tax
$amount = 100;
$tax_rate = 0.08;  // 8%
$total = $amount + ($amount * $tax_rate);  // 108

// Alternative
$total = $amount * (1 + $tax_rate);  // 108

// Calculate discount
$original = 50;
$discount = 0.20;  // 20%
$final = $original * (1 - $discount);  // 40
?>
```

---

## Common Patterns

### Increment/Decrement

```php
<?php
// Increment
$count = 5;
$count = $count + 1;  // 6

// Shorthand (see increment-decrement topic)
$count += 1;  // 6
$count++;     // 7

// Decrement
$count--;     // 6
$count -= 1;  // 5
?>
```

### Calculation with Variables

```php
<?php
// Calculate average
$scores = [85, 90, 78, 92];
$sum = 85 + 90 + 78 + 92;  // Or use array_sum()
$average = $sum / 4;  // 86.25

// Better approach
$sum = array_sum($scores);
$average = $sum / count($scores);
?>
```

### Running Totals

```php
<?php
$total = 0;

$total = $total + 10;  // 10
$total = $total + 20;  // 30
$total = $total + 15;  // 45

// Shorthand
$total += 10;  // 55
?>
```

---

## Common Mistakes

### 1. String to Number Conversion

```php
<?php
$str = "10";
$num = 5;

$result = $str + $num;  // 15 (string converted to number)
var_dump($result);      // int(15) - but this is risky!

// Better: explicitly convert
$str = "10";
$num = (int)$str + 5;   // Clearly intentional
?>
```

### 2. Division by Zero

```php
<?php
$a = 10;
$b = 0;

$result = $a / $b;  // Warning! Division by zero
// Result: INF (infinity)

// Always check
if ($b != 0) {
    $result = $a / $b;
} else {
    $result = 0;  // Or handle as appropriate
}
?>
```

### 3. Modulo with Floats

```php
<?php
// Modulo works with floats but can be imprecise
$result = 5.5 % 2.2;  // Might not be exact

// Better: use floordiv for integer division
$result = intdiv(10, 3);  // 3
?>
```

### 4. Operator Precedence Issues

```php
<?php
// Wrong precedence assumption
$result = 10 - 5 - 2;  // 3, not 7
// Left to right: (10 - 5) - 2 = 5 - 2 = 3

// Use parentheses for clarity
$result = (10 - 5) - 2;  // 3 (clear)
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

// Invoice calculation
$items = [
    ['name' => 'Widget', 'quantity' => 5, 'price' => 19.99],
    ['name' => 'Gadget', 'quantity' => 2, 'price' => 49.99],
    ['name' => 'Doohickey', 'quantity' => 3, 'price' => 9.99],
];

$subtotal = 0;

foreach ($items as $item) {
    $item_total = $item['quantity'] * $item['price'];
    $subtotal += $item_total;
    
    echo $item['name'] . ": " . $item['quantity'] . " × $" . 
         number_format($item['price'], 2) . " = $" . 
         number_format($item_total, 2) . "\n";
}

// Calculate total with tax
$tax_rate = 0.08;
$tax = $subtotal * $tax_rate;
$total = $subtotal + $tax;

echo "\nSubtotal: $" . number_format($subtotal, 2) . "\n";
echo "Tax (8%): $" . number_format($tax, 2) . "\n";
echo "Total: $" . number_format($total, 2) . "\n";
?>
```

**Output:**
```
Widget: 5 × $19.99 = $99.95
Gadget: 2 × $49.99 = $99.98
Doohickey: 3 × $9.99 = $29.97

Subtotal: $229.90
Tax (8%): $18.39
Total: $248.29
```

---

## Next Steps

✅ Understand arithmetic operators  
→ Learn [assignment operators](11-operators-assignment.md)  
→ Study [comparison operators](12-operators-comparison.md)  
→ Master [logical operators](13-operators-logical.md)
