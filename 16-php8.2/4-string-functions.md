# String Functions Enhancements

## Overview

Learn about the new string manipulation functions introduced in PHP 8.2, which provide more readable and efficient alternatives to traditional string operations.

---

## Table of Contents

1. Overview of String Functions
2. str_contains()
3. str_starts_with()
4. str_ends_with()
5. Performance Comparison
6. Array Functions
7. Implementation Patterns
8. Complete Examples

---

## Overview of String Functions

### Before PHP 8.2

```php
<?php
// Old way: Using strpos() and related functions
$haystack = "Hello World";

// Check if contains
if (strpos($haystack, "World") !== false) {
    echo "Contains";
}

// Check if starts with
if (strpos($haystack, "Hello") === 0) {
    echo "Starts with";
}

// Check if ends with
if (substr($haystack, -5) === "World") {
    echo "Ends with";
}

// Problems:
// ❌ Verbose
// ❌ Error-prone (need !== false, === 0)
// ❌ Easy to forget position checks
// ❌ Less readable intent
```

### PHP 8.2 Solution

```php
<?php
$haystack = "Hello World";

// New way: Clear, intentional functions
if (str_contains($haystack, "World")) {
    echo "Contains";
}

if (str_starts_with($haystack, "Hello")) {
    echo "Starts with";
}

if (str_ends_with($haystack, "World")) {
    echo "Ends with";
}

// Benefits:
// ✓ Clear intent
// ✓ Readable code
// ✓ Returns boolean
// ✓ Safer (no type confusion)
// ✓ More efficient
```

---

## str_contains()

### Basic Usage

```php
<?php
// Syntax: str_contains(string $haystack, string $needle): bool

// Simple check
if (str_contains("Hello World", "World")) {
    echo "Found!";
}

// Case-sensitive
str_contains("Hello", "hello");  // false
str_contains("Hello", "Hello");  // true

// Empty string always contained
str_contains("anything", "");    // true

// Search in variables
$email = "john@example.com";
if (str_contains($email, "@")) {
    echo "Valid email format";
}
```

### Real-world Examples

```php
<?php
// Email validation
function isValidEmail(string $email): bool
{
    return str_contains($email, "@") && str_contains($email, ".");
}

// URL validation
function isInternalLink(string $url): bool
{
    return !str_contains($url, "http") && !str_contains($url, "://");
}

// Content filtering
function isSafeContent(string $content): bool
{
    $dangerous = ['<script', 'javascript:', 'onclick'];

    foreach ($dangerous as $keyword) {
        if (str_contains(strtolower($content), $keyword)) {
            return false;
        }
    }

    return true;
}

// API endpoint detection
function isApiRoute(string $path): bool
{
    return str_contains($path, "/api/");
}

// File type checking
function isImageFile(string $filename): bool
{
    $imageExtensions = ['.jpg', '.png', '.gif', '.webp'];

    foreach ($imageExtensions as $ext) {
        if (str_ends_with(strtolower($filename), $ext)) {
            return true;
        }
    }

    return false;
}
```

---

## str_starts_with()

### Basic Usage

```php
<?php
// Syntax: str_starts_with(string $haystack, string $needle): bool

// Simple check
if (str_starts_with("Hello World", "Hello")) {
    echo "Starts correctly";
}

// Case-sensitive
str_starts_with("Hello", "hello");  // false
str_starts_with("Hello", "Hello");  // true

// Empty prefix always matches
str_starts_with("anything", "");    // true

// Position checking
str_starts_with("Hello World", "World");  // false
```

### Practical Examples

```php
<?php
// URL routing
function getRouteHandler(string $path): ?callable
{
    if (str_starts_with($path, "/api/")) {
        return 'handleApi';
    } elseif (str_starts_with($path, "/admin/")) {
        return 'handleAdmin';
    } elseif (str_starts_with($path, "/user/")) {
        return 'handleUserRoute';
    }

    return null;
}

// Request method checking
function isReadOperation(string $method): bool
{
    return str_starts_with($method, "GET");
}

// Log level filtering
function isErrorLog(string $line): bool
{
    return str_starts_with($line, "[ERROR]") ||
           str_starts_with($line, "[FATAL]") ||
           str_starts_with($line, "[WARNING]");
}

// Class namespace checking
function isFrameworkClass(string $className): bool
{
    return str_starts_with($className, "Framework\\") ||
           str_starts_with($className, "App\\");
}

// Variable prefix detection
function isInternalVariable(string $varName): bool
{
    return str_starts_with($varName, "_");
}

// Comment detection
function isCommentLine(string $line): bool
{
    $trimmed = ltrim($line);
    return str_starts_with($trimmed, "//") ||
           str_starts_with($trimmed, "#") ||
           str_starts_with($trimmed, "/*");
}
```

