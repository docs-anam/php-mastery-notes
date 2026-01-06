# Type System Improvements

## Overview

PHP 8.4 continues improving the type system with better union/intersection types support, improved type narrowing, and enhanced disjunctive normal form (DNF) types for more expressive type declarations.

---

## Table of Contents

1. Type System Evolution
2. Union Types
3. Intersection Types
4. Disjunctive Normal Form (DNF)
5. Type Narrowing
6. Readonly Properties and Enums
7. Mixed Type
8. Practical Examples
9. Best Practices
10. Complete Examples

---

## Type System Evolution

### PHP Type System Progress

```php
<?php
// PHP 8.0: Named arguments, union types, match
// PHP 8.1: Enums, readonly, never type
// PHP 8.2: Disjunctive Normal Form
// PHP 8.3: Typed constants
// PHP 8.4: Property hooks, asymmetric visibility, enhanced types

// Modern PHP is strongly typed
declare(strict_types=1);

class TypeSystemExample
{
    // Typed property
    private int $id;

    // Typed parameter and return
    public function processData(array|string $input): int|string
    {
        return match(true) {
            is_array($input) => count($input),
            default => strlen($input),
        };
    }

    // Union type with null
    public function getValue(): string|int|null
    {
        return null;
    }
}
```

---

## Union Types

### Basic Union Types

```php
<?php
// Multiple types accepted

class UnionTypeExample
{
    // Parameter can be string or int
    public function process(string|int $value): void
    {
        if (is_string($value)) {
            echo "String: " . strlen($value);
        } else {
            echo "Int: " . $value * 2;
        }
    }

    // Return can be multiple types
    public function getValue(bool $asString): string|int
    {
        return $asString ? "text" : 123;
    }

    // Union with null
    public function findUser(int $id): array|null
    {
        if ($id < 1) {
            return null;
        }
        return ['id' => $id, 'name' => 'User'];
    }

    // Shorthand for union with null - nullable
    public function findUserShort(int $id): ?array
    {
        return $id < 1 ? null : ['id' => $id];
    }
}

// Usage
$example = new UnionTypeExample();
$example->process("hello");     // Works
$example->process(42);          // Works
// $example->process([]);        // TypeError

$result = $example->getValue(true);      // string
$result = $example->getValue(false);     // int
```

### Union with Duplicate Types

```php
<?php
// Union types eliminate duplicates

class DuplicateHandling
{
    // Automatically combines duplicates
    public function getValue(): string|string|int
    {
        // Treated as: string|int
        return "text";
    }

    // Null is special
    public function nullable(): string|int|null
    {
        // Not the same as ?string|int (which means (string|int)|null)
        // This specifically accepts null
        return null;
    }
}

// Type resolution:
// string|int|string  => string|int
// string|?int        => string|int|null
// ?(string|int)      => (string|int)|null
```

---

## Intersection Types

### Multiple Type Requirements

```php
<?php
// Object must implement multiple interfaces/classes

interface Logger
{
    public function log(string $message): void;
}

interface Serializable
{
    public function serialize(): string;
}

class Service
{
    // Parameter must be BOTH Logger and Serializable
    public function process(Logger&Serializable $handler): void
    {
        $handler->log('processing');
        echo $handler->serialize();
    }

    // Multiple intersections with union
    public function handle((Logger&Serializable)|(int&float) $value): void
    {
        if (is_int($value) && is_float($value)) {
            // This branch won't happen (contradiction)
        }
    }
}

// Implementation
class FileLogger implements Logger, Serializable
{
    public function log(string $message): void
    {
        // Log message
    }

    public function serialize(): string
    {
        return json_encode([]);
    }
}

// Usage
$service = new Service();
$logger = new FileLogger();
$service->process($logger);  // ✓ Implements both interfaces
```

### Practical Intersection Patterns

```php
<?php
// Require object to be both Countable and Iterable

interface Queryable
{
    public function query(): array;
}

interface Cacheable
{
    public function cache(): void;
}

class Repository
{
    // Requires both query and cache capabilities
    public function process(Queryable&Cacheable $source): void
    {
        $data = $source->query();
        $source->cache();
    }
}

class CachedRepository implements Queryable, Cacheable
{
    public function query(): array
    {
        return [];
    }

    public function cache(): void
    {
        // Cache implementation
    }
}

// Enforce multiple capabilities
$repo = new CachedRepository();
$repositoryService = new Repository();
$repositoryService->process($repo);  // ✓ Both interfaces implemented
```

---

## Disjunctive Normal Form (DNF)

### Complex Type Combinations

