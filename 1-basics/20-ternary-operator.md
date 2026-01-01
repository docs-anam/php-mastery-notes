# Ternary Operator

```php

// The ternary operator is a shorthand for if-else statements.
// Syntax: condition ? value_if_true : value_if_false;

$age = 18;

// Traditional if-else
if ($age >= 18) {
    $status = "Adult";
} else {
    $status = "Minor";
}

// Using ternary operator
$status = ($age >= 18) ? "Adult" : "Minor";

echo $status; // Output: Adult

// Nested ternary example
$score = 75;
$result = ($score >= 90) ? "A" : (($score >= 80) ? "B" : (($score >= 70) ? "C" : "F"));
echo $result; // Output: C

// Null coalescing shorthand (PHP 7+)
$name = $_GET['name'] ?? 'Guest'; // If 'name' is not set, 'Guest' is used

// Summary:
// - Ternary operator provides concise conditional assignment.
// - Use for simple conditions to improve code readability.
```

