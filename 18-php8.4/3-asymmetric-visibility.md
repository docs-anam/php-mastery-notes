# Asymmetric Visibility

## Overview

Asymmetric visibility allows properties to have different visibility levels for read and write operations, enabling better encapsulation without boilerplate getter/setter methods.

---

## Table of Contents

1. Introduction to Asymmetric Visibility
2. Basic Syntax
3. Public Read with Private Write
4. Protected Read with Private Write
5. Advanced Patterns
6. Encapsulation Benefits
7. Immutability Patterns
8. Best Practices
9. Complete Examples

---

## Introduction to Asymmetric Visibility

### What Is Asymmetric Visibility?

```php
<?php
// Set different visibility for read vs write

class BankAccount
{
    // Public read, private write
    public private(set) int $balance = 0;

    public function deposit(int $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Deposit must be positive');
        }
        $this->balance += $amount;
    }

    public function withdraw(int $amount): void
    {
        if ($amount <= 0 || $amount > $this->balance) {
            throw new InvalidArgumentException('Invalid withdrawal');
        }
        $this->balance -= $amount;
    }
}

// Usage
$account = new BankAccount();
echo $account->balance;        // ✓ Can read
// $account->balance = 500;     // ✗ Cannot write directly

$account->deposit(1000);       // ✓ Must use method
echo $account->balance;        // 1000

// Benefits:
// ✓ Protects invariants
// ✓ No getBalance()/setBalance() methods needed
// ✓ Looks like normal property access
// ✓ Forces business logic
```

### Why Use Asymmetric Visibility?

```php
<?php
// Before: Tedious getter/setter pattern
class OldProduct
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

// After: Clean property syntax with protection
class NewProduct
{
    public float $price {
        get => $this->price;
        set => $this->price = max(0.0, $value);
    }
}

// Usage comparison
$old = new OldProduct();
$old->setPrice(99.99);
echo $old->getPrice();  // Verbose

$new = new NewProduct();
$new->price = 99.99;
echo $new->price;       // Clean and readable!
```

---

## Basic Syntax

### Asymmetric Visibility Modifiers

```php
<?php
// Different visibility combinations

class VisibilityExamples
{
    // Public read, private write (most common)
    public private(set) string $id = '';

    // Public read, protected write
    public protected(set) int $version = 1;

    // Protected read, private write
    protected private(set) array $data = [];

    // Private read, protected write (rare)
    private protected(set) string $internal = '';

    // Regular public (no asymmetry)
    public string $name = '';

    // Regular private
    private string $secret = '';
}

// Syntax rules:
// 1. Write modifier goes in parentheses: private(set)
// 2. Read modifier is on the left: public private(set)
// 3. Default is same visibility for both if not specified
// 4. Works with properties and hooks
```

### Visibility Levels

```php
<?php
// Visibility hierarchy

class VisibilityHierarchy
{
    // Outside class, child class, and inside class can all read
    // Only inside class can write
    public private(set) string $uuid = '';

    // Outside class can only read (not write)
    // Child class can read and write
    // Inside class can read and write
    public protected(set) int $count = 0;

    // Even child classes can't read or write
    // Only inside class can access
    private string $secret = '';

    // Outside class can only read (not write)
    // Child class cannot read or write
    protected private(set) string $internal = '';
}

// Access matrix:
//                    | Outside | Child | Inside |
// public private     | R       | R     | RW     |
// public protected   | R       | RW    | RW     |
// protected private  | X       | X     | RW     |
```

---

## Public Read with Private Write

### Immutable Public Data

```php
<?php
// Most common pattern - public read, private write

class User
{
    public private(set) int $id = 0;
    public private(set) string $email = '';
    public private(set) string $username = '';
    public private(set) DateTimeImmutable $createdAt;

    public function __construct(int $id, string $email, string $username)
    {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
        $this->createdAt = new DateTimeImmutable();
    }

    public function changeEmail(string $newEmail): void
    {
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email');
        }
        $this->email = $newEmail;
    }

    public function changeUsername(string $newUsername): void
    {
        if (strlen($newUsername) < 3) {
            throw new InvalidArgumentException('Username too short');
        }
        $this->username = $newUsername;
    }
}

// Usage
$user = new User(1, 'john@example.com', 'johndoe');
echo $user->id;            // ✓ Can read
echo $user->email;         // ✓ Can read
echo $user->createdAt;     // ✓ Can read

// $user->email = 'hack@example.com';  // ✗ Cannot write
$user->changeEmail('newemail@example.com');  // ✓ Must use method
```

