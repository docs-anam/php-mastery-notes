# Final Class Constants

## Overview

Learn about final class constants in PHP 8.1, which prevent child classes from overriding constants defined in parent classes.

---

## Table of Contents

1. What are Final Class Constants
2. Basic Syntax
3. Immutability Guarantees
4. Inheritance Rules
5. API Design Patterns
6. vs Private Constants
7. Real-world Patterns
8. Complete Examples

---

## What are Final Class Constants

### Purpose

```php
<?php
// Before PHP 8.1: Constants can be overridden

class BaseConfig {
    public const VERSION = '1.0.0';
}

class ChildConfig extends BaseConfig {
    // Can override parent constant!
    public const VERSION = '2.0.0';
}

echo BaseConfig::VERSION;   // 1.0.0
echo ChildConfig::VERSION;  // 2.0.0 (overridden!)

// Problem: Child breaks parent's contract

// Solution: Final Constants (8.1+)
class StrictConfig {
    final public const VERSION = '1.0.0';
}

class StrictChild extends StrictConfig {
    // Error: Cannot override final constant!
    // public const VERSION = '2.0.0';  // Fatal Error
}
```

### Benefits

```
✓ Enforce API contracts
✓ Prevent unintended overrides
✓ Guarantee immutable values
✓ Better inheritance safety
✓ Documentation of intent
✓ Clearer class hierarchies
✓ Reduce bugs
```

---

## Basic Syntax

### Declaration

```php
<?php
// Final constant
class Config {
    final public const STATUS = 'active';
}

// Cannot override
class ChildConfig extends Config {
    // Error: Cannot override final constant STATUS
    // public const STATUS = 'inactive';
}

// Visibility modifiers
class Features {
    final public const PUBLIC_VALUE = 'public';
    final protected const PROTECTED_VALUE = 'protected';
    final private const PRIVATE_VALUE = 'private';
}

// Only public and protected can be inherited
// Private cannot be overridden anyway (hidden from children)
```

### Multiple Final Constants

```php
<?php
class Environment {
    final public const DEVELOPMENT = 'development';
    final public const TESTING = 'testing';
    final public const STAGING = 'staging';
    final public const PRODUCTION = 'production';
}

class AppEnvironment extends Environment {
    // Cannot override any of them
    public const CUSTOM = 'custom';  // Can add new ones
}
```

---

## Immutability Guarantees

### Value Protection

```php
<?php
// Final ensures the value never changes across hierarchy

class DatabaseConfig {
    // These values are guaranteed in all subclasses
    final public const HOST = 'localhost';
    final public const PORT = 5432;
    final public const CHARSET = 'utf8mb4';
    
    // Children can add new constants
    public const POOL_SIZE = 10;
}

class ProductionDatabaseConfig extends DatabaseConfig {
    // Can override non-final
    public const POOL_SIZE = 100;
    
    // Cannot override final ones
    // They're guaranteed to remain:
    // - HOST: localhost
    // - PORT: 5432
    // - CHARSET: utf8mb4
}
```

### Array Constants

```php
<?php
class Permissions {
    // Array constant - the array is final, but contents can change
    // Only the reference is immutable
    final public const ROLES = [
        'admin' => 'Administrator',
        'user' => 'User',
        'guest' => 'Guest',
    ];
}

class ExtendedPermissions extends Permissions {
    // Error: Cannot override
    // public const ROLES = ['admin' => 'Admin', 'root' => 'Root'];
    
    // Children cannot modify
    // but note: array values themselves aren't "deep" final
}
```

---

## Inheritance Rules

### Overriding Rules

```php
<?php
class Parent {
    final public const FINAL_CONST = 'final';
    public const NORMAL_CONST = 'normal';
}

class Child extends Parent {
    // ✗ Error: Cannot override final
    // public const FINAL_CONST = 'new value';
    
    // ✓ OK: Can override non-final
    public const NORMAL_CONST = 'overridden';
    
    // ✓ OK: Can add new constants
    public const NEW_CONST = 'new';
}

echo Child::FINAL_CONST;  // Still 'final'
echo Child::NORMAL_CONST;  // Now 'overridden'
echo Child::NEW_CONST;  // 'new'
```

### Multi-level Hierarchy

```php
<?php
class GrandParent {
    final public const SAFE = 'immutable';
    public const FLEXIBLE = 'can change';
}

class Parent extends GrandParent {
    // ✗ Cannot override SAFE
    // public const SAFE = 'new';  // Error!
    
    // ✓ Can override FLEXIBLE
    public const FLEXIBLE = 'parent version';
}

class Child extends Parent {
    // ✗ Cannot override SAFE (from grandparent)
    // ✗ Cannot override FLEXIBLE (now set in parent)
    // public const FLEXIBLE = 'child version';
    
    // ✓ Can add new
    public const CHILD_SPECIFIC = 'only in child';
}
```

