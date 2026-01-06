# Enum Enhancements

## Overview

Learn about enum improvements in PHP 8.3, including better method support, improved readonly handling, and enhanced reflection capabilities.

---

## Table of Contents

1. Enum Recap
2. PHP 8.3 Improvements
3. Methods and Behavior
4. Readonly Enums
5. Reflection and Discovery
6. Practical Patterns
7. Best Practices
8. Complete Examples

---

## Enum Recap

### Brief History

```php
<?php
// PHP 8.1: Enums introduced
enum Status
{
    case PENDING;
    case ACTIVE;
    case INACTIVE;
}

// PHP 8.2: Backed enums and some improvements
enum OrderStatus: string
{
    case PENDING = 'pending';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
}

// PHP 8.3: Better methods, improved behavior, enhanced reflection
enum PaymentMethod: string
{
    case CREDIT_CARD = 'credit_card';
    case DEBIT_CARD = 'debit_card';
    case PAYPAL = 'paypal';

    // Better method support in 8.3
    public function getDisplayName(): string
    {
        return match($this) {
            self::CREDIT_CARD => 'Credit Card',
            self::DEBIT_CARD => 'Debit Card',
            self::PAYPAL => 'PayPal',
        };
    }
}
```

---

## PHP 8.3 Improvements

### Method Enhancements

```php
<?php
// Improved method support in enums
enum UserRole: string
{
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case USER = 'user';
    case GUEST = 'guest';

    // Better method implementation
    public function canDelete(): bool
    {
        return match($this) {
            self::ADMIN => true,
            self::MODERATOR => true,
            self::USER => false,
            self::GUEST => false,
        };
    }

    public function getPermissions(): array
    {
        return match($this) {
            self::ADMIN => ['create', 'read', 'update', 'delete', 'manage_users'],
            self::MODERATOR => ['create', 'read', 'update', 'delete'],
            self::USER => ['create', 'read', 'update'],
            self::GUEST => ['read'],
        };
    }

    public function getHierarchyLevel(): int
    {
        return match($this) {
            self::ADMIN => 4,
            self::MODERATOR => 3,
            self::USER => 2,
            self::GUEST => 1,
        };
    }
}

// Usage
$role = UserRole::ADMIN;
echo $role->getDisplayName();  // "Admin"
echo $role->canDelete() ? "Can delete" : "Cannot delete";
$permissions = $role->getPermissions();
```

### Pure Enums Methods

```php
<?php
// Pure enums (non-backed) with methods
enum StatusCode
{
    case SUCCESS;
    case WARNING;
    case ERROR;
    case CRITICAL;

    public function getSeverity(): int
    {
        return match($this) {
            self::SUCCESS => 0,
            self::WARNING => 1,
            self::ERROR => 2,
            self::CRITICAL => 3,
        };
    }

    public function isError(): bool
    {
        return $this === self::ERROR || $this === self::CRITICAL;
    }

    public function requiresAlert(): bool
    {
        return $this->getSeverity() >= 2;
    }
}

// Usage
$status = StatusCode::ERROR;
echo $status->getSeverity();      // 2
echo $status->isError() ? "Error" : "OK";
echo $status->requiresAlert() ? "Alert!" : "Normal";
```

---

## Backed Enums with Methods

### String-Backed Enums

```php
<?php
enum HttpMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case PATCH = 'PATCH';

    public function isSafe(): bool
    {
        return $this === self::GET || $this === self::PUT;
    }

    public function isIdempotent(): bool
    {
        return match($this) {
            self::GET, self::PUT, self::DELETE, self::PATCH => true,
            self::POST => false,
        };
    }

    public function getSuccessStatusCode(): int
    {
        return match($this) {
            self::GET => 200,
            self::POST => 201,
            self::PUT => 200,
            self::DELETE => 204,
            self::PATCH => 200,
        };
    }
}

// Usage
$method = HttpMethod::POST;
echo $method->value;                    // 'POST'
echo $method->getSuccessStatusCode();   // 201
echo $method->isSafe() ? "Safe" : "Unsafe";
```

### Integer-Backed Enums

