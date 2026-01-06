# Mixed Type (Version 2)

## Overview

PHP 8 introduces `mixed` type hint to explicitly indicate a parameter or return value can be of any type, providing better documentation and type safety while allowing flexibility.

---

## Basic Mixed Type

```php
<?php
function process(mixed $data): mixed {
    if (is_array($data)) {
        return count($data);
    } elseif (is_string($data)) {
        return strlen($data);
    }
    return $data;
}

echo process([1, 2, 3]); // 3
echo process("hello"); // 5
echo process(42); // 42
?>
```

---

## Mixed vs No Type Hint

```php
<?php
// ❌ No type hint - unclear intent
function oldWay($data) {
    return process($data);
}

// ✅ Explicit mixed - clear intent
function newWay(mixed $data): mixed {
    return process($data);
}
?>
```

---

## In Parameters

```php
<?php
class DataHandler {
    public function handle(mixed $input, string $format = 'json'): array {
        return match($format) {
            'json' => json_encode($input),
            'csv' => (array)$input,
            'xml' => $this->toXml($input),
            default => [(string)$input]
        };
    }
    
    private function toXml(mixed $data): array {
        return ['<root>' . var_export($data, true) . '</root>'];
    }
}
?>
```

---

## In Return Types

```php
<?php
function getValue(string $key): mixed {
    $data = [
        'count' => 42,
        'name' => 'John',
        'active' => true,
        'tags' => ['php', 'web'],
        'config' => null
    ];
    
    return $data[$key] ?? null;
}

var_dump(getValue('count')); // int
var_dump(getValue('name')); // string
var_dump(getValue('tags')); // array
?>
```

---

## Type Checking with Mixed

```php
<?php
function process(mixed $value): void {
    match(true) {
        is_int($value) => echo "Integer: $value\n",
        is_string($value) => echo "String: $value\n",
        is_array($value) => echo "Array with " . count($value) . " items\n",
        is_bool($value) => echo "Boolean: " . ($value ? 'true' : 'false') . "\n",
        $value === null => echo "Null value\n",
        default => echo "Other type\n"
    };
}

process(42);
process("hello");
process([1, 2, 3]);
process(true);
?>
```

---

## Real-World Examples

### 1. API Responses

```php
<?php
class APIResponse {
    public function __construct(
        public int $statusCode,
        public mixed $data,
        public array $headers = []
    ) {}
    
    public function json(): string {
        return json_encode([
            'status' => $this->statusCode,
            'data' => $this->data
        ]);
    }
}

$response = new APIResponse(200, ['user_id' => 123, 'name' => 'John']);
echo $response->json();
?>
```

### 2. Cache Storage

```php
<?php
class Cache {
    private array $storage = [];
    
    public function set(string $key, mixed $value, int $ttl = 3600): void {
        $this->storage[$key] = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
    }
    
    public function get(string $key): mixed {
        if (!isset($this->storage[$key])) {
            return null;
        }
        
        $item = $this->storage[$key];
        if ($item['expires'] < time()) {
            unset($this->storage[$key]);
            return null;
        }
        
        return $item['value'];
    }
}

$cache = new Cache();
$cache->set('user:123', ['id' => 123, 'name' => 'John']);
$cache->set('counter', 42);
$cache->set('active', true);

var_dump($cache->get('user:123'));
?>
```

### 3. Configuration Manager

```php
<?php
class Config {
    private array $config = [];
    
    public function set(string $key, mixed $value): void {
        $this->config[$key] = $value;
    }
    
    public function get(string $key, mixed $default = null): mixed {
        return $this->config[$key] ?? $default;
    }
    
    public function all(): array {
        return $this->config;
    }
}

$config = new Config();
$config->set('app_name', 'MyApp');
$config->set('debug', true);
$config->set('port', 8080);
$config->set('databases', ['mysql' => [...], 'redis' => [...]]);

echo $config->get('app_name'); // MyApp
echo $config->get('debug'); // 1
?>
```

### 4. Form Validation

