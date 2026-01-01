# Array Operators in PHP

## What are Array Operators?

Array operators are used to combine, compare, and manipulate arrays. They provide special syntax for working with multiple arrays together, enabling operations like union, comparison, and validation.

```
Array Union:           $a + $b      (combine arrays)
Equality:              $a == $b     (same key-value pairs)
Identity:              $a === $b    (same order and types)
Inequality:            $a != $b     (different values)
Non-identity:          $a !== $b    (different order/types)
```

## Array Union (+)

Combines two arrays. Duplicate keys from the first array are kept; keys from the second array are added.

```php
<?php
$array1 = ["a" => 1, "b" => 2];
$array2 = ["b" => 3, "c" => 4];

$result = $array1 + $array2;
// Result: ["a" => 1, "b" => 2, "c" => 4]
// Note: "b" => 3 from $array2 is ignored because "b" exists in $array1

// With indexed arrays
$colors1 = [0 => "red", 1 => "blue"];
$colors2 = [0 => "green", 1 => "yellow", 2 => "purple"];

$combined = $colors1 + $colors2;
// Result: [0 => "red", 1 => "blue", 2 => "purple"]
// First array's keys are preserved, second array's new keys added
?>
```

## Array Equality (==)

Checks if two arrays have the same key-value pairs (loose comparison).

```php
<?php
$array1 = ["a" => 1, "b" => 2, "c" => 3];
$array2 = ["a" => 1, "b" => 2, "c" => 3];

echo $array1 == $array2;  // true (same content)

// Order doesn't matter for equality
$array3 = ["c" => 3, "a" => 1, "b" => 2];
echo $array1 == $array3;  // true (same key-value pairs)

// But type matters with == (loose comparison)
$array4 = ["1" => "hello", "2" => "world"];
$array5 = [1 => "hello", 2 => "world"];
echo $array4 == $array5;  // true (1 == "1" in loose comparison)
?>
```

## Array Identity (===)

Checks if two arrays have the same key-value pairs in the same order with the same types (strict comparison).

```php
<?php
$array1 = ["a" => 1, "b" => 2];
$array2 = ["a" => 1, "b" => 2];

echo $array1 === $array2;  // true (exact same)

// Order matters for identity
$array3 = ["b" => 2, "a" => 1];
echo $array1 === $array3;  // false (different order)

// Type matters for identity
$array4 = ["1" => "hello"];
$array5 = [1 => "hello"];
echo $array4 === $array5;  // false (string key vs int key)

// Indexed arrays
$indexed1 = [1, 2, 3];
$indexed2 = [1, 2, 3];
echo $indexed1 === $indexed2;  // true
?>
```

## Array Inequality (!=)

Checks if two arrays are NOT equal (loose comparison).

```php
<?php
$array1 = ["a" => 1, "b" => 2];
$array2 = ["a" => 1, "c" => 3];

echo $array1 != $array2;  // true (different content)

// Alternative syntax
echo $array1 <> $array2;  // true (same as !=)

// Same content means not unequal
$array3 = ["a" => 1, "b" => 2];
echo $array1 != $array3;  // false (they are equal)
?>
```

## Array Non-Identity (!==)

Checks if two arrays are NOT identical (strict comparison).

```php
<?php
$array1 = ["a" => 1, "b" => 2];
$array2 = ["b" => 2, "a" => 1];

echo $array1 !== $array2;  // true (different order)

// Same order and content
$array3 = ["a" => 1, "b" => 2];
echo $array1 !== $array3;  // false (they are identical)

// Type differences
$array4 = ["1" => "hello"];
$array5 = [1 => "hello"];
echo $array4 !== $array5;  // true (different key types)
?>
```

## Array Operators Summary Table

