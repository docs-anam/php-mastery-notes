# PHP 8.3 Overview

## Overview

Learn about PHP 8.3, released in November 2023, featuring typed constants, the #[Override] attribute, JSON validation, and significant performance improvements.

---

## Table of Contents

1. What's New in PHP 8.3
2. Release Information
3. Major Features Summary
4. Type System Enhancements
5. Performance Improvements
6. Deprecations and Removals
7. Feature Comparison
8. Migration Guide
9. System Requirements
10. Learning Path
11. Key Takeaways

---

## What's New in PHP 8.3

### Release Timeline

```php
<?php
// PHP 8.3 Timeline
// Released: November 23, 2023
// Stable until: November 25, 2024 (12 months)
// Security fixes until: November 23, 2025 (24 months)

// Version information
echo PHP_VERSION;        // 8.3.0+
echo phpversion('json'); // Check extension versions

// Feature availability check
if (PHP_VERSION_ID >= 80300) {
    // PHP 8.3+ features available
    // - Typed constants
    // - #[Override] attribute
    // - json_validate()
    // - Random improvements
}
```

### Key Statistics

```
PHP 8.3 Features:
- 10+ major features
- 50+ smaller improvements
- 8-15% performance gain
- Multiple deprecations removed
- Better type safety
- Enhanced standard library
```

---

## Major Features Summary

### 1. Typed Class Constants

```php
<?php
// NEW: Constants can now have explicit types
class DatabaseConfig
{
    public const string HOST = 'localhost';
    public const int PORT = 5432;
    public const bool ENABLED = true;
    public const array ALLOWED_HOSTS = ['localhost', '127.0.0.1'];
    private const string PASSWORD = 'secret';
}

// Type-safe constant access
$host = DatabaseConfig::HOST;      // string type enforced
$port = DatabaseConfig::PORT;      // int type enforced

// Benefits:
// ✓ Type checking
// ✓ IDE auto-completion
// ✓ Runtime safety
// ✓ Documentation clarity
```

### 2. #[Override] Attribute

```php
<?php
// NEW: Verify method overrides
class Parent
{
    public function process(): void {}
}

class Child extends Parent
{
    // Ensures this method actually overrides parent
    #[Override]
    public function process(): void
    {
        parent::process();
        // Custom logic
    }
}

// Benefits:
// ✓ Prevent accidental signature mismatches
// ✓ Catch refactoring errors
// ✓ Better code intent
```

### 3. json_validate() Function

```php
<?php
// NEW: Validate JSON without decoding
$json = '{"name":"John","email":"john@example.com"}';

// Validate without performance cost of full decode
if (json_validate($json)) {
    echo "Valid JSON";
    $data = json_decode($json, true);
}

// Benefits:
// ✓ Faster validation
// ✓ No unnecessary decoding
// ✓ Cleaner code
```

### 4. Random Class Improvements

```php
<?php
// IMPROVED: Better randomization API
$random = new Random\Randomizer();

// Secure random generation
$randomInt = $random->nextInt(100);              // 0-99
$randomFloat = $random->getFloat(0, 1);         // 0.0-1.0
$randomBytes = $random->getBytes(16);           // 16 random bytes

// Benefits:
// ✓ Better algorithm choices
// ✓ More secure defaults
// ✓ Consistent API
```

### 5. Additional Features

```php
<?php
// readonly properties can't be cloned
class ImmutableData
{
    public readonly string $id;
    // Automatic prevention of unintended cloning
}

// Digging into enums improvements
enum Status: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    
    public function isActive(): bool
    {
        return $this === Status::ACTIVE;
    }
}

// Array unpacking improvements
$array = ['b' => 2, 'a' => 1];
$unpacked = ['first' => 1, ...$array];
// Order preserved properly

// New array functions
$array = [1, 2, 3, 4, 5];
array_is_list($array);    // true - is sequential 0-indexed array
```

---

## Type System Enhancements

### Typed Constants

