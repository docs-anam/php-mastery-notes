# Never Return Type

## Overview

Learn about the `never` return type in PHP 8.1, which indicates a function never returns normally to the caller.

---

## Table of Contents

1. What is Never Return Type
2. Basic Syntax
3. Exit and Return Cases
4. Exception Throwing
5. Infinite Loops
6. Combining with Types
7. Control Flow Analysis
8. Real-world Patterns
9. Complete Examples

---

## What is Never Return Type

### Purpose

```php
<?php
// Function that never returns to caller

// Throws exception
function throwError(string $msg): never {
    throw new Exception($msg);
}

// Calls exit()
function shutdown(): never {
    exit(1);
}

// Infinite loop
function poll(): never {
    while (true) {
        // Keep looping forever
    }
}

// Benefits of never type:
// ✓ Clear intent - Function never returns
// ✓ Control flow - Compiler knows execution stops
// ✓ Type safety - Stronger guarantees
// ✓ Dead code detection - Warns if code after never
// ✓ Documentation - API contract explicit
```

### When to Use

```
Use 'never' when function:
✓ Always throws exception
✓ Always calls exit/die
✓ Contains infinite loop
✓ Never completes successfully

Don't use 'never' for:
✗ Functions that might return
✗ Functions with optional exits
✗ Functions that might loop
```

---

## Basic Syntax

### Exception Throwing

```php
<?php
// Function that only throws
function assert(bool $condition, string $message): never {
    if ($condition) {
        throw new AssertionError($message);
    }
}

// Called when exception always thrown
function parseJson(string $json): never {
    $data = json_decode($json);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new JsonException(json_last_error_msg());
    }
    
    // Never reaches here if error
}
```

### Simple Never Functions

```php
<?php
// Simple exit
function exit_handler(int $code = 0): never {
    exit($code);
}

// Calls shutdown
function die_with_error(string $message): never {
    die("ERROR: $message");
}

// Type declaration ensures safety
function stopExecution(): never {
    // Guaranteed to never return
    exit;
}
```

### Infinite Loop

```php
<?php
// Process that runs forever
function daemon(): never {
    while (true) {
        // Handle signals, tasks, etc.
        sleep(1);
    }
}

// Long-running process
function eventLoop(): never {
    $loop = new EventLoop();
    $loop->run();  // Runs forever
}
```

---

## Exit and Return Cases

### Explicit Exit

```php
<?php
function cleanShutdown(int $code): never {
    echo "Cleaning up...\n";
    
    // Cleanup code
    @unlink('temp_files');
    
    exit($code);  // Never returns
}

// Call it
cleanShutdown(0);  // Program terminates
echo "Never printed";  // Dead code

// Usage
try {
    cleanShutdown(1);
} catch (Throwable $e) {
    // Won't catch - exit doesn't throw
}
```

### Die Construct

```php
<?php
function abort(string $message): never {
    die("FATAL: $message");  // Also never returns
}

// Equivalent to exit()
function stopNow(): never {
    // Either works
    exit;
}
```

### Return After Exception

```php
<?php
// After throwing, execution never returns
function validate(string $input): never {
    if (empty($input)) {
        throw new InvalidArgumentException('Empty input');
    }
    
    // All paths lead to throw
    throw new RuntimeException('Invalid format');
    
    return;  // Unreachable
}
```

---

## Exception Throwing

### Always Throws

```php
<?php
// Function that handles errors by throwing
function throwIf(bool $condition, string $exception, string $message): never {
    if ($condition) {
        $class = $exception;
        throw new $class($message);
    }
    
    // This path also throws
    throw new Exception('Unexpected condition');
}

// Multiple exception paths
function parse(mixed $value): never {
    if (is_null($value)) {
        throw new NullPointerException();
    }
    
    if (is_array($value) && empty($value)) {
        throw new EmptyArrayException();
    }
    
    if (!is_scalar($value)) {
        throw new InvalidTypeException();
    }
    
    throw new ParseException('Could not parse');
}
```

### Exception Methods

```php
<?php
class ValidationError extends Exception {}

// Always throw validation errors
function validate(string $data): never {
    $errors = [];
    
    if (strlen($data) < 3) {
        $errors[] = 'Too short';
    }
    
    if (!preg_match('/^[a-z]+$/', $data)) {
        $errors[] = 'Invalid characters';
    }
    
    if (!empty($errors)) {
        throw new ValidationError(
            implode(', ', $errors)
        );
    }
    
    // All paths must throw
    throw new ValidationError('Unknown error');
}
```

