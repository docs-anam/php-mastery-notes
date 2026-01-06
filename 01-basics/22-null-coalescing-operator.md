# Null Coalescing and Null Safe Operators

## Table of Contents
1. [Overview](#overview)
2. [Null Coalescing Operator](#null-coalescing-operator)
3. [Null Coalescing Assignment](#null-coalescing-assignment)
4. [Null Safe Operator](#null-safe-operator)
5. [Comparison with Alternatives](#comparison-with-alternatives)
6. [Practical Examples](#practical-examples)
7. [Common Mistakes](#common-mistakes)

---

## Overview

Null operators provide safe ways to handle missing or null values.

| Operator | Name | Example | Behavior |
|----------|------|---------|----------|
| `??` | Null Coalescing | `$x ?? "default"` | Returns left if not null, else right |
| `??=` | Null Coalescing Assignment | `$x ??= "default"` | Assigns right only if left is null |
| `?->` | Null Safe | `$obj?->method()` | Calls method safely, returns null if object is null |

---

## Null Coalescing Operator

The `??` operator returns the left operand if it exists and is not null; otherwise returns the right operand.

### Basic Usage

```php
<?php
// Simple null check
$name = $_GET['name'] ?? "Guest";

// Won't generate undefined index notice
$email = $_POST['email'] ?? "no-email@example.com";

// Works with variables
$user = $session['user'] ?? null;

// Check 0 and empty string (unlike Elvis operator)
$count = $data['count'] ?? 0;
$text = $data['text'] ?? "";
?>
```

### Comparison with isset()

```php
<?php
// Traditional isset() check
if (isset($data['key'])) {
    $value = $data['key'];
} else {
    $value = "default";
}

// With null coalescing
$value = $data['key'] ?? "default";

// Both prevent undefined index notice
// But ?? specifically checks for null
?>
```

### Avoiding Type Juggling Issues

```php
<?php
// Elvis operator (? :) uses truthiness
$value = "" ?: "default";  // "default" - empty string is falsy
$value = "0" ?: "default"; // "default" - "0" is falsy
$value = 0 ?: "default";   // "default" - 0 is falsy

// Null coalescing only cares about null
$value = "" ?? "default";  // "" - not null
$value = "0" ?? "default"; // "0" - not null
$value = 0 ?? "default";   // 0 - not null
?>
```

---

## Null Coalescing Assignment

The `??=` operator assigns only if the left side is null or undefined.

### Basic Assignment

```php
<?php
$name = null;
$name ??= "Guest";
echo $name;  // "Guest"

// If already set, no change
$name = "John";
$name ??= "Guest";
echo $name;  // "John"

// With undefined variable (creates it)
$age ??= 18;
echo $age;   // 18
?>
```

### In Loops

```php
<?php
$users = [
    ['id' => 1, 'role' => 'admin'],
    ['id' => 2, 'role' => null],
    ['id' => 3],
];

foreach ($users as &$user) {
    $user['role'] ??= 'user';  // Assign default if null or missing
}

print_r($users);
// [0] => role = 'admin'
// [1] => role = 'user' (was null, now set)
// [2] => role = 'user' (was missing, now set)
?>
```

---

## Null Safe Operator

The `?->` operator (PHP 8.0+) safely calls methods on potentially null objects.

### Basic Usage

```php
<?php
// Without null safe operator
$user = getUser();
if ($user !== null) {
    $name = $user->getProfile()->getName();
}

// With null safe operator (shorter!)
$user = getUser();
$name = $user?->getProfile()?->getName();  // Returns null if any step is null
?>
```

### Method Chaining

```php
<?php
class User {
    public ?Profile $profile = null;
}

class Profile {
    public string $name = "John";
}

// Can chain null safe operators
$user = getUserOrNull();
$name = $user?->profile?->name;  // Returns null if user or profile is null

// With methods
$greeting = $user?->getProfile()?->formatName()?->getGreeting();
// Returns null at first null, doesn't continue chain
?>
```

---

## Comparison with Alternatives

### Traditional Approach

```php
<?php
// Verbose nested checks
$name = "Unknown";
if ($user !== null) {
    if ($user->profile !== null) {
        if ($user->profile->name !== null) {
            $name = $user->profile->name;
        }
    }
}
?>
```

### With isset()

```php
<?php
// Still verbose
$name = isset($user->profile->name) ? $user->profile->name : "Unknown";
?>
```

### With Null Safe Operator

```php
<?php
// Clean and modern
$name = $user?->profile?->name ?? "Unknown";
?>
```

---

## Practical Examples

### Array Configuration

```php
<?php
function processConfig($config) {
    $settings = [
        'host' => $config['database']['host'] ?? 'localhost',
        'port' => $config['database']['port'] ?? 3306,
        'user' => $config['database']['user'] ?? 'root',
        'password' => $config['database']['password'] ?? '',
        'charset' => $config['database']['charset'] ?? 'utf8mb4',
    ];
    
    return $settings;
}

$config = processConfig([
    'database' => [
        'host' => 'db.example.com',
        // port not specified - will use default 3306
    ]
]);

print_r($config);
// host => db.example.com
// port => 3306
// user => root
// etc.
?>
```

### API Response Handling

```php
<?php
function extractUserInfo($response) {
    return [
        'id' => $response['user']['id'] ?? null,
        'name' => $response['user']['name'] ?? 'Unknown',
        'email' => $response['user']['contact']['email'] ?? null,
        'phone' => $response['user']['contact']['phone'] ?? null,
        'verified' => $response['user']['verified'] ?? false,
    ];
}

// Handles incomplete API responses gracefully
$userData = extractUserInfo($apiResponse);
?>
```

### Database Query Handling

```php
<?php
class UserRepository {
    public function getUser($id) {
        $result = $this->query("SELECT * FROM users WHERE id = ?", [$id]);
        return $result[0] ?? null;
    }
    
    public function getUserEmail($id) {
        // Get email or return placeholder
        $user = $this->getUser($id);
        return $user?->email ?? 'no-email@example.com';
    }
}
?>
```

### Form Data Handling

```php
<?php
function handleUserForm() {
    $user = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'country' => $_POST['country'] ?? 'USA',
        'subscribe' => $_POST['subscribe'] ?? false,
    ];
    
    // Fill in missing required fields
    $user['name'] ??= 'Anonymous';
    $user['email'] ??= 'noemail@example.com';
    
    return $user;
}
?>
```

### Cache Fallback

```php
<?php
function getUser($id) {
    // Try cache first
    $user = $cache->get("user_{$id}");
    
    // Fall back to database
    $user ??= $database->findUser($id);
    
    // Fall back to default
    $user ??= ['id' => $id, 'name' => 'Unknown'];
    
    return $user;
}
?>
```

---

## Common Mistakes

### 1. Null Coalescing with Function Calls

```php
<?php
// ❌ Function still called even if first operand exists
$value = $cache->get('key') ?? fetchExpensiveData();
// fetchExpensiveData() called only if cache returns null

// ✅ If you want to avoid function call, check explicitly
if ($cached = $cache->get('key')) {
    $value = $cached;
} else {
    $value = fetchExpensiveData();
}

// ✅ Or use short-circuit evaluation
$value = $cache->get('key') && $cache->get('key') || fetchExpensiveData();
// Actually, this is confusing. Better to use if/else
?>
```

### 2. Confusing Null Safe with Null Coalescing

```php
<?php
// These are different!

// Null coalescing (??) - handles null values
$value = $data['key'] ?? "default";

// Null safe (?->) - safely calls on potentially null object
$name = $user?->getProfile()->getName();

// Combining both
$name = $user?->getProfile()?->getName() ?? "Unknown";
?>
```

### 3. Forgetting Chaining

```php
<?php
// ❌ Incomplete null safety
$name = $user?->getProfile()->getName();
// If $user is null, returns null (good)
// But if getProfile() returns null, this will error!

// ✅ Chain properly
$name = $user?->getProfile()?->getName() ?? "Unknown";
// Safe at each step
?>
```

### 4. Assignment in Null Safe

```php
<?php
// ❌ Can't assign through null safe
$user?->profile = new Profile();  // Syntax error!

// ✅ Use traditional check
if ($user !== null) {
    $user->profile = new Profile();
}

// ✅ Or check first
$user = $user ?? new User();
$user->profile = new Profile();
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class APIClient {
    private array $cache = [];
    
    public function getUser($id) {
        // Try cache first
        $user = $this->cache["user_{$id}"] ?? null;
        
        if ($user !== null) {
            return $user;
        }
        
        // Fetch from API
        $response = $this->fetchAPI("/users/{$id}");
        
        // Extract with null coalescing for missing fields
        $user = [
            'id' => $response['data']['id'] ?? null,
            'name' => $response['data']['name'] ?? 'Unknown',
            'email' => $response['data']['contact']['email'] ?? null,
            'phone' => $response['data']['contact']['phone'] ?? null,
            'verified' => $response['data']['verified'] ?? false,
        ];
        
        // Cache it
        $this->cache["user_{$id}"] = $user;
        
        return $user;
    }
    
    public function getUserEmail($id) {
        // Get user, safely access email
        $user = $this->getUser($id);
        
        // Return email or fallback
        return $user['email'] ?? "email-not-found@{$id}.invalid";
    }
    
    private function fetchAPI($endpoint) {
        // Simulate API call
        return [
            'data' => [
                'id' => 1,
                'name' => 'John Doe',
                'contact' => [
                    'email' => 'john@example.com',
                ],
            ]
        ];
    }
}

// Usage
$client = new APIClient();
$email = $client->getUserEmail(123);
echo $email;  // john@example.com
?>
```

---

## Next Steps

✅ Understand null coalescing operators  
→ Learn [ternary operator](20-ternary-operator.md)  
→ Study [null/undefined handling](8-data-null.md)  
→ Master [comparison operators](12-operators-comparison.md)