```php
<?php
class FormValidator {
    public function validate(string $field, mixed $value, string $rule): bool {
        return match($rule) {
            'required' => !empty($value),
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL),
            'numeric' => is_numeric($value),
            'array' => is_array($value),
            'boolean' => is_bool($value) || in_array($value, [0, 1, '0', '1'], true),
            'min' => is_numeric($value) && $value >= 0,
            'max' => is_numeric($value) && $value <= 100,
            default => true
        };
    }
}

$validator = new FormValidator();
var_dump($validator->validate('age', 25, 'numeric')); // true
var_dump($validator->validate('email', 'user@example.com', 'email')); // true
?>
```

---

## Best Practices

### 1. Document Mixed Parameters

```php
<?php
/**
 * Process data
 *
 * @param mixed $data Can be array, string, object, or scalar value
 * @param string $format Output format (json, xml, csv)
 * @return mixed Processed data in requested format
 */
function process(mixed $data, string $format = 'json'): mixed {
    // Implementation
    return $data;
}
?>
```

### 2. Provide Type Checking Hints

```php
<?php
function handle(mixed $value): void {
    // Document what types are accepted
    if (is_array($value)) {
        // Handle array
    } elseif (is_string($value)) {
        // Handle string
    } elseif (is_numeric($value)) {
        // Handle number
    } else {
        throw new InvalidArgumentException("Unsupported type");
    }
}
?>
```

### 3. Use Type Guards Early

```php
<?php
function convert(mixed $value, string $targetType): mixed {
    return match($targetType) {
        'int' => (int)$value,
        'float' => (float)$value,
        'string' => (string)$value,
        'bool' => (bool)$value,
        'array' => (array)$value,
        default => throw new InvalidArgumentException("Unknown type: $targetType")
    };
}
?>
```

---

## Common Mistakes

### 1. Over-Using Mixed

```php
<?php
// ❌ Too broad - all parameters mixed
class Service {
    public function process(mixed $a, mixed $b, mixed $c): mixed {
        // What types are expected?
    }
}

// ✅ Better - specific where possible
class Service {
    public function process(int|string $input, array $config, ?string $callback = null): mixed {
        // Clearer intent
    }
}
?>
```

### 2. Forgetting Type Checks

```php
<?php
// ❌ Wrong - assuming type
function getLength(mixed $value): int {
    return strlen($value); // Fails if not string
}

// ✅ Correct - check type
function getLength(mixed $value): int {
    if (is_array($value)) {
        return count($value);
    }
    return strlen((string)$value);
}
?>
```

### 3. Not Documenting Intent

```php
<?php
// ❌ No indication of expected types
public function handle(mixed $data): mixed {}

// ✅ Clear documentation
/**
 * Process various data types
 * 
 * @param mixed $data String (JSON), array, or object
 * @return mixed Processed result
 */
public function handle(mixed $data): mixed {}
?>
```

---

## Complete Example

```php
<?php
class DataProcessor {
    public function process(mixed $input, array $options = []): mixed {
        // Normalize input
        $data = $this->normalize($input);
        
        // Apply transformations
        if ($options['uppercase'] ?? false) {
            $data = $this->toUppercase($data);
        }
        
        if ($options['trim'] ?? false) {
            $data = $this->trim($data);
        }
        
        return $data;
    }
    
    private function normalize(mixed $data): mixed {
        return match(true) {
            is_string($data) => trim($data),
            is_array($data) => array_map(fn($v) => $this->normalize($v), $data),
            is_numeric($data) => (float)$data,
            is_bool($data) => $data,
            $data === null => null,
            is_object($data) => get_object_vars($data),
            default => $data
        };
    }
    
    private function toUppercase(mixed $data): mixed {
        return match(true) {
            is_string($data) => strtoupper($data),
            is_array($data) => array_map(fn($v) => $this->toUppercase($v), $data),
            default => $data
        };
    }
    
    private function trim(mixed $data): mixed {
        if (is_string($data)) {
            return trim($data);
        } elseif (is_array($data)) {
            return array_map(fn($v) => $this->trim($v), $data);
        }
        return $data;
    }
}

$processor = new DataProcessor();
echo $processor->process("hello", ['uppercase' => true]);
print_r($processor->process(['hello', 'world'], ['trim' => true]));
?>
```

---

## See Also

- Documentation: [Mixed Type](https://www.php.net/manual/en/language.types.mixed.php)
- Related: [Union Types](5-union-types.md), [Type Declarations](../01-basics/12-operators-comparison.md)
