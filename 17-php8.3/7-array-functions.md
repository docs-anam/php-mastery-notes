# Array Functions

## Overview

Learn about new and improved array functions in PHP 8.3, including array_is_list() and enhancements to existing array operations.

---

## Table of Contents

1. New Array Functions
2. array_is_list()
3. Array Unpacking Improvements
4. Type Improvements
5. Performance Enhancements
6. Practical Examples
7. Best Practices
8. Complete Examples

---

## New Array Functions in PHP 8.3

### array_is_list()

```php
<?php
// NEW in PHP 8.3: Check if array is a list
$list = [1, 2, 3, 4, 5];
var_dump(array_is_list($list));  // true

$associative = ['a' => 1, 'b' => 2];
var_dump(array_is_list($associative));  // false

$mixed = [0 => 'a', 'key' => 'b', 1 => 'c'];
var_dump(array_is_list($mixed));  // false

// List requirements:
// ✓ Keys are sequential integers starting from 0
// ✓ No gaps in sequence
// ✓ Keys are in order

$validList = [0 => 'a', 1 => 'b', 2 => 'c'];
var_dump(array_is_list($validList));  // true

$invalidList = [0 => 'a', 2 => 'b', 1 => 'c'];
var_dump(array_is_list($invalidList));  // false (out of order)
```

### Purpose and Benefits

```php
<?php
// array_is_list() helps with type handling
function processData(mixed $data): void
{
    if (is_array($data) && array_is_list($data)) {
        // Safe to iterate with integer keys
        foreach ($data as $index => $item) {
            echo "Item $index: $item\n";
        }
    } else {
        // Handle associative array
        foreach ($data as $key => $value) {
            echo "Key '$key': $value\n";
        }
    }
}

// Benefits:
// ✓ Type safety
// ✓ Cleaner code
// ✓ Better JSON serialization
// ✓ Avoid type confusion
```

---

## Practical Use Cases

### JSON Serialization

```php
<?php
// Distinguish between array types for JSON
$list = [1, 2, 3];
$associative = ['a' => 1, 'b' => 2];

if (array_is_list($list)) {
    // Encode as JSON array
    echo json_encode($list);       // [1,2,3]
} else {
    // Encode as JSON object
    echo json_encode($associative); // {"a":1,"b":2}
}

// This matters for API responses
class ApiResponse
{
    public function format(array $data): string
    {
        if (array_is_list($data)) {
            return json_encode(['items' => $data]);
        } else {
            return json_encode($data);
        }
    }
}
```

### Type-Safe Processing

```php
<?php
function processArray(array $data): array
{
    if (array_is_list($data)) {
        // Process as list - can safely use numeric keys
        return array_map(fn($item) => $item * 2, $data);
    } else {
        // Process as associative - preserve keys
        return array_map(fn($value) => $value * 2, $data);
    }
}

// Different results
$list = [1, 2, 3];
$assoc = ['a' => 1, 'b' => 2];

print_r(processArray($list));   // [2, 4, 6]
print_r(processArray($assoc));  // ['a' => 2, 'b' => 4]
```

### Data Validation

```php
<?php
class DataValidator
{
    public function validateInput(mixed $input): bool
    {
        // Expect array list, not associative
        if (!is_array($input) || !array_is_list($input)) {
            return false;
        }

        // Validate each item
        foreach ($input as $item) {
            if (!is_string($item)) {
                return false;
            }
        }

        return true;
    }

    public function validateMapping(mixed $input): bool
    {
        // Expect associative array, not list
        if (!is_array($input) || array_is_list($input)) {
            return false;
        }

        // Validate mappings
        foreach ($input as $key => $value) {
            if (!is_string($key) || !is_numeric($value)) {
                return false;
            }
        }

        return true;
    }
}

// Usage
$validator = new DataValidator();
echo $validator->validateInput(['a', 'b', 'c']) ? "Valid" : "Invalid";  // Valid
echo $validator->validateInput(['x' => 'a']) ? "Valid" : "Invalid";      // Invalid
```

---

## Array Unpacking Improvements

### Enhanced Unpacking

```php
<?php
// Array unpacking with string keys (improved in 8.3)
$array1 = ['a' => 1, 'b' => 2];
$array2 = ['c' => 3, 'd' => 4];

// Unpack both - string keys preserved
$merged = [...$array1, ...$array2];
print_r($merged);
// Array ( [a] => 1 [b] => 2 [c] => 3 [d] => 4 )

// Better type consistency
$data = ['name' => 'John', 'age' => 30];
$defaults = ['email' => 'test@example.com', 'role' => 'user'];

$merged = [...$defaults, ...$data];  // Data overrides defaults
print_r($merged);
// [email => test@example.com, role => user, name => John, age => 30]
```

### Practical Unpacking Patterns

