# Ternary Operator and Conditional Expressions

## Table of Contents
1. [Overview](#overview)
2. [Basic Ternary Operator](#basic-ternary-operator)
3. [Shorthand Elvis Operator](#shorthand-elvis-operator)
4. [Null Coalescing](#null-coalescing)
5. [Nested Ternary](#nested-ternary)
6. [Practical Examples](#practical-examples)
7. [Common Mistakes](#common-mistakes)

---

## Overview

The ternary operator (`? :`) provides a shorthand way to write simple if/else statements.

```
condition ? value_if_true : value_if_false
```

| Operator | Form | Example |
|----------|------|---------|
| Ternary | `? :` | `$age > 18 ? "Adult" : "Minor"` |
| Elvis | `? :` | `$name ?: "Guest"` |
| Null Coalesce | `??` | `$user['name'] ?? "Unknown"` |

---

## Basic Ternary Operator

### Simple Usage

```php
<?php
$age = 20;
$status = ($age >= 18) ? "Adult" : "Minor";
echo $status;  // Adult

// Without parentheses (but safer with them)
$status = $age >= 18 ? "Adult" : "Minor";
echo $status;  // Adult
?>
```

### Assignment

```php
<?php
$score = 75;
$result = ($score >= 60) ? "Pass" : "Fail";
echo $result;  // Pass

// With variable on right side
$default_name = "Guest";
$name = (!empty($input)) ? $input : $default_name;
echo $name;  // Value from $input or default
?>
```

### Compared to If/Else

```php
<?php
// If/Else version
$age = 25;
if ($age >= 21) {
    $can_drink = true;
} else {
    $can_drink = false;
}

// Ternary version
$can_drink = ($age >= 21) ? true : false;

// Simplified
$can_drink = $age >= 21;
?>
```

---

## Shorthand Elvis Operator

The Elvis operator (`? :`) omits the true value (uses the condition if true).

### Basic Elvis

```php
<?php
// Full ternary
$username = isset($user['name']) ? $user['name'] : "Guest";

// Elvis operator (shorthand)
$username = $user['name'] ?: "Guest";

// Equivalent to checking if true
$flag = $condition ?: false;
?>
```

### Truthy/Falsy Checks

```php
<?php
// Get first non-empty value
$first_name = $input['first'] ?: $input['fallback'] ?: "Unknown";

// Check variable existence and value
$role = $user['role'] ?: "visitor";

// Get non-null value
$config_value = $_GET['value'] ?: getDefaultValue();
?>
```

### Common Pattern

```php
<?php
// Provide defaults
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: 3306;
$username = getenv('DB_USER') ?: 'root';

// Use values or defaults
$name = $profile['display_name'] ?: $profile['username'] ?: "Anonymous";
?>
```

---

## Null Coalescing

The null coalescing operator (`??`) only triggers on `null`, not other falsy values.

### Basic Usage

```php
<?php
// Ternary: null check
$name = isset($user['name']) ? $user['name'] : "Guest";

// Null coalescing: simpler
$name = $user['name'] ?? "Guest";

// Only cares about null, not empty string
$value = $data['empty_string'] ?? "default";
echo $value;  // "" (empty string, not null)

$value = $data['null_value'] ?? "default";
echo $value;  // "default" (was null)
?>
```

### Chaining

```php
<?php
// Find first non-null value
$display_name = $user['nickname'] ?? $user['full_name'] ?? $user['email'] ?? "Guest";

// Access nested arrays safely
$city = $address['city'] ?? $user['default_city'] ?? "Unknown";

// With function calls
$value = $config['custom'] ?? getDefaultConfig() ?? "fallback";
?>
```

### Avoiding Undefined Index Errors

```php
<?php
// ❌ Without null coalescing - generates notice
$email = $data['email'];  // Notice if 'email' doesn't exist

// ✅ With null coalescing - safe
$email = $data['email'] ?? 'no-email@example.com';

// ✅ Or with isset
if (isset($data['email'])) {
    $email = $data['email'];
} else {
    $email = 'no-email@example.com';
}
?>
```

---

## Nested Ternary

Nesting ternary operators creates complex conditional logic.

### Multiple Conditions

```php
<?php
$score = 85;
$grade = ($score >= 90) ? "A" : (($score >= 80) ? "B" : (($score >= 70) ? "C" : "F"));
echo $grade;  // B
?>
```

### Better: Use If/Else

```php
<?php
// Hard to read
$grade = ($score >= 90) ? "A" : (($score >= 80) ? "B" : (($score >= 70) ? "C" : "F"));

// Much clearer
if ($score >= 90) {
    $grade = "A";
} elseif ($score >= 80) {
    $grade = "B";
} elseif ($score >= 70) {
    $grade = "C";
} else {
    $grade = "F";
}

// Or use switch
switch (true) {
    case $score >= 90: $grade = "A"; break;
    case $score >= 80: $grade = "B"; break;
    case $score >= 70: $grade = "C"; break;
    default: $grade = "F";
}
?>
```

---

## Practical Examples

### Default Values

```php
<?php
// User profile with defaults
$profile = [
    'name' => $input['name'] ?? "Anonymous",
    'email' => $input['email'] ?? null,
    'age' => isset($input['age']) ? (int)$input['age'] : null,
    'country' => $input['country'] ?: "USA",
];

var_dump($profile);
?>
```

### Display Logic

```php
<?php
function getDisplayStatus($user) {
    return $user['is_online'] ? "Online" : 
           $user['last_seen'] ? "Last seen: " . formatTime($user['last_seen']) : 
           "Offline";
}

// Simpler version
function getDisplayStatus($user) {
    if ($user['is_online']) return "Online";
    if ($user['last_seen']) return "Last seen: " . formatTime($user['last_seen']);
    return "Offline";
}
?>
```

### Validation Response

```php
<?php
function validateEmail($email) {
    $is_valid = filter_var($email, FILTER_VALIDATE_EMAIL);
    
    return [
        'valid' => (bool)$is_valid,
        'message' => $is_valid ? "Email is valid" : "Invalid email format"
    ];
}

// Or shorter
function validateEmail($email) {
    return [
        'valid' => (bool)filter_var($email, FILTER_VALIDATE_EMAIL),
        'message' => filter_var($email, FILTER_VALIDATE_EMAIL) ? "Valid" : "Invalid"
    ];
}
?>
```

### Permissions Check

```php
<?php
class User {
    public function canEdit($post) {
        // Can edit if: owner, OR admin
        return ($this->id === $post->author_id) ? true : 
               ($this->is_admin) ? true : 
               false;
    }
    
    // Cleaner version
    public function canEdit($post) {
        return $this->id === $post->author_id || $this->is_admin;
    }
}
?>
```

### Formatting Values

```php
<?php
// Format boolean
$status = $is_active ? "Active" : "Inactive";

// Format currency
$price = $product['on_sale'] ? "$" . ($product['price'] * 0.8) : "$" . $product['price'];

// Format date
$date_display = $created_at ? date('M d, Y', strtotime($created_at)) : "Unknown";

// Format count
$count_text = count($items) . " item" . (count($items) != 1 ? "s" : "");
// Better: use plural() function if available
?>
```

---

## Common Mistakes

### 1. Confusing == and ===

```php
<?php
// ❌ Type juggling
$value = "0";
$result = $value ? "truthy" : "falsy";
echo $result;  // "falsy" - "0" is falsy!

// ✅ Explicit check
$result = ($value === true) ? "is true" : "not true";
echo $result;  // "not true"

// ✅ Or use strict null check
$result = ($value !== null && $value !== "") ? "has value" : "empty";
?>
```

### 2. Side Effects in Conditions

```php
<?php
// ❌ Function with side effects
$count = 0;
$result = (++$count > 0) ? "yes" : "no";
echo $count;  // 1 - modified!

// ✅ Separate the increment
$count++;
$result = ($count > 0) ? "yes" : "no";
?>
```

### 3. Unclear Precedence

```php
<?php
// ❌ Ambiguous
$a = 5;
$b = 10;
$result = $a > 3 ? "yes" : $b > 20 ? "maybe" : "no";

// ✅ Add parentheses for clarity
$result = ($a > 3) ? "yes" : (($b > 20) ? "maybe" : "no");

// ✅ Better: use if/else
if ($a > 3) {
    $result = "yes";
} elseif ($b > 20) {
    $result = "maybe";
} else {
    $result = "no";
}
?>
```

### 4. Over-nesting

```php
<?php
// ❌ Too nested
$x = $a ? ($b ? ($c ? "all" : "c") : "b") : "a";

// ✅ Use if/else
if ($a) {
    if ($b) {
        $x = $c ? "all" : "c";
    } else {
        $x = "b";
    }
} else {
    $x = "a";
}

// ✅ Or use switch
switch (true) {
    case $a && $b && $c: $x = "all"; break;
    case $a && $b: $x = "b"; break;
    case $a: $x = "c"; break;
    default: $x = "a";
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class UserProfile {
    private ?array $user;
    
    public function __construct(?array $user = null) {
        $this->user = $user;
    }
    
    public function getDisplayName(): string {
        // Null coalesce chain
        return $this->user['display_name'] ?? 
               $this->user['first_name'] ?? 
               $this->user['username'] ?? 
               "Anonymous";
    }
    
    public function getStatus(): string {
        if (!$this->user) {
            return "Not logged in";
        }
        
        // Ternary operator
        return $this->user['is_online'] ? "Online" : 
               ($this->user['is_away'] ? "Away" : "Offline");
    }
    
    public function formatProfile(): array {
        return [
            'name' => $this->getDisplayName(),
            'status' => $this->getStatus(),
            'email' => $this->user['email'] ?? "No email",
            'phone' => $this->user['phone'] ?? "No phone",
            'admin' => $this->user['is_admin'] ?? false ? "Yes" : "No",
            'member_since' => $this->user['created_at'] ? 
                             date('Y', strtotime($this->user['created_at'])) : 
                             "Unknown"
        ];
    }
}

// Usage
$user = [
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'is_online' => true,
    'is_admin' => true,
    'created_at' => '2020-06-15'
];

$profile = new UserProfile($user);
print_r($profile->formatProfile());

// Array (
//     [name] => john_doe
//     [status] => Online
//     [email] => john@example.com
//     [phone] => No phone
//     [admin] => Yes
//     [member_since] => 2020
// )
?>
```

---

## Next Steps

✅ Understand ternary operator  
→ Learn [null coalescing operator](21-null-coalescing-operator.md)  
→ Study [comparison operators](12-operators-comparison.md)  
→ Master [if/else statements](18-if-statement.md)