```php
<?php
enum LogLevel: int
{
    case DEBUG = 0;
    case INFO = 1;
    case WARNING = 2;
    case ERROR = 3;
    case CRITICAL = 4;

    public function getName(): string
    {
        return match($this) {
            self::DEBUG => 'DEBUG',
            self::INFO => 'INFO',
            self::WARNING => 'WARNING',
            self::ERROR => 'ERROR',
            self::CRITICAL => 'CRITICAL',
        };
    }

    public function isHighPriority(): bool
    {
        return $this->value >= self::ERROR->value;
    }

    public static function fromNumericString(string $level): ?self
    {
        $numeric = (int)$level;
        return self::tryFrom($numeric);
    }
}

// Usage
$level = LogLevel::ERROR;
echo $level->value;             // 3
echo $level->getName();         // 'ERROR'
echo $level->isHighPriority();  // true
```

---

## Readonly Enums

### Readonly Properties in Enums

```php
<?php
// Enums with readonly backing
enum DatabaseDriver: string
{
    case MYSQL = 'mysql';
    case POSTGRESQL = 'postgresql';
    case SQLITE = 'sqlite';
    case MONGODB = 'mongodb';

    // Can't add regular properties, but backed value is readonly
    public function getPort(): int
    {
        return match($this) {
            self::MYSQL => 3306,
            self::POSTGRESQL => 5432,
            self::SQLITE => 0,  // N/A
            self::MONGODB => 27017,
        };
    }

    public function getDefaultDatabase(): string
    {
        return match($this) {
            self::MYSQL => 'mysql',
            self::POSTGRESQL => 'postgres',
            self::SQLITE => ':memory:',
            self::MONGODB => 'admin',
        };
    }
}

// Backed value cannot be changed
$driver = DatabaseDriver::MYSQL;
echo $driver->value;  // 'mysql' (readonly)
// $driver->value = 'different';  // Error!
```

---

## Practical Patterns

### Status Machine Pattern

```php
<?php
enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function canTransitionTo(self $nextStatus): bool
    {
        return match([$this, $nextStatus]) {
            [self::PENDING, self::PROCESSING] => true,
            [self::PENDING, self::CANCELLED] => true,
            [self::PROCESSING, self::SHIPPED] => true,
            [self::PROCESSING, self::CANCELLED] => true,
            [self::SHIPPED, self::DELIVERED] => true,
            default => false,
        };
    }

    public function getNextPossibleStatuses(): array
    {
        return match($this) {
            self::PENDING => [self::PROCESSING, self::CANCELLED],
            self::PROCESSING => [self::SHIPPED, self::CANCELLED],
            self::SHIPPED => [self::DELIVERED],
            self::DELIVERED => [],
            self::CANCELLED => [],
        };
    }
}

// Usage
class Order
{
    private OrderStatus $status = OrderStatus::PENDING;

    public function transition(OrderStatus $newStatus): void
    {
        if (!$this->status->canTransitionTo($newStatus)) {
            throw new Exception("Invalid transition");
        }

        $this->status = $newStatus;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }
}
```

### Feature Flag Pattern

```php
<?php
enum Feature: string
{
    case BETA_UI = 'beta_ui';
    case ADVANCED_ANALYTICS = 'advanced_analytics';
    case EXPORT_PDF = 'export_pdf';
    case DARK_MODE = 'dark_mode';
    case API_V2 = 'api_v2';

    public function isAvailable(UserRole $role): bool
    {
        return match([$this, $role]) {
            [self::BETA_UI, UserRole::ADMIN] => true,
            [self::BETA_UI, UserRole::MODERATOR] => true,
            [self::ADVANCED_ANALYTICS, UserRole::ADMIN] => true,
            [self::ADVANCED_ANALYTICS, UserRole::MODERATOR] => true,
            [self::EXPORT_PDF, UserRole::USER] => true,
            [self::DARK_MODE, UserRole::USER] => true,
            [self::API_V2, UserRole::ADMIN] => true,
            default => false,
        };
    }

    public function getMinimumPlanTier(): PlanTier
    {
        return match($this) {
            self::BETA_UI => PlanTier::PROFESSIONAL,
            self::ADVANCED_ANALYTICS => PlanTier::ENTERPRISE,
            self::EXPORT_PDF => PlanTier::BASIC,
            self::DARK_MODE => PlanTier::BASIC,
            self::API_V2 => PlanTier::PROFESSIONAL,
        };
    }
}

enum UserRole: string
{
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case USER = 'user';
}

enum PlanTier: string
{
    case FREE = 'free';
    case BASIC = 'basic';
    case PROFESSIONAL = 'professional';
    case ENTERPRISE = 'enterprise';
}
```

---

## Reflection and Discovery

### Enum Reflection

