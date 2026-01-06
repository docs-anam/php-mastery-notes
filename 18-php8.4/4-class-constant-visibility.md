# Class Constant Visibility

## Overview

PHP 8.4 introduces visibility modifiers for class constants, enabling better encapsulation and control over constant access similar to properties and methods.

---

## Table of Contents

1. Introduction to Constant Visibility
2. Basic Syntax
3. Public Constants
4. Protected Constants
5. Private Constants
6. Practical Patterns
7. Interface Constants
8. Inheritance
9. Best Practices
10. Complete Examples

---

## Introduction to Constant Visibility

### What Are Class Constant Visibility Modifiers?

```php
<?php
// PHP 8.4 allows visibility control on constants

class ApiConfig
{
    // Public constant - accessible from anywhere
    public const string VERSION = '1.0';

    // Protected constant - accessible in class and children
    protected const int TIMEOUT = 30;

    // Private constant - only accessible within class
    private const string API_KEY = 'secret';

    public static function getConfig(): array
    {
        return [
            'version' => self::VERSION,
            'timeout' => self::TIMEOUT,
            'api_key' => self::API_KEY,
        ];
    }
}

// Usage
echo ApiConfig::VERSION;        // ✓ Public
// echo ApiConfig::TIMEOUT;     // ✗ Protected
// echo ApiConfig::API_KEY;     // ✗ Private

class ChildApiConfig extends ApiConfig
{
    public static function childConfig(): array
    {
        return [
            'version' => self::VERSION,    // ✓ Public
            'timeout' => self::TIMEOUT,    // ✓ Protected
            // 'api_key' => self::API_KEY,  // ✗ Private
        ];
    }
}
```

### Before PHP 8.4

```php
<?php
// Before: All constants were public

class OldConfig
{
    // Always public - no way to hide
    const VERSION = '1.0';
    const INTERNAL_KEY = 'secret';
    const TIMEOUT = 30;

    // Workaround: use private static properties
    private static string $secret = 'hidden';

    public static function getSecret(): string
    {
        return self::$secret;
    }
}

// Problems:
// ✗ Constants couldn't be private
// ✗ Had to use static properties as workaround
// ✗ No clear intent about constant access

// After PHP 8.4
class NewConfig
{
    public const string VERSION = '1.0';
    private const string INTERNAL_KEY = 'secret';
    protected const int TIMEOUT = 30;

    // Now constants can be hidden like properties
}
```

---

## Basic Syntax

### Visibility Modifiers

```php
<?php
// Different visibility levels for constants

class ConstantVisibility
{
    // Public - default behavior
    public const int PUBLIC_CONSTANT = 100;

    // Protected - accessible in class and children
    protected const string PROTECTED_CONSTANT = 'protected';

    // Private - only in this class
    private const float PRIVATE_CONSTANT = 3.14;

    public static function showAll(): array
    {
        return [
            'public' => self::PUBLIC_CONSTANT,
            'protected' => self::PROTECTED_CONSTANT,
            'private' => self::PRIVATE_CONSTANT,
        ];
    }
}

// Access from outside
echo ConstantVisibility::PUBLIC_CONSTANT;     // ✓
// echo ConstantVisibility::PROTECTED_CONSTANT;  // ✗
// echo ConstantVisibility::PRIVATE_CONSTANT;    // ✗

// From child class
class ChildConstants extends ConstantVisibility
{
    public static function childAccess(): array
    {
        return [
            'public' => self::PUBLIC_CONSTANT,        // ✓
            'protected' => self::PROTECTED_CONSTANT,  // ✓
            // 'private' => self::PRIVATE_CONSTANT,   // ✗
        ];
    }
}
```

### Typed Constants with Visibility

```php
<?php
// Combine type declarations with visibility

class TypedConstants
{
    // Public typed constant
    public const string DATABASE_HOST = 'localhost';
    public const int DATABASE_PORT = 5432;
    public const float PI = 3.14159;
    public const bool DEBUG = false;
    public const array CONFIG = [
        'timeout' => 30,
        'retries' => 3,
    ];

    // Protected typed constant
    protected const string INTERNAL_PATH = '/var/data';

    // Private typed constant
    private const string SECRET_KEY = 'classification: top-secret';

    public static function getPublicConfig(): array
    {
        return [
            'host' => self::DATABASE_HOST,
            'port' => self::DATABASE_PORT,
        ];
    }
}

// Usage
echo TypedConstants::DATABASE_HOST;  // ✓ localhost
echo TypedConstants::PI;             // ✓ 3.14159
print_r(TypedConstants::CONFIG);     // ✓ Array
```

