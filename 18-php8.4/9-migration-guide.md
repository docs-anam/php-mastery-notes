# Migration Guide: PHP 8.3 to PHP 8.4

## Overview

Step-by-step guide to upgrading your PHP 8.3 application to PHP 8.4, including new feature adoption, compatibility considerations, and best practices.

---

## Table of Contents

1. Pre-Migration Checklist
2. Installation and Setup
3. New Features to Adopt
4. Breaking Changes
5. Deprecations
6. Code Modernization
7. Testing and Validation
8. Troubleshooting
9. Complete Migration Example

---

## Pre-Migration Checklist

### Before Upgrading

```
□ Backup your application
□ Review PHP 8.4 changelog
□ Check dependency compatibility
□ Verify extension support
□ Plan downtime (if needed)
□ Set up test environment
□ Update development tools
□ Review team capabilities
```

### Version Compatibility Check

```php
<?php
// Verify current PHP version
if (PHP_VERSION_ID < 80300) {
    echo "PHP 8.3 or later required\n";
    exit(1);
}

// Check for required extensions
$extensions = ['spl', 'reflection', 'json'];
foreach ($extensions as $ext) {
    if (!extension_loaded($ext)) {
        echo "Missing extension: $ext\n";
    }
}

// Check PHP 8.4 readiness
$php84Ready = [
    'version' => version_compare(PHP_VERSION, '8.4.0', '>='),
    'opcache' => extension_loaded('opcache'),
    'memory' => ini_get('memory_limit'),
];

echo "PHP 8.4 Readiness:\n";
foreach ($php84Ready as $check => $status) {
    echo "  $check: " . ($status ? "OK" : "CHECK") . "\n";
}
```

---

## Installation and Setup

### Install PHP 8.4

```bash
# macOS with Homebrew
brew install php@8.4
brew link php@8.4

# Ubuntu/Debian
sudo apt-add-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.4 php8.4-common php8.4-cli php8.4-fpm

# Windows (using Chocolatey)
choco install php --version=8.4.0

# Docker
docker pull php:8.4-fpm
docker pull php:8.4-cli
```

### Configure PHP 8.4

```ini
# php.ini for PHP 8.4
[PHP]
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
memory_limit = 256M

[opcache]
opcache.enable = 1
opcache.memory_consumption = 256
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 0

[opcache.jit]
opcache.jit = tracing
opcache.jit_buffer_size = 256M

[security]
expose_php = Off
error_reporting = E_ALL
```

### Update Dependencies

```bash
# Update Composer
composer update

# Check compatibility
composer diagnose

# Run security audit
composer audit
```

---

## New Features to Adopt

### 1. Use Property Hooks

```php
<?php
// Before PHP 8.4
class OldStyle
{
    private float $price = 0;

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $value): void
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Price cannot be negative');
        }
        $this->price = $value;
    }
}

// After PHP 8.4 - Adopt hooks
class NewStyle
{
    public float $price {
        get => $this->price;
        set => $this->price = max(0.0, $value);
    }
}

// Migration path:
// 1. Identify getter/setter pairs
// 2. Convert to property hooks
// 3. Remove getter/setter methods
// 4. Test thoroughly
```

### 2. Leverage Asymmetric Visibility

```php
<?php
// Before: Public property with no control
class Old
{
    public string $email = '';
}

// After: Public read, private write
class New
{
    public private(set) string $email = '';

    public function changeEmail(string $newEmail): void
    {
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email');
        }
        $this->email = $newEmail;
    }
}

// Benefits:
// ✓ Better encapsulation
// ✓ Enforces validation
// ✓ Cleaner than getter/setter methods
```

### 3. Add Class Constant Visibility

```php
<?php
// Before: All constants public
class OldConfig
{
    const API_KEY = 'secret';
    const TIMEOUT = 30;
}

// After: Hide implementation details
class NewConfig
{
    public const int TIMEOUT = 30;
    private const string API_KEY = 'secret';

    public static function getSettings(): array
    {
        return ['timeout' => self::TIMEOUT];
    }
}

// Migration strategy:
// 1. Review all constants
// 2. Determine appropriate visibility
// 3. Add visibility modifiers
// 4. Update access patterns
```

### 4. Enhance Type System

