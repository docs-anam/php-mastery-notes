# Operators - Comparison Operators

## Table of Contents
1. [Overview](#overview)
2. [Equality Operators](#equality-operators)
3. [Relational Operators](#relational-operators)
4. [Spaceship Operator](#spaceship-operator)
5. [Type Comparison](#type-comparison)
6. [Practical Examples](#practical-examples)
7. [Common Mistakes](#common-mistakes)

---

## Overview

Comparison operators compare two values and return a boolean result (`true` or `false`).

| Operator | Name | Example | Result |
|----------|------|---------|--------|
| `==` | Loose Equal | `5 == "5"` | `true` |
| `===` | Strict Equal | `5 === "5"` | `false` |
| `!=` | Loose Not Equal | `5 != "5"` | `false` |
| `!==` | Strict Not Equal | `5 !== "5"` | `true` |
| `<>` | Loose Not Equal | `5 <> 3` | `true` |
| `<` | Less Than | `3 < 5` | `true` |
| `>` | Greater Than | `5 > 3` | `true` |
| `<=` | Less Than or Equal | `3 <= 3` | `true` |
| `>=` | Greater Than or Equal | `5 >= 3` | `true` |
| `<=>` | Spaceship | `5 <=> 3` | `1` |

---

## Equality Operators

### Loose Equality (==)

Compares values with type juggling:

```php
<?php
// Same type
5 == 5;           // true
"hello" == "hello"; // true

// Different types - TYPE JUGGLING
5 == "5";         // true (string converted to int)
0 == false;       // true (false is 0)
0 == "hello";     // true (string starting with non-digit is 0)
1 == true;        // true (true is 1)
null == "";       // true
null == 0;        // true
[] == false;      // true (empty array is falsy)

// Practical
if ($user_input == 1) {
    // Matches 1, "1", 1.0, true
}
?>
```

### Strict Equality (===)

Compares both value AND type (NO type juggling):

```php
<?php
// Must match exactly
5 === 5;          // true
"5" === "5";      // true
5 === "5";        // false (int vs string)
0 === false;      // false (int vs bool)
"" === false;     // false (string vs bool)
null === 0;       // false (null vs int)

// Practical
if ($user_id === 1) {
    // Only matches integer 1, not "1" or true
}
?>
```

### Not Equal Operators

```php
<?php
// Loose not equal (!=)
5 != "5";         // false (they're equal with juggling)
5 != 3;           // true (different values)

// Strict not equal (!==)
5 !== "5";        // true (different types)
5 !== 5;          // false (same value and type)

// Alternative <> (loose not equal)
5 <> 3;           // true
5 <> "5";         // false
?>
```

---

## Relational Operators

### Less Than / Greater Than

```php
<?php
// Less than
3 < 5;            // true
5 < 3;            // false
5 < 5;            // false

// Greater than
5 > 3;            // true
3 > 5;            // false
5 > 5;            // false

// Less than or equal
3 <= 5;           // true
5 <= 5;           // true
5 <= 3;           // false

// Greater than or equal
5 >= 3;           // true
5 >= 5;           // true
3 >= 5;           // false
?>
```

### String Comparison

Strings compare lexicographically (alphabetically):

```php
<?php
// Alphabetical order
"apple" < "banana";       // true
"zebra" > "apple";        // true
"apple" == "apple";       // true
"Apple" == "apple";       // true (case-insensitive)
"Apple" === "apple";      // false (different case)

// Numbers in strings
"2" < "10";               // false (string comparison: "2" > "1")
2 < 10;                   // true (numeric comparison)
"2" < "10" + 0;           // true (converted to numeric)
?>
```

---

## Spaceship Operator

The spaceship operator (`<=>`) returns:
- `-1` if left is less than right
- `0` if left equals right
- `1` if left is greater than right

```php
<?php
// Numbers
1 <=> 2;          // -1 (1 is less than 2)
2 <=> 2;          // 0 (equal)
3 <=> 2;          // 1 (3 is greater than 2)

// Strings
"a" <=> "b";      // -1
"b" <=> "b";      // 0
"c" <=> "b";      // 1

// Practical: sorting with custom order
$users = [
    ['id' => 3, 'name' => 'Alice'],
    ['id' => 1, 'name' => 'Bob'],
    ['id' => 2, 'name' => 'Charlie'],
];

usort($users, fn($a, $b) => $a['id'] <=> $b['id']);
// Sorted by id: 1, 2, 3
?>
```

---

## Type Comparison

### Type Juggling Behavior

```php
<?php
// Strings to numbers
"10 apples" == 10;        // true (string starts with 10)
"apples 10" == 0;         // true (doesn't start with digit)

// Boolean conversions
true == 1;                // true
false == 0;               // true
true == "1";              // true
false == "";              // true

// Null conversions
null == 0;                // true
null == "";               // true
null == false;            // true

// Array/Object
[] == false;              // true
[] == null;               // true
new stdClass == false;    // true
?>
```

### Avoiding Type Juggling Issues

```php
<?php
// Problem: loose comparison with unexpected results
if ("0" == 0) {
    echo "This is TRUE!";  // Executes
}

// Solution: use strict comparison
if ("0" === 0) {
    echo "This is FALSE";  // Doesn't execute
}

// Explicit type casting
if ((int)"0" == 0) {
    echo "Now we're intentional";
}
?>
```

---

## Practical Examples

### Validation Checks

```php
<?php
// Check if value exists
$user_id = 0;

if ($user_id) {
    // FALSE: 0 is falsy
}

if ($user_id !== null) {
    // TRUE: 0 is not null
}

// Better approach
if ($user_id > 0) {
    // Only positive IDs
}
?>
```

### User Authentication

```php
<?php
// Simulate database lookup
function authenticate($password_input) {
    $stored_hash = password_hash("mypassword", PASSWORD_BCRYPT);
    
    // Check password
    if (password_verify($password_input, $stored_hash)) {
        return true;  // Correct password
    }
    return false;     // Wrong password
}

// Don't do this - simple string comparison
// if ($input == $stored_hash) { ... }  // INSECURE

// Use hashing instead
?>
```

### Range Validation

```php
<?php
function validateAge($age) {
    // Check type and value
    if (!is_int($age) || $age === null) {
        return false;
    }
    
    // Check range
    if ($age >= 0 && $age <= 150) {
        return true;
    }
    return false;
}

echo validateAge(25) ? "Valid" : "Invalid";  // Valid
echo validateAge(-5) ? "Valid" : "Invalid";  // Invalid
echo validateAge(200) ? "Valid" : "Invalid"; // Invalid
?>
```

### Comparison Chain

```php
<?php
$score = 85;

// Chain comparisons
if ($score >= 90) {
    $grade = "A";
} elseif ($score >= 80) {
    $grade = "B";
} elseif ($score >= 70) {
    $grade = "C";
} elseif ($score >= 60) {
    $grade = "D";
} else {
    $grade = "F";
}

echo "Grade: $grade";  // B
?>
```

### Sorting with Comparison

```php
<?php
$numbers = [3, 1, 4, 1, 5, 9, 2, 6];

// Ascending
sort($numbers);  // [1, 1, 2, 3, 4, 5, 6, 9]

// Descending
rsort($numbers); // [9, 6, 5, 4, 3, 2, 1, 1]

// Custom comparison
$items = [
    ['price' => 100],
    ['price' => 50],
    ['price' => 75],
];

usort($items, function($a, $b) {
    return $a['price'] <=> $b['price'];
});

// Sorted by price: 50, 75, 100
?>
```

---

## Common Mistakes

### 1. Using == Instead of ===

```php
<?php
// ❌ Problematic
if ($status == "success") {
    // May match unintended values
}

// ✅ Better
if ($status === "success") {
    // Only matches exact string
}

// Real example
$user_id = "123abc";  // From database
if ($user_id == 123) {
    // TRUE! String converted to int
    echo "ID matches";  // Executes
}

if ($user_id === 123) {
    // FALSE! Different types
    echo "ID matches";  // Doesn't execute
}
?>
```

### 2. Comparing Strings as Numbers

```php
<?php
// ❌ Wrong
if ("10" < "9") {
    // TRUE! String comparison: "1" < "9"
    echo "10 is less than 9";
}

// ✅ Correct
if (10 < 9) {
    // FALSE! Numeric comparison
    echo "10 is less than 9";  // Doesn't execute
}

// ✅ Or cast explicitly
if ((int)"10" < (int)"9") {
    // FALSE
}
?>
```

### 3. Forgetting Type Juggling in Conditionals

```php
<?php
// ❌ Surprising behavior
$value = "0";
if ($value) {
    echo "Value is truthy";
} else {
    echo "Value is falsy";  // Executes! "0" is falsy
}

// ✅ Better: be explicit
if ($value !== null && $value !== "") {
    echo "Value exists";
}

// ✅ Or use strict comparison
if ($value === "0") {
    echo "Value is the string '0'";
}
?>
```

### 4. Not Considering NULL

```php
<?php
// ❌ Loose comparison with null
$value = null;
if ($value == 0) {
    echo "Value is zero";  // TRUE! null == 0
}

// ✅ Strict comparison
if ($value === 0) {
    echo "Value is zero";  // FALSE
}

if ($value === null) {
    echo "Value is null";  // TRUE
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class PasswordValidator {
    private string $minimum_length = 8;
    
    public function validate(string $password, ?string $confirm = null): array {
        $errors = [];
        
        // Check length
        if (strlen($password) < $this->minimum_length) {
            $errors[] = "Password must be at least $this->minimum_length characters";
        }
        
        // Check not empty
        if ($password === "") {
            $errors[] = "Password cannot be empty";
        }
        
        // Check confirmation
        if ($confirm !== null && $password !== $confirm) {
            $errors[] = "Passwords do not match";
        }
        
        // Check for uppercase
        if ($password === strtolower($password)) {
            $errors[] = "Password must contain uppercase letter";
        }
        
        return [
            'valid' => count($errors) === 0,
            'errors' => $errors
        ];
    }
}

// Test
$validator = new PasswordValidator();

$result = $validator->validate("Weak", "Weak");
var_dump($result);
// Array with errors about length and uppercase

$result = $validator->validate("Strong123", "Strong123");
var_dump($result);
// ['valid' => true, 'errors' => []]
?>
```

---

## Next Steps

✅ Understand comparison operators  
→ Learn [logical operators](13-operators-logical.md)  
→ Study [ternary operator](20-ternary-operator.md)  
→ Master [conditionals](18-if-statement.md)