---

## Public Constants

### Default Public Visibility

```php
<?php
// Public constants are accessible from anywhere

class Application
{
    public const string APP_NAME = 'MyApp';
    public const string VERSION = '2.4.1';
    public const string ENVIRONMENT = 'production';
    public const int MAX_CONNECTIONS = 100;
    public const float MEMORY_LIMIT_MB = 256.0;

    public static function info(): string
    {
        return self::APP_NAME . ' v' . self::VERSION;
    }
}

// Accessible everywhere
echo Application::APP_NAME;         // MyApp
echo Application::VERSION;          // 2.4.1
echo Application::ENVIRONMENT;      // production

// Can be used in default values
function getAppName(string $app = Application::APP_NAME): string
{
    return $app;
}

// Can be used in switch statements
$env = Application::ENVIRONMENT;
switch ($env) {
    case 'production':
        // Use production settings
        break;
}
```

### API Constants

```php
<?php
// Public constants for API clients

class HttpStatus
{
    public const int OK = 200;
    public const int CREATED = 201;
    public const int BAD_REQUEST = 400;
    public const int UNAUTHORIZED = 401;
    public const int FORBIDDEN = 403;
    public const int NOT_FOUND = 404;
    public const int INTERNAL_ERROR = 500;

    public const array STATUS_MESSAGES = [
        self::OK => 'OK',
        self::CREATED => 'Created',
        self::BAD_REQUEST => 'Bad Request',
        self::UNAUTHORIZED => 'Unauthorized',
        self::FORBIDDEN => 'Forbidden',
        self::NOT_FOUND => 'Not Found',
        self::INTERNAL_ERROR => 'Internal Server Error',
    ];
}

// Usage in code
if ($response->code === HttpStatus::OK) {
    echo HttpStatus::STATUS_MESSAGES[HttpStatus::OK];
}
```

---

## Protected Constants

### Family-Only Constants

```php
<?php
// Protected constants are shared within inheritance hierarchy

class BaseEntity
{
    public const string ID_TYPE = 'uuid';
    public const int ID_LENGTH = 36;

    // Constants for derived classes but not external code
    protected const string TABLE_PREFIX = 'entity_';
    protected const string DEFAULT_SORT = 'created_at';
    protected const int DEFAULT_LIMIT = 100;

    protected static function getTableName(string $entity): string
    {
        return self::TABLE_PREFIX . strtolower($entity);
    }
}

class UserEntity extends BaseEntity
{
    // Can access protected constants
    private const string TABLE_NAME = self::TABLE_PREFIX . 'users';

    public static function getUsers(): array
    {
        $limit = self::DEFAULT_LIMIT;  // ✓ Access protected constant
        $sort = self::DEFAULT_SORT;    // ✓ Access protected constant

        return [];  // Fetch from table
    }
}

class ProductEntity extends BaseEntity
{
    private const string TABLE_NAME = self::TABLE_PREFIX . 'products';

    public static function getProducts(): array
    {
        $limit = self::DEFAULT_LIMIT;  // ✓ Access protected constant

        return [];
    }
}

// Outside usage
echo BaseEntity::ID_TYPE;           // ✓ Public
// echo BaseEntity::TABLE_PREFIX;   // ✗ Protected

echo UserEntity::ID_TYPE;           // ✓ Inherited public
// echo UserEntity::DEFAULT_LIMIT;  // ✗ Protected
```

### Configuration Hierarchy

```php
<?php
// Protected constants for configuration inheritance

class BaseConfig
{
    public const string ENV = 'development';

    protected const int TIMEOUT = 30;
    protected const int RETRY_COUNT = 3;
    protected const string LOG_LEVEL = 'info';
}

class DatabaseConfig extends BaseConfig
{
    public const string DRIVER = 'pdo';
    public const string HOST = 'localhost';

    // Can access parent protected constants
    public static function getConnectionString(): string
    {
        $timeout = self::TIMEOUT;  // ✓ From parent
        return "Database: timeout=$timeout";
    }
}

class CacheConfig extends BaseConfig
{
    public const string DRIVER = 'redis';

    public static function getSettings(): array
    {
        return [
            'timeout' => self::TIMEOUT,      // ✓ From parent
            'retries' => self::RETRY_COUNT,  // ✓ From parent
        ];
    }
}
```

---

## Private Constants

### Implementation Details