```php
<?php
// Combine unions and intersections for precise types
// Format: (A&B)|(C&D)|E

interface Readable
{
    public function read(): string;
}

interface Writable
{
    public function write(string $data): void;
}

interface Closeable
{
    public function close(): void;
}

class FileHandler
{
    // Accepts either:
    // 1. Object that is both Readable and Writable, OR
    // 2. Object that is Closeable, OR
    // 3. A file resource
    public function process((Readable&Writable)|Closeable|resource $stream): void
    {
        if ($stream instanceof Readable && $stream instanceof Writable) {
            $data = $stream->read();
            $stream->write($data);
        } elseif ($stream instanceof Closeable) {
            $stream->close();
        } else {
            // It's a resource
            fclose($stream);
        }
    }
}

// Complex type example
class DataProcessor
{
    // Accepts: (Logger&Serializable) OR (Countable&Iterator) OR string
    public function handle(
        (Logger&Serializable)|(Countable&Iterator)|string $data
    ): void {
        // Handle complex type
    }
}
```

### DNF Type Validation

```php
<?php
// Validate objects match DNF type requirements

function validateType(mixed $value): bool
{
    // Check if matches: (Logger&Serializable)|Countable|string
    
    if (is_string($value)) {
        return true;
    }

    if ($value instanceof Countable) {
        return true;
    }

    if ($value instanceof Logger && $value instanceof Serializable) {
        return true;
    }

    return false;
}

// Type narrowing with DNF
function processData((stdClass&ArrayAccess)|array|string $data): string
{
    if (is_string($data)) {
        return strtoupper($data);
    }

    if (is_array($data)) {
        return json_encode($data);
    }

    // Must be stdClass&ArrayAccess
    return $data['key'] ?? '';
}
```

---

## Type Narrowing

### Type Narrowing with Conditions

```php
<?php
// Type system understands type narrowing

function processValue(string|int|array $value): string
{
    if (is_string($value)) {
        // In this block, $value is definitely string
        return strtoupper($value);
    }

    if (is_int($value)) {
        // In this block, $value is definitely int
        return (string)($value * 2);
    }

    // Here, $value must be array
    return count($value);
}

// Narrowing with instanceof
class Service
{
    public function handle(Service|Database|string $input): void
    {
        if ($input instanceof Service) {
            // $input is Service
            $input->process();
        } elseif ($input instanceof Database) {
            // $input is Database
            $input->query();
        } else {
            // $input is string
            echo strlen($input);
        }
    }
}

// Narrowing with array_is_list
function sortArray(array $items): void
{
    if (array_is_list($items)) {
        // $items is definitely a list
        foreach ($items as $index => $item) {
            // $index is int
        }
    } else {
        // $items is definitely associative
        foreach ($items as $key => $value) {
            // $key is string
        }
    }
}
```

### Type Guards

```php
<?php
// Helper functions for type checking

class TypeGuards
{
    public static function isPositiveInt(mixed $value): value is int
    {
        return is_int($value) && $value > 0;
    }

    public static function isNonEmptyString(mixed $value): value is string
    {
        return is_string($value) && strlen($value) > 0;
    }

    public static function isValidEmail(mixed $value): value is string
    {
        return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}

// Usage with type guards
$items = [1, -5, 'text', 0, 10];

foreach ($items as $item) {
    if (TypeGuards::isPositiveInt($item)) {
        // $item is guaranteed to be positive int
        echo $item + 5;
    }
}
```

---

## Readonly Properties and Enums

### Readonly Type Safety

```php
<?php
// Readonly prevents mutation after initialization

class User
{
    public readonly int $id;
    public readonly string $email;
    public readonly DateTimeImmutable $createdAt;

    public function __construct(int $id, string $email)
    {
        $this->id = $id;
        $this->email = $email;
        $this->createdAt = new DateTimeImmutable();
    }

    // Cannot modify readonly properties
    public function changeEmail(string $newEmail): User
    {
        // Create new instance instead (copy constructor)
        return new User($this->id, $newEmail);
    }
}

// Usage
$user = new User(1, 'john@example.com');
echo $user->id;         // ✓ Can read
// $user->email = '';   // ✗ Error: readonly

// Create modified copy
$newUser = $user->changeEmail('newemail@example.com');
```

### Readonly Classes

```php
<?php
// All properties are readonly by default

readonly class ImmutableData
{
    public function __construct(
        public int $id,
        public string $name,
        public float $value,
    ) {}

    // Can have methods
    public function display(): string
    {
        return "$this->name: $this->value";
    }

    // But all properties are immutable
}

// Usage
$data = new ImmutableData(1, 'widget', 19.99);
echo $data->display();      // ✓
// $data->value = 29.99;    // ✗ Error
```

---

## Mixed Type

### When to Use Mixed

```php
<?php
// Use mixed when type truly varies

interface DataProcessor
{
    // OK: Return type is genuinely unknown
    public function process(array $data): mixed;

    // Better: Be specific if possible
    public function getData(): array|null;
}

class Processor implements DataProcessor
{
    public function process(array $data): mixed
    {
        // Could return various types depending on data
        return match($data['type'] ?? null) {
            'number' => (int)$data['value'],
            'text' => (string)$data['value'],
            default => null,
        };
    }

    public function getData(): array|null
    {
        return null;
    }
}

// Better than mixed when possible
function getValue(string $key): int|string|bool|null
{
    // More specific than mixed
    return null;
}
```

