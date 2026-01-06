# Data Types - Null

## Overview

The NULL data type represents a variable with no value. It is the only value that belongs to the NULL data type.

```php
<?php
$emptyValue = NULL;
var_dump($emptyValue); // Output: NULL
?>
```

## Creating NULL Values

A variable is considered NULL if:

1. It has been set to NULL explicitly
2. It has not been set to any value yet
3. It has been unset()

```php
<?php
// Explicit NULL assignment
$x = NULL;
var_dump($x); // NULL

// Undefined variable (generates notice/warning)
var_dump($y); // NULL (but with warning/notice)

// Using unset()
$z = "value";
unset($z);
// $z is now undefined

// Empty string or 0 is NOT NULL
$empty = "";
var_dump($empty); // string(0) ""
var_dump($empty === NULL); // false
?>
```

## Checking for NULL

### isset()

Returns false if a variable is unset or NULL:

```php
<?php
$var = NULL;
var_dump(isset($var)); // false

$var2 = 0;
var_dump(isset($var2)); // true

$var3 = "";
var_dump(isset($var3)); // true (empty string is set)

// Multiple variables
var_dump(isset($var, $var2)); // true only if all are set
?>
```

### is_null()

Checks if a variable is NULL:

```php
<?php
$x = NULL;
var_dump(is_null($x)); // true

$y = 0;
var_dump(is_null($y)); // false

$z = "";
var_dump(is_null($z)); // false
?>
```

### empty()

Returns true if variable is falsy (includes NULL, but also 0, "", false, etc.):

```php
<?php
$x = NULL;
var_dump(empty($x)); // true

$y = 0;
var_dump(empty($y)); // true

$z = "";
var_dump(empty($z)); // true

$a = "0";
var_dump(empty($a)); // true
?>
```

## Practical Examples

### Database NULL Values

```php
<?php
// NULL often represents missing database data
$userPhone = NULL;

if ($userPhone !== NULL) {
    echo "Phone: " . $userPhone;
} else {
    echo "Phone not provided";
}
?>
```

### Function Return Values

```php
<?php
function findUserById($id) {
    $users = [
        1 => "Alice",
        2 => "Bob"
    ];
    
    return $users[$id] ?? NULL; // Return NULL if not found
}

$user = findUserById(3);

if ($user === NULL) {
    echo "User not found";
} else {
    echo "User: " . $user;
}
?>
```

### Nullsafe Operator (PHP 8.0+)

```php
<?php
class User {
    public $profile = NULL;
}

$user = new User();

// Without nullsafe operator
$city = null;
if ($user !== null && $user->profile !== null) {
    $city = $user->profile->city;
}

// With nullsafe operator (PHP 8.0+)
$city = $user?->profile?->city ?? "Unknown";
?>
```

## NULL vs Other Falsy Values

```php
<?php
$values = [
    "NULL" => NULL,
    "false" => false,
    "0 (int)" => 0,
    "0.0 (float)" => 0.0,
    "empty string" => "",
    "zero string" => "0",
    "empty array" => [],
];

foreach ($values as $name => $value) {
    echo "\n--- $name ---\n";
    echo "isset: " . (isset($value) ? "true" : "false") . "\n";
    echo "empty: " . (empty($value) ? "true" : "false") . "\n";
    echo "is_null: " . (is_null($value) ? "true" : "false") . "\n";
}
?>
```

## Best Practices

✅ **Do:**
- Use `isset()` to check if a variable is set and not NULL
- Use `is_null()` for explicit NULL checks
- Use nullsafe operator in PHP 8.0+ for safe property access
- Use `??` (null coalescing) operator for default values
- Document which values can be NULL in your code

❌ **Don't:**
- Confuse NULL with false, 0, or empty string
- Use `empty()` without understanding what it checks
- Ignore NULL values in calculations or string operations
- Assume undefined variables are safe to use

## Common Pitfalls

### Type Confusion

```php
// ❌ Wrong - confusing NULL with other falsy values
if (!$value) {
    // This handles NULL, false, 0, "", "0", []
}

// ✅ Correct - explicit NULL check
if ($value === NULL) {
    // This only handles NULL
}
```

### Array Key Existence

```php
// ❌ Dangerous - generates notice/warning if key doesn't exist
$name = $user['name'];

// ✅ Safe - checks existence first
$name = isset($user['name']) ? $user['name'] : "Unknown";

// ✅ Modern approach - null coalescing
$name = $user['name'] ?? "Unknown";
```

## Next Steps

- [Data Type - Array](9-data-type-array.md) - Collection data type
- [Null Coalescing Operator](21-null-coalescing-operator.md) - Safe NULL handling
- [Control Structures - If Statement](18-if-statement.md) - Conditional logic
