# Assignment Operators in PHP

## What are Assignment Operators?

Assignment operators are used to assign values to variables. The most basic is the equals sign (`=`), but PHP also provides compound assignment operators that combine an operation with assignment for convenience.

```
Simple assignment:        $x = 5
Add and assign:          $x += 3   // Same as: $x = $x + 3
Subtract and assign:     $x -= 2   // Same as: $x = $x - 2
Multiply and assign:     $x *= 4   // Same as: $x = $x * 4
```

## Basic Assignment Operator

### Simple Assignment (=)

```php
<?php
// Basic assignment
$name = "John";
$age = 25;
$height = 5.9;
$is_active = true;

echo $name;      // John
echo $age;       // 25

// Assignment returns the assigned value
$x = $y = $z = 10;
echo $x;  // 10
echo $y;  // 10
echo $z;  // 10

// Reassignment
$name = "Alice";  // Previous value is overwritten
echo $name;       // Alice
?>
```

## Compound Assignment Operators

### Addition Assignment (+=)

Adds a value to a variable and assigns the result.

```php
<?php
$x = 10;
$x += 5;    // Same as: $x = $x + 5
echo $x;    // Output: 15

// Practical example: accumulating totals
$total_price = 100;
$tax = 10;
$total_price += $tax;
echo $total_price;  // Output: 110

// Works with strings (concatenation)
$greeting = "Hello";
$greeting += " World";  // Concatenates
echo $greeting;  // Output: Hello World
?>
```

### Subtraction Assignment (-=)

Subtracts a value from a variable and assigns the result.

```php
<?php
$x = 10;
$x -= 3;    // Same as: $x = $x - 3
echo $x;    // Output: 7

// Practical example: counting down
$remaining_items = 100;
$sold = 15;
$remaining_items -= $sold;
echo $remaining_items;  // Output: 85

// Works with floats
$balance = 1000.50;
$balance -= 250.25;
echo $balance;  // Output: 750.25
?>
```

### Multiplication Assignment (*=)

Multiplies a variable by a value and assigns the result.

```php
<?php
$x = 10;
$x *= 2;    // Same as: $x = $x * 2
echo $x;    // Output: 20

// Practical example: scaling values
$width = 100;
$width *= 1.5;  // Increase by 50%
echo $width;    // Output: 150

// Double and triple
$number = 5;
$number *= 3;
echo $number;  // Output: 15
?>
```

### Division Assignment (/=)

Divides a variable by a value and assigns the result.

```php
<?php
$x = 20;
$x /= 4;    // Same as: $x = $x / 4
echo $x;    // Output: 5

// Practical example: averaging
$total = 300;
$count = 3;
$total /= $count;
echo $total;  // Output: 100

// Percentage calculation
$price = 100;
$price /= 2;  // 50% of original
echo $price;  // Output: 50
?>
```

### Modulo Assignment (%=)

Performs modulo operation and assigns the result.

```php
<?php
$x = 17;
$x %= 5;    // Same as: $x = $x % 5
echo $x;    // Output: 2

// Practical example: cycling through values
$day = 10;
$day %= 7;  // Get day of week
echo $day;  // Output: 3

// Check remaining quantity
$items = 25;
$items %= 10;  // Remainder after packing
echo $items;  // Output: 5
?>
```

### Exponentiation Assignment (**=)

Raises a variable to a power and assigns the result (PHP 5.6+).

```php
<?php
$x = 2;
$x **= 3;   // Same as: $x = $x ** 3
echo $x;    // Output: 8

// Practical example: compound growth
$value = 10;
$value **= 2;  // Square the value
echo $value;   // Output: 100

// Population growth
$population = 2;
$population **= 10;  // Double 10 times
echo $population;    // Output: 1024
?>
```

## String Assignment Operators

### Concatenation Assignment (.=)

Appends a string to a variable.

```php
<?php
$text = "Hello";
$text .= " World";
echo $text;  // Output: Hello World

// Building strings incrementally
$message = "Welcome, ";
$message .= "John";
$message .= "!";
echo $message;  // Output: Welcome, John!

// Building HTML
$html = "<div>";
$html .= "<p>Hello</p>";
$html .= "</div>";
echo $html;  // Output: <div><p>Hello</p></div>
?>
```

## All Assignment Operators Summary

| Operator | Same As | Example | Result |
|----------|---------|---------|--------|
| = | Assignment | $x = 5 | $x = 5 |
| += | Add and assign | $x += 3 | $x = $x + 3 |
| -= | Subtract and assign | $x -= 2 | $x = $x - 2 |
| *= | Multiply and assign | $x *= 4 | $x = $x * 4 |
| /= | Divide and assign | $x /= 2 | $x = $x / 2 |
| %= | Modulo and assign | $x %= 5 | $x = $x % 5 |
| **= | Exponent and assign | $x **= 2 | $x = $x ** 2 |
| .= | Concatenate and assign | $x .= "!" | $x = $x . "!" |

## Practical Examples

### Shopping Cart Total

