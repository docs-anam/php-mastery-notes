# Ternary Operator in PHP

## Overview

The ternary operator (also called conditional operator) is a shorthand way to make a decision between two values based on a condition. It allows you to write concise conditional expressions that return different values.

## Basic Ternary Operator

### Simple Ternary

```php
<?php
// Basic ternary syntax: condition ? value_if_true : value_if_false
$age = 25;
$status = ($age >= 18) ? "Adult" : "Minor";
echo $status;  // Output: Adult

// Used in echo
$score = 45;
echo ($score >= 60) ? "Passed" : "Failed";  // Output: Failed

// Assigning to variable
$weather = "sunny";
$activity = ($weather == "sunny") ? "Go outside" : "Stay indoors";
echo $activity;  // Output: Go outside
?>
```

### Ternary with Numbers

```php
<?php
$temperature = 25;
$feeling = ($temperature > 20) ? "Warm" : "Cold";
echo $feeling;  // Output: Warm

// Mathematical operations
$height = 180;
$category = ($height >= 180) ? "Tall" : "Not tall";
echo $category;  // Output: Tall

// With calculations
$score1 = 85;
$score2 = 90;
$highest = ($score1 > $score2) ? $score1 : $score2;
echo $highest;  // Output: 90
?>
```

## Nested Ternary

### Chained Ternary

```php
<?php
// Grade assignment
$score = 75;
$grade = ($score >= 90) ? "A" : 
         ($score >= 80) ? "B" : 
         ($score >= 70) ? "C" : 
         ($score >= 60) ? "D" : "F";
echo $grade;  // Output: C

// Age category
$age = 45;
$category = ($age < 13) ? "Child" :
            ($age < 18) ? "Teen" :
            ($age < 65) ? "Adult" : "Senior";
echo $category;  // Output: Adult

// Price calculation
$quantity = 50;
$price = ($quantity >= 100) ? 10 :
         ($quantity >= 50) ? 12 :
         ($quantity >= 10) ? 15 : 20;
echo "Price: $" . $price;  // Output: Price: $12
?>
```

## Shorthand Ternary

### Elvis Operator

```php
<?php
// Elvis operator (?:) - returns left side if truthy, else right side
// Syntax: value1 ?: value2

$name = "";
$display = $name ?: "Anonymous";
echo $display;  // Output: Anonymous

// With variables
$user_input = null;
$default_value = "no input";
$result = $user_input ?: $default_value;
echo $result;  // Output: no input

// Practical use
$username = "";
$welcome = "Hello " . ($username ?: "Guest");
echo $welcome;  // Output: Hello Guest

// Better than repeated variable
$email = $_GET["email"] ?? "";  // Null coalescing (safer)
// or
$email = $_GET["email"] ?: "";   // Elvis operator
?>
```

## Practical Examples

### User Greeting

```php
<?php
function greetUser($isLoggedIn, $username) {
    return $isLoggedIn ? "Hello, $username!" : "Hello, Guest!";
}

echo greetUser(true, "John");   // Output: Hello, John!
echo greetUser(false, "");      // Output: Hello, Guest!
?>
```

### Membership Discount

```php
<?php
$isMember = true;
$originalPrice = 100;

$finalPrice = $isMember ? $originalPrice * 0.9 : $originalPrice;
echo "Price: $" . $finalPrice;  // Output: Price: $90

// More complex
$purchaseAmount = 500;
$isMember = true;
$discount = ($isMember && $purchaseAmount > 300) ? 0.20 : 
            ($isMember) ? 0.10 :
            ($purchaseAmount > 300) ? 0.05 : 0;
            
echo "Discount: " . ($discount * 100) . "%";  // Output: Discount: 20%
?>
```

### Conditional CSS Classes

```php
<?php
$isActive = true;
$class = $isActive ? "btn-primary" : "btn-secondary";
echo '<button class="' . $class . '">Click me</button>';
// Output: <button class="btn-primary">Click me</button>

// Multiple conditions
$isValid = true;
$hasError = false;
$inputClass = $isValid && !$hasError ? "success" : "error";
echo '<input class="' . $inputClass . '">';
// Output: <input class="success">
?>
```

### Status Display

```php
<?php
$orderStatus = "processing";

$icon = ($orderStatus == "completed") ? "✓" :
        ($orderStatus == "processing") ? "⟳" :
        ($orderStatus == "cancelled") ? "✗" : "?";

$color = ($orderStatus == "completed") ? "green" :
         ($orderStatus == "processing") ? "blue" :
         ($orderStatus == "cancelled") ? "red" : "gray";

echo '<span style="color:' . $color . '">' . $icon . ' ' . $orderStatus . '</span>';
// Output: <span style="color:blue">⟳ processing</span>
?>
```

### Permission Check

```php
<?php
$userRole = "editor";
$isOwner = true;

$canEdit = ($userRole == "admin" || $isOwner) ? true : false;
$canDelete = ($userRole == "admin") ? true : false;
$canView = !empty($userRole) ? true : false;

echo "Can Edit: " . ($canEdit ? "Yes" : "No");    // Can Edit: Yes
echo "Can Delete: " . ($canDelete ? "Yes" : "No"); // Can Delete: No
echo "Can View: " . ($canView ? "Yes" : "No");    // Can View: Yes
?>
```

### Age Category

