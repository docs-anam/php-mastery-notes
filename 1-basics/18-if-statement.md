# If Statement

```php
// The if statement in PHP is used to execute code only if a specified condition is true.

// Basic syntax:
if ('condition') {
    // Code to execute if condition is true
}

// The condition can be any expression that evaluates to true or false (boolean).
// If the condition is true, the code block inside the curly braces {} runs.
// If the condition is false, the code block is skipped.

// Example:
$number = 10;

if ($number > 5) {
    echo "$number is greater than 5";
}

// You can also use else and elseif for additional conditions:

if ($number > 10) {
    echo "$number is greater than 10";
} elseif ($number == 10) {
    echo "$number is equal to 10";
} else {
    echo "$number is less than 10";
}

// The condition inside the if statement is evaluated as a boolean (true or false).
// Common comparison operators: == (equal), != (not equal), > (greater than), < (less than), >= (greater than or equal to), <= (less than or equal to)
// Logical operators: && (and), || (or), ! (not)

// Example using logical operators:
$age = 20;
$is_member = true;

if ($age >= 18 && $is_member) {
    echo "Access granted.";
} else {
    echo "Access denied.";
}

// Single-line if statement using a semicolon:
// If the statement to execute is only one line, you can omit the curly braces.
if ($number == 10) echo " (Checked with single-line if statement)";

// You can also write single-line if statements with curly braces for clarity:
if ($number == 10) { echo " (Checked with single-line if statement and braces)"; }

// Note: For readability and to avoid errors, it's recommended to use curly braces even for single-line statements.
```

