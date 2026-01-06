# Type Checking Functions (is_* Functions)

## Table of Contents
1. [Overview](#overview)
2. [Type Checking Functions](#type-checking-functions)
3. [Comparison with gettype()](#comparison-with-gettype)
4. [Type Juggling Detection](#type-juggling-detection)
5. [Practical Examples](#practical-examples)
6. [Common Mistakes](#common-mistakes)

---

## Overview

PHP provides `is_*()` functions to check variable types.

**Common functions:**
- `is_int()`, `is_integer()`, `is_long()` - Integer check
- `is_float()`, `is_double()`, `is_real()` - Float check
- `is_string()` - String check
- `is_array()` - Array check
- `is_bool()` - Boolean check
- `is_null()` - NULL check
- `is_numeric()` - Numeric value check
- `is_callable()` - Callable check
- `is_resource()` - Resource check
- `is_object()` - Object check
- `is_scalar()` - Scalar check

---

## Type Checking Functions

### is_int() / is_integer() / is_long()

```php
<?php
// All three are aliases
var_dump(is_int(42));           // true
var_dump(is_integer(42));       // true
var_dump(is_long(42));          // true (deprecated in PHP 8)

var_dump(is_int(42.5));         // false
var_dump(is_int("42"));         // false
var_dump(is_int(true));         // false

// Common usage
function processId($id) {
    if (!is_int($id)) {
        throw new TypeError("ID must be integer");
    }
    return $id;
}

processId(123);    // OK
processId("123");  // Error
?>
```

### is_float() / is_double() / is_real()

```php
<?php
// All three are aliases
var_dump(is_float(3.14));       // true
var_dump(is_double(3.14));      // true
var_dump(is_real(3.14));        // true

var_dump(is_float(3));          // false
var_dump(is_float("3.14"));     // false

// Scientific notation
var_dump(is_float(1.2e3));      // true
var_dump(is_float(1.2E-3));     // true

// Common usage
function calculatePrice($amount) {
    if (!is_float($amount)) {
        $amount = (float)$amount;
    }
    return $amount * 1.1;  // Add 10%
}

echo calculatePrice(100);     // 110
echo calculatePrice("99.99"); // 109.989
?>
```

### is_string()

```php
<?php
var_dump(is_string("hello"));      // true
var_dump(is_string('world'));      // true
var_dump(is_string(123));          // false
var_dump(is_string(12.34));        // false

// String values that look like numbers
var_dump(is_string("123"));        // true (string)
var_dump(is_string("12.34"));      // true (string)

// Common usage
function displayText($text) {
    if (!is_string($text)) {
        $text = (string)$text;
    }
    return htmlspecialchars($text);
}

echo displayText("Hello");     // Hello
echo displayText(123);         // 123
?>
```

### is_array()

```php
<?php
var_dump(is_array([]));                    // true
var_dump(is_array([1, 2, 3]));            // true
var_dump(is_array(['a' => 1, 'b' => 2])); // true

var_dump(is_array("array"));              // false
var_dump(is_array(123));                  // false

// Common usage
function processArray($items) {
    if (!is_array($items)) {
        $items = [$items];  // Convert to array
    }
    return array_map('strtoupper', $items);
}

print_r(processArray("hello"));           // [HELLO]
print_r(processArray(["a", "b"]));        // [A, B]
?>
```

### is_bool()

```php
<?php
var_dump(is_bool(true));       // true
var_dump(is_bool(false));      // true
var_dump(is_bool(1));          // false
var_dump(is_bool(0));          // false
var_dump(is_bool("true"));     // false

// Common usage
function setDebug($enabled) {
    if (!is_bool($enabled)) {
        $enabled = (bool)$enabled;
    }
    return $enabled ? "DEBUG ON" : "DEBUG OFF";
}

echo setDebug(true);      // DEBUG ON
echo setDebug(1);         // DEBUG ON
echo setDebug(0);         // DEBUG OFF
?>
```

### is_null()

```php
<?php
var_dump(is_null(null));       // true
var_dump(is_null(0));          // false
var_dump(is_null(""));         // false
var_dump(is_null(false));      // false
var_dump(is_null([]));         // false

// Common usage
function getValue($key, $array, $default = null) {
    $value = $array[$key] ?? null;
    
    if (is_null($value)) {
        return $default;
    }
    
    return $value;
}

$data = ['name' => 'John', 'age' => null];
echo getValue('name', $data);          // John
echo getValue('age', $data, 'Unknown');// Unknown
echo getValue('city', $data, 'NYC');   // NYC
?>
```

### is_numeric()

```php
<?php
// Strings that represent numbers
var_dump(is_numeric("123"));       // true
var_dump(is_numeric("12.34"));     // true
var_dump(is_numeric("1.2e3"));     // true
var_dump(is_numeric("-42"));       // true

// Actual numbers
var_dump(is_numeric(123));         // true
var_dump(is_numeric(12.34));       // true

// Non-numeric
var_dump(is_numeric("12x"));       // false
var_dump(is_numeric("hello"));     // false
var_dump(is_numeric(true));        // false

// Common usage
function add($a, $b) {
    if (!is_numeric($a) || !is_numeric($b)) {
        throw new TypeError("Arguments must be numeric");
    }
    return $a + $b;
}

echo add(5, 10);      // 15
echo add("5", 10);    // 15
echo add("5x", 10);   // Error
?>
```

### is_callable()

```php
<?php
// Functions
var_dump(is_callable('strlen'));              // true
var_dump(is_callable('array_map'));           // true

// Closures
$callback = function($x) { return $x * 2; };
var_dump(is_callable($callback));             // true

// Methods
class Calculator {
    public function add($a, $b) {
        return $a + $b;
    }
}

$calc = new Calculator();
var_dump(is_callable([$calc, 'add']));       // true
var_dump(is_callable([Calculator::class, 'add'])); // false

// Common usage
function executeCallback($callback, $data) {
    if (!is_callable($callback)) {
        throw new TypeError("Callback must be callable");
    }
    return $callback($data);
}

echo executeCallback('strlen', 'hello');     // 5
echo executeCallback(fn($x) => $x * 2, 5);   // 10
?>
```

### is_object()

```php
<?php
class MyClass {}

var_dump(is_object(new MyClass()));   // true
var_dump(is_object(new stdClass()));  // true
var_dump(is_object([]));              // false
var_dump(is_object("object"));        // false

// Common usage
function processObject($obj) {
    if (!is_object($obj)) {
        throw new TypeError("Expected object");
    }
    return get_class($obj);
}

echo processObject(new MyClass());    // MyClass
?>
```

### is_scalar()

```php
<?php
// Scalars are: int, float, string, bool
var_dump(is_scalar(123));             // true
var_dump(is_scalar(3.14));            // true
var_dump(is_scalar("hello"));         // true
var_dump(is_scalar(true));            // true

// Not scalars
var_dump(is_scalar([]));              // false
var_dump(is_scalar(new stdClass()));  // false
var_dump(is_scalar(null));            // false

// Common usage
function filterScalars($data) {
    return array_filter($data, 'is_scalar');
}

$mixed = [1, "hello", [], true, new stdClass()];
print_r(filterScalars($mixed));
// Output: [1, "hello", true]
?>
```

---

## Comparison with gettype()

```php
<?php
// gettype() returns string representation
var_dump(gettype(123));        // "integer"
var_dump(gettype(3.14));       // "double"
var_dump(gettype("hello"));    // "string"
var_dump(gettype(true));       // "boolean"
var_dump(gettype(null));       // "NULL"
var_dump(gettype([]));         // "array"
var_dump(gettype(new stdClass())); // "object"

// is_*() functions are more specific and readable
if (is_int($value)) {
    // Handle integer
}

// vs.
if (gettype($value) === "integer") {
    // Handle integer
}

// Performance: is_*() functions are faster
$benchmark_is = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    is_int(123);
}
$time_is = microtime(true) - $benchmark_is;

$benchmark_get = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    gettype(123) === "integer";
}
$time_get = microtime(true) - $benchmark_get;

echo "is_int: $time_is\n";
echo "gettype: $time_get\n";
// is_int is significantly faster
?>
```

---

## Type Juggling Detection

```php
<?php
// PHP type juggling can be surprising
var_dump("42" == 42);           // true (loose comparison)
var_dump("42" === 42);          // false (strict comparison)

// is_*() functions check actual type
$value = "42";
var_dump(is_int($value));       // false (it's a string)

// Type casting
$int_value = (int)"42";
var_dump(is_int($int_value));   // true

// Common type juggling in conditionals
if ("0") {
    echo "True\n";  // Won't print (falsy string)
}

if (is_numeric("0")) {
    echo "It's numeric\n";  // Will print
}

// Safe type checking for user input
function getUserId($id) {
    // Don't accept string IDs
    if (!is_int($id)) {
        throw new TypeError("ID must be integer, got " . gettype($id));
    }
    return $id;
}

getUserId(123);    // OK
getUserId("123");  // Error
?>
```

---

## Practical Examples

### Input Validation

```php
<?php
class FormValidator {
    public function validate(array $data): array {
        $errors = [];
        
        // Validate email (string)
        if (!isset($data['email']) || !is_string($data['email'])) {
            $errors['email'] = 'Email must be a string';
        }
        
        // Validate age (integer)
        if (!isset($data['age']) || !is_int($data['age'])) {
            $errors['age'] = 'Age must be an integer';
        }
        
        // Validate score (numeric)
        if (!isset($data['score']) || !is_numeric($data['score'])) {
            $errors['score'] = 'Score must be numeric';
        }
        
        // Validate active (boolean)
        if (!isset($data['active']) || !is_bool($data['active'])) {
            $errors['active'] = 'Active must be boolean';
        }
        
        // Validate tags (array)
        if (!isset($data['tags']) || !is_array($data['tags'])) {
            $errors['tags'] = 'Tags must be an array';
        }
        
        return $errors;
    }
}

$validator = new FormValidator();
$result = $validator->validate([
    'email' => 'john@example.com',
    'age' => 25,
    'score' => 95.5,
    'active' => true,
    'tags' => ['php', 'web']
]);

if (empty($result)) {
    echo "Valid!";
} else {
    print_r($result);
}
?>
```

### Data Processing Pipeline

```php
<?php
class DataProcessor {
    public function processValue($value): mixed {
        // Handle integers
        if (is_int($value)) {
            return $value * 2;
        }
        
        // Handle floats
        if (is_float($value)) {
            return round($value, 2);
        }
        
        // Handle strings
        if (is_string($value)) {
            return strtoupper($value);
        }
        
        // Handle arrays
        if (is_array($value)) {
            return array_filter($value, 'is_numeric');
        }
        
        // Handle callables
        if (is_callable($value)) {
            return $value();
        }
        
        // Handle null
        if (is_null($value)) {
            return "N/A";
        }
        
        // Default
        return $value;
    }
    
    public function processData($data) {
        if (!is_array($data)) {
            return null;
        }
        
        $result = [];
        foreach ($data as $key => $value) {
            $result[$key] = $this->processValue($value);
        }
        
        return $result;
    }
}

$processor = new DataProcessor();
$data = [
    'count' => 10,
    'price' => 19.99,
    'name' => 'product',
    'tags' => ['a', 'b', '123'],
    'callback' => fn() => 'called',
    'empty' => null,
];

print_r($processor->processData($data));
?>
```

---

## Common Mistakes

### 1. Forgetting Type Juggling

```php
<?php
// ❌ Wrong: Type juggling may cause unexpected behavior
$value = "42";
if ($value == 42) {
    echo "Equal";  // Prints! (loose comparison)
}

if (is_int($value)) {
    echo "Is integer";  // Doesn't print
}

// ✓ Correct: Use strict comparison or type checking
if ($value === 42) {
    echo "Identical";  // Doesn't print
}

if (is_int($value)) {
    // Explicitly check type
    echo "Is actually a string";
}
?>
```

### 2. Using gettype() for Comparison

```php
<?php
// ❌ Wrong: Verbose and slower
if (gettype($value) === "integer") {
    // Process integer
}

if (gettype($value) === "array") {
    // Process array
}

// ✓ Correct: Use is_*() functions
if (is_int($value)) {
    // Process integer
}

if (is_array($value)) {
    // Process array
}
?>
```

### 3. Not Checking for NULL

```php
<?php
// ❌ Wrong: May cause issues if value is null
$result = $array['key'];
$length = strlen($result);  // Error if null

// ✓ Correct: Check type first
$result = $array['key'] ?? null;
if (is_string($result)) {
    $length = strlen($result);
}

// ✓ Or use null coalescing with default
$result = $array['key'] ?? '';
$length = strlen($result);
?>
```

### 4. Assuming String Numbers Are Integers

```php
<?php
// ❌ Wrong: Forgetting that "123" is a string
$id = $_GET['id'];  // Always string from GET
if (is_int($id)) {
    // Never executes!
}

// ✓ Correct: Check for numeric string, then cast
if (is_numeric($id)) {
    $id = (int)$id;
    if (is_int($id)) {
        // Now it's safe
    }
}

// ✓ Or cast first
$id = (int)$_GET['id'];
if (is_int($id) && $id > 0) {
    // Valid integer ID
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class DataValidator {
    private $errors = [];
    
    public function validateData(mixed $data): bool {
        $this->errors = [];
        
        if (!is_array($data)) {
            $this->errors[] = "Input must be an array";
            return false;
        }
        
        // Check each field
        $this->checkString($data, 'username');
        $this->checkString($data, 'email');
        $this->checkInt($data, 'age');
        $this->checkNumeric($data, 'salary');
        $this->checkBool($data, 'active');
        $this->checkArray($data, 'roles');
        
        return empty($this->errors);
    }
    
    private function checkString($data, $field): void {
        if (!isset($data[$field]) || !is_string($data[$field])) {
            $this->errors[] = "$field must be a string";
        }
    }
    
    private function checkInt($data, $field): void {
        if (!isset($data[$field]) || !is_int($data[$field])) {
            $this->errors[] = "$field must be an integer";
        }
    }
    
    private function checkNumeric($data, $field): void {
        if (!isset($data[$field]) || !is_numeric($data[$field])) {
            $this->errors[] = "$field must be numeric";
        }
    }
    
    private function checkBool($data, $field): void {
        if (!isset($data[$field]) || !is_bool($data[$field])) {
            $this->errors[] = "$field must be boolean";
        }
    }
    
    private function checkArray($data, $field): void {
        if (!isset($data[$field]) || !is_array($data[$field])) {
            $this->errors[] = "$field must be an array";
        }
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
}

// Usage
$validator = new DataValidator();
$valid = $validator->validateData([
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'age' => 30,
    'salary' => 50000.50,
    'active' => true,
    'roles' => ['admin', 'user'],
]);

if ($valid) {
    echo "Data is valid!";
} else {
    foreach ($validator->getErrors() as $error) {
        echo "- $error\n";
    }
}
?>
```

---

## Next Steps

✅ Understand type checking with is_*() functions  
→ Learn [variable scope](31-variable-scope.md)  
→ Study [type casting](3-data-type-number.md)  
→ Explore [functions](28-functions.md)
