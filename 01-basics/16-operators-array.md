# Operators - Array Operators

## Table of Contents
1. [Overview](#overview)
2. [Union Operator](#union-operator)
3. [Comparison Operators](#comparison-operators)
4. [Practical Examples](#practical-examples)
5. [Common Mistakes](#common-mistakes)

---

## Overview

Array operators are used to combine, compare, and work with arrays.

| Operator | Name | Example | Result |
|----------|------|---------|--------|
| `+` | Union | `$a + $b` | Union of arrays |
| `==` | Equal | `$a == $b` | true if same key-value pairs |
| `===` | Identical | `$a === $b` | true if same key-value pairs and types |
| `!=` | Not Equal | `$a != $b` | true if different |
| `!==` | Not Identical | `$a !== $b` | true if different or different types |

---

## Union Operator

The `+` operator combines two arrays.

### Basic Union

```php
<?php
$array1 = ['a' => 'apple', 'b' => 'banana'];
$array2 = ['b' => 'blackberry', 'c' => 'cherry'];

// Union: array1 + array2
$result = $array1 + $array2;
print_r($result);
// Array (
//   [a] => apple
//   [b] => banana      (from array1, not array2!)
//   [c] => cherry      (added from array2)
// )

// Note: array1 values take precedence!
?>
```

### How Union Works

```php
<?php
// Union keeps first array's values for duplicate keys
$base = [1, 2, 3];           // keys: 0, 1, 2
$append = [4, 5, 6];         // keys: 0, 1, 2

$result = $base + $append;
print_r($result);
// Array (
//   [0] => 1      (from base)
//   [1] => 2      (from base)
//   [2] => 3      (from base)
//   [3] => 5      (added, key 3 doesn't conflict)
// )

// Better for numeric arrays: use array_merge()
$merged = array_merge($base, $append);
print_r($merged);
// Array (
//   [0] => 1
//   [1] => 2
//   [2] => 3
//   [3] => 4
//   [4] => 5
//   [5] => 6
// )
?>
```

### Practical Union

```php
<?php
// Merge configuration arrays
$default_config = [
    'host' => 'localhost',
    'port' => 3306,
    'charset' => 'utf8mb4',
];

$custom_config = [
    'host' => 'db.example.com',
    'timeout' => 30,
];

// Union operator (custom values override defaults)
$config = $custom_config + $default_config;
// Result: host=db.example.com, port=3306, charset=utf8mb4, timeout=30

// Wait! This is backwards. Custom should override defaults!
$config = $default_config + $custom_config;
// This also doesn't work as expected

// Better: use array_merge() which re-indexes numeric keys
$config = array_merge($default_config, $custom_config);
// Result: host=db.example.com, port=3306, charset=utf8mb4, timeout=30
?>
```

---

## Comparison Operators

### Loose Equality (==)

```php
<?php
$array1 = ['a' => 1, 'b' => 2];
$array2 = ['a' => 1, 'b' => 2];
$array3 = ['b' => 2, 'a' => 1];

// Equal: same key-value pairs (order doesn't matter)
$array1 == $array2;  // true
$array1 == $array3;  // true (different order, same content)

// Loose equality with type juggling
$array = ['a' => '1', 'b' => '2'];
$array2 = ['a' => 1, 'b' => 2];
$array == $array2;   // true (values match with type juggling)
?>
```

### Strict Equality (===)

```php
<?php
$array1 = ['a' => 1, 'b' => 2];
$array2 = ['a' => 1, 'b' => 2];
$array3 = ['a' => '1', 'b' => '2'];

// Identical: same key-value pairs AND same types
$array1 === $array2;  // true
$array1 === $array3;  // false (different types: int vs string)

// Check types
$array1 === $array1;  // true (same array)
$array2 === $array1;  // false (different arrays, same content)
?>
```

### Not Equal and Not Identical

```php
<?php
$array1 = ['a' => 1];
$array2 = ['a' => 2];
$array3 = ['b' => 1];

// Not equal
$array1 != $array2;   // true (different values)
$array1 != $array3;   // true (different keys)

// Not identical
$array1 !== $array2;  // true
$array1 !== $array3;  // true

// Different cases for each
$array1 = [];
$array2 = [];
$array1 == $array2;   // true
$array1 === $array2;  // true
?>
```

---

## Practical Examples

### Configuration Merging

```php
<?php
// Default settings
$defaults = [
    'debug' => false,
    'cache' => true,
    'cache_ttl' => 3600,
    'log_level' => 'info',
];

// Development overrides
$dev_config = [
    'debug' => true,
    'cache' => false,
    'log_level' => 'debug',
];

// Merge: dev overrides defaults
$config = array_merge($defaults, $dev_config);
// Result:
// [debug => true, cache => false, cache_ttl => 3600, log_level => debug]
?>
```

### Array Validation

```php
<?php
// Check if arrays are the same
function validateResponse($response, $expected_format) {
    // Check keys and types match
    if ($response === $expected_format) {
        return true;  // Perfect match
    }
    
    // Check same keys exist
    foreach ($expected_format as $key => $value) {
        if (!isset($response[$key])) {
            return false;  // Missing key
        }
        if (gettype($response[$key]) !== gettype($value)) {
            return false;  // Type mismatch
        }
    }
    
    return true;  // Valid
}

$api_response = [
    'status' => 200,
    'message' => 'Success',
    'data' => [],
];

$expected = [
    'status' => 0,
    'message' => '',
    'data' => [],
];

if (validateResponse($api_response, $expected)) {
    echo "Response format valid";
}
?>
```

### Combining User Preferences

```php
<?php
// System defaults
$system_prefs = [
    'theme' => 'light',
    'language' => 'en',
    'font_size' => 'medium',
    'notifications' => true,
    'auto_save' => true,
];

// User preferences
$user_prefs = [
    'theme' => 'dark',
    'font_size' => 'large',
];

// Merge: user prefs override system defaults
$final_prefs = array_merge($system_prefs, $user_prefs);
// Result:
// [theme => dark, language => en, font_size => large, 
//  notifications => true, auto_save => true]
?>
```

### Compare API Responses

```php
<?php
function compareResponses($current, $previous) {
    $changes = [];
    
    // Find what changed
    foreach ($current as $key => $value) {
        if ($value !== ($previous[$key] ?? null)) {
            $changes[$key] = [
                'old' => $previous[$key] ?? null,
                'new' => $value,
            ];
        }
    }
    
    return $changes;
}

$previous = [
    'status' => 'pending',
    'attempts' => 0,
];

$current = [
    'status' => 'processing',
    'attempts' => 1,
];

$changes = compareResponses($current, $previous);
print_r($changes);
// Array (
//   [status] => Array ( [old] => pending [new] => processing )
//   [attempts] => Array ( [old] => 0 [new] => 1 )
// )
?>
```

---

## Common Mistakes

### 1. Confusing Union Operator Behavior

```php
<?php
// ❌ Wrong expectation
$defaults = ['a' => 1, 'b' => 2];
$custom = ['b' => 3, 'c' => 4];

$result = $defaults + $custom;
// Result: ['a' => 1, 'b' => 2, 'c' => 4]
// Expected: ['a' => 1, 'b' => 3, 'c' => 4]
// The defaults take precedence, not custom!

// ✓ Use array_merge instead
$result = array_merge($defaults, $custom);
// Result: ['a' => 1, 'b' => 3, 'c' => 4]
?>
```

### 2. Using == When === Needed

```php
<?php
// ❌ Type juggling causes unexpected matches
$array1 = [1, 2, 3];
$array2 = ['1', '2', '3'];

if ($array1 == $array2) {
    echo "Equal";  // This is true! (type juggling)
}

// ✓ Use strict comparison
if ($array1 === $array2) {
    echo "Identical";  // This is false
}
?>
```

### 3. Key Order with Associative Arrays

```php
<?php
// ❌ Order doesn't matter for ==
$array1 = ['a' => 1, 'b' => 2];
$array2 = ['b' => 2, 'a' => 1];

// These are equal!
if ($array1 == $array2) {
    echo "Arrays are equal";  // Executes
}

// ✓ If order matters, check it separately
function orderMatters($arr1, $arr2) {
    return array_keys($arr1) === array_keys($arr2);
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class ConfigManager {
    private array $defaults;
    private array $config;
    
    public function __construct(array $defaults = []) {
        $this->defaults = $defaults;
        $this->config = $defaults;
    }
    
    public function merge(array $custom): void {
        // Custom values override defaults
        $this->config = array_merge($this->defaults, $custom);
    }
    
    public function get($key, $default = null) {
        return $this->config[$key] ?? $default;
    }
    
    public function getAll(): array {
        return $this->config;
    }
    
    public function compare(array $other): array {
        $differences = [];
        
        $all_keys = array_unique(array_merge(
            array_keys($this->config),
            array_keys($other)
        ));
        
        foreach ($all_keys as $key) {
            $self_val = $this->config[$key] ?? null;
            $other_val = $other[$key] ?? null;
            
            if ($self_val !== $other_val) {
                $differences[$key] = [
                    'current' => $self_val,
                    'other' => $other_val,
                ];
            }
        }
        
        return $differences;
    }
}

// Usage
$defaults = [
    'host' => 'localhost',
    'port' => 3306,
    'charset' => 'utf8mb4',
];

$config = new ConfigManager($defaults);

// Merge custom values
$config->merge([
    'host' => 'db.example.com',
    'timeout' => 30,
]);

echo "Current config:\n";
print_r($config->getAll());

// Compare with another config
$other = [
    'host' => 'db2.example.com',
    'port' => 3307,
];

echo "\nDifferences:\n";
print_r($config->compare($other));
?>
```

---

## Next Steps

✅ Understand array operators  
→ Learn [arrays](9-data-type-array.md)  
→ Study [foreach loops](26-for-each-loop.md)  
→ Master [array functions](../02-basics-study-case/)
