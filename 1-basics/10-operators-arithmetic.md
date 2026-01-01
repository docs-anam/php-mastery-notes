# Arithmetic Operators in PHP

## What are Arithmetic Operators?

Arithmetic operators are used to perform common mathematical operations on numbers. They allow you to add, subtract, multiply, divide, and perform other calculations in your PHP code.

```
$a = 10
$b = 3

Addition:       $a + $b = 13
Subtraction:    $a - $b = 7
Multiplication: $a * $b = 30
Division:       $a / $b = 3.33...
Modulo:         $a % $b = 1
Exponentiation: $a ** $b = 1000
```

## Basic Arithmetic Operators

### 1. Addition (+)

Adds two numbers together.

```php
<?php
$x = 10;
$y = 5;
echo $x + $y;  // Output: 15

// Also works with floats
$a = 3.5;
$b = 2.5;
echo $a + $b;  // Output: 6

// String concatenation with + (automatic type conversion)
$num1 = "5";
$num2 = "3";
echo $num1 + $num2;  // Output: 8 (strings converted to numbers)
?>
```

### 2. Subtraction (-)

Subtracts the second number from the first.

```php
<?php
$x = 10;
$y = 5;
echo $x - $y;  // Output: 5

// Works with negative results
$a = 3;
$b = 7;
echo $a - $b;  // Output: -4

// Useful for calculating differences
$height1 = 180;
$height2 = 165;
echo "Height difference: " . ($height1 - $height2) . " cm";  // 15 cm
?>
```

### 3. Multiplication (*)

Multiplies two numbers together.

```php
<?php
$x = 10;
$y = 5;
echo $x * $y;  // Output: 50

// Multiplication order doesn't matter
echo 3 * 7;     // Output: 21
echo 7 * 3;     // Output: 21

// Useful for calculations
$price_per_item = 25.50;
$quantity = 4;
echo "Total: $" . ($price_per_item * $quantity);  // Total: $102
?>
```

### 4. Division (/)

Divides the first number by the second.

```php
<?php
$x = 15;
$y = 3;
echo $x / $y;  // Output: 5

// Returns float for non-exact division
$a = 10;
$b = 3;
echo $a / $b;  // Output: 3.3333333333333

// Practical example
$total_price = 100;
$num_people = 4;
echo "Per person: $" . ($total_price / $num_people);  // Per person: $25
?>
```

### 5. Modulo (%)

Returns the remainder of a division operation.

```php
<?php
$x = 10;
$y = 3;
echo $x % $y;  // Output: 1 (10 divided by 3 = 3 with remainder 1)

// Check if number is even or odd
$number = 15;
if ($number % 2 == 0) {
    echo "Even";
} else {
    echo "Odd";  // Output: Odd
}

// Useful for cycles
$current_day = 10;
$days_in_week = 7;
$day_of_week = $current_day % $days_in_week;  // Output: 3
?>
```

### 6. Exponentiation (**)

Raises a number to the power of another number (PHP 5.6+).

```php
<?php
$x = 2;
$y = 3;
echo $x ** $y;  // Output: 8 (2 raised to power 3)

// Useful for calculations
echo 10 ** 2;   // Output: 100 (10 squared)
echo 10 ** 3;   // Output: 1000 (10 cubed)

// Square root (power of 0.5)
$number = 16;
echo $number ** 0.5;  // Output: 4

// Practical example: calculating compound interest
$principal = 1000;
$rate = 1.05;  // 5% annual rate
$years = 3;
$amount = $principal * ($rate ** $years);
echo "Amount after 3 years: $" . round($amount, 2);  // $1157.63
?>
```

## Operator Precedence

Mathematical operators follow standard order of operations (PEMDAS/BODMAS):

```php
<?php
// 1. Exponentiation (**)
// 2. Multiplication (*), Division (/), Modulo (%)
// 3. Addition (+), Subtraction (-)

// Without parentheses (left to right for same precedence)
echo 10 + 5 * 2;      // Output: 20 (5*2=10, then 10+10)
echo 10 - 5 + 2;      // Output: 7 (left to right: 10-5=5, then 5+2)

// With parentheses (changes order)
echo (10 + 5) * 2;    // Output: 30
echo 10 * 2 ** 3;     // Output: 80 (2**3=8, then 10*8)
echo (10 * 2) ** 3;   // Output: 8000

// Exponentiation is right-associative
echo 2 ** 3 ** 2;     // Output: 512 (3**2=9, then 2**9)
echo (2 ** 3) ** 2;   // Output: 64
?>
```

## Type Juggling in Arithmetic

PHP automatically converts types when performing arithmetic:

```php
<?php
// String to number conversion
echo "5" + 3;              // Output: 8 (string "5" converts to 5)
echo "5.5" + 2;            // Output: 7.5
echo "10 apples" + 5;      // Output: 15 (extracts "10")
echo "apples 10" + 5;      // Output: 5 (cannot extract, becomes 0)

// Boolean to number conversion
echo true + 2;             // Output: 3 (true = 1)
echo false + 5;            // Output: 5 (false = 0)

// NULL to number conversion
echo null + 10;            // Output: 10 (null = 0)

// Array operations not supported
$arr1 = [1, 2, 3];
$arr2 = [4, 5, 6];
// echo $arr1 + $arr2;     // ERROR: Cannot use + with arrays

// To combine arrays, use union operator (+)
$result = [1, 2] + [3, 4];  // Result: [1, 2] (array union)
?>
```

