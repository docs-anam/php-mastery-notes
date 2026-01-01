# Null Coalescing Operator in PHP

## Overview

The null coalescing operator (??) is a shorthand way to check if a variable exists and is not null. It returns the left operand if it exists and is not null, otherwise returns the right operand. Introduced in PHP 7.0, it's a powerful tool for handling missing or null values safely.

## Basic Null Coalescing

### Simple Usage

```php
<?php
// Basic syntax: value1 ?? value2
$name = null;
$result = $name ?? "Unknown";
echo $result;  // Output: Unknown

// With variables
$name = "John";
$result = $name ?? "Unknown";
echo $result;  // Output: John

// With undefined variables
$undefinedVar = $undefined ?? "Not set";
echo $undefinedVar;  // Output: Not set (No error!)

// Short form of isset()
$value = isset($array['key']) ? $array['key'] : "default";
// Same as
$value = $array['key'] ?? "default";
?>
```

### From User Input

```php
<?php
// Safe access to GET/POST variables
$name = $_GET['name'] ?? "Guest";
$email = $_POST['email'] ?? "";

// Array access
$config = ["debug" => true];
$debug = $config['debug'] ?? false;

// Nested array access
$user = ["profile" => ["name" => "John"]];
$name = $user['profile']['name'] ?? "Unknown";
?>
```

## Chaining Null Coalescing

### Multiple Fallbacks

```php
<?php
// Chain multiple null coalescing operators
$value = $var1 ?? $var2 ?? $var3 ?? "default";

// Practical example
$name = $_POST['name'] ?? $_GET['name'] ?? $_SESSION['name'] ?? "Guest";

// Check multiple potential values
$email = $user['email'] ?? $user['contact_email'] ?? $user['backup_email'] ?? "no-email";

// Priority: input > cookie > session > default
$theme = $_POST['theme'] ?? $_COOKIE['theme'] ?? $_SESSION['theme'] ?? "light";
?>
```

### Multiple Variable Levels

```php
<?php
$config = [
    'database' => [
        'host' => 'localhost',
        'port' => null
    ]
];

// Get port or use default
$port = $config['database']['port'] ?? 3306;
echo $port;  // Output: 3306

// Check if nested key exists safely
$ssl = $config['database']['ssl'] ?? false;
echo $ssl;  // Output: false
?>
```

## Practical Examples

### Form Processing

```php
<?php
// Get form data with defaults
$name = $_POST['name'] ?? "";
$email = $_POST['email'] ?? "";
$age = $_POST['age'] ?? 0;
$newsletter = $_POST['newsletter'] ?? false;

// Process the form
echo "Name: " . $name;
echo "Email: " . $email;
echo "Age: " . $age;
?>
```

### API Response Handling

```php
<?php
// Simulated API response
$response = [
    'status' => 'success',
    'data' => [
        'id' => 123,
        'name' => 'John'
        // 'email' may or may not exist
    ]
];

// Safe access with defaults
$id = $response['data']['id'] ?? 0;
$name = $response['data']['name'] ?? "Unknown";
$email = $response['data']['email'] ?? "no-email@example.com";

echo "ID: $id, Name: $name, Email: $email";
?>
```

### Configuration Defaults

```php
<?php
// User config with system defaults
$userConfig = [
    'theme' => 'dark'
    // 'language' not set
];

$defaultConfig = [
    'theme' => 'light',
    'language' => 'en',
    'timezone' => 'UTC'
];

// Use user config or fall back to defaults
$theme = $userConfig['theme'] ?? $defaultConfig['theme'];
$language = $userConfig['language'] ?? $defaultConfig['language'];
$timezone = $userConfig['timezone'] ?? $defaultConfig['timezone'];

echo "Theme: $theme, Language: $language, Timezone: $timezone";
// Output: Theme: dark, Language: en, Timezone: UTC
?>
```

### User Profile Data

```php
<?php
$user = [
    'username' => 'john_doe',
    'email' => 'john@example.com'
    // 'phone' and 'address' not set
];

// Display user info with safe defaults
echo "Username: " . $user['username'] ?? "Not set";
echo "Email: " . $user['email'] ?? "Not set";
echo "Phone: " . $user['phone'] ?? "Not provided";
echo "Address: " . $user['address'] ?? "Not provided";
?>
```

### Selective Array Values

```php
<?php
$filters = [
    'sort' => 'name',
    'order' => 'asc',
    // 'limit' not set
];

// Get parameters with defaults
$sortBy = $filters['sort'] ?? 'date';
$sortOrder = $filters['order'] ?? 'desc';
$limit = $filters['limit'] ?? 10;

echo "Sort: $sortBy, Order: $sortOrder, Limit: $limit";
// Output: Sort: name, Order: asc, Limit: 10
?>
```

### Conditional String Building