---

## str_ends_with()

### Basic Usage

```php
<?php
// Syntax: str_ends_with(string $haystack, string $needle): bool

// Simple check
if (str_ends_with("Hello World", "World")) {
    echo "Ends correctly";
}

// Case-sensitive
str_ends_with("Hello", "hello");   // false
str_ends_with("Hello", "Hello");   // true

// Empty suffix always matches
str_ends_with("anything", "");     // true

// File extension checking
str_ends_with("image.jpg", ".jpg");  // true
```

### Practical Examples

```php
<?php
// File type validation
function isPhpFile(string $filename): bool
{
    return str_ends_with(strtolower($filename), ".php");
}

function isImageFile(string $filename): bool
{
    $extensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg'];
    $lower = strtolower($filename);

    foreach ($extensions as $ext) {
        if (str_ends_with($lower, $ext)) {
            return true;
        }
    }

    return false;
}

// Namespace checking
function isTestClass(string $className): bool
{
    return str_ends_with($className, "Test");
}

// HTTP status codes
function isSuccessResponse(string $response): bool
{
    return str_ends_with($response, "200 OK") ||
           str_ends_with($response, "201 Created") ||
           str_ends_with($response, "204 No Content");
}

// Configuration validation
function isJsonConfig(string $filename): bool
{
    return str_ends_with($filename, ".json");
}

// URL validation
function isSecureConnection(string $url): bool
{
    return str_starts_with($url, "https://");
}

// Command detection
function isShellCommand(string $input): bool
{
    return str_starts_with(ltrim($input), "$") ||
           str_ends_with($input, ";") ||
           str_contains($input, "|");
}

// Cache key patterns
function isExpiredCacheKey(string $key): bool
{
    return str_ends_with($key, "_expired");
}
```

---

## Performance Comparison

### Benchmarking

```php
<?php
// Performance comparison: Old vs New

echo "=== str_contains() Performance ===\n";

$iterations = 1000000;
$haystack = "The quick brown fox jumps over the lazy dog";

// Old method using strpos()
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    strpos($haystack, "fox") !== false;
}
$oldTime = microtime(true) - $start;
echo "strpos() method: {$oldTime}s\n";

// New method using str_contains()
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    str_contains($haystack, "fox");
}
$newTime = microtime(true) - $start;
echo "str_contains() method: {$newTime}s\n";

echo "Improvement: " . round(($oldTime / $newTime - 1) * 100, 2) . "%\n\n";

// Similar benchmarks for str_starts_with() and str_ends_with()
echo "=== str_starts_with() Performance ===\n";

// Old method
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    strpos($haystack, "The") === 0;
}
$oldTime = microtime(true) - $start;
echo "strpos() method: {$oldTime}s\n";

// New method
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    str_starts_with($haystack, "The");
}
$newTime = microtime(true) - $start;
echo "str_starts_with() method: {$newTime}s\n";

echo "Improvement: " . round(($oldTime / $newTime - 1) * 100, 2) . "%\n";
```

### Why str_* Functions are Faster

```
1. Purpose-built: No need to check return value
2. Direct comparison: Optimized C implementation
3. No ambiguity: Always returns boolean
4. Better caching: CPU-friendly operations
5. Simpler logic: Less overhead than strpos()
```

---

## Array String Functions

### str_contains() with Arrays

```php
<?php
// Check if any array element is contained
function containsAny(string $haystack, array $needles): bool
{
    foreach ($needles as $needle) {
        if (str_contains($haystack, $needle)) {
            return true;
        }
    }

    return false;
}

// Check if all needles are contained
function containsAll(string $haystack, array $needles): bool
{
    foreach ($needles as $needle) {
        if (!str_contains($haystack, $needle)) {
            return false;
        }
    }

    return true;
}

// Usage
$content = "Welcome to our amazing PHP tutorial";
$keywords = ["PHP", "tutorial"];
$badWords = ["bad", "evil"];

echo containsAll($content, $keywords) ? "Good" : "Bad";  // Good
echo containsAny($content, $badWords) ? "Found" : "Safe"; // Safe
```