### Throwing from Called Functions

```php
<?php
function throwException(): never {
    throw new Exception('Error');
}

function wrapper(): never {
    // Calling function that throws never
    throwException();  // Never returns
    
    // Dead code below
    echo "Never printed";
}
```

---

## Infinite Loops

### Event Loop

```php
<?php
class EventLoop {
    private array $handlers = [];
    
    public function on(string $event, callable $handler): void {
        $this->handlers[$event][] = $handler;
    }
    
    public function run(): never {
        while (true) {
            // Wait for events
            foreach ($this->handlers as $event => $handlers) {
                foreach ($handlers as $handler) {
                    $handler();
                }
            }
            
            usleep(100000);  // 100ms
        }
    }
}

// Usage
$loop = new EventLoop();
$loop->on('tick', function() {
    echo "Tick\n";
});

$loop->run();  // Runs forever
```

### Daemon Process

```php
<?php
class Worker {
    private bool $running = true;
    
    public function stop(): void {
        $this->running = false;
    }
    
    public function run(): never {
        while (true) {
            // Process tasks
            $this->processTask();
            
            // Check interval
            sleep(5);
            
            // Even with stop(), this never returns
            // It would need to exit() or throw
        }
    }
    
    private function processTask(): void {
        echo "Processing...\n";
    }
}

// Note: This daemon would never actually stop without exit/throw
// Better to use exit() when needed
```

---

## Combining with Types

### With Union Types

```php
<?php
// Can combine never with other types conceptually
// (though return type can't be union with never)

interface Logger {
    // Logs and throws
    public function logAndThrow(string $msg): never;
}

class FileLogger implements Logger {
    public function logAndThrow(string $msg): never {
        file_put_contents('log.txt', $msg);
        throw new LoggingException($msg);
    }
}
```

### Parameter Type Hints

```php
<?php
// Parameter can be any type
function fail(mixed $data): never {
    echo "Failed with: ";
    var_dump($data);
    exit(1);
}

function enforceType(string $data): never {
    throw new TypeError("Invalid type");
}

function enforcePattern(callable $validator, mixed $data): never {
    if (!$validator($data)) {
        throw new ValueError("Validation failed");
    }
    throw new Exception("Unknown error");
}
```

---

## Control Flow Analysis

### Dead Code Detection

```php
<?php
// PHP detects dead code after never functions
function fail(): never {
    throw new Exception();
}

function process(): void {
    fail();
    echo "Never printed";  // IDE warns: Unreachable code
}

// Code after never is unreachable
$result = getData();
fail("Error processing");  // never returns
sendResponse($result);  // Unreachable - IDE warns
```

### Type Narrowing

```php
<?php
// Never helps type narrowing
function ensure(mixed $value): never {
    if ($value === null) {
        throw new NullPointerException();
    }
    throw new InvalidValueException();
}

function process(mixed $value): void {
    ensure($value);  // This line never returns
    
    // Below is unreachable
    echo $value;  // Type is 'never' - unreachable
}
```

---

## Real-world Patterns

### Assertion Helper

```php
<?php
function assert(mixed $value, string $message = ''): never {
    if (!$value) {
        throw new AssertionError($message ?: 'Assertion failed');
    }
    throw new AssertionError('Assertion failed');
}

function assertType(mixed $value, string $type): never {
    if (gettype($value) !== $type) {
        throw new TypeError("Expected $type, got " . gettype($value));
    }
    throw new TypeError('Type assertion failed');
}

// Usage
function processUser(mixed $user): void {
    assertType($user, 'array');  // Throws if not array
    
    // After this point, PHP knows $user is array
    echo $user['name'];  // Type is narrowed
}
```

### Error Handling Middleware

```php
<?php
class ErrorHandler {
    public function handle(Exception $exception): never {
        // Log error
        error_log($exception->getMessage());
        
        // Send response
        http_response_code(500);
        echo json_encode([
            'error' => $exception->getMessage()
        ]);
        
        exit(1);  // Never returns
    }
}

// Usage in middleware
function errorMiddleware(callable $next): void {
    try {
        $next();
    } catch (Exception $e) {
        (new ErrorHandler())->handle($e);
    }
}
```

### Validation Assertion

