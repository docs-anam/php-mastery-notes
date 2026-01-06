# Null-Safe Arrays (Array Key Access)

## Overview

Learn about null-safe array access in PHP 8.2, which provides a safer way to access array elements with null checking and prevents undefined offset warnings.

---

## Table of Contents

1. What is Null-Safe Array Access
2. Basic Syntax
3. Array Access Patterns
4. Null Coalescing vs Null-Safe
5. Chaining with Methods
6. Practical Applications
7. Type Safety
8. Complete Examples

---

## What is Null-Safe Array Access

### Purpose

```php
<?php
// Before PHP 8.2: Handling null arrays
$user = null;
$email = null;

// Old way: Multiple checks needed
if ($user !== null && isset($user['email'])) {
    $email = $user['email'];
}

// Problems:
// ❌ Verbose
// ❌ Easy to miss null checks
// ❌ Warnings on undefined offsets
// ❌ Multiple nested conditions

// PHP 8.2: Null-safe array access
$email = $user?['email'];  // Safe, returns null if $user is null

// Benefits:
// ✓ Clear, concise syntax
// ✓ Automatic null handling
// ✓ No undefined offset warnings
// ✓ Works with chains
```

### Key Features

```php
<?php
// Null-safe array access operator: ?[]

$array = null;
$result = $array?['key'];  // null (no error)

$array = ['key' => 'value'];
$result = $array?['key'];  // 'value'

// Chaining null-safe operations
$data = $result?['nested']?['deeper'];  // null-safe at each level

// With function calls
$user = getUser();  // might return null
$email = $user?['profile']?['email'];   // null-safe chain
```

---

## Basic Syntax

### Simple Null-Safe Access

```php
<?php
// Syntax: $variable?['key']

// Example 1: Direct variable
$user = ['name' => 'John', 'email' => 'john@example.com'];
$name = $user?['name'];      // 'John'
$phone = $user?['phone'];    // null (key doesn't exist, no warning)

// Example 2: Null variable
$user = null;
$name = $user?['name'];      // null (no error)

// Example 3: Null-safe in assignment
$data = null;
$value = $data?['key'] ?? 'default';  // 'default'
```

### With Numeric Keys

```php
<?php
// Works with numeric indices
$items = [1, 2, 3, 4, 5];
$first = $items?[0];         // 1
$tenth = $items?[10];        // null (no warning)

// Null array
$items = null;
$first = $items?[0];         // null

// Dynamic indices
$index = 2;
$item = $items?[$index];     // Works with variables
```

---

## Nested Null-Safe Access

### Chaining Operations

```php
<?php
// Multiple levels of null-safe access
$data = [
    'user' => [
        'profile' => [
            'email' => 'john@example.com'
        ]
    ]
];

// Null-safe chain
$email = $data?['user']?['profile']?['email'];
// Result: 'john@example.com'

// When null at any level
$data = null;
$email = $data?['user']?['profile']?['email'];
// Result: null (stops at first null, no errors)

$data = ['user' => null];
$email = $data?['user']?['profile']?['email'];
// Result: null (null at 'user' level)

$data = ['user' => ['profile' => null]];
$email = $data?['user']?['profile']?['email'];
// Result: null (null at 'profile' level)
```

### Complex Chains

```php
<?php
// Combining with method calls
$response = null;
$userId = $response?['data']?['user']?['id'];

// With array and method chaining (if available)
class Config {
    private array $data = [];
    
    public function get(string $key): mixed {
        return $this->data[$key] ?? null;
    }
}

$config = null;
$value = $config?->get('key');  // Null-safe method call
```

---

## Null Coalescing Comparison

### Null-Safe vs Null Coalescing

```php
<?php
// Null-safe array access: ?[]
// Null coalescing operator: ??
// Null-safe property access: ?->
// Null-safe method call: ?->method()

// Difference
$array = null;
$value1 = $array?['key'];        // null (null-safe)
$value2 = $array['key'] ?? null;  // Error! (null coalescing after error)

// Combine them
$array = null;
$value = $array?['key'] ?? 'default';  // 'default'

$array = ['key' => 'value'];
$value = $array?['key'] ?? 'default';  // 'value'

// Without null-safe (requires checks)
$value = ($array !== null && isset($array['key'])) ? $array['key'] : 'default';

// With null-safe (cleaner)
$value = $array?['key'] ?? 'default';
```

