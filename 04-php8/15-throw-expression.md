# Throw Expression

## Overview

The throw keyword can now be used as an expression in addition to being a statement, allowing throwing exceptions in ternary operators, arrow functions, and other expression contexts.

---

## Basic Throw Expression

```php
<?php
// PHP 7 - only statement context
if ($id <= 0) {
    throw new InvalidArgumentException('ID must be positive');
}

// PHP 8 - expression context
$id = $id > 0 ? $id : throw new InvalidArgumentException('ID must be positive');
?>
```

---

## In Ternary Operator

```php
<?php
function getUser($id) {
    $user = $id > 0 ? $this->findUser($id) : throw new InvalidArgumentException('Invalid ID');
    return $user;
}

// With null coalescing
$value = $data['key'] ?? throw new InvalidArgumentException('Key missing');

// Conditional throw
$result = $condition ? $value : throw new RuntimeException('Condition failed');
?>
```

---

## In Arrow Functions

```php
<?php
class Processor {
    public function validate(array $items) {
        // Throw in arrow function
        array_walk($items, fn($item) => 
            $item > 0 ?: throw new InvalidArgumentException('Value must be positive')
        );
    }
    
    public function map(array $data) {
        // Throw in map transformation
        return array_map(
            fn($item) => $item['id'] ?? throw new InvalidArgumentException('Missing ID'),
            $data
        );
    }
}
?>
```

---

## In Match Expression

```php
<?php
$status = match($code) {
    200, 201 => 'success',
    400 => throw new BadRequestException(),
    404 => throw new NotFoundException(),
    500 => throw new ServerException(),
    default => throw new UnknownStatusException()
};
?>
```

---

## In Null Coalescing

```php
<?php
class Config {
    public function get(string $key): mixed {
        // Return config or throw if missing and required
        return $this->config[$key] ?? throw new ConfigurationException("Missing key: $key");
    }
}

$dbHost = $config->get('database.host');
$userId = $user['id'] ?? throw new InvalidArgumentException('User ID required');
?>
```

---

## In Method Chaining

```php
<?php
class QueryBuilder {
    private ?string $table = null;
    
    public function from(string $table): self {
        $this->table = $table ?? throw new InvalidArgumentException('Table name required');
        return $this;
    }
    
    public function build(): string {
        return $this->table ?? throw new RuntimeException('Table not set');
    }
}

$query = (new QueryBuilder())
    ->from($tableName ?? throw new InvalidArgumentException('No table'))
    ->build();
?>
```

---

## Real-World Examples

### 1. Type Validation

```php
<?php
function processData(mixed $data): array {
    return match(true) {
        is_array($data) => $data,
        is_string($data) => json_decode($data, true) ?? throw new InvalidArgumentException('Invalid JSON'),
        $data instanceof Traversable => iterator_to_array($data),
        default => throw new InvalidArgumentException('Unsupported data type')
    };
}
?>
```

### 2. Value Validation

```php
<?php
class User {
    public function __construct(
        public int $id,
        public string $email,
        public int $age
    ) {
        if ($id <= 0) throw new InvalidArgumentException('ID must be positive');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Invalid email');
        if ($age < 18) throw new InvalidArgumentException('Must be 18+');
    }
}

// Simplified with throw expression
class UserSimplified {
    public function __construct(
        public int $id,
        public string $email,
        public int $age
    ) {
        $id > 0 ?: throw new InvalidArgumentException('ID must be positive');
        filter_var($email, FILTER_VALIDATE_EMAIL) ?: throw new InvalidArgumentException('Invalid email');
        $age >= 18 ?: throw new InvalidArgumentException('Must be 18+');
    }
}
?>
```

### 3. Collection Operations

```php
<?php
class Collection {
    public function __construct(private array $items = []) {}
    
    public function first() {
        return $this->items[0] ?? throw new RuntimeException('Collection is empty');
    }
    
    public function last() {
        return end($this->items) ?: throw new RuntimeException('Collection is empty');
    }
    
    public function findByKey(string $key) {
        return $this->items[$key] ?? throw new OutOfBoundsException("Key '$key' not found");
    }
}
?>
```

### 4. Configuration Loading

```php
<?php
class ConfigLoader {
    public function load(string $file): array {
        return match(pathinfo($file, PATHINFO_EXTENSION)) {
            'json' => json_decode(
                file_get_contents($file) ?: throw new RuntimeException("Cannot read $file"),
                true
            ) ?? throw new RuntimeException("Invalid JSON in $file"),
            'yaml' => yaml_parse_file($file) ?: throw new RuntimeException("Invalid YAML in $file"),
            'php' => include($file) ?: throw new RuntimeException("Invalid PHP in $file"),
            default => throw new RuntimeException("Unsupported format: $file")
        };
    }
}
?>
```

