# Functions in PHP

## Overview

Functions are reusable blocks of code designed to perform specific tasks. They are fundamental to writing clean, maintainable, and DRY (Don't Repeat Yourself) code. Functions allow you to encapsulate logic, improve code organization, and make your applications more scalable. PHP supports multiple types of functions: user-defined functions, built-in functions, anonymous functions, arrow functions, and recursive functions.

## Basic Function Structure

### Simple Function Without Parameters

```php
<?php
// Basic function definition
function sayHello() {
    echo "Hello, World!";
}

// Calling the function
sayHello();
// Output: Hello, World!

// Function with echo output
function displayGreeting() {
    echo "Welcome to PHP Functions!\n";
}

displayGreeting();
// Output: Welcome to PHP Functions!
?>
```

### Function With Parameters

```php
<?php
// Function with single parameter
function greet($name) {
    echo "Hello, " . $name . "!";
}

greet("Alice");  // Output: Hello, Alice!
greet("Bob");    // Output: Hello, Bob!

// Function with multiple parameters
function add($a, $b) {
    echo "Sum: " . ($a + $b);
}

add(5, 3);   // Output: Sum: 8
add(10, 20); // Output: Sum: 30
?>
```

### Function With Return Value

```php
<?php
// Function returning a value
function multiply($x, $y) {
    return $x * $y;
}

$result = multiply(4, 5);
echo "4 * 5 = " . $result;
// Output: 4 * 5 = 20

// Function with early return
function checkAge($age) {
    if ($age < 18) {
        return "Too young";
    }
    return "You are an adult";
}

echo checkAge(16);  // Output: Too young
echo checkAge(25);  // Output: You are an adult
?>
```

### Default Parameters

```php
<?php
// Function with default parameter values
function createUser($name = "Guest", $role = "user") {
    echo "User: $name, Role: $role\n";
}

createUser();                    // Output: User: Guest, Role: user
createUser("Alice");             // Output: User: Alice, Role: user
createUser("Bob", "admin");      // Output: User: Bob, Role: admin

// Default with mixed parameters
function buildURL($domain, $protocol = "https") {
    return $protocol . "://" . $domain;
}

echo buildURL("example.com");           // Output: https://example.com
echo buildURL("example.com", "http");   // Output: http://example.com
?>
```

## Type Declarations (PHP 7+)

### Type Hints for Parameters and Return Types

```php
<?php
// Type hint for parameters
function calculateDiscount(float $price, float $discountPercent): float {
    return $price - ($price * $discountPercent / 100);
}

echo calculateDiscount(100, 10);  // Output: 90

// Type hints with strings and arrays
function processData(string $data, array $options): string {
    return "Data: " . $data . " Options: " . count($options);
}

echo processData("test", ["a", "b"]);
// Output: Data: test Options: 2

// Strict type checking
function subtract(int $a, int $b): int {
    return $a - $b;
}

echo subtract(10, 3);  // Output: 7
// subtract(10, 3.5);  // Would convert 3.5 to 3 in non-strict mode
?>
```

## Practical Examples

### String Processing Functions

```php
<?php
// Truncate string to specific length
function truncateString($str, $length = 50, $suffix = "...") {
    if (strlen($str) > $length) {
        return substr($str, 0, $length) . $suffix;
    }
    return $str;
}

$text = "This is a very long text that needs to be truncated for display purposes";
echo truncateString($text, 30);
// Output: This is a very long text that...

// Capitalize first letter of each word
function capitalizeWords($str) {
    return ucwords(strtolower($str));
}

echo capitalizeWords("hello WORLD from PHP");
// Output: Hello World From Php

// Count word frequency
function countWords($str) {
    $words = str_word_count($str, 1);
    return array_count_values($words);
}

$text = "apple banana apple cherry banana apple";
$counts = countWords($text);
print_r($counts);
// Output: Array ( [apple] => 3 [banana] => 2 [cherry] => 1 )
?>
```

### Array Processing Functions

```php
<?php
// Filter array based on condition
function filterEven($array) {
    return array_filter($array, function($num) {
        return $num % 2 == 0;
    });
}

$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
print_r(filterEven($numbers));
// Output: Array ( [1] => 2 [3] => 4 [5] => 6 [7] => 8 [9] => 10 )

// Calculate array statistics
function arrayStats($array) {
    return [
        "sum" => array_sum($array),
        "count" => count($array),
        "average" => array_sum($array) / count($array),
        "max" => max($array),
        "min" => min($array)
    ];
}

$data = [10, 20, 15, 30, 25];
$stats = arrayStats($data);
print_r($stats);
// Output: Array ( [sum] => 100 [count] => 5 [average] => 20 [max] => 30 [min] => 10 )

// Remove duplicates and sort
function uniqueAndSort($array) {
    $unique = array_unique($array);
    sort($unique);
    return $unique;
}

$items = [3, 1, 4, 1, 5, 9, 2, 6, 5];
print_r(uniqueAndSort($items));
// Output: Array ( [0] => 1 [1] => 2 [2] => 3 [3] => 4 [4] => 5 [5] => 6 [6] => 9 )
?>
```

## Advanced Function Types

### Variadic Functions (Variable Arguments)

```php
<?php
// Accept variable number of arguments
function sumAll(...$numbers) {
    $total = 0;
    foreach ($numbers as $num) {
        $total += $num;
    }
    return $total;
}

echo sumAll(1, 2, 3);        // Output: 6
echo sumAll(5, 10, 15, 20);  // Output: 50

// Mix regular and variadic parameters
function buildList($title, ...$items) {
    echo "$title:\n";
    foreach ($items as $item) {
        echo "- $item\n";
    }
}

buildList("Fruits", "Apple", "Banana", "Cherry");
// Output:
// Fruits:
// - Apple
// - Banana
// - Cherry
?>
```

### Anonymous Functions (Closures)

```php
<?php
// Anonymous function stored in variable
$square = function($n) {
    return $n * $n;
};

echo $square(5);  // Output: 25

// Closure with use keyword (capturing variables)
$multiplier = 2;
$multiply = function($x) use ($multiplier) {
    return $x * $multiplier;
};

echo $multiply(5);  // Output: 10

// Using closure as callback
$numbers = [1, 2, 3, 4, 5];
$squared = array_map(function($n) {
    return $n * $n;
}, $numbers);

print_r($squared);
// Output: Array ( [0] => 1 [1] => 4 [2] => 9 [3] => 16 [4] => 25 )
?>
```

### Arrow Functions (PHP 7.4+)

```php
<?php
// Short closure syntax with automatic variable capture
$add = fn($a, $b) => $a + $b;
echo $add(3, 4);  // Output: 7

// Arrow function in array operations
$numbers = [1, 2, 3, 4, 5];
$doubled = array_map(fn($x) => $x * 2, $numbers);
print_r($doubled);
// Output: Array ( [0] => 2 [1] => 4 [2] => 6 [3] => 8 [4] => 10 )

// Comparing arrow vs traditional closure
$multiplier = 3;
$fn1 = fn($x) => $x * $multiplier;  // Automatically captures $multiplier
$fn2 = function($x) use ($multiplier) {  // Requires use keyword
    return $x * $multiplier;
};

echo $fn1(5);  // Output: 15
echo $fn2(5);  // Output: 15
?>
```

### Pass by Reference

```php
<?php
// Modify variable by reference
function increment(&$value) {
    $value++;
}

$num = 5;
increment($num);
echo $num;  // Output: 6

// Swap values using references
function swap(&$a, &$b) {
    $temp = $a;
    $a = $b;
    $b = $temp;
}

$x = 10;
$y = 20;
swap($x, $y);
echo "x=$x, y=$y";  // Output: x=20, y=10

// Modify array elements
function updatePrices(&$prices, $percentage) {
    foreach ($prices as &$price) {
        $price *= (1 + $percentage / 100);
    }
    unset($price);  // Break reference to avoid side effects
}

$items = [10, 20, 30];
updatePrices($items, 10);
print_r($items);
// Output: Array ( [0] => 11 [1] => 22 [2] => 33 )
?>
```

### Recursive Functions

```php
<?php
// Calculate factorial recursively
function factorial($n) {
    if ($n <= 1) {
        return 1;
    }
    return $n * factorial($n - 1);
}

echo factorial(5);  // Output: 120

// Sum array elements recursively
function sumArray($arr, $index = 0) {
    if ($index >= count($arr)) {
        return 0;
    }
    return $arr[$index] + sumArray($arr, $index + 1);
}

echo sumArray([1, 2, 3, 4, 5]);  // Output: 15

// Flatten multidimensional array
function flattenArray($arr) {
    $result = [];
    foreach ($arr as $item) {
        if (is_array($item)) {
            $result = array_merge($result, flattenArray($item));
        } else {
            $result[] = $item;
        }
    }
    return $result;
}

$nested = [1, [2, 3, [4, 5]], 6];
print_r(flattenArray($nested));
// Output: Array ( [0] => 1 [1] => 2 [2] => 3 [3] => 4 [4] => 5 [5] => 6 )
?>
```

## Common Pitfalls and Solutions

### Pitfall 1: Variable Scope Issues

```php
<?php
// ❌ WRONG: Variable not accessible inside function
$message = "Hello";
function printMessage() {
    echo $message;  // Notice: Undefined variable
}

// ✅ CORRECT: Pass as parameter
function printMessage($msg) {
    echo $msg;  // Output: Hello
}
printMessage($message);

// ✅ CORRECT: Use global keyword (less recommended)
$message = "Hello";
function printMessage() {
    global $message;
    echo $message;  // Output: Hello
}
printMessage();
?>
```

### Pitfall 2: Modifying Parameters Unintentionally

```php
<?php
// ❌ WRONG: Creating new variable instead of modifying parameter
function updateArray($arr) {
    $arr[] = "new";  // Only modifies local copy
}

$data = [1, 2, 3];
updateArray($data);
print_r($data);  // Output: Array ( [0] => 1 [1] => 2 [2] => 3 )

// ✅ CORRECT: Use reference or return new value
function updateArray(&$arr) {
    $arr[] = "new";
}

$data = [1, 2, 3];
updateArray($data);
print_r($data);  // Output: Array ( [0] => 1 [1] => 2 [3] => new )
?>
```

### Pitfall 3: Missing Return Statement

```php
<?php
// ❌ WRONG: Function returns null implicitly
function getUsername($user_id) {
    if ($user_id > 0) {
        return "User" . $user_id;
    }
    // Missing return for false case
}

$result = getUsername(-1);
echo $result ?? "No result";  // Output: No result

// ✅ CORRECT: Always return a value
function getUsername($user_id) {
    if ($user_id > 0) {
        return "User" . $user_id;
    }
    return "Invalid ID";
}

echo getUsername(-1);  // Output: Invalid ID
?>
```

## Best Practices

✅ **Use Type Hints**: Type declarations prevent bugs and improve code clarity
```php
function processPayment(float $amount, string $currency): bool
```

✅ **Single Responsibility**: Each function should do one thing well
```php
// Good: Two focused functions
function validateEmail($email): bool { ... }
function sendEmail($email, $subject): bool { ... }

// Avoid: One function doing too much
function validateAndSendEmail($email, $subject): bool { ... }
```

✅ **Descriptive Names**: Function names should clearly indicate purpose
```php
// Good: Clear action
function calculateTotalPrice($items, $tax) { ... }

// Avoid: Vague names
function calc($i, $t) { ... }
```

✅ **Limit Parameters**: Functions with many parameters are harder to use
```php
// Good: Fewer parameters
function createUser(string $name, string $email) { ... }

// Avoid: Too many parameters
function createUser($name, $email, $phone, $address, $city, $state, ...) { ... }
```

✅ **Use Default Values**: Provide sensible defaults to simplify function calls
```php
function paginate($page = 1, $limit = 10, $sort = 'asc') { ... }
```

✅ **Input Validation**: Always validate function inputs
```php
function divide(float $a, float $b): float {
    if ($b == 0) {
        throw new Exception("Division by zero");
    }
    return $a / $b;
}
```

✅ **Consistent Return Types**: Always return the same type
```php
function findUser($id) {
    if ($user = getFromDatabase($id)) {
        return $user;  // Always return User object or null
    }
    return null;
}
```

## Key Takeaways

✓ Functions are reusable code blocks that improve code organization and reduce duplication

✓ Type declarations (PHP 7+) help prevent bugs and make code more maintainable

✓ Parameters can have default values to simplify function calls

✓ Pass by reference (`&$param`) allows functions to modify caller's variables

✓ Variadic functions accept variable numbers of arguments using `...$args`

✓ Arrow functions (`fn() => ...`) provide concise syntax for simple callbacks

✓ Recursive functions call themselves but require proper base cases to avoid infinite loops

✓ Always validate inputs and provide clear return values

✓ Use meaningful function and parameter names for code clarity

✓ Follow the Single Responsibility Principle to keep functions focused and reusable