```php
<?php
// Access enum information via reflection
$reflection = new ReflectionEnum(OrderStatus::class);

// Get all cases
$cases = $reflection->getCases();
foreach ($cases as $case) {
    echo $case->name . ": " . $case->getValue() . "\n";
}

// Get specific case
$pending = $reflection->getCase('PENDING');
echo $pending->name;

// Check if backed
echo $reflection->isBacked() ? "Backed" : "Pure";

// Check backing type
if ($reflection->isBacked()) {
    echo "Backing type: " . $reflection->getBackingType();
}

// Get all methods
$methods = $reflection->getMethods();
foreach ($methods as $method) {
    echo $method->name . "\n";
}
```

### Dynamic Case Access

```php
<?php
enum Status: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}

// Try to get case from value
$value = 'active';
$status = Status::tryFrom($value);  // Status::ACTIVE or null

if ($status !== null) {
    echo "Found status: " . $status->name;
} else {
    echo "Unknown status";
}

// All cases
$allCases = Status::cases();  // [Status::ACTIVE, Status::INACTIVE]
foreach ($allCases as $case) {
    echo $case->name;
}
```

---

## Complete Example

### Comprehensive Status System

```php
<?php
declare(strict_types=1);

namespace App\Models;

enum TaskStatus: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case IN_REVIEW = 'in_review';
    case DONE = 'done';
    case CANCELLED = 'cancelled';

    // Display name
    public function getDisplayName(): string
    {
        return match($this) {
            self::TODO => 'To Do',
            self::IN_PROGRESS => 'In Progress',
            self::IN_REVIEW => 'In Review',
            self::DONE => 'Done',
            self::CANCELLED => 'Cancelled',
        };
    }

    // Color for UI
    public function getColor(): string
    {
        return match($this) {
            self::TODO => '#gray',
            self::IN_PROGRESS => '#blue',
            self::IN_REVIEW => '#yellow',
            self::DONE => '#green',
            self::CANCELLED => '#red',
        };
    }

    // Check if task is complete
    public function isComplete(): bool
    {
        return $this === self::DONE || $this === self::CANCELLED;
    }

    // Check if can transition
    public function canTransitionTo(self $nextStatus): bool
    {
        return match([$this, $nextStatus]) {
            [self::TODO, self::IN_PROGRESS] => true,
            [self::TODO, self::CANCELLED] => true,
            [self::IN_PROGRESS, self::IN_REVIEW] => true,
            [self::IN_PROGRESS, self::CANCELLED] => true,
            [self::IN_REVIEW, self::IN_PROGRESS] => true,
            [self::IN_REVIEW, self::DONE] => true,
            [self::IN_REVIEW, self::CANCELLED] => true,
            [self::DONE, self::CANCELLED] => true,  // Allow uncomplete
            default => false,
        };
    }

    // Get next possible statuses
    public function getNextStatuses(): array
    {
        return match($this) {
            self::TODO => [self::IN_PROGRESS, self::CANCELLED],
            self::IN_PROGRESS => [self::IN_REVIEW, self::CANCELLED],
            self::IN_REVIEW => [self::IN_PROGRESS, self::DONE, self::CANCELLED],
            self::DONE => [self::CANCELLED],
            self::CANCELLED => [],
        };
    }

    // Get all active statuses
    public static function activeStatuses(): array
    {
        return [
            self::TODO,
            self::IN_PROGRESS,
            self::IN_REVIEW,
        ];
    }

    // Get all completed statuses
    public static function completedStatuses(): array
    {
        return [
            self::DONE,
            self::CANCELLED,
        ];
    }
}

// Usage in Task model
class Task
{
    private TaskStatus $status = TaskStatus::TODO;

    public function transitionTo(TaskStatus $newStatus): void
    {
        if (!$this->status->canTransitionTo($newStatus)) {
            throw new Exception(
                "Cannot transition from {$this->status->value} to {$newStatus->value}"
            );
        }

        $this->status = $newStatus;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return !$this->status->isComplete();
    }
}

// Usage
$task = new Task();
$task->transitionTo(TaskStatus::IN_PROGRESS);  // OK
$task->transitionTo(TaskStatus::IN_REVIEW);    // OK
$task->transitionTo(TaskStatus::DONE);         // OK

// Get display information
echo $task->getStatus()->getDisplayName();  // "Done"
echo $task->getStatus()->getColor();        // "#green"
```

---

## See Also

- [PHP 8.3 Overview](0-php8.3-overview.md)
- [Typed Constants](2-typed-constants.md)
- [Override Attribute](3-override-attribute.md)