```php
<?php
// Private constants hide implementation details

class CryptographyService
{
    public const string ALGORITHM = 'aes-256-cbc';

    // Internal constants not exposed
    private const int SALT_LENGTH = 32;
    private const int ITERATIONS = 100000;
    private const string HASH_ALGO = 'sha256';

    public static function encrypt(string $data): string
    {
        $salt = random_bytes(self::SALT_LENGTH);  // ✓ Use private constant
        // Encryption logic
        return base64_encode($data . ':' . $salt);
    }

    public static function hash(string $data): string
    {
        return hash(self::HASH_ALGO, $data, false);  // ✓ Use private constant
    }

    private static function derive(string $password): string
    {
        // Use private constants
        return hash_pbkdf2(
            self::HASH_ALGO,
            $password,
            'salt',
            self::ITERATIONS,
            32,
            false
        );
    }
}

// Usage
echo CryptographyService::ALGORITHM;    // ✓ Public algorithm
// echo CryptographyService::SALT_LENGTH;  // ✗ Private implementation detail

CryptographyService::encrypt('secret');  // ✓ Works
```

### Feature Flags with Private Defaults

```php
<?php
// Private constants for internal feature configuration

class FeatureFlags
{
    // Public flags for external code
    public const bool ENABLE_API_V2 = true;
    public const bool ENABLE_SSO = false;

    // Private defaults and limits
    private const int MAX_API_REQUESTS = 1000;
    private const int API_RATE_LIMIT_SECONDS = 3600;
    private const bool DEBUG_MODE = false;

    public static function canAccessApi(string $userId): bool
    {
        return self::ENABLE_API_V2;  // ✓ Public flag
    }

    public static function checkRateLimit(string $userId): bool
    {
        $limit = self::MAX_API_REQUESTS;  // ✓ Use private constant
        // Check if user exceeded limit
        return true;
    }

    public static function getDebugInfo(): array
    {
        if (!self::DEBUG_MODE) {  // ✓ Use private debug constant
            return [];
        }
        return ['debug' => true];
    }
}
```

---

## Practical Patterns

### Configuration Class Pattern

```php
<?php
// Well-structured configuration with controlled visibility

class AppConfig
{
    // Public API configuration
    public const string API_BASE_URL = 'https://api.example.com';
    public const string API_VERSION = 'v2';
    public const int API_TIMEOUT = 30;

    // Protected for child configurations
    protected const array HEADERS = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    protected const array RETRY_POLICY = [
        'max_retries' => 3,
        'backoff_multiplier' => 2,
    ];

    // Private implementation details
    private const string SECRET_KEY = 'top-secret-key-12345';
    private const array RATE_LIMITS = [
        'requests_per_minute' => 60,
        'requests_per_hour' => 1000,
    ];

    public static function getApiUrl(string $endpoint): string
    {
        return self::API_BASE_URL . '/' . self::API_VERSION . '/' . $endpoint;
    }

    protected static function getHeaders(): array
    {
        return self::HEADERS;
    }

    protected static function getRetryPolicy(): array
    {
        return self::RETRY_POLICY;
    }

    private static function validateRateLimit(): bool
    {
        $limit = self::RATE_LIMITS['requests_per_minute'];
        return true;
    }
}

// Usage
echo AppConfig::API_BASE_URL;  // ✓
echo AppConfig::getApiUrl('users');  // ✓
```

### Status Enumeration

```php
<?php
// Using constants for status values

class OrderStatus
{
    public const string PENDING = 'pending';
    public const string PROCESSING = 'processing';
    public const string SHIPPED = 'shipped';
    public const string DELIVERED = 'delivered';
    public const string CANCELLED = 'cancelled';

    // Validation constants
    protected const array VALID_STATUSES = [
        self::PENDING,
        self::PROCESSING,
        self::SHIPPED,
        self::DELIVERED,
        self::CANCELLED,
    ];

    // Status transition rules
    private const array TRANSITIONS = [
        self::PENDING => [self::PROCESSING, self::CANCELLED],
        self::PROCESSING => [self::SHIPPED, self::CANCELLED],
        self::SHIPPED => [self::DELIVERED],
        self::DELIVERED => [],
        self::CANCELLED => [],
    ];

    public static function isValid(string $status): bool
    {
        return in_array($status, self::VALID_STATUSES);
    }

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? []);
    }
}

// Usage
echo OrderStatus::PENDING;  // ✓
if (OrderStatus::isValid('processing')) {
    // Valid status
}
```

---

## Interface Constants

### Public Constants in Interfaces

