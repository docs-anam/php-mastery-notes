# Comparison Operators in PHP

## What are Comparison Operators?

Comparison operators are used to compare two values and return a boolean result (`true` or `false`). They are essential for making decisions in your code through conditional statements.

```
$a = 10
$b = 5

Equal:           $a == $b  → false
Not equal:       $a != $b  → true
Greater than:    $a > $b   → true
Less than:       $a < $b   → false
Greater or equal: $a >= $b → true
Less or equal:   $a <= $b  → false
```

## Basic Comparison Operators

### Equal (==)

Checks if two values are equal (loose comparison - type conversion allowed).

```php
<?php
$x = 5;
$y = "5";
echo $x == $y;      // Output: true (5 == "5" - same value)

$a = 10;
$b = 10;
echo $a == $b;      // Output: true

// Type juggling
echo 0 == false;    // true
echo 1 == true;     // true
echo "" == 0;       // true
echo null == false; // true
?>
```

### Identical (===)

Checks if two values are equal AND of the same type (strict comparison).

```php
<?php
$x = 5;
$y = "5";
echo $x === $y;     // Output: false (different types)

$a = 10;
$b = 10;
echo $a === $b;     // Output: true (same value and type)

// Type matters
echo 0 === false;    // false
echo 1 === true;     // false
echo "" === 0;       // false
echo null === false; // false
?>
```

### Not Equal (!= and <>)

Checks if two values are NOT equal.

```php
<?php
$x = 5;
$y = 3;
echo $x != $y;      // Output: true

// With type conversion
$a = 5;
$b = "5";
echo $a != $b;      // Output: false (values are equal after conversion)

// Alternative syntax
echo $x <> $y;      // Output: true (same as !=)

$name = "John";
$target = "Alice";
echo $name != $target;  // Output: true
?>
```

### Not Identical (!==)

Checks if two values are NOT equal OR not of the same type (strict comparison).

```php
<?php
$x = 5;
$y = "5";
echo $x !== $y;     // Output: true (different types)

$a = 5;
$b = 5;
echo $a !== $b;     // Output: false (same value and type)

// Type checking
echo 0 !== false;    // true
echo 1 !== true;     // true
echo null !== false; // true
?>
```

### Greater Than (>)

Checks if the first value is greater than the second.

```php
<?php
$x = 10;
$y = 5;
echo $x > $y;       // Output: true

$a = 3;
$b = 8;
echo $a > $b;       // Output: false

// Works with strings
echo "apple" > "application"; // false
echo "zebra" > "apple";       // true

// Practical example
$age = 25;
if ($age > 18) {
    echo "Adult";   // Output: Adult
}
?>
```

### Less Than (<)

Checks if the first value is less than the second.

```php
<?php
$x = 5;
$y = 10;
echo $x < $y;       // Output: true

$a = 20;
$b = 15;
echo $a < $b;       // Output: false

// Age checking
$user_age = 16;
if ($user_age < 18) {
    echo "Minor";
}
?>
```

### Greater Than or Equal To (>=)

Checks if the first value is greater than or equal to the second.

```php
<?php
$x = 10;
echo $x >= 10;      // Output: true
echo $x >= 5;       // Output: true
echo $x >= 15;      // Output: false

// Score checking
$score = 80;
if ($score >= 75) {
    echo "Pass";    // Output: Pass
}

// Minimum requirement
$balance = 1000;
$minimum = 500;
if ($balance >= $minimum) {
    echo "Sufficient funds";
}
?>
```

### Less Than or Equal To (<=)

Checks if the first value is less than or equal to the second.

```php
<?php
$x = 5;
echo $x <= 5;       // Output: true
echo $x <= 10;      // Output: true
echo $x <= 3;       // Output: false

// Age validation
$age = 65;
if ($age <= 18) {
    echo "Young";
} else {
    echo "Adult";   // Output: Adult
}

// Budget checking
$spending = 500;
$budget = 1000;
if ($spending <= $budget) {
    echo "Within budget";
}
?>
```

