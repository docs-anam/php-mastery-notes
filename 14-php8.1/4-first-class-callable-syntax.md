# First-class Callable Syntax

## Overview

Learn about first-class callable syntax in PHP 8.1, which provides a cleaner way to create callables from functions and methods.

---

## Table of Contents

1. What is First-class Callable Syntax
2. Basic Syntax
3. Function Callables
4. Method Callables
5. Static Method Callables
6. Comparing with Alternatives
7. Type Hints and Callables
8. Real-world Patterns
9. Complete Examples

---

## What is First-class Callable Syntax

### Purpose

```php
<?php
// PHP 8.0 and earlier: Using Closure::fromCallable()
$getLength = Closure::fromCallable('strlen');
echo $getLength('hello');  // 5

// Alternatives before 8.1:
$getLength = fn($str) => strlen($str);
$getLength = 'strlen';  // String callable (deprecated)

// PHP 8.1: First-class callable syntax
$getLength = strlen(...);  // Clean, obvious syntax!
echo $getLength('hello');  // 5

// Benefits:
// ✓ Cleaner syntax than Closure::fromCallable()
// ✓ More explicit than string callables
// ✓ IDE autocomplete support
// ✓ Type checking support
// ✓ Reflection integration
```

### When to Use

```php
<?php
// When you need to pass a function as argument
array_map(strlen(...), ['hello', 'world']);

// When storing callable in variable
$validator = filter_var(...);

// When composing functions
function compose(callable $f, callable $g) {
    return fn($x) => $f($g($x));
}

$compose = compose(strlen(...), trim(...));

// Replaces need for array(object, 'method') syntax
// or Closure::fromCallable()
```

---

## Basic Syntax

### Simple Functions

```php
<?php
// Get callable reference to built-in function
$strLen = strlen(...);
$strPos = strpos(...);
$strReplace = str_replace(...);

// Call like normal function
echo $strLen('hello');  // 5
echo $strPos('hello', 'l');  // 2

// Pass to array functions
$words = ['hello', 'world', 'php'];
$lengths = array_map(strlen(...), $words);
print_r($lengths);  // [5, 5, 3]
```

### User-defined Functions

```php
<?php
// Define a function
function multiply(int $a, int $b): int {
    return $a * $b;
}

// Create callable
$multiplier = multiply(...);

// Use as callback
echo $multiplier(5, 3);  // 15

// Pass to higher-order function
function applyTwice(callable $fn, int $value): int {
    return $fn($fn($value, $value), $value);
}

echo applyTwice($multiplier, 2);  // ((2*2)*2) = 8
```

### Accessing Reflection

```php
<?php
// First-class callables provide reflection
$strlen = strlen(...);

// Get reflection
$reflection = new ReflectionFunction($strlen);
echo $reflection->getName();  // strlen

// Works with user functions too
function myFunction(string $param): string {
    return $param;
}

$callable = myFunction(...);
$reflection = new ReflectionFunction($callable);
// Access parameters, return type, etc.
```

---

## Function Callables

### Built-in Functions

```php
<?php
// Create callables for built-in functions
$strlen = strlen(...);
$strtolower = strtolower(...);
$strtoupper = strtoupper(...);
$trim = trim(...);
$explode_by_comma = fn($str) => explode(',', $str);

// Use in array operations
$words = ['HELLO', ' WORLD ', 'PHP'];

// Convert to lowercase
$lower = array_map(strtolower(...), $words);
print_r($lower);  // [hello, world, php]

// Filter by length
$longWords = array_filter($words, fn($w) => strlen(trim($w)) > 4);

// Map over each item
$trimmed = array_map(trim(...), $words);
```

### User Functions

```php
<?php
// Define function
function greet(string $name): string {
    return "Hello, $name!";
}

// Create callable
$greeter = greet(...);

// Use as callback
echo $greeter('John');  // Hello, John!

// Higher-order usage
function applyN(callable $fn, array $items): array {
    return array_map($fn, $items);
}

$names = ['Alice', 'Bob', 'Charlie'];
$greetings = applyN(greet(...), $names);
print_r($greetings);
// [Hello, Alice!, Hello, Bob!, Hello, Charlie!]
```

