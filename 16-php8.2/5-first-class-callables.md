# First-Class Callables

## Overview

Learn about first-class callables in PHP 8.2, an enhanced syntax for creating callable references from functions, methods, and closures with improved type safety and clarity.

---

## Table of Contents

1. What are First-Class Callables
2. Basic Syntax
3. Function References
4. Method References
5. Static Method References
6. Callable Type Hints
7. Practical Applications
8. Complete Examples

---

## What are First-Class Callables

### Purpose

```php
<?php
// Before PHP 8.1: Using string references
class UserRepository
{
    public function findById(int $id): ?User
    {
        // Find user logic
    }
}

$repo = new UserRepository();

// Old way: Pass method as string
$callable = [$repo, 'findById'];  // Array format
call_user_func($callable, 1);

// Problems:
// ❌ String references not type-safe
// ❌ Refactoring breaks without IDE support
// ❌ No clear callable intent
// ❌ Runtime errors possible

// PHP 8.1 Solution: First-Class Callables
$callable = $repo->findById(...);  // Clear syntax with ...
$callable(1);

// PHP 8.2 Enhancement: Better type checking and patterns
```

### Key Features

```php
<?php
// PHP 8.2 first-class callable advantages:
// ✓ Type-safe references
// ✓ IDE auto-completion
// ✓ Refactoring friendly
// ✓ Clear callable intent
// ✓ Works with Closure type hints
// ✓ Better static analysis

function processUser(int $id): User {}

// Create callable reference
$processor = processUser(...);

// Use anywhere
$users = array_map($processor, [1, 2, 3]);
```

---

## Basic Syntax

### Function References

```php
<?php
// Simple function
function greet(string $name): string
{
    return "Hello, $name";
}

// Reference using ...
$greeter = greet(...);

// Call the callable
echo $greeter("John");  // Hello, John

// Use in array functions
$names = ["Alice", "Bob", "Carol"];
$greetings = array_map(greet(...), $names);
// Result: ["Hello, Alice", "Hello, Bob", "Hello, Carol"]
```

### Method References

```php
<?php
class Logger
{
    public function log(string $message): void
    {
        echo "[LOG] $message\n";
    }
}

$logger = new Logger();

// Create callable from method
$logFunction = $logger->log(...);

// Use it
$logFunction("Starting process");  // [LOG] Starting process

// Pass to function expecting callable
function executeWithLogging(callable $logger, string $task): void
{
    $logger("Executing: $task");
}

executeWithLogging($logFunction, "Database Migration");
```

### Static Method References

```php
<?php
class Math
{
    public static function add(int $a, int $b): int
    {
        return $a + $b;
    }

    public static function multiply(int $a, int $b): int
    {
        return $a * $b;
    }
}

// Reference static methods
$adder = Math::add(...);
$multiplier = Math::multiply(...);

// Use them
echo $adder(5, 3);           // 8
echo $multiplier(5, 3);      // 15

// Use in array operations
$numbers = [1, 2, 3, 4, 5];
$results = array_map(Math::multiply(...), $numbers);
// Each number multiplied by itself? No - needs partial application
```

---

## Advanced Patterns

### Callable Type Hints

```php
<?php
// Type hint for callables
function executeCallback(callable $callback, int $value): int
{
    return $callback($value);
}

function double(int $n): int
{
    return $n * 2;
}

// Pass first-class callable
$result = executeCallback(double(...), 5);
echo $result;  // 10

// Works with Closure type hints too
function applyOperation(Closure $operation, int $x, int $y): int
{
    return $operation($x, $y);
}

$add = fn($a, $b) => $a + $b;
$result = applyOperation($add, 5, 3);  // 8
```

### Array Processing

```php
<?php
class UserFilter
{
    public function isActive(User $user): bool
    {
        return $user->isActive;
    }

    public function isAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }

    public static function isBanned(User $user): bool
    {
        return $user->banned;
    }
}

$filter = new UserFilter();
$users = [...];  // Array of User objects

// Filter using first-class callables
$activeUsers = array_filter($users, $filter->isActive(...));
$admins = array_filter($users, $filter->isAdmin(...));
$unbanned = array_filter($users, fn($u) => !UserFilter::isBanned($u));
```

---

## Practical Applications

### Callback Systems

```php
<?php
class EventDispatcher
{
    private array $listeners = [];

    public function on(string $event, callable $callback): void
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $callback;
    }

    public function dispatch(string $event, ...$args): void
    {
        if (!isset($this->listeners[$event])) {
            return;
        }

        foreach ($this->listeners[$event] as $callback) {
            $callback(...$args);
        }
    }
}

class UserService
{
    private EventDispatcher $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function createUser(string $email): User
    {
        $user = new User($email);

        // Dispatch with first-class callable
        $this->dispatcher->dispatch('user.created', $user);

        return $user;
    }

    public function onUserCreated(User $user): void
    {
        // Send email
        echo "Email sent to: {$user->email}\n";
    }
}

// Usage
$dispatcher = new EventDispatcher();
$service = new UserService($dispatcher);

// Register callback using first-class callable
$dispatcher->on('user.created', $service->onUserCreated(...));

$user = $service->createUser('john@example.com');
```

