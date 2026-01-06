# PSR-12: Extended Coding Style Guide

## Overview

Learn about PSR-12, the extended coding style guide that builds on PSR-2 and PSR-1, providing comprehensive style requirements for modern PHP code.

---

## Table of Contents

1. What is PSR-12
2. Code Structure
3. Declarations
4. Operators and Control Structures
5. Closures and Callables
6. Methods and Functions
7. Type Declarations
8. Namespace and Use Statements
9. Real-world Examples
10. Complete Examples

---

## What is PSR-12

### Purpose

```php
<?php
// Before PSR-12: Inconsistent code style

class User{public $name;public $email;function __construct($name,$email){
$this->name=$name;$this->email=$email;}function getName(){return $this->name;}
}

// Problems:
// - Inconsistent formatting
// - Hard to read
// - Difficult to maintain
// - Merge conflicts

// Solution: PSR-12 (standardized style)

class User
{
    private string $name;
    private string $email;

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

// Benefits:
// ✓ Consistent code style
// ✓ Easier to read and maintain
// ✓ Better collaboration
// ✓ Automated enforcement
```

### Key Principles

```
PSR-12 extends PSR-2 with:
- 4 spaces for indentation
- Consistent line endings
- Line length recommendations
- Type declarations
- Constructor property promotion
- Union types
- Named arguments
```

---

## Code Structure

### File Structure

```php
<?php
// 1. Opening PHP tag at top of file
<?php

// 2. File-level docblock (optional)
/**
 * This file contains the User class.
 *
 * @author John Doe <john@example.com>
 */

// 3. Declare statements
declare(strict_types=1);

// 4. Namespace declaration
namespace App\Models;

// 5. Use statements (grouped and sorted)
use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;

// 6. Class/Interface/Trait declaration
class User implements Countable, IteratorAggregate
{
    // Class content
}
```

### Blank Lines

```php
<?php
namespace App;

// One blank line between:
// - Namespace and first use statement
// - Use statements and first class/interface/trait
// - Class constants and properties
// - Properties and first method
// - Methods

class User
{

    // Good: blank line before constants
    public const ADMIN = 'admin';
    public const USER = 'user';

    // Good: blank line between constants and properties
    private string $name = '';
    private string $email = '';

    // Good: blank line before first method
    public function getName(): string
    {
        return $this->name;
    }

    // Good: blank line between methods
    public function getEmail(): string
    {
        return $this->email;
    }
}
```

### Line Length

```php
<?php
// Recommended maximum: 80-120 characters per line
// Hard limit: 120 characters for readability

// Good - reasonable length
if ($user->isActive() && $user->hasPermission('edit')) {
    $user->update($data);
}

// Bad - too long
if ($user->isActive() && $user->hasPermission('edit') && !$user->isSuspended() && $user->verificationStatus() == 'verified') {
    $user->update($data);
}

// Better - broken into multiple lines
if ($user->isActive() 
    && $user->hasPermission('edit') 
    && !$user->isSuspended() 
    && $user->verificationStatus() === 'verified'
) {
    $user->update($data);
}
```

---

## Declarations

### Class Declaration

```php
<?php
// Opening brace on same line

namespace App\Models;

use ArrayIterator;
use Countable;
use IteratorAggregate;

final class User implements Countable, IteratorAggregate
{
    // Content
}

// Abstract class
abstract class BaseModel
{
    // Content
}

// Interface
interface UserInterface
{
    // Content
}

// Trait
trait TimestampTrait
{
    // Content
}
```

### Property Declaration

```php
<?php
class User
{
    // Visibility required for properties
    private string $name;
    protected int $age;
    public string $email;

    // Properties grouped by visibility
    private string $id;
    private string $status;

    protected string $createdAt;
    protected string $updatedAt;

    public string $publicField;

    // Type hints required
    private ?string $nickname = null;
    private array $tags = [];
    private DateTimeImmutable $createdDate;
}
```

---

## Operators and Control Structures

### Operator Spacing

```php
<?php
// Binary operators: space on both sides
$sum = $a + $b;
$product = $x * $y;
$equal = ($a === $b);
$and = ($valid && $active);

// Assignment: space on both sides
$name = 'John';
$count = 5;

// Ternary: spaces around ?:
$status = $isActive ? 'active' : 'inactive';

// Null coalescing: no spaces
$name = $user->name ?? 'Unknown';

// String concatenation: space around .
$message = 'Hello ' . $name;

// Comparison
$isEqual = ($a == $b);
$isIdentical = ($a === $b);
$isGreater = ($a > $b);
$isLessOrEqual = ($a <= $b);
```