### Validated Properties

```php
<?php
// Enforce validation through asymmetric visibility

class Product
{
    public private(set) string $sku = '';
    public private(set) string $name = '';
    public private(set) float $price = 0;
    public private(set) int $quantity = 0;

    private function validateSku(string $sku): string
    {
        if (!preg_match('/^[A-Z0-9]{5,10}$/', $sku)) {
            throw new InvalidArgumentException('Invalid SKU format');
        }
        return $sku;
    }

    private function validateName(string $name): string
    {
        $name = trim($name);
        if (strlen($name) < 3) {
            throw new InvalidArgumentException('Name too short');
        }
        return $name;
    }

    public function __construct(string $sku, string $name, float $price, int $quantity)
    {
        $this->sku = $this->validateSku($sku);
        $this->name = $this->validateName($name);
        $this->price = max(0.0, $price);
        $this->quantity = max(0, $quantity);
    }

    public function updateStock(int $quantity): void
    {
        $this->quantity = max(0, $quantity);
    }

    public function updatePrice(float $price): void
    {
        if ($price < 0) {
            throw new InvalidArgumentException('Price cannot be negative');
        }
        $this->price = $price;
    }
}

// Usage
$product = new Product('SKU001', 'Widget', 19.99, 100);
echo $product->price;                 // ✓ Can read
// $product->price = 5;               // ✗ Cannot write directly
$product->updatePrice(24.99);        // ✓ Through method
```

---

## Protected Read with Private Write

### Family-Only Properties

```php
<?php
// Properties accessible in child classes but controlled modification

class BaseEntity
{
    public protected(set) int $id = 0;
    public protected(set) DateTimeImmutable $createdAt;
    public protected(set) DateTimeImmutable $updatedAt;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    protected function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}

class User extends BaseEntity
{
    public protected(set) string $email = '';
    public protected(set) string $username = '';

    public function __construct(int $id, string $email, string $username)
    {
        parent::__construct($id);
        $this->email = $email;
        $this->username = $username;
    }

    public function update(string $email, string $username): void
    {
        // Can read parent protected properties
        echo $this->createdAt;

        // Can write parent protected properties
        $this->email = $email;
        $this->username = $username;

        // Call protected method
        $this->updateTimestamp();
    }
}

// Outside class usage
$user = new User(1, 'john@example.com', 'john');
echo $user->id;          // ✓ Can read (it's public protected)
echo $user->email;       // ✓ Can read
// $user->email = '';    // ✗ Cannot write
// $user->updateTimestamp();  // ✗ Cannot call (protected method)
```

### Inheritance Hierarchy

```php
<?php
// Controlled modification in inheritance

class Repository
{
    public protected(set) array $items = [];

    public function add(object $item): void
    {
        $this->items[] = $item;
    }

    public function getAll(): array
    {
        return $this->items;
    }
}

class UserRepository extends Repository
{
    public function findById(int $id): ?object
    {
        // Can access protected(set) property in child class
        return current(array_filter($this->items, fn($u) => $u->id === $id));
    }

    public function clear(): void
    {
        // Can modify in child class
        $this->items = [];
    }
}

// Usage
$repo = new UserRepository();
echo count($repo->items);      // ✓ Can read
// $repo->items = [];           // ✗ Cannot write (outside class)
$repo->clear();                // ✓ Child class can modify

// In child class it's different
class ExtendedRepository extends UserRepository
{
    public function reset(): void
    {
        // Child class can modify
        $this->items = [];
    }
}
```

---

## Advanced Patterns

### Computed Properties with Asymmetric Visibility