### Function Composition

```php
<?php
function add(int $a, int $b): int {
    return $a + $b;
}

function double(int $x): int {
    return $x * 2;
}

function subtract(int $a, int $b): int {
    return $a - $b;
}

// Compose functions
function compose(callable $f, callable $g): callable {
    return fn($x) => $f($g($x));
}

// Create pipelines
$pipeline1 = compose(double(...), add(...));
echo $pipeline1(5, 3);  // double(add(5, 3)) = 16

$pipeline2 = compose(subtract(...), double(...));
// Result: callable that can be used with parameters
```

---

## Method Callables

### Instance Methods

```php
<?php
class Calculator {
    public function add(int $a, int $b): int {
        return $a + $b;
    }
    
    public function multiply(int $a, int $b): int {
        return $a * $b;
    }
}

$calc = new Calculator();

// Create callables from methods
$adder = $calc->add(...);
$multiplier = $calc->multiply(...);

// Use them
echo $adder(5, 3);  // 8
echo $multiplier(5, 3);  // 15

// Alternative before 8.1
// $adder = $calc->add(...);  // This syntax
// vs
// $adder = [$calc, 'add'];  // Old array syntax
```

### Method in Array Functions

```php
<?php
class Processor {
    public function process(string $item): string {
        return strtoupper($item);
    }
    
    public function validate(string $item): bool {
        return strlen($item) > 2;
    }
}

$processor = new Processor();
$items = ['hello', 'a', 'world', 'x', 'php'];

// Use first-class callable syntax
$processed = array_map(
    $processor->process(...),
    $items
);
print_r($processed);
// [HELLO, A, WORLD, X, PHP]

$valid = array_filter(
    $items,
    $processor->validate(...)
);
print_r($valid);
// [hello, world, php]
```

### Method Chains

```php
<?php
class Logger {
    private array $logs = [];
    
    public function log(string $message): self {
        $this->logs[] = $message;
        return $this;
    }
    
    public function getLogs(): array {
        return $this->logs;
    }
}

$logger = new Logger();

// Create callable that logs
$logMessage = $logger->log(...);

// Use in callback
$logMessage('Starting process');
$logMessage('Processing data');
$logMessage('Complete');

print_r($logger->getLogs());
// [Starting process, Processing data, Complete]
```

---

## Static Method Callables

### Static Methods

```php
<?php
class Math {
    public static function add(int $a, int $b): int {
        return $a + $b;
    }
    
    public static function multiply(int $a, int $b): int {
        return $a * $b;
    }
}

// Create callables from static methods
$adder = Math::add(...);
$multiplier = Math::multiply(...);

echo $adder(5, 3);  // 8
echo $multiplier(5, 3);  // 15

// Before 8.1
// $adder = [Math::class, 'add'];  // Array syntax
// $adder = 'Math::add';  // String syntax

// Array callables still work but 8.1 syntax is cleaner
```

### Factory Pattern

```php
<?php
class Logger {
    public static function createFileLogger(string $path): FileLogger {
        return new FileLogger($path);
    }
    
    public static function createConsoleLogger(): ConsoleLogger {
        return new ConsoleLogger();
    }
}

class FileLogger {}
class ConsoleLogger {}

// Use static method callables
$fileLoggerFactory = Logger::createFileLogger(...);
$consoleLoggerFactory = Logger::createConsoleLogger(...);

// Create instances via callable
// Note: Single argument for file path
$logger1 = $fileLoggerFactory('/var/log/app.log');
$logger2 = $consoleLoggerFactory();
```

---

## Comparing with Alternatives

### vs Closure::fromCallable()

```php
<?php
// PHP 8.0 style
$strlen1 = Closure::fromCallable('strlen');

// PHP 8.1 style
$strlen2 = strlen(...);

// Same result, cleaner syntax

// Instance method
class Handler {
    public function handle($data) {}
}

$handler = new Handler();

// PHP 8.0
$callback1 = Closure::fromCallable([$handler, 'handle']);

// PHP 8.1
$callback2 = $handler->handle(...);

// Preference: PHP 8.1 syntax is cleaner and more discoverable
```

