# #[Override] Attribute

## Overview

Learn about the #[Override] attribute in PHP 8.3, which helps prevent accidental mistakes when overriding parent methods by verifying the override relationship.

---

## Table of Contents

1. What is #[Override]
2. Basic Syntax
3. Use Cases
4. Error Detection
5. Best Practices
6. Practical Examples
7. Integration Patterns
8. Complete Examples

---

## What is #[Override]

### Purpose

```php
<?php
// Before PHP 8.3: Easy to make mistakes when overriding
class Parent
{
    public function process(string $data): void
    {
        echo "Processing: $data";
    }

    public function validate(string $input): bool
    {
        return !empty($input);
    }
}

class Child extends Parent
{
    // Method signature slightly different - parent method NOT overridden!
    public function process(string $data, int $flags = 0): void  // Different signature
    {
        parent::process($data);
    }

    // Typo in method name - creates new method instead of overriding
    public function validat(string $input): bool  // Missing 'e'
    {
        return parent::validate($input);
    }

    // Problems:
    // ❌ Hard to spot mistakes
    // ❌ Silent bugs in production
    // ❌ IDE may not catch signature mismatches
    // ❌ Refactoring parent breaks silently
}

// PHP 8.3 Solution: #[Override] attribute
class BetterChild extends Parent
{
    #[Override]  // Ensures this actually overrides parent
    public function process(string $data): void
    {
        parent::process($data);
        // Custom logic
    }

    // #[Override]
    // public function validat(string $input): bool {}  // Error! Parent has no "validat"
}
```

### Key Features

```php
<?php
// #[Override] guarantees:
// ✓ Method exists in parent
// ✓ Method signature is compatible
// ✓ Refactoring detected early
// ✓ Clear override intent
// ✓ IDE support and validation

// Syntax
#[Override]
public function methodName(): void {}

// Must be used before method definition
// Can be combined with other attributes
```

---

## Basic Syntax

### Simple Override

```php
<?php
class BaseClass
{
    public function getData(): array
    {
        return [];
    }

    protected function processData(array $data): void
    {
        // Process data
    }
}

class DerivedClass extends BaseClass
{
    // Mark explicit override
    #[Override]
    public function getData(): array
    {
        return ['id' => 1, 'name' => 'Item'];
    }

    // Can override protected methods too
    #[Override]
    protected function processData(array $data): void
    {
        parent::processData($data);
        echo "Processing " . count($data) . " items";
    }
}

// Usage
$derived = new DerivedClass();
$data = $derived->getData();  // Uses overridden method
```

### Error Detection

```php
<?php
class Parent
{
    public function save(): void {}
}

class Child extends Parent
{
    // ❌ WRONG: No parent method to override
    #[Override]
    public function saved(): void {}  // Error! Parent has no "saved"

    // ❌ WRONG: Signature mismatch
    #[Override]
    public function save(int $id): void {}  // Error! Signature differs

    // ✓ CORRECT: Exact override
    #[Override]
    public function save(): void {}  // OK
}
```

---

## Preventing Common Mistakes

### Refactoring Safety

```php
<?php
// Before refactoring
class User
{
    public function getName(): string
    {
        return $this->name;
    }
}

class AdminUser extends User
{
    #[Override]
    public function getName(): string
    {
        return "ADMIN: " . parent::getName();
    }
}

// After refactoring: Parent method renamed
class User
{
    public function getFullName(): string  // Renamed from getName
    {
        return $this->name;
    }
}

// ERROR: #[Override] catches the problem!
// Can't use #[Override] on getName anymore - parent doesn't have it
class AdminUser extends User
{
    // #[Override]  // Can't be here - method doesn't exist
    public function getName(): string  // Now an extra method, not override
    {
        return "ADMIN: " . parent::getFullName();  // Must update this too
    }
}
```

### Typo Protection

```php
<?php
class Logger
{
    public function log(string $message): void
    {
        echo "[LOG] $message";
    }
}

class CustomLogger extends Logger
{
    // ❌ Typo: "logg" instead of "log"
    #[Override]
    public function logg(string $message): void  // Error! No parent "logg" method
    {
        parent::log($message . " (custom)");
    }

    // ✓ Correct spelling
    #[Override]
    public function log(string $message): void
    {
        parent::log($message);
        echo " - Custom logging";
    }
}
```

---

## Practical Patterns

### With Inheritance Chains

```php
<?php
// Multi-level inheritance
class Base
{
    public function handle(): void {}
}

class Middle extends Base
{
    #[Override]
    public function handle(): void
    {
        parent::handle();
        echo "Middle handling";
    }
}

class Final extends Middle
{
    #[Override]
    public function handle(): void
    {
        parent::handle();
        echo "Final handling";
    }
}

// All overrides verified
$final = new Final();
$final->handle();
// Outputs: Middle handling Final handling
```

### With Interfaces

```php
<?php
interface Processor
{
    public function process(array $data): array;
}

class BaseProcessor implements Processor
{
    #[Override]  // Implements interface method
    public function process(array $data): array
    {
        return array_map(fn($x) => $x * 2, $data);
    }
}

class CachedProcessor extends BaseProcessor
{
    private array $cache = [];

    #[Override]
    public function process(array $data): array
    {
        $key = md5(json_encode($data));

        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $result = parent::process($data);
        $this->cache[$key] = $result;

        return $result;
    }
}
```

