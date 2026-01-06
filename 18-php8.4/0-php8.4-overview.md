# PHP 8.4 Overview

## Introduction

PHP 8.4 was released in **November 2024** with major new features focused on object-oriented programming improvements, type system enhancements, and performance optimizations. This guide covers the major features and migration path from PHP 8.3.

---

## Table of Contents

1. Release Information
2. Major Features
3. Type System Enhancements
4. Object-Oriented Improvements
5. Performance Improvements
6. Deprecations and Breaking Changes
7. Comparison with Previous Versions
8. Migration Path
9. System Requirements
10. Learning Path

---

## Release Information

### Timeline and Support

```php
<?php
// PHP 8.4 Release Information
$releaseInfo = [
    'Version' => '8.4.0',
    'Release Date' => 'November 2024',
    'Feature Freeze' => 'August 2024',
    'Bug Fix Release Cycle' => '12 months',
    'End of Life' => 'November 2026',
    'Security Support Until' => 'November 2027',
];

// Check version
if (version_compare(PHP_VERSION, '8.4.0', '>=')) {
    echo "PHP 8.4 or later detected\n";
}

// Key dates
echo "Current Version: " . PHP_VERSION . "\n";
echo "Major Version: " . PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION . "\n";
```

### Upgrade Path Recommendations

```
PHP 8.2 → PHP 8.3 → PHP 8.4
    ↓
- Version 8.4 is recommended for new projects
- Version 8.3 still has 1 year of support (until Nov 2026)
- Version 8.2 still has 2 years of support (until Dec 2026)
- Version 8.1 reached end of life (November 2023)
```

---

## Major Features

### 1. Property Hooks (NEW)

```php
<?php
// Brand new feature in PHP 8.4
// Hooks provide computed properties and validation

class Temperature
{
    private float $celsius;

    // get hook - computes Fahrenheit on read
    public float $fahrenheit {
        get => $this->celsius * 9/5 + 32;
    }

    // set hook - validates input before storing
    public float $value {
        get => $this->celsius;
        set => $this->celsius = max(-273.15, $value);  // Absolute zero check
    }

    public function __construct(float $celsius)
    {
        $this->value = $celsius;
    }
}

// Usage
$temp = new Temperature(25);
echo $temp->fahrenheit;  // 77
$temp->value = -300;      // Set to -273.15 (absolute zero limit)
```

### 2. Asymmetric Visibility (NEW)

```php
<?php
// Set different visibility for get/set operations

class BankAccount
{
    // Readable publicly, but writable only internally
    public private(set) int $balance = 0;

    // Readable only to class, writable internally
    protected private(set) string $accountNumber = '';

    public function deposit(int $amount): void
    {
        $this->balance += $amount;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }
}

// Usage
$account = new BankAccount();
echo $account->balance;        // ✓ Readable
// $account->balance = 500;     // ✗ Error: Cannot write

$account->deposit(500);        // ✓ Allowed
echo $account->getBalance();   // 500
```

### 3. Class Constant Visibility (NEW)

```php
<?php
// Control visibility of class constants (new in 8.4)

class ApiClient
{
    // Public constant
    public const string VERSION = '1.0';

    // Protected constant
    protected const int TIMEOUT = 30;

    // Private constant
    private const string API_KEY = 'secret';

    public function getConfig(): array
    {
        return [
            'version' => self::VERSION,
            'timeout' => self::TIMEOUT,
        ];
    }
}

// Usage
echo ApiClient::VERSION;       // ✓ Accessible
// echo ApiClient::TIMEOUT;    // ✗ Protected
// echo ApiClient::API_KEY;    // ✗ Private

class ChildApiClient extends ApiClient
{
    public function getTimeout(): int
    {
        return self::TIMEOUT;   // ✓ Accessible from child
    }
}
```

### 4. Improved Enum Methods

```php
<?php
// Enums have better method support in 8.4

enum Status: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    // Methods work seamlessly
    public function isPublic(): bool
    {
        return $this === Status::PUBLISHED;
    }

    public function isFinal(): bool
    {
        return in_array($this, [Status::PUBLISHED, Status::ARCHIVED]);
    }

    // Readonly properties in enums
    #[ReadOnly]
    public string $displayName;

    public function __construct(public readonly string $value)
    {
        $this->displayName = ucfirst($this->value);
    }
}
```

### 5. Improved Type System

```php
<?php
// Better type narrowing and intersection types

class DataProcessor
{
    // Intersection types (combined requirements)
    public function processData(Countable&Traversable $data): void
    {
        $count = count($data);  // Countable interface
        foreach ($data as $item) {  // Traversable interface
            // Process
        }
    }

    // Disjunctive Normal Form (DNF) types - cleaner syntax
    public function handleValue((stdClass&Serializable)|(int&float) $value): void
    {
        // Handle complex type requirements
    }

    // Better null coalescing and match expressions
    public function getValue(?array $data): string
    {
        return match(true) {
            empty($data) => 'empty',
            array_is_list($data) => 'list',
            default => 'associative',
        };
    }
}
```

