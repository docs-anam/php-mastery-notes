# Logical Operators in PHP

## What are Logical Operators?

Logical operators are used to combine or negate boolean values. They allow you to create complex conditions by joining multiple comparisons together, enabling your code to make more sophisticated decisions.

```
$a = true, $b = false

AND:  $a && $b  → false
OR:   $a || $b  → true
NOT:  !$a       → false
XOR:  $a xor $b → true
```

## Basic Logical Operators

### AND (&&)

Returns `true` only if BOTH conditions are `true`.

```php
<?php
$age = 25;
$has_license = true;

// Both conditions must be true
if ($age >= 18 && $has_license) {
    echo "Can drive";  // Output: Can drive
}

// Works with multiple conditions
if ($age > 18 && $age < 65 && $has_license) {
    echo "Eligible";
}

// Short circuit evaluation
$x = 10;
$y = 0;
if ($x > 5 && $y > 0) {
    echo "Both positive";  // Doesn't execute, $y > 0 is false
}
?>
```

### AND (and)

Alternative syntax for && (lower precedence).

```php
<?php
// Same as && but with lower precedence
$a = true;
$b = true;

if ($a and $b) {
    echo "Both true";
}

// Note: precedence difference
$result = false or true;    // $result = true
$result = false || true;    // $result = true (different behavior with assignment)
?>
```

### OR (||)

Returns `true` if AT LEAST ONE condition is `true`.

```php
<?php
$is_weekend = false;
$is_holiday = true;

// At least one condition must be true
if ($is_weekend || $is_holiday) {
    echo "No work today";  // Output: No work today
}

// Works with multiple conditions
if ($age < 13 || $age > 65 || $income < 1000) {
    echo "Eligible for special rate";
}

// Short circuit evaluation
$x = 5;
$y = 10;
if ($x > 10 || $y > 5) {
    echo "At least one is true";  // Executes because $y > 5
}
?>
```

### OR (or)

Alternative syntax for || (lower precedence).

```php
<?php
$is_admin = false;
$is_moderator = true;

if ($is_admin or $is_moderator) {
    echo "Has permissions";
}
?>
```

### NOT (!)

Inverts/negates a boolean value.

```php
<?php
$is_logged_in = true;

// NOT operator reverses the value
if (!$is_logged_in) {
    echo "Please log in";  // Doesn't execute
} else {
    echo "Welcome back";   // Output: Welcome back
}

// Useful for checking false conditions
$has_error = false;
if (!$has_error) {
    echo "All good";  // Output: All good
}

// Double negative
$is_valid = true;
if (!!$is_valid) {
    echo "Definitely valid";  // Output: Definitely valid
}
?>
```

### XOR (xor)

Returns `true` if EXACTLY ONE condition is `true` (exclusive or).

```php
<?php
// True if one or the other, but not both
$a = true;
$b = false;
echo $a xor $b;  // Output: true (one is true)

$x = true;
$y = true;
echo $x xor $y;  // Output: false (both are true)

// Practical example: payment method
$credit_card = true;
$paypal = false;
if ($credit_card xor $paypal) {
    echo "Exactly one payment method selected";  // Output
}
?>
```

## Logical Operators Summary Table

| Operator | Name | Example | Result |
|----------|------|---------|--------|
| && | AND | true && false | false |
| \|\| | OR | true \|\| false | true |
| ! | NOT | !true | false |
| and | AND (lower precedence) | true and false | false |
| or | OR (lower precedence) | true or false | true |
| xor | XOR (exclusive or) | true xor false | true |

## Combining Multiple Conditions

### AND with Multiple Conditions

```php
<?php
$age = 25;
$has_license = true;
$no_violations = true;

// All conditions must be true
if ($age >= 18 && $has_license && $no_violations) {
    echo "Can drive";  // Output: Can drive
}

// Short circuit: stops checking if one fails
if ($age >= 18 && $has_license && $no_violations) {
    // If $age < 18, PHP doesn't check the rest
}
?>
```

### OR with Multiple Conditions