### Middleware Pipeline

```php
<?php
class Pipeline
{
    private array $pipes = [];

    public function pipe(callable $middleware): self
    {
        $this->pipes[] = $middleware;
        return $this;
    }

    public function execute(mixed $payload): mixed
    {
        $result = $payload;

        foreach ($this->pipes as $pipe) {
            $result = $pipe($result);
        }

        return $result;
    }
}

class RequestProcessor
{
    public function authenticate(string $request): string
    {
        return "[AUTH] $request";
    }

    public function validate(string $request): string
    {
        return "[VALID] $request";
    }

    public function process(string $request): string
    {
        return "[PROCESS] $request";
    }
}

// Build pipeline with first-class callables
$processor = new RequestProcessor();
$pipeline = new Pipeline();

$pipeline
    ->pipe($processor->authenticate(...))
    ->pipe($processor->validate(...))
    ->pipe($processor->process(...));

$result = $pipeline->execute("user request");
// [PROCESS] [VALID] [AUTH] user request
```

### Sorting with Callables

```php
<?php
class SortHelper
{
    public function sortByAge(User $a, User $b): int
    {
        return $a->age <=> $b->age;
    }

    public function sortByName(User $a, User $b): int
    {
        return strcmp($a->name, $b->name);
    }

    public static function sortByEmail(User $a, User $b): int
    {
        return strcmp($a->email, $b->email);
    }
}

$helper = new SortHelper();
$users = [...];  // Array of users

// Sort using first-class callables
usort($users, $helper->sortByAge(...));
usort($users, $helper->sortByName(...));
usort($users, SortHelper::sortByEmail(...));
```

---

## Type Safety

### Callable Type Declarations

```php
<?php
// Using callable as parameter type
function mapWithCallback(array $items, callable $mapper): array
{
    return array_map($mapper, $items);
}

// Using Closure for more specific type hints
function reduceWithCallback(array $items, Closure $reducer): mixed
{
    return array_reduce($items, $reducer, 0);
}

// Define callable that takes specific parameters
class Service
{
    /**
     * @param callable(int): string $formatter
     */
    public function format(int $value, callable $formatter): string
    {
        return $formatter($value);
    }
}

// Usage with type safety
$numbers = [1, 2, 3, 4, 5];

$doubled = mapWithCallback($numbers, fn($x) => $x * 2);
$sum = reduceWithCallback($numbers, fn($carry, $item) => $carry + $item);

$service = new Service();
$result = $service->format(42, strval(...));  // Safe: converts to string
```

---

## Complete Examples

### Full Application Pattern

```php
<?php
declare(strict_types=1);

namespace App\Processing;

use Closure;

class DataPipeline
{
    private array $transformers = [];
    private array $validators = [];

    public function addTransformer(callable $transformer): self
    {
        $this->transformers[] = $transformer;
        return $this;
    }

    public function addValidator(callable $validator): self
    {
        $this->validators[] = $validator;
        return $this;
    }

    public function process(array $data): array
    {
        // Validate
        foreach ($this->validators as $validator) {
            if (!$validator($data)) {
                throw new InvalidArgumentException("Validation failed");
            }
        }

        // Transform
        $result = $data;
        foreach ($this->transformers as $transformer) {
            $result = $transformer($result);
        }

        return $result;
    }
}

class DataTransformer
{
    public function normalize(array $data): array
    {
        return array_map(trim(...), $data);
    }

    public function lowercase(array $data): array
    {
        return array_map(strtolower(...), $data);
    }

    public function removeEmpty(array $data): array
    {
        return array_filter($data, fn($v) => !empty($v));
    }

    public static function toUppercase(array $data): array
    {
        return array_map(strtoupper(...), $data);
    }
}

class DataValidator
{
    public function hasRequiredKeys(array $required): Closure
    {
        return fn($data) => count(array_intersect_key(
            array_flip($required),
            $data
        )) === count($required);
    }

    public function noEmptyValues(array $data): bool
    {
        return count(array_filter($data, fn($v) => empty($v))) === 0;
    }

    public static function isArray($data): bool
    {
        return is_array($data);
    }
}

// Usage
$transformer = new DataTransformer();
$validator = new DataValidator();

$pipeline = new DataPipeline();

// Add validators using first-class callables
$pipeline->addValidator(DataValidator::isArray(...));
$pipeline->addValidator($validator->hasRequiredKeys(['name', 'email']));
$pipeline->addValidator($validator->noEmptyValues(...));

// Add transformers
$pipeline->addTransformer($transformer->normalize(...));
$pipeline->addTransformer($transformer->lowercase(...));
$pipeline->addTransformer($transformer->removeEmpty(...));

// Process data
$input = [
    'name' => '  John Doe  ',
    'email' => '  JOHN@EXAMPLE.COM  ',
    'phone' => '',
];

try {
    $output = $pipeline->process($input);
    print_r($output);
    // Array
    // (
    //     [name] => john doe
    //     [email] => john@example.com
    // )
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
}
```

---

## See Also

- [PHP 8.2 Overview](0-php8.2-overview.md)
- [String Functions](4-string-functions.md)
- [Null-Safe Arrays](6-null-safe-arrays.md)