---

## Type System Enhancements

### Type Narrowing Improvements

```php
<?php
// Better type narrowing in PHP 8.4

function processData(mixed $data): void
{
    if (is_array($data) && array_is_list($data)) {
        // $data is now known to be list
        foreach ($data as $index => $item) {
            echo $index;  // Guaranteed integer
        }
    }

    if ($data instanceof \ArrayObject) {
        // Type is narrowed
        $data->asort();
    }

    if (is_numeric($data)) {
        // Numeric type narrowed
        echo $data * 2;
    }
}

// Readonly improvements
class Configuration
{
    public readonly string $host;
    public readonly int $port;
    public readonly array $options;

    public function __construct(string $host, int $port, array $options)
    {
        $this->host = $host;
        $this->port = $port;
        $this->options = $options;
    }
}
```

### Union and Intersection Types

```php
<?php
// Better support for complex type combinations

interface Logger
{
    public function log(string $message): void;
}

interface Serializable
{
    public function serialize(): string;
}

class Service
{
    // Union type (either type is acceptable)
    public function process(array|string $data): void
    {
        if (is_array($data)) {
            // Process array
        } else {
            // Process string
        }
    }

    // Intersection type (must implement both)
    public function handleLogger(Logger&Serializable $logger): void
    {
        $logger->log('message');
        echo $logger->serialize();
    }

    // Union with null
    public function getData(): string|int|null
    {
        return null;
    }
}
```

---

## Object-Oriented Improvements

### Property Hooks Deep Dive

```php
<?php
// Property hooks enable computed properties

class User
{
    private string $firstName = '';
    private string $lastName = '';

    // Computed property that combines first and last name
    public string $fullName {
        get => trim($this->firstName . ' ' . $this->lastName);
        set {
            $parts = explode(' ', $value, 2);
            $this->firstName = $parts[0] ?? '';
            $this->lastName = $parts[1] ?? '';
        }
    }

    public string $email {
        get => $this->email;
        set => $this->email = strtolower($value);
    }
}

// Usage
$user = new User();
$user->fullName = "John Doe";
echo $user->fullName;  // "John Doe"
```

### Asymmetric Visibility Benefits

```php
<?php
// Encapsulation without boilerplate

class Product
{
    // Price is readable but controlled through method
    public private(set) float $price = 0;

    // Quantity is internal only
    private int $quantity = 0;

    public function updatePrice(float $newPrice): void
    {
        if ($newPrice < 0) {
            throw new InvalidArgumentException('Price cannot be negative');
        }
        $this->price = $newPrice;
    }

    public function reducePrice(float $percentage): void
    {
        $this->price *= (1 - $percentage / 100);
    }
}

$product = new Product();
echo $product->price;           // ✓ Readable
// $product->price = 100;       // ✗ Cannot set
$product->updatePrice(99.99);  // ✓ Allowed
```

---

## Performance Improvements

### Expected Performance Gains

```php
<?php
// PHP 8.4 shows 10-18% improvement over 8.3

$performanceData = [
    'Property hooks' => '+15-20% for computed properties',
    'Type checking' => '+5-10% faster',
    'JIT improvements' => '+10-15% for arithmetic',
    'Array operations' => '+8-12% faster',
    'JSON operations' => '+18-25% faster',
];

echo "Expected improvements:\n";
foreach ($performanceData as $feature => $improvement) {
    echo "  $feature: $improvement\n";
}

// Property hook performance
class ComputedProperty
{
    private float $value = 0;

    public float $squared {
        get => $this->value ** 2;  // Computed on access
    }

    public float $doubled {
        get => $this->value * 2;
    }
}
```

### Memory Efficiency

```php
<?php
// Improved memory handling in 8.4

class EfficientStorage
{
    // Readonly properties use less memory
    public readonly string $id;
    public readonly string $data;

    // Lazy initialization possible with hooks
    private ?array $config = null;

    public array $configuration {
        get => $this->config ??= $this->loadConfig();
    }

    private function loadConfig(): array
    {
        // Load only when accessed
        return [];
    }
}
```

---

## Deprecations and Breaking Changes

### Removed in PHP 8.4

```php
<?php
// Features removed from PHP 8.4

// 1. Some ctype functions behavior changed
// 2. mb_ereg behavior changes
// 3. Serializable interface changes

// Check for deprecated functions
function checkDeprecations(): void
{
    $deprecated = [
        'ereg' => 'Use preg_match instead',
        'split' => 'Use preg_split instead',
        'mysql_*' => 'Use mysqli or PDO instead',
    ];

    echo "Deprecated functions:\n";
    foreach ($deprecated as $func => $replacement) {
        echo "  $func -> $replacement\n";
    }
}
```

