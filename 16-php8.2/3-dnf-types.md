# Disjunctive Normal Form (DNF) Types

## Overview

Learn about Disjunctive Normal Form (DNF) types in PHP 8.2, which allow complex type combinations using both union (|) and intersection (&) operators.

---

## Table of Contents

1. What are DNF Types
2. Basic Syntax
3. Understanding DNF
4. Type Combinations
5. Complex Patterns
6. Implementation Examples
7. Best Practices
8. Complete Examples

---

## What are DNF Types

### Purpose

```php
<?php
// Before PHP 8.2: Limited type combinations

interface Drawable {}
interface Colorable {}
interface Serializable {}

// Could not properly express:
// "A Drawable that is also Colorable OR a Serializable object"

// Solution: DNF Types (PHP 8.2)
// (Drawable & Colorable) | Serializable

function processObject((Drawable & Colorable) | Serializable $obj): void
{
    // Object is either:
    // 1. Both Drawable AND Colorable, OR
    // 2. Serializable
}

// Benefits:
// ✓ Express complex type requirements
// ✓ More precise type hints
// ✓ Better IDE support
// ✓ Clearer code intent
```

### Key Concepts

```php
<?php
// Union Types (OR)
function process(string | int $value): void {}

// Intersection Types (AND)
function draw(Drawable & Colorable $obj): void {}

// Disjunctive Normal Form (DNF)
// Combines both: (A & B) | (C & D)
function handle((Reader & Closeable) | Iterator $obj): void {}

// Rules:
// ✓ Mix union and intersection
// ✓ Parentheses required for clarity
// ✓ Cannot nest unions (A | B | (C & D)) - use DNF form
```

---

## Basic Syntax

### Simple DNF

```php
<?php
interface Drawable {}
interface Colorable {}
interface Serializable {}

// DNF: Intersection OR Intersection
class Canvas
{
    public function render((Drawable & Colorable) | Serializable $obj): void
    {
        if ($obj instanceof Drawable && $obj instanceof Colorable) {
            echo "Drawing colored object";
        } else if ($obj instanceof Serializable) {
            echo "Serializing object";
        }
    }
}

// Syntax breakdown:
// (Drawable & Colorable) - intersection: must be BOTH
// |                      - union: OR
// Serializable           - single type
```

### Multiple Intersections

```php
<?php
interface Reader {}
interface Writer {}
interface Closeable {}
interface Flushable {}

// Complex DNF
function processStream(
    (Reader & Writer & Closeable) | (Closeable & Flushable) | string $input
): void {
    // Input is either:
    // 1. Reader, Writer, AND Closeable
    // 2. Closeable AND Flushable
    // 3. Simple string
}
```

---

## Understanding DNF

### Type Matching

```php
<?php
interface Drawable { public function draw(); }
interface Colorable { public function setColor(string $c); }
interface Serializable { public function serialize(); }

class ColoredShape implements Drawable, Colorable
{
    public function draw() {}
    public function setColor(string $color) {}
}

class Document implements Serializable
{
    public function serialize() {}
}

function process((Drawable & Colorable) | Serializable $obj): void
{
    // $obj matches if:
    // - It implements both Drawable AND Colorable, OR
    // - It implements Serializable
}

$shape = new ColoredShape();     // Matches first intersection
$doc = new Document();            // Matches second type
process($shape);
process($doc);
// process("string");            // Error: doesn't match any type
```

### Type Narrowing

```php
<?php
interface Logger {}
interface Formatter {}

function handle((Logger & Formatter) | Iterator $data): void
{
    if ($data instanceof Logger && $data instanceof Formatter) {
        // Type narrowed: Logger & Formatter
        $data->log("message");
        $data->format("data");
    } else if ($data instanceof Iterator) {
        // Type narrowed: Iterator
        foreach ($data as $item) {
            echo $item;
        }
    }
}
```

---

## Advanced Type Combinations

### Real-world DNF Patterns

