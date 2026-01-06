# PHP 8.0 - Modern PHP Features & Breaking Changes

## Table of Contents
1. [Overview](#overview)
2. [Major Improvements](#major-improvements)
3. [What Changed](#what-changed-from-php-7)
4. [New Features](#new-features-in-detail)
5. [Breaking Changes](#breaking-changes)
6. [Migration Guide](#migration-guide)
7. [Learning Path](#learning-path)
8. [Prerequisites](#prerequisites)

---

## Overview

PHP 8.0 (Released November 2020) is a major release bringing significant improvements to the language:

- **Better Performance**: 10-15% faster than PHP 7.4
- **Better Syntax**: Cleaner, more intuitive code
- **Better Type System**: Stricter type checking by default
- **Better Tooling**: JIT compilation support

### Why PHP 8 Matters

PHP 8 brings the language closer to modern standards like Python and JavaScript, making it more developer-friendly while maintaining backward compatibility where possible.

## Major Improvements

### 1. Named Arguments (Game-Changer)

Call functions with parameter names instead of relying on position:

**PHP 7.4 (Order-Dependent)**
```php
function makeRequest($host, $port, $ssl, $timeout) {
    // ...
}

// Confusing - what does false mean?
makeRequest('example.com', 8080, false, 30);
```

**PHP 8.0 (Named Arguments)**
```php
// Crystal clear!
makeRequest(
    host: 'example.com',
    port: 8080,
    ssl: false,
    timeout: 30
);

// Can pass in any order
makeRequest(
    timeout: 30,
    host: 'example.com',
    ssl: false,
    port: 8080,
);
```

**Benefits:**
- Self-documenting code
- Can skip optional parameters
- Resilient to parameter reordering

### 2. Union Types

Variables can be one of multiple types:

**PHP 7.4**
```php
// What types are allowed?
public function process($input) {
    // Documentation needed
}
```

**PHP 8.0**
```php
public function process(int|float|string $input): bool {
    // Clear what's accepted
}

process(42);      // OK
process(3.14);    // OK
process("text");  // OK
process([]);      // Error!
```

**Use Cases:**
- Accept multiple types explicitly
- Better IDE support
- Clearer function contracts

### 3. Match Expressions

More powerful switch statements:

**PHP 7.4 (Switch)**
```php
switch ($status) {
    case 'pending':
        $message = 'Pending approval';
        break;
    case 'approved':
        $message = 'Approved!';
        break;
    default:
        $message = 'Unknown';
}
```

**PHP 8.0 (Match)**
```php
$message = match($status) {
    'pending' => 'Pending approval',
    'approved' => 'Approved!',
    default => 'Unknown',
};

// Multiple values
$level = match($score) {
    0, 1, 2 => 'F',
    3, 4 => 'D',
    5, 6 => 'C',
    7, 8 => 'B',
    9, 10 => 'A',
};
```

**Advantages:**
- Expression (returns value)
- Strict comparison (===)
- Throws exception if no match
- Cleaner syntax

### 4. Constructor Property Promotion

Reduce boilerplate in classes:

**PHP 7.4**
```php
class User {
    private $name;
    private $email;
    private $age;
    
    public function __construct($name, $email, $age) {
        $this->name = $name;
        $this->email = $email;
        $this->age = $age;
    }
}
```

**PHP 8.0**
```php
class User {
    public function __construct(
        private $name,
        private $email,
        private $age,
    ) {}
}
// Properties auto-assigned!
```

**Benefits:**
- Less code
- Properties and parameters together
- Cleaner constructors

### 5. Nullsafe Operator

Safe navigation through objects:

**PHP 7.4**
```php
// Must check each level
if ($user && $user->profile && $user->profile->settings) {
    $theme = $user->profile->settings->theme;
}
```

**PHP 8.0**
```php
// Null-safe chaining
$theme = $user?->profile?->settings?->theme;

// If any is null, result is null
// No errors thrown!
```

**Use Cases:**
- Deep object navigation
- API responses with optional fields
- Simplified null checking

### 6. Attributes (Metadata)

Attach metadata to code:

```php
#[Route('/users', methods: ['GET'])]
#[Auth('admin')]
public function listUsers() {
    // ...
}

#[Deprecated(reason: "Use newFunction instead")]
public function oldFunction() {
    // ...
}
```

**Use Cases:**
- Framework routing
- Validation rules
- Documentation
- ORM mappings

### 7. JIT Compilation

Just-In-Time compilation for performance:

- Automatic, no configuration
- ~10-15% faster execution
- Not needed for most apps

## What Changed from PHP 7

### Type Declaration Changes

**PHP 7.4 - Flexible**
```php
function test(int $x): int {
    return $x;
}

test("5");  // Auto-converts to int 5 (loose typing)
```

**PHP 8.0 - Strict Mode by Default**
```php
declare(strict_types=1);

function test(int $x): int {
    return $x;
}

test("5");  // Error! String can't be converted
test(5);    // OK
```

### throw Can Be Expression

**PHP 7.4**
```php
if (!$email) {
    throw new InvalidArgumentException("Email required");
}
$value = $email;
```

**PHP 8.0**
```php
$value = $email ?: throw new InvalidArgumentException("Email required");

$value = $email ?? throw new InvalidArgumentException("Email required");
```

### Error Handling

More exceptions thrown (instead of warnings):

```php
// PHP 7.4: Warning, returns null
$result = $array['undefined_key'];

// PHP 8.0: Notice (with error_reporting)
$result = $array['undefined_key'];

// Recommendation: Use isset() or ??
$result = $array['undefined_key'] ?? null;
```

## New Features in Detail

### 1. Nullsafe Operator
```php
$country = $user?->address?->country;
```

### 2. Named Arguments
```php
function makeCoffee($type, $size = 'medium') {}
makeCoffee(size: 'large', type: 'espresso');
```

### 3. Constructor Property Promotion
```php
public function __construct(
    private string $name,
    private int $age,
) {}
```

### 4. Union Types
```php
public function process(int|string|null $value): bool {}
```

### 5. Match Expression
```php
$result = match($x) {
    1, 2 => 'small',
    3, 4 => 'medium',
    default => 'large',
};
```

### 6. Static Return Type
```php
class Builder {
    public static function create(): static {
        return new static();
    }
}
```

### 7. Weak Comparisons Fixed
```php
// PHP 7: Unexpected behavior
0 == 'foo';      // true (yikes!)
0 === 'foo';     // false

// PHP 8: More predictable
0 == 'foo';      // false (fixed!)
0 === 'foo';     // false
```

## Breaking Changes

### Important Changes for Migration

**1. Type Juggling**
```php
// PHP 7: May work with type coercion
$x = '5' + 2;  // 7

// PHP 8: Still works, but be explicit
$x = (int)'5' + 2;  // Better
```

**2. Null Handling**
```php
// PHP 7: Returns null, no warning
$x = null + 5;

// PHP 8: Returns 5, better error handling
$x = null + 5;  // Better warning
```

**3. Object Comparison**
```php
// Objects compared by reference now
$obj1 = new stdClass();
$obj2 = $obj1;
$obj1 == $obj2;  // Still true
```

**4. Global Variables**
```php
// Less flexible variable variable access
$GLOBALS['x'] = 5;
// Use normal variables instead
$x = 5;
```

## Migration Guide

### Step 1: Check PHP Version
```bash
php -v
# Should show PHP 8.x.x
```

### Step 2: Enable strict_types (Recommended)
```php
declare(strict_types=1);

// Put at top of every file for stricter typing
```

### Step 3: Review Code for Breaking Changes

Look for:
- Type coercion issues
- Error suppression operators (@)
- Variable variables
- Global state manipulation

### Step 4: Update Type Declarations
```php
// Old
public function getName() {
    return $this->name;
}

// New - explicit
public function getName(): string {
    return $this->name;
}
```

### Step 5: Use New Syntax Where Beneficial

```php
// Constructor property promotion
public function __construct(private string $name) {}

// Named arguments
makeCoffee(size: 'large', type: 'espresso');

// Match expression
$category = match($age) {
    0..12 => 'child',
    13..17 => 'teen',
    default => 'adult',
};
```

## Learning Path

Master PHP 8 features progressively:

1. **[Overview](1-install.md)** - What's new in PHP 8
2. **[Named Arguments](2-named-argument.md)** - Function parameter names
3. **[Constructor Promotion](3-constructor-property-promotion.md)** - Shorter classes
4. **[Union Types](5-union-types.md)** - Multiple type support
5. **[Match Expression](6-match-expression.md)** - Better switch
6. **[Nullsafe Operator](7-nullsafe-operator.md)** - Safe navigation
7. **[Type Improvements](8-string-to-number-comparison.md)** - Better type handling
8. **[Attributes](4-attributes.md)** - Code metadata
9. **[Advanced Features](15-throw-expression.md)** - More improvements

## Prerequisites

Before learning PHP 8:

✅ **Required:**
- PHP 7.4 fundamentals
- Object-oriented programming
- Type declarations basics

✅ **Helpful:**
- Understanding of type systems
- Experience with modern languages (Python, TypeScript, etc.)

## Quick Comparison Table

| Feature | PHP 7.4 | PHP 8.0 |
|---------|---------|---------|
| Named Arguments | ❌ | ✅ |
| Union Types | ❌ | ✅ |
| Match Expression | ❌ | ✅ |
| Constructor Promotion | ❌ | ✅ |
| Nullsafe Operator | ❌ | ✅ |
| Attributes | ❌ | ✅ |
| JIT Compilation | ❌ | ✅ |

## Common Questions

**Q: Should I upgrade to PHP 8?**
A: Yes, if you're starting new projects. It's more modern and secure.

**Q: Will my PHP 7 code break?**
A: Mostly no, but some edge cases may need updates. Test thoroughly.

**Q: Is PHP 8 slower?**
A: No, it's actually faster (~10-15% improvement).

**Q: Should I use all the new features?**
A: Use them where they improve clarity. Don't force them everywhere.

## Next Steps

1. Understand [Named Arguments](2-named-argument.md) - Most impactful feature
2. Learn [Union Types](5-union-types.md) - Essential for type safety
3. Master [Match Expression](6-match-expression.md) - Replaces switch
4. Explore [Constructor Promotion](3-constructor-property-promotion.md) - Write less code
5. Continue with other features as needed

## Resources

- **Official Upgrade Guide**: [php.net/manual/en/migration80.php](https://www.php.net/manual/en/migration80.php)
- **PHP 8 Features**: [PHP 8.0 Release Notes](https://www.php.net/releases/8.0/)
- **Type Declarations**: [php.net/manual/en/language.types.declarations.php](https://www.php.net/manual/en/language.types.declarations.php)
