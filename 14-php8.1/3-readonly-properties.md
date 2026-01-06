# Readonly Properties

## Overview

Learn about readonly properties in PHP 8.1, enabling immutable object properties that can be set once and never modified again.

---

## Table of Contents

1. What are Readonly Properties
2. Basic Usage
3. Initialization Rules
4. Backed Enums and Readonly
5. Value Objects with Readonly
6. Constructor Property Promotion with Readonly
7. Performance Benefits
8. Common Patterns
9. Complete Examples

---

## What are Readonly Properties

### Purpose

```php
<?php
// Problem: Object properties can be modified after creation

class User {
    public string $email;
    
    public function __construct(string $email) {
        $this->email = $email;
    }
}

$user = new User('john@example.com');
echo $user->email;  // john@example.com

// Anyone can change it!
$user->email = 'malicious@example.com';  // Allowed, but dangerous

// Solution: Readonly properties (8.1+)
class User {
    public readonly string $email;
    
    public function __construct(string $email) {
        $this->email = $email;
    }
}

$user = new User('john@example.com');
$user->email = 'new@example.com';  // Error!
```

### Benefits

```
✓ Immutability - Cannot change after initialization
✓ Data integrity - Guarantees consistent state
✓ Thread safety - Readonly by nature
✓ Performance - Compiler optimizations
✓ Intent - Clear API contract
✓ Safer code - Less defensive programming
✓ Copy-on-write - Efficient memory usage
```

### Key Rules

```php
<?php
// 1. Readonly property can be set ONCE
public readonly string $value;  // Initially unset

public function __construct(string $value) {
    $this->value = $value;  // ✓ First assignment OK
    // $this->value = 'new'; // ✗ Second assignment ERROR
}

// 2. Can be set in constructor
// 3. Can be set via direct property assignment in constructor
// 4. Once set, cannot be unset or modified
// 5. Cannot have default value (must be initialized)
// 6. Works with typed properties
```

---

## Basic Usage

### Simple Example

```php
<?php
class Product {
    public readonly string $sku;
    public readonly float $price;
    
    public function __construct(string $sku, float $price) {
        $this->sku = $sku;
        $this->price = $price;
    }
}

$product = new Product('ABC-123', 29.99);
echo $product->sku;    // ABC-123
echo $product->price;  // 29.99

// $product->sku = 'DEF-456';  // Error: Cannot modify readonly property
```

### With Type Declarations

```php
<?php
class Address {
    public readonly string $street;
    public readonly string $city;
    public readonly string $zipcode;
    public readonly ?string $apartment;  // Nullable
    
    public function __construct(
        string $street,
        string $city,
        string $zipcode,
        ?string $apartment = null
    ) {
        $this->street = $street;
        $this->city = $city;
        $this->zipcode = $zipcode;
        $this->apartment = $apartment;
    }
}

$address = new Address('123 Main St', 'Boston', '02101');
echo $address->street;  // 123 Main St
// $address->city = 'New York';  // Error
```

### Visibility Levels

```php
<?php
class Secret {
    // Public readonly
    public readonly string $publicKey;
    
    // Protected readonly
    protected readonly string $internalKey;
    
    // Private readonly
    private readonly string $secretKey;
    
    public function __construct(string $secret) {
        $this->publicKey = str_repeat('*', strlen($secret));
        $this->internalKey = hash('md5', $secret);
        $this->secretKey = $secret;
    }
}

$secret = new Secret('mypassword');
echo $secret->publicKey;  // ****

// Parent access
class ExtendedSecret extends Secret {
    public function getInternalKey(): string {
        return $this->internalKey;  // Can access protected
    }
}
```

---

## Initialization Rules

### When to Set

```php
<?php
class Order {
    public readonly string $id;
    public readonly DateTime $createdAt;
    
    public function __construct(string $id) {
        // ✓ Set in constructor
        $this->id = $id;
        $this->createdAt = new DateTime();
    }
    
    // ✗ Cannot set after construction
    public function setId(string $newId): void {
        // $this->id = $newId;  // Error!
    }
}
```

### Uninitialized Access

```php
<?php
class User {
    public readonly string $email;
    // Note: Property is uninitialized at this point
    
    public function __construct(string $email) {
        $this->email = $email;
    }
    
    public function getEmail(): string {
        // isset returns false for uninitialized readonly
        return isset($this->email) ? $this->email : 'N/A';
    }
}

// $user = new User('...');
// Accessing uninitialized readonly throws error
```

### Multiple Initialization Paths

```php
<?php
class Config {
    public readonly string $apiKey;
    
    public function __construct(string $key = '') {
        if ($key !== '') {
            // Path 1: Set from parameter
            $this->apiKey = $key;
        } else {
            // Path 2: Load from environment
            $this->apiKey = getenv('API_KEY');
        }
        // Both paths must set the property before constructor returns
    }
}
```

---

## Backed Enums and Readonly

### Readonly with Enums

