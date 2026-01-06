# Typed Class Constants

## Overview

Learn about typed class constants in PHP 8.3, which allow you to specify explicit types for constants while maintaining backward compatibility with untyped constants.

---

## Table of Contents

1. What are Typed Constants
2. Basic Syntax
3. Type Declaration Rules
4. Inheritance Patterns
5. Access Control with Types
6. Real-world Examples
7. Best Practices
8. Complete Examples

---

## What are Typed Constants

### Purpose and Benefits

```php
<?php
// Before PHP 8.3: Constants without type enforcement
class Config
{
    const HOST = 'localhost';      // Type unknown
    const PORT = 5432;              // Type unknown
    const ENABLED = true;           // Type unknown
    const ALLOWED = ['127.0.0.1']; // Type unknown

    // Problems:
    // ❌ No type checking
    // ❌ Can accidentally override with wrong type
    // ❌ IDE doesn't provide accurate hints
    // ❌ Unclear intent in code
}

// PHP 8.3 Solution: Typed constants
class TypedConfig
{
    public const string HOST = 'localhost';
    public const int PORT = 5432;
    public const bool ENABLED = true;
    public const array ALLOWED = ['127.0.0.1'];

    // Benefits:
    // ✓ Type-safe
    // ✓ IDE auto-completion
    // ✓ Prevents mistakes
    // ✓ Self-documenting code
    // ✓ Runtime type checking
}
```

### Key Features

```php
<?php
// Typed constants syntax:
class ClassName
{
    // Type annotation required for typed constants
    public const TYPE NAME = value;
    
    // Can be public, protected, or private
    private const string PRIVATE_CONST = 'value';
    protected const int PROTECTED_CONST = 42;
}

// Supported types:
// string, int, float, bool, array, object, mixed, null
```

---

## Basic Syntax

### Simple Typed Constants

```php
<?php
class ApplicationConfig
{
    // String constants
    public const string APP_NAME = 'MyApplication';
    public const string APP_VERSION = '1.0.0';
    public const string ENVIRONMENT = 'production';

    // Integer constants
    public const int MAX_CONNECTIONS = 10;
    public const int RETRY_ATTEMPTS = 3;
    public const int TIMEOUT_SECONDS = 30;

    // Boolean constants
    public const bool DEBUG_MODE = false;
    public const bool CACHE_ENABLED = true;

    // Array constants
    public const array ALLOWED_HOSTS = ['localhost', 'example.com'];
    public const array DEFAULT_PORTS = [80, 443, 8080];

    // Float constants
    public const float DEFAULT_INTEREST_RATE = 0.05;
}

// Access typed constants
echo ApplicationConfig::APP_NAME;           // 'MyApplication'
echo ApplicationConfig::MAX_CONNECTIONS;    // 10
echo ApplicationConfig::ALLOWED_HOSTS[0];   // 'localhost'
```

### Type Enforcement

```php
<?php
class StrictConfig
{
    public const string NAME = 'app';
    public const int COUNT = 5;
}

// ✓ Correct: Matching types
$name = StrictConfig::NAME;      // string
$count = StrictConfig::COUNT;    // int

// ❌ Wrong: Type mismatch in definition
class BadConfig
{
    // public const string NAME = 123;  // TypeError at runtime!
    // public const int COUNT = 'five';  // TypeError at runtime!
}
```

---

## Access Control Modifiers

### Visibility Levels

```php
<?php
class AccessControl
{
    // Public: accessible everywhere
    public const string PUBLIC_CONST = 'public';

    // Protected: accessible in class and children
    protected const int PROTECTED_CONST = 42;

    // Private: accessible only in class
    private const array PRIVATE_CONST = ['a', 'b'];

    public function getPrivate(): array
    {
        return self::PRIVATE_CONST;  // Accessible here
    }
}

$config = new AccessControl();
echo AccessControl::PUBLIC_CONST;      // Works
// echo AccessControl::PROTECTED_CONST;  // Error!
// echo AccessControl::PRIVATE_CONST;    // Error!
```