```php
<?php
function getAgeCategory($age) {
    return ($age < 13) ? "Child" :
           ($age < 18) ? "Teen" :
           ($age < 65) ? "Adult" : "Senior";
}

echo getAgeCategory(10);   // Output: Child
echo getAgeCategory(15);   // Output: Teen
echo getAgeCategory(30);   // Output: Adult
echo getAgeCategory(70);   // Output: Senior
?>
```

### Available Quantity Badge

```php
<?php
$quantity = 2;

$badge = ($quantity > 10) ? '<span class="badge badge-success">In Stock</span>' :
         ($quantity > 0) ? '<span class="badge badge-warning">Low Stock</span>' :
         '<span class="badge badge-danger">Out of Stock</span>';

echo $badge;
// Output: <span class="badge badge-warning">Low Stock</span>
?>
```

## Ternary vs If/Else

### When to Use Ternary

```php
<?php
// GOOD - Simple condition with simple values
$age = 20;
$status = ($age >= 18) ? "Adult" : "Minor";

// GOOD - Concise assignments
$discount = $isMember ? 0.10 : 0;

// GOOD - One-liners in templates/expressions
echo '<div class="' . ($isActive ? "active" : "inactive") . '">';
?>
```

### When to Use If/Else

```php
<?php
// BETTER - Complex conditions
if ($age >= 18 && $creditScore > 700 && $income > 30000) {
    $loanApproved = true;
} else {
    $loanApproved = false;
}

// BETTER - Multiple statements in each branch
if ($userRole == "admin") {
    $canEdit = true;
    $canDelete = true;
    $accessLevel = "full";
} else {
    $canEdit = false;
    $canDelete = false;
    $accessLevel = "limited";
}

// BETTER - Complex logic
if ($isWeekend && !isHoliday()) {
    // Complex calculation
    // Multiple operations
}
?>
```

## Common Patterns

### Default Value

```php
<?php
// Get value or use default
$name = isset($userData['name']) ? $userData['name'] : "Guest";

// Or use null coalescing operator (PHP 7+) - PREFERRED
$name = $userData['name'] ?? "Guest";

// Elvis operator alternative
$name = $userData['name'] ?: "Guest";
?>
```

### Type Conversion

```php
<?php
$input = "yes";

$boolean = ($input == "yes") ? true : false;

// Or more concisely
$boolean = $input == "yes";

// With strict comparison
$boolean = ($input === "yes") ? true : false;
?>
```

### Conditional String Building

```php
<?php
$user = ["name" => "John", "role" => "admin"];

$greeting = "Welcome, " . $user['name'] . 
            ($user['role'] == "admin" ? " (Administrator)" : "");
            
echo $greeting;  // Output: Welcome, John (Administrator)
?>
```

## Common Pitfalls

### Overly Nested Ternary

```php
<?php
// HARD TO READ
$grade = ($score >= 90) ? "A" : 
         ($score >= 80) ? "B" : 
         ($score >= 70) ? "C" : 
         ($score >= 60) ? "D" : 
         ($score >= 50) ? "E" : "F";

// BETTER - Use switch or if/elseif
switch(true) {
    case $score >= 90:
        $grade = "A";
        break;
    case $score >= 80:
        $grade = "B";
        break;
    // ... etc
}
?>
```

### Assignment Instead of Comparison

```php
<?php
$x = 5;

// WRONG - assigns 10
if (($x = 10) ? true : false) {
    // $x is now 10
}

// CORRECT
$result = ($x == 10) ? true : false;
?>
```

### Type Coercion Surprises

```php
<?php
$value = "0";  // String "0"

// This is FALSE (string "0" is falsy)
$result = $value ? "truthy" : "falsy";
echo $result;  // Output: falsy

// Better: explicit comparison
$result = ($value === "0") ? "is string zero" : "not string zero";
echo $result;  // Output: is string zero

// Or use null coalescing
$result = isset($value) ? $value : "not set";
?>
```

### Unreadable Chaining

```php
<?php
// CONFUSING
$status = $a ? $b ? $c : $d : $e ? $f : $g;

// BETTER - Add comments and better formatting
$status = ($a) ? 
    ($b) ? $c : $d :   // if a and b
    ($e) ? $f : $g;    // if a false, check e
    
// BEST - Use if/else or switch
if ($a) {
    $status = $b ? $c : $d;
} else {
    $status = $e ? $f : $g;
}
?>
```

## Best Practices

✓ **Use for simple conditions** only
✓ **Avoid deep nesting** (max 2-3 levels)
✓ **Use parentheses** for clarity
✓ **Prefer null coalescing (??)** over Elvis (?:)
✓ **Consider if/else** for multiple statements
✓ **Comment complex ternary** operations
✓ **Keep ternary on one line** when possible
✓ **Use switch** for many options

## Key Takeaways

✓ **Ternary operator** is `condition ? true_value : false_value`
✓ **Shorthand ternary** (Elvis) is `value1 ?: value2`
✓ **Null coalescing** (??) is preferred over Elvis
✓ **Returns a value** (assignment-friendly)
✓ **Good for simple conditions** only
✓ **Avoid nesting** too deeply
✓ **Use parentheses** for clarity
✓ **If/else better** for complex logic
✓ **Condition is type coerced** to boolean
✓ **Not suitable for multiple statements**