```php
<?php
// HTTP-related interfaces
interface ServerRequestInterface {}
interface ResponseInterface {}
interface StreamInterface {}

// File handling interfaces
interface FileSystemInterface {}
interface ReadableInterface {}
interface WriteableInterface {}

// Complex: Request can be either HTTP response-like with streaming
// OR a file system resource that's readable and writeable
function handleResource(
    (ServerRequestInterface & StreamInterface) | (FileSystemInterface & ReadableInterface & WriteableInterface) $resource
): void {
    // Handle two different types of resources
}

// PSR interfaces combination
interface ContainerInterface {}
interface EventDispatcherInterface {}
interface LoggerInterface {}

// Service container that can also dispatch events or log
function initializeService(
    (ContainerInterface & EventDispatcherInterface) | (ContainerInterface & LoggerInterface) $container
): void {
    // Service uses container either with events or logging
}
```

### Practical Examples

```php
<?php
// Database connection abstraction
interface QueryableInterface { public function query(string $sql); }
interface TransactionalInterface { public function beginTransaction(); }
interface CacheableInterface { public function setCache(string $key, $value); }

// Accept either:
// 1. Queryable connection with transaction support
// 2. OR cacheable queryable connection
function executeQuery(
    (QueryableInterface & TransactionalInterface) | (QueryableInterface & CacheableInterface) $connection,
    string $sql
): void {
    $result = $connection->query($sql);
    // Handle based on type
}

// Validation scenario
interface ValidatorInterface { public function validate($data): bool; }
interface SanitizeableInterface { public function sanitize($data); }
interface LoggableInterface { public function log(string $message); }

// Validator that either sanitizes or logs
function validateInput(
    (ValidatorInterface & SanitizeableInterface) | (ValidatorInterface & LoggableInterface) $validator,
    $data
): bool {
    return $validator->validate($data);
}
```

---

## Implementation Patterns

### Flexible API Design

```php
<?php
interface EventHandlerInterface
{
    public function handle($event): void;
}

interface AsyncInterface
{
    public function async(): void;
}

interface QueueableInterface
{
    public function queue(): void;
}

class EventProcessor
{
    // Event handler can be:
    // - Synchronous (just EventHandler)
    // - Async (EventHandler & Async)
    // - Queueable (EventHandler & Queueable)
    public function process(
        EventHandlerInterface | (EventHandlerInterface & AsyncInterface) | (EventHandlerInterface & QueueableInterface) $handler,
        $event
    ): void {
        $handler->handle($event);

        if ($handler instanceof AsyncInterface) {
            $handler->async();
        } else if ($handler instanceof QueueableInterface) {
            $handler->queue();
        }
    }
}

// Usage
$handler = new SyncEventHandler();
$asyncHandler = new AsyncEventHandler();
$queueHandler = new QueueEventHandler();

$processor = new EventProcessor();
$processor->process($handler, $event);
$processor->process($asyncHandler, $event);
$processor->process($queueHandler, $event);
```

### Plugin Systems

```php
<?php
interface PluginInterface
{
    public function execute(): void;
}

interface ConfigurableInterface
{
    public function configure(array $config): void;
}

interface CacheableInterface
{
    public function setCacheKey(string $key): void;
}

class PluginManager
{
    private array $plugins = [];

    public function register(
        (PluginInterface & ConfigurableInterface) | (PluginInterface & CacheableInterface) | PluginInterface $plugin
    ): void {
        $this->plugins[] = $plugin;
    }

    public function executePlugin($plugin, array $config = null): void
    {
        if ($plugin instanceof ConfigurableInterface && $config !== null) {
            $plugin->configure($config);
        }

        if ($plugin instanceof CacheableInterface) {
            $plugin->setCacheKey(md5(serialize($config ?? [])));
        }

        $plugin->execute();
    }
}
```

---

## Testing Patterns

### Type Checking Tests