```php
<?php
// Combine hooks with asymmetric visibility

class Temperature
{
    private float $celsius = 0;

    // Fahrenheit is public readable, writable through validation
    public float $fahrenheit {
        get => $this->celsius * 9/5 + 32;
        set => $this->celsius = ($value - 32) * 5/9;
    }

    // Status is computed and read-only
    public string $status {
        get => match(true) {
            $this->celsius < 0 => 'Freezing',
            $this->celsius < 15 => 'Cold',
            $this->celsius < 25 => 'Comfortable',
            default => 'Hot',
        };
    }

    public function __construct(float $celsius)
    {
        $this->celsius = $celsius;
    }
}

// Usage
$temp = new Temperature(25);
echo $temp->fahrenheit;    // 77
echo $temp->status;        // Comfortable

$temp->fahrenheit = 86;    // Convert from Fahrenheit
echo $temp->status;        // Hot
```

### Multi-Level Access Control

```php
<?php
// Complex access patterns for different stakeholder types

class Document
{
    public private(set) string $id = '';
    public private(set) string $title = '';
    public private(set) string $content = '';
    public private(set) string $status = 'draft';
    public protected(set) string $author = '';
    public protected(set) string $reviewer = '';
    private string $internalNotes = '';

    public function __construct(string $title, string $author)
    {
        $this->id = uniqid();
        $this->title = $title;
        $this->author = $author;
    }

    // Only reviewer can update status
    public function setStatus(string $status): void
    {
        if (!in_array($status, ['draft', 'review', 'approved', 'published'])) {
            throw new InvalidArgumentException('Invalid status');
        }
        $this->status = $status;
    }

    // Only admin/system can update content
    public function updateContent(string $content): void
    {
        $this->content = $content;
    }
}

// Access levels:
// Public:     Can read id, title, content, status
// Child:      Can read/write author, reviewer
// Inside:     Can read/write content and status through methods
```

---

## Encapsulation Benefits

### Protection Against Invariant Violations

```php
<?php
// Prevent invalid state with asymmetric visibility

class EmailQueue
{
    public private(set) int $count = 0;
    public private(set) array $messages = [];
    public private(set) bool $isProcessing = false;

    public function enqueue(string $recipient, string $subject, string $body): void
    {
        if ($this->isProcessing) {
            throw new RuntimeException('Cannot enqueue while processing');
        }

        $this->messages[] = [
            'recipient' => $recipient,
            'subject' => $subject,
            'body' => $body,
            'timestamp' => time(),
        ];

        $this->count++;
    }

    public function process(): void
    {
        if (empty($this->messages)) {
            return;
        }

        $this->isProcessing = true;

        try {
            foreach ($this->messages as $message) {
                // Send email
            }
            $this->messages = [];
            $this->count = 0;
        } finally {
            $this->isProcessing = false;
        }
    }
}

// Usage - cannot break invariants
$queue = new EmailQueue();
$queue->enqueue('user@example.com', 'Welcome', 'Hello!');

echo $queue->count;        // ✓ 1
// $queue->count = 100;    // ✗ Cannot break invariant
// $queue->isProcessing = true;  // ✗ Cannot cause invalid state

$queue->process();         // ✓ Proper state management
```

---

## Immutability Patterns

### Read-Only After Construction

```php
<?php
// Use asymmetric visibility for immutable objects

class ImmutableUser
{
    public private(set) int $id;
    public private(set) string $email;
    public private(set) string $name;
    public private(set) DateTimeImmutable $createdAt;

    public function __construct(int $id, string $email, string $name)
    {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();
    }

    // All properties visible but immutable after construction
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }

    // Only way to get modified instance is through copy constructor
    public function withEmail(string $email): self
    {
        return new self($this->id, $email, $this->name);
    }
}

// Usage
$user = new ImmutableUser(1, 'john@example.com', 'John');
echo $user->email;         // ✓ Readable

// $user->email = 'hack@example.com';  // ✗ Cannot modify
$newUser = $user->withEmail('newemail@example.com');  // ✓ Create new instance
```

---

## Best Practices

### When to Use Asymmetric Visibility