### vs Anonymous Functions

```php
<?php
// Anonymous function
$adder1 = fn($a, $b) => add($a, $b);

// First-class callable
$adder2 = add(...);

// First-class is simpler when wrapping existing functions

// Use anonymous functions when:
// - Adding logic/transformation
// - Partial application needed
// - Argument manipulation required

$addFive = fn($x) => add($x, 5);  // Can't do with first-class alone

// Use first-class callable when:
// - Just passing function as-is
// - No transformation needed
```

### vs String Callables

```php
<?php
// Old string callable (deprecated)
$strlen = 'strlen';
echo $strlen('hello');  // 5

// First-class callable (recommended)
$strlen = strlen(...);
echo $strlen('hello');  // 5

// String callables problems:
// ✗ No IDE support
// ✗ String isn't obvious it's callable
// ✗ Deprecated in recent PHP versions

// First-class callables:
// ✓ Clear syntax
// ✓ Full IDE support
// ✓ Type-safe
```

---

## Type Hints and Callables

### Callable Type Hints

```php
<?php
// Type hint any callable
function mapValues(callable $transformer, array $values): array {
    return array_map($transformer, $values);
}

// Can pass any callable type
$result1 = mapValues(strlen(...), ['a', 'bb', 'ccc']);
$result2 = mapValues(strtoupper(...), ['a', 'b', 'c']);

class Transformer {
    public function uppercase(string $value): string {
        return strtoupper($value);
    }
}

$transformer = new Transformer();
$result3 = mapValues($transformer->uppercase(...), ['a', 'b', 'c']);
```

### Callable Types with Union Types

```php
<?php
// Function that accepts function or method callable
function execute(callable $fn): mixed {
    return $fn();
}

// Can be function reference
execute(strlen(...));

// Or method reference
$obj = new class {
    public function method() { return 'result'; }
};
execute($obj->method(...));

// Both work with callable type hint
```

---

## Real-world Patterns

### Array Processing Pipeline

```php
<?php
class Pipeline {
    private array $steps = [];
    
    public function addStep(callable $step): self {
        $this->steps[] = $step;
        return $this;
    }
    
    public function execute($value) {
        foreach ($this->steps as $step) {
            $value = $step($value);
        }
        return $value;
    }
}

// Usage
$pipeline = (new Pipeline())
    ->addStep(trim(...))
    ->addStep(strtolower(...))
    ->addStep(fn($s) => explode(' ', $s))
    ->addStep(fn($arr) => array_map(ucfirst(...), $arr));

$result = $pipeline->execute('  HELLO WORLD  ');
// [Hello, World]
```

### Event Listeners

```php
<?php
class EventDispatcher {
    private array $listeners = [];
    
    public function on(string $event, callable $listener): void {
        $this->listeners[$event][] = $listener;
    }
    
    public function emit(string $event, $data): void {
        foreach ($this->listeners[$event] ?? [] as $listener) {
            $listener($data);
        }
    }
}

class UserService {
    public function __construct(
        private EventDispatcher $dispatcher
    ) {}
    
    public function registerUser(string $email): void {
        // Logic...
        $this->dispatcher->emit('user:registered', $email);
    }
}

// Usage
$dispatcher = new EventDispatcher();

// Add listeners using first-class callables
$dispatcher->on('user:registered', function($email) {
    echo "Welcome email sent to $email\n";
});

$dispatcher->on('user:registered', function($email) {
    echo "Added to newsletter: $email\n";
});

// Also can use existing methods
class Logger {
    public function logRegistration(string $email): void {
        echo "Logged: $email\n";
    }
}

$logger = new Logger();
$dispatcher->on('user:registered', $logger->logRegistration(...));

$service = new UserService($dispatcher);
$service->registerUser('john@example.com');
```

---

## Complete Examples

### Example 1: Data Transformation

