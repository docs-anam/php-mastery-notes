# PHP 8.2 Overview and Major Features

## Overview

Learn about PHP 8.2, released in December 2022, which introduces powerful new language features, performance improvements, and modern type system enhancements.

---

## Table of Contents

1. What's New in PHP 8.2
2. Major Features Overview
3. Type System Enhancements
4. Performance Improvements
5. Deprecations and Changes
6. Migration Guide
7. Quick Reference
8. Learning Resources

---

## What's New in PHP 8.2

### Release Timeline

```
Release Date: December 8, 2022
Support Until: December 8, 2024 (Security fixes)
Stability: Stable and production-ready
```

### Key Statistics

```
✓ 15+ major features
✓ 50+ improvements
✓ 30+ deprecations
✓ 8-12% performance gain
✓ Better type safety
✓ More readable code
```

---

## Major Features Overview

### 1. Disjunctive Normal Form (DNF) Types

```php
<?php
// Complex union types with AND (&) and OR (|)

// (A & B) | (C & D)
function process(
    (Serializable & Countable) | (Iterator & ArrayAccess) $data
): void {
    // Works with complex type combinations
}

// Practical example
interface Cache {}
interface Async {}

function handler(
    (Cache & Async) | ArrayAccess $storage
): void {
    // Handle either cached async storage or array-like storage
}
```

### 2. Readonly Classes

```php
<?php
// Fully immutable classes

readonly class Point
{
    public function __construct(
        public float $x,
        public float $y,
    ) {}
}

$point = new Point(10.5, 20.3);
// $point->x = 15;  // Error! Cannot modify readonly property
```

### 3. Null-Safe Array Access

```php
<?php
// Chain method calls safely with null coalescing

$user = User::find(1);

// Before PHP 8.2
$city = $user?->address?->city ?? 'Unknown';

// PHP 8.2 also supports
$tags = $post?->getTags()[0] ?? null;
```

### 4. String Functions with Array Keys

```php
<?php
// New str_contains, str_starts_with, str_ends_with for arrays

$text = "Hello World";

if (str_contains($text, "World")) {
    echo "Found!";
}

if (str_starts_with($text, "Hello")) {
    echo "Greeting!";
}
```

### 5. Attributes for First-Class Callables

```php
<?php
// More power for callable references

class Controller
{
    #[Route('GET', '/users')]
    public function listUsers(): array
    {
        return [];
    }
}

// First-class callable
$callable = [$controller, 'listUsers'];
$result = $callable();
```

---

## Type System Enhancements

### Union Type Improvements

```php
<?php
// PHP 8.2 improves union type flexibility

// Disjunctive Normal Form (DNF)
function validate(
    (DateTimeImmutable & Serializable) | null $date
): bool {
    return $date instanceof Serializable;
}

// Intersection types improved
function process(
    Countable & ArrayAccess & IteratorAggregate $collection
): void {
    // Collection must implement all three interfaces
}

// Mixed type (PHP 8.0+)
function handle(mixed $value): mixed
{
    return $value;
}
```

### Type Covariance

```php
<?php
// Better return type variance

interface Repository
{
    public function find(int $id): Entity;
}

class UserRepository implements Repository
{
    // Return type can be more specific
    public function find(int $id): User  // User extends Entity
    {
        return new User();
    }
}
```

---

## Performance Improvements

### Speed Gains

```
JIT Improvements:      8-12% faster
Opcache:               Faster class loading
Memory:                Reduced footprint
Type checks:           Optimized
String operations:     30% faster
```

### Benchmark Examples

```
Simple script:     ~5% faster
Database queries:  ~8% faster
API calls:         ~10% faster
File operations:   ~6% faster
```

---

## Deprecations and Changes

### Deprecated Features

```php
<?php
// Deprecated: Case-insensitive constants
define('MY_CONST', 'value', true);  // Deprecated!

// Deprecated: Dynamic properties in stdClass
$obj = new stdClass();
$obj->dynamicProp = 'value';  // Still works, but deprecated

// Deprecated: strpos() without offset
if (strpos($haystack, $needle) !== false) {
    // Works, but should use str_contains()
}
```

### Breaking Changes

```php
<?php
// Removed: get_parent_class() on stdClass
get_parent_class(new stdClass());  // Error in 8.2

// Removed: Serializable interface behavior change
class MyClass implements Serializable
{
    // Must implement serialize() and unserialize()
}
```

---

## Migration Guide