## Spaceship Operator (<=>)

Returns -1, 0, or 1 depending on comparison (PHP 7+).

```php
<?php
// Returns: -1 if left < right, 0 if equal, 1 if left > right
echo 1 <=> 2;       // Output: -1 (1 is less than 2)
echo 2 <=> 2;       // Output: 0 (equal)
echo 3 <=> 2;       // Output: 1 (3 is greater than 2)

// Works with strings
echo "a" <=> "b";   // Output: -1
echo "b" <=> "b";   // Output: 0
echo "c" <=> "b";   // Output: 1

// Useful for sorting
$results = [3, 1, 2];
usort($results, function($a, $b) {
    return $a <=> $b;  // Ascending sort
});
// Results: [1, 2, 3]

// Reverse sort
usort($results, function($a, $b) {
    return $b <=> $a;  // Descending sort
});
// Results: [3, 2, 1]
?>
```

## Comparison Operators Summary Table

| Operator | Name | Example | Result |
|----------|------|---------|--------|
| == | Equal | 5 == "5" | true |
| === | Identical | 5 === "5" | false |
| != | Not equal | 5 != 3 | true |
| !== | Not identical | 5 !== "5" | true |
| > | Greater than | 10 > 5 | true |
| < | Less than | 3 < 7 | true |
| >= | Greater or equal | 10 >= 10 | true |
| <= | Less or equal | 5 <= 10 | true |
| <=> | Spaceship | 1 <=> 2 | -1 |

## Practical Examples

### User Authentication

```php
<?php
function validateCredentials($inputPassword, $storedPassword) {
    // Use === to check exact match
    if ($inputPassword === $storedPassword) {
        return true;
    }
    return false;
}

// WRONG: == would accept "123" when password is 123
// CORRECT: === ensures type and value match
?>
```

### Age Verification

```php
<?php
$age = 21;
$minimum_age = 18;
$maximum_age = 65;

if ($age >= $minimum_age && $age <= $maximum_age) {
    echo "Eligible";
}

// Alternative: more readable range check
if ($age >= 18 && $age <= 65) {
    echo "Within working age range";
}
?>
```

### Grade Assignment

```php
<?php
function getGrade($score) {
    if ($score >= 90) {
        return "A";
    } elseif ($score >= 80) {
        return "B";
    } elseif ($score >= 70) {
        return "C";
    } elseif ($score >= 60) {
        return "D";
    } else {
        return "F";
    }
}

echo getGrade(85);  // Output: B
?>
```

### Discount Based on Amount

```php
<?php
function calculateDiscount($amount) {
    $discount = 0;
    
    if ($amount >= 1000) {
        $discount = 0.20;  // 20% off
    } elseif ($amount >= 500) {
        $discount = 0.15;  // 15% off
    } elseif ($amount >= 100) {
        $discount = 0.10;  // 10% off
    } elseif ($amount >= 50) {
        $discount = 0.05;  // 5% off
    }
    
    return $amount * (1 - $discount);
}

echo calculateDiscount(750);  // Output: 637.5 (15% off)
?>
```

### Data Validation

```php
<?php
function validateAge($age) {
    if ($age < 0) {
        return "Age cannot be negative";
    }
    if ($age > 150) {
        return "Age seems unrealistic";
    }
    if ($age == 0) {
        return "Age must be greater than 0";
    }
    return "Valid age";
}

echo validateAge(25);   // Output: Valid age
echo validateAge(-5);   // Output: Age cannot be negative
?>
```

### Comparing Arrays

```php
<?php
$array1 = [1, 2, 3];
$array2 = [1, 2, 3];
$array3 = ["1", "2", "3"];

// Loose comparison (==)
echo $array1 == $array2;    // true
echo $array1 == $array3;    // true

// Strict comparison (===)
echo $array1 === $array2;   // true
echo $array1 === $array3;   // false (different types)
?>
```

## Type Coercion in Comparisons