```php
<?php
$cart_total = 0;

// Add items to cart
$item1_price = 29.99;
$cart_total += $item1_price;

$item2_price = 15.50;
$cart_total += $item2_price;

$item3_price = 42.00;
$cart_total += $item3_price;

// Apply discount
$discount = 5.00;
$cart_total -= $discount;

// Add tax
$tax_rate = 0.08;  // 8% tax
$tax = $cart_total * $tax_rate;
$cart_total += $tax;

echo "Total: $" . round($cart_total, 2);  // Total: $95.17
?>
```

### Building a Report

```php
<?php
$report = "";

// Build header
$report .= "=== Monthly Report ===\n";
$report .= "Date: " . date("Y-m-d") . "\n";
$report .= "---\n";

// Add sections
$report .= "Sales: $5000\n";
$report .= "Expenses: $2000\n";
$report .= "Net: $3000\n";

// Add footer
$report .= "---\n";
$report .= "End of Report";

echo $report;
?>
```

### Scaling and Adjusting

```php
<?php
// Image dimensions
$width = 800;
$height = 600;

// Scale to 75%
$width *= 0.75;
$height *= 0.75;

echo "New dimensions: {$width}x{$height}";  // New dimensions: 600x450

// Double the font size
$font_size = 14;
$font_size *= 2;
echo "New font size: {$font_size}px";  // New font size: 28px
?>
```

### Counter and Accumulator

```php
<?php
$count = 0;
$sum = 0;
$product = 1;

// Simulate processing items
for ($i = 1; $i <= 5; $i++) {
    $count += 1;           // Increment count
    $sum += $i;           // Add to sum
    $product *= $i;       // Multiply product
}

echo "Count: " . $count;        // Count: 5
echo "Sum: " . $sum;            // Sum: 15
echo "Product: " . $product;    // Product: 120 (5!)
?>
```

### Building Dynamic SQL

```php
<?php
$query = "SELECT * FROM users";

// Add conditions
if ($has_role) {
    $query .= " WHERE role = 'admin'";
}

if ($is_active) {
    $query .= " AND is_active = 1";
}

// Add ordering
$query .= " ORDER BY created_at DESC";

// Add limit
$query .= " LIMIT 10";

echo $query;
// Output: SELECT * FROM users WHERE role = 'admin' AND is_active = 1 ORDER BY created_at DESC LIMIT 10
?>
```

## Assignment Chaining

Multiple assignments can be chained together:

```php
<?php
// Chain assignment from right to left
$a = $b = $c = 10;

echo $a;  // 10
echo $b;  // 10
echo $c;  // 10

// Changing one doesn't affect others
$a = 20;
echo $b;  // Still 10 (they're independent after assignment)
?>
```

## Important Notes

### Assignment vs Comparison

```php
<?php
// Common mistake: Using = instead of ==
$x = 5;

if ($x = 10) {        // ✗ Wrong! Assigns 10 to $x
    echo "True";     // This executes because assignment returns value
}
echo $x;  // 10 (value was changed!)

if ($x == 10) {       // ✓ Correct! Compares values
    echo "True";
}
?>
```

### Assignment Returns Value

```php
<?php
// Assignment is an expression that returns the assigned value
$x = ($y = 5) + 3;
echo $x;  // 8
echo $y;  // 5

// Can use in conditions
if ($name = "John") {
    echo "Name is: " . $name;  // Works because "John" is truthy
}
?>
```

### Performance with Strings

```php
<?php
// String concatenation assignment is efficient
$output = "";
$output .= "Line 1\n";
$output .= "Line 2\n";
$output .= "Line 3\n";
echo $output;

// More efficient than string concatenation in expressions
echo "Better: " . $output;
?>
```

## Common Pitfalls

### Confusing = and ==

```php
<?php
$x = 5;

// Wrong: Assignment in condition
if ($x = 10) {
    echo "Assigns 10 to x, then evaluates to true";
    echo $x;  // x is now 10!
}

// Right: Comparison
if ($x == 10) {
    echo "Checks if x equals 10 without changing it";
}

// Most safe: Strict comparison
if ($x === 10) {
    echo "Checks type and value equality";
}
?>
```

### Reference Assignment

```php
<?php
// Normal assignment (copies value)
$a = 5;
$b = $a;
$b = 10;
echo $a;  // 5 (unchanged)

// Reference assignment (same variable)
$x = 5;
$y = &$x;    // $y references $x
$y = 10;
echo $x;  // 10 (changed! They're the same variable)
?>
```

## Key Takeaways

✓ Assignment operator (=) assigns a value to a variable
✓ **Compound operators** combine an operation with assignment (+=, -=, *=, /=, %=, **=, .=)
✓ **Compound operators are more concise** than separate operation and assignment
✓ **Don't confuse = (assignment) with == or === (comparison)**
✓ **Assignment is an expression** that returns the assigned value
✓ **String concatenation assignment (.=)** efficiently builds strings
✓ **Chaining assignments** from right to left works: $a = $b = $c = 10
✓ **Reference assignment (&=)** creates alias to same variable
✓ Use assignment operators to **update and accumulate values** efficiently