```php
<?php
class DataTransformer {
    private array $transformers = [];
    
    public function register(string $type, callable $transformer): self {
        $this->transformers[$type] = $transformer;
        return $this;
    }
    
    public function transform(string $type, $data) {
        if (!isset($this->transformers[$type])) {
            throw new InvalidArgumentException("No transformer for $type");
        }
        
        return $this->transformers[$type]($data);
    }
}

// Define transformers
function jsonToArray(string $json): array {
    return json_decode($json, true);
}

function csvToArray(string $csv): array {
    return array_map('str_getcsv', explode("\n", trim($csv)));
}

class XmlTransformer {
    public function toArray(string $xml): array {
        $data = simplexml_load_string($xml);
        return json_decode(json_encode($data), true);
    }
}

// Setup
$transformer = new DataTransformer();
$transformer
    ->register('json', jsonToArray(...))
    ->register('csv', csvToArray(...));

$xmlTransformer = new XmlTransformer();
$transformer->register('xml', $xmlTransformer->toArray(...));

// Use
$jsonData = '{"name": "John", "age": 30}';
$arrayData = $transformer->transform('json', $jsonData);
print_r($arrayData);
// [name => John, age => 30]
```

### Example 2: Validation Chain

```php
<?php
class Validator {
    private array $rules = [];
    
    public function addRule(string $field, callable $rule): self {
        $this->rules[$field][] = $rule;
        return $this;
    }
    
    public function validate(array $data): array {
        $errors = [];
        
        foreach ($this->rules as $field => $rules) {
            foreach ($rules as $rule) {
                $result = $rule($data[$field] ?? null, $data);
                
                if ($result !== true) {
                    $errors[$field][] = $result;
                }
            }
        }
        
        return $errors;
    }
}

// Validation rules
function notEmpty($value): bool|string {
    return empty($value) ? 'Field is required' : true;
}

function isEmail($value): bool|string {
    return filter_var($value, FILTER_VALIDATE_EMAIL) 
        ? true 
        : 'Invalid email format';
}

function minLength($min) {
    return function($value) use ($min) {
        return strlen($value) < $min 
            ? "Must be at least $min characters" 
            : true;
    };
}

// Setup validator
$validator = new Validator();
$validator
    ->addRule('email', notEmpty(...))
    ->addRule('email', isEmail(...))
    ->addRule('password', notEmpty(...))
    ->addRule('password', minLength(8));

// Validate
$errors = $validator->validate([
    'email' => 'invalid',
    'password' => 'short'
]);

// Would show errors for both fields
```

### Example 3: Caching Decorator

```php
<?php
class CachedFunction {
    private array $cache = [];
    
    public function __construct(
        private callable $function,
        private int $ttl = 3600
    ) {}
    
    public function __invoke(...$args) {
        $key = json_encode($args);
        
        if (isset($this->cache[$key])) {
            $entry = $this->cache[$key];
            if (time() - $entry['time'] < $this->ttl) {
                return $entry['value'];
            }
        }
        
        $result = ($this->function)(...$args);
        
        $this->cache[$key] = [
            'value' => $result,
            'time' => time()
        ];
        
        return $result;
    }
}

// Use
function expensive(string $param): string {
    sleep(2);  // Simulate expensive operation
    return "Result: $param";
}

$cached = new CachedFunction(expensive(...));

echo $cached('test');     // Takes 2 seconds
echo $cached('test');     // Returns from cache immediately
echo $cached('other');    // Different param, takes 2 seconds
```

---

## Key Takeaways

**First-class Callable Syntax Checklist:**

1. ✅ Use `function(...)` for functions
2. ✅ Use `$obj->method(...)` for instance methods
3. ✅ Use `Class::method(...)` for static methods
4. ✅ Prefer over `Closure::fromCallable()`
5. ✅ Prefer over string/array callables
6. ✅ Works with array_map, array_filter, etc.
7. ✅ Enable IDE autocomplete
8. ✅ Type-safe approach

---

## See Also

- [Enumerations](2-enumerations.md)
- [Readonly Properties](3-readonly-properties.md)
- [Never Return Type](6-never-return-type.md)
- [Anonymous Functions (PHP 7.4)](../02-basics-study-case/anonymous-functions.md)
