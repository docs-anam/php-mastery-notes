# If Statement in PHP

## Overview

The if statement is the fundamental conditional control structure that allows your code to make decisions based on conditions. It lets you execute different code blocks depending on whether a condition is true or false.

## Basic If Statement

### Simple If

```php
<?php
// Basic if statement
$age = 25;

if ($age >= 18) {
    echo "You are an adult";
}

// If without braces (not recommended)
if ($age >= 18)
    echo "You are an adult";

// Better: always use braces for readability
if ($age >= 18) {
    echo "You are an adult";
    // Multiple statements
    $status = "adult";
}
?>
```

### If-Else

```php
<?php
$age = 15;

if ($age >= 18) {
    echo "You are an adult";
} else {
    echo "You are a minor";
}

// Block execution
$score = 45;

if ($score >= 60) {
    echo "Passed";
    $result = true;
} else {
    echo "Failed";
    $result = false;
}
?>
```

### If-Elseif-Else

```php
<?php
$grade = 75;

if ($grade >= 90) {
    echo "A - Excellent";
} elseif ($grade >= 80) {
    echo "B - Good";
} elseif ($grade >= 70) {
    echo "C - Average";
} elseif ($grade >= 60) {
    echo "D - Below Average";
} else {
    echo "F - Fail";
}

// Multiple conditions
$age = 20;
$license = true;

if ($age >= 18 && $license) {
    echo "Can drive";
} elseif ($age < 18) {
    echo "Too young";
} else {
    echo "Need license";
}
?>
```

## Condition Types

### Comparison Conditions

```php
<?php
// Equal and strict equal
$x = 5;
if ($x == "5") {
    echo "Loose equality";
}
if ($x === 5) {
    echo "Strict equality";
}

// Greater/Less than
$age = 25;
if ($age > 18) {
    echo "Adult";
}
if ($age >= 18) {
    echo "At least 18";
}

// Not equal
$status = "active";
if ($status != "inactive") {
    echo "Is active";
}
if ($status !== "inactive") {
    echo "Strictly not inactive";
}
?>
```

### Logical Conditions

```php
<?php
$age = 25;
$license = true;

// AND operator (&&)
if ($age >= 18 && $license) {
    echo "Can drive";
}

// OR operator (||)
if ($age < 16 || $license == false) {
    echo "Cannot drive";
}

// NOT operator (!)
if (!$license) {
    echo "No license";
}

// Combined logic
$income = 50000;
$employed = true;

if ($income > 30000 && $employed) {
    echo "Eligible for loan";
} elseif ($income > 20000 || $employed) {
    echo "May qualify";
} else {
    echo "Not eligible";
}
?>
```

### Boolean Conditions

```php
<?php
$isActive = true;
$isEmpty = false;

// Direct boolean
if ($isActive) {
    echo "Active";
}

// Negated boolean
if (!$isEmpty) {
    echo "Has content";
}

// Multiple booleans
$isAdmin = true;
$canDelete = true;

if ($isAdmin && $canDelete) {
    echo "Can delete";
}
?>
```

### String and Array Conditions

```php
<?php
// Empty string check
$name = "";

if ($name) {
    echo "Name exists";
} else {
    echo "Name is empty";
}

// String value check
$status = "active";

if ($status == "active") {
    echo "Status is active";
}

// Array check
$items = [];

if ($items) {
    echo "Has items";
} else {
    echo "Empty";
}

// Array key existence
$user = ["name" => "John", "age" => 30];

if (isset($user["name"])) {
    echo "Name exists: " . $user["name"];
}

if (array_key_exists("email", $user)) {
    echo "Has email";
} else {
    echo "No email";
}
?>
```

## Practical Examples

### User Authentication

```php
<?php
function checkLogin($username, $password, $stored_hash) {
    if (empty($username)) {
        return "Username required";
    }
    
    if (empty($password)) {
        return "Password required";
    }
    
    if (!password_verify($password, $stored_hash)) {
        return "Invalid credentials";
    }
    
    if (strlen($password) < 8) {
        return "Password too short";
    }
    
    return "Login successful";
}

echo checkLogin("john", "pass123", password_hash("pass123", PASSWORD_DEFAULT));
?>
```

### Age-Based Access Control

```php
<?php
function checkAccess($age, $parental_consent) {
    if ($age >= 18) {
        return "Full access granted";
    } elseif ($age >= 13 && $parental_consent) {
        return "Teen access with consent";
    } elseif ($age >= 13) {
        return "Parental consent required";
    } else {
        return "Access denied";
    }
}

echo checkAccess(16, true);   // Teen access with consent
echo checkAccess(12, false);  // Access denied
?>
```

### Grade Calculation