### Error Prevention

```php
<?php
// Without null-safe (generates warnings)
$user = null;
// $name = $user['name'];  // Error: Trying to access array offset on null

// With null-safe (safe)
$name = $user?['name'];  // null, no error

// Without null-safe (undefined offset warning)
$array = ['name' => 'John'];
// $age = $array['age'];  // Warning: Undefined array key "age"

// With null-safe (no warning)
$age = $array?['age'];  // null, no warning
```

---

## Practical Applications

### API Response Handling

```php
<?php
class ApiClient
{
    public function fetchUser(int $id): ?array
    {
        // Fetch from API, might return null
        $response = $this->makeRequest("/users/$id");
        return $response?['data'];
    }

    public function getUserEmail(int $id): ?string
    {
        $response = $this->makeRequest("/users/$id");
        return $response?['data']?['user']?['email'];
    }

    public function getUserProfile(int $id): array
    {
        $response = $this->makeRequest("/users/$id");
        
        return [
            'email' => $response?['data']?['email'],
            'name' => $response?['data']?['name'],
            'avatar' => $response?['data']?['avatar']?['url'],
            'role' => $response?['data']?['role'] ?? 'user',
        ];
    }

    private function makeRequest(string $endpoint): ?array
    {
        // Make HTTP request
        return null;  // Simulate
    }
}

// Usage
$client = new ApiClient();
$email = $client->getUserEmail(1);  // null, string, or error handled
```

### Configuration Access

```php
<?php
class Config
{
    private static ?array $config = null;

    public static function load(array $config): void
    {
        self::$config = $config;
    }

    public static function get(string $key): mixed
    {
        return self::$config?[$key];
    }

    public static function getNestedValue(string ...$keys): mixed
    {
        $value = self::$config;
        
        foreach ($keys as $key) {
            $value = $value?[$key];
            if ($value === null) {
                break;
            }
        }
        
        return $value;
    }
}

// Usage
Config::load([
    'database' => [
        'host' => 'localhost',
        'port' => 5432,
        'credentials' => [
            'user' => 'admin',
            'pass' => 'secret'
        ]
    ]
]);

$host = Config::get('database');           // Array
$dbUser = Config::getNestedValue('database', 'credentials', 'user');  // 'admin'
$missing = Config::getNestedValue('cache', 'redis', 'host');  // null
```

### Form Data Processing

```php
<?php
class FormProcessor
{
    public function processUserForm(?array $data): array
    {
        // Extract values safely
        return [
            'name' => $data?['name'] ?? '',
            'email' => $data?['email'] ?? '',
            'phone' => $data?['contact']?['phone'] ?? '',
            'country' => $data?['address']?['country'] ?? 'Unknown',
            'city' => $data?['address']?['city'] ?? 'Unknown',
        ];
    }

    public function validateForm(?array $data): array
    {
        $errors = [];
        
        if (empty($data?['name'])) {
            $errors['name'] = 'Name is required';
        }
        
        if (empty($data?['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        }
        
        if (!empty($data?['phone']) && !$this->isValidPhone($data['phone'])) {
            $errors['phone'] = 'Invalid phone number';
        }
        
        return $errors;
    }

    private function isValidPhone(string $phone): bool
    {
        return preg_match('/^[0-9\-\+\(\)\s]{10,}$/', $phone) === 1;
    }
}

// Usage
$formData = $_POST;  // might be empty/null
$processor = new FormProcessor();

$errors = $processor->validateForm($formData);
if (empty($errors)) {
    $processed = $processor->processUserForm($formData);
}
```

### JSON Data Navigation