### == (Loose Comparison)

PHP converts types to match for comparison:

```php
<?php
// String to number
echo "10" == 10;         // true
echo "10.5" == 10.5;     // true

// Boolean conversions
echo true == 1;          // true
echo false == 0;         // true
echo false == "";        // true
echo false == null;      // true

// Array comparisons
echo [] == false;        // true (empty array is falsy)
echo [0] == false;       // false

// Be careful!
echo "abc" == 0;         // true (string can't convert, becomes 0)
?>
```

### === (Strict Comparison)

No type conversion:

```php
<?php
// String to number
echo "10" === 10;        // false (different types)
echo "10.5" === 10.5;    // false

// Boolean
echo true === 1;         // false
echo false === 0;        // false
echo false === "";       // false

// Array
echo [] === false;       // false
?>
```

## Chaining Comparisons

```php
<?php
$age = 25;

// Multiple comparisons
if ($age > 18 && $age < 65) {
    echo "Working age";
}

// Not recommended: chaining without explicit operators
// echo 10 < 20 < 30;  // May not work as expected

// Better: use explicit comparisons
if (10 < $value && $value < 30) {
    echo "Value in range";
}

// Or use variable
$x = 20;
if (10 < $x && $x < 30) {
    echo "Within range";  // Output: Within range
}
?>
```

## Common Pitfalls

### Confusing = and ==

```php
<?php
$x = 5;

// WRONG: Assignment in condition
if ($x = 10) {
    echo "Assigns 10 to x";
    echo $x;  // x is now 10!
}

// RIGHT: Comparison
if ($x == 10) {
    echo "Checks if x equals 10";
}
?>
```

### Floating Point Precision

```php
<?php
// Floating point comparison issues
$a = 0.1 + 0.2;
$b = 0.3;

echo $a == $b;      // May be false due to precision
echo $a === $b;     // Definitely false

// Solution: Use comparison within margin
$epsilon = 0.0001;
echo abs($a - $b) < $epsilon;  // true
?>
```

### Unexpected Type Conversion

```php
<?php
$value = "10 apples";

if ($value == 10) {
    echo "Matches!";  // Output: Matches! (string converts to 10)
}

// Better: Use strict comparison
if ($value === 10) {
    echo "Exact match";  // Doesn't execute
}

// Or validate type first
if (is_numeric($value) && $value == 10) {
    echo "Valid number";
}
?>
```

## Best Practices

### 1. Use === Instead of ==

```php
<?php
// Better: Always use strict comparison
if ($value === 5) {
    // more predictable
}

// Avoid: Loose comparison
if ($value == 5) {
    // type coercion can be surprising
}
?>
```

### 2. Clear Comparison Chains

```php
<?php
// Good: Clear what's being checked
$x = 25;
if ($x >= 18 && $x <= 65) {
    echo "Working age";
}

// Also clear
if ($x > 100) {
    echo "Very high";
} elseif ($x > 50) {
    echo "High";
} else {
    echo "Low";
}
?>
```

### 3. Parentheses for Clarity

```php
<?php
// Makes intent clear
$x = 5;
$y = 10;

if ($x > 0 && $y > 0) {
    echo "Both positive";
}

// More complex
if (($x > 5 && $x < 10) || ($y > 50)) {
    echo "Complex condition";
}
?>
```

## Key Takeaways

✓ Comparison operators return `true` or `false`
✓ **== and !=** perform loose comparison (type conversion allowed)
✓ **=== and !==** perform strict comparison (type must match)
✓ **Always prefer === over ==** to avoid unexpected type coercion
✓ **>, <, >=, <=** compare magnitude of values
✓ **Spaceship (<=>)** returns -1, 0, or 1 for sorting
✓ **Type matters**: "5" == 5 is true, but "5" === 5 is false
✓ **Chain comparisons** with && and || for complex logic
✓ **Use parentheses** for clarity in complex comparisons
✓ **Beware of floating-point precision** when comparing decimals