```php
<?php
function getGrade($score) {
    if ($score >= 90) {
        return ["grade" => "A", "status" => "Excellent"];
    } elseif ($score >= 80) {
        return ["grade" => "B", "status" => "Good"];
    } elseif ($score >= 70) {
        return ["grade" => "C", "status" => "Average"];
    } elseif ($score >= 60) {
        return ["grade" => "D", "status" => "Below Average"];
    } else {
        return ["grade" => "F", "status" => "Failed"];
    }
}

$result = getGrade(85);
echo "Grade: " . $result["grade"] . " - " . $result["status"];
?>
```

### Form Validation

```php
<?php
function validateForm($email, $password, $age) {
    if (empty($email)) {
        return "Email is required";
    }
    
    if (strpos($email, "@") === false) {
        return "Invalid email format";
    }
    
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters";
    }
    
    if ($age < 18) {
        return "Must be 18 or older";
    }
    
    return "Form is valid";
}

echo validateForm("john@example.com", "securepass123", 25);
?>
```

### File Access Control

```php
<?php
function canAccessFile($user_role, $file_level, $is_owner) {
    if ($user_role == "admin") {
        return "Access granted";
    }
    
    if ($is_owner && $file_level <= 2) {
        return "Access granted (owner)";
    }
    
    if ($user_role == "editor" && $file_level <= 2) {
        return "Access granted (editor)";
    }
    
    if ($user_role == "viewer" && $file_level == 1) {
        return "Access granted (viewer)";
    }
    
    return "Access denied";
}

echo canAccessFile("editor", 2, false);
?>
```

## Nested If Statements

```php
<?php
$age = 25;
$income = 50000;
$creditScore = 750;

if ($age >= 18) {
    if ($income >= 30000) {
        if ($creditScore >= 700) {
            echo "Eligible for premium loan";
        } else {
            echo "Eligible for standard loan";
        }
    } else {
        echo "Income too low";
    }
} else {
    echo "Too young";
}

// Better: use combined conditions instead
if ($age >= 18 && $income >= 30000 && $creditScore >= 700) {
    echo "Eligible for premium loan";
} elseif ($age >= 18 && $income >= 30000) {
    echo "Eligible for standard loan";
} elseif ($age >= 18) {
    echo "Income too low";
} else {
    echo "Too young";
}
?>
```

## Common Patterns

### Default Values

```php
<?php
$discount = null;

if ($purchaseAmount > 1000) {
    $discount = 0.20;
} elseif ($purchaseAmount > 500) {
    $discount = 0.10;
} else {
    $discount = 0;
}

// Better approach
$discount = match(true) {
    $purchaseAmount > 1000 => 0.20,
    $purchaseAmount > 500 => 0.10,
    default => 0
};
?>
```

### Multiple Conditions

```php
<?php
$user_role = "editor";
$is_published = true;
$is_owner = true;

// Can edit if: owner OR (published AND editor)
if ($is_owner || ($is_published && $user_role == "editor")) {
    echo "Can edit";
}

// Can delete if: owner AND (not published OR is_admin)
if ($is_owner && (!$is_published || $user_role == "admin")) {
    echo "Can delete";
}
?>
```

## Common Pitfalls

### Assignment Instead of Comparison

```php
<?php
$x = 5;

// WRONG - assigns 10 to $x
if ($x = 10) {
    echo "This always runs";
}

// CORRECT - compares
if ($x == 10) {
    echo "Only if x equals 10";
}

// CORRECT - strict comparison (recommended)
if ($x === 10) {
    echo "Only if x strictly equals 10";
}
?>
```

### Missing Braces

```php
<?php
$age = 25;

// Confusing - only if statement within the block
if ($age >= 18)
    echo "Adult";
    echo "This runs regardless"; // This ALWAYS runs

// Correct
if ($age >= 18) {
    echo "Adult";
}
// This runs regardless
echo "Done";
?>
```

### Type Coercion Surprises

```php
<?php
$value = "0";

// String "0" is falsy
if ($value) {
    echo "True";
} else {
    echo "False";  // This executes
}

// Better: use strict comparison
if ($value === "0") {
    echo "Is string zero";
}

// Or explicit conversion
if ((int)$value > 0) {
    echo "Greater than zero";
}
?>
```

## Best Practices

✓ **Always use braces** even for single statements
✓ **Use === instead of ==** to avoid type coercion issues
✓ **Avoid deeply nested if statements** (max 2-3 levels)
✓ **Use meaningful variable names** in conditions
✓ **Put conditions on one line** when possible
✓ **Extract complex conditions** into descriptive functions
✓ **Order conditions** from most likely to least likely

## Key Takeaways

✓ **if** executes code when condition is true
✓ **else** provides alternative when condition is false
✓ **elseif** allows multiple branches
✓ **&&** (and) requires ALL conditions true
✓ **||** (or) requires ANY condition true
✓ **!** (not) negates a condition
✓ **Strict comparison (===)** is generally safer than loose (==)
✓ **Braces {} are required** for multiple statements
✓ **Nested if** should usually be replaced by combined conditions
✓ **Type coercion** can cause unexpected behavior