```php
<?php
// Before: Basic types
function process(mixed $data)
{
    return $data;
}

// After: More specific types
function process(array|stdClass $data): array|object
{
    return match(true) {
        is_array($data) => $data,
        default => (array)$data,
    };
}

// Advanced: Intersection types
function handle((Logger&Serializable)&Countable $service): void
{
    $service->log('processing');
    $count = count($service);
}
```

---

## Breaking Changes

### Strict Type Behavior

```php
<?php
// Some type coercions are stricter in PHP 8.4

// String to numeric comparison
// PHP 8.3: "10" == 10 (true with loose comparison)
// PHP 8.4: Same behavior, but use strict===

// Good practice: Always use strict comparison
if ($value === 10) {
    // Type-safe
}

// Object property behavior
class Example
{
    // Accessing non-existent property may behave differently
    public private(set) string $prop = 'default';
}
```

### Property Access Changes

```php
<?php
// Property hooks affect access patterns

// Before:
class Old
{
    public $value = 0;

    public function getValue()
    {
        return $this->value;
    }
}

// After:
class New
{
    public int $value {
        get => $this->value;
    }
}

// Serialization may differ
$object = new New();
$serialized = serialize($object);
// Hooks affect how properties are serialized
```

---

## Deprecations

### Check for Deprecated Features

```php
<?php
// Some features may be deprecated

// Enable deprecation warnings
error_reporting(E_ALL);

// Check for deprecated function usage
$deprecated = [
    // Add any deprecated features from PHP 8.4
];

// Recommended to use static analysis
// phpstan --configuration=phpstan.neon src/
// psalm --config=psalm.xml src/
```

---

## Code Modernization

### Update Class Structure

```php
<?php
// Modernize classes for PHP 8.4

class ModernizedUser
{
    // Use readonly for immutable properties
    public readonly int $id;
    public readonly DateTimeImmutable $createdAt;

    // Use hooks for computed/validated properties
    public string $email {
        get => $this->email;
        set => $this->email = $this->validateEmail($value);
    }

    // Use asymmetric visibility for controlled access
    public private(set) array $roles = [];

    // Use class constant visibility
    public const string DEFAULT_ROLE = 'user';
    private const string VALIDATION_PATTERN = '/^[a-z0-9]+@/';

    public function __construct(int $id, string $email)
    {
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
        $this->email = $email;
    }

    private function validateEmail(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email');
        }
        return strtolower($email);
    }

    public function addRole(string $role): void
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }
}

// Usage
$user = new ModernizedUser(1, 'john@example.com');
echo $user->email;              // ✓ Readable
// $user->email = 'invalid';    // ✗ Cannot set directly
$user->addRole('admin');        // ✓ Controlled modification
echo ModernizedUser::DEFAULT_ROLE;  // ✓ Public constant
```

### Refactor Methods to Hooks

```php
<?php
// Identify candidates for hook conversion

// Before: Getter/setter pair
class Before
{
    private float $value;

    public function getValue(): float
    {
        return $this->value * 1.1;
    }

    public function setValue(float $v): void
    {
        $this->value = max(0, $v);
    }
}

// After: Use hooks
class After
{
    public float $value {
        get => $this->value * 1.1;
        set => $this->value = max(0, $value);
    }
}

// Refactoring checklist:
// 1. Find all getter methods (public, no parameters)
// 2. Find matching setter methods
// 3. Convert to hooks
// 4. Remove old methods
// 5. Test thoroughly
// 6. Update client code
```

---

## Testing and Validation

### Update Test Suite

```php
<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class MigrationTests extends TestCase
{
    public function testPropertyHooks(): void
    {
        $object = new class {
            public int $value {
                get => $this->value;
                set => $this->value = max(0, $value);
            }
        };

        $object->value = 100;
        $this->assertSame(100, $object->value);

        $object->value = -50;
        $this->assertSame(0, $object->value);  // Constrained
    }

    public function testAsymmetricVisibility(): void
    {
        $class = new class {
            public private(set) string $id = 'test';
        };

        // Can read
        $this->assertSame('test', $class->id);

        // Cannot write from outside
        $this->expectError();
        $class->id = 'new';
    }

    public function testClassConstantVisibility(): void
    {
        $class = new class {
            public const string PUBLIC = 'public';
            private const string PRIVATE = 'private';
        };

        // Can access public
        $this->assertSame('public', $class::PUBLIC);

        // Cannot access private
        $this->expectError();
        $_ = $class::PRIVATE;
    }

    public function testTypeSystem(): void
    {
        $func = function(int|string $value): string {
            return (string)$value;
        };

        $this->assertSame('42', $func(42));
        $this->assertSame('test', $func('test'));
    }
}
```