| Operator | Name | Example | Description |
|----------|------|---------|-------------|
| + | Union | $a + $b | Combine arrays (right's new keys) |
| == | Equality | $a == $b | Same key-value pairs (loose) |
| === | Identity | $a === $b | Same key-value pairs in order (strict) |
| != | Inequality | $a != $b | Different key-value pairs (loose) |
| !== | Non-identity | $a !== $b | Different key-value pairs/order (strict) |
| <> | Inequality | $a <> $b | Same as != (loose) |

## Practical Examples

### Merging Configuration Arrays

```php
<?php
// Default configuration
$default_config = [
    "host" => "localhost",
    "port" => 3306,
    "user" => "root",
    "password" => ""
];

// User configuration (overrides defaults)
$user_config = [
    "host" => "example.com",
    "password" => "secret123"
];

// Merge: defaults first, then user overrides
// BUT: user_config takes precedence
$config = $user_config + $default_config;
// Result: host=example.com, port=3306, user=root, password=secret123

// More practical: user config first to override defaults
$config = array_merge($default_config, $user_config);
// Result: host=example.com, port=3306, user=root, password=secret123
?>
```

### Combining User Settings

```php
<?php
$admin_permissions = [
    "create_user" => true,
    "delete_user" => true,
    "edit_post" => true,
    "delete_post" => true,
    "view_reports" => true
];

$moderator_permissions = [
    "edit_post" => true,
    "delete_post" => true,
    "view_reports" => false
];

// Combine permissions (admin takes precedence)
$combined = $admin_permissions + $moderator_permissions;

// Check permissions
if ($combined["delete_user"] ?? false) {
    echo "Can delete users";
}
?>
```

### Comparing User Inputs

```php
<?php
// Original data
$original_user = [
    "name" => "John",
    "email" => "john@example.com",
    "age" => 30
];

// Updated data from form
$updated_user = [
    "name" => "John",
    "email" => "john@example.com",
    "age" => 30
];

// Check if data changed
if ($original_user === $updated_user) {
    echo "No changes made";
} else {
    echo "Data has been modified";
}

// With changes
$updated_user["age"] = 31;
if ($original_user !== $updated_user) {
    echo "Age was changed!";
}
?>
```

### Validating Arrays

```php
<?php
function validateUserData($required_fields, $submitted_data) {
    // Check if all required fields exist and have values
    $valid = true;
    
    foreach ($required_fields as $field) {
        if (!isset($submitted_data[$field]) || empty($submitted_data[$field])) {
            echo "Missing: $field\n";
            $valid = false;
        }
    }
    
    return $valid;
}

$required = ["name", "email", "password"];
$submitted = ["name" => "John", "email" => "john@example.com"];

if (!validateUserData($required, $submitted)) {
    echo "Form validation failed";
}
// Output: Missing: password
// Form validation failed
?>
```

### Building Response Objects

```php
<?php
$base_response = [
    "success" => false,
    "message" => "",
    "data" => null,
    "errors" => [],
    "timestamp" => date("Y-m-d H:i:s")
];

// Success response
$success = [
    "success" => true,
    "message" => "User created",
    "data" => ["id" => 123, "name" => "John"]
];

$response = $success + $base_response;
// Result: success=true, message="User created", data=[...], errors=[], timestamp=...

// Verify structure
if ($response === array_merge($base_response, $success)) {
    echo "Response structure valid";
}
?>
```

## Array vs Equality Comparison

### Using ==

```php
<?php
$user1 = ["id" => 1, "name" => "John"];
$user2 = ["id" => 1, "name" => "John"];

echo $user1 == $user2;   // true (content is same)

// Order doesn't matter
$user3 = ["name" => "John", "id" => 1];
echo $user1 == $user3;   // true (order doesn't matter with ==)
?>
```

### Using ===

```php
<?php
$user1 = ["id" => 1, "name" => "John"];
$user2 = ["id" => 1, "name" => "John"];

echo $user1 === $user2;  // true (exact same)

// Order matters with ===
$user3 = ["name" => "John", "id" => 1];
echo $user1 === $user3;  // false (different order)

// Key type matters
$array1 = [1 => "a", 2 => "b"];
$array2 = ["1" => "a", "2" => "b"];
echo $array1 === $array2;  // false (int vs string keys)
?>
```

## Best Practices for Array Operations

### 1. Use array_merge() for Most Cases

```php
<?php
// Array union (+) keeps first array's keys
$base = ["a" => 1, "b" => 2];
$override = ["b" => 3, "c" => 4];
$result = $base + $override;  // ["a" => 1, "b" => 2, "c" => 4]

// array_merge() lets second array override
$result = array_merge($base, $override);  // ["a" => 1, "b" => 3, "c" => 4]

// For most cases, array_merge() is more intuitive
?>
```

### 2. Use === for Array Validation

```php
<?php
// Always use strict comparison for arrays
// Prevents unexpected type coercion

$expected = [1, 2, 3];
$received = ["1", "2", "3"];

// Wrong: loose comparison
if ($expected == $received) {
    // Executes even though types differ!
}

// Right: strict comparison
if ($expected === $received) {
    // Only executes if exactly same
}
?>
```

### 3. Check Array Structure Before Use

```php
<?php
function processUser($user) {
    // Verify array structure
    $required = ["id", "name", "email"];
    
    foreach ($required as $field) {
        if (!isset($user[$field])) {
            throw new Exception("Missing field: $field");
        }
    }
    
    // Safe to use now
    echo "Processing: " . $user["name"];
}

// Or check array keys
if (array_key_exists("id", $user) && array_key_exists("name", $user)) {
    // Safe to proceed
}
?>
```

## Important Notes

### Array Union Order Matters

```php
<?php
$array1 = ["a" => 1, "b" => 2];
$array2 = ["a" => 9, "c" => 3];

// First array takes precedence
$result = $array1 + $array2;
// Result: ["a" => 1, "b" => 2, "c" => 3]

// Reverse order gives different result
$result = $array2 + $array1;
// Result: ["a" => 9, "c" => 3, "b" => 2]
?>
```

### Numeric Keys in Union

```php
<?php
$arr1 = [0 => "apple", 1 => "banana"];
$arr2 = [0 => "cherry", 1 => "date"];

// Numeric keys are preserved from first array
$result = $arr1 + $arr2;
// Result: [0 => "apple", 1 => "banana"]

// Second array's numeric keys are ignored
// Use array_merge() to combine indexed arrays
$result = array_merge($arr1, $arr2);
// Result: [0 => "apple", 1 => "banana", 2 => "cherry", 3 => "date"]
?>
```

## Common Pitfalls

### Confusing = with ==

```php
<?php
$array1 = [1, 2, 3];
$array2 = [1, 2, 3];

// WRONG: Assignment
if ($array1 = $array2) {  // Assigns $array2 to $array1
    echo "This always executes";
}

// RIGHT: Comparison
if ($array1 == $array2) {  // Compares arrays
    echo "Arrays are equal";
}

// RIGHT: Strict comparison
if ($array1 === $array2) {  // Strict comparison
    echo "Arrays are identical";
}
?>
```

### Array Union Loses Data

```php
<?php
// Be careful with union operator on indexed arrays
$list1 = ["apple", "banana"];        // Keys: 0, 1
$list2 = ["cherry", "date"];         // Keys: 0, 1

$combined = $list1 + $list2;
// Result: ["apple", "banana"]  (list2's keys ignored!)

// Use array_merge() for indexed arrays
$combined = array_merge($list1, $list2);
// Result: ["apple", "banana", "cherry", "date"]
?>
```

### Type Coercion in Comparison

```php
<?php
$strict = [1, 2, 3];
$loose = ["1", "2", "3"];

// Loose comparison allows type conversion
echo $strict == $loose;    // true (values coerced)

// Strict comparison does not
echo $strict === $loose;   // false (different types)

// Always use === for arrays
?>
```

## Key Takeaways

✓ **+ (Union)** combines arrays, first array's keys take precedence
✓ **==** checks equality (same key-value pairs, loose comparison)
✓ **===** checks identity (same key-value pairs AND order and type)
✓ **!=** checks inequality (loose comparison)
✓ **!==** checks non-identity (strict comparison)
✓ **Order matters for ===** but not for ==
✓ **Key type matters for ===** (string vs int keys)
✓ **Use array_merge() usually** instead of + for combining
✓ **Always use === for validation** to avoid type coercion
✓ **Numeric keys behave specially** in union operations
