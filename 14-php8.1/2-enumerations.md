# Enumerations (Enums)

## Overview

Learn about PHP 8.1 enumerations, a powerful feature for creating type-safe enumerated types with built-in methods, traits, and backed values.

---

## Table of Contents

1. What are Enumerations
2. Pure Enums
3. Backed Enums
4. Enum Methods and Properties
5. Traits in Enums
6. Interfaces with Enums
7. Using Enums with Match
8. Real-world Patterns
9. Complete Examples

---

## What are Enumerations

### Purpose

```php
<?php
// Problem: Using class constants for statuses

class OrderStatus {
    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const COMPLETED = 'completed';
}

// Issues:
// - Can pass any string as status
// - No type safety
// - No IDE autocomplete
// - No connection between values

function process($status) {
    if ($status === OrderStatus::PENDING) {
        // Process
    }
}

process('invalid');  // No error!

// Solution: Enumerations (8.1+)
enum OrderStatus {
    case Pending;
    case Processing;
    case Completed;
}

function processOrder(OrderStatus $status): void {
    // Type-safe, IDE autocomplete, compile-time checking
}

processOrder(OrderStatus::Pending);  // ✓ Correct
// processOrder('pending');  // ✗ Type error
```

### Benefits

```
✓ Type safety - Can't pass wrong value
✓ IDE support - Full autocomplete
✓ Compile-time checking - Catch errors early
✓ Cleaner code - No magic strings
✓ Self-documenting - Clear intent
✓ Pattern matching - With match expressions
✓ Method support - Add behavior to values
✓ Trait support - Reuse code across enums
```

---

## Pure Enums

### Basic Definition

```php
<?php
// Define an enum
enum Status {
    case Draft;
    case Published;
    case Archived;
}

// Access cases
echo Status::Draft->name;   // "Draft"

// Use in type hints
function changeStatus(Status $newStatus): void {
    // ...
}

// Pass enum value
changeStatus(Status::Published);
```

### Multiple Cases

```php
<?php
enum Color {
    case Red;
    case Green;
    case Blue;
    case Yellow;
    case Orange;
}

enum Size {
    case XSmall;
    case Small;
    case Medium;
    case Large;
    case XLarge;
    case XXLarge;
}

enum Priority {
    case Low;
    case Medium;
    case High;
    case Critical;
}

// Use them
function displayColor(Color $color): string {
    return match($color) {
        Color::Red => '🔴 Red',
        Color::Green => '🟢 Green',
        Color::Blue => '🔵 Blue',
        Color::Yellow => '🟡 Yellow',
        Color::Orange => '🟠 Orange',
    };
}

echo displayColor(Color::Red);  // 🔴 Red
```

### Comparing Enums

```php
<?php
enum Status {
    case Pending;
    case Active;
    case Inactive;
}

$status1 = Status::Active;
$status2 = Status::Active;
$status3 = Status::Pending;

// Equality
var_dump($status1 === $status2);  // bool(true)
var_dump($status1 === $status3);  // bool(false)

// In conditions
if ($status1 === Status::Active) {
    echo "User is active";
}

// Switch statement
switch ($status1) {
    case Status::Active:
        echo "Active";
        break;
    case Status::Pending:
        echo "Pending";
        break;
}
```

---

## Backed Enums

### String-backed

```php
<?php
// Enum with string values
enum Environment: string {
    case Development = 'development';
    case Staging = 'staging';
    case Production = 'production';
}

// Access backing value
echo Environment::Production->value;  // "production"

// Useful for:
// - Database storage
// - API responses
// - Configuration files
// - External system integration
```

### Integer-backed

```php
<?php
// Enum with integer values
enum HttpStatus: int {
    case Ok = 200;
    case Created = 201;
    case BadRequest = 400;
    case Unauthorized = 401;
    case NotFound = 404;
    case ServerError = 500;
}

echo HttpStatus::NotFound->value;  // 404

// Use for codes
function sendResponse(HttpStatus $status): void {
    http_response_code($status->value);
}

sendResponse(HttpStatus::NotFound);  // Sets response code to 404
```

### From Database

