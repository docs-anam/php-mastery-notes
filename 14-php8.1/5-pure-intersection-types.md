# Pure Intersection Types

## Overview

Learn about intersection types in PHP 8.1, which require values to satisfy multiple type constraints simultaneously.

---

## Table of Contents

1. What are Intersection Types
2. Basic Syntax
3. Multiple Interface Requirements
4. vs Union Types
5. Advanced Patterns
6. Type Checking with Intersections
7. Performance Considerations
8. Real-world Examples
9. Complete Examples

---

## What are Intersection Types

### Purpose

```php
<?php
// Before PHP 8.1: Accept any Logger
interface Logger {
    public function log(string $msg): void;
}

function saveLog(Logger $logger): void {
    $logger->log('Saving...');
}

// Problem: Logger might not be flushable
// We need both logging AND flushing capability

// Solution: Intersection Types (8.1+)
interface Logger {
    public function log(string $msg): void;
}

interface Flushable {
    public function flush(): void;
}

// Type must implement BOTH interfaces
function saveLog(Logger&Flushable $logger): void {
    $logger->log('Saving...');
    $logger->flush();  // Safe to call - guaranteed exists
}

// Only classes implementing both work
class FileLogger implements Logger, Flushable {
    public function log(string $msg): void { }
    public function flush(): void { }
}

saveLog(new FileLogger());  // ✓ Works
```

### Benefits

```
✓ Type safety - Guarantees multiple capabilities
✓ No inheritance chains - Avoid deep hierarchies
✓ Clear contracts - Intent is obvious
✓ Flexible composition - Mix interfaces as needed
✓ No null checks - Properties/methods always exist
✓ Better documentation - API requirements explicit
```

### Key Rules

```php
<?php
// 1. All types must be classes or interfaces
Type1&Type2&Type3

// 2. Cannot use with union types (separate feature)
// ✗ Type1|Type2&Type3  // Cannot mix

// 3. Order doesn't matter
Logger&Flushable === Flushable&Logger

// 4. Cannot have duplicate types
// ✗ Logger&Logger  // Error

// 5. Can only use with parameters, not return types (mixed)
// ✓ function process(Logger&Flushable $h): void
// ✗ function process(): Logger&Flushable  // Not allowed in 8.1
```

---

## Basic Syntax

### Simple Intersection

```php
<?php
interface Printable {
    public function toString(): string;
}

interface Comparable {
    public function compareTo($other): int;
}

// Function requires both capabilities
function display(Printable&Comparable $item): void {
    echo $item->toString();
}

class Product implements Printable, Comparable {
    public function __construct(
        private string $name,
        private float $price
    ) {}
    
    public function toString(): string {
        return "$this->name: \$$this->price";
    }
    
    public function compareTo($other): int {
        return $this->price <=> $other->price;
    }
}

$product = new Product('Laptop', 999.99);
display($product);  // Laptop: $999.99
```

### Three-way Intersection

```php
<?php
interface Logger {
    public function log(string $msg): void;
}

interface Serializable {
    public function serialize(): string;
}

interface Dumpable {
    public function dump(): void;
}

function processData(Logger&Serializable&Dumpable $handler): void {
    $handler->log('Processing');
    $data = $handler->serialize();
    $handler->dump();
}

class CompleteHandler implements Logger, Serializable, Dumpable {
    public function log(string $msg): void {
        echo "LOG: $msg\n";
    }
    
    public function serialize(): string {
        return json_encode(['data' => 'value']);
    }
    
    public function dump(): void {
        echo "Dumping...\n";
    }
}

processData(new CompleteHandler());
```

---

## Multiple Interface Requirements

### Practical Example

