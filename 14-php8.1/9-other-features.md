# Other PHP 8.1 Features

## Overview

Discover additional PHP 8.1 features and improvements beyond the major language features, including performance enhancements, array unpacking, and more.

---

## Table of Contents

1. Array Unpacking with String Keys
2. Named Arguments Improvements
3. Fibers
4. Deprecations and Changes
5. Performance Improvements
6. Additional Type System Features
7. Miscellaneous Features
8. Migration Guide

---

## Array Unpacking with String Keys

### Basic Unpacking

```php
<?php
// Before PHP 8.1: String keys not unpacked

$array1 = ['a' => 1, 'b' => 2];
$array2 = ['c' => 3, 'd' => 4];

// Old way (numeric keys only)
$merged = [...[1, 2], ...[3, 4]];
// Result: [1, 2, 3, 4]

// With string keys - now works!
$merged = [...$array1, ...$array2];
// Result: ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]

// Useful for configuration merging
$defaults = ['debug' => false, 'cache' => true];
$custom = ['debug' => true];
$config = [...$defaults, ...$custom];
// Result: ['debug' => true, 'cache' => true]
```

### Duplicate Key Handling

```php
<?php
// Last value wins
$array1 = ['name' => 'John', 'age' => 30];
$array2 = ['name' => 'Jane', 'email' => 'jane@example.com'];

$merged = [...$array1, ...$array2];
// Result: ['name' => 'Jane', 'age' => 30, 'email' => 'jane@example.com']
// 'name' from $array2 overwrites $array1

// Useful for configuration override
$defaults = ['host' => 'localhost', 'port' => 5432];
$environment = ['host' => 'db.remote.com'];

$config = [...$defaults, ...$environment];
// Result: ['host' => 'db.remote.com', 'port' => 5432]
```

### Deep Merging

```php
<?php
// Note: Spread operator does shallow merge

function deepMerge(array $array1, array $array2): array {
    foreach ($array2 as $key => $value) {
        if (is_array($value) && is_array($array1[$key] ?? null)) {
            $array1[$key] = deepMerge($array1[$key], $value);
        } else {
            $array1[$key] = $value;
        }
    }
    return $array1;
}

$config1 = [
    'database' => [
        'host' => 'localhost',
        'port' => 5432,
    ],
    'cache' => [
        'driver' => 'redis',
    ]
];

$config2 = [
    'database' => [
        'host' => 'remote.db.com',
    ],
    'cache' => [
        'ttl' => 3600,
    ]
];

$merged = deepMerge($config1, $config2);
// Properly merges nested arrays
```

---

## Named Arguments Improvements

### Argument Skipping

```php
<?php
// Define function with many parameters
function sendEmail(
    string $to,
    string $subject,
    string $body,
    string $from = 'noreply@example.com',
    bool $html = false,
    array $attachments = [],
): void {
    // Implementation
}

// Skip non-required parameters using named args
sendEmail(
    to: 'john@example.com',
    subject: 'Hello',
    body: 'Welcome!',
    attachments: ['file.pdf']
    // Skipped $from and $html, use defaults
);

// More readable than positional
sendEmail(
    'john@example.com',
    'Hello',
    'Welcome!',
    'noreply@example.com',  // What is this?
    false,                   // And this?
    ['file.pdf']             // Finally!
);
```

### Class Construction

```php
<?php
class ApiClient {
    public function __construct(
        private string $baseUrl,
        private int $timeout = 30,
        private bool $verify = true,
        private array $headers = [],
    ) {}
}

// Skip optional parameters
$client = new ApiClient(
    baseUrl: 'https://api.example.com',
    headers: ['Authorization' => 'Bearer token']
);

// vs positional
$client = new ApiClient(
    'https://api.example.com',
    30,
    true,
    ['Authorization' => 'Bearer token']
);

// Named arguments are clearer
```

---

## Fibers

### What are Fibers

```php
<?php
// Fibers are lightweight threads for concurrent code

class AsyncTask {
    public static function example(): void {
        $fiber1 = new Fiber(function(): void {
            echo "Fiber 1 start\n";
            Fiber::suspend();
            echo "Fiber 1 end\n";
        });
        
        $fiber2 = new Fiber(function(): void {
            echo "Fiber 2 start\n";
            Fiber::suspend();
            echo "Fiber 2 end\n";
        });
        
        // Run fibers
        $fiber1->start();
        $fiber2->start();
        
        $fiber1->resume();
        $fiber2->resume();
    }
}

// Output:
// Fiber 1 start
// Fiber 2 start
// Fiber 1 end
// Fiber 2 end
```

