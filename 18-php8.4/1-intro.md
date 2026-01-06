# PHP 8.3 - Typed Constants and Advanced Features

## Table of Contents
1. [Overview](#overview)
2. [Major Features](#major-features)
3. [Feature Details](#feature-details)
4. [Performance & Improvements](#performance--improvements)
5. [Migration from PHP 8.2](#migration-from-php-82)
6. [Learning Path](#learning-path)
7. [Deprecations](#deprecations)

---

## Overview

PHP 8.3 (Released November 2023) brings:

- **Typed Class Constants** - Constants with specific types
- **#[\Override] Attribute** - Verify method overrides
- **json_validate()** - Native JSON validation
- **Randomizer improvements** - Better random number generation
- **Performance improvements** - 8-15% faster
- **Deprecation cleanups** - More robust code

**Release Cycle:** Bug fixes until November 2024, Security fixes until November 2025

---

## Major Features

### 1. Typed Class Constants

Constants can now have explicit types:

**Before PHP 8.3 (Untyped Constants):**
```php
<?php
class Config {
    const DATABASE_HOST = 'localhost';      // string
    const DATABASE_PORT = 5432;             // int
    const FEATURES_ENABLED = true;          // bool
    const ALLOWED_HOSTS = ['localhost'];    // array
    
    // Problems:
    // - No type checking
    // - Can be overridden with wrong type
    // - IDE doesn't know the type
}

class CustomConfig extends Config {
    // Silent bug: wrong type
    const DATABASE_PORT = 'invalid';  // Should be int!
}
```

**PHP 8.3 (Typed Constants):**
```php
<?php
class Config {
    public const string DATABASE_HOST = 'localhost';
    public const int DATABASE_PORT = 5432;
    public const bool FEATURES_ENABLED = true;
    public const array ALLOWED_HOSTS = ['localhost', '127.0.0.1'];
    
    // Access with type safety
    public static function getDbUri(): string {
        return "postgres://" . self::DATABASE_HOST . ":" . self::DATABASE_PORT;
    }
}

class CustomConfig extends Config {
    // Type error! Can't override with wrong type
    public const int DATABASE_PORT = 'invalid';  // Error!
    public const int DATABASE_PORT = 3306;       // Correct
}

// Usage
echo Config::DATABASE_HOST;  // 'localhost'
echo Config::DATABASE_PORT;  // 5432 (guaranteed int)

// Type is enforced
$port = Config::DATABASE_PORT;  // int
```

**Supported Types for Constants:**

```php
<?php
class TypedConstants {
    // Scalar types
    public const int TIMEOUT = 30;
    public const float VERSION = 1.5;
    public const string NAME = 'MyApp';
    public const bool DEBUG = false;
    
    // Array type
    public const array DEFAULTS = [
        'timeout' => 30,
        'retries' => 3,
    ];
    
    // Union types
    public const int|string ID = 'DEFAULT_ID';
    
    // Nullable
    public const ?string OPTIONAL = null;
}
```

**Real-World Example:**

```php
<?php
class DatabaseConfig {
    public const string HOST = 'localhost';
    public const int PORT = 5432;
    public const string USER = 'admin';
    public const string PASSWORD = 'secret';
    public const int TIMEOUT = 30;
    public const bool SSL = true;
    public const array OPTIONS = [
        'charset' => 'utf8mb4',
        'timezone' => 'UTC',
    ];
}

class Database {
    private ?PDO $connection = null;
    
    public function connect(): void {
        $dsn = sprintf(
            'pgsql:host=%s;port=%d;dbname=mydb',
            DatabaseConfig::HOST,
            DatabaseConfig::PORT,  // Type safe!
        );
        
        $options = DatabaseConfig::OPTIONS + [
            PDO::ATTR_TIMEOUT => DatabaseConfig::TIMEOUT,
        ];
        
        $this->connection = new PDO(
            $dsn,
            DatabaseConfig::USER,
            DatabaseConfig::PASSWORD,
            $options,
        );
    }
}
```

### 2. #[\Override] Attribute

Verify that a method is actually overriding a parent method:

**Problem (Before PHP 8.3):**
```php
<?php
class BaseHandler {
    public function process(): void {
        // Process request
    }
}

class CustomHandler extends BaseHandler {
    // Typo! Not actually overriding
    public function processa(): void {  // Wrong method name
        // This runs instead, parent.process() never called
    }
}

// Silent bug - no error!
$handler = new CustomHandler();
$handler->process();  // Calls parent, not custom logic!
```

**PHP 8.3 (#[\Override]):**
```php
<?php
use \Override;

class BaseHandler {
    public function process(): void {
        echo "Processing";
    }
}

class CustomHandler extends BaseHandler {
    #[Override]
    public function process(): void {  // Correct method name
        echo "Custom processing";
        parent::process();
    }
    
    #[Override]
    public function processa(): void {  // Error! Method doesn't exist in parent
        // Compile error - prevents typos!
    }
}

class ImprovedHandler extends BaseHandler {
    #[Override]
    public function process(): void {  // Always verified
        echo "Better processing";
    }
}
```

**Benefits:**
- Catches refactoring errors
- Documents intent
- Prevents silent bugs
- IDE support

### 3. json_validate() Function

Validate JSON without decoding:

**Before PHP 8.3:**
```php
<?php
function isValidJson(string $json): bool {
    json_decode($json);
    return json_last_error() === JSON_ERROR_NONE;
}

// Works but inefficient
// Decodes JSON just to check validity
$json = file_get_contents('large-file.json');
if (isValidJson($json)) {
    $data = json_decode($json, true);  // Decode again
}
```

**PHP 8.3:**
```php
<?php
// Native, efficient validation
if (json_validate('{"valid": true}')) {
    echo "Valid JSON";
}

// Doesn't decode - just validates
$json = file_get_contents('large-file.json');

if (json_validate($json, depth: 512, flags: JSON_INVALID_UTF8_IGNORE)) {
    $data = json_decode($json, true);  // Only decode when needed
    // Process $data
} else {
    echo "Invalid JSON";
}

// Supports options
json_validate($json, flags: JSON_ALLOW_LEADING_PLUS);
```

### 4. Randomizer Improvements

Better random number generation:

```php
<?php
$randomizer = new Random\Randomizer();

// Get random integers
$int = $randomizer->getInt(1, 100);           // 1-100
$int = $randomizer->getInt(PHP_INT_MIN, PHP_INT_MAX);

// Shuffle arrays
$array = [1, 2, 3, 4, 5];
$randomizer->shuffleArray($array);
// Array is modified in place

// Bytes for cryptography
$bytes = $randomizer->getBytes(16);            // 16 random bytes
$token = bin2hex($bytes);                      // Hex token

// Built-in engines
$randomizer = new Random\Randomizer(
    new Random\Engine\Secure(),                // Default secure
);

// Or
$randomizer = new Random\Randomizer(
    new Random\Engine\Mt19937(seed: 12345),   // Mersenne Twister
);
```

---

## Feature Details

### Enum Methods with Typed Constants

```php
<?php
enum HttpStatus: int {
    case OK = 200;
    case CREATED = 201;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case NOT_FOUND = 404;
    case SERVER_ERROR = 500;
    
    public function description(): string {
        return match($this) {
            self::OK => 'Request successful',
            self::CREATED => 'Resource created',
            self::BAD_REQUEST => 'Invalid request',
            self::UNAUTHORIZED => 'Authentication required',
            self::NOT_FOUND => 'Resource not found',
            self::SERVER_ERROR => 'Internal server error',
        };
    }
}

// Enums with typed constants
class Response {
    public const int SUCCESS = 1;
    public const int ERROR = 0;
    
    public function __construct(
        public readonly int STATUS,  // Typed constant
        public readonly string MESSAGE,
    ) {}
}
```

### Array Constants with Complex Types

```php
<?php
class Settings {
    // Typed array constant
    public const array DATABASE_CONFIGS = [
        'primary' => [
            'host' => 'localhost',
            'port' => 5432,
            'timeout' => 30,
        ],
        'replica' => [
            'host' => 'replica.example.com',
            'port' => 5432,
            'timeout' => 60,
        ],
    ];
    
    // Union type constant
    public const int|string DEFAULT_TIMEOUT = 30;
    
    // Nullable constant
    public const ?string API_KEY = null;
}
```

---

## Performance & Improvements

### Benchmarks vs PHP 8.2

| Operation | Improvement |
|-----------|-------------|
| Array operations | +12% |
| String operations | +10% |
| JSON parsing | +15% |
| Object creation | +8% |
| Overall | +8-15% |

### Memory Improvements

- Reduced memory footprint
- Better garbage collection
- More efficient string handling
- Optimized array operations

### Feature Performance

```php
<?php
// Faster JSON validation (no full decode)
json_validate($largeJson);  // Much faster than json_decode

// Typed constants - no runtime overhead
class Config {
    public const int PORT = 5432;
}
// Access is zero-cost

// #[Override] attribute - zero runtime cost
#[Override]
public function method() {}  // Compile-time check only
```

---

## Migration from PHP 8.2

### Updates for PHP 8.3

**Add Type Hints to Constants:**

```php
<?php
// Before PHP 8.3
class OldConfig {
    const HOST = 'localhost';  // Unknown type
}

// PHP 8.3
class NewConfig {
    public const string HOST = 'localhost';  // Explicit type
}
```

**Use #[\Override] Attribute:**

```php
<?php
class Handler {
    // Mark overrides
    #[Override]
    public function handle() {
        // ...
    }
}
```

**Use json_validate():**

```php
<?php
// Old way
if (json_decode($json) !== null && json_last_error() === JSON_ERROR_NONE) {}

// New way
if (json_validate($json)) {}
```

### Deprecations Removed

```php
<?php
// Removed: DateTime serialization without interface
$date = new DateTime();
// json_encode($date);  // Error in 8.3!

// Use DateTimeImmutable or implement JsonSerializable
class Event implements JsonSerializable {
    public function __construct(private DateTime $createdAt) {}
    
    public function jsonSerialize(): array {
        return ['createdAt' => $this->createdAt->format(DateTime::ATOM)];
    }
}
```

---

## Code Example: PHP 8.3 Features

```php
<?php
declare(strict_types=1);

use Override;

// Configuration with typed constants
class AppConfig {
    public const string APP_NAME = 'MyApp';
    public const string VERSION = '1.0.0';
    public const int MAX_CONNECTIONS = 100;
    public const bool DEBUG = false;
    public const array ALLOWED_HOSTS = ['localhost', 'example.com'];
}

// Base request handler
class RequestHandler {
    public function handle(array $request): array {
        return ['status' => 'ok'];
    }
}

// Override validation
class ApiRequestHandler extends RequestHandler {
    #[Override]
    public function handle(array $request): array {
        // Validate JSON
        if (!json_validate(json_encode($request))) {
            return ['error' => 'Invalid request data'];
        }
        
        // Process with typed constants
        return [
            'status' => 'ok',
            'version' => AppConfig::VERSION,
            'maxConnections' => AppConfig::MAX_CONNECTIONS,
        ];
    }
}

// Usage
$handler = new ApiRequestHandler();
$response = $handler->handle(['data' => 'value']);
```

---

## Learning Path

Master PHP 8.3 progressively:

1. **Typed Constants** - Type-safe class constants
2. **#[\Override] Attribute** - Verify method overrides
3. **json_validate()** - Efficient JSON validation
4. **Randomizer** - Random number improvements
5. **Deprecation Updates** - Handle removed features
6. **Type System** - Advanced type combinations
7. **Performance** - Optimizations in PHP 8.3
8. **Best Practices** - Use new features effectively

## Quick Feature Comparison

| Feature | PHP 8.2 | PHP 8.3 |
|---------|---------|---------|
| Typed Constants | ❌ | ✅ |
| #[\Override] | ❌ | ✅ |
| json_validate() | ❌ | ✅ |
| DNF Types | ✅ | ✅ |
| Readonly Classes | ✅ | ✅ |
| Randomizer | Basic | ✅ Enhanced |
| Performance | Baseline | +8-15% |

---

## Prerequisites

Before learning PHP 8.3:

✅ **Required:**
- PHP 8.2 fundamentals
- OOP principles
- Type system knowledge

✅ **Helpful:**
- Understanding of attributes
- Knowledge of JSON processing
- Cryptography basics

## Best Practices

### ✅ DO

```php
<?php
// 1. Use typed constants
class Config {
    public const int TIMEOUT = 30;
    public const string API_URL = 'https://api.example.com';
}

// 2. Use #[Override] for clarity
#[Override]
public function handle(Request $request): Response {}

// 3. Use json_validate() before processing
if (json_validate($userInput)) {
    $data = json_decode($userInput, true);
}

// 4. Use Randomizer for cryptography
$randomizer = new Random\Randomizer();
$token = bin2hex($randomizer->getBytes(32));
```

### ❌ DON'T

```php
<?php
// 1. Don't use untyped constants
const HOST = 'localhost';  // Type not enforced

// 2. Don't ignore method overrides
public function processa() {}  // Typo not caught

// 3. Don't decode JSON just to validate
json_decode($json);  // Inefficient

// 4. Don't use weak random
$random = rand(1, 1000000);  // Not secure
```

---

## Resources

- **Official Migration Guide**: [php.net/manual/en/migration83.php](https://www.php.net/manual/en/migration83.php)
- **Type System**: [Type Declarations](https://www.php.net/manual/en/language.types.declarations.php)
- **Attributes**: [Attributes RFC](https://wiki.php.net/rfc/attributes/notation_for_metadata)
- **Random**: [Random Library](https://www.php.net/manual/en/intro.random.php)