```php
<?php
// ✓ DO: Use for value objects and entities
class OrderItem
{
    public private(set) string $sku;
    public private(set) int $quantity;
    public private(set) float $price;

    public function __construct(string $sku, int $quantity, float $price)
    {
        $this->sku = $sku;
        $this->quantity = $quantity;
        $this->price = $price;
    }
}

// ✓ DO: Use when you need validation
class Age
{
    public private(set) int $value;

    public function __construct(int $age)
    {
        if ($age < 0 || $age > 150) {
            throw new InvalidArgumentException('Invalid age');
        }
        $this->value = $age;
    }
}

// ✗ DON'T: Use for simple mutable data
// Use public properties instead:
class ConfigData
{
    public string $host = '';
    public int $port = 0;
    public bool $debug = false;
}

// ✓ DO: Use protected(set) in base classes for inheritance
class BaseService
{
    public protected(set) array $config = [];

    protected function updateConfig(array $config): void
    {
        $this->config = $config;
    }
}

// ✗ DON'T: Overuse - keep it simple
class BadExample
{
    public private(set) string $a;
    public private(set) string $b;
    public private(set) string $c;
    // Too many controlled properties - use methods instead
}
```

---

## Complete Examples

### Banking System with Asymmetric Visibility

```php
<?php
declare(strict_types=1);

namespace App\Banking;

final class Account
{
    public private(set) string $accountNumber;
    public private(set) string $ownerName;
    public private(set) float $balance;
    public private(set) string $currency;
    public protected(set) bool $isFrozen = false;
    private array $transactions = [];

    public function __construct(string $accountNumber, string $ownerName, float $initialBalance = 0)
    {
        $this->accountNumber = $accountNumber;
        $this->ownerName = $ownerName;
        $this->balance = max(0.0, $initialBalance);
        $this->currency = 'USD';
        $this->recordTransaction('OPEN', $initialBalance);
    }

    public function deposit(float $amount): void
    {
        $this->validateOperation('deposit');
        if ($amount <= 0) {
            throw new InvalidArgumentException('Deposit amount must be positive');
        }

        $this->balance += $amount;
        $this->recordTransaction('DEPOSIT', $amount);
    }

    public function withdraw(float $amount): void
    {
        $this->validateOperation('withdraw');
        if ($amount <= 0) {
            throw new InvalidArgumentException('Withdrawal amount must be positive');
        }
        if ($amount > $this->balance) {
            throw new RuntimeException('Insufficient funds');
        }

        $this->balance -= $amount;
        $this->recordTransaction('WITHDRAW', -$amount);
    }

    public function transfer(Account $recipient, float $amount): void
    {
        $this->validateOperation('transfer');
        if ($amount <= 0) {
            throw new InvalidArgumentException('Transfer amount must be positive');
        }
        if ($amount > $this->balance) {
            throw new RuntimeException('Insufficient funds for transfer');
        }

        $this->balance -= $amount;
        $recipient->balance += $amount;

        $this->recordTransaction('TRANSFER_OUT', -$amount);
        $recipient->recordTransaction('TRANSFER_IN', $amount);
    }

    public function freeze(): void
    {
        $this->isFrozen = true;
    }

    public function unfreeze(): void
    {
        $this->isFrozen = false;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getTransactionHistory(): array
    {
        return $this->transactions;
    }

    private function validateOperation(string $operation): void
    {
        if ($this->isFrozen) {
            throw new RuntimeException("Account is frozen, cannot $operation");
        }
    }

    private function recordTransaction(string $type, float $amount): void
    {
        $this->transactions[] = [
            'type' => $type,
            'amount' => $amount,
            'balance' => $this->balance,
            'timestamp' => new DateTimeImmutable(),
        ];
    }

    public function __toString(): string
    {
        return sprintf(
            "%s (%s): %s %.2f",
            $this->accountNumber,
            $this->ownerName,
            $this->currency,
            $this->balance
        );
    }
}

// Usage
$account1 = new Account('ACC-001', 'John Doe', 1000);
$account2 = new Account('ACC-002', 'Jane Smith', 500);

echo $account1->accountNumber;   // ✓ ACC-001
echo $account1->balance;         // ✓ 1000.00

// Cannot directly modify
// $account1->balance = 5000;     // ✗ Error

$account1->deposit(500);         // ✓ 1500.00
$account1->transfer($account2, 200);  // ✓ Transfers 200 to account2

$account1->freeze();             // ✓ Account frozen
// $account1->withdraw(100);      // ✗ Account frozen error
```

---

## See Also

- [PHP 8.4 Overview](0-php8.4-overview.md)
- [Property Hooks](2-property-hooks.md)
- [Class Constant Visibility](4-class-constant-visibility.md)
- [Type System Improvements](5-type-system.md)