### Fiber Use Cases

```php
<?php
// Concurrent I/O operations
$readFile = new Fiber(function(string $path): string {
    echo "Reading $path\n";
    // Suspend to let other fiber run
    Fiber::suspend();
    // Resume later
    return file_get_contents($path);
});

// Process data while I/O happens
$processData = new Fiber(function(): void {
    echo "Processing...\n";
    Fiber::suspend();
    echo "Done\n";
});

// Interleaved execution
$readFile->start('/path/to/file');
$processData->start();

$readFile->resume();
$processData->resume();

// Useful for:
// - Concurrent HTTP requests
// - Parallel I/O operations
// - Cooperative multitasking
// - More responsive applications
```

---

## Deprecations and Changes

### Deprecated Features

```php
<?php
// Auto-casting numeric strings to int

$value = (int)'123abc';  // Previously: 123, Now: deprecated

// PDO::ATTR_ERRMODE defaults changing
$pdo->setAttribute(
    PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION  // Be explicit
);

// Deprecated: Implicit float overflow to INF
$value = 9.223372036854775807e307 + 1;  // Avoid

// Deprecated: Implicit null type in properties
class Old {
    public string $property;  // Could be null implicitly
    // Better: ?string or = null
}
```

### Breaking Changes

```php
<?php
// Fixed: array_is_list() function (new)
var_dump(array_is_list([1, 2, 3]));          // true
var_dump(array_is_list([0 => 1, 2 => 2]));   // false

// Changed: Serializable behavior
// Use JsonSerializable instead when possible

// Changed: PDO behavior
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check deprecation notices
// php -d error_reporting=E_ALL -d error_log=/dev/stdout script.php
```

---

## Performance Improvements

### JIT Compilation

```php
<?php
// PHP 8.1 continues JIT improvements from 8.0

// Enable JIT in php.ini
// opcache.jit=tracing or opcache.jit=function
// opcache.jit_buffer_size=100M

// JIT benefits:
// - Compiled machine code for hot paths
// - 2-3x faster for CPU-bound code
// - Better performance on math-heavy operations
// - Stream processing improvements

// Test JIT performance
$start = microtime(true);

// CPU-intensive work
for ($i = 0; $i < 1000000; $i++) {
    $result = ($i * 2) + ($i / 3) - ($i % 5);
}

$end = microtime(true);
echo "Time: " . ($end - $start) . " seconds\n";

// With JIT: Significantly faster
```

### Memory Efficiency

```php
<?php
// PHP 8.1 optimizes memory usage

// Readonly properties reduce memory overhead
class OptimizedEntity {
    public function __construct(
        public readonly string $id,
        public readonly int $timestamp,
    ) {}
}

// vs mutable
class Heavier {
    private string $id;
    private int $timestamp;
    
    public function __construct(string $id, int $timestamp) {
        $this->id = $id;
        $this->timestamp = $timestamp;
    }
}

// Readonly allows compiler optimizations
```

---

## Additional Type System Features

### Mixed Type

```php
<?php
// Mixed type accepts any type (PHP 8.0 feature, continued)

function flexible(mixed $value): mixed {
    // Can be any type
    return match(gettype($value)) {
        'string' => strlen($value),
        'array' => count($value),
        'object' => get_class($value),
        default => null,
    };
}

echo flexible('hello');     // 5
echo flexible([1, 2, 3]);   // 3
```

### False and True Types

```php
<?php
// Specific boolean types (PHP 8.1+)

function onlyTrue(true $value): void {
    // Only true accepted
    echo "Got true";
}

function onlyFalse(false $value): void {
    // Only false accepted
    echo "Got false";
}

function maybe(true|false $value): void {
    // Union of true and false (equivalent to bool)
}

// Useful for literal values
function processFlag(true|null $flag): void {
    if ($flag === true) {
        echo "Enabled";
    } else {
        echo "Disabled (null)";
    }
}
```

---

## Miscellaneous Features

### fsync() Function

```php
<?php
// Ensure file data is written to disk

$file = fopen('data.txt', 'w');
fwrite($file, 'Important data');

// Ensure it's physically written
if (function_exists('fsync')) {
    fsync($file);  // PHP 8.1+
}

fclose($file);

// Important for:
// - Critical data
// - Database files
// - Configuration files
```