```php
<?php
// Complex data handler needs multiple capabilities

interface Readable {
    public function read(): string;
}

interface Writable {
    public function write(string $data): void;
}

interface Closeable {
    public function close(): void;
}

class FileHandler implements Readable, Writable, Closeable {
    private $handle;
    
    public function __construct(string $path) {
        $this->handle = fopen($path, 'r+');
    }
    
    public function read(): string {
        return fread($this->handle, 8192);
    }
    
    public function write(string $data): void {
        fwrite($this->handle, $data);
    }
    
    public function close(): void {
        fclose($this->handle);
    }
}

// Can safely use all methods
function processFile(Readable&Writable&Closeable $file): void {
    $data = $file->read();
    $file->write("Processed: $data");
    $file->close();
}

$file = new FileHandler('data.txt');
processFile($file);
```

### Database Connection Pool

```php
<?php
interface QueryExecutor {
    public function execute(string $sql): array;
}

interface TransactionManager {
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollback(): void;
}

interface ConnectionState {
    public function isConnected(): bool;
    public function disconnect(): void;
}

class DatabaseConnection implements QueryExecutor, TransactionManager, ConnectionState {
    private bool $connected = false;
    private bool $inTransaction = false;
    private PDO $pdo;
    
    public function execute(string $sql): array {
        return $this->pdo->query($sql)->fetchAll();
    }
    
    public function beginTransaction(): void {
        $this->pdo->beginTransaction();
        $this->inTransaction = true;
    }
    
    public function commit(): void {
        $this->pdo->commit();
        $this->inTransaction = false;
    }
    
    public function rollback(): void {
        $this->pdo->rollback();
        $this->inTransaction = false;
    }
    
    public function isConnected(): bool {
        return $this->connected;
    }
    
    public function disconnect(): void {
        $this->pdo = null;
        $this->connected = false;
    }
}

function safeDatabaseOperation(
    QueryExecutor&TransactionManager&ConnectionState $db
): void {
    if (!$db->isConnected()) {
        return;
    }
    
    $db->beginTransaction();
    try {
        $results = $db->execute("SELECT * FROM users");
        $db->commit();
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    } finally {
        $db->disconnect();
    }
}
```

---

## vs Union Types

### Key Differences

```php
<?php
// Union Type: Accept ONE of several types
function processUnion(LoggerA|LoggerB|LoggerC $logger): void {
    // $logger could be any ONE of these types
    // Must check which one before using specific methods
    if ($logger instanceof LoggerA) {
        $logger->methodA();
    }
}

// Intersection Type: Accept ALL types simultaneously
interface LoggerA {
    public function log(): void;
}

interface LoggerB {
    public function flush(): void;
}

function processIntersection(LoggerA&LoggerB $logger): void {
    // $logger is guaranteed to be BOTH types
    // Can safely call methods from both
    $logger->log();
    $logger->flush();
}

// Union: "Accept this OR that"
// Intersection: "Accept this AND that"
```

### When to Use Each

```php
<?php
// Use Union Types when:
// - Accepting different alternative implementations
// - Each alternative has different methods
// - Some methods only in certain types

function handle(FileHandler|ApiHandler $source): void {
    if ($source instanceof FileHandler) {
        $source->readFile();
    } else {
        $source->fetchApi();
    }
}

// Use Intersection Types when:
// - Object needs multiple capabilities
// - All methods needed from all types
// - No branching logic needed

function process(Logger&Flushable&Serializable $handler): void {
    // Use all methods without checking
    $handler->log('Processing');
    $handler->flush();
    $data = $handler->serialize();
}
```

---

## Advanced Patterns

### Intersection with Inheritance

```php
<?php
// Base interfaces
interface Reader {
    public function read(): string;
}

interface Writer {
    public function write(string $data): void;
}

// Extended interfaces
interface Searchable extends Reader {
    public function search(string $term): array;
}

interface Configurable extends Writer {
    public function configure(array $config): void;
}

// Intersection of extended interfaces
function enhance(Searchable&Configurable $handler): void {
    // Has all methods from:
    // - Searchable (read + search)
    // - Configurable (write + configure)
    
    $handler->configure(['timeout' => 5]);
    $results = $handler->search('term');
    $handler->write(json_encode($results));
    $content = $handler->read();
}
```