---

## API Design Patterns

### Framework Base Classes

```php
<?php
class FrameworkController {
    // These cannot be changed by implementing classes
    final public const NAMESPACE = 'Framework';
    final public const VERSION = '1.0';
    final public const COMPATIBILITY = '8.1+';
    
    // Optional features subclasses can set
    public const DEBUG = false;
    public const ENABLE_CACHING = true;
}

class MyController extends FrameworkController {
    // Cannot change framework requirements
    // MyController will still have:
    // - NAMESPACE = 'Framework'
    // - VERSION = '1.0'
    // - COMPATIBILITY = '8.1+'
    
    // But can customize:
    public const DEBUG = true;  // Override for testing
    
    public function handle(): void {
        echo static::NAMESPACE;  // Framework (guaranteed)
        echo static::DEBUG;  // true (customized)
    }
}
```

### Library Configuration

```php
<?php
class LibraryConfig {
    // Library behavior guarantees
    final public const MIN_PHP_VERSION = '8.1';
    final public const DEPENDENCIES = [
        'ext-json' => '*',
        'ext-pdo' => '*',
    ];
    
    // User customizations
    public const CACHE_TTL = 3600;
    public const LOG_LEVEL = 'info';
}

class CustomLibraryConfig extends LibraryConfig {
    // Must support:
    // - PHP 8.1+
    // - ext-json and ext-pdo
    
    // But can customize:
    public const CACHE_TTL = 7200;      // Custom cache
    public const LOG_LEVEL = 'debug';   // Custom logging
}
```

---

## vs Private Constants

### Visibility Comparison

```php
<?php
class Parent {
    final public const FINAL_PUBLIC = 'final, visible';
    public const NORMAL_PUBLIC = 'normal, visible';
    private const PRIVATE = 'hidden from children';
}

class Child extends Parent {
    public function show(): void {
        echo self::FINAL_PUBLIC;    // ✓ Accessible (final)
        echo self::NORMAL_PUBLIC;   // ✓ Accessible (can override)
        echo self::PRIVATE;         // ✗ Not accessible (private)
    }
    
    // ✗ Cannot override final
    // public const FINAL_PUBLIC = 'new';
    
    // ✓ Can override normal
    public const NORMAL_PUBLIC = 'overridden';
}

// Private constants:
// - Not visible to children
// - Cannot be overridden (they're hidden)

// Final constants:
// - Visible to children
// - Cannot be overridden (explicitly prevented)
```

### When to Use Which

```php
<?php
class SecurityClass {
    // Use private when you want to hide implementation detail
    private const ENCRYPTION_KEY = 'secret';
    
    // Use final when you want to expose but prevent override
    final public const ALGORITHM = 'SHA256';
    
    // Use normal when subclasses should be able to customize
    public const MAX_ATTEMPTS = 3;
    
    // Use private when only this class needs it
    private const INTERNAL_FLAG = true;
}
```

---

## Real-world Patterns

### Status/State Constants

```php
<?php
class OrderStatus {
    // These statuses are part of the public API
    // Cannot be changed by extensions
    final public const PENDING = 'pending';
    final public const PROCESSING = 'processing';
    final public const SHIPPED = 'shipped';
    final public const DELIVERED = 'delivered';
    final public const CANCELLED = 'cancelled';
    
    // Can be extended with new statuses
    public const RETURNED = 'returned';
}

class SpecialOrderStatus extends OrderStatus {
    // Cannot change standard statuses
    // Still has: PENDING, PROCESSING, SHIPPED, DELIVERED, CANCELLED
    
    // Can add specialty statuses
    public const PREMIUM = 'premium';
    public const VIP = 'vip';
}
```

### Database Table Names

```php
<?php
class Model {
    // Table name is fixed for ORM compatibility
    final public const TABLE = 'models';
    
    // Allow customization of behavior
    public const AUTO_INCREMENT = true;
    public const TIMESTAMPS = true;
}

class User extends Model {
    // Cannot change: TABLE is still 'models'
    // This breaks ORM if changed!
    
    // Can customize:
    public const AUTO_INCREMENT = false;  // Custom ID logic
    
    // Would normally override:
    // final public const TABLE = 'users';  // Error!
}
```

### HTTP Response Codes

```php
<?php
class HttpResponse {
    // Standard HTTP codes - never change
    final public const OK = 200;
    final public const CREATED = 201;
    final public const BAD_REQUEST = 400;
    final public const UNAUTHORIZED = 401;
    final public const NOT_FOUND = 404;
    final public const SERVER_ERROR = 500;
    
    // Custom behavior
    public const DEFAULT_CHARSET = 'utf-8';
}

class ApiResponse extends HttpResponse {
    // HTTP codes are unchanged
    // But can customize:
    public const DEFAULT_CHARSET = 'utf-8';
    public const JSON_PRETTY = true;
}
```

