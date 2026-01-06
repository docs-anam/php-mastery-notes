# Migration Guide: PHP 8.2 to PHP 8.3

## Overview

Step-by-step guide to upgrading your PHP 8.2 application to PHP 8.3, including code changes, deprecations, and compatibility considerations.

---

## Table of Contents

1. Pre-Migration Checklist
2. Installation and Setup
3. Code Changes Required
4. Deprecations and Breaking Changes
5. Testing and Validation
6. Performance Optimization
7. Troubleshooting
8. Complete Migration Example

---

## Pre-Migration Checklist

### Before You Start

```php
<?php
// Check current PHP version
echo "Current PHP version: " . PHP_VERSION . "\n";

// Verify PHP 8.2 compatibility
$requirements = [
    'PHP Version' => version_compare(PHP_VERSION, '8.2.0', '>='),
    'Extensions' => extension_loaded('json') && extension_loaded('spl'),
    'Memory Limit' => ini_get('memory_limit'),
    'Max Execution Time' => ini_get('max_execution_time'),
];

foreach ($requirements as $requirement => $status) {
    echo "$requirement: " . ($status ? "OK" : "MISSING") . "\n";
}

// Check for deprecated features
$deprecated = [
    'Dynamic properties' => true,  // Deprecated in 8.2
    'strtoupper(null)' => false,   // Deprecated in 8.2
];
```

### Pre-Migration Tasks

```
1. ✓ Backup your application
2. ✓ Create a test environment
3. ✓ Review changelog (php.net/8.3)
4. ✓ Check dependency compatibility
5. ✓ Run static analysis (PHPStan, Psalm)
6. ✓ Update development tools
7. ✓ Review deprecation warnings
```

---

## Installation and Setup

### Install PHP 8.3

```bash
# macOS with Homebrew
brew install php@8.3
brew link php@8.3

# Ubuntu/Debian
sudo apt-add-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.3 php8.3-common php8.3-cli php8.3-fpm

# Windows (using Chocolatey)
choco install php --version=8.3.0

# Docker
docker pull php:8.3-fpm
docker pull php:8.3-cli
```

### Configure PHP 8.3

```ini
# php.ini configuration for PHP 8.3
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log

; OPCache for performance
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000

; JIT compilation (recommended)
opcache.jit=tracing
opcache.jit_buffer_size=256M

; Security
expose_php = Off
error_reporting = E_ALL
```

### Update Composer

```bash
# Update Composer dependencies
composer update

# Run diagnosis
composer diagnose

# Check for version conflicts
composer require-dev phpstan/phpstan
```

---

## Code Changes Required

### 1. Implement Typed Constants

```php
<?php
// Before (PHP 8.2)
class Config
{
    const DEFAULT_TIMEOUT = 30;
    const DEFAULT_RETRIES = 3;
    const ALLOWED_HOSTS = ['localhost', 'example.com'];
}

// After (PHP 8.3) - Add type declarations
class Config
{
    const int DEFAULT_TIMEOUT = 30;
    const int DEFAULT_RETRIES = 3;
    const array ALLOWED_HOSTS = ['localhost', 'example.com'];
}

// Benefits:
// ✓ Type safety
// ✓ IDE support
// ✓ Static analysis
// ✓ Runtime validation
```

### 2. Add #[Override] Attribute

```php
<?php
// Before (PHP 8.2) - Risk of typos
class BaseController
{
    public function handleRequest(): void
    {
        // ...
    }
}

class UserController extends BaseController
{
    // Typo! Method not actually overriding parent
    public function handlRequest(): void
    {
        // ...
    }
}

// After (PHP 8.3) - Catch typos
class BaseController
{
    public function handleRequest(): void
    {
        // ...
    }
}

class UserController extends BaseController
{
    #[Override]
    public function handleRequest(): void
    {
        // Attribute ensures this overrides parent
    }

    // This would throw error - method doesn't exist in parent
    // #[Override]
    // public function handlRequest(): void { }
}
```

### 3. Use json_validate() Instead of Decode+Check

```php
<?php
// Before (PHP 8.2) - Expensive operation
$json = file_get_contents('input.json');
$decoded = json_decode($json);
if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
    // Invalid JSON
}

// After (PHP 8.3) - Lightweight validation
$json = file_get_contents('input.json');
if (!json_validate($json)) {
    // Invalid JSON
    exit;
}
$decoded = json_decode($json, true);  // Now safe to decode

// Performance improvement:
// ✓ 50-70% faster for validation only
// ✓ Prevents unnecessary memory allocation
// ✓ Perfect for input validation
```

### 4. Use New Random API