### Conditional Behavior

```php
<?php
interface Logger {
    public function log(string $msg): void;
}

interface ErrorHandler {
    public function handleError(Exception $e): void;
}

interface RecoveryMechanism {
    public function recover(): void;
}

function robustExecution(
    Logger $logger,
    ErrorHandler&RecoveryMechanism $errorHandler
): void {
    // Logger optional, but ErrorHandler must have both ErrorHandler AND RecoveryMechanism
    
    try {
        $logger?->log('Starting operation');
        // Do work
    } catch (Exception $e) {
        $errorHandler->handleError($e);
        $errorHandler->recover();  // Guaranteed to exist
        $logger?->log("Recovered from error");
    }
}
```

---

## Type Checking with Intersections

### Instance Checking

```php
<?php
interface Logger {
    public function log(string $msg): void;
}

interface Flushable {
    public function flush(): void;
}

class FileLogger implements Logger, Flushable {
    public function log(string $msg): void {}
    public function flush(): void {}
}

$logger = new FileLogger();

// Check for intersection
if ($logger instanceof (Logger&Flushable)) {  // 8.1+ syntax
    // Guaranteed both interfaces
}

// Traditional checks still work
if ($logger instanceof Logger && $logger instanceof Flushable) {
    // Both interfaces confirmed
}
```

### Reflection with Intersections

```php
<?php
use ReflectionFunction;
use ReflectionIntersectionType;

interface A {}
interface B {}

function process(A&B $param) {}

$reflection = new ReflectionFunction('process');
$param = $reflection->getParameters()[0];
$type = $param->getType();

if ($type instanceof ReflectionIntersectionType) {
    $types = $type->getTypes();
    foreach ($types as $t) {
        echo $t->getName();  // A, B
    }
}
```

---

## Performance Considerations

### Compilation Impact

```php
<?php
// Intersection types are evaluated at compile time
// Minimal runtime overhead

interface A {}
interface B {}
interface C {}

function processThree(A&B&C $obj): void {
    // Single type check, not three separate checks
    // PHP optimizes this internally
}

// Better than:
function processThreeOld($obj): void {
    if (!($obj instanceof A && $obj instanceof B && $obj instanceof C)) {
        throw new TypeError();
    }
}
```

### Memory Efficiency

```php
<?php
// Intersection types don't create wrapper objects
// Just references to the same object

interface Logger {}
interface Flushable {}

function handle(Logger&Flushable $obj): void {
    // $obj is single object, not wrapper
    // No memory overhead
}
```

---

## Real-world Examples

### API Client

```php
<?php
interface ApiClient {
    public function request(string $method, string $endpoint): array;
}

interface Retryable {
    public function setMaxRetries(int $max): void;
    public function retry(callable $fn): mixed;
}

interface Loggable {
    public function enableLogging(bool $enable): void;
    public function getLog(): array;
}

class HttpClient implements ApiClient, Retryable, Loggable {
    private int $maxRetries = 3;
    private bool $logging = false;
    private array $log = [];
    
    public function request(string $method, string $endpoint): array {
        return []; // HTTP call
    }
    
    public function setMaxRetries(int $max): void {
        $this->maxRetries = $max;
    }
    
    public function retry(callable $fn): mixed {
        for ($i = 0; $i < $this->maxRetries; $i++) {
            try {
                return $fn();
            } catch (Exception $e) {
                if ($i === $this->maxRetries - 1) throw $e;
            }
        }
    }
    
    public function enableLogging(bool $enable): void {
        $this->logging = $enable;
    }
    
    public function getLog(): array {
        return $this->log;
    }
}

function fetchUserData(
    ApiClient&Retryable&Loggable $client
): array {
    $client->enableLogging(true);
    $client->setMaxRetries(5);
    
    return $client->retry(
        fn() => $client->request('GET', '/users')
    );
}
```

### Message Queue Handler

