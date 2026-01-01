# For Loop in PHP

## Overview

The for loop is used to execute a block of code a specific number of times. It's ideal when you know exactly how many times you want to repeat something, making it the most common loop for counting operations.

## Basic For Loop

### Simple For Loop

```php
<?php
// For loop syntax: for (init; condition; increment)
for ($i = 0; $i < 5; $i++) {
    echo $i . " ";  // Output: 0 1 2 3 4
}

// Loop with different starting point
for ($i = 1; $i <= 10; $i++) {
    echo $i . " ";  // Output: 1 2 3 4 5 6 7 8 9 10
}

// Counting down
for ($i = 10; $i >= 1; $i--) {
    echo $i . " ";  // Output: 10 9 8 7 6 5 4 3 2 1
}
?>
```

### Loop Components

```php
<?php
// Three parts of for loop:
// 1. Initialization ($i = 0) - runs once at start
// 2. Condition ($i < 5) - checked before each iteration
// 3. Increment ($i++) - runs after each iteration

for ($i = 0; $i < 3; $i++) {
    echo "Iteration " . ($i + 1) . "\n";
}
// Output:
// Iteration 1
// Iteration 2
// Iteration 3
?>
```

## Multiple Variables

### Multiple Loop Variables

```php
<?php
// Multiple initialization and increment
for ($i = 0, $j = 10; $i < 5; $i++, $j--) {
    echo "i=$i, j=$j ";  // i=0, j=10 i=1, j=9 i=2, j=8 i=3, j=7 i=4, j=6
}

// Using commas to split operations
for ($x = 0, $y = 100; $x < 5; $x++, $y -= 20) {
    echo "x=$x, y=$y ";
}
?>
```

## Practical Examples

### Multiplication Table

```php
<?php
function printMultiplicationTable($number) {
    echo "Multiplication table for $number:\n";
    for ($i = 1; $i <= 10; $i++) {
        echo "$number x $i = " . ($number * $i) . "\n";
    }
}

printMultiplicationTable(5);
// Output:
// Multiplication table for 5:
// 5 x 1 = 5
// 5 x 2 = 10
// ... etc
?>
```

### Array Iteration

```php
<?php
$fruits = ["Apple", "Banana", "Orange", "Mango"];

// Loop through array by index
for ($i = 0; $i < count($fruits); $i++) {
    echo ($i + 1) . ". " . $fruits[$i] . "\n";
}
// Output:
// 1. Apple
// 2. Banana
// 3. Orange
// 4. Mango

// Reverse iteration
for ($i = count($fruits) - 1; $i >= 0; $i--) {
    echo $fruits[$i] . " ";  // Mango Orange Banana Apple
}
?>
```

### Nested Loops - Number Pattern

```php
<?php
// 3x3 grid
for ($i = 1; $i <= 3; $i++) {
    for ($j = 1; $j <= 3; $j++) {
        echo $i . $j . " ";
    }
    echo "\n";
}
// Output:
// 11 12 13
// 21 22 23
// 31 32 33
?>
```

### Creating Multiplication Grid

```php
<?php
// Print multiplication table grid
echo "   ";
for ($i = 1; $i <= 5; $i++) {
    echo " " . $i;
}
echo "\n";

for ($i = 1; $i <= 5; $i++) {
    echo $i . " |";
    for ($j = 1; $j <= 5; $j++) {
        echo " " . ($i * $j);
    }
    echo "\n";
}
?>
```

### Factorial Calculation

```php
<?php
function calculateFactorial($number) {
    $result = 1;
    for ($i = 1; $i <= $number; $i++) {
        $result *= $i;
    }
    return $result;
}

echo calculateFactorial(5);  // Output: 120 (5! = 5*4*3*2*1)
echo calculateFactorial(7);  // Output: 5040
?>
```

### Star Pattern

```php
<?php
// Triangle pattern
for ($i = 1; $i <= 5; $i++) {
    for ($j = 1; $j <= $i; $j++) {
        echo "*";
    }
    echo "\n";
}
// Output:
// *
// **
// ***
// ****
// *****

// Reverse triangle
for ($i = 5; $i >= 1; $i--) {
    for ($j = 1; $j <= $i; $j++) {
        echo "*";
    }
    echo "\n";
}
// Output:
// *****
// ****
// ***
// **
// *
?>
```