### In Inheritance

```php
<?php
// Parent class with typed constants
class Parent
{
    public const string APP_NAME = 'ParentApp';
    protected const int VERSION = 1;
    private const array SETTINGS = [];
}

// Child class inheritance
class Child extends Parent
{
    // Can access public constants
    public const string CHILD_NAME = parent::APP_NAME . '_Child';

    // Can override public constants (must match type)
    public const string APP_NAME = 'ChildApp';

    // Can access protected constants
    public function getVersion(): int
    {
        return parent::VERSION;  // 1
    }

    // Cannot access private constants
    // public function getSettings() { return parent::SETTINGS; }  // Error!
}
```

---

## Type Combinations

### All Supported Types

```php
<?php
class AllTypes
{
    // Scalar types
    public const string STRING = 'value';
    public const int INT = 42;
    public const float FLOAT = 3.14;
    public const bool BOOL = true;
    public const null NULL_VALUE = null;

    // Array type
    public const array ARRAY = ['a', 'b', 'c'];

    // Mixed type (any type)
    public const mixed MIXED = 'could be anything';

    // Object/class type
    public const object OBJ = new stdClass();

    // Union types (not directly in constants, but through mixed)
    // public const string|int UNION = 'value';  // Not supported
}

// Workaround for complex types
class ComplexTypes
{
    public const mixed UNION_LIKE = 'string or int';
    
    public static function getUnionValue(): string|int
    {
        return self::UNION_LIKE;
    }
}
```

---

## Practical Implementation Patterns

### Configuration Class

```php
<?php
class DatabaseConfig
{
    // Connection settings
    public const string HOST = 'localhost';
    public const int PORT = 5432;
    public const string DATABASE = 'myapp';
    public const string USERNAME = 'root';
    public const string PASSWORD = 'secret';

    // Pool settings
    public const int MAX_CONNECTIONS = 10;
    public const int MIN_CONNECTIONS = 2;
    public const int IDLE_TIMEOUT = 300;

    // Feature flags
    public const bool SSL_ENABLED = false;
    public const bool PERSISTENCE = true;
    public const array CHARSET = ['utf-8', 'utf8mb4'];

    public static function getDsn(): string
    {
        return "pgsql:host=" . self::HOST . ";port=" . self::PORT 
               . ";dbname=" . self::DATABASE;
    }

    public static function getCredentials(): array
    {
        return [
            'username' => self::USERNAME,
            'password' => self::PASSWORD,
        ];
    }
}

// Usage
$dsn = DatabaseConfig::getDsn();
$pool = [
    'max' => DatabaseConfig::MAX_CONNECTIONS,
    'min' => DatabaseConfig::MIN_CONNECTIONS,
];
```

### Feature Flags

```php
<?php
class Features
{
    // Feature availability
    public const bool BETA_ENABLED = false;
    public const bool ANALYTICS_ENABLED = true;
    public const bool CACHE_ENABLED = true;
    public const bool REDIS_CACHE = true;
    public const bool EMAIL_NOTIFICATIONS = true;

    // Feature list
    public const array ENABLED_FEATURES = [
        'analytics',
        'cache',
        'notifications',
    ];

    public static function isEnabled(string $feature): bool
    {
        return in_array($feature, self::ENABLED_FEATURES, true);
    }

    public static function enableBeta(): void
    {
        // Note: Constants can't be changed, but can check value
        if (self::BETA_ENABLED) {
            echo "Beta features enabled";
        }
    }
}

// Usage
if (Features::ANALYTICS_ENABLED) {
    // Initialize analytics
}

if (Features::isEnabled('notifications')) {
    // Send notifications
}
```

### Validation Rules