```php
<?php
enum Status {
    case Pending;
    case Active;
}

class Entity {
    // Enum is implicitly readonly
    public readonly Status $status;
    
    public function __construct(Status $status) {
        $this->status = $status;
    }
}

$entity = new Entity(Status::Active);
// $entity->status = Status::Pending;  // Error
```

### Combining Features

```php
<?php
enum PaymentMethod: string {
    case Card = 'card';
    case Bank = 'bank';
    case Crypto = 'crypto';
}

class Payment {
    public readonly string $transactionId;
    public readonly PaymentMethod $method;
    public readonly float $amount;
    public readonly DateTime $processedAt;
    
    public function __construct(
        PaymentMethod $method,
        float $amount
    ) {
        $this->transactionId = uniqid('txn_');
        $this->method = $method;
        $this->amount = $amount;
        $this->processedAt = new DateTime();
    }
}
```

---

## Value Objects with Readonly

### Money Value Object

```php
<?php
class Money {
    public readonly float $amount;
    public readonly string $currency;
    
    public function __construct(float $amount, string $currency = 'USD') {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount must be positive');
        }
        
        $this->amount = $amount;
        $this->currency = $currency;
    }
    
    public function add(Money $other): Money {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Currency mismatch');
        }
        
        return new Money(
            $this->amount + $other->amount,
            $this->currency
        );
    }
    
    public function multiply(float $multiplier): Money {
        return new Money(
            $this->amount * $multiplier,
            $this->currency
        );
    }
    
    public function equals(Money $other): bool {
        return $this->amount === $other->amount &&
               $this->currency === $other->currency;
    }
}

// Usage
$price = new Money(29.99, 'USD');
$tax = $price->multiply(0.1);
$total = $price->add($tax);

echo $total->amount;  // 32.989
// $price->amount = 100;  // Error: Cannot modify
```

### Date Range Value Object

```php
<?php
class DateRange {
    public readonly DateTime $start;
    public readonly DateTime $end;
    
    public function __construct(DateTime $start, DateTime $end) {
        if ($start > $end) {
            throw new InvalidArgumentException('Start must be before end');
        }
        
        $this->start = clone $start;
        $this->end = clone $end;
    }
    
    public function contains(DateTime $date): bool {
        return $date >= $this->start && $date <= $this->end;
    }
    
    public function days(): int {
        return (int)$this->end->diff($this->start)->days;
    }
    
    public function overlaps(DateRange $other): bool {
        return $this->start <= $other->end && 
               $this->end >= $other->start;
    }
}

// Usage
$range = new DateRange(
    new DateTime('2024-01-01'),
    new DateTime('2024-12-31')
);

echo $range->days();  // 366 (leap year)
echo $range->contains(new DateTime('2024-06-15')) ? 'Yes' : 'No';  // Yes
```

---

## Constructor Property Promotion with Readonly

### Promotion Syntax

```php
<?php
// Before PHP 8.0
class User {
    public string $name;
    public string $email;
    
    public function __construct(string $name, string $email) {
        $this->name = $name;
        $this->email = $email;
    }
}

// PHP 8.0: Constructor promotion
class User {
    public function __construct(
        public string $name,
        public string $email,
    ) {}
}

// PHP 8.1: With readonly
class User {
    public function __construct(
        public readonly string $name,
        public readonly string $email,
    ) {}
}

// Clean and immutable!
$user = new User('John', 'john@example.com');
```

### Mixing Properties

```php
<?php
class Article {
    public function __construct(
        public readonly string $title,
        public readonly string $slug,
        public readonly DateTime $publishedAt,
        public string $content = '',  // Can be modified
        private readonly array $tags = [],  // Private readonly
    ) {}
}

$article = new Article(
    'PHP 8.1 Features',
    'php-8-1-features',
    new DateTime()
);

// Can modify non-readonly
$article->content = 'Updated content';

// Cannot modify readonly
// $article->title = 'New Title';  // Error
```

---

## Performance Benefits

### Memory Efficiency

```php
<?php
// Readonly enables copy-on-write optimization
class Point {
    public function __construct(
        public readonly int $x,
        public readonly int $y,
    ) {}
}

$p1 = new Point(10, 20);
$p2 = $p1;  // Internally just reference (copy-on-write)

// No memory overhead because readonly prevents modification
```

### Compiler Optimizations

```php
<?php
// Compiler can assume readonly never changes
class User {
    public function __construct(
        public readonly string $email,
    ) {}
    
    public function isValidEmail(): bool {
        // Compiler can inline/cache this check
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
```

---

## Common Patterns

### Immutable Update

```php
<?php
class UserProfile {
    public function __construct(
        public readonly string $username,
        public readonly string $email,
        public readonly string $bio = '',
    ) {}
    
    // Return new instance instead of modifying
    public function updateBio(string $newBio): UserProfile {
        return new UserProfile(
            $this->username,
            $this->email,
            $newBio
        );
    }
    
    public function updateEmail(string $newEmail): UserProfile {
        return new UserProfile(
            $this->username,
            $newEmail,
            $this->bio
        );
    }
}

// Usage
$profile = new UserProfile('john_doe', 'john@example.com');
$updated = $profile->updateBio('Software Developer');
// Original $profile unchanged
```