### Combined with Other Attributes

```php
<?php
use Doctrine\ORM\Mapping as ORM;
use App\Attributes\Log;
use App\Attributes\Authenticate;

class Base
{
    public function save(): void {}
}

class Entity extends Base
{
    #[Override]
    #[ORM\Column(type: 'string')]
    #[Log(level: 'info')]
    #[Authenticate('admin')]
    public function save(): void
    {
        parent::save();
        // Save entity
    }
}
```

---

## Advanced Patterns

### Abstract Class Pattern

```php
<?php
abstract class AbstractRepository
{
    abstract public function find(int $id): ?object;

    public function findOrFail(int $id): object
    {
        $result = $this->find($id);

        if ($result === null) {
            throw new Exception("Not found");
        }

        return $result;
    }
}

class UserRepository extends AbstractRepository
{
    #[Override]
    public function find(int $id): ?User
    {
        // Specific user finding logic
        return null;
    }
}

class ProductRepository extends AbstractRepository
{
    #[Override]
    public function find(int $id): ?Product
    {
        // Specific product finding logic
        return null;
    }
}
```

### Template Method Pattern

```php
<?php
abstract class DataProcessor
{
    final public function process(array $data): array
    {
        $data = $this->validate($data);
        $data = $this->transform($data);
        return $this->output($data);
    }

    abstract protected function validate(array $data): array;
    abstract protected function transform(array $data): array;
    abstract protected function output(array $data): array;
}

class JsonProcessor extends DataProcessor
{
    #[Override]
    protected function validate(array $data): array
    {
        // JSON validation
        return $data;
    }

    #[Override]
    protected function transform(array $data): array
    {
        // JSON transformation
        return $data;
    }

    #[Override]
    protected function output(array $data): array
    {
        return json_decode(json_encode($data), true);
    }
}
```

---

## IDE and Static Analysis

### PHPStan Configuration

```php
<?php
// phpstan.neon
/*
parameters:
    checkUnusedAttributes: true
    checkOverriddenAttributes: true
*/

class Parent
{
    public function method(): void {}
}

class Child extends Parent
{
    #[Override]  // PHPStan verifies this
    public function method(): void {}
}
```

### Psalm Integration

```php
<?php
// psalm.xml - Psalm checks Override attributes
/*
<psalm>
    <plugins>
        <pluginClass class="Psalm\Plugin\OverrideChecker"/>
    </plugins>
</psalm>
*/
```

---

## Best Practices

**#[Override] Guidelines:**

```php
<?php
// ✓ GOOD: Use for all overrides
class Child extends Parent
{
    #[Override]
    public function method(): void {}
}

// ✓ GOOD: Works with inheritance chains
class Middle extends Parent
{
    #[Override]
    public function method(): void {}
}

class Final extends Middle
{
    #[Override]  // Also override Middle's version
    public function method(): void {}
}

// ✓ GOOD: Clear intent
#[Override]
public function importantBusinessLogic(): void {}

// ⚠️ CONSIDER: Not needed for interface implementation
// (Some teams use it, some don't - choose consistently)
#[Override]
public function fromInterface(): void {}

// ❌ AVOID: Multiple declarations
class Bad
{
    #[Override]
    #[Override]  // Unnecessary duplication
    public function method(): void {}
}

// ❌ AVOID: Using on non-overriding methods
class Bad2
{
    #[Override]
    public function newMethod(): void {}  // Not an override!
}
```

---

## Complete Examples

### Full Application Example

```php
<?php
declare(strict_types=1);

namespace App\Services;

abstract class BaseService
{
    abstract public function execute(): void;

    public function validate(): bool
    {
        return true;
    }

    final public function run(): void
    {
        if (!$this->validate()) {
            throw new InvalidArgumentException("Validation failed");
        }

        $this->execute();
        $this->logExecution();
    }

    protected function logExecution(): void
    {
        echo "Executed at " . date('Y-m-d H:i:s');
    }
}

class UserService extends BaseService
{
    #[Override]
    public function execute(): void
    {
        echo "Processing users";
    }

    #[Override]
    public function validate(): bool
    {
        echo "Validating user data";
        return true;
    }

    #[Override]
    protected function logExecution(): void
    {
        parent::logExecution();
        echo " - User service";
    }
}

class ProductService extends BaseService
{
    #[Override]
    public function execute(): void
    {
        echo "Processing products";
    }

    #[Override]
    protected function logExecution(): void
    {
        parent::logExecution();
        echo " - Product service";
    }
}

// Implementation
class ServiceFactory
{
    public static function create(string $type): BaseService
    {
        return match($type) {
            'user' => new UserService(),
            'product' => new ProductService(),
            default => throw new InvalidArgumentException("Unknown type: $type"),
        };
    }
}

// Usage
$service = ServiceFactory::create('user');
$service->run();

// Output: Validating user data
//         Processing users
//         Executed at 2024-01-06 10:30:45 - User service
```

---

## See Also

- [PHP 8.3 Overview](0-php8.3-overview.md)
- [Typed Constants](2-typed-constants.md)
- [Enum Enhancements](6-enum-enhancements.md)
