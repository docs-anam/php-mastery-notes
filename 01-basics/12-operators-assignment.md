# Operators - Assignment Operators

## Table of Contents
1. [Overview](#overview)
2. [Basic Assignment](#basic-assignment)
3. [Compound Assignment](#compound-assignment)
4. [Shorthand Operators](#shorthand-operators)
5. [String Concatenation Assignment](#string-concatenation-assignment)
6. [Practical Examples](#practical-examples)
7. [Common Mistakes](#common-mistakes)

---

## Overview

Assignment operators assign values to variables and combine assignment with other operations.

| Operator | Name | Example | Equivalent |
|----------|------|---------|------------|
| `=` | Assignment | `$a = 5` | Assign 5 to $a |
| `+=` | Addition | `$a += 3` | `$a = $a + 3` |
| `-=` | Subtraction | `$a -= 2` | `$a = $a - 2` |
| `*=` | Multiplication | `$a *= 4` | `$a = $a * 4` |
| `/=` | Division | `$a /= 2` | `$a = $a / 2` |
| `%=` | Modulo | `$a %= 3` | `$a = $a % 3` |
| `.=` | Concatenation | `$a .= "x"` | `$a = $a . "x"` |
| `**=` | Exponentiation | `$a **= 2` | `$a = $a ** 2` |

---

## Basic Assignment

### Simple Assignment

```php
<?php
// Assign value
$name = "Alice";
$age = 25;
$price = 19.99;

// Assign to multiple variables at once
$x = $y = $z = 0;  // All equal 0

// Assign from expression
$total = 10 + 5;   // 15
$area = 5 * 4;     // 20
?>
```

### Return Value of Assignment

Assignment expressions return the assigned value:

```php
<?php
// Assignment returns the value
$a = ($b = 5);  // $a = 5, $b = 5

// Can use in conditionals
if ($value = getUserData()) {
    echo "Got: $value";
}

// Can assign and use in one statement
echo $result = 5 + 3;  // Outputs: 8
?>
```

---

## Compound Assignment

Combine assignment with arithmetic operations:

### Addition Assignment (+=)

```php
<?php
$count = 10;
$count += 5;   // $count = $count + 5
echo $count;   // 15

// Practical example: accumulate
$total = 0;
$total += 100;  // 100
$total += 50;   // 150
$total += 25;   // 175
echo $total;    // 175
?>
```

### Subtraction Assignment (-=)

```php
<?php
$balance = 100;
$balance -= 25;  // $balance = $balance - 25
echo $balance;   // 75

// Practical: balance operations
$inventory = 50;
$inventory -= 5;  // Sold 5 items
echo $inventory;  // 45
?>
```

### Multiplication Assignment (*=)

```php
<?php
$quantity = 10;
$quantity *= 2;  // $quantity = $quantity * 2
echo $quantity;  // 20

// Practical: scale values
$price = 100;
$price *= 1.1;   // Add 10% tax
echo $price;     // 110
?>
```

### Division Assignment (/=)

```php
<?php
$total = 100;
$total /= 4;  // $total = $total / 4
echo $total;  // 25

// Practical: divide equally
$earnings = 1000;
$earnings /= 4;  // Divide by 4 people
echo $earnings;  // 250
?>
```

### Modulo Assignment (%=)

```php
<?php
$number = 17;
$number %= 5;  // $number = $number % 5
echo $number;  // 2

// Practical: cycle through values
$index = 7;
$index %= 3;   // Keep in range 0-2
echo $index;   // 1
?>
```

### Exponentiation Assignment (**=)

```php
<?php
$base = 2;
$base **= 3;   // $base = $base ** 3
echo $base;    // 8

// Practical: power calculations
$multiplier = 10;
$multiplier **= 2;  // Square it
echo $multiplier;   // 100
?>
```

---

## String Concatenation Assignment

### Concatenation Operator (.=)

```php
<?php
$message = "Hello";
$message .= " World";      // $message = $message . " World"
echo $message;             // Hello World

// Building strings
$output = "";
$output .= "Name: Alice\n";
$output .= "Age: 25\n";
$output .= "Email: alice@example.com\n";
echo $output;
?>
```

### Practical String Building

```php
<?php
// Build HTML
$html = "";
$html .= "<div>";
$html .= "<p>Hello</p>";
$html .= "</div>";
echo $html;
// Output: <div><p>Hello</p></div>

// Build SQL
$query = "SELECT * FROM users ";
$query .= "WHERE age > 18 ";
$query .= "AND status = 'active'";
echo $query;
?>
```

---

## Shorthand Operators

### Increment/Decrement (See dedicated topic)

```php
<?php
$count = 0;
$count++;     // $count = $count + 1
$count--;     // $count = $count - 1
$count += 2;  // $count = $count + 2
?>
```

---

## Practical Examples

### Running Total

```php
<?php
// Initialize
$total = 0;
$items = [19.99, 29.99, 9.99, 49.99];

// Process each item
foreach ($items as $price) {
    $total += $price;
}

echo "Total: $" . number_format($total, 2);  // Total: $109.96
?>
```

### Building Configuration

```php
<?php
// Start with base config
$config = [
    'host' => 'localhost',
    'port' => 3306,
];

// Add optional settings
if (function_exists('mysqli_connect')) {
    $config['driver'] = 'mysqli';
}

$config['timeout'] = 30;
$config['charset'] = 'utf8mb4';

print_r($config);
?>
```

### Accumulating Statistics

```php
<?php
$stats = [
    'views' => 0,
    'clicks' => 0,
    'conversions' => 0,
];

// User 1 stats
$stats['views'] += 100;
$stats['clicks'] += 5;
$stats['conversions'] += 1;

// User 2 stats
$stats['views'] += 200;
$stats['clicks'] += 8;
$stats['conversions'] += 2;

// Results
echo "Views: " . $stats['views'] . "\n";         // 300
echo "Clicks: " . $stats['clicks'] . "\n";       // 13
echo "Conversions: " . $stats['conversions'];    // 3
?>
```

---

## Common Mistakes

### 1. Assignment vs Comparison

```php
<?php
// ❌ Wrong: assignment (=)
if ($count = 5) {
    echo "Count is 5";  // Sets $count to 5, always true
}

// ✅ Correct: comparison (==)
if ($count == 5) {
    echo "Count is 5";
}

// ✅ Better: strict comparison (===)
if ($count === 5) {
    echo "Count is 5";
}
?>
```

### 2. Wrong Operator Order

```php
<?php
// ❌ Doesn't work (no +=- operator)
$value =+ 5;  // Sets to +5 (positive 5), not increment!

// ✅ Correct
$value += 5;  // Increments by 5

// ❌ Easy mistake
$value = -5;  // Assigns negative 5
$value -= 5;  // Decrements by 5
?>
```

### 3. Type Coercion Issues

```php
<?php
$string = "10";
$string += 5;   // Converts "10" to int, result: 15

$string = "hello";
$string += 5;   // Converts "hello" to 0, result: 5

// Better: be explicit
$number = (int)"10";
$number += 5;   // 15
?>
```

### 4. Forgetting Side Effect

```php
<?php
// ❌ Not updating original
$original = 10;
$copy = $original;
$copy += 5;
echo $original;  // Still 10, not 15

// ✅ Updating original
$original += 5;  // Now 15
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

// Shopping cart calculation
$cart = [
    'items' => 3,
    'subtotal' => 0.0,
    'tax' => 0.0,
    'total' => 0.0,
    'description' => ''
];

// Add items
$prices = [19.99, 29.99, 9.99];

foreach ($prices as $price) {
    $cart['subtotal'] += $price;
    $cart['description'] .= "$" . number_format($price, 2) . ", ";
}

// Calculate tax (8%)
$cart['tax'] = $cart['subtotal'] * 0.08;

// Calculate total
$cart['total'] = $cart['subtotal'] + $cart['tax'];

// Remove trailing comma
$cart['description'] = rtrim($cart['description'], ", ");

// Display
echo "Items: " . $cart['items'] . "\n";
echo "Items: " . $cart['description'] . "\n";
echo "Subtotal: $" . number_format($cart['subtotal'], 2) . "\n";
echo "Tax: $" . number_format($cart['tax'], 2) . "\n";
echo "Total: $" . number_format($cart['total'], 2) . "\n";
?>
```

**Output:**
```
Items: 3
Items: $19.99, $29.99, $9.99
Subtotal: $59.97
Tax: $4.80
Total: $64.77
```

---

## Next Steps

✅ Understand assignment operators  
→ Learn [arithmetic operators](10-operators-arithmetic.md)  
→ Study [comparison operators](12-operators-comparison.md)  
→ Master [increment/decrement](14-operators-increment-decrement.md)