```php
<?php
// Constants in interfaces are always public

interface CacheInterface
{
    // These are always public
    public const int DEFAULT_TTL = 3600;
    public const string NAMESPACE = 'cache:';

    public function get(string $key);
    public function set(string $key, mixed $value, int $ttl = self::DEFAULT_TTL): void;
}

class RedisCache implements CacheInterface
{
    public function set(string $key, mixed $value, int $ttl = self::DEFAULT_TTL): void
    {
        // TTL defaults to 3600
    }

    public static function getNamespace(): string
    {
        return self::NAMESPACE;  // Can access interface constant
    }
}

// Usage
echo CacheInterface::DEFAULT_TTL;  // ✓ 3600
echo RedisCache::DEFAULT_TTL;      // ✓ Also accessible
```

---

## Inheritance

### Overriding Visibility

```php
<?php
// Cannot change visibility in child classes - must stay same or more public

class Parent1
{
    protected const string VALUE = 'protected';
}

// Child visibility must be same or more public
class Child1 extends Parent1
{
    // ✓ Can make more public
    public const string VALUE = 'now public';

    // ✗ Cannot make more private
    // private const string VALUE = 'invalid';
}

// Access patterns
echo Parent1::VALUE;   // ✗ Protected
echo Child1::VALUE;    // ✓ Now public

// This same rule applies to methods and properties
```

---

## Best Practices

### When to Use Each Visibility

```php
<?php
// ✓ DO: Use public for stable API contracts
class PublicApi
{
    public const string API_VERSION = '2.0';
    public const int SUCCESS = 200;
    public const array ALLOWED_METHODS = ['GET', 'POST', 'PUT', 'DELETE'];
}

// ✓ DO: Use protected for inheritance hierarchies
class BaseRepository
{
    protected const string ENTITY_TYPE = 'Entity';
    protected const int DEFAULT_PAGE_SIZE = 20;
}

// ✓ DO: Use private for implementation details
class InternalService
{
    private const string INTERNAL_API_KEY = 'secret';
    private const int CACHE_DURATION = 300;
}

// ✗ DON'T: Expose implementation details as public
class BadExample
{
    // Should be private
    public const string DATABASE_PASSWORD = 'secret123';
    public const string STRIPE_SECRET_KEY = 'sk_live_...';
}

// ✗ DON'T: Make everything protected
class TooManyProtected
{
    protected const int BUFFER_SIZE = 1024;
    protected const float THRESHOLD = 0.5;
    // Most of these should be private
}
```

---

## Complete Examples

### Full Configuration Class

```php
<?php
declare(strict_types=1);

namespace App\Config;

final class SystemConfig
{
    // Public application constants
    public const string APP_NAME = 'MyApplication';
    public const string VERSION = '1.0.0';
    public const string ENVIRONMENT = 'production';

    // Public API constants
    public const int API_TIMEOUT = 30;
    public const int MAX_RETRIES = 3;
    public const string API_BASE_URL = 'https://api.example.com';

    // Protected configuration for subclasses
    protected const array SUPPORTED_FORMATS = ['json', 'xml', 'csv'];
    protected const int DEFAULT_PAGE_SIZE = 50;
    protected const int MAX_PAGE_SIZE = 1000;

    // Private implementation details
    private const string LOG_LEVEL = 'info';
    private const array SECURITY_HEADERS = [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
        'X-XSS-Protection' => '1; mode=block',
    ];
    private const int SESSION_TIMEOUT = 1800;
    private const bool ENABLE_DEBUG = false;

    public static function getAppInfo(): string
    {
        return self::APP_NAME . ' ' . self::VERSION;
    }

    public static function isProduction(): bool
    {
        return self::ENVIRONMENT === 'production';
    }

    public static function getApiUrl(string $endpoint): string
    {
        return self::API_BASE_URL . '/' . $endpoint;
    }

    protected static function getSecurityHeaders(): array
    {
        return self::SECURITY_HEADERS;
    }

    protected static function getDefaultPageSize(): int
    {
        return self::DEFAULT_PAGE_SIZE;
    }

    private static function getSessionTimeout(): int
    {
        return self::SESSION_TIMEOUT;
    }
}

// Usage
echo SystemConfig::APP_NAME;                    // ✓ MyApplication
echo SystemConfig::getAppInfo();                // ✓ MyApplication 1.0.0
$url = SystemConfig::getApiUrl('users');       // ✓ Works
// echo SystemConfig::LOG_LEVEL;                // ✗ Private
```

---

## See Also

- [PHP 8.4 Overview](0-php8.4-overview.md)
- [Property Hooks](2-property-hooks.md)
- [Asymmetric Visibility](3-asymmetric-visibility.md)
- [Type System Improvements](5-type-system.md)