### Builder with Readonly

```php
<?php
class User {
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly bool $isActive,
    ) {}
}

class UserBuilder {
    private string $name = '';
    private string $email = '';
    private ?string $phone = null;
    private bool $isActive = true;
    
    public function name(string $name): self {
        $this->name = $name;
        return $this;
    }
    
    public function email(string $email): self {
        $this->email = $email;
        return $this;
    }
    
    public function phone(?string $phone): self {
        $this->phone = $phone;
        return $this;
    }
    
    public function inactive(): self {
        $this->isActive = false;
        return $this;
    }
    
    public function build(): User {
        return new User(
            $this->name,
            $this->email,
            $this->phone,
            $this->isActive
        );
    }
}

// Usage
$user = (new UserBuilder())
    ->name('John Doe')
    ->email('john@example.com')
    ->phone('555-1234')
    ->build();
```

---

## Complete Examples

### Example 1: Immutable Configuration

```php
<?php
class DatabaseConfig {
    public function __construct(
        public readonly string $host,
        public readonly int $port,
        public readonly string $database,
        public readonly string $user,
        public readonly string $password,
        public readonly array $options = [],
    ) {}
    
    public function getDsn(): string {
        return sprintf(
            'mysql:host=%s;port=%d;dbname=%s',
            $this->host,
            $this->port,
            $this->database
        );
    }
    
    public static function fromEnv(): self {
        return new self(
            host: getenv('DB_HOST'),
            port: (int)getenv('DB_PORT'),
            database: getenv('DB_NAME'),
            user: getenv('DB_USER'),
            password: getenv('DB_PASSWORD'),
        );
    }
}

// Usage
$config = DatabaseConfig::fromEnv();
$pdo = new PDO(
    $config->getDsn(),
    $config->user,
    $config->password,
    $config->options
);
```

### Example 2: Event Classes

```php
<?php
class UserCreatedEvent {
    public function __construct(
        public readonly int $userId,
        public readonly string $email,
        public readonly DateTime $createdAt = new DateTime(),
    ) {}
}

class OrderPlacedEvent {
    public function __construct(
        public readonly int $orderId,
        public readonly int $userId,
        public readonly float $total,
        public readonly DateTime $placedAt = new DateTime(),
        public readonly array $items = [],
    ) {}
}

class EventDispatcher {
    private array $listeners = [];
    
    public function listen(string $eventClass, callable $callback): void {
        $this->listeners[$eventClass][] = $callback;
    }
    
    public function dispatch(object $event): void {
        $eventClass = $event::class;
        
        foreach ($this->listeners[$eventClass] ?? [] as $callback) {
            $callback($event);
        }
    }
}

// Usage
$dispatcher = new EventDispatcher();

$dispatcher->listen(UserCreatedEvent::class, function(UserCreatedEvent $event) {
    echo "User {$event->email} created at {$event->createdAt->format('Y-m-d')}";
});

$event = new UserCreatedEvent(
    userId: 123,
    email: 'john@example.com'
);
$dispatcher->dispatch($event);
```

### Example 3: Entity with Readonly Identity

```php
<?php
class Entity {
    protected function __construct(
        public readonly string $id,
        public readonly DateTime $createdAt,
    ) {}
    
    public static function create(string $id): static {
        return new static(
            id: $id,
            createdAt: new DateTime()
        );
    }
    
    public static function fromDatabase(array $row): static {
        return new static(
            id: $row['id'],
            createdAt: DateTime::createFromFormat('Y-m-d H:i:s', $row['created_at'])
        );
    }
}

class User extends Entity {
    public function __construct(
        string $id,
        DateTime $createdAt,
        public readonly string $email,
        public readonly string $name,
    ) {
        parent::__construct($id, $createdAt);
    }
    
    public static function create(string $email, string $name): self {
        return new self(
            id: uniqid('user_'),
            createdAt: new DateTime(),
            email: $email,
            name: $name
        );
    }
}

// Usage
$user = User::create('john@example.com', 'John Doe');
echo $user->id;  // user_...
echo $user->name;  // John Doe
// Identity cannot be changed
```

---

## Key Takeaways

**Readonly Properties Checklist:**

1. ✅ Use for immutable properties
2. ✅ Set once in constructor
3. ✅ Combine with type declarations
4. ✅ Use with constructor promotion
5. ✅ Return new instances for updates
6. ✅ Leverage for value objects
7. ✅ Document immutability contracts
8. ✅ Consider performance benefits

---

## See Also

- [Enumerations](2-enumerations.md)
- [Constructor Property Promotion (PHP 8.0)](../04-php8/constructor-property-promotion.md)
- [First-class Callables](4-first-class-callable-syntax.md)
