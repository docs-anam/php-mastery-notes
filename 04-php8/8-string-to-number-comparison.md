# String-to-Number Comparison

## Overview

PHP 8 improves string-to-number comparison with stricter type comparison rules, reducing unexpected type juggling and improving code predictability.

---

## String-to-Number Comparison Rules

```php
<?php
// PHP 8 - Stricter comparison rules

// Numeric string compared to number
var_dump("123" == 123); // true
var_dump("123" === 123); // false

// Non-numeric string compared to number
var_dump("abc" == 0); // false (was true in PHP 7)
var_dump("" == 0); // false (was true in PHP 7)

// String with leading zeros
var_dump("0123" == 123); // false
var_dump("0123" === 123); // false

// Hexadecimal string (NOT converted in comparisons)
var_dump("0x10" == 16); // false (was true in PHP 7.0)
?>
```

---

## Type Juggling Changes

```php
<?php
// PHP 7 behavior (old)
// "hello" == 0 => true (string converted to 0)
// "" == 0 => true

// PHP 8 behavior (new)
var_dump("hello" == 0); // false
var_dump("" == 0); // false
var_dump("0hello" == 0); // true (starts with numeric part)
var_dump("123hello" == 123); // true (numeric prefix)
?>
```

---

## Numeric String Detection

```php
<?php
// PHP 8 numeric string detection

// Purely numeric
var_dump(is_numeric("123")); // true
var_dump(is_numeric("123.45")); // true
var_dump(is_numeric("-123")); // true
var_dump(is_numeric("1e5")); // true (scientific notation)

// NOT numeric
var_dump(is_numeric("123abc")); // false
var_dump(is_numeric("abc123")); // false
var_dump(is_numeric("0x123")); // false
var_dump(is_numeric("")); // false
?>
```

---

## Arithmetic Operations

```php
<?php
// Arithmetic still converts strings when possible

// Numeric string
$result = "10" + 5; // 15

// Non-numeric string generates warning
$result = "abc" + 5; // 5 (with warning in PHP 8)

// Hexadecimal strings
$result = "0x10" + 5; // 5 (not 21, not converted)
?>
```

---

## Practical Examples

### 1. Type-Safe Comparisons

```php
<?php
function validatePort(mixed $port): bool {
    $port = (int)$port;
    
    // Type-safe comparison
    if ($port !== (int)$port) {
        return false;
    }
    
    return $port >= 1 && $port <= 65535;
}

var_dump(validatePort("8080")); // true
var_dump(validatePort("8080abc")); // false
?>
```

### 2. Strict Comparisons in Conditions

```php
<?php
function processValue(mixed $value): void {
    // ✅ Use strict comparison with numbers
    if ($value === 0) {
        echo "Exactly zero\n";
    } elseif ((int)$value === 0) {
        echo "Converts to zero\n";
    } else {
        echo "Other value\n";
    }
}

processValue("0"); // Converts to zero
processValue(0); // Exactly zero
processValue("abc"); // Other value
?>
```

### 3. Array Key Comparison

```php
<?php
$data = [
    123 => "numeric key",
    "123" => "string key"
];

// In PHP 8, these are still treated as same key
var_dump($data[123]); // numeric key
var_dump($data["123"]); // numeric key (same key)

// Non-numeric string keys are different
$array = [
    "abc" => "string key",
    "abc123" => "another key"
];

var_dump($array["abc"]); // string key
var_dump($array["abc123"]); // another key
?>
```

### 4. Database Value Validation

```php
<?php
class DatabaseValidator {
    public function isValidId(mixed $id): bool {
        // Type-safe ID check
        if (!is_int($id) && !is_numeric($id)) {
            return false;
        }
        
        $intId = (int)$id;
        return $intId > 0;
    }
    
    public function isValidStatus(mixed $status): bool {
        // Strict status check
        return $status === 'active' || 
               $status === 'inactive' || 
               $status === 'pending';
    }
}

$validator = new DatabaseValidator();
var_dump($validator->isValidId("123")); // true
var_dump($validator->isValidId("123abc")); // false
var_dump($validator->isValidStatus("active")); // true
var_dump($validator->isValidStatus(1)); // false
?>
```

### 5. Type Coercion in Functions