```php
<?php
// Before (PHP 8.2) - Using deprecated functions
$random = random_int(1, 100);
$token = bin2hex(random_bytes(32));

// After (PHP 8.3) - Using Random\Randomizer
$randomizer = new Random\Randomizer();
$random = $randomizer->nextInt(1, 100);
$token = bin2hex($randomizer->getBytes(32));

// Crypto-secure version
$secureRandomizer = new Random\Randomizer(new Random\Engine\Secure());
$secureToken = bin2hex($secureRandomizer->getBytes(32));

// Benefits:
// ✓ More explicit about security level
// ✓ Better type safety
// ✓ Configurable entropy source
```

### 5. Leverage Enum Improvements

```php
<?php
// Before (PHP 8.2) - Limited enum support
enum Status: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}

// After (PHP 8.3) - Add methods and features
enum Status: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function getDisplayName(): string
    {
        return match($this) {
            Status::PENDING => 'Pending Review',
            Status::APPROVED => 'Approved',
            Status::REJECTED => 'Rejected',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [Status::APPROVED, Status::REJECTED]);
    }
}

// Usage
$status = Status::APPROVED;
echo $status->getDisplayName();  // "Approved"
echo $status->value;              // "approved"
```

### 6. Use array_is_list()

```php
<?php
// Before (PHP 8.2) - Manual checking
function processArray(array $data): void
{
    $isSequential = true;
    $lastKey = -1;

    foreach ($data as $key => $value) {
        if ($key !== $lastKey + 1) {
            $isSequential = false;
            break;
        }
        $lastKey = $key;
    }

    if ($isSequential) {
        // Process as list
    } else {
        // Process as associative
    }
}

// After (PHP 8.3) - Clean and clear
function processArray(array $data): void
{
    if (array_is_list($data)) {
        // Process as list
    } else {
        // Process as associative
    }
}
```

---

## Deprecations and Breaking Changes

### Removed Features

```php
<?php
// 1. DOMDocument::save() now returns false on error
//    (Previously returned 0)

// 2. SOAP changes
// - soap.wsdl_cache_dir removed
// - soap.wsdl_cache_mode removed

// 3. CLI changes
// - Interactive shell features removed

// Check documentation for your specific use cases
```

### Type Strictness Changes

```php
<?php
// Stricter type coercion in some scenarios
// This may affect comparisons

// Example: String to number comparison
// PHP 8.2: "10" == 10 (true)
// PHP 8.3: "10" == 10 (true, but use strict ===)

// Best practice: Always use strict comparison
$value = "10";
if ($value === 10) {
    // Type-safe comparison
}
```

---

## Testing and Validation

### Update Your Tests

```php
<?php
// Update test suite for PHP 8.3
namespace Tests;

use PHPUnit\Framework\TestCase;

class MigrationTest extends TestCase
{
    public function testTypedConstantsAreEnforced(): void
    {
        $config = new \Config();

        // Type is enforced
        $this->assertIsInt($config::DEFAULT_TIMEOUT);

        // This would cause an error now
        // $config::DEFAULT_TIMEOUT = "invalid";
    }

    public function testJsonValidationWorks(): void
    {
        $valid = '{"key": "value"}';
        $invalid = '{invalid json}';

        $this->assertTrue(json_validate($valid));
        $this->assertFalse(json_validate($invalid));
    }

    public function testRandomizerWorks(): void
    {
        $randomizer = new \Random\Randomizer();
        $number = $randomizer->nextInt(1, 100);

        $this->assertGreaterThanOrEqual(1, $number);
        $this->assertLessThanOrEqual(100, $number);
    }

    public function testOverrideAttributeWorks(): void
    {
        // Check that #[Override] is properly used
        $reflection = new \ReflectionClass(\UserController::class);
        $method = $reflection->getMethod('handleRequest');

        // Verify override attribute exists
        $attributes = $method->getAttributes(\Override::class);
        $this->assertNotEmpty($attributes);
    }
}
```

### Static Analysis Configuration

```php
<?php
// phpstan.neon configuration
parameters:
    level: max
    paths:
        - src
        - tests
    
    excludePaths:
        - src/Cache
        - src/Generated

    php:
        version: '8.3'

    treatPhpDocTypesAsCertain: false

    checkModelProperties: true
    checkUninitializedProperties: true

// Run analysis
// vendor/bin/phpstan analyse src/
```

---

## Performance Optimization

### Optimize for PHP 8.3

```php
<?php
class PerformanceOptimization
{
    // 1. Enable JIT compilation
    // Set in php.ini: opcache.jit=tracing

    // 2. Use typed properties
    private int $count = 0;
    private string $name = '';
    private array $items = [];

    // 3. Cache expensive operations
    private ?array $cache = null;

    public function getData(): array
    {
        return $this->cache ??= $this->computeData();
    }

    // 4. Use native functions
    public function processItems(array $items): array
    {
        return array_map(fn($i) => $i * 2, $items);
    }

    // 5. Leverage array_is_list
    public function handleArray(array $data): void
    {
        if (array_is_list($data)) {
            foreach ($data as $index => $item) {
                // Optimize for list
            }
        }
    }

    // 6. Use json_validate before decode
    public function validateAndDecode(string $json): ?array
    {
        if (!json_validate($json)) {
            return null;
        }
        return json_decode($json, true);
    }

    private function computeData(): array
    {
        // Expensive operation
        return [];
    }
}
```