```php
<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

interface Reader {}
interface Writer {}
interface Closeable {}

final class DNFTypeTest extends TestCase
{
    public function testDNFTypeAcceptance(): void
    {
        $readWriter = new class implements Reader, Writer {};
        $closeable = new class implements Closeable {};

        // Should accept both
        $this->processStream($readWriter);
        $this->processStream($closeable);
    }

    public function testInvalidTypeFails(): void
    {
        $this->expectError(TypeError::class);
        $invalidObject = new stdClass();
        // $this->processStream($invalidObject);  // Would fail
    }

    private function processStream((Reader & Writer) | Closeable $stream): void
    {
        // Process stream
    }
}
```

---

## Best Practices

**DNF Type Guidelines:**

```php
<?php
// ✓ GOOD: Clear intent
function process((Logger & Formatter) | Serializable $obj): void {}

// ✓ GOOD: Meaningful combinations
function handleStream((ReadableInterface & StreamInterface) | Iterator $stream): void {}

// ❌ AVOID: Overly complex
function bad((A & B & C) | (D & E & F) | (G & H & I) $obj): void {}
// Better: Break into smaller, more focused functions

// ❌ AVOID: Redundant types
function bad2((Reader | Writer) & (Reader | Writer) $obj): void {}
// Better: (Reader & Reader) | (Reader & Writer) | (Writer & Reader) | (Writer & Writer)
// Even better: Reconsider your design

// ✓ GOOD: Logical grouping
interface CacheableInterface {}
interface QueryableInterface {}
interface TransactionalInterface {}

function execute(
    (QueryableInterface & TransactionalInterface) | (QueryableInterface & CacheableInterface) $connection
): void {}

// ✓ GOOD: Document complex types
/**
 * @param (Reader & Closeable) | (Writer & Flushable) $resource
 *        Either a readable closeable resource or a writable flushable one
 */
function handleResource($resource): void {}
```

---

## Complete Examples

### Full Application Pattern

```php
<?php
declare(strict_types=1);

namespace App\DataProcessing;

use Iterator;

interface ProcessorInterface
{
    public function process($data): mixed;
}

interface CacheableInterface
{
    public function cache(string $key, $value): void;
    public function getCache(string $key): mixed;
}

interface LoggableInterface
{
    public function log(string $message): void;
}

class DataPipeline
{
    /**
     * @param (ProcessorInterface & CacheableInterface) | (ProcessorInterface & LoggableInterface) | ProcessorInterface $processor
     */
    public function execute($processor, $input): mixed
    {
        $cacheKey = null;

        // Try cache if available
        if ($processor instanceof CacheableInterface) {
            $cacheKey = md5(serialize($input));
            $cached = $processor->getCache($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        // Log if available
        if ($processor instanceof LoggableInterface) {
            $processor->log("Processing: " . serialize($input));
        }

        // Process
        $result = $processor->process($input);

        // Cache result if available
        if ($processor instanceof CacheableInterface && $cacheKey !== null) {
            $processor->cache($cacheKey, $result);
        }

        return $result;
    }
}

// Implementations
class CachedProcessor implements ProcessorInterface, CacheableInterface
{
    private array $cache = [];

    public function process($data): mixed
    {
        return array_map(fn($x) => $x * 2, (array)$data);
    }

    public function cache(string $key, $value): void
    {
        $this->cache[$key] = $value;
    }

    public function getCache(string $key): mixed
    {
        return $this->cache[$key] ?? null;
    }
}

class LoggedProcessor implements ProcessorInterface, LoggableInterface
{
    public function process($data): mixed
    {
        return array_sum((array)$data);
    }

    public function log(string $message): void
    {
        echo "[LOG] $message\n";
    }
}

// Usage
$pipeline = new DataPipeline();
$cachedProcessor = new CachedProcessor();
$loggedProcessor = new LoggedProcessor();

echo "Result 1: " . $pipeline->execute($cachedProcessor, [1, 2, 3]) . "\n";
echo "Result 2: " . $pipeline->execute($loggedProcessor, [1, 2, 3]) . "\n";
```

---

## See Also

- [PHP 8.2 Overview](0-php8.2-overview.md)
- [Readonly Classes](2-readonly-classes.md)
- [Union Types (PHP 8.0)](../14-php8.1/1-intro.md)