### From PHP 8.1 to PHP 8.2

```php
<?php
// Step 1: Update type declarations

// PHP 8.1
function process(array | Iterator $data): void
{
}

// PHP 8.2 (can use DNF)
function process((Countable & Iterator) | array $data): void
{
}

// Step 2: Use readonly classes for immutables

readonly class Configuration
{
    public function __construct(
        public string $host,
        public int $port,
    ) {}
}

// Step 3: Use new string functions

// Before
if (strpos($url, 'https') !== false) {
    // ...
}

// After
if (str_starts_with($url, 'https')) {
    // ...
}

// Step 4: Remove deprecated patterns

// Before
define('CONSTANT', 'value', true);  // Case-insensitive

// After
const CONSTANT = 'value';  // Always case-sensitive
```

---

## Feature Categories

### Type System

- ✅ Disjunctive Normal Form Types
- ✅ Intersection Type Improvements
- ✅ Type Narrowing Enhancements
- ✅ Static Return Type

### Performance

- ✅ JIT Compiler Improvements
- ✅ Opcache Optimization
- ✅ Memory Efficiency
- ✅ String Operation Speed

### Language Features

- ✅ Readonly Classes
- ✅ Null-Safe Array/Method Chaining
- ✅ First-Class Callable Syntax
- ✅ Attributes on Properties

### Standard Library

- ✅ String Functions Enhanced
- ✅ DateTime Improvements
- ✅ JSON Processing
- ✅ Array Functions

---

## Quick Comparison

### PHP 8.0 → 8.1 → 8.2

```
Feature                    | 8.0 | 8.1 | 8.2
═══════════════════════════╪═════╪═════╪════
Union Types                | ✓   | ✓   | ✓
Named Arguments             | ✓   | ✓   | ✓
Match Expression            | ✓   | ✓   | ✓
Enums                       | ✗   | ✓   | ✓
Readonly Properties         | ✗   | ✓   | ✓
First-Class Callables       | ✗   | ✓   | ✓
Disjunctive Normal Form     | ✗   | ✗   | ✓
Readonly Classes            | ✗   | ✗   | ✓
Performance                 | ✓✓  | ✓✓✓ | ✓✓✓✓
Type Safety                 | ✓✓  | ✓✓✓ | ✓✓✓✓
```

---

## Common Use Cases

### Modern API Development

```php
<?php
readonly class ApiResponse
{
    public function __construct(
        public int $status,
        public string $message,
        public array $data = [],
    ) {}
}

class ApiHandler
{
    public function handle(
        (ServerRequestInterface & LoggerAware) | array $request
    ): ApiResponse {
        // Type-safe API handler
    }
}
```

### Data Validation

```php
<?php
interface Validator {}
interface Loggable {}

readonly class ValidatedData
{
    public function __construct(
        private (Validator & Loggable) $validator,
        public array $data,
    ) {}
}
```

### Caching Systems

```php
<?php
readonly class CacheKey
{
    public function __construct(
        public string $namespace,
        public string $key,
        public int $ttl = 3600,
    ) {}
}
```

---

## System Requirements

```
PHP: 8.2.x
Memory: Minimum 128MB, recommended 256MB
Extensions: Standard extensions compatible
Databases: All major databases supported
Web Servers: Apache, Nginx, IIS, all supported
Operating Systems: Linux, macOS, Windows
```

---

## Recommended Learning Path

### 1. Basics (Week 1-2)
- Readonly Classes
- DNF Types
- Null-Safe Access

### 2. Intermediate (Week 3-4)
- First-Class Callables
- Attributes
- Type Narrowing

### 3. Advanced (Week 5-6)
- Complex Type Combinations
- Performance Optimization
- Migration Patterns

### 4. Mastery (Week 7-8)
- Best Practices
- Architecture Patterns
- Production Deployment

---

## Key Takeaways

**PHP 8.2 Highlights:**

1. ✅ Disjunctive Normal Form for complex types
2. ✅ Readonly classes for immutability
3. ✅ Better performance (8-12% gain)
4. ✅ Enhanced type safety
5. ✅ Improved null handling
6. ✅ String function improvements
7. ✅ Modern, clean syntax
8. ✅ Production-ready stability

---

## See Also

- [Readonly Classes](2-readonly-classes.md)
- [Disjunctive Normal Form Types](3-dnf-types.md)
- [String Functions](4-string-functions.md)
- [First-Class Callables](5-first-class-callables.md)
