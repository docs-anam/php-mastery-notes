# PHP 8.1 Overview

## Introduction

PHP 8.1, released November 25, 2021, introduces powerful features for modern PHP development including enumerations, readonly properties, first-class callable syntax, and improved type system capabilities.

---

## Table of Contents

1. What's New in PHP 8.1
2. System Requirements
3. Major Features
4. Performance Improvements
5. Deprecations and Removals
6. Upgrade Path
7. Compatibility Considerations

---

## What's New in PHP 8.1

### Major Feature Categories

```php
<?php
// Enumerations - Type-safe enums
enum Status {
    case Pending;
    case Active;
    case Completed;
}

// Readonly Properties - Immutable once set
class User {
    public readonly string $email;
    
    public function __construct(string $email) {
        $this->email = $email;
    }
}

// First-class Callable Syntax - Functions as objects
$strlen = strlen(...);  // Instead of Closure::fromCallable
echo $strlen('hello');

// Pure Intersection Types - Combining multiple interfaces
function process(LoggerInterface&FlushableInterface $logger): void {
    $logger->log('Processing');
    $logger->flush();
}

// Never Return Type - Functions that never return
function throwException(): never {
    throw new Exception('Error');
}

// Final Class Constants - Lock constants in parent class
class Parent {
    final public const VALUE = 'immutable';
}

// New in Initializer - Create objects in property defaults
class Service {
    public function __construct(
        private Logger $logger = new Logger()
    ) {}
}
```

### Feature Maturity Timeline

```
PHP 8.0 (Nov 2020)
├─ Named arguments
├─ Union types
├─ Constructor property promotion
└─ Match expressions

PHP 8.1 (Nov 2021) ← YOU ARE HERE
├─ Enumerations
├─ Readonly properties
├─ Intersection types
├─ First-class callables
└─ Never return type

PHP 8.2 (Dec 2022)
├─ Disjunctive Normal Form Types
├─ Readonly classes
├─ Intersection type improvements
└─ Deprecated features

PHP 8.3 (Nov 2023)
├─ Typed class constants
├─ readonly class shorthand
└─ Additional deprecations
```

---

## System Requirements

### Minimum Requirements

```
✓ PHP 8.0+ base (backward compatible)
✓ 64-bit architecture recommended
✓ No special extensions required
✓ Composer 2.0+ (for modern packages)
✓ ZTS (Thread Safety) optional
```

### Checking Your Version

```php
<?php
// Check PHP version
echo PHP_VERSION;  // 8.1.0

// Check if version supports feature
if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
    // Use 8.1 features
}

// Programmatic version check
printf("PHP %d.%d\n", PHP_MAJOR_VERSION, PHP_MINOR_VERSION);
// Output: PHP 8 1

// Check specific version parts
var_dump(phpversion());  // Full version string with build info
```

### Upgrading From Earlier Versions

```php
<?php
// From PHP 8.0 → 8.1
// Most code works without changes
// Follow deprecation notices

// From PHP 7.4 → 8.1
// More breaking changes
// Modern type system required
// Use declare(strict_types=1)
```

---

## Major Features

### 1. Enumerations (Enums)

```php
<?php
// Type-safe enums with methods and traits

// Pure Enum
enum Status {
    case Draft;
    case Published;
    case Archived;
}

// Backed Enum
enum Color: string {
    case Red = 'red';
    case Green = 'green';
    case Blue = 'blue';
}

// Using enums
function publish(Status $status): void {
    match($status) {
        Status::Draft => echo 'Publishing...',
        Status::Published => echo 'Already published',
        Status::Archived => echo 'Cannot publish archived',
    };
}

publish(Status::Draft);  // Publishing...
```

### 2. Readonly Properties

```php
<?php
// Immutable properties set once at initialization

class User {
    public readonly string $email;
    
    public function __construct(string $email) {
        $this->email = $email;
    }
}

$user = new User('john@example.com');
echo $user->email;  // john@example.com

// $user->email = 'new@example.com';  // Error: Cannot modify readonly property
```

### 3. First-class Callable Syntax

```php
<?php
// Functions and methods as first-class values

// Traditional closure
$getLength = fn($str) => strlen($str);

// First-class callable (8.1+)
$getLength = strlen(...);

// Method callable
class Calculator {
    public function add(int $a, int $b): int {
        return $a + $b;
    }
}

$calc = new Calculator();
$adder = $calc->add(...);

echo $adder(5, 3);  // 8

// Static methods
class Math {
    public static function multiply(int $a, int $b): int {
        return $a * $b;
    }
}

$multiplier = Math::multiply(...);
echo $multiplier(4, 5);  // 20
```

### 4. Intersection Types

```php
<?php
// Require multiple interfaces/types

interface Logger {
    public function log(string $msg): void;
}

interface Flushable {
    public function flush(): void;
}

// Type must implement both
function process(Logger&Flushable $handler): void {
    $handler->log('Processing');
    $handler->flush();
}

class FileLogger implements Logger, Flushable {
    public function log(string $msg): void {
        // log to file
    }
    
    public function flush(): void {
        // flush buffer
    }
}

process(new FileLogger());
```

### 5. Never Return Type

```php
<?php
// Function always exits/throws, never returns

function throwError(string $msg): never {
    throw new Exception($msg);
}

function exitProgram(): never {
    echo "Shutting down...";
    exit(1);
}

function infiniteLoop(): never {
    while (true) {
        echo "Looping...\n";
    }
}

// Usage
try {
    throwError('Something went wrong');
} catch (Exception $e) {
    echo $e->getMessage();
}
```

### 6. Final Class Constants