```php
<?php
// Combine configurations
class ConfigMerger
{
    public function merge(array ...$configs): array
    {
        return array_reduce(
            $configs,
            fn($carry, $config) => [...$carry, ...$config],
            []
        );
    }
}

$defaultConfig = ['timeout' => 30, 'retry' => 3];
$envConfig = ['timeout' => 60];
$userConfig = ['retry' => 5, 'cache' => true];

$merger = new ConfigMerger();
$final = $merger->merge($defaultConfig, $envConfig, $userConfig);
print_r($final);
// [timeout => 60, retry => 5, cache => true]

// Create data from multiple sources
function combineUserData(array $profile, array $settings, array $preferences): array
{
    return [
        'profile' => $profile,
        'settings' => $settings,
        'preferences' => $preferences,
        'combined' => [...$profile, ...$settings, ...$preferences],
    ];
}
```

---

## Type System Improvements

### Better Type Handling

```php
<?php
// Improved type consistency in PHP 8.3
function processArray(array $input): array
{
    // array_is_list helps with type safety
    if (array_is_list($input)) {
        // Type narrowing: treat as list
        return array_filter($input, fn($v) => $v > 0);
    } else {
        // Type narrowing: treat as associative
        return array_filter($input, fn($v) => strlen($v) > 0);
    }
}

// Function signature improvements
function sortArray(
    array $items,
    int $flags = SORT_REGULAR
): array {
    asort($items, $flags);
    return $items;
}
```

---

## Performance Enhancements

### Array Operation Speed

```php
<?php
// PHP 8.3 array operations are faster
class ArrayPerformanceTest
{
    public function benchmark(): void
    {
        $array = range(0, 10000);

        // array_is_list performance
        $start = microtime(true);
        for ($i = 0; $i < 100000; $i++) {
            array_is_list($array);
        }
        $time = (microtime(true) - $start) * 1000;
        echo "array_is_list: {$time}ms\n";

        // array_filter
        $start = microtime(true);
        for ($i = 0; $i < 1000; $i++) {
            array_filter($array, fn($v) => $v > 5000);
        }
        $time = (microtime(true) - $start) * 1000;
        echo "array_filter: {$time}ms\n";

        // array_map
        $start = microtime(true);
        for ($i = 0; $i < 1000; $i++) {
            array_map(fn($v) => $v * 2, $array);
        }
        $time = (microtime(true) - $start) * 1000;
        echo "array_map: {$time}ms\n";
    }
}
```

---

## Complete Examples

### Array Utility Class

```php
<?php
declare(strict_types=1);

namespace App\Utilities;

class ArrayUtility
{
    /**
     * Check if array is a proper list
     */
    public static function isList(mixed $value): bool
    {
        return is_array($value) && array_is_list($value);
    }

    /**
     * Check if array is associative
     */
    public static function isAssociative(mixed $value): bool
    {
        return is_array($value) && !array_is_list($value);
    }

    /**
     * Deep merge arrays
     */
    public static function deepMerge(array ...$arrays): array
    {
        $result = [];

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_array($value) && isset($result[$key]) && is_array($result[$key])) {
                    $result[$key] = self::deepMerge($result[$key], $value);
                } else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Flatten array
     */
    public static function flatten(array $array, int $depth = PHP_INT_MAX): array
    {
        $result = [];

        foreach ($array as $item) {
            if (is_array($item) && $depth > 0) {
                $result = [...$result, ...self::flatten($item, $depth - 1)];
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Group array by key
     */
    public static function groupBy(array $array, string|callable $keySelector): array
    {
        $groups = [];

        foreach ($array as $item) {
            $key = is_callable($keySelector)
                ? $keySelector($item)
                : $item[$keySelector];

            if (!isset($groups[$key])) {
                $groups[$key] = [];
            }

            $groups[$key][] = $item;
        }

        return $groups;
    }

    /**
     * Convert between array types
     */
    public static function toAssociative(array $items, string|callable $keySelector): array
    {
        $result = [];

        foreach ($items as $item) {
            $key = is_callable($keySelector)
                ? $keySelector($item)
                : $item[$keySelector];

            $result[$key] = $item;
        }

        return $result;
    }
}

// Usage examples
$items = [
    ['id' => 1, 'name' => 'John', 'role' => 'admin'],
    ['id' => 2, 'name' => 'Jane', 'role' => 'user'],
    ['id' => 3, 'name' => 'Bob', 'role' => 'admin'],
];

// Check type
echo ArrayUtility::isList($items) ? "Is list" : "Is associative";

// Group by role
$byRole = ArrayUtility::groupBy($items, 'role');
print_r($byRole);

// Convert to associative by ID
$byId = ArrayUtility::toAssociative($items, 'id');
print_r($byId);

// Deep merge
$config1 = ['db' => ['host' => 'localhost', 'port' => 5432]];
$config2 = ['db' => ['user' => 'root']];
$merged = ArrayUtility::deepMerge($config1, $config2);
print_r($merged);
```

---

## See Also

- [PHP 8.3 Overview](0-php8.3-overview.md)
- [JSON Validation](4-json-validation.md)
- [Typed Constants](2-typed-constants.md)