```php
<?php
class Validator {
    private array $errors = [];
    
    public function add(bool $condition, string $error): self {
        if (!$condition) {
            $this->errors[] = $error;
        }
        return $this;
    }
    
    public function throwIfInvalid(): never {
        if (!empty($this->errors)) {
            throw new ValidationException(
                implode(', ', $this->errors)
            );
        }
        
        throw new ValidationException('Unknown error');
    }
}

// Usage
function registerUser(array $data): void {
    (new Validator())
        ->add(!empty($data['email']), 'Email required')
        ->add(filter_var($data['email'], FILTER_VALIDATE_EMAIL), 'Invalid email')
        ->add(!empty($data['password']), 'Password required')
        ->add(strlen($data['password']) >= 8, 'Password too short')
        ->throwIfInvalid();
    
    // If we reach here, all validations passed
    saveUser($data);
}
```

---

## Complete Examples

### Example 1: Custom Exception Handler

```php
<?php
class ExceptionHandler {
    private bool $debug;
    
    public function __construct(bool $debug = false) {
        $this->debug = $debug;
    }
    
    public function handle(Throwable $throwable): never {
        $statusCode = 500;
        $message = 'Internal Server Error';
        
        if ($throwable instanceof HttpException) {
            $statusCode = $throwable->getStatusCode();
            $message = $throwable->getMessage();
        }
        
        http_response_code($statusCode);
        
        $response = [
            'status' => 'error',
            'code' => $statusCode,
            'message' => $message,
        ];
        
        if ($this->debug) {
            $response['debug'] = [
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => $throwable->getTraceAsString(),
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit($statusCode);
    }
    
    public static function register(bool $debug = false): void {
        $handler = new self($debug);
        set_exception_handler([$handler, 'handle']);
    }
}

// Usage
ExceptionHandler::register(true);

function riskyOperation(): void {
    throw new Exception("Something failed");
}

// Exception caught by registered handler and never returns
```

### Example 2: Request Validator

```php
<?php
class RequestValidator {
    private Request $request;
    
    public function __construct(Request $request) {
        $this->request = $request;
    }
    
    public function validateJson(): never {
        $json = $this->request->getBody();
        $decoded = json_decode($json);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->respondWithError(
                'Invalid JSON: ' . json_last_error_msg(),
                400
            );
        }
        
        throw new Exception('Validation failed');
    }
    
    public function requireMethod(string $method): never {
        if ($this->request->getMethod() !== strtoupper($method)) {
            $this->respondWithError(
                'Method must be ' . strtoupper($method),
                405
            );
        }
        
        throw new Exception('Method validation failed');
    }
    
    public function requireHeader(string $header): never {
        if (!$this->request->hasHeader($header)) {
            $this->respondWithError(
                "Header '$header' required",
                400
            );
        }
        
        throw new Exception('Header validation failed');
    }
    
    private function respondWithError(string $message, int $code): never {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => $message,
            'code' => $code
        ]);
        exit;
    }
}

// Usage
function handleRequest(Request $request): void {
    $validator = new RequestValidator($request);
    
    $validator->requireMethod('POST');  // Throws if not POST
    $validator->requireHeader('Content-Type');  // Throws if missing
    $validator->validateJson();  // Throws if invalid JSON
    
    // If all validations pass (won't happen with above calls)
    processRequest($request);
}
```

### Example 3: Graceful Shutdown

```php
<?php
class Application {
    private array $shutdownHandlers = [];
    
    public function onShutdown(callable $handler): void {
        $this->shutdownHandlers[] = $handler;
    }
    
    public function shutdown(string $message = '', int $code = 0): never {
        // Call all shutdown handlers
        foreach ($this->shutdownHandlers as $handler) {
            try {
                $handler();
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
        }
        
        // Log final message
        if ($message) {
            error_log($message);
        }
        
        // Exit cleanly
        exit($code);
    }
}

// Usage
$app = new Application();

$app->onShutdown(function() {
    echo "Closing database...\n";
});

$app->onShutdown(function() {
    echo "Cleaning temporary files...\n";
});

// Later...
$app->shutdown("Application terminated", 0);
// Both shutdown handlers called, then exits
```

---

## Key Takeaways

**Never Return Type Checklist:**

1. ✅ Use for functions that never return
2. ✅ Use for exception-only functions
3. ✅ Use for exit/die functions
4. ✅ Use for infinite loops
5. ✅ Helps with control flow analysis
6. ✅ Dead code detection
7. ✅ Clear API contracts
8. ✅ Works with all parameter types

---

## See Also

- [Enumerations](2-enumerations.md)
- [Readonly Properties](3-readonly-properties.md)
- [Intersection Types](5-pure-intersection-types.md)
- [Exception Handling (PHP Basics)](../01-basics/exception-handling.md)