### Static Analysis Configuration

```neon
# phpstan.neon
parameters:
    level: max
    paths:
        - src
        - tests
    
    php:
        version: '8.4'
    
    treatPhpDocTypesAsCertain: false
    checkModelProperties: true
    checkUninitializedProperties: true
```

---

## Troubleshooting

### Common Migration Issues

```php
<?php
// Issue 1: Property hook syntax errors
// Error: Parse error in property definition

// Solution: Check hook syntax
class Fixed
{
    // Correct syntax
    public int $value {
        get => $this->value;
        set => $this->value = $value;
    }
}

// Issue 2: Asymmetric visibility errors
// Error: Cannot set private property

// Solution: Check visibility modifiers
class Correct
{
    public private(set) string $id = '';
}

$obj = new Correct();
echo $obj->id;              // ✓ Works
// $obj->id = 'new';        // ✗ Fails as expected

// Issue 3: Type narrowing not recognized
// Solution: Use explicit type checks

function handle(int|string|array $value): void
{
    if (is_array($value)) {
        // $value is array here
        count($value);
    } else if (is_string($value)) {
        // $value is string here
        strlen($value);
    }
}
```

---

## Complete Migration Example

### Real-World Migration

```php
<?php
declare(strict_types=1);

namespace App;

// BEFORE: PHP 8.3 code
/*
class Product
{
    private int $id;
    private string $name;
    private float $price;
    private int $stock = 0;
    private array $tags = [];
    public const API_VERSION = '1.0';
    public const INTERNAL_CODE = 'secret';

    public function __construct(int $id, string $name, float $price)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        if ($price < 0) {
            throw new InvalidArgumentException('Price cannot be negative');
        }
        $this->price = $price;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): void
    {
        $this->stock = max(0, $stock);
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function addTag(string $tag): void
    {
        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
    }
}
*/

// AFTER: PHP 8.4 modernized code
class Product
{
    // Readonly immutable properties
    public readonly int $id;
    public readonly DateTimeImmutable $createdAt;

    // Property hooks for computed/validated properties
    public float $price {
        get => $this->price;
        set => $this->price = max(0.0, $value);
    }

    public int $stock {
        get => $this->stock;
        set => $this->stock = max(0, $value);
    }

    // Asymmetric visibility for controlled access
    public private(set) string $name = '';
    public private(set) array $tags = [];

    // Class constant visibility
    public const string API_VERSION = '1.0';
    private const string INTERNAL_CODE = 'secret';

    public function __construct(int $id, string $name, float $price)
    {
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
        $this->name = $name;
        $this->price = $price;
    }

    public function addTag(string $tag): void
    {
        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
    }

    public function display(): string
    {
        return "$this->name - $" . number_format($this->price, 2);
    }
}

// Migration benefits:
// ✓ Less boilerplate code
// ✓ Better encapsulation
// ✓ Cleaner syntax
// ✓ Type safety
// ✓ Performance improvements

// Usage remains similar
$product = new Product(1, 'Widget', 29.99);
echo $product->display();           // Widget - $29.99
echo $product->price;               // 29.99

$product->price = 39.99;            // Uses hook
echo $product->price;               // 39.99

// $product->name = 'changed';      // ✗ Cannot write
$product->addTag('featured');       // ✓ Controlled access

// Performance bonus: 10-15% faster!
```

---

## Migration Timeline

```
Week 1: Preparation
├─ Set up PHP 8.4 environment
├─ Review changelog
├─ Run static analysis
└─ Create test environment

Week 2: Feature Adoption
├─ Refactor to property hooks
├─ Add asymmetric visibility
├─ Add class constant visibility
└─ Adopt new type features

Week 3: Testing
├─ Run full test suite
├─ Update documentation
├─ Security audit
└─ Performance testing

Week 4: Deployment
├─ Staging deployment
├─ Final testing
├─ Monitoring setup
└─ Production deployment
```

---

## See Also

- [PHP 8.4 Overview](0-php8.4-overview.md)
- [Property Hooks](2-property-hooks.md)
- [Asymmetric Visibility](3-asymmetric-visibility.md)
- [Class Constant Visibility](4-class-constant-visibility.md)
- [Type System Improvements](5-type-system.md)
- [Performance Improvements](8-performance-improvements.md)