```php
<?php
$is_admin = false;
$is_moderator = false;
$is_owner = true;

// At least one must be true
if ($is_admin || $is_moderator || $is_owner) {
    echo "Has permissions";  // Output: Has permissions
}

// Short circuit: stops checking if one is true
if ($is_admin || $is_moderator || $is_owner) {
    // If $is_admin is true, doesn't check the rest
}
?>
```

### Mixing AND and OR

```php
<?php
$age = 25;
$has_license = true;
$is_insured = false;
$is_parent = true;

// Complex condition
if (($age >= 25 && $has_license) || ($is_parent && !$is_insured)) {
    echo "Can borrow car";
}

// Better to use parentheses for clarity
if ((age >= 18 && $has_license && $is_insured) || 
    ($age < 18 && $is_parent)) {
    echo "Allowed";
}
?>
```

## Practical Examples

### User Access Control

```php
<?php
function hasAccess($is_logged_in, $is_admin, $is_owner) {
    // Admin OR owner can access
    if ($is_admin || $is_owner) {
        return true;
    }
    
    // Regular user must be logged in
    if ($is_logged_in && !$is_admin) {
        return true;
    }
    
    return false;
}

echo hasAccess(true, false, false);   // true (logged in)
echo hasAccess(false, true, false);   // true (admin)
echo hasAccess(false, false, true);   // true (owner)
echo hasAccess(false, false, false);  // false
?>
```

### Form Validation

```php
<?php
$username = "john_doe";
$password = "secure123";
$email = "john@example.com";

// All fields must be filled
if (!empty($username) && !empty($password) && !empty($email)) {
    echo "Form is valid";
}

// At least one field is empty
if (empty($username) || empty($password) || empty($email)) {
    echo "Please fill all fields";
}

// Username length and no spaces
if (strlen($username) >= 3 && strlen($username) <= 20 && strpos($username, " ") === false) {
    echo "Username is valid";
}
?>
```

### Age and Income Eligibility

```php
<?php
function isEligible($age, $income) {
    // Young and low income OR senior and low income
    if (($age < 18 && $income < 30000) || 
        ($age > 65 && $income < 20000)) {
        return true;  // Eligible for subsidy
    }
    
    // Middle-aged with very low income
    if ($age >= 18 && $age <= 65 && $income < 10000) {
        return true;  // Eligible for hardship program
    }
    
    return false;
}

echo isEligible(16, 15000);  // true
echo isEligible(70, 18000);  // true
echo isEligible(45, 8000);   // true
echo isEligible(45, 50000);  // false
?>
```

### Permission System

```php
<?php
function canDelete($user_id, $post_owner, $is_admin, $is_moderator) {
    // Admin or moderator can delete any post
    if ($is_admin || $is_moderator) {
        return true;
    }
    
    // User can only delete their own posts
    if ($user_id === $post_owner && !$is_admin) {
        return true;
    }
    
    return false;
}

// User deleting own post
echo canDelete(5, 5, false, false);      // true

// User deleting others post
echo canDelete(5, 6, false, false);      // false

// Admin deleting any post
echo canDelete(5, 6, true, false);       // true
?>
```

## Short Circuit Evaluation

Logical operators stop evaluating once the result is determined:

```php
<?php
// AND: stops when it finds false
$a = false;
$b = expensiveFunction();  // Never called!

if ($a && $b) {
    echo "Both true";
}

// OR: stops when it finds true
$x = true;
$y = expensiveFunction();  // Never called!

if ($x || $y) {
    echo "At least one true";
}

// Practical example: avoid null pointer errors
$user = null;
if ($user != null && $user->isAdmin()) {
    echo "Admin user";  // Safe - won't call isAdmin on null
}

// Optimization example
if (isUserLoggedIn() && hasPermission($user)) {
    // If not logged in, doesn't check permission
}
?>
```

## De Morgan's Laws

Logical equivalences helpful for simplifying conditions:

```php
<?php
// NOT (A AND B) = (NOT A) OR (NOT B)
// !(a && b) === (!a || !b)

// NOT (A OR B) = (NOT A) AND (NOT B)
// !(a || b) === (!a && !b)

// Example: checking if input is invalid
$age = 15;
$has_license = false;

// Original
if (!($age >= 18 && $has_license)) {
    echo "Cannot drive";
}

// Simplified with De Morgan's
if ($age < 18 || !$has_license) {
    echo "Cannot drive";
}

// Both are equivalent!
?>
```

## Common Pitfalls

### Operator Precedence

```php
<?php
// Be careful with precedence
$a = true;
$b = false;
$c = true;

// This might not do what you expect
$result = $a || $b && $c;  // && has higher precedence
// Interpreted as: $a || ($b && $c)
// Result: true

// Be explicit with parentheses
$result = ($a || $b) && $c;  // Different result!
// Result: true

// Always use parentheses for clarity
if ((condition1 || condition2) && condition3) {
    // Clear intent
}
?>
```

### Forgetting Parentheses

```php
<?php
$admin = false;
$owner = true;
$user_id = 5;
$post_owner = 5;

// Wrong: logic is incorrect
if ($admin || $owner && $user_id == $post_owner) {
    // Executes if admin OR if (owner AND user_id == post_owner)
}

// Right: explicit parentheses
if ($admin || ($owner && $user_id == $post_owner)) {
    // Clear intent
}

// Or completely different
if (($admin || $owner) && $user_id == $post_owner) {
    // Different logic
}
?>
```

### Using Assignment in Conditions

```php
<?php
// Wrong: uses = instead of ==
$x = 5;
if ($x = 10) {  // Assigns 10, doesn't compare
    echo $x;   // 10 (value was changed)
}

// Right: use ==
if ($x == 10) {
    echo "x equals 10";
}
?>
```

## Best Practices

### 1. Use Clear Variable Names

```php
<?php
// Good
$is_adult = $age >= 18;
$has_valid_license = $license && !$license_expired;

if ($is_adult && $has_valid_license) {
    echo "Can drive";
}

// Avoid
$a = $age >= 18;
$b = $license && !$license_expired;

if ($a && $b) {
    echo "Can drive";
}
?>
```

### 2. Use Parentheses for Complex Conditions

```php
<?php
// Good: parentheses make it clear
if ((is_logged_in && has_permission) || is_admin) {
    // Logic is clear
}

// Avoid
if (is_logged_in && has_permission || is_admin) {
    // Could be ambiguous
}
?>
```

### 3. Break into Multiple Lines

```php
<?php
// Good: easier to read
if ($age >= 18 && 
    $has_license && 
    $is_insured) {
    echo "Can drive";
}

// Still readable
if ($is_admin || 
    $is_moderator || 
    $is_owner) {
    echo "Has permissions";
}
?>
```

### 4. Extract Complex Logic

```php
<?php
// Instead of one big condition
if ((age >= 18 && has_license && is_insured) || 
    (age < 18 && is_parent_approved && parent_drivers)) {
    echo "Can drive";
}

// Extract to readable function
function canDrive($age, $has_license, $is_insured, $is_parent_approved, $parent_drivers) {
    $adult_driver = $age >= 18 && $has_license && $is_insured;
    $young_driver = $age < 18 && $is_parent_approved && $parent_drivers;
    
    return $adult_driver || $young_driver;
}

if (canDrive($age, $has_license, $is_insured, $is_parent_approved, $parent_drivers)) {
    echo "Can drive";
}
?>
```

## Key Takeaways

✓ **AND (&&)** returns true only if BOTH conditions are true
✓ **OR (||)** returns true if AT LEAST ONE condition is true
✓ **NOT (!)** reverses/negates a boolean value
✓ **XOR (xor)** returns true if EXACTLY ONE condition is true
✓ **Short circuit evaluation** improves performance and prevents errors
✓ **Always use === with parentheses** for clarity in complex conditions
✓ **De Morgan's Laws** help simplify logical expressions
✓ **Lower precedence (and/or)** exists but prefer && and || for clarity
✓ **Extract complex logic** into named functions for readability
✓ **Use meaningful variable names** to make conditions self-documenting