```php
<?php
class ValidationRules
{
    // Length constraints
    public const int MIN_PASSWORD_LENGTH = 8;
    public const int MAX_PASSWORD_LENGTH = 128;
    public const int MIN_USERNAME_LENGTH = 3;
    public const int MAX_USERNAME_LENGTH = 50;

    // Pattern rules
    public const string EMAIL_PATTERN = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    public const string USERNAME_PATTERN = '/^[a-zA-Z0-9_-]{3,50}$/';
    public const string PHONE_PATTERN = '/^\+?[0-9\s\-()]{10,}$/';

    // Allowed values
    public const array ALLOWED_ROLES = ['admin', 'moderator', 'user'];
    public const array ALLOWED_COUNTRIES = ['US', 'CA', 'UK', 'AU'];
    public const array ALLOWED_CURRENCIES = ['USD', 'EUR', 'GBP', 'AUD'];

    public static function validatePassword(string $password): bool
    {
        $length = strlen($password);
        return $length >= self::MIN_PASSWORD_LENGTH
               && $length <= self::MAX_PASSWORD_LENGTH;
    }

    public static function validateEmail(string $email): bool
    {
        return preg_match(self::EMAIL_PATTERN, $email) === 1;
    }

    public static function isValidRole(string $role): bool
    {
        return in_array($role, self::ALLOWED_ROLES, true);
    }
}

// Usage
if (ValidationRules::validatePassword($pwd)) {
    echo "Password valid";
}

if (ValidationRules::validateEmail($email)) {
    echo "Email valid";
}
```

---

## Inheritance and Override

### Constant Inheritance

```php
<?php
// Base configuration
class BaseConfig
{
    public const string APP_NAME = 'BaseApp';
    public const int VERSION = 1;
    public const array FEATURES = ['basic'];
}

// Child configuration with overrides
class ProductionConfig extends BaseConfig
{
    // Override with same type
    public const string APP_NAME = 'ProdApp';
    public const int VERSION = 1;
    public const array FEATURES = ['basic', 'advanced', 'premium'];

    // Override type must match parent
    // public const int VERSION = '1.0';  // Error!
}

// Use in inheritance chain
class DevelopmentConfig extends BaseConfig
{
    public const string APP_NAME = 'DevApp';
    public const array FEATURES = ['basic', 'advanced', 'debug', 'test'];
}

// Runtime selection
$config = (getenv('ENVIRONMENT') === 'production')
    ? ProductionConfig::class
    : DevelopmentConfig::class;

$appName = constant($config . '::APP_NAME');
$version = constant($config . '::VERSION');
$features = constant($config . '::FEATURES');
```

### Interface Constants

```php
<?php
interface ConfigInterface
{
    public const string DEFAULT_TIMEZONE = 'UTC';
    public const int MAX_RETRY = 3;
    public const array SUPPORTED_LOCALES = ['en', 'de', 'fr'];
}

class AppConfig implements ConfigInterface
{
    // Must implement interface constants with matching types
    public const string DEFAULT_TIMEZONE = 'Europe/Berlin';
    public const int MAX_RETRY = 5;
    public const array SUPPORTED_LOCALES = ['en', 'de', 'fr', 'es'];
}

// Use in traits
trait TimezoneConfig
{
    public const string DEFAULT_TIMEZONE = 'America/New_York';
}

class LocalizedConfig
{
    use TimezoneConfig;
    
    public const string LANGUAGE = 'en';
}
```

---

## Type Safety Benefits

### Runtime Validation

```php
<?php
class SafeConfig
{
    public const string HOST = 'localhost';
    public const int PORT = 5432;
}

// Type safety in use
$host = SafeConfig::HOST;  // PHP knows this is string
$port = SafeConfig::PORT;  // PHP knows this is int

// IDE provides correct hints
strlen($host);    // ✓ string method, auto-complete works
$port + 1;        // ✓ int arithmetic, auto-complete works

// Prevents type confusion
class IncorrectConfig
{
    // public const string PORT = 5432;  // TypeError at definition!
}
```

---

## Complete Examples

### Full Application Configuration

