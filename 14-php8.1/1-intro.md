# PHP 8.1 - Advanced Modern Features

## Table of Contents
1. [Overview](#overview)
2. [Major Features](#major-features)
3. [Feature Details](#feature-details)
4. [Migration from PHP 8.0](#migration-from-php-80)
5. [Performance Improvements](#performance-improvements)
6. [Learning Path](#learning-path)
7. [Prerequisites](#prerequisites)

---

## Overview

PHP 8.1 (Released November 2021) continues improving upon PHP 8.0 with:

- **Enumerations (Enums)** - Type-safe constants
- **Readonly Properties** - Immutable data
- **First-Class Callables** - Function references
- **Fibers** - Async programming
- **Intersection Types** - AND type checking
- **Never Return Type** - Functions that never return
- **Performance improvements** - 5-10% faster

## Major Features

### 1. Enumerations (Enums)

Type-safe way to define constants:

**Before PHP 8.1 (Using Constants)**
```php
class Status {
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
}

// Problem: Type isn't enforced
$status = 'invalid_value';  // No error!
```

**PHP 8.1 (Enums)**
```php
enum Status {
    case PENDING;
    case APPROVED;
    case REJECTED;
}

// Type is enforced
function processOrder(Status $status) {
    match($status) {
        Status::PENDING => 'Waiting approval',
        Status::APPROVED => 'Processing',
        Status::REJECTED => 'Cancelled',
    };
}

$status = Status::APPROVED;  // Type-safe!
processOrder('invalid');  // Error: Wrong type
```

### 2. Readonly Properties

Make properties immutable after initialization:

```php
class User {
    public readonly string $id;
    public readonly string $email;
    
    public function __construct(string $id, string $email) {
        $this->id = $id;          // Can set once
        $this->email = $email;
    }
}

$user = new User('123', 'user@example.com');

$user->id = '456';  // Error: Can't modify readonly property!
```

**Benefits:**
- Prevent accidental modifications
- Document intent in code
- Enforce immutability
- Better performance (immutable is faster)

### 3. First-Class Callable Syntax

Create references to functions:

**Before PHP 8.1**
```php
$callable = 'strlen';  // String reference
$result = $callable('hello');  // Works but unclear

// With callbacks
array_map('strtoupper', ['a', 'b', 'c']);
```

**PHP 8.1**
```php
$callable = strlen(...);  // Clear and type-checked
$result = $callable('hello');

// With callbacks
array_map(strtoupper(...), ['a', 'b', 'c']);
array_filter($array, is_string(...));
```

**Benefits:**
- Type checking at compile time
- IDE autocomplete support
- Clearer intent

### 4. Intersection Types

Value must be instance of ALL types:

```php
interface Loggable {
    public function log(): void;
}

interface Serializable {
    public function serialize(): string;
}

class Report implements Loggable, Serializable {
    public function log(): void { /* ... */ }
    public function serialize(): string { /* ... */ }
}

// Requires BOTH interfaces
function process(Loggable&Serializable $data): void {
    $data->log();
    $data->serialize();
}

$report = new Report();
process($report);  // OK - has both

class SimpleLog implements Loggable {
    // Only has Loggable
}
process(new SimpleLog());  // Error: Missing Serializable
```

**When to use:**
- Multiple interface requirements
- Strict type contracts
- Complex inheritance hierarchies

### 5. Never Return Type

Function that never returns (throws or infinite loop):

```php
// Throws exception
function fail(string $message): never {
    throw new Exception($message);
}

// Infinite loop
function keepRunning(): never {
    while (true) {
        sleep(1);
    }
}

// Exits
function terminate(): never {
    exit(1);
}

function process($value): string {
    if ($value === null) {
        fail('Value cannot be null');  // Never returns
    }
    return (string)$value;
}
```

**Benefits:**
- Documents intent clearly
- Type checker can verify flow
- Prevents invalid code paths

### 6. Final Class Constants

Make class constants final (can't override):

```php
class BaseConfig {
    final public const MAX_USERS = 100;
}

class CustomConfig extends BaseConfig {
    // Error: Can't override final constant
    public const MAX_USERS = 200;
}
```

## Feature Details

### Array Unpacking with String Keys

```php
// PHP 8.0: Numeric keys only
$array1 = [1 => 'a', 2 => 'b'];
$array2 = [...$array1];  // Works

// PHP 8.1: String keys too
$array1 = ['a' => 1, 'b' => 2];
$array2 = [...$array1];  // Works! {a: 1, b: 2}
```

### New in Initializers

Use `new` in property default values:

```php
// PHP 8.0: Can't use new in initializer
class User {
    private Logger $logger = new Logger();  // Error!
}

// PHP 8.1: Can use new
class User {
    public function __construct(
        private Logger $logger = new Logger(),
    ) {}
}
```

### Fibers (Async)

Lightweight concurrency primitive:

```php
// Simple fiber
$fiber = new Fiber(function(): void {
    echo "1\n";
    Fiber::suspend();
    echo "2\n";
});

$fiber->start();      // Output: 1
$fiber->resume();     // Output: 2
```

## Migration from PHP 8.0

### Changes to Consider

**Deprecations removed:**
```php
// Removed: Calling functions via first-class strings
$callable = 'count';
$callable([]);  // Error in 8.1!

// Use
$callable = count(...);  // New syntax
```

**Type checking stricter:**
```php
// More warnings for implicit type conversions
// Ensure explicit casts
```

**Performance**:
- 5-10% faster than PHP 8.0
- Less memory usage
- Better JIT optimization

## Performance Improvements

### Benchmarks vs PHP 8.0

| Operation | Improvement |
|-----------|-------------|
| Array operations | +8% |
| String operations | +6% |
| Class instantiation | +4% |
| Function calls | +5% |
| Overall | +5-10% |

### JIT Improvements

PHP 8.1 JIT is more mature:
- Better optimization
- Fewer memory issues
- Stable for production

## Learning Path

Master PHP 8.1 progressively:

1. **Enumerations** - Type-safe constants
2. **Readonly Properties** - Immutable data
3. **First-Class Callables** - Function references
4. **Intersection Types** - Multiple interfaces
5. **Never Return Type** - Non-returning functions
6. **Final Constants** - Immutable constants
7. **New in Initializers** - Default object creation
8. **Fibers** - Async programming
9. **Array Unpacking** - Spread operator improvements
10. **Performance** - Optimization in PHP 8.1

## Quick Feature Comparison

| Feature | PHP 8.0 | PHP 8.1 |
|---------|---------|---------|
| Enums | ❌ | ✅ |
| Readonly Properties | ❌ | ✅ |
| First-Class Callables | ❌ | ✅ |
| Intersection Types | ❌ | ✅ |
| Never Return Type | ❌ | ✅ |
| String/Array Unpacking | ❌ | ✅ |
| new in Initializers | ❌ | ✅ |
| Fibers | ❌ | ✅ |

## Code Example: Using PHP 8.1

```php
<?php
declare(strict_types=1);

// Enum for type-safe states
enum OrderStatus {
    case PENDING;
    case PROCESSING;
    case SHIPPED;
    case DELIVERED;
}

// Readonly properties for immutability
class Order {
    public readonly int $id;
    public readonly string $customer;
    public OrderStatus $status;
    
    public function __construct(
        int $id,
        string $customer,
        OrderStatus $status = OrderStatus::PENDING,
    ) {
        $this->id = $id;
        $this->customer = $customer;
        $this->status = $status;
    }
    
    public function process(): never|true {
        if ($this->status !== OrderStatus::PENDING) {
            throw new Exception('Order already processing');
        }
        
        $this->status = OrderStatus::PROCESSING;
        return true;
    }
}

// Usage
$order = new Order(123, 'John Doe');
$order->process();

// Type safe
$order->status = 'invalid';  // Error: Wrong type!
```

## Prerequisites

Before learning PHP 8.1:

✅ **Required:**
- Solid PHP 8.0 knowledge
- Understanding of OOP principles
- Familiarity with type declarations

✅ **Helpful:**
- Knowledge of design patterns
- Experience with async programming concepts
- Understanding of type systems

## Upgrade Recommendations

### When to Upgrade?

✅ **Good time:**
- Starting new projects
- PHP 8.0 support ending (November 2023)
- Need latest performance

❌ **Not yet:**
- Heavy dependency on PHP 7 libraries
- Production stability critical
- Team not ready for changes

### Testing After Upgrade

```bash
# Run tests
vendor/bin/phpunit

# Check for deprecations
php -l file.php  # Syntax check

# Type checking
vendor/bin/phpstan analyze src/
```

## Resources

- **Official Upgrade Guide**: [php.net/manual/en/migration81.php](https://www.php.net/manual/en/migration81.php)
- **RFC Discussions**: [PHP RFC Archive](https://wiki.php.net/rfc)
- **Enums**: [php.net/manual/en/language.enumerations.php](https://www.php.net/manual/en/language.enumerations.php)
- **Fibers**: [php.net/manual/en/language.fibers.php](https://www.php.net/manual/en/language.fibers.php)
