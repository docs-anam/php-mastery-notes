# PHP 8.2 - Modern Language Features

## Table of Contents
1. [Overview](#overview)
2. [Major Features](#major-features)
3. [Feature Deep Dive](#feature-deep-dive)
4. [Performance Improvements](#performance-improvements)
5. [Migration from PHP 8.1](#migration-from-php-81)
6. [Learning Path](#learning-path)
7. [Deprecations](#deprecations)

---

## Overview

PHP 8.2 (Released December 2022) introduces:

- **Disjunctive Normal Form (DNF) Types** - Complex union types
- **Readonly Classes** - Fully immutable classes
- **First-Class Callable Improvements** - Better function references
- **Intersection Types in Generics** - More flexible type constraints
- **Performance improvements** - 8-12% faster
- **Stricter type checking** - Better type safety

**Release Cycle:** Bug fixes until December 2023, Security fixes until December 2024

---

## Major Features

### 1. Disjunctive Normal Form (DNF) Types

Type mixing with AND (&) and OR (|) operators:

**Problem (Before PHP 8.2):**
```php
// Can't express: (A & B) | (C & D)
// This violates type mixing rules
function process((Serializable & Countable) | (Iterator & Throwable) $data) {
    // Invalid in PHP 8.1!
}
```

**PHP 8.2 Solution:**

```php
<?php
interface Loggable {}
interface Serializable {}
interface Monitorable {}
interface Traceable {}

// Express complex type requirements
// (Loggable & Serializable) | (Monitorable & Traceable)
function process(
    (Loggable & Serializable) | (Monitorable & Traceable) $handler
): void {
    if ($handler instanceof Loggable && $handler instanceof Serializable) {
        $handler->log();
        $handler->serialize();
    } elseif ($handler instanceof Monitorable && $handler instanceof Traceable) {
        $handler->monitor();
        $handler->trace();
    }
}

class EventLogger implements Loggable, Serializable {
    public function log(): void { echo "Logging"; }
    public function serialize(): string { return ""; }
}

class Monitor implements Monitorable, Traceable {
    public function monitor(): void { echo "Monitoring"; }
    public function trace(): void { echo "Tracing"; }
}

// Both valid
process(new EventLogger());
process(new Monitor());
```

**When to use DNF:**
- Complex inheritance hierarchies
- Multiple interface requirements
- Library code with flexible contracts

### 2. Readonly Classes

Make entire class immutable:

**Before PHP 8.2 (Readonly Properties):**
```php
class User {
    public readonly int $id;
    public readonly string $email;
    
    public function __construct(int $id, string $email) {
        $this->id = $id;
        $this->email = $email;
    }
}

// Problem: Can still add dynamic properties
$user = new User(1, 'user@example.com');
$user->phone = '123-456-7890';  // Allowed but not desired
```

**PHP 8.2 (Readonly Classes):**
```php
readonly class User {  // All properties readonly
    public function __construct(
        public int $id,
        public string $email,
    ) {}
    
    // Can have methods
    public function getDisplayName(): string {
        return "User #{$this->id}";
    }
}

$user = new User(1, 'user@example.com');
$user->phone = '123-456-7890';  // Error! Can't modify readonly class
```

**Benefits:**
- All properties immutable by default
- Less boilerplate
- Clear intent
- Better performance (compiler optimization)

### 3. New in Initializers Improvements

**PHP 8.1 Added:**
```php
class Service {
    public function __construct(
        private Logger $logger = new Logger(),
    ) {}
}
```

**PHP 8.2 Extends:**
```php
class Config {
    public readonly string $path;
    
    public function __construct(
        public readonly string $environment = 'production',
        public readonly int $timeout = new Defaults('timeout'),  // New!
        private Logger $logger = new Logger(),
    ) {
        $this->path = match($environment) {
            'production' => '/var/config/prod.json',
            'staging' => '/var/config/staging.json',
            default => '/var/config/dev.json',
        };
    }
}
```

### 4. Null, True, False as Standalone Types

Can use directly in type unions:

```php
<?php
// PHP 8.2: null, true, false are standalone types
function process(int|null $value): void {}         // Old: null allowed in union
function flag(): true|false { return true; }        // New: explicit boolean values
function result(): int|false { return 0; }          // Explicit false type

// Examples
function divide(int $a, int $b): int|false {
    if ($b === 0) {
        return false;  // Explicit failure value
    }
    return intdiv($a, $b);
}

$result = divide(10, 2);  // int | false
if ($result === false) {
    echo "Division error";
} else {
    echo "Result: $result";
}
```

---

## Feature Deep Dive

### DNF Type Examples

**Example 1: Event System**

```php
<?php
interface Observable {}
interface Reportable {}
interface Loggable {}

// Event can be either:
// - Observable and Reportable (for events)
// - Loggable (for logs)
function processData(
    (Observable & Reportable) | Loggable $data
): void {
    if ($data instanceof Observable && $data instanceof Reportable) {
        // Observable and Reportable handler
        $data->observe();
        $data->report();
    } else {
        // Loggable handler
        $data->log();
    }
}
```

**Example 2: Database Abstraction**

```php
<?php
interface Connectable {}
interface Queryable {}
interface Transactional {}
interface Cacheable {}

// Database connection can implement different combinations
function execute(
    (Connectable & Queryable & Transactional) | (Cacheable & Queryable) $db
): void {
    // Full featured DB vs Cache
}
```

### Readonly Class Benefits

```php
<?php
// Good for Data Transfer Objects
readonly class UserDTO {
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public array $roles,
    ) {}
    
    public function hasRole(string $role): bool {
        return in_array($role, $this->roles);
    }
}

// Immutable by design
$user = new UserDTO(1, 'John', 'john@example.com', ['admin', 'user']);

// Type safe and immutable
// Can safely pass around knowing it won't be modified
function sendEmail(UserDTO $user) {
    echo "Email: {$user->email}";
}
```

---

## Performance Improvements

### Benchmarks vs PHP 8.1

| Operation | Improvement |
|-----------|-------------|
| Array operations | +10% |
| String operations | +8% |
| Object creation | +6% |
| Inheritance chains | +7% |
| Overall | +8-12% |

### JIT Performance

- Better optimization for loops
- Reduced memory footprint
- More stable for production workloads
- Up to 3x faster than PHP 7.4

```php
<?php
// PHP 8.2 JIT handles this efficiently
for ($i = 0; $i < 1000000; $i++) {
    $sum += fibonacci($i);  // Optimized by JIT
}
```

---

## Migration from PHP 8.1

### Changes to be Aware Of

**Deprecations Removed:**

```php
<?php
// Removed: Case-insensitive constants
define('MY_CONSTANT', 'value', true);  // Error in 8.2!
// Use case-sensitive instead
define('MY_CONSTANT', 'value');

// Removed: Use of "curly" for string/array access
$string = "hello";
echo $string{0};  // Error in 8.2!
echo $string[0];  // Use bracket syntax

// Removed: Mbstring functions with no encoding
mb_strtoupper($str);  // Error!
mb_strtoupper($str, 'UTF-8');  // Specify encoding
```

**Type Stricter:**

```php
<?php
// More warnings for loose type comparisons
$value = '0';
if ($value) {}  // Warning: condition always false with strict types

// Be explicit
if ($value !== '') {}  // Clear intent
```

### Upgrade Process

```bash
# 1. Check compatibility
php -l file.php

# 2. Run tests
vendor/bin/phpunit

# 3. Static analysis
vendor/bin/phpstan analyze src/

# 4. Code style
vendor/bin/phpcs --standard=PSR12 src/

# 5. Check deprecations
php -d display_errors=1 vendor/bin/phpunit
```

---

## Deprecations

### PHP 8.2 Deprecations

| Feature | Use Instead |
|---------|-------------|
| Curly string/array access | Bracket syntax `$str[0]` |
| Case-insensitive constants | Case-sensitive constants |
| `#[\AllowDynamicProperties]` | Use readonly or typed properties |
| Some reflection methods | Newer reflection APIs |

---

## Code Example: PHP 8.2 Features

```php
<?php
declare(strict_types=1);

// Readonly class for immutability
readonly class ConfigValue {
    public function __construct(
        public string $key,
        public mixed $value,
        public string $environment,
    ) {}
}

interface Configurable {}
interface Observable {}
interface Reportable {}

// DNF Type: Flexible requirement combinations
class ConfigManager {
    public function handle(
        (Configurable & Observable) | (Reportable & Observable) $config,
    ): void {
        if ($config instanceof Configurable && $config instanceof Observable) {
            echo "Configuration handler";
        } elseif ($config instanceof Reportable) {
            echo "Report handler";
        }
    }
}

// Usage
$config = new ConfigValue('db.host', 'localhost', 'production');

// Type-safe, immutable
function processConfig(ConfigValue $value): void {
    echo "Processing {$value->key} = {$value->value}";
}

processConfig($config);
```

---

## Learning Path

Master PHP 8.2 progressively:

1. **DNF Types** - Complex type unions
2. **Readonly Classes** - Immutable classes
3. **Standalone Types** - null, true, false
4. **Type Safety** - Stricter type checking
5. **Performance** - Optimizations
6. **Deprecation Handling** - Update legacy code
7. **Migration** - Upgrade from PHP 8.1
8. **Best Practices** - Use new features effectively

## Quick Feature Comparison

| Feature | PHP 8.1 | PHP 8.2 |
|---------|---------|---------|
| Enums | ✅ | ✅ |
| Readonly Properties | ✅ | ✅ |
| Readonly Classes | ❌ | ✅ |
| DNF Types | ❌ | ✅ |
| Standalone Types | Partial | ✅ |
| First-Class Callables | ✅ | ✅ |
| Fibers | ✅ | ✅ |
| Performance | Baseline | +8-12% |

---

## Prerequisites

Before learning PHP 8.2:

✅ **Required:**
- PHP 8.1 fundamentals
- Understanding of advanced types
- OOP principles (inheritance, interfaces)

✅ **Helpful:**
- Knowledge of type systems
- Experience with immutable data structures
- Understanding of generic types

## Resources

- **Official Migration Guide**: [php.net/manual/en/migration82.php](https://www.php.net/manual/en/migration82.php)
- **RFC List**: [PHP RFC Archive](https://wiki.php.net/rfc)
- **Type System**: [Type Declarations](https://www.php.net/manual/en/language.types.declarations.php)
- **Performance**: [PHP Benchmarks](https://www.php.net/~derick/php-performance/)