```php
<?php
$product = [
    'name' => 'Laptop',
    'category' => 'Electronics'
    // 'discount' not set
];

$description = "Product: " . $product['name'] ?? 'Unknown';
if (isset($product['discount'])) {
    $description .= " (Discount: " . $product['discount'] . "%)";
}

// Or better, using null coalescing
$discount = $product['discount'] ?? null;
$description = "Product: " . $product['name'] ?? 'Unknown';
if ($discount !== null) {
    $description .= " (Discount: $discount%)";
}

echo $description;
?>
```

## Null Coalescing vs Other Methods

### Comparison with isset()

```php
<?php
$value = null;

// Using isset()
if (isset($value)) {
    $result = $value;
} else {
    $result = "default";
}

// Null coalescing - more concise
$result = $value ?? "default";

// isset() returns false for null
$value = null;
echo isset($value) ? "Set" : "Not set";  // Output: Not set

// Null coalescing considers null as "not set"
echo ($value ?? "default");  // Output: default
?>
```

### Comparison with empty()

```php
<?php
$value = "";  // Empty string

// empty() treats "" as empty
echo empty($value) ? "Empty" : "Not empty";  // Output: Empty

// Null coalescing only checks for null
echo ($value ?? "default");  // Output: "" (empty string, not default)

// For both null AND empty, use empty()
$result = empty($value) ? "default" : $value;

// For only null, use null coalescing
$result = $value ?? "default";
?>
```

### Comparison with ternary

```php
<?php
$name = null;

// Ternary - verbose
$result = isset($name) ? $name : "Unknown";

// Null coalescing - cleaner
$result = $name ?? "Unknown";

// Elvis operator (older, less safe)
$result = $name ?: "Unknown";  // Treats empty strings as "not set"

// Null coalescing is safest and clearest
$result = $name ?? "Unknown";
?>
```

## Null Coalescing Assignment

### Assignment Operator (PHP 7.4+)

```php
<?php
// Null coalescing assignment ??=
$config = [];

// Set only if not already set
$config['debug'] ??= false;
$config['timeout'] ??= 30;

// Equivalent to
$config['debug'] = $config['debug'] ?? false;

echo $config['debug'];      // false
echo $config['timeout'];    // 30

// With existing values
$config['debug'] = true;
$config['debug'] ??= false;
echo $config['debug'];      // true (not changed)
?>
```

## Common Pitfalls

### Null vs Empty String

```php
<?php
$value = "";  // Empty string, not null

// Null coalescing only checks for null
$result = $value ?? "default";
echo $result;  // Output: "" (empty string)

// If you want to handle empty strings
$result = $value ?: "default";  // Falls back on empty string
// or
$result = empty($value) ? "default" : $value;

// To be explicit
$result = ($value === null) ? "default" : $value;
?>
```

### Not Defined vs Null

```php
<?php
// Null coalescing returns the value even if it's false
$defined = false;
$result = $defined ?? "default";
echo $result;  // Output: false (not "default")

// If you need to check for null specifically
$result = ($defined === null) ? "default" : $defined;
echo $result;  // Output: false

// For undefined variables, no error is thrown
$undefined = $this_variable_does_not_exist ?? "default";
echo $undefined;  // Output: default (no error!)
?>
```

### Key Doesn't Exist in Array

```php
<?php
$user = ['name' => 'John'];

// Safe - returns default
$email = $user['email'] ?? "no-email@example.com";
echo $email;  // Output: no-email@example.com

// Without null coalescing - would cause notice
$email = $user['email'];  // PHP Notice: Undefined array key

// Check before access
if (array_key_exists('email', $user)) {
    $email = $user['email'];
}
?>
```

### Chain Order Matters

```php
<?php
$a = null;
$b = "default";
$c = "fallback";

// Left to right evaluation
$result = $a ?? $b ?? $c;
echo $result;  // Output: default

// First non-null value is used
$a = "first";
$result = $a ?? $b ?? $c;
echo $result;  // Output: first
?>
```

## Best Practices

✓ **Use for optional array keys** (like $_GET, $_POST)
✓ **Chain multiple fallbacks** for priority handling
✓ **Prefer ?? over ternary** for null checking
✓ **Don't mix with empty()** - they check different things
✓ **Use ??= for assignments** (PHP 7.4+)
✓ **Be aware of what "not set" means** (null vs undefined vs empty)
✓ **Combine with isset()** for strict checking if needed

## Key Takeaways

✓ **Null coalescing (??)** returns left if not null, else right
✓ **Checks for null AND undefined** (no error on undefined)
✓ **Ignores empty strings** - they're not null
✓ **Ignores false values** - they're not null
✓ **Chainable** for multiple fallbacks
✓ **Cleaner than ternary** with isset()
✓ **Assignment form (??=)** available in PHP 7.4+
✓ **Safe for array access** - no "Undefined key" notices
✓ **Left-to-right evaluation** when chained
✓ **Different from empty()** - only checks for null