```php
<?php
class JsonNavigator
{
    private ?array $data = null;

    public function load(string $json): self
    {
        $this->data = json_decode($json, true);
        return $this;
    }

    public function getValue(string ...$path): mixed
    {
        $current = $this->data;
        
        foreach ($path as $key) {
            $current = $current?[$key];
        }
        
        return $current;
    }

    public function getAsString(string ...$path): string
    {
        return (string)($this->getValue(...$path) ?? '');
    }

    public function getAsArray(string ...$path): array
    {
        return (array)($this->getValue(...$path) ?? []);
    }

    public function getAsInt(string ...$path): int
    {
        return (int)($this->getValue(...$path) ?? 0);
    }
}

// Usage
$json = '{
    "user": {
        "id": 1,
        "profile": {
            "name": "John",
            "email": "john@example.com"
        }
    }
}';

$navigator = new JsonNavigator();
$navigator->load($json);

$name = $navigator->getValue('user', 'profile', 'name');     // 'John'
$missing = $navigator->getValue('user', 'avatar', 'url');   // null
$id = $navigator->getAsInt('user', 'id');                   // 1
```

---

## Type Safety

### With Type Declarations

```php
<?php
class DataHandler
{
    private ?array $cache = null;

    public function getValue(string $key): mixed
    {
        return $this->cache?[$key];  // mixed return
    }

    public function getRequiredValue(string $key): string
    {
        $value = $this->cache?[$key];
        
        if (!is_string($value)) {
            throw new InvalidArgumentException("Key '$key' is not a string");
        }
        
        return $value;
    }

    public function getIntValue(string $key): ?int
    {
        $value = $this->cache?[$key];
        return is_int($value) ? $value : null;
    }
}
```

---

## Complete Examples

### Full Application

```php
<?php
declare(strict_types=1);

namespace App\Data;

class SafeDataAccessor
{
    private ?array $data = null;

    public function __construct(?array $data = null)
    {
        $this->data = $data;
    }

    /**
     * Safe nested value access
     */
    public function get(string ...$path): mixed
    {
        $current = $this->data;
        
        foreach ($path as $key) {
            if (!is_array($current)) {
                return null;
            }
            $current = $current[$key] ?? null;
        }
        
        return $current;
    }

    /**
     * Safe value with default
     */
    public function getString(string $default, string ...$path): string
    {
        return (string)($this->get(...$path) ?? $default);
    }

    public function getInt(int $default, string ...$path): int
    {
        $value = $this->get(...$path);
        return is_int($value) ? $value : $default;
    }

    /**
     * Safe array access
     */
    public function getArray(string ...$path): array
    {
        $value = $this->get(...$path);
        return is_array($value) ? $value : [];
    }

    /**
     * Check if path exists
     */
    public function has(string ...$path): bool
    {
        return $this->get(...$path) !== null;
    }

    /**
     * Get multiple values
     */
    public function getMultiple(array $keys): array
    {
        $results = [];
        
        foreach ($keys as $key => $path) {
            if (is_array($path)) {
                $results[$key] = $this->get(...$path);
            } else {
                $results[$key] = $this->get($path);
            }
        }
        
        return $results;
    }
}

// Usage
$apiResponse = [
    'status' => 'success',
    'data' => [
        'user' => [
            'id' => 1,
            'email' => 'john@example.com',
            'profile' => [
                'name' => 'John Doe',
                'avatar' => [
                    'url' => 'https://example.com/avatar.jpg',
                    'size' => 'medium'
                ]
            ]
        ]
    ]
];

$accessor = new SafeDataAccessor($apiResponse);

// Safe access
$email = $accessor->getString('unknown', 'data', 'user', 'email');
// Result: 'john@example.com'

$missing = $accessor->getString('N/A', 'data', 'user', 'phone');
// Result: 'N/A'

$avatarUrl = $accessor->getString('', 'data', 'user', 'profile', 'avatar', 'url');
// Result: 'https://example.com/avatar.jpg'

// With null input
$accessor2 = new SafeDataAccessor(null);
$value = $accessor2->get('any', 'path');
// Result: null (safe)

// Multiple values
$values = $accessor->getMultiple([
    'email' => ['data', 'user', 'email'],
    'name' => ['data', 'user', 'profile', 'name'],
    'city' => ['data', 'user', 'address', 'city'],
]);

// Check existence
if ($accessor->has('data', 'user', 'profile', 'avatar')) {
    echo "User has avatar";
}
```

---

## See Also

- [PHP 8.2 Overview](0-php8.2-overview.md)
- [String Functions](4-string-functions.md)
- [DNF Types](3-dnf-types.md)