```php
<?php
// Type hints on class constants
class AppConfig
{
    // Public typed constant
    public const string APP_NAME = 'MyApp';
    
    // Protected typed constant
    protected const int MAX_RETRIES = 3;
    
    // Private typed constant
    private const array ALLOWED_HOSTS = ['localhost'];
    
    // Enum constant
    private const Status STATUS = Status::ACTIVE;
}

// Type validation at assignment
class UserConfig extends AppConfig
{
    // Must match parent type
    public const string APP_NAME = 'UserApp';
    // public const int APP_NAME = 123;  // Error!
}
```

### Intersection Type Improvements

```php
<?php
// Better support for complex type combinations
interface Logger {}
interface Formatter {}

// More reliable intersection handling
function process(Logger & Formatter $handler): void
{
    $handler->log("Processing");
    $handler->format("data");
}

// DNF types even more stable
function handle(
    (Logger & Formatter) | (Logger & Flushable) | string $input
): void {
    // Better type narrowing
}
```

---

## Performance Improvements

### Benchmark Results

```
PHP 8.3 Performance (vs PHP 8.2):
┌─────────────────────────┬─────────┬──────────┐
│ Operation               │ 8.2 ref │ 8.3 gain │
├─────────────────────────┼─────────┼──────────┤
│ General operations      │ 100%    │ +10%     │
│ Array operations        │ 100%    │ +12%     │
│ String operations       │ 100%    │ +8%      │
│ JSON handling           │ 100%    │ +15%     │
│ Class instantiation     │ 100%    │ +9%      │
│ Method calls            │ 100%    │ +8%      │
│ Total average           │ 100%    │ +10.3%   │
└─────────────────────────┴─────────┴──────────┘
```

### Where Performance Improves

```php
<?php
// JSON operations (up to 15% faster)
$json = json_encode($largeArray);
$valid = json_validate($json);  // Very fast
$decoded = json_decode($json, true);

// Array handling (12% faster)
$array = [];
for ($i = 0; $i < 100000; $i++) {
    $array[] = ['id' => $i, 'name' => "Item $i"];
}

// Enum handling (8% faster)
function processStatus(Status $status): void {}
foreach ($statuses as $status) {
    processStatus($status);
}
```

---

## Deprecations and Removals

### Features Deprecated in PHP 8.3

```php
<?php
// 1. Calling non-static methods statically (still emits deprecation)
class Helper
{
    public function calculate(int $a, int $b): int
    {
        return $a + $b;
    }
}

// Still deprecated:
// $result = Helper::calculate(5, 3);  // Deprecation warning

// 2. Implicit nullable parameters continue to be deprecated
// function test(string $param = null) {}  // Don't use
// function test(?string $param = null) {}  // Correct

// 3. Dynamic properties with typed classes still deprecated
class User
{
    public int $id;
    
    public function __construct(int $id)
    {
        $this->id = $id;
        // $this->email = 'test@example.com';  // Deprecated
    }
}
```

### Features Removed in PHP 8.3

```php
<?php
// Some things fully removed from PHP 8.3:
// - None from core, but deprecations from 8.2 continue warning

// Migration path required for:
// - Legacy array access syntax
// - Old style method calls
// - Untyped properties in typed classes
```

---

## Feature Comparison Table

```
Feature                    │ 8.0  │ 8.1  │ 8.2  │ 8.3
────────────────────────────────────────────────────
Typed properties           │ ✓    │ ✓    │ ✓    │ ✓
Readonly properties        │      │ ✓    │ ✓    │ ✓
Named arguments            │ ✓    │ ✓    │ ✓    │ ✓
Constructor promotion      │ ✓    │ ✓    │ ✓    │ ✓
Readonly classes           │      │      │ ✓    │ ✓
DNF types                  │      │      │ ✓    │ ✓
Typed constants            │      │      │      │ ✓
#[Override] attribute      │      │      │      │ ✓
json_validate()            │      │      │      │ ✓
Enum improvements          │      │ ✓    │ ✓    │ ✓
First-class callables      │      │ ✓    │ ✓    │ ✓
Array unpacking            │ ✓    │ ✓    │ ✓    │ ✓ (improved)
```

---

## Migration Guide

### From PHP 8.2 to 8.3