```php
<?php
interface MessageProducer {
    public function publish(string $message): void;
}

interface MessageConsumer {
    public function consume(): ?string;
}

interface QueueManagement {
    public function getQueueSize(): int;
    public function purge(): void;
}

class RabbitMQQueue implements MessageProducer, MessageConsumer, QueueManagement {
    private AMQPChannel $channel;
    
    public function publish(string $message): void {
        // Publish to RabbitMQ
    }
    
    public function consume(): ?string {
        // Consume from RabbitMQ
        return null;
    }
    
    public function getQueueSize(): int {
        // Get queue size
        return 0;
    }
    
    public function purge(): void {
        // Clear queue
    }
}

function monitorQueue(
    MessageProducer&MessageConsumer&QueueManagement $queue
): void {
    echo "Queue size: " . $queue->getQueueSize() . "\n";
    
    while ($message = $queue->consume()) {
        echo "Processing: $message\n";
    }
    
    if ($queue->getQueueSize() > 1000) {
        $queue->purge();
    }
}
```

---

## Complete Examples

### Example 1: Plugin System

```php
<?php
interface Plugin {
    public function getName(): string;
    public function execute(): void;
}

interface Configurable {
    public function configure(array $config): void;
}

interface Hooks {
    public function registerHook(string $name, callable $handler): void;
    public function executeHook(string $name): void;
}

class PluginManager {
    private array $plugins = [];
    
    public function register(Plugin $plugin): void {
        $this->plugins[$plugin->getName()] = $plugin;
    }
    
    public function execute(string $name): void {
        if (!isset($this->plugins[$name])) {
            throw new Exception("Plugin not found: $name");
        }
        
        $plugin = $this->plugins[$name];
        $plugin->execute();
    }
    
    public function configurePlugin(
        string $name,
        Plugin&Configurable $plugin,
        array $config
    ): void {
        $plugin->configure($config);
        $this->register($plugin);
    }
    
    public function setupHooks(
        string $name,
        Plugin&Hooks $plugin
    ): void {
        $plugin->registerHook('init', fn() => echo "Init\n");
        $this->register($plugin);
    }
}
```

### Example 2: Repository Pattern

```php
<?php
interface Repository {
    public function findById(int $id);
    public function save($entity): void;
}

interface Queryable {
    public function where(string $field, $value): self;
    public function get(): array;
}

interface Transactional {
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollback(): void;
}

class UserRepository implements Repository, Queryable, Transactional {
    private PDO $pdo;
    
    public function findById(int $id) {
        // Find user by ID
    }
    
    public function save($entity): void {
        // Save user
    }
    
    public function where(string $field, $value): self {
        // Build query
        return $this;
    }
    
    public function get(): array {
        // Execute query
        return [];
    }
    
    public function beginTransaction(): void {
        $this->pdo->beginTransaction();
    }
    
    public function commit(): void {
        $this->pdo->commit();
    }
    
    public function rollback(): void {
        $this->pdo->rollback();
    }
}

function complexUserOperation(
    Repository&Queryable&Transactional $users
): void {
    $users->beginTransaction();
    
    try {
        $activeUsers = $users
            ->where('status', 'active')
            ->get();
        
        foreach ($activeUsers as $user) {
            $users->save(['id' => $user['id'], 'updated' => true]);
        }
        
        $users->commit();
    } catch (Exception $e) {
        $users->rollback();
        throw $e;
    }
}
```

---

## Key Takeaways

**Intersection Types Checklist:**

1. ✅ Use for multiple interface requirements
2. ✅ All types must be interfaces/classes
3. ✅ Cannot use with union types (|)
4. ✅ Prefer over multiple instanceof checks
5. ✅ Document API expectations
6. ✅ Works with inheritance chains
7. ✅ Use reflection when needed
8. ✅ Combine with modern type hints

---

## See Also

- [Enumerations](2-enumerations.md)
- [Readonly Properties](3-readonly-properties.md)
- [Union Types (PHP 8.0)](../04-php8/union-types.md)
- [Never Return Type](6-never-return-type.md)