```php
<?php
declare(strict_types=1);

namespace App\Config;

class ApplicationConfig
{
    // Application metadata
    public const string NAME = 'Advanced Application';
    public const string VERSION = '2.0.0';
    public const string ENVIRONMENT = 'production';

    // Database configuration
    public const string DB_HOST = 'db.example.com';
    public const int DB_PORT = 5432;
    public const string DB_NAME = 'app_database';
    public const int DB_POOL_SIZE = 10;
    public const int DB_TIMEOUT = 30;

    // Cache configuration
    public const bool CACHE_ENABLED = true;
    public const string CACHE_DRIVER = 'redis';
    public const string CACHE_HOST = 'cache.example.com';
    public const int CACHE_PORT = 6379;
    public const int CACHE_TTL = 3600;

    // Security
    public const string SESSION_NAME = 'app_session';
    public const int SESSION_LIFETIME = 86400;
    public const bool HTTPS_REQUIRED = true;
    public const array ALLOWED_CORS = ['https://example.com'];

    // Feature flags
    public const bool DEBUG = false;
    public const bool MAINTENANCE_MODE = false;
    public const array ENABLED_MODULES = ['api', 'web', 'admin'];

    // Limits
    public const int MAX_UPLOAD_SIZE = 52428800;  // 50MB
    public const int MAX_CONNECTIONS = 100;
    public const int RATE_LIMIT_REQUESTS = 1000;
    public const int RATE_LIMIT_WINDOW = 3600;

    /**
     * Get database DSN
     */
    public static function getDbDsn(): string
    {
        return sprintf(
            'pgsql:host=%s;port=%d;dbname=%s',
            self::DB_HOST,
            self::DB_PORT,
            self::DB_NAME
        );
    }

    /**
     * Get cache configuration
     */
    public static function getCacheConfig(): array
    {
        return [
            'driver' => self::CACHE_DRIVER,
            'host' => self::CACHE_HOST,
            'port' => self::CACHE_PORT,
            'ttl' => self::CACHE_TTL,
            'enabled' => self::CACHE_ENABLED,
        ];
    }

    /**
     * Get all settings as array
     */
    public static function toArray(): array
    {
        return [
            'app' => [
                'name' => self::NAME,
                'version' => self::VERSION,
                'environment' => self::ENVIRONMENT,
            ],
            'database' => [
                'host' => self::DB_HOST,
                'port' => self::DB_PORT,
                'name' => self::DB_NAME,
            ],
            'cache' => self::getCacheConfig(),
            'security' => [
                'https_required' => self::HTTPS_REQUIRED,
                'allowed_cors' => self::ALLOWED_CORS,
            ],
        ];
    }

    /**
     * Check if module is enabled
     */
    public static function isModuleEnabled(string $module): bool
    {
        return in_array($module, self::ENABLED_MODULES, true);
    }
}

// Usage in application
class Application
{
    public function __construct()
    {
        // Check environment
        if (ApplicationConfig::MAINTENANCE_MODE) {
            die('System under maintenance');
        }

        // Initialize components
        $this->initializeDatabase();
        $this->initializeCache();
    }

    private function initializeDatabase(): void
    {
        $dsn = ApplicationConfig::getDbDsn();
        $timeout = ApplicationConfig::DB_TIMEOUT;
        // Connect to database
    }

    private function initializeCache(): void
    {
        if (!ApplicationConfig::CACHE_ENABLED) {
            return;
        }

        $config = ApplicationConfig::getCacheConfig();
        // Initialize cache
    }

    public function run(): void
    {
        // Check if API module is enabled
        if (ApplicationConfig::isModuleEnabled('api')) {
            // Initialize API routes
        }
    }
}

// Access configuration
$config = ApplicationConfig::toArray();
echo json_encode($config);
```

---

## See Also

- [PHP 8.3 Overview](0-php8.3-overview.md)
- [Override Attribute](3-override-attribute.md)
- [JSON Validation](4-json-validation.md)
- [Enum Enhancements](6-enum-enhancements.md)