---

## Complete Examples

### Example 1: Framework Contracts

```php
<?php
abstract class Framework {
    // Core framework constants - cannot be changed
    final public const VERSION = '2024.1';
    final public const MINIMUM_PHP = '8.1';
    final public const AUTHOR = 'Framework Team';
    
    // Customizable settings
    public const DEBUG_MODE = false;
    public const ENABLE_PROFILING = false;
    
    final public function boot(): void {
        $this->validateEnvironment();
        $this->initialize();
    }
    
    private function validateEnvironment(): void {
        // Check PHP version using constant
        if (PHP_VERSION_ID < 80100) {
            throw new RuntimeException(
                "Framework requires PHP " . self::MINIMUM_PHP
            );
        }
    }
    
    protected function initialize(): void {
        if (static::DEBUG_MODE) {
            error_reporting(E_ALL);
        }
        
        if (static::ENABLE_PROFILING) {
            $this->startProfiling();
        }
    }
    
    protected function startProfiling(): void {
        // Implementation
    }
}

class MyFramework extends Framework {
    // Cannot change: VERSION, MINIMUM_PHP, AUTHOR
    
    // Can customize:
    public const DEBUG_MODE = true;
    public const ENABLE_PROFILING = true;
}

$framework = new MyFramework();
echo $framework::VERSION;  // 2024.1 (guaranteed)
```

### Example 2: Library Configuration

```php
<?php
class PaymentGateway {
    // API requirements - cannot override
    final public const API_VERSION = 'v2';
    final public const ENDPOINT = 'https://api.payment.com';
    final public const TIMEOUT = 30;
    
    // Merchant customizations
    public const CURRENCY = 'USD';
    public const SANDBOX = true;
    
    protected array $credentials;
    
    public function __construct(string $apiKey) {
        $this->credentials = [
            'api_key' => $apiKey,
            'api_version' => static::API_VERSION,
            'currency' => static::CURRENCY,
        ];
    }
    
    public function charge(float $amount): bool {
        // Use static constants
        $url = static::ENDPOINT . '/charge';
        
        // Make request with static values
        return true;
    }
}

class StripeGateway extends PaymentGateway {
    // Cannot change: API_VERSION, ENDPOINT, TIMEOUT
    // Gateway would break!
    
    // Can customize:
    public const CURRENCY = 'EUR';  // Merchant's currency
    public const SANDBOX = false;  // Production
}
```

### Example 3: Enum-like Behavior

```php
<?php
class UserRole {
    // These roles form the core of the system
    // Cannot be overridden by child classes
    final public const ADMIN = 1;
    final public const MODERATOR = 2;
    final public const USER = 3;
    final public const GUEST = 4;
    
    // Can be customized
    public const DEFAULT_ROLE = self::USER;
    
    public static function getName(int $role): string {
        return match ($role) {
            self::ADMIN => 'Administrator',
            self::MODERATOR => 'Moderator',
            self::USER => 'User',
            self::GUEST => 'Guest',
            default => 'Unknown',
        };
    }
    
    public static function hasPermission(
        int $role,
        string $permission
    ): bool {
        $permissions = [
            self::ADMIN => ['read', 'write', 'delete', 'manage'],
            self::MODERATOR => ['read', 'write', 'delete'],
            self::USER => ['read', 'write'],
            self::GUEST => ['read'],
        ];
        
        return in_array($permission, $permissions[$role] ?? []);
    }
}

class ExtendedUserRole extends UserRole {
    // Cannot change core roles
    // Still has: ADMIN=1, MODERATOR=2, USER=3, GUEST=4
    
    // Can customize:
    public const DEFAULT_ROLE = self::GUEST;  // More restrictive default
    
    // Could add new roles with different numbers
    public const PREMIUM = 5;
}

$role = ExtendedUserRole::ADMIN;
echo ExtendedUserRole::getName($role);  // Administrator
```

---

## Key Takeaways

**Final Constants Checklist:**

1. ✅ Use for API contracts
2. ✅ Use for version constants
3. ✅ Use for framework requirements
4. ✅ Use for status/state values
5. ✅ Allow normal constants for customization
6. ✅ Document why constants are final
7. ✅ Consider inheritance impact
8. ✅ Private vs Final trade-offs

---

## See Also

- [Enumerations](2-enumerations.md)
- [Readonly Properties](3-readonly-properties.md)
- [Intersection Types](5-pure-intersection-types.md)
- [Class Constants (PHP Basics)](../01-basics/class-definition.md#constants)