### json_validate() Function

```php
<?php
// Check if string is valid JSON (without decoding)

$json = '{"name": "John"}';
$invalid = '{"name": John}';

var_dump(json_validate($json));     // bool(true)
var_dump(json_validate($invalid));  // bool(false)

// Faster than json_decode() + error check
// Useful for:
// - Input validation
// - Performance-critical code
// - Pre-validation before processing
```

### Array Functions

```php
<?php
// array_is_list() - Check if array is list (sequential int keys)

var_dump(array_is_list([1, 2, 3]));          // bool(true)
var_dump(array_is_list([0 => 1, 2 => 2]));   // bool(false)
var_dump(array_is_list(['a' => 1, 'b' => 2])); // bool(false)

// Useful for:
// - Type checking
// - JSON encoding decisions
// - Array format validation
```

---

## Migration Guide

### From PHP 8.0 to 8.1

```php
<?php
// 1. Start using Readonly properties
class UserProfile {
    public function __construct(
        public readonly string $email,
    ) {}
}

// 2. Replace string callables with first-class syntax
$strlen = strlen(...);

// 3. Use enums instead of class constants
enum Status {
    case Active;
    case Inactive;
}

// 4. Use new in initializer
class Service {
    public function __construct(
        private Logger $logger = new Logger(),
    ) {}
}

// 5. Use intersection types
function process(Logger&Flushable $handler): void {}

// 6. Use never return type for functions that don't return
function fail(): never {
    throw new Exception();
}

// 7. Mark constants as final in abstract classes
class AbstractConfig {
    final public const VERSION = '1.0';
}

// 8. Test all changes thoroughly
```

### Compatibility Checklist

```php
<?php
// Before upgrading:

// 1. Check PHP version requirement
if (PHP_VERSION_ID < 80100) {
    die("PHP 8.1+ required");
}

// 2. Update composer.json
// "php": ">=8.1",

// 3. Test with newer type hints
// Use declare(strict_types=1) everywhere

// 4. Check extensions
// Some may not support 8.1 yet
// Run: php -m

// 5. Update dependencies
// composer update

// 6. Run tests
// vendor/bin/phpunit

// 7. Check for deprecation notices
// php -d error_reporting=E_ALL script.php
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

use enum Status;

// Enum for statuses
enum Status: string {
    case Pending = 'pending';
    case Active = 'active';
    case Archived = 'archived';
}

// Logger interface
interface Logger {
    public function log(string $msg): void;
}

interface Flushable {
    public function flush(): void;
}

// Modern PHP 8.1 service class
class UserService {
    public function __construct(
        private Logger&Flushable $logger = new ConsoleLogger(),
        private int $maxRetries = 3,
    ) {}
    
    public function createUser(
        string $email,
        string $password,
    ): User|never {
        // Validate
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->fail("Invalid email");
        }
        
        if (strlen($password) < 8) {
            $this->fail("Password too short");
        }
        
        $this->logger->log("Creating user: $email");
        
        $user = new User(
            email: $email,
            status: Status::Pending,
        );
        
        $this->logger->flush();
        
        return $user;
    }
    
    private function fail(string $message): never {
        $this->logger->log("ERROR: $message");
        throw new InvalidArgumentException($message);
    }
}

// User value object with readonly
class User {
    public function __construct(
        public readonly string $email,
        public readonly Status $status = Status::Pending,
        public readonly DateTime $createdAt = new DateTime(),
    ) {}
}

class ConsoleLogger implements Logger, Flushable {
    public function log(string $msg): void {
        echo "[LOG] $msg\n";
    }
    
    public function flush(): void {
        echo "[FLUSH]\n";
    }
}

// First-class callables
$mapToString = strlen(...);
```

---

## Key Takeaways

**PHP 8.1 Other Features Checklist:**

1. ✅ Array unpacking with string keys
2. ✅ Named arguments for flexibility
3. ✅ Fibers for concurrency
4. ✅ Check deprecation warnings
5. ✅ Enable JIT for performance
6. ✅ Use json_validate()
7. ✅ Use array_is_list()
8. ✅ Update dependencies

---

## See Also

- [Enumerations](2-enumerations.md)
- [Readonly Properties](3-readonly-properties.md)
- [First-class Callables](4-first-class-callable-syntax.md)
- [Intersection Types](5-pure-intersection-types.md)
- [Never Return Type](6-never-return-type.md)