```php
<?php
// Create enum from value
enum Status: string {
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}

// Convert database string to enum
function getPostStatus(string $status): Status {
    return Status::tryFrom($status);
}

// From query result
$row = $pdo->query("SELECT status FROM posts LIMIT 1")->fetch();
$status = Status::from($row['status']);  // Throws if invalid

// Safe version
$status = Status::tryFrom('draft');     // Returns Status | null
if ($status !== null) {
    echo $status->value;
}
```

---

## Enum Methods and Properties

### Adding Methods

```php
<?php
enum Status {
    case Draft;
    case Published;
    case Archived;
    
    // Instance method
    public function isPublic(): bool {
        return $this === Status::Published;
    }
    
    // Another method
    public function canEdit(): bool {
        return $this === Status::Draft;
    }
    
    // Static method
    public static function publishable(): array {
        return [Status::Draft];
    }
}

// Use methods
$status = Status::Draft;
echo $status->isPublic() ? 'Public' : 'Private';  // Private
echo $status->canEdit() ? 'Editable' : 'Read-only';  // Editable

// Static method
$editable = Status::publishable();
```

### Backed Enum Methods

```php
<?php
enum Environment: string {
    case Development = 'development';
    case Staging = 'staging';
    case Production = 'production';
    
    public function isProduction(): bool {
        return $this === Environment::Production;
    }
    
    public function getDbHost(): string {
        return match($this) {
            Environment::Development => 'localhost',
            Environment::Staging => 'staging-db.internal',
            Environment::Production => 'prod-db.aws.rds.amazonaws.com',
        };
    }
    
    public function getDebugMode(): bool {
        return !$this->isProduction();
    }
}

// Usage
$env = Environment::Production;
echo $env->getDbHost();  // prod-db.aws.rds.amazonaws.com
echo $env->getDebugMode();  // false
```

### Enum with Properties

```php
<?php
// Note: Enums cannot have instance properties (unlike classes)
// But backed enums have the 'value' property

enum Status: string {
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
    
    // Return calculated properties
    public function displayName(): string {
        return match($this) {
            Status::Draft => 'Draft (Not Published)',
            Status::Published => 'Published',
            Status::Archived => 'Archived (Inactive)',
        };
    }
    
    public function icon(): string {
        return match($this) {
            Status::Draft => '📝',
            Status::Published => '✅',
            Status::Archived => '📦',
        };
    }
}

// Use
echo Status::Draft->displayName();  // Draft (Not Published)
echo Status::Draft->icon();  // 📝
```

---

## Traits in Enums

### Using Traits

```php
<?php
trait TimestampFormatter {
    public function formatTime(int $timestamp): string {
        return date('Y-m-d H:i:s', $timestamp);
    }
}

enum LogLevel: string {
    case Debug = 'debug';
    case Info = 'info';
    case Warning = 'warning';
    case Error = 'error';
    case Critical = 'critical';
    
    use TimestampFormatter;
    
    public function isError(): bool {
        return $this === LogLevel::Error || 
               $this === LogLevel::Critical;
    }
}

// Use trait method
echo LogLevel::Info->formatTime(time());
```

### Multiple Traits

```php
<?php
trait Comparable {
    public function equals($other): bool {
        return $this === $other;
    }
}

trait Serializable {
    public function toArray(): array {
        return ['name' => $this->name];
    }
}

enum Status {
    case Active;
    case Inactive;
    
    use Comparable, Serializable;
}

$status = Status::Active;
var_dump($status->toArray());  // ['name' => 'Active']
```

---

## Interfaces with Enums

### Implementing Interfaces

```php
<?php
interface Loggable {
    public function getLogMessage(): string;
}

enum Priority: int implements Loggable {
    case Low = 1;
    case Medium = 2;
    case High = 3;
    case Critical = 4;
    
    public function getLogMessage(): string {
        return match($this) {
            Priority::Low => 'Low priority task',
            Priority::Medium => 'Medium priority task',
            Priority::High => 'High priority task',
            Priority::Critical => 'CRITICAL: Immediate action required',
        };
    }
}

// Type hint on interface
function log(Loggable $item): void {
    echo $item->getLogMessage();
}

log(Priority::Critical);  // CRITICAL: Immediate action required
```

### Multiple Interfaces