### Sum and Average

```php
<?php
$numbers = [10, 20, 30, 40, 50];
$sum = 0;

for ($i = 0; $i < count($numbers); $i++) {
    $sum += $numbers[$i];
}

$average = $sum / count($numbers);

echo "Sum: $sum\n";      // Output: Sum: 150
echo "Average: $average"; // Output: Average: 30
?>
```

## Loop Control

### Break Statement

```php
<?php
// Exit loop early
for ($i = 0; $i < 10; $i++) {
    if ($i == 5) {
        break;  // Exit loop when i equals 5
    }
    echo $i . " ";  // Output: 0 1 2 3 4
}

// Find element in array
$items = ["apple", "banana", "orange", "grape"];
for ($i = 0; $i < count($items); $i++) {
    if ($items[$i] == "orange") {
        echo "Found at index: $i";
        break;
    }
}
?>
```

### Continue Statement

```php
<?php
// Skip to next iteration
for ($i = 0; $i < 5; $i++) {
    if ($i == 2) {
        continue;  // Skip iteration when i equals 2
    }
    echo $i . " ";  // Output: 0 1 3 4
}

// Skip even numbers
for ($i = 1; $i <= 10; $i++) {
    if ($i % 2 == 0) {
        continue;  // Skip even numbers
    }
    echo $i . " ";  // Output: 1 3 5 7 9
}
?>
```

## Common Pitfalls

### Off-by-One Error

```php
<?php
$array = ["a", "b", "c"];

// WRONG - accesses index 3 which doesn't exist
for ($i = 0; $i <= count($array); $i++) {
    echo $array[$i];  // Notice: Undefined array key 3
}

// CORRECT - use < instead of <=
for ($i = 0; $i < count($array); $i++) {
    echo $array[$i];  // Output: abc
}
?>
```

### Modifying Loop Variable

```php
<?php
// DON'T modify $i inside the loop
for ($i = 0; $i < 5; $i++) {
    echo $i . " ";
    $i += 2;  // Dangerous! Changes loop behavior
}
// Output: 0 3 (unexpected)

// Safe: use different variable
for ($i = 0; $i < 5; $i++) {
    $value = $i + 2;
    echo $value . " ";  // Output: 2 3 4 5 6
}
?>
```

### Empty Loop Header

```php
<?php
// Valid but confusing - infinite loop without condition change
for (;;) {
    echo "This runs forever";
    break;  // Must break manually
}

// Better to use while for infinite loops
while (true) {
    echo "This runs forever";
    break;
}
?>
```

## For vs Foreach

### When to Use For

```php
<?php
// Good for: indexed operations, access to index
$items = ["apple", "banana", "orange"];

for ($i = 0; $i < count($items); $i++) {
    echo "Item " . ($i + 1) . ": " . $items[$i] . "\n";
}
// Better because we need the index number
?>
```

### When to Use Foreach

```php
<?php
// Good for: simple iteration, don't need index
$items = ["apple", "banana", "orange"];

foreach ($items as $item) {
    echo $item . "\n";
}
// Better because we don't need the index
?>
```

## Best Practices

✓ **Use count() correctly** - call once or store in variable
✓ **Start from 0 or 1** depending on needs
✓ **Use < for conditions** to avoid off-by-one errors
✓ **Use foreach** when you don't need the index
✓ **Break early** when condition is met
✓ **Use meaningful variable names** ($i for simple index)
✓ **Keep loop body simple** - extract to functions
✓ **Avoid modifying** the loop variable

## Key Takeaways

✓ **For loop** executes code a specific number of times
✓ **Three parts**: initialization, condition, increment
✓ **Condition checked** before each iteration
✓ **Break exits** the loop immediately
✓ **Continue skips** to next iteration
✓ **Common pitfall**: off-by-one with array access
✓ **Foreach preferred** for simple array iteration
✓ **Multiple variables** supported in loop header
✓ **Nested loops** create 2D/3D patterns
✓ **Counter starts at 0** - remember array indices start at 0