### Control Structures

```php
<?php
// if/elseif/else: opening brace on same line
if ($condition) {
    // Code
} elseif ($otherCondition) {
    // Code
} else {
    // Code
}

// switch: colon and break on new lines
switch ($status) {
    case 'active':
        $message = 'Active';
        break;
    case 'inactive':
        $message = 'Inactive';
        break;
    default:
        $message = 'Unknown';
}

// for loop
for ($i = 0; $i < 10; $i++) {
    echo $i;
}

// foreach loop
foreach ($items as $key => $value) {
    echo "$key: $value";
}

// while loop
while ($isRunning) {
    process();
}

// try/catch
try {
    riskyOperation();
} catch (Exception $e) {
    handleError($e);
} finally {
    cleanup();
}
```

---

## Closures and Callables

### Closure Declaration

```php
<?php
// Closure: opening brace on same line
$greeter = function (string $name): string {
    return "Hello $name";
};

// With use clause: formatted properly
$multiplier = 2;
$double = function (int $number) use ($multiplier): int {
    return $number * $multiplier;
};

// Arrow function (PHP 7.4+)
$triple = fn(int $number) => $number * 3;

// Multiple parameters
$add = function (int $a, int $b): int {
    return $a + $b;
};

// With type declarations
$process = function (User $user, string $action): bool {
    return $user->perform($action);
};
```

### Callable Type Hints

```php
<?php
// Callable parameter
function executeCallback(callable $callback, array $data): mixed
{
    return $callback($data);
}

// Closure injection
function applyFormatter(string $text, callable $formatter): string
{
    return $formatter($text);
}

// Usage
$result = applyFormatter('hello', function (string $text): string {
    return strtoupper($text);
});
```

---

## Methods and Functions

### Method Declaration

```php
<?php
class UserService
{
    // Opening brace on same line
    // One space after visibility keyword
    public function getUser(int $id): ?User
    {
        // Code
    }

    // Abstract method: no body
    abstract public function delete(int $id): bool;

    // Static method
    public static function create(string $name): static
    {
        return new static($name);
    }

    // Protected method
    protected function validate(): bool
    {
        // Code
    }

    // Private method
    private function buildQuery(): string
    {
        // Code
    }
}
```

### Method Parameters

```php
<?php
class Repository
{
    // Single line parameters
    public function find(int $id): ?Model
    {
        // Code
    }

    // Multi-line parameters: one per line
    public function create(
        string $name,
        string $email,
        string $password,
        array $roles = []
    ): Model {
        // Code
    }

    // With type declarations
    public function update(
        int $id,
        string $name,
        ?string $email = null,
        array $options = []
    ): bool {
        // Code
    }

    // Constructor property promotion (PHP 8+)
    public function __construct(
        private readonly PDO $pdo,
        private readonly LoggerInterface $logger,
    ) {}
}
```

### Return Types

```php
<?php
class DataService
{
    // Scalar return type
    public function count(): int
    {
        return 42;
    }

    // Nullable return type
    public function findById(int $id): ?User
    {
        return null;
    }

    // Array return type
    public function getAll(): array
    {
        return [];
    }

    // Union type (PHP 8+)
    public function getValue(): string|int|null
    {
        return null;
    }

    // Void return
    public function process(): void
    {
        // No return value
    }

    // Never return (PHP 8.1+)
    public function throwException(): never
    {
        throw new Exception('Error');
    }

    // Self return type
    public function clone(): static
    {
        return clone $this;
    }
}
```

---

## Type Declarations

### Scalar Types

```php
<?php
class User
{
    // String type
    public string $name;

    // Integer type
    public int $age;

    // Float type
    public float $salary;

    // Boolean type
    public bool $isActive;

    // Array type
    public array $tags;

    // Nullable types
    public ?string $nickname = null;
    public ?int $manager = null;
}

// Function parameters and return types
function process(string $input, int $count, float $factor): string
{
    return substr($input, 0, $count);
}
```

### Complex Types

