# Consistent Type Errors

## Overview

PHP 8 introduces consistent type error handling, throwing TypeError for invalid type arguments in both built-in functions and user-defined functions, providing more predictable error behavior.

---

## Type Errors in Built-in Functions

```php
<?php
// PHP 8 - TypeError exceptions

// Wrong type for built-in function
strlen([]); // TypeError: strlen() expects string, array given

// Wrong type for function parameter
array_slice([1, 2, 3], "offset"); // TypeError: array_slice() offset must be int, string given

// Invalid type
json_decode(123); // Works: converts to string first
preg_match([], "text"); // TypeError: preg_match() pattern must be string
?>
```

---

## User-Defined Function Errors

```php
<?php
function processData(int $count, string $name): void {
    echo "Count: $count, Name: $name\n";
}

// These throw TypeError
processData("invalid", 123); // TypeError: processData() expects int, string given

processData(10, "valid"); // OK
?>
```

---

## Type Coercion vs Errors

```php
<?php
// Strict type checking in PHP 8
declare(strict_types=1);

function add(int $a, int $b): int {
    return $a + $b;
}

add(5, 10); // OK: 15
add(5, "10"); // TypeError: expects int, string given

// Without strict_types, coercion happens
add(5, "10"); // Coerces "10" to 10
?>
```

---

## Catching Type Errors

```php
<?php
function safeFunctionCall(callable $fn, array $args = []): mixed {
    try {
        return $fn(...$args);
    } catch (TypeError $e) {
        echo "Type error caught: " . $e->getMessage() . "\n";
        return null;
    }
}

function greet(string $name): string {
    return "Hello, $name!";
}

safeFunctionCall('greet', ['John']); // OK
safeFunctionCall('greet', [123]); // Type error caught
?>
```

---

## Common Type Error Scenarios

### 1. Array Functions

```php
<?php
$data = ['a', 'b', 'c'];

// ❌ TypeError - offset must be int
array_slice($data, "0");

// ✅ Correct
array_slice($data, 0);

// ❌ TypeError - length must be int
array_splice($data, 0, "2");

// ✅ Correct
array_splice($data, 0, 2);
?>
```

### 2. String Functions

```php
<?php
// ❌ TypeError - expects string
strlen([1, 2, 3]);

// ✅ Correct
strlen("hello");

// ❌ TypeError - haystack must be string
strpos(123, "3");

// ✅ Correct
strpos("123", "3");
?>
```

### 3. File Functions

```php
<?php
// ❌ TypeError - expects string or stream
fopen(123, "r");

// ✅ Correct
fopen("file.txt", "r");
?>
```

### 4. Type Declaration Errors

```php
<?php
class Repository {
    public function save(array $data): bool {
        // Parameter type enforced
        return true;
    }
}

$repo = new Repository();

$repo->save(['id' => 1]); // OK
$repo->save("invalid"); // TypeError: expects array, string given
?>
```

---

## Real-World Examples

### 1. API Request Validation

```php
<?php
class APIHandler {
    public function handle(string $method, string $endpoint, array $data = []): array {
        // Type errors are thrown automatically if wrong types provided
        return [
            'method' => strtoupper($method),
            'endpoint' => $endpoint,
            'data' => $data
        ];
    }
}

$handler = new APIHandler();

try {
    // ✅ Valid
    $result = $handler->handle('GET', '/api/users');
    
    // ❌ TypeError - method must be string
    $result = $handler->handle(123, '/api/users');
} catch (TypeError $e) {
    echo "Invalid request: " . $e->getMessage();
}
?>
```

### 2. Database Query Builder

```php
<?php
class QueryBuilder {
    private array $conditions = [];
    
    public function where(string $column, string $operator, mixed $value): self {
        // Column and operator must be strings
        $this->conditions[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        return $this;
    }
    
    public function limit(int $limit): self {
        // Limit must be int
        $this->conditions['limit'] = $limit;
        return $this;
    }
}

$query = new QueryBuilder();

// ✅ Valid
$query->where('age', '>', 18)->limit(10);

// ❌ TypeError - limit expects int
try {
    $query->limit("10");
} catch (TypeError $e) {
    echo "Invalid limit type\n";
}
?>
```

### 3. Strict Type Processing

```php
<?php
declare(strict_types=1);

class PaymentProcessor {
    public function process(float $amount, string $currency, int $userId): bool {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Amount must be positive");
        }
        
        echo "Processing: $amount $currency for user $userId\n";
        return true;
    }
}

$processor = new PaymentProcessor();

// ✅ All correct types
$processor->process(99.99, "USD", 123);

// ❌ TypeError - userId must be int
try {
    $processor->process(99.99, "USD", "123");
} catch (TypeError $e) {
    echo "Type error: " . $e->getMessage();
}
?>
```

---

## Handling Type Errors Gracefully