### Type System Changes

```php
<?php
// Stricter type checking in some scenarios

// Property hooks affect object serialization
class HookExample
{
    private string $value = '';

    public string $computed {
        get => strtoupper($this->value);
        set => $this->value = strtolower($value);
    }

    // Note: Serialization of computed properties requires care
}
```

---

## Version Comparison

### PHP 8.0 to 8.4 Features

```
Feature                    | 8.0 | 8.1 | 8.2 | 8.3 | 8.4 |
--------------------------|-----|-----|-----|-----|-----|
Named Arguments            |  ✓  |  -  |  -  |  -  |  ✓  |
Union Types               |  ✓  |  -  |  -  |  -  |  ✓  |
Attributes                |  ✓  |  -  |  -  |  -  |  ✓  |
Match Expression          |  ✓  |  -  |  -  |  -  |  ✓  |
Readonly Properties       |  ✓  |  ✓  |  ✓  |  ✓  |  ✓  |
Enums                     |  -  |  ✓  |  -  |  -  |  ✓  |
Readonly Classes          |  -  |  ✓  |  -  |  -  |  ✓  |
Disjunctive Normal Form   |  -  |  -  |  ✓  |  -  |  ✓  |
Typed Constants           |  -  |  -  |  -  |  ✓  |  ✓  |
#[Override]               |  -  |  -  |  -  |  ✓  |  ✓  |
json_validate()           |  -  |  -  |  -  |  ✓  |  ✓  |
array_is_list()           |  -  |  -  |  -  |  ✓  |  ✓  |
Property Hooks            |  -  |  -  |  -  |  -  |  ✓  |
Asymmetric Visibility     |  -  |  -  |  -  |  -  |  ✓  |
Const Visibility          |  -  |  -  |  -  |  -  |  ✓  |
```

---

## Migration Path from PHP 8.3

### Recommended Upgrade Steps

```php
<?php
// 1. Check version compatibility
if (PHP_VERSION_ID < 80400) {
    echo "PHP 8.4+ required\n";
}

// 2. Review property hooks - new feature
// 3. Review asymmetric visibility - new feature
// 4. Update class constants to use visibility
// 5. Test thoroughly
// 6. Deploy to staging
// 7. Performance test
// 8. Deploy to production

// Safe migration example
class LegacyCode
{
    // Old way (still works)
    public $value = 0;

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($v)
    {
        $this->value = $v;
    }
}

class ModernCode
{
    // New way with hooks and asymmetric visibility
    public int $value {
        get => $this->value;
        set => $this->value = max(0, $value);  // Validation
    }
}
```

---

## System Requirements

### Minimum Requirements for PHP 8.4

```php
<?php
// System requirements
$requirements = [
    'PHP Version' => '8.4.0',
    'Memory' => '128 MB minimum (256 MB recommended)',
    'Disk Space' => '20 MB minimum',
    'Extensions' => [
        'SPL' => 'required',
        'Reflection' => 'required',
        'json' => 'required',
        'date' => 'required',
        'pcre' => 'required',
    ],
];

// Recommended extensions
$recommended = [
    'OPCache' => 'For performance (50%+ faster)',
    'JIT' => 'For arithmetic operations',
    'curl' => 'For HTTP requests',
    'mbstring' => 'For Unicode handling',
    'pdo' => 'For database access',
];
```

---

## Learning Path

### Suggested Learning Order

```
1. Basic Concepts (Review from 8.3)
   ├─ Type system
   ├─ OOP fundamentals
   └─ Attributes

2. New Features (PHP 8.4)
   ├─ Property Hooks
   ├─ Asymmetric Visibility
   ├─ Class Constant Visibility
   └─ Improved Type System

3. Advanced Topics
   ├─ Performance Optimization
   ├─ Migration from 8.3
   └─ Best Practices

4. Practical Application
   ├─ Real-world examples
   ├─ Integration patterns
   └─ Enterprise patterns
```

---

## Key Takeaways

### PHP 8.4 Highlights

✅ **Property Hooks** - Computed and validated properties
✅ **Asymmetric Visibility** - Different read/write access levels
✅ **Class Constant Visibility** - Control constant access
✅ **Type System** - Better union/intersection support
✅ **Performance** - 10-18% faster overall
✅ **Backward Compatible** - 8.3 code mostly works unchanged

### Next Steps

1. Review property hooks documentation
2. Understand asymmetric visibility patterns
3. Plan migration from PHP 8.3
4. Set up PHP 8.4 development environment
5. Test your codebase thoroughly

---

## See Also

- [Property Hooks](2-property-hooks.md)
- [Asymmetric Visibility](3-asymmetric-visibility.md)
- [Class Constant Visibility](4-class-constant-visibility.md)
- [Type System Improvements](5-type-system.md)
- [Performance Improvements](8-performance-improvements.md)
- [Migration Guide](9-migration-guide.md)