---

## Practical Examples

### Type-Safe HTTP Request Handler

```php
<?php
declare(strict_types=1);

namespace App\Http;

interface Response
{
    public function send(): void;
}

interface Serializable
{
    public function serialize(): string;
}

class RequestHandler
{
    public function handle(
        array $params,
        (Response&Serializable)|string $handler
    ): void {
        if (is_string($handler)) {
            // String is a class name
            $handler = new $handler();
        }

        // Now handler is Response&Serializable
        $response = $handler;
        $response->send();
    }

    public function process(
        int|string $identifier,
        array|object $data
    ): array|null {
        if (is_string($identifier)) {
            // Lookup by name
        } else {
            // Lookup by ID
        }

        if (is_object($data)) {
            // Convert to array
            $data = (array)$data;
        }

        return $data;
    }
}
```

### Generic Collection Class

```php
<?php
declare(strict_types=1);

class Collection
{
    private array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function map(callable $callback): self
    {
        return new self(array_map($callback, $this->items));
    }

    public function filter(callable $predicate): self
    {
        return new self(array_filter($this->items, $predicate));
    }

    public function reduce(
        callable $reducer,
        string|int|float|array|null $initial = null
    ): string|int|float|array|null {
        return array_reduce($this->items, $reducer, $initial);
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }
}

// Usage
$numbers = new Collection([1, 2, 3, 4, 5]);
$doubled = $numbers
    ->filter(fn($n) => $n > 2)
    ->map(fn($n) => $n * 2);

print_r($doubled->getItems());  // [6, 8, 10]
```

---

## Best Practices

### Type Safety Guidelines

```php
<?php
// ✓ DO: Be as specific as possible
class GoodExample
{
    public function find(int $id): User|null
    {
        // Specific return type
        return null;
    }

    public function process(string|int $identifier): void
    {
        // Only accepts string or int
    }
}

// ✗ DON'T: Use mixed unnecessarily
class BadExample
{
    public function find(mixed $id): mixed
    {
        // Too vague
        return null;
    }
}

// ✓ DO: Use union types for alternatives
class AlternativeHandler
{
    public function handle(stdClass|array $data): string|int
    {
        return match(true) {
            is_object($data) => count((array)$data),
            default => count($data),
        };
    }
}

// ✓ DO: Use intersection for requirements
interface Processor
{
    public function process(Logger&Serializable $handler): void;
}

// ✗ DON'T: Use overly complex DNF without reason
class OverlyComplex
{
    // Too complex if not needed
    public function handle(
        (A&B&C)|(D&E&F)|(G&H)|I|string $value
    ): void {
    }
}
```

---

## Complete Examples

### Full Data Service with Advanced Types

```php
<?php
declare(strict_types=1);

namespace App\Services;

interface Cache
{
    public function get(string $key): mixed;
    public function set(string $key, mixed $value, int $ttl): void;
}

interface Logger
{
    public function info(string $message): void;
    public function error(string $message): void;
}

class DataService
{
    public function __construct(
        private Cache&Logger $handler  // Requires both
    ) {}

    public function fetch(int|string $identifier): array|null
    {
        $cacheKey = "data:$identifier";

        // Check cache
        $cached = $this->handler->get($cacheKey);
        if ($cached !== null) {
            $this->handler->info("Cache hit: $cacheKey");
            return $cached;
        }

        // Fetch data
        $data = $this->fetchFromSource($identifier);

        if ($data !== null) {
            $this->handler->set($cacheKey, $data, 3600);
        } else {
            $this->handler->error("Failed to fetch: $identifier");
        }

        return $data;
    }

    public function process(
        array|object $input
    ): array|object {
        if (is_object($input)) {
            return $this->processObject($input);
        }

        return $this->processArray($input);
    }

    private function processArray(array $data): array
    {
        return array_map(
            fn($item) => is_scalar($item) ? $item : json_encode($item),
            $data
        );
    }

    private function processObject(object $data): object
    {
        return $data;
    }

    private function fetchFromSource(
        int|string $identifier
    ): array|null {
        // Fetch from API or database
        return null;
    }
}

// Usage
class CombinedHandler implements Cache, Logger
{
    public function get(string $key): mixed
    {
        return null;
    }

    public function set(string $key, mixed $value, int $ttl): void
    {
    }

    public function info(string $message): void
    {
        echo "INFO: $message\n";
    }

    public function error(string $message): void
    {
        echo "ERROR: $message\n";
    }
}

$handler = new CombinedHandler();
$service = new DataService($handler);
$data = $service->fetch(1);
print_r($data);
```

---

## See Also

- [PHP 8.4 Overview](0-php8.4-overview.md)
- [Property Hooks](2-property-hooks.md)
- [Asymmetric Visibility](3-asymmetric-visibility.md)
- [Class Constant Visibility](4-class-constant-visibility.md)
- [Performance Improvements](8-performance-improvements.md)