```php
<?php
// Prevent child classes from overriding constants

class Parent {
    final public const STATUS = 'active';
    public const MUTABLE = 'can override';
}

class Child extends Parent {
    // Error: Cannot override final constant
    // public const STATUS = 'inactive';
    
    // OK: Can override non-final
    public const MUTABLE = 'overridden';
}
```

### 7. New in Initializer

```php
<?php
// Create objects in property initializers

class Service {
    public function __construct(
        private Logger $logger = new Logger(),
        private PDO $db = new PDO('sqlite::memory:'),
    ) {}
}

// Replaces old pattern
class OldService {
    private Logger $logger;
    
    public function __construct(Logger $logger = null) {
        $this->logger = $logger ?? new Logger();
    }
}
```

---

## Performance Improvements

### Speed Enhancements

```
8.0 vs 8.1 Performance
├─ Enum improvements: ~5-10% faster
├─ JIT optimizations: Continues from 8.0
├─ FFI improvements: ~15% faster
├─ Array handling: Optimized internally
└─ Type checking: More efficient

Real-world impact:
- Web applications: 2-5% improvement
- Data processing: 5-15% improvement
- CPU-bound tasks: 10-20% improvement
```

### Optimization Opportunities

```php
<?php
// Use readonly for immutable objects
class ImmutableConfig {
    public readonly string $apiKey;
    public readonly string $endpoint;
    
    // PHP optimizes property access
}

// Use enums instead of class constants
enum PaymentMethod {
    case CreditCard;
    case PayPal;
    case BankTransfer;
}

// Cleaner, faster type checking
function process(PaymentMethod $method): void {
    // match() with enums is highly optimized
}
```

---

## Deprecations and Removals

### Removed From PHP 8.1

```php
<?php
// Removed Features (from 8.0)

// Removed: Automatic re-binding of closures
// Use explicit $this or static context

// Removed: Array unpacking with string keys
$array = ['a' => 1, ...$other];  // Only int keys

// Removed: mb_* shortcut functions
```

### Deprecations in 8.1 (To Remove Later)

```php
<?php
// Deprecated: Return type of Serializable::serialize
// Use JsonSerializable instead

class Data implements JsonSerializable {
    public function jsonSerialize(): mixed {
        return $this->data;
    }
}

// Deprecated: Calling parent constructor implicitly
class Parent {
    public function __construct() {}
}

class Child extends Parent {
    public function __construct() {
        parent::__construct();  // Must be explicit
    }
}

// Deprecated: PDO::ATTR_ERRMODE defaults
$pdo->setAttribute(
    PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION
);
```

---

## Upgrade Path

### Step-by-Step Upgrade

```bash
# 1. Check current version
php -v

# 2. Install PHP 8.1
# On macOS with Homebrew
brew install php@8.1

# 3. Switch version
brew unlink php && brew link php@8.1

# 4. Update Composer dependencies
composer update

# 5. Run tests
vendor/bin/phpunit

# 6. Check deprecations
php -d error_reporting=E_ALL script.php
```

### Compatibility Checklist

```php
<?php
// 1. Declare strict types
declare(strict_types=1);

// 2. Use type hints everywhere
function process(string $data): array {
    // ...
}

// 3. Handle deprecation notices
// Set error_reporting appropriately

// 4. Update dependencies
// Run: composer update

// 5. Test thoroughly
// Run: vendor/bin/phpunit

// 6. Check for extensions
// Some may not support 8.1 yet
phpinfo();
```

---

## Compatibility Considerations

### PHP 7.4 Code in 8.1

```php
<?php
// 7.4 style still works
function old_function($param) {
    return $param;
}

// But 8.1 prefers strict typing
function new_function(mixed $param): mixed {
    return $param;
}

// Mixed type helps migration
```

### Third-party Libraries

```
Popular Library Support
├─ Symfony: 5.4+ (8.1 support)
├─ Laravel: 8.0+ (8.1 support)
├─ Doctrine ORM: 2.10+ (8.1 support)
├─ Monolog: 2.0+ (8.1 support)
├─ PHPUnit: 9.5+ (8.1 support)
└─ Composer: 2.0+ (required)
```

### Testing for Compatibility

```php
<?php
// Test matrix in CI/CD
$versions = [
    '8.0',
    '8.1',  // Your target
    '8.2',  // Future
];

// Version-specific code
if (PHP_VERSION_ID >= 80100) {
    // Use 8.1 features
    enum Status { case Active; }
} else {
    // Fallback for 8.0
    class Status {
        const ACTIVE = 'active';
    }
}
```

---

## Learning Path

### Essential to Learn First

1. **Enumerations** - Replaces many class constant patterns
2. **Readonly Properties** - Immutability for value objects
3. **First-class Callables** - Cleaner callback syntax

### Next Level

4. **Intersection Types** - Advanced type checking
5. **Never Return Type** - Better flow analysis
6. **New in Initializer** - Constructor simplification

### Advanced Topics

7. **Final Constants** - API design patterns
8. **Performance Tuning** - JIT compilation
9. **Extension Development** - Native PHP extensions

---

## Key Takeaways

**PHP 8.1 Essentials:**

1. ✅ Modern type system (union, intersection, never)
2. ✅ Enums for type-safe constants
3. ✅ Readonly for immutability
4. ✅ First-class callables for cleaner code
5. ✅ Better performance from JIT improvements
6. ✅ Backward compatible from 8.0
7. ✅ Deprecations guide upgrade path

---

## See Also

- [Enumerations](2-enumerations.md)
- [Readonly Properties](3-readonly-properties.md)
- [First-class Callables](4-first-class-callable-syntax.md)
- [Intersection Types](5-pure-intersection-types.md)
- [Never Return Type](6-never-return-type.md)
