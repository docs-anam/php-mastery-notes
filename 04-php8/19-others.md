# Other PHP 8 Features

## Overview

PHP 8 introduces several additional features beyond the major ones, including weakmap, nullsafe operator improvements, and other enhancements that improve code quality and performance.

---

## Constructor Promotion Shorthand

```php
<?php
// Traditional
class Point {
    public int $x;
    public int $y;
    
    public function __construct(int $x, int $y) {
        $this->x = $x;
        $this->y = $y;
    }
}

// PHP 8 - promoted properties
class PointNew {
    public function __construct(
        public int $x,
        public int $y
    ) {}
}

$point = new PointNew(10, 20);
echo $point->x; // 10
?>
```

---

## WeakMap Collection

```php
<?php
// WeakMap stores objects without preventing garbage collection
class Cache {
    private WeakMap $cache;
    
    public function __construct() {
        $this->cache = new WeakMap();
    }
    
    public function set(object $key, mixed $value): void {
        $this->cache[$key] = $value;
    }
    
    public function get(object $key): mixed {
        return $this->cache[$key] ?? null;
    }
}

$cache = new Cache();
$object = new stdClass();
$cache->set($object, 'data');

var_dump($cache->get($object)); // 'data'
// When $object is destroyed, it's removed from WeakMap
?>
```

---

## Weak References

```php
<?php
$object = new stdClass();
$weakRef = \WeakReference::create($object);

var_dump($weakRef->get()); // stdClass object

unset($object);
var_dump($weakRef->get()); // null
?>
```

---

## Named Captures in Regex

```php
<?php
$pattern = '/(?P<year>\d{4})-(?P<month>\d{2})-(?P<day>\d{2})/';
$subject = '2024-01-15';

if (preg_match($pattern, $subject, $matches)) {
    echo "Year: " . $matches['year'] . "\n";
    echo "Month: " . $matches['month'] . "\n";
    echo "Day: " . $matches['day'] . "\n";
}
?>
```

---

## fdiv() Function

```php
<?php
// PHP 7 - division by zero issues
$a = 10;
$b = 0;
// $result = $a / $b; // Warning

// PHP 8 - fdiv() handles edge cases
echo fdiv(10, 3); // 3.3333...
echo fdiv(10, 0); // INF
echo fdiv(-10, 0); // -INF
echo fdiv(0, 0); // NAN
?>
```

---

## get_debug_type()

```php
<?php
// Better type information for debugging
var_dump(get_debug_type(new DateTime())); // "DateTime"
var_dump(get_debug_type([])); // "array"
var_dump(get_debug_type(123)); // "int"
var_dump(get_debug_type("hello")); // "string"
var_dump(get_debug_type(null)); // "null"

// Helpful in error messages
function process(mixed $value): void {
    if (!is_string($value)) {
        throw new TypeError("Expected string, got " . get_debug_type($value));
    }
}
?>
```

---

## str_increment()

```php
<?php
// Increment alphanumeric strings (useful for sequences)
// Note: This may not be in all PHP 8 versions

// Practical use: generate next values
$ids = ['id-001', 'id-002'];
// Can be used with custom logic for incrementing
?>
```

---

## Real-World Examples

### 1. Object Caching with WeakMap

```php
<?php
class EntityCache {
    private WeakMap $cache;
    
    public function __construct() {
        $this->cache = new WeakMap();
    }
    
    public function remember(object $entity, callable $loader): mixed {
        if (isset($this->cache[$entity])) {
            return $this->cache[$entity];
        }
        
        $value = $loader($entity);
        $this->cache[$entity] = $value;
        
        return $value;
    }
}

class User {
    public function __construct(public int $id, public string $name) {}
}

$cache = new EntityCache();
$user = new User(1, 'Alice');

$userData = $cache->remember($user, function($u) {
    echo "Loading data for user {$u->id}\n";
    return ['roles' => ['admin', 'user']];
});

print_r($userData);
?>
```

### 2. Debug Type in Error Handling

```php
<?php
class TypeValidator {
    public function validate(mixed $value, string $expectedType): void {
        $actualType = get_debug_type($value);
        
        if ($actualType !== $expectedType) {
            throw new TypeError(
                "Expected $expectedType, but got $actualType with value: " . 
                var_export($value, true)
            );
        }
    }
}

$validator = new TypeValidator();

try {
    $validator->validate("string", "int");
} catch (TypeError $e) {
    echo $e->getMessage();
    // Expected int, but got string with value: 'string'
}
?>
```