```php
<?php
// Step 1: Update code to use typed constants
// ❌ Old way
class Config
{
    const HOST = 'localhost';
    const PORT = 5432;
}

// ✓ New way
class Config
{
    public const string HOST = 'localhost';
    public const int PORT = 5432;
}

// Step 2: Add #[Override] attributes
// ✓ Best practice
class Child extends Parent
{
    #[Override]
    public function process(): void {}
}

// Step 3: Use json_validate() where applicable
// ❌ Old way
$data = json_decode($json);
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    // Invalid
}

// ✓ New way
if (!json_validate($json)) {
    // Invalid
}

// Step 4: Update Random usage
// ✓ Use new Random\Randomizer
$random = new Random\Randomizer();
$value = $random->nextInt(100);
```

---

## System Requirements

```php
<?php
// PHP 8.3 Requirements:
// - 64-bit PHP recommended (32-bit supported)
// - 5 MB minimum disk space
// - For web: compatible web server
// - For CLI: command line interface

// Check version
if (version_compare(PHP_VERSION, '8.3.0', '>=')) {
    echo "PHP 8.3 compatible";
}

// Check for specific features
if (function_exists('json_validate')) {
    echo "json_validate() available";
}

// Check for attributes support
if (method_exists(ReflectionClass::class, 'getAttributes')) {
    echo "Attributes available";
}
```

---

## Learning Path

### For New PHP 8.3 Users

```
1. Understand Typed Constants
   - Read: Typed Constants chapter
   - Time: 30 minutes
   - Practice: Define config classes

2. Learn #[Override] Attribute
   - Read: Override Attribute chapter
   - Time: 20 minutes
   - Practice: Refactor inheritance

3. Master json_validate()
   - Read: JSON Validation chapter
   - Time: 25 minutes
   - Practice: Validate API responses

4. Explore Random Improvements
   - Read: Random Class chapter
   - Time: 20 minutes
   - Practice: Generate secure tokens

5. Review All Features
   - Read: Complete chapters
   - Time: 2-3 hours
   - Project: Build complete app
```

### For Upgrading from Earlier Versions

```
Priority Migration Steps:
1. Add typed constants (High priority)
2. Fix deprecation warnings (High priority)
3. Use json_validate() where applicable (Medium priority)
4. Add #[Override] attributes (Medium priority)
5. Explore other improvements (Low priority)
```

---

## Key Takeaways

### What You Should Remember

```php
<?php
// 1. Constants now have types
public const string NAME = 'app';

// 2. Override attribute prevents mistakes
#[Override]
public function method() {}

// 3. Validate JSON efficiently
if (json_validate($json)) {}

// 4. Better randomization available
$random = new Random\Randomizer();

// 5. 8-15% performance improvement
// Upgrade for better performance

// 6. Array handling improvements
array_is_list($array);

// 7. Enum enhancements available
enum Status: string { /* ... */ }

// 8. Type safety strengthened
// All improvements toward stricter typing
```

---

## Common Questions

### Should I upgrade to PHP 8.3?

```
✓ YES, if you want:
  - Better type safety
  - Performance improvements
  - Latest features
  - Security patches

⏳ WAIT, if you have:
  - Legacy code needing extensive refactoring
  - Dependencies not yet compatible
  - Critical systems in production
```

### Backward Compatibility?

```php
<?php
// PHP 8.3 is mostly backward compatible with 8.2
// Some deprecations become strict warnings
// Upgrade is usually safe for well-written code
```

### Performance Impact?

```
Positive Impact:
- 8-15% faster overall
- Especially on JSON operations (15%)
- And array operations (12%)
- Total cost of migration: low
```

---

## See Also

- [Typed Constants](2-typed-constants.md)
- [Override Attribute](3-override-attribute.md)
- [JSON Validation](4-json-validation.md)
- [Random Improvements](5-random-improvements.md)
- [Enum Enhancements](6-enum-enhancements.md)
- [Array Functions](7-array-functions.md)
- [Performance Tips](8-performance-improvements.md)
- [Migration Guide](9-migration-guide.md)
