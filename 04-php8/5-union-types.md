# Union Types

## Overview

Union types allow declaring that a parameter or return value can accept multiple different types, providing better type safety and documentation while allowing flexibility.

---

## Basic Union Types

```php
<?php
function process(int|string $value): int|string {
    if (is_int($value)) {
        return $value * 2;
    }
    return strtoupper($value);
}

echo process(5); // 10
echo process("hello"); // HELLO
?>
```

---

## With Nullable Types

```php
<?php
function getConfig(string $key): string|int|null {
    $config = ['port' => 3306, 'host' => 'localhost'];
    return $config[$key] ?? null;
}

var_dump(getConfig('port')); // int: 3306
var_dump(getConfig('host')); // string: localhost
var_dump(getConfig('missing')); // null
?>
```

---

## Multiple Types

```php
<?php
class DataProcessor {
    public function handle(
        int|float|string $data,
        bool|string $options = false
    ): array|null {
        if ($data === null) {
            return null;
        }
        
        return [
            'processed' => $data,
            'options' => $options
        ];
    }
}
?>
```

---

## In Class Properties

```php
<?php
class Configuration {
    public int|string $port;
    public string|null $host;
    public array|string $databases;
    
    public function __construct() {
        $this->port = 3306;
        $this->host = "localhost";
        $this->databases = ["main" => "primary"];
    }
}

$config = new Configuration();
echo $config->port; // 3306
?>
```

---

## With Objects

```php
<?php
class Logger {
    public function log(string|Exception $message): void {
        if ($message instanceof Exception) {
            echo "Error: " . $message->getMessage();
        } else {
            echo "Log: " . $message;
        }
    }
}

$logger = new Logger();
$logger->log("All good");
$logger->log(new Exception("Something failed"));
?>
```

---

## Type Checking with Union Types

```php
<?php
function getLength(string|array|int $data): int {
    if (is_string($data)) {
        return strlen($data);
    } elseif (is_array($data)) {
        return count($data);
    } else {
        return $data;
    }
}

echo getLength("hello"); // 5
echo getLength([1, 2, 3]); // 3
echo getLength(10); // 10
?>
```

---

## Use Cases

### 1. API Response Handling

```php
<?php
class APIClient {
    public function fetch(string $url): string|array|null {
        try {
            $response = file_get_contents($url);
            
            $decoded = json_decode($response, true);
            if ($decoded !== null) {
                return $decoded; // array
            }
            
            return $response; // string
        } catch (Exception $e) {
            return null; // null on error
        }
    }
}
?>
```

### 2. Flexible Parameter Handling

```php
<?php
class DateHelper {
    public function format(
        string|int|DateTime $date,
        string $format = 'Y-m-d'
    ): string {
        if ($date instanceof DateTime) {
            return $date->format($format);
        }
        
        if (is_int($date)) {
            return date($format, $date);
        }
        
        return date($format, strtotime($date));
    }
}

$helper = new DateHelper();
echo $helper->format("2024-01-15");
echo $helper->format(time());
echo $helper->format(new DateTime());
?>
```

### 3. Configuration Values

```php
<?php
class Config {
    private array $values = [];
    
    public function get(string $key): string|int|float|bool|array|null {
        return $this->values[$key] ?? null;
    }
    
    public function set(
        string $key,
        string|int|float|bool|array $value
    ): void {
        $this->values[$key] = $value;
    }
}

$config = new Config();
$config->set('debug', true);
$config->set('port', 8080);
$config->set('name', 'MyApp');
?>
```

---

## Best Practices

### 1. Limit Union Types

```php
<?php
// ✅ Good - 2-3 related types
function process(int|string $value): int|string {}

// ❌ Avoid - too many unrelated types
function handle(
    int|string|float|bool|array|object|null $data
): mixed {}
?>
```

### 2. Use Type Checking

```php
<?php
function getSize(string|array $data): int {
    // Check type explicitly
    if (is_array($data)) {
        return count($data);
    }
    
    return strlen($data);
}
?>
```

### 3. Document Intent

```php
<?php
/**
 * Fetch configuration value
 *
 * Returns string for text configs, int for numeric configs,
 * or null if key doesn't exist
 */
public function get(string $key): string|int|null {
    return $this->config[$key] ?? null;
}
?>
```

---

## Common Mistakes

### 1. Wrong Order

```php
<?php
// ❌ Order doesn't matter but be consistent
function test(): int|string {}
function other(): string|int {}

// ✅ Better - same order
function test(): int|string {}
function other(): int|string {}
?>
```

### 2. Forgetting Type Checking

```php
<?php
// ❌ Wrong - assuming type without checking
function calculate(int|float $value): int {
    return $value * 2; // May fail if null
}

// ✅ Correct
function calculate(int|float|null $value): int|null {
    if ($value === null) {
        return null;
    }
    return $value * 2;
}
?>
```

### 3. Overcomplicating

```php
<?php
// ❌ Too many types
function process(
    int|string|float|bool|array $data
): int|string|float|bool|array|null {}

// ✅ Simpler - use mixed if needed
function process(mixed $data): mixed {
    // Process logic
    return $data;
}
?>
```

---

## Complete Example

```php
<?php
class FormValidator {
    private array $errors = [];
    
    public function validate(
        string $field,
        mixed $value,
        string|array $rules
    ): bool {
        $ruleList = is_string($rules) ? explode('|', $rules) : $rules;
        
        foreach ($ruleList as $rule) {
            if (!$this->checkRule($field, $value, $rule)) {
                $this->errors[$field] = "Validation failed for rule: $rule";
                return false;
            }
        }
        
        return true;
    }
    
    private function checkRule(
        string $field,
        int|string|array|null $value,
        string $rule
    ): bool {
        return match($rule) {
            'required' => !empty($value),
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL),
            'numeric' => is_numeric($value),
            'array' => is_array($value),
            'string' => is_string($value),
            default => true,
        };
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
}

$validator = new FormValidator();
$validator->validate('email', 'user@example.com', 'required|email');
$validator->validate('age', 25, 'numeric');
?>
```

---

## See Also

- Documentation: [Union Types](https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.union)
- Related: [Named Arguments](2-named-argument.md), [Match Expression](6-match-expression.md)