### 3. Safe Division

```php
<?php
class Calculator {
    public function divide(float $numerator, float $denominator): float|string {
        $result = fdiv($numerator, $denominator);
        
        return match(true) {
            is_infinite($result) => "Infinity",
            is_nan($result) => "Not a Number",
            default => $result
        };
    }
}

$calc = new Calculator();
echo $calc->divide(10, 3); // 3.3333...
echo $calc->divide(10, 0); // Infinity
echo $calc->divide(0, 0); // Not a Number
?>
```

### 4. Date Parsing with Named Captures

```php
<?php
class DateParser {
    private string $pattern = '/(?P<year>\d{4})-(?P<month>\d{2})-(?P<day>\d{2})/';
    
    public function parse(string $dateString): ?DateTime {
        if (!preg_match($this->pattern, $dateString, $matches)) {
            return null;
        }
        
        return new DateTime(
            "{$matches['year']}-{$matches['month']}-{$matches['day']}"
        );
    }
}

$parser = new DateParser();
$date = $parser->parse('2024-01-15');
echo $date->format('l, F j, Y'); // Monday, January 15, 2024
?>
```

---

## Comparing Old vs New

```php
<?php
// Old way - verbose
class OldService {
    private int $timeout;
    private string $host;
    private int $port;
    
    public function __construct(int $timeout, string $host, int $port) {
        $this->timeout = $timeout;
        $this->host = $host;
        $this->port = $port;
    }
}

// New way - constructor property promotion
class NewService {
    public function __construct(
        private int $timeout,
        private string $host,
        private int $port
    ) {}
}

// Old way - type checking
function oldProcess(mixed $value) {
    $type = gettype($value);
    if ($type === 'object') {
        $type = get_class($value);
    }
}

// New way - better debug info
function newProcess(mixed $value) {
    $type = get_debug_type($value); // More informative
}
?>
```

---

## Best Practices

### 1. Use WeakMap for Object Metadata

```php
<?php
// Store metadata without keeping objects alive
class MetadataStore {
    private WeakMap $metadata;
    
    public function __construct() {
        $this->metadata = new WeakMap();
    }
    
    public function set(object $object, array $meta): void {
        $this->metadata[$object] = $meta;
    }
    
    public function get(object $object): ?array {
        return $this->metadata[$object] ?? null;
    }
}
?>
```

### 2. Use get_debug_type() for Errors

```php
<?php
function safeCast(mixed $value, string $type): mixed {
    if (!$this->isType($value, $type)) {
        throw new TypeError(
            "Cannot cast " . get_debug_type($value) . " to $type"
        );
    }
    return match($type) {
        'int' => (int)$value,
        'float' => (float)$value,
        'string' => (string)$value,
        default => $value
    };
}
?>
```

### 3. Use fdiv() for Safe Math

```php
<?php
function calculateAverage(array $values): float|string {
    if (empty($values)) {
        return "No values";
    }
    
    $sum = array_sum($values);
    $count = count($values);
    
    $average = fdiv($sum, $count);
    
    return is_finite($average) ? $average : "Invalid result";
}
?>
```

---

## Complete Example

```php
<?php
class DataProcessor {
    private WeakMap $cache;
    
    public function __construct() {
        $this->cache = new WeakMap();
    }
    
    public function process(object $data): array {
        // Check cache without keeping object alive
        if (isset($this->cache[$data])) {
            return $this->cache[$data];
        }
        
        // Process data
        $result = match(get_debug_type($data)) {
            'DateTime' => $this->processDateTime($data),
            'stdClass' => $this->processObject($data),
            default => throw new TypeError(
                "Cannot process " . get_debug_type($data)
            )
        };
        
        // Cache result
        $this->cache[$data] = $result;
        
        return $result;
    }
    
    private function processDateTime(DateTime $dt): array {
        return [
            'date' => $dt->format('Y-m-d'),
            'time' => $dt->format('H:i:s'),
            'timestamp' => $dt->getTimestamp()
        ];
    }
    
    private function processObject(stdClass $obj): array {
        return get_object_vars($obj);
    }
}

// Usage
$processor = new DataProcessor();
$date = new DateTime('2024-01-15 10:30:00');
$result = $processor->process($date);
print_r($result);
?>
```

---

## See Also

- Documentation: [PHP 8.0 Features](https://www.php.net/releases/8.0/)
- Related: [Named Arguments](2-named-argument.md), [Union Types](5-union-types.md)