```php
<?php
class Service {
    public function processData(array $data, int $timeout = 30): array {
        // Implementation
        return ['processed' => true];
    }
    
    public function safeProcess(mixed $data, mixed $timeout = null): array {
        try {
            // Ensure correct types
            if (!is_array($data)) {
                throw new TypeError("Data must be an array");
            }
            
            $timeout = $timeout !== null ? (int)$timeout : 30;
            
            return $this->processData($data, $timeout);
        } catch (TypeError $e) {
            return ['error' => $e->getMessage(), 'processed' => false];
        }
    }
}

$service = new Service();

// Safe - handles type errors
$result = $service->safeProcess("invalid");
print_r($result); // Shows error
?>
```

---

## Best Practices

### 1. Use Type Declarations

```php
<?php
// ✅ Good - clear type expectations
function process(string $name, int $age, array $data): bool {
    return true;
}

// ❌ Avoid - no type hints
function process($name, $age, $data) {
    return true;
}
?>
```

### 2. Enable Strict Types

```php
<?php
declare(strict_types=1);

// Enforces strict type checking at the function boundary
// Prevents automatic type coercion
?>
```

### 3. Document Expected Types

```php
<?php
/**
 * Process user data
 *
 * @param array $data User data array with 'name' and 'email'
 * @param int $status User status (1=active, 0=inactive)
 * @return bool Success status
 * @throws TypeError If data types don't match
 */
function processUser(array $data, int $status): bool {
    return true;
}
?>
```

### 4. Catch and Log Type Errors

```php
<?php
class ErrorHandler {
    public function handleError(Throwable $e): void {
        if ($e instanceof TypeError) {
            echo "Type error (invalid argument): " . $e->getMessage();
        } else {
            echo "Other error: " . $e->getMessage();
        }
    }
}
?>
```

---

## Common Mistakes

### 1. Forgetting Type Declarations

```php
<?php
// ❌ No type checking happens
function process($data) {
    strlen($data); // No error if $data is array
}

// ✅ Type checking enforced
function process(string $data): void {
    strlen($data); // Always string
}
?>
```

### 2. Not Handling Type Errors

```php
<?php
// ❌ Unhandled TypeError crashes application
function setup(array $config) {
    // ...
}

setup("invalid"); // Fatal error

// ✅ Handle gracefully
try {
    setup("invalid");
} catch (TypeError $e) {
    echo "Configuration error: " . $e->getMessage();
}
?>
```

### 3. Trusting External Input

```php
<?php
// ❌ User input can cause TypeError
$_GET['count'] = "abc";

function display(int $count): void {
    echo "Showing $count items\n";
}

display((int)$_GET['count']); // OK
display($_GET['count']); // TypeError

// ✅ Validate and convert
$count = (int)($_GET['count'] ?? 0);
if ($count < 0) {
    $count = 0;
}
display($count);
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class DataProcessor {
    public function process(array $data, string $format = 'json'): string {
        // Type errors thrown automatically for wrong types
        
        if ($format === 'json') {
            return json_encode($data);
        } elseif ($format === 'csv') {
            return $this->convertToCSV($data);
        } else {
            throw new InvalidArgumentException("Unknown format: $format");
        }
    }
    
    private function convertToCSV(array $data): string {
        $lines = [];
        foreach ($data as $row) {
            $lines[] = implode(',', array_map('escapeCsvField', (array)$row));
        }
        return implode("\n", $lines);
    }
    
    public function validate(array $data, array $rules): array {
        // Rules: [field => type] where type is 'string', 'int', 'float', 'bool', 'array'
        $errors = [];
        
        foreach ($rules as $field => $type) {
            if (!isset($data[$field])) {
                $errors[$field] = "Required field missing";
                continue;
            }
            
            $value = $data[$field];
            $actualType = gettype($value);
            
            if ($this->getTypeName($actualType) !== $type) {
                $errors[$field] = "Expected $type, got $actualType";
            }
        }
        
        return $errors;
    }
    
    private function getTypeName(string $phpType): string {
        return match($phpType) {
            'integer' => 'int',
            'double' => 'float',
            'boolean' => 'bool',
            default => $phpType
        };
    }
}

// Usage
$processor = new DataProcessor();

// ✅ Correct types
$result = $processor->process(['name' => 'John'], 'json');
echo $result;

// ❌ TypeError - format must be string
try {
    $processor->process(['name' => 'John'], 123);
} catch (TypeError $e) {
    echo "Error: " . $e->getMessage();
}

// Validation
$errors = $processor->validate(
    ['name' => 'John', 'age' => 25],
    ['name' => 'string', 'age' => 'int']
);
print_r($errors); // Empty if valid
?>
```

---

## See Also

- Documentation: [Type Declarations](https://www.php.net/manual/en/language.types.declarations.php)
- Related: [Union Types](5-union-types.md), [Named Arguments](2-named-argument.md)