### 5. Factory Pattern

```php
<?php
class ServiceFactory {
    private array $services = [];
    
    public function get(string $name) {
        return $this->services[$name] ?? throw new ServiceNotFoundException("Service '$name' not registered");
    }
    
    public function create(string $className) {
        $reflection = new ReflectionClass($className);
        
        return $reflection->isInstantiable()
            ? new $className()
            : throw new RuntimeException("Cannot instantiate $className");
    }
}
?>
```

---

## Best Practices

### 1. Use for Input Validation

```php
<?php
// ✅ Good - clear and concise validation
function createUser(array $data): User {
    return new User(
        id: isset($data['id']) && $data['id'] > 0 
            ? $data['id'] 
            : throw new InvalidArgumentException('Invalid ID'),
        email: filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)
            ?: throw new InvalidArgumentException('Invalid email'),
        age: is_int($data['age'] ?? null) && $data['age'] >= 18
            ? $data['age']
            : throw new InvalidArgumentException('Age must be 18+')
    );
}
?>
```

### 2. With Type Checking

```php
<?php
// ✅ Good - type-safe throw
function getValue(mixed $data, string $key) {
    return is_array($data)
        ? $data[$key] ?? throw new KeyError("Key '$key' missing")
        : throw new TypeError('Data must be array');
}
?>
```

### 3. Meaningful Error Messages

```php
<?php
// ✅ Good - informative error
$port = $data['port'] ?? throw new ConfigException(
    'Port not configured. Check your .env file and ensure PORT is set'
);

// ❌ Avoid - vague error
$port = $data['port'] ?? throw new ConfigException('Missing port');
?>
```

---

## Common Patterns

### 1. Required Configuration

```php
<?php
class Environment {
    public static function get(string $name): string {
        return $_ENV[$name] ?? throw new RuntimeException(
            "Environment variable '$name' not set"
        );
    }
}

$apiKey = Environment::get('API_KEY');
$dbHost = Environment::get('DATABASE_HOST');
?>
```

### 2. Safe Extraction

```php
<?php
function extract(array $data, string $key, mixed $default = null) {
    if (array_key_exists($key, $data)) {
        return $data[$key];
    }
    
    return $default ?? throw new KeyError("Required key '$key' not found");
}
?>
```

### 3. Chain Operations

```php
<?php
$result = process($input)
    |> $isValid ? $_ : throw new ValidationException()
    |> transform($_)
    |> $_ ?? throw new ProcessingException();
?>
```

---

## Complete Example

```php
<?php
class PaymentValidator {
    public function validate(array $payment): PaymentData {
        return new PaymentData(
            amount: $this->validateAmount($payment['amount'] ?? null),
            currency: $this->validateCurrency($payment['currency'] ?? null),
            method: $this->validateMethod($payment['method'] ?? null),
            cardToken: $this->validateCardToken($payment['cardToken'] ?? null),
        );
    }
    
    private function validateAmount(mixed $amount): float {
        return is_numeric($amount) && $amount > 0
            ? (float)$amount
            : throw new InvalidArgumentException('Amount must be positive number');
    }
    
    private function validateCurrency(mixed $currency): string {
        $valid = ['USD', 'EUR', 'GBP'];
        return in_array($currency, $valid)
            ? $currency
            : throw new InvalidArgumentException('Invalid currency: ' . $currency);
    }
    
    private function validateMethod(mixed $method): string {
        $valid = ['card', 'bank', 'wallet'];
        return in_array($method, $valid)
            ? $method
            : throw new InvalidArgumentException('Invalid payment method');
    }
    
    private function validateCardToken(mixed $token): string {
        return is_string($token) && strlen($token) > 0
            ? $token
            : throw new InvalidArgumentException('Card token required');
    }
}

class PaymentData {
    public function __construct(
        public float $amount,
        public string $currency,
        public string $method,
        public string $cardToken
    ) {}
}

// Usage
try {
    $validator = new PaymentValidator();
    $payment = $validator->validate([
        'amount' => 99.99,
        'currency' => 'USD',
        'method' => 'card',
        'cardToken' => 'tok_visa'
    ]);
    
    echo "Payment valid: $payment->amount $payment->currency";
} catch (InvalidArgumentException $e) {
    echo "Validation error: " . $e->getMessage();
}
?>
```

---

## See Also

- Documentation: [Throw Expression](https://www.php.net/manual/en/language.exceptions.php)
- Related: [Exception Handling](../03-oop/38-exception.md), [Match Expression](6-match-expression.md)