```php
<?php
function divide(int|float $a, int|float $b): float {
    if ($b == 0) { // loose comparison OK here
        throw new DivisionByZeroError();
    }
    
    return $a / $b;
}

echo divide(10, 2); // 5
echo divide("10", "2"); // 5 (converted)
?>
```

---

## Best Practices

### 1. Use Strict Comparisons

```php
<?php
// ✅ Preferred - strict comparison
if ($value === 0) {
    echo "Exactly zero\n";
}

// ❌ Avoid loose comparison with numbers
if ($value == 0) {
    echo "Could be zero or empty string\n";
}
?>
```

### 2. Explicit Type Conversion

```php
<?php
// ✅ Clear intent
$userId = (int)$_GET['id'] ?? 0;

// ✅ Validation before use
if (is_numeric($_GET['port'])) {
    $port = (int)$_GET['port'];
}
?>
```

### 3. Validate Input Types

```php
<?php
function processData(mixed $value): void {
    // Check type explicitly
    if (is_string($value)) {
        echo "String: $value\n";
    } elseif (is_int($value)) {
        echo "Int: $value\n";
    } else {
        throw new InvalidArgumentException("Invalid type");
    }
}
?>
```

### 4. Use Type Hints

```php
<?php
// ✅ Type hints prevent string-to-number issues
function processPort(int $port): void {
    echo "Port: $port\n";
}

processPort(8080); // OK
processPort("8080"); // Converted automatically
processPort("8080abc"); // Warning/Error
?>
```

---

## Common Mistakes

### 1. Loose Comparison with Strings

```php
<?php
// ❌ Wrong - "0" and "" both equal 0 loosely in PHP 7
if ($value == 0) {
    // Could be 0, "0", "", null, false...
}

// ✅ Correct - explicit type check
if ($value === 0 || $value === "0") {
    // Clear intent
}
?>
```

### 2. Relying on String Conversion in Comparisons

```php
<?php
// ❌ Wrong - hexadecimal string comparison
if ("0x10" == 16) { // false in PHP 8
    echo "matched";
}

// ✅ Correct - explicit conversion
if ((int)"0x10" === 16) { // still false, hex not auto-converted
    echo "matched";
}
?>
```

### 3. Not Handling Non-Numeric Strings

```php
<?php
// ❌ Wrong - assumes string is numeric
$id = (int)$_GET['id']; // Could be "abc"
$query = "SELECT * FROM users WHERE id = $id";

// ✅ Correct - validate first
$id = (int)$_GET['id'];
if (!is_numeric($_GET['id']) || $id <= 0) {
    throw new InvalidArgumentException("Invalid ID");
}
$query = "SELECT * FROM users WHERE id = $id";
?>
```

---

## Complete Example

```php
<?php
class FormProcessor {
    public function processUserForm(array $data): array {
        $errors = [];
        
        // Age - must be numeric
        if (empty($data['age']) || !is_numeric($data['age'])) {
            $errors['age'] = "Age must be numeric";
        } else {
            $age = (int)$data['age'];
            if ($age < 18 || $age > 120) {
                $errors['age'] = "Age must be between 18 and 120";
            }
        }
        
        // Status - must be exact string match
        if (empty($data['status']) || !in_array($data['status'], ['active', 'inactive', 'pending'], true)) {
            $errors['status'] = "Invalid status";
        }
        
        // Email - string type
        if (empty($data['email']) || !is_string($data['email'])) {
            $errors['email'] = "Invalid email";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email format invalid";
        }
        
        // Admin flag - must be boolean-like
        $isAdmin = isset($data['admin']) && in_array($data['admin'], [1, true, '1'], true);
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => [
                'age' => (int)($data['age'] ?? 0),
                'status' => $data['status'] ?? '',
                'email' => $data['email'] ?? '',
                'admin' => $isAdmin
            ]
        ];
    }
}

$processor = new FormProcessor();
$result = $processor->processUserForm([
    'age' => '25',
    'status' => 'active',
    'email' => 'user@example.com',
    'admin' => 1
]);

print_r($result);
?>
```

---

## See Also

- Documentation: [Type Comparison](https://www.php.net/manual/en/language.types.string.php#language.types.string.conversion)
- Related: [Union Types](5-union-types.md), [Match Expression](6-match-expression.md)