```php
<?php
interface Displayable {
    public function display(): string;
}

interface Comparable {
    public function compareTo($other): int;
}

enum PaymentStatus: string implements Displayable, Comparable {
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
    
    public function display(): string {
        return ucfirst($this->value);
    }
    
    public function compareTo($other): int {
        $order = [
            PaymentStatus::Pending => 1,
            PaymentStatus::Processing => 2,
            PaymentStatus::Completed => 3,
            PaymentStatus::Failed => 0,
        ];
        
        return $order[$this] <=> $order[$other];
    }
}

echo PaymentStatus::Pending->display();  // Pending
```

---

## Using Enums with Match

### Match Expressions

```php
<?php
enum HttpMethod: string {
    case Get = 'GET';
    case Post = 'POST';
    case Put = 'PUT';
    case Delete = 'DELETE';
    case Patch = 'PATCH';
}

function handleRequest(HttpMethod $method): string {
    return match($method) {
        HttpMethod::Get => 'Retrieving resource',
        HttpMethod::Post => 'Creating resource',
        HttpMethod::Put => 'Updating entire resource',
        HttpMethod::Patch => 'Updating part of resource',
        HttpMethod::Delete => 'Deleting resource',
    };
}

echo handleRequest(HttpMethod::Post);  // Creating resource
```

### Complex Match Logic

```php
<?php
enum Status {
    case Pending;
    case Active;
    case Paused;
    case Completed;
    case Failed;
}

function getStatusAction(Status $status): string {
    return match($status) {
        Status::Pending, Status::Active => 'Process task',
        Status::Paused => 'Resume task',
        Status::Completed, Status::Failed => 'Archive task',
    };
}

enum TransactionType: string {
    case Debit = 'debit';
    case Credit = 'credit';
    case Transfer = 'transfer';
}

function calculateFee(TransactionType $type, float $amount): float {
    return match($type) {
        TransactionType::Debit => $amount * 0.01,
        TransactionType::Credit => $amount * 0.005,
        TransactionType::Transfer => max($amount * 0.002, 2.00),
    };
}

echo calculateFee(TransactionType::Transfer, 1000);  // 2.00
```

---

## Real-world Patterns

### User Role Management

```php
<?php
enum Role: string {
    case Admin = 'admin';
    case Moderator = 'moderator';
    case User = 'user';
    case Guest = 'guest';
    
    public function canDeleteUsers(): bool {
        return $this === Role::Admin;
    }
    
    public function canBanUsers(): bool {
        return match($this) {
            Role::Admin, Role::Moderator => true,
            default => false,
        };
    }
    
    public function canEditPosts(): bool {
        return $this !== Role::Guest;
    }
    
    public function permissions(): array {
        return match($this) {
            Role::Admin => ['read', 'write', 'delete', 'manage-users'],
            Role::Moderator => ['read', 'write', 'delete'],
            Role::User => ['read', 'write'],
            Role::Guest => ['read'],
        };
    }
}

class User {
    public function __construct(
        public readonly string $name,
        public readonly Role $role,
    ) {}
    
    public function canPerform(string $action): bool {
        return in_array($action, $this->role->permissions());
    }
}

$admin = new User('John', Role::Admin);
echo $admin->canPerform('manage-users') ? 'Yes' : 'No';  // Yes
```

### Order Status Workflow

```php
<?php
enum OrderStatus: string {
    case New = 'new';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    
    public function canCancel(): bool {
        return match($this) {
            OrderStatus::New,
            OrderStatus::Confirmed,
            OrderStatus::Processing => true,
            default => false,
        };
    }
    
    public function canShip(): bool {
        return $this === OrderStatus::Processing;
    }
    
    public function nextStatus(): ?OrderStatus {
        return match($this) {
            OrderStatus::New => OrderStatus::Confirmed,
            OrderStatus::Confirmed => OrderStatus::Processing,
            OrderStatus::Processing => OrderStatus::Shipped,
            OrderStatus::Shipped => OrderStatus::Delivered,
            default => null,
        };
    }
}

class Order {
    public function __construct(
        public readonly string $id,
        public OrderStatus $status,
    ) {}
    
    public function processOrder(): void {
        if ($this->status->nextStatus()) {
            $this->status = $this->status->nextStatus();
        }
    }
    
    public function cancelOrder(): bool {
        if ($this->status->canCancel()) {
            $this->status = OrderStatus::Cancelled;
            return true;
        }
        return false;
    }
}
```

---

## Complete Examples

### Example 1: Logger with Enum Levels

