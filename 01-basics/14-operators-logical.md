# Operators - Logical Operators

## Table of Contents
1. [Overview](#overview)
2. [AND Operator](#and-operator)
3. [OR Operator](#or-operator)
4. [NOT Operator](#not-operator)
5. [XOR Operator](#xor-operator)
6. [Operator Precedence](#operator-precedence)
7. [Short-Circuit Evaluation](#short-circuit-evaluation)
8. [Practical Examples](#practical-examples)
9. [Common Mistakes](#common-mistakes)

---

## Overview

Logical operators combine boolean values and return boolean results.

| Operator | Name | Example | Result |
|----------|------|---------|--------|
| `&&` | AND (logical) | `true && true` | `true` |
| `and` | AND (word) | `true and true` | `true` |
| `\|\|` | OR (logical) | `false \|\| true` | `true` |
| `or` | OR (word) | `false or true` | `true` |
| `!` | NOT | `!true` | `false` |
| `xor` | XOR | `true xor false` | `true` |

---

## AND Operator

Returns `true` if BOTH operands are true.

### Logical AND (&&)

```php
<?php
// Both must be true
true && true;      // true
true && false;     // false
false && true;     // false
false && false;    // false

// With values
5 > 3 && 2 < 4;    // true (both conditions true)
5 > 10 && 2 < 4;   // false (first condition false)

// Multiple conditions
if ($age >= 18 && $license && $insurance) {
    echo "Can drive";
}

// With variables
$is_admin = true;
$is_active = true;
if ($is_admin && $is_active) {
    echo "Admin access granted";
}
?>
```

### Word AND (and)

Same logic, but different precedence:

```php
<?php
// && has higher precedence than and
$result = true or false && false;
// Evaluated as: true or (false && false)
// Result: true

// Assignment with &&
$x = 5 && 10;
echo $x;  // 1 (true)

// Assignment with and
$y = 5 and 10;
echo $y;  // 5 (not assigned, different precedence)
?>
```

---

## OR Operator

Returns `true` if AT LEAST ONE operand is true.

### Logical OR (||)

```php
<?php
// At least one must be true
true || true;      // true
true || false;     // true
false || true;     // true
false || false;    // false

// With conditions
5 > 3 || 2 > 4;    // true (first condition true)
5 > 10 || 2 > 4;   // false (both false)

// Check multiple options
if ($payment_method == "credit" || $payment_method == "debit" || $payment_method == "paypal") {
    echo "Valid payment";
}

// User role check
if ($role == "admin" || $role == "moderator" || $role == "editor") {
    echo "Can edit content";
}
?>
```

### Word OR (or)

```php
<?php
// Same logic as ||
if ($user == null or $user == "") {
    echo "No user";
}

// Different precedence than ||
$x = true or false;
echo $x;  // 1 (true), but assignment might behave differently

$y = true || false;
echo $y;  // 1 (true)
?>
```

---

## NOT Operator

Reverses the boolean value.

### Logical NOT (!)

```php
<?php
!true;             // false
!false;            // true

// With expressions
!(5 > 3);          // false (because 5 > 3 is true)
!(5 < 3);          // true (because 5 < 3 is false)

// With variables
$is_active = true;
if (!$is_active) {
    echo "User is inactive";
} else {
    echo "User is active";
}

// Double negative
$value = 5;
if (!!$value) {
    echo "Value is truthy";  // Casts to boolean twice
}
?>
```

### NOT with Other Operators

```php
<?php
// NOT with equals
if ($x != 5) {
    echo "x is not 5";
}

// NOT with in_array
$allowed = ['admin', 'moderator'];
if (!in_array($role, $allowed)) {
    echo "Access denied";
}

// NOT with function
if (!isset($variable)) {
    echo "Variable not set";
}

// NOT with is_null
if (!is_null($value)) {
    echo "Value exists";
}
?>
```

---

## XOR Operator

Returns `true` if operands are different (exclusive OR).

```php
<?php
// Exactly one must be true
true xor true;     // false (same)
true xor false;    // true (different)
false xor true;    // true (different)
false xor false;   // false (same)

// With conditions
5 > 3 xor 2 < 1;   // true XOR false = true
5 > 3 xor 2 < 4;   // true XOR true = false

// Practical: toggle
$is_active = true;
$is_active = $is_active xor true;  // Now false (toggle)

$is_active = $is_active xor true;  // Now true (toggle)
?>
```

---

## Operator Precedence

Order from highest to lowest:

```
1. ! (NOT)
2. && (AND)
3. || (OR)
4. and, xor, or (lower precedence)
```

### Precedence Examples

```php
<?php
// Without parentheses (follows precedence)
true || false && false;
// Evaluated as: true || (false && false)
// Result: true

// With parentheses (explicit)
(true || false) && false;
// Result: false

// Best practice: use parentheses
if (($is_admin || $is_moderator) && $is_active) {
    echo "Access granted";
}
?>
```

---

## Short-Circuit Evaluation

Operators stop evaluating once result is determined.

### AND Short-Circuit

```php
<?php
// If first is false, second isn't evaluated
false && expensive_function();
// expensive_function() is NOT called

// If first is true, second IS evaluated
true && expensive_function();
// expensive_function() IS called

// Practical use
$user = getUserById(123);
if ($user && $user->isActive()) {
    // Second condition only checked if $user exists
    echo "User is active";
}
?>
```

### OR Short-Circuit

```php
<?php
// If first is true, second isn't evaluated
true || expensive_function();
// expensive_function() is NOT called

// If first is false, second IS evaluated
false || expensive_function();
// expensive_function() IS called

// Practical: default values
$username = $input['username'] || 'Guest';
// Uses $input['username'] if true, otherwise evaluates second part
?>
```

---

## Practical Examples

### Permission Checking

```php
<?php
class User {
    public function hasPermission($resource, $action) {
        // Check multiple conditions
        return (
            ($this->is_admin && $this->is_active) ||
            ($this->role === 'editor' && in_array($action, ['read', 'write']))
        ) && $this->has_permission_for($resource);
    }
}

if ($user->hasPermission('posts', 'delete')) {
    echo "Delete allowed";
}
?>
```

### Form Validation

```php
<?php
function validateUser($data) {
    $errors = [];
    
    // Name validation
    if (!isset($data['name']) || trim($data['name']) === "") {
        $errors[] = "Name required";
    }
    
    // Email validation
    if (!isset($data['email']) || 
        !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email required";
    }
    
    // Age validation
    if (!isset($data['age']) || $data['age'] < 18 || $data['age'] > 120) {
        $errors[] = "Age must be between 18 and 120";
    }
    
    // Terms agreement
    if (!isset($data['terms']) || !$data['terms']) {
        $errors[] = "Must agree to terms";
    }
    
    return [
        'valid' => count($errors) === 0,
        'errors' => $errors
    ];
}

$result = validateUser([
    'name' => 'John',
    'email' => 'john@example.com',
    'age' => 25,
    'terms' => true
]);

if ($result['valid']) {
    echo "Form is valid";
} else {
    foreach ($result['errors'] as $error) {
        echo "- $error\n";
    }
}
?>
```

### Conditional Authentication

```php
<?php
class Auth {
    public function canAccess($user, $resource) {
        // Must be logged in AND either admin or resource owner
        return isset($user) && 
               $user->isActive && 
               ($user->isAdmin() || $user->ownsResource($resource));
    }
}

// Usage
if ($auth->canAccess($currentUser, $post)) {
    echo "You can access this post";
} else {
    echo "Access denied";
}
?>
```

### Feature Flags

```php
<?php
$config = [
    'feature_beta' => true,
    'feature_dark_mode' => false,
    'user_is_beta_tester' => true,
];

// User can see beta features if they're a beta tester AND feature is enabled
if ($config['feature_beta'] && $config['user_is_beta_tester']) {
    echo "Show beta features";
}

// Show dark mode if enabled OR user has it in preferences
if ($config['feature_dark_mode'] || $user->prefersDarkMode()) {
    echo "Apply dark mode";
}
?>
```

---

## Common Mistakes

### 1. Confusing && and ||

```php
<?php
// ❌ Wrong logic
if ($age > 18 || $parent_consent) {
    echo "Can proceed";
    // This allows 12-year-old with parent consent
    // But also allows 50-year-old without consent (because age > 18)
}

// ✅ Correct logic
if (($age > 18) || ($age <= 18 && $parent_consent)) {
    echo "Can proceed";
}

// Or simpler
if ($age >= 18 || $parent_consent) {
    echo "Can proceed";
}
?>
```

### 2. NOT Operator Precedence

```php
<?php
// ❌ Wrong (! binds tighter than comparison)
if (!$x > 5) {
    // Interpreted as: (!$x) > 5
    // !$x is a boolean, comparing boolean > 5
    echo "This is confusing";
}

// ✅ Correct
if ($x > 5 == false) {
    echo "x is not greater than 5";
}

// ✅ Clearer
if (!($x > 5)) {
    echo "x is not greater than 5";
}
?>
```

### 3. Logical AND with Assignment

```php
<?php
// ❌ Confusing precedence
$value = $condition && $function();
// && is evaluated before assignment!
// $value gets boolean result, not return value of function

// ✅ Better
if ($condition && $function()) {
    $value = true;
}

// ✅ Or use ternary
$value = $condition ? $function() : null;
?>
```

### 4. Array and Loose Truthiness

```php
<?php
// ❌ Surprising behavior
$result = [];
if ($result) {
    echo "Array is truthy";
} else {
    echo "Array is falsy";  // Executes - empty array is falsy
}

// ✅ Check explicitly
if (count($result) > 0) {
    echo "Array has items";
}

// ✅ Or use empty()
if (!empty($result)) {
    echo "Array has items";
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class BookStore {
    private array $inventory = [];
    private float $total_value = 0;
    
    public function addBook($title, $quantity, $price) {
        // Validate input
        if (
            empty($title) || !is_string($title) ||
            $quantity <= 0 || !is_int($quantity) ||
            $price <= 0 || !is_numeric($price)
        ) {
            return false;
        }
        
        $this->inventory[$title] = [
            'quantity' => $quantity,
            'price' => (float)$price
        ];
        
        return true;
    }
    
    public function canSell($title, $quantity) {
        // Check if book exists AND has enough stock
        return isset($this->inventory[$title]) && 
               $this->inventory[$title]['quantity'] >= $quantity;
    }
    
    public function sellBook($title, $quantity) {
        // Must exist AND have stock
        if (!$this->canSell($title, $quantity)) {
            return null;
        }
        
        $book = $this->inventory[$title];
        $price = $book['price'] * $quantity;
        
        $this->inventory[$title]['quantity'] -= $quantity;
        
        // Remove if empty
        if ($this->inventory[$title]['quantity'] === 0) {
            unset($this->inventory[$title]);
        }
        
        return [
            'title' => $title,
            'quantity' => $quantity,
            'price' => $price
        ];
    }
    
    public function getStatus() {
        // Book has discount if stock is low OR price is over $50
        foreach ($this->inventory as $title => $book) {
            $low_stock = $book['quantity'] < 5;
            $high_price = $book['price'] > 50;
            
            $has_discount = $low_stock || $high_price;
            
            echo "$title: {$book['quantity']} units @ \${$book['price']}" .
                 ($has_discount ? " [DISCOUNT]" : "") . "\n";
        }
    }
}

// Usage
$store = new BookStore();
$store->addBook("PHP Master", 10, 29.99);
$store->addBook("JavaScript Pro", 3, 59.99);

if ($store->canSell("PHP Master", 2)) {
    $sale = $store->sellBook("PHP Master", 2);
    echo "Sold: {$sale['title']} x {$sale['quantity']} = \${$sale['price']}\n";
}

$store->getStatus();
?>
```

---

## Next Steps

✅ Understand logical operators  
→ Learn [comparison operators](12-operators-comparison.md)  
→ Study [conditionals (if/else)](18-if-statement.md)  
→ Master [switch statements](19-switch-statement.md)
