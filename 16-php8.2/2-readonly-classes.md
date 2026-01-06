# Readonly Classes

## Overview

Learn about readonly classes in PHP 8.2, which provide a convenient way to create fully immutable classes with automatic property initialization and protection.

---

## Table of Contents

1. What are Readonly Classes
2. Basic Syntax
3. Properties
4. Constraints
5. Best Practices
6. Implementation
7. Real-world Examples
8. Complete Examples

---

## What are Readonly Classes

### Purpose

```php
<?php
// Before PHP 8.2: Manual immutability

class Point
{
    private float $x;
    private float $y;

    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getX(): float { return $this->x; }
    public function getY(): float { return $this->y; }

    // Must manually prevent modifications
}

// Problems:
// - Boilerplate code
// - Manual property protection
// - Easy to make mistakes
// - Verbose getters

// Solution: Readonly Classes (PHP 8.2)

readonly class Point
{
    public function __construct(
        public float $x,
        public float $y,
    ) {}
}

// Benefits:
// ✓ All properties immutable
// ✓ Constructor property promotion
// ✓ Less boilerplate
// ✓ Automatic protection
// ✓ Clear intent
```

### Key Features

```php
<?php
// Readonly class syntax:
readonly class ClassName
{
    // All properties are readonly
    public function __construct() {}
}

// Benefits:
// - All properties immutable after creation
// - Automatic property protection
// - Clear immutable intent
// - Reduced boilerplate
// - Type-safe
```

---

## Basic Syntax

### Declaration

```php
<?php
// Simple readonly class
readonly class User
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {}
}

// All properties are automatically readonly
$user = new User(1, 'John', 'john@example.com');
echo $user->name;  // John

// $user->name = 'Jane';  // Error: Cannot modify readonly property
```

### Properties

```php
<?php
readonly class Product
{
    public int $id;
    public string $name;
    public float $price;
    public string $category;

    public function __construct(
        int $id,
        string $name,
        float $price,
        string $category = 'General'
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->category = $category;
    }

    // Getters are optional (properties are public)
    public function getDescription(): string
    {
        return "{$this->name} ({$this->category})";
    }
}

// Usage
$product = new Product(1, 'Laptop', 999.99, 'Electronics');
echo $product->name;  // Laptop
```

### Typed Properties

```php
<?php
use DateTimeImmutable;

readonly class Order
{
    public function __construct(
        public int $id,
        public string $orderNumber,
        public float $total,
        public DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $shippedAt = null,
    ) {}
}

// Usage
$order = new Order(
    1,
    'ORD-001',
    150.00,
    new DateTimeImmutable(),
    null
);
```

---

## Advanced Features

### Methods in Readonly Classes

```php
<?php
readonly class Rectangle
{
    public function __construct(
        public float $width,
        public float $height,
    ) {}

    // Methods are allowed
    public function area(): float
    {
        return $this->width * $this->height;
    }

    public function perimeter(): float
    {
        return 2 * ($this->width + $this->height);
    }

    // Static methods
    public static function square(float $side): self
    {
        return new self($side, $side);
    }
}

// Usage
$rect = Rectangle::square(5);
echo $rect->area();        // 25
echo $rect->perimeter();   // 20
```

### Inheritance

```php
<?php
// Parent must also be readonly
readonly class Shape
{
    public function __construct(public string $color) {}
}

readonly class Circle extends Shape
{
    public function __construct(
        string $color,
        public float $radius,
    ) {
        parent::__construct($color);
    }

    public function area(): float
    {
        return pi() * $this->radius ** 2;
    }
}

// Usage
$circle = new Circle('red', 5.0);
echo $circle->color;      // red
echo $circle->radius;      // 5
```

### Interfaces

```php
<?php
interface Drawable
{
    public function draw(): string;
}

readonly class Canvas implements Drawable
{
    public function __construct(
        public int $width,
        public int $height,
    ) {}

    public function draw(): string
    {
        return "Canvas {$this->width}x{$this->height}";
    }
}

// Usage
$canvas = new Canvas(800, 600);
echo $canvas->draw();
```

---

## Constraints

### What's Not Allowed

```php
<?php
// ❌ Cannot modify properties after creation
readonly class Point
{
    public function __construct(
        public float $x,
        public float $y,
    ) {}
}

$point = new Point(1.0, 2.0);
// $point->x = 3.0;  // Error!

// ❌ Cannot have non-readonly parent
readonly class Child extends NonReadonlyParent  // Error!
{
}

// ❌ Cannot use __set magic method
readonly class Invalid
{
    public function __set(string $name, mixed $value): void {}  // Error!
}

// ❌ Cannot use __unset magic method
readonly class Invalid2
{
    public function __unset(string $name): void {}  // Error!
}
```

