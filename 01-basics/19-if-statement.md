# Conditionals - if/else, Control Flow

## Table of Contents
1. [Overview](#overview)
2. [if Statement](#if-statement)
3. [if/else Statement](#ifelse-statement)
4. [else if (elseif) Statement](#else-if-elseif-statement)
5. [Comparison Operators](#comparison-operators)
6. [Nested Conditionals](#nested-conditionals)
7. [Common Patterns](#common-patterns)
8. [Common Mistakes](#common-mistakes)

---

## Overview

Conditionals allow your code to make decisions based on conditions.

```php
<?php
$age = 18;

if ($age >= 18) {
    echo "You are an adult";
} else {
    echo "You are a minor";
}
?>
```

---

## if Statement

Execute code only if a condition is true:

```php
<?php
$temperature = 85;

if ($temperature > 80) {
    echo "It's hot outside!";
}
?>
```

### Syntax

```php
<?php
if (condition) {
    // Code executes if condition is true
}
?>
```

### Examples

```php
<?php
// Simple condition
if (true) {
    echo "This always runs";
}

// Check variable
$isLoggedIn = true;
if ($isLoggedIn) {
    echo "Welcome, user!";
}

// Comparison
$score = 85;
if ($score >= 80) {
    echo "Pass!";
}

// Without braces (not recommended)
if ($score >= 80) echo "Pass!";
?>
```

---

## if/else Statement

Execute one block if true, another if false:

```php
<?php
$age = 16;

if ($age >= 18) {
    echo "You can vote";
} else {
    echo "You cannot vote yet";
}
// Output: You cannot vote yet
?>
```

### Syntax

```php
<?php
if (condition) {
    // Executes if condition is true
} else {
    // Executes if condition is false
}
?>
```

### Examples

```php
<?php
// Positive/Negative
$number = -5;
if ($number > 0) {
    echo "Positive";
} else {
    echo "Negative or zero";
}

// Even/Odd
$num = 7;
if ($num % 2 == 0) {
    echo "Even";
} else {
    echo "Odd";
}

// Status check
$isActive = false;
if ($isActive) {
    echo "Account is active";
} else {
    echo "Account is inactive";
}
?>
```

---

## else if (elseif) Statement

Check multiple conditions:

```php
<?php
$score = 75;

if ($score >= 90) {
    echo "Grade: A";
} elseif ($score >= 80) {
    echo "Grade: B";
} elseif ($score >= 70) {
    echo "Grade: C";
} elseif ($score >= 60) {
    echo "Grade: D";
} else {
    echo "Grade: F";
}
// Output: Grade: C
?>
```

### Syntax

```php
<?php
if (condition1) {
    // Executes if condition1 is true
} elseif (condition2) {
    // Executes if condition1 is false AND condition2 is true
} elseif (condition3) {
    // Executes if previous conditions are false AND condition3 is true
} else {
    // Executes if all conditions are false
}
?>
```

### Complete Grade Example

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

echo getGrade(95);   // A
echo getGrade(85);   // B
echo getGrade(75);   // C
echo getGrade(65);   // D
echo getGrade(55);   // F
?>
```

---

## Comparison Operators

### Common Operators

| Operator | Name | Example | Result |
|----------|------|---------|--------|
| `==` | Equal | `5 == 5` | true |
| `===` | Identical | `5 === "5"` | false |
| `!=` | Not equal | `5 != 3` | true |
| `!==` | Not identical | `5 !== "5"` | true |
| `<` | Less than | `3 < 5` | true |
| `>` | Greater than | `5 > 3` | true |
| `<=` | Less or equal | `5 <= 5` | true |
| `>=` | Greater or equal | `5 >= 5` | true |

### Equality vs Identity

```php
<?php
// Equal (==) - compares value only
5 == "5";        // true - values match
0 == false;      // true - values match

// Identical (===) - compares value AND type
5 === "5";       // false - different types
0 === false;     // false - different types

// In conditionals, always use ===
if ($count === 0) {    // Recommended
    echo "No items";
}

if ($count == 0) {     // Risky - might match other falsy values
    echo "No items";
}
?>
```

---

## Nested Conditionals

Conditionals inside conditionals:

```php
<?php
$age = 25;
$hasLicense = true;

if ($age >= 18) {
    if ($hasLicense) {
        echo "You can drive";
    } else {
        echo "Get your license";
    }
} else {
    echo "You're too young to drive";
}
// Output: You can drive
?>
```

### Using Logical Operators Instead

```php
<?php
$age = 25;
$hasLicense = true;

// More readable
if ($age >= 18 && $hasLicense) {
    echo "You can drive";
} elseif ($age >= 18 && !$hasLicense) {
    echo "Get your license";
} else {
    echo "You're too young to drive";
}
?>
```

---

## Common Patterns

### Validation Pattern

```php
<?php
$email = "user@example.com";

if (empty($email)) {
    echo "Email required";
} elseif (strlen($email) < 5) {
    echo "Email too short";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format";
} else {
    echo "Email is valid";
}
?>
```

### Authorization Pattern

```php
<?php
$userRole = "user";
$resource = "admin-panel";

if ($userRole === "admin") {
    echo "Access granted";
} elseif ($userRole === "moderator" && $resource !== "admin-panel") {
    echo "Access granted";
} else {
    echo "Access denied";
}
?>
```

### Early Return Pattern

```php
<?php
function processUser($user) {
    // Check conditions early and return if invalid
    if (empty($user['name'])) {
        return "Error: Name required";
    }
    
    if (empty($user['email'])) {
        return "Error: Email required";
    }
    
    if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        return "Error: Invalid email";
    }
    
    // If all checks pass, process normally
    return "User processed successfully";
}
?>
```

---

## Common Mistakes

### 1. Assignment Instead of Comparison

```php
<?php
$count = 5;

// ❌ Wrong: Assignment (=)
if ($count = 0) {  // Sets $count to 0!
    echo "No items";
}

// ✅ Correct: Comparison (==)
if ($count == 0) {
    echo "No items";
}

// ✅ Better: Strict comparison (===)
if ($count === 0) {
    echo "No items";
}
?>
```

### 2. Forgetting Braces

```php
<?php
$age = 16;

// ❌ Easy to make mistakes
if ($age >= 18)
    echo "Adult";
else
    echo "Minor";

// ✅ Always use braces
if ($age >= 18) {
    echo "Adult";
} else {
    echo "Minor";
}
?>
```

### 3. String Comparison Issues

```php
<?php
// ❌ Can be unreliable
if ("10" > "9") {
    echo "10 is greater";
} else {
    echo "9 is greater";  // This runs! String comparison
}

// ✅ Use explicit types
if ((int)"10" > (int)"9") {
    echo "10 is greater";
}

// ✅ Or use strict comparison
if (10 > 9) {  // Numbers, not strings
    echo "10 is greater";
}
?>
```

### 4. Missing elseif

```php
<?php
$day = "Monday";

// ❌ Inefficient: multiple if statements
if ($day == "Monday") { echo "Day 1"; }
if ($day == "Tuesday") { echo "Day 2"; }
if ($day == "Wednesday") { echo "Day 3"; }

// ✅ Use elseif
if ($day == "Monday") {
    echo "Day 1";
} elseif ($day == "Tuesday") {
    echo "Day 2";
} elseif ($day == "Wednesday") {
    echo "Day 3";
}

// ✅ Or better: use switch
switch ($day) {
    case "Monday":
        echo "Day 1";
        break;
    case "Tuesday":
        echo "Day 2";
        break;
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

// Age-based membership tier
function getMembershipTier(int $age, bool $isPremium): string {
    if ($age < 13) {
        return "Child account (not allowed)";
    } elseif ($age < 18) {
        if ($isPremium) {
            return "Teen Premium";
        } else {
            return "Teen Free";
        }
    } else {
        if ($isPremium) {
            return "Adult Premium";
        } else {
            return "Adult Free";
        }
    }
}

// Test cases
echo getMembershipTier(12, false) . "\n";   // Child account (not allowed)
echo getMembershipTier(15, true) . "\n";    // Teen Premium
echo getMembershipTier(16, false) . "\n";   // Teen Free
echo getMembershipTier(25, true) . "\n";    // Adult Premium
echo getMembershipTier(30, false) . "\n";   // Adult Free
?>
```

**Output:**
```
Child account (not allowed)
Teen Premium
Teen Free
Adult Premium
Adult Free
```

---

## Next Steps

✅ Master if/else conditionals  
→ Learn [switch statements](19-switch-statement.md)  
→ Study [loops](22-for-loop.md)  
→ Practice [logical operators](13-operators-logical.md)