### Filter Functions

```php
<?php
// Filter array by string criteria
function filterByPrefix(array $items, string $prefix): array
{
    return array_filter($items, fn($item) => str_starts_with($item, $prefix));
}

function filterBySuffix(array $items, string $suffix): array
{
    return array_filter($items, fn($item) => str_ends_with($item, $suffix));
}

function filterByContains(array $items, string $needle): array
{
    return array_filter($items, fn($item) => str_contains($item, $needle));
}

// Usage
$files = [
    "config.php",
    "index.html",
    "style.css",
    "script.js",
    "database.php",
];

$phpFiles = filterBySuffix($files, ".php");
// Result: ["config.php", "database.php"]

$apiFiles = filterByPrefix($files, "api_");
$phpConfigs = filterByContains($files, "config");
```

---

## Implementation Patterns

### Validation Class

```php
<?php
class StringValidator
{
    public static function isValidEmail(string $email): bool
    {
        return str_contains($email, "@") &&
               str_contains($email, ".") &&
               !str_contains($email, " ") &&
               str_starts_with($email, "mail") === false;
    }

    public static function isValidUrl(string $url): bool
    {
        return str_starts_with($url, "http://") ||
               str_starts_with($url, "https://");
    }

    public static function isValidPhoneNumber(string $phone): bool
    {
        return str_starts_with($phone, "+") &&
               strlen($phone) >= 10 &&
               !str_contains($phone, " ");
    }

    public static function isValidUsername(string $username): bool
    {
        return strlen($username) >= 3 &&
               !str_starts_with($username, "_") &&
               !str_contains($username, " ");
    }
}

// Usage
echo StringValidator::isValidEmail("john@example.com") ? "Valid" : "Invalid";
echo StringValidator::isValidUrl("https://example.com") ? "Valid" : "Invalid";
```

### Router Implementation

```php
<?php
class SimpleRouter
{
    private array $routes = [];

    public function register(string $pattern, callable $handler): void
    {
        $this->routes[$pattern] = $handler;
    }

    public function dispatch(string $uri): mixed
    {
        // Check exact match first
        if (isset($this->routes[$uri])) {
            return call_user_func($this->routes[$uri]);
        }

        // Check prefix patterns
        foreach ($this->routes as $pattern => $handler) {
            if (str_starts_with($pattern, "/api/") &&
                str_starts_with($uri, "/api/")) {
                if (str_contains($pattern, "{id}")) {
                    return call_user_func($handler, $uri);
                }
            }
        }

        return null;
    }
}

// Usage
$router = new SimpleRouter();
$router->register("/api/users", fn() => "Users API");
$router->register("/admin/", fn() => "Admin Panel");
$router->register("/public/", fn() => "Public Content");
```

---

## Complete Examples

### Full Validation System

```php
<?php
declare(strict_types=1);

namespace App\Validation;

class StringValidator
{
    private array $errors = [];

    public function validate(string $value, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $rule => $config) {
            if (!$this->applyRule($value, $rule, $config)) {
                return false;
            }
        }

        return true;
    }

    private function applyRule(string $value, string $rule, mixed $config): bool
    {
        return match ($rule) {
            'required' => $this->validateRequired($value),
            'email' => $this->validateEmail($value),
            'url' => $this->validateUrl($value),
            'startsWith' => str_starts_with($value, $config),
            'endsWith' => str_ends_with($value, $config),
            'contains' => str_contains($value, $config),
            'minLength' => strlen($value) >= $config,
            'maxLength' => strlen($value) <= $config,
            default => true,
        };
    }

    private function validateRequired(string $value): bool
    {
        return !empty(trim($value));
    }

    private function validateEmail(string $value): bool
    {
        return str_contains($value, "@") &&
               str_contains($value, ".") &&
               filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validateUrl(string $value): bool
    {
        return (str_starts_with($value, "http://") ||
                str_starts_with($value, "https://")) &&
               filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

// Usage
$validator = new StringValidator();

$isValid = $validator->validate("john@example.com", [
    'required' => true,
    'email' => true,
]);

echo $isValid ? "Valid" : "Invalid";
```

---

## See Also

- [PHP 8.2 Overview](0-php8.2-overview.md)
- [Readonly Classes](2-readonly-classes.md)
- [First-Class Callables](5-first-class-callables.md)