```php
<?php
use stdClass;

class Repository
{
    // Class type
    public function find(int $id): ?User
    {
        return null;
    }

    // Interface type
    public function process(LoggerInterface $logger): void
    {
        $logger->info('Processing');
    }

    // Union types (PHP 8.0+)
    public function getValue(): string|int|null
    {
        return null;
    }

    // Intersection types (PHP 8.1+)
    public function handle(
        Countable&ArrayAccess $collection
    ): void {
        // Code
    }

    // Mixed type (PHP 8.0+)
    public function doSomething(mixed $data): mixed
    {
        return $data;
    }

    // Iterator/Iterable
    public function process(iterable $data): void
    {
        foreach ($data as $item) {
            // Code
        }
    }
}
```

---

## Namespace and Use Statements

### Namespace Declaration

```php
<?php
// Single namespace per file
namespace App\Services;

// Use statements before class
use App\Models\User;
use App\Repositories\UserRepository;
use Psr\Log\LoggerInterface;

// Grouped and sorted alphabetically by vendor
use function App\Helpers\format;
use const App\Config\DEFAULT_TIMEOUT;

class UserService
{
    // Class content
}
```

### Import Statements

```php
<?php
namespace App\Services;

// Classes sorted alphabetically
use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Repositories\UserRepository;

// Interfaces/Traits before other classes
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

// Functions with use function
use function array_map;
use function str_replace;

// Constants with use const
use const PHP_VERSION;
use const PHP_VERSION_ID;
```

---

## Real-world Examples

### Complete Class Example

```php
<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Psr\Log\LoggerInterface;

final class UserService
{
    private const MAX_ATTEMPTS = 3;

    public function __construct(
        private readonly UserRepository $repository,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * Get user by ID
     */
    public function getUser(int $id): ?User
    {
        $user = $this->repository->findById($id);

        if ($user === null) {
            $this->logger->warning("User not found: {$id}");
        }

        return $user;
    }

    /**
     * Create new user
     */
    public function createUser(
        string $name,
        string $email,
        string $password
    ): User {
        if (!$this->isValidEmail($email)) {
            throw new InvalidArgumentException("Invalid email: {$email}");
        }

        $user = new User(
            name: $name,
            email: $email,
            password: password_hash($password, PASSWORD_DEFAULT),
        );

        return $this->repository->save($user);
    }

    /**
     * Update user
     */
    public function updateUser(
        int $id,
        string $name,
        ?string $email = null
    ): bool {
        $user = $this->getUser($id);

        if ($user === null) {
            return false;
        }

        $user->setName($name);

        if ($email !== null) {
            $user->setEmail($email);
        }

        return $this->repository->save($user);
    }

    /**
     * Validate email format
     */
    private function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
```

---

## Complete Examples

### PSR-12 Compliant Project

```php
<?php
// src/Models/User.php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

final class User
{
    private int $id;
    private string $name;
    private string $email;
    private string $password;
    private bool $isActive;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $lastLoginAt;

    public function __construct(
        string $name,
        string $email,
        string $password,
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->isActive = true;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updateLastLogin(): void
    {
        $this->lastLoginAt = new DateTimeImmutable();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }
}

// src/Repositories/UserRepository.php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use PDO;

final class UserRepository
{
    public function __construct(private readonly PDO $pdo) {}

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->hydrate($row);
        }

        return null;
    }

    public function save(User $user): User
    {
        // Save to database
        return $user;
    }

    private function hydrate(array $data): User
    {
        $user = new User(
            $data['name'],
            $data['email'],
            $data['password'],
        );

        return $user;
    }
}
```

---

## Key Takeaways

**PSR-12 Style Checklist:**

1. ✅ Use 4 spaces for indentation
2. ✅ Opening braces on same line
3. ✅ Type declare at file start
4. ✅ Include type hints everywhere
5. ✅ One blank line between sections
6. ✅ Keep lines under 120 characters
7. ✅ Sort and group use statements
8. ✅ Document methods with docblocks
9. ✅ Use modern PHP features (8.0+)
10. ✅ Enforce with PHP-CS-Fixer

---

## See Also

- [PSR Overview](0-psr-overview.md)
- [Basic Coding Standard (PSR-1)](2-basic-coding-standard.md)
- [Logger Interface (PSR-3)](3-logger-interface.md)