## Practical Arithmetic Examples

### Calculator Function

```php
<?php
function calculate($a, $b, $operator) {
    switch ($operator) {
        case '+':
            return $a + $b;
        case '-':
            return $a - $b;
        case '*':
            return $a * $b;
        case '/':
            if ($b == 0) {
                return "Error: Division by zero";
            }
            return $a / $b;
        case '%':
            return $a % $b;
        case '**':
            return $a ** $b;
        default:
            return "Unknown operator";
    }
}

echo calculate(10, 3, '+');   // Output: 13
echo calculate(10, 3, '/');   // Output: 3.3333
echo calculate(10, 3, '%');   // Output: 1
?>
```

### Grade Calculator

```php
<?php
function calculateGrade($marks1, $marks2, $marks3) {
    $total = $marks1 + $marks2 + $marks3;
    $average = $total / 3;
    
    if ($average >= 90) return 'A';
    if ($average >= 80) return 'B';
    if ($average >= 70) return 'C';
    if ($average >= 60) return 'D';
    return 'F';
}

$grade = calculateGrade(85, 90, 78);
echo "Grade: " . $grade;  // Output: Grade: B
?>
```

### Distance and Speed Calculator

```php
<?php
function calculateSpeed($distance, $time) {
    return $distance / $time;  // speed = distance / time
}

function calculateDistance($speed, $time) {
    return $speed * $time;     // distance = speed * time
}

function calculateTime($distance, $speed) {
    if ($speed == 0) return "Speed cannot be zero";
    return $distance / $speed;  // time = distance / speed
}

// Usage
$speed = calculateSpeed(100, 2);      // 100 km in 2 hours
echo "Speed: " . $speed . " km/h";    // Speed: 50 km/h

$distance = calculateDistance(60, 3);  // 60 km/h for 3 hours
echo "Distance: " . $distance . " km"; // Distance: 180 km
?>
```

### Financial Calculations

```php
<?php
// Simple Interest Calculator
function simpleInterest($principal, $rate, $time) {
    return ($principal * $rate * $time) / 100;
}

$interest = simpleInterest(1000, 5, 2);  // $1000 at 5% for 2 years
echo "Interest: $" . $interest;  // Output: Interest: $100

// Discount Calculator
function calculateDiscount($price, $discount_percent) {
    return $price - ($price * $discount_percent / 100);
}

$final_price = calculateDiscount(100, 20);  // 20% off $100
echo "Final Price: $" . $final_price;  // Output: Final Price: $80

// Tax Calculator
function addTax($amount, $tax_percent) {
    return $amount + ($amount * $tax_percent / 100);
}

$total = addTax(100, 10);  // $100 with 10% tax
echo "Total: $" . $total;  // Output: Total: $110
?>
```

## Common Pitfalls

### Division by Zero

```php
<?php
// This will generate a warning
$result = 10 / 0;  // Warning: Division by zero

// Safe approach
function safeDivide($a, $b) {
    if ($b == 0) {
        return "Error: Cannot divide by zero";
    }
    return $a / $b;
}
?>
```

### Floating Point Precision

```php
<?php
// Floating point arithmetic can have precision issues
echo 0.1 + 0.2;           // Output: 0.30000000000000004
echo 0.1 + 0.2 == 0.3;    // Output: false (not equal!)

// Solution: Use round() for comparisons
echo round(0.1 + 0.2, 1) == 0.3;  // Output: true

// Or use bcmath for precise calculations
echo bcadd('0.1', '0.2', 1);  // Output: 0.3
?>
```

### Type Conversion Surprises

```php
<?php
// Be careful with automatic type conversion
echo "10" + "20";        // Output: 30 (strings to numbers)
echo "10" + 20;          // Output: 30
echo "10 dollars" + 5;   // Output: 15 (extracts number)
echo "dollars 10" + 5;   // Output: 5 (cannot extract, becomes 0)

// Always be explicit when needed
$a = (int)"10";
$b = (int)"5";
echo $a + $b;  // Output: 15
?>
```

## Arithmetic Operators Summary Table

| Operator | Name | Example | Result |
|----------|------|---------|--------|
| + | Addition | 10 + 5 | 15 |
| - | Subtraction | 10 - 5 | 5 |
| * | Multiplication | 10 * 5 | 50 |
| / | Division | 10 / 5 | 2 |
| % | Modulo | 10 % 3 | 1 |
| ** | Exponentiation | 2 ** 3 | 8 |

## Key Takeaways

✓ Arithmetic operators perform mathematical calculations on numbers
✓ **Addition (+)**, subtraction (-), multiplication (*), division (/), modulo (%), exponentiation (**)
✓ **Precedence matters**: Exponentiation first, then *, /, %, then +, -
✓ Use **parentheses** to change order of operations explicitly
✓ PHP **automatically converts types** (strings, booleans to numbers)
✓ **Always check for division by zero** to avoid errors
✓ **Beware of floating-point precision** issues in comparisons
✓ **Modulo (%)** is useful for cycles, even/odd checks, remainders
✓ **Exponentiation (**)** calculates powers and roots efficiently