```php
<?php
enum LogLevel: int {
    case Debug = 0;
    case Info = 1;
    case Warning = 2;
    case Error = 3;
    case Critical = 4;
    
    public function label(): string {
        return match($this) {
            LogLevel::Debug => 'DEBUG',
            LogLevel::Info => 'INFO',
            LogLevel::Warning => 'WARN',
            LogLevel::Error => 'ERROR',
            LogLevel::Critical => 'CRITICAL',
        };
    }
    
    public function shouldLog(LogLevel $minimumLevel): bool {
        return $this->value >= $minimumLevel->value;
    }
}

class Logger {
    private LogLevel $minimumLevel = LogLevel::Info;
    
    public function setMinimumLevel(LogLevel $level): void {
        $this->minimumLevel = $level;
    }
    
    public function log(LogLevel $level, string $message): void {
        if (!$level->shouldLog($this->minimumLevel)) {
            return;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        echo "[$timestamp] {$level->label()}: $message\n";
    }
    
    public function debug(string $message): void {
        $this->log(LogLevel::Debug, $message);
    }
    
    public function info(string $message): void {
        $this->log(LogLevel::Info, $message);
    }
    
    public function error(string $message): void {
        $this->log(LogLevel::Error, $message);
    }
}

// Usage
$logger = new Logger();
$logger->setMinimumLevel(LogLevel::Info);
$logger->debug('Debug message');  // Not logged
$logger->info('Application started');  // Logged
$logger->error('An error occurred');  // Logged
```

### Example 2: Database Query Builder

```php
<?php
enum JoinType: string {
    case Inner = 'INNER JOIN';
    case Left = 'LEFT JOIN';
    case Right = 'RIGHT JOIN';
    case Full = 'FULL OUTER JOIN';
}

enum OrderDirection: string {
    case Ascending = 'ASC';
    case Descending = 'DESC';
}

class QueryBuilder {
    private array $selects = ['*'];
    private string $from = '';
    private array $joins = [];
    private array $wheres = [];
    private array $orders = [];
    
    public function select(string ...$columns): self {
        $this->selects = $columns;
        return $this;
    }
    
    public function from(string $table): self {
        $this->from = $table;
        return $this;
    }
    
    public function join(
        string $table,
        string $condition,
        JoinType $type = JoinType::Inner
    ): self {
        $this->joins[] = [
            'table' => $table,
            'condition' => $condition,
            'type' => $type,
        ];
        return $this;
    }
    
    public function where(string $condition): self {
        $this->wheres[] = $condition;
        return $this;
    }
    
    public function orderBy(
        string $column,
        OrderDirection $direction = OrderDirection::Ascending
    ): self {
        $this->orders[] = "$column {$direction->value}";
        return $this;
    }
    
    public function toSql(): string {
        $sql = "SELECT " . implode(', ', $this->selects);
        $sql .= " FROM {$this->from}";
        
        foreach ($this->joins as $join) {
            $sql .= " {$join['type']->value} {$join['table']} ";
            $sql .= "ON {$join['condition']}";
        }
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        if (!empty($this->orders)) {
            $sql .= " ORDER BY " . implode(', ', $this->orders);
        }
        
        return $sql;
    }
}

// Usage
$query = (new QueryBuilder())
    ->select('users.name', 'posts.title')
    ->from('users')
    ->join('posts', 'users.id = posts.user_id', JoinType::Left)
    ->where('users.active = 1')
    ->orderBy('posts.created_at', OrderDirection::Descending);

echo $query->toSql();
// SELECT users.name, posts.title FROM users 
// LEFT JOIN posts ON users.id = posts.user_id 
// WHERE users.active = 1 ORDER BY posts.created_at DESC
```

---

## Key Takeaways

**Enumerations Checklist:**

1. ✅ Use for fixed set of values
2. ✅ Backed enums for database integration
3. ✅ Add methods for behavior
4. ✅ Use traits for code reuse
5. ✅ Implement interfaces for contracts
6. ✅ Use with match expressions
7. ✅ Document enum cases
8. ✅ Test enum transitions

---

## See Also

- [Readonly Properties](3-readonly-properties.md)
- [First-class Callables](4-first-class-callable-syntax.md)
- [Match Expressions (PHP 8.0)](../04-php8/match-expression.md)