### Dynamic Properties

```php
<?php
readonly class User
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}

$user = new User(1, 'John');

// Cannot add new properties dynamically
// $user->age = 30;  // Error in strict mode!
```

---

## Implementation Patterns

### Value Objects

```php
<?php
use DateTimeImmutable;

readonly class EmailAddress
{
    public readonly string $value;

    public function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email: $email");
        }

        $this->value = $email;
    }

    public function toString(): string
    {
        return $this->value;
    }
}

// Usage
$email = new EmailAddress('john@example.com');
echo $email->value;
```

### Immutable Collections

```php
<?php
readonly class UserCollection
{
    private array $users;

    public function __construct(array $users)
    {
        // Validate and store
        $this->users = array_values($users);
    }

    public function getUsers(): array
    {
        return $this->users;
    }

    public function count(): int
    {
        return count($this->users);
    }

    public function first(): ?User
    {
        return $this->users[0] ?? null;
    }
}
```

### Data Transfer Objects

```php
<?php
readonly class UserDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public array $roles = [],
    ) {}

    public static function fromDatabase(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['email'],
            $data['roles'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->roles,
        ];
    }
}
```

---

## Real-world Examples

### Configuration Object

```php
<?php
readonly class DatabaseConfig
{
    public function __construct(
        public string $host,
        public int $port,
        public string $database,
        public string $username,
        public string $password,
        public array $options = [],
    ) {}

    public function getDsn(): string
    {
        return "mysql:host={$this->host}:{$this->port};dbname={$this->database}";
    }

    public static function fromEnv(): self
    {
        return new self(
            host: getenv('DB_HOST'),
            port: (int)getenv('DB_PORT'),
            database: getenv('DB_NAME'),
            username: getenv('DB_USER'),
            password: getenv('DB_PASS'),
        );
    }
}

// Usage
$config = DatabaseConfig::fromEnv();
$pdo = new PDO($config->getDsn(), $config->username, $config->password);
```

### API Response

```php
<?php
readonly class ApiResponse
{
    public function __construct(
        public int $status,
        public string $message,
        public array $data = [],
        public ?string $error = null,
    ) {}

    public static function success(int $status = 200, array $data = []): self
    {
        return new self($status, 'Success', $data);
    }

    public static function error(int $status, string $error): self
    {
        return new self($status, 'Error', error: $error);
    }

    public function toJson(): string
    {
        return json_encode([
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
            'error' => $this->error,
        ]);
    }
}

// Usage
$response = ApiResponse::success(data: ['user' => $user]);
echo $response->toJson();
```

---

## Complete Examples

### Full Application Example

```php
<?php
declare(strict_types=1);

namespace App;

readonly class User
{
    public function __construct(
        public int $id,
        public string $email,
        public string $name,
        public array $roles = [],
    ) {}

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}

readonly class AuthToken
{
    public function __construct(
        public string $token,
        public int $expiresAt,
        public int $userId,
    ) {}

    public function isExpired(): bool
    {
        return time() > $this->expiresAt;
    }
}

class AuthService
{
    public function authenticate(string $email, string $password): ?AuthToken
    {
        $user = $this->findUser($email);

        if (!$user || !$this->verifyPassword($password, $user->id)) {
            return null;
        }

        return new AuthToken(
            token: bin2hex(random_bytes(32)),
            expiresAt: time() + 3600,
            userId: $user->id,
        );
    }

    private function findUser(string $email): ?User
    {
        // Fetch from database
        return new User(1, $email, 'John', ['user']);
    }

    private function verifyPassword(string $password, int $userId): bool
    {
        // Verify password
        return true;
    }
}

// Usage
$auth = new AuthService();
$token = $auth->authenticate('john@example.com', 'password');

if ($token && !$token->isExpired()) {
    echo "Authenticated!";
}
```

---

## Best Practices

**Readonly Classes Checklist:**

1. ✅ Use for immutable value objects
2. ✅ Use for DTOs (Data Transfer Objects)
3. ✅ Use for configuration objects
4. ✅ Always initialize properties in constructor
5. ✅ Implement validation in constructor
6. ✅ Provide factory methods when needed
7. ✅ Make classes final when appropriate
8. ✅ Document immutability contracts

---

## See Also

- [PHP 8.2 Overview](0-php8.2-overview.md)
- [DNF Types](3-dnf-types.md)
- [Enumerations (PSR-1)](../15-php-standard-recommendation/2-basic-coding-standard.md)