---

## Troubleshooting

### Common Migration Issues

```php
<?php
// Issue 1: Type mismatch in constants
// Error: Cannot assign string to const int

// Solution: Update constant value to correct type
class Config
{
    const int TIMEOUT = 30;      // ✓ Correct
    // const int TIMEOUT = "30";  // ✗ Error
}

// Issue 2: #[Override] attribute errors
// Error: Method does not override parent method

// Solution: Check parent class for correct method name
class Child extends Parent
{
    #[Override]
    public function methodName(): void  // Ensure name matches parent
    {
        //
    }
}

// Issue 3: json_validate vs json_decode
// Error: Mixing validation logic

// Solution: Validate first, then decode
if (json_validate($json)) {
    $data = json_decode($json, true);
}

// Issue 4: Random API changes
// Error: Using old random_int/random_bytes

// Solution: Use Random\Randomizer
$randomizer = new \Random\Randomizer();
$int = $randomizer->nextInt(1, 100);
$bytes = $randomizer->getBytes(32);
```

---

## Complete Migration Example

### Real-World Migration Scenario

```php
<?php
declare(strict_types=1);

namespace App;

// BEFORE: PHP 8.2 compatible code
/*
class UserService
{
    const DEFAULT_ROLE = 'user';
    const DEFAULT_TIMEOUT = 30;

    public function createUser(array $data): User
    {
        if (!isset($data['name'], $data['email'])) {
            throw new \InvalidArgumentException('Missing required fields');
        }

        $this->validateEmail($data['email']);

        // Manual validation
        $isSequential = true;
        foreach ($data['items'] ?? [] as $key => $item) {
            if (!is_int($key) || $key !== count($items) - 1) {
                $isSequential = false;
                break;
            }
        }

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = self::DEFAULT_ROLE;

        return $user;
    }

    public function processJsonData(string $json): array
    {
        $decoded = json_decode($json, true);
        if ($decoded === null) {
            throw new \Exception('Invalid JSON');
        }

        return $decoded;
    }

    public function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
*/

// AFTER: PHP 8.3 optimized code
class UserService
{
    // Typed constants
    const string DEFAULT_ROLE = 'user';
    const int DEFAULT_TIMEOUT = 30;

    private Random\Randomizer $randomizer;

    public function __construct()
    {
        $this->randomizer = new Random\Randomizer(new Random\Engine\Secure());
    }

    public function createUser(array $data): User
    {
        if (!isset($data['name'], $data['email'])) {
            throw new \InvalidArgumentException('Missing required fields');
        }

        $this->validateEmail($data['email']);

        // Use array_is_list
        $items = $data['items'] ?? [];
        if (array_is_list($items)) {
            // Safe to process as list
            foreach ($items as $index => $item) {
                // Process with index
            }
        }

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = self::DEFAULT_ROLE;

        return $user;
    }

    public function processJsonData(string $json): array
    {
        // Use json_validate for lightweight check
        if (!json_validate($json)) {
            throw new \Exception('Invalid JSON');
        }

        return json_decode($json, true);
    }

    public function generateToken(): string
    {
        // Use Random\Randomizer
        return bin2hex($this->randomizer->getBytes(32));
    }

    private function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }
    }
}

// Usage example
class User
{
    public string $name = '';
    public string $email = '';
    public string $role = 'user';
}

// Migration checklist
$migration = [
    'Typed constants' => true,
    'Override attributes' => false,  // Not used in service layer
    'json_validate' => true,
    'Random\Randomizer' => true,
    'array_is_list' => true,
];

echo "Migration Status:\n";
foreach ($migration as $feature => $done) {
    echo "  $feature: " . ($done ? "✓" : "✗") . "\n";
}
```

---

## Migration Timeline

```
Week 1: Preparation & Testing
├─ Set up test environment
├─ Run static analysis
├─ Identify deprecated features
└─ Review changelog

Week 2: Code Updates
├─ Add typed constants
├─ Add #[Override] attributes
├─ Replace manual validation with json_validate
└─ Update Random API calls

Week 3: Testing & Optimization
├─ Run full test suite
├─ Performance testing
├─ Static analysis
└─ Security review

Week 4: Deployment
├─ Staging deployment
├─ Production preparation
├─ Monitoring setup
└─ Rollback plan
```

---

## See Also

- [PHP 8.3 Overview](0-php8.3-overview.md)
- [Typed Constants](2-typed-constants.md)
- [Override Attribute](3-override-attribute.md)
- [JSON Validation](4-json-validation.md)
- [Random Improvements](5-random-improvements.md)
- [Array Functions](7-array-functions.md)
- [Performance Improvements](8-performance-improvements.md)
