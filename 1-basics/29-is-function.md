# Type Checking Functions (is_*) in PHP

## Overview

PHP provides a set of built-in functions prefixed with `is_` that allow you to check the data type of variables. These functions are essential for input validation, type checking, and ensuring your code handles different data types correctly. Type checking helps prevent bugs and makes your code more robust.

## Basic Type Checking Functions

### Checking Primitive Types

```php
<?php
// Check for integer
$num = 42;
if (is_int($num)) {
    echo "It's an integer\n";
}

// Check for float
$pi = 3.14;
if (is_float($pi)) {
    echo "It's a float\n";
}

// Check for string
$text = "Hello";
if (is_string($text)) {
    echo "It's a string\n";
}

// Check for boolean
$flag = true;
if (is_bool($flag)) {
    echo "It's a boolean\n";
}

// Check for NULL
$empty = null;
if (is_null($empty)) {
    echo "It's NULL\n";
}

// Example with multiple variables
$values = [42, 3.14, "text", true, null];
foreach ($values as $value) {
    echo "Type: " . gettype($value) . "\n";
}
?>
```

### Checking Complex Types

```php
<?php
// Check for array
$arr = [1, 2, 3];
if (is_array($arr)) {
    echo "It's an array\n";
}

// Check for object
class User {}
$user = new User();
if (is_object($user)) {
    echo "It's an object\n";
}

// Check for callable (function/method)
$func = 'strlen';
if (is_callable($func)) {
    echo "It's callable\n";
}

// Check for resource
$file = fopen('test.txt', 'r');
if (is_resource($file)) {
    echo "It's a resource\n";
}
fclose($file);
?>
```

## Practical Examples

### Input Validation

```php
<?php
function validateUserInput($name, $age, $score) {
    $errors = [];
    
    // Validate name is string
    if (!is_string($name)) {
        $errors[] = "Name must be a string";
    }
    
    // Validate age is integer
    if (!is_int($age) || $age < 0 || $age > 150) {
        $errors[] = "Age must be a positive integer";
    }
    
    // Validate score is numeric
    if (!is_numeric($score) || $score < 0 || $score > 100) {
        $errors[] = "Score must be numeric between 0-100";
    }
    
    if (empty($errors)) {
        echo "All inputs valid!\n";
        return true;
    } else {
        foreach ($errors as $error) {
            echo "Error: $error\n";
        }
        return false;
    }
}

validateUserInput("John", 30, 85);
validateUserInput("Jane", "25", 120);
?>
```

### Processing Mixed Data

```php
<?php
function processMixedData($data) {
    if (is_array($data)) {
        echo "Processing array with " . count($data) . " items\n";
        foreach ($data as $item) {
            processMixedData($item);  // Recursive
        }
    } elseif (is_string($data)) {
        echo "String: " . strtoupper($data) . "\n";
    } elseif (is_int($data) || is_float($data)) {
        echo "Number: " . ($data * 2) . "\n";
    } elseif (is_bool($data)) {
        echo "Boolean: " . ($data ? "True" : "False") . "\n";
    } elseif (is_null($data)) {
        echo "Null value\n";
    }
}

processMixedData([10, "test", 3.14, true, null]);
?>
```

### Secure Function Parameters

```php
<?php
function calculateTotal($items, $taxRate) {
    // Validate inputs
    if (!is_array($items)) {
        throw new InvalidArgumentException("Items must be an array");
    }
    
    if (!is_numeric($taxRate) || $taxRate < 0 || $taxRate > 1) {
        throw new InvalidArgumentException("Tax rate must be 0-1");
    }
    
    $total = 0;
    foreach ($items as $price) {
        if (!is_numeric($price)) {
            throw new InvalidArgumentException("All prices must be numeric");
        }
        $total += $price;
    }
    
    return $total + ($total * $taxRate);
}

try {
    $total = calculateTotal([10, 20, 30], 0.1);
    echo "Total: \$$total\n";
} catch (InvalidArgumentException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
```

### Database Query Parameter Binding

```php
<?php
function buildQuery($userId, $filters) {
    $query = "SELECT * FROM users WHERE user_id = " . (int)$userId;
    
    if (is_array($filters)) {
        foreach ($filters as $key => $value) {
            if (is_string($value)) {
                $query .= " AND $key = '" . addslashes($value) . "'";
            } elseif (is_numeric($value)) {
                $query .= " AND $key = " . (int)$value;
            } elseif (is_bool($value)) {
                $query .= " AND $key = " . ($value ? 1 : 0);
            }
        }
    }
    
    return $query;
}

echo buildQuery(5, ['status' => 'active', 'verified' => true]);
?>
```

### Type-Safe Array Processing

```php
<?php
function summarizeData($data) {
    $summary = [
        'strings' => 0,
        'numbers' => 0,
        'booleans' => 0,
        'arrays' => 0,
        'nulls' => 0,
        'other' => 0
    ];
    
    if (!is_array($data)) {
        return $summary;
    }
    
    foreach ($data as $item) {
        if (is_string($item)) $summary['strings']++;
        elseif (is_int($item) || is_float($item)) $summary['numbers']++;
        elseif (is_bool($item)) $summary['booleans']++;
        elseif (is_array($item)) $summary['arrays']++;
        elseif (is_null($item)) $summary['nulls']++;
        else $summary['other']++;
    }
    
    return $summary;
}

$data = [1, "test", 2.5, true, null, [1,2], new stdClass()];
print_r(summarizeData($data));
?>
```

## All Type Checking Functions

### Complete Reference Table

```php
<?php
// Primitive Types
is_int($var)         // Integer (integer)
is_float($var)       // Float (double, real)
is_string($var)      // String
is_bool($var)        // Boolean
is_null($var)        // NULL value

// Complex Types
is_array($var)       // Array
is_object($var)      // Object
is_callable($var)    // Callable (function/method name)
is_resource($var)    // Resource

// Composite Checks
is_numeric($var)     // Numeric string or number
is_scalar($var)      // Scalar (int, float, string, bool)
is_iterable($var)    // Array or Traversable object (PHP 7.1+)
is_countable($var)   // Countable object or array (PHP 7.3+)

// Value Checks
isset($var)          // Variable is set and not NULL
empty($var)          // Variable is empty
```
?>
```

## Type Juggling and Conversion

### Type Coercion Rules

```php
<?php
// Type juggling examples
$num = "42";
if (is_numeric($num)) {
    $num = (int)$num;  // Explicit conversion
    if (is_int($num)) {
        echo "Converted to int: $num\n";
    }
}

// String to boolean
$str = "0";
if (is_string($str) && !empty($str)) {
    echo "Non-empty string\n";
}

// Array to string
$arr = [1, 2, 3];
if (is_array($arr)) {
    $str = implode(",", $arr);
    echo "Array as string: $str\n";
}
?>
```

## Common Pitfalls

### Confusing is_int() and is_numeric()

```php
<?php
// is_int() checks type
if (is_int("42")) {
    echo "This won't print (string, not int)\n";
}

// is_numeric() checks if value can be used as number
if (is_numeric("42")) {
    echo "This will print (string contains number)\n";
}

// Correct validation
$value = "42";
if (is_numeric($value)) {
    $number = (int)$value;  // Safe conversion
    if (is_int($number)) {
        echo "Now it's an int: $number\n";
    }
}
?>
```

### Forgetting to Check Array Type

```php
<?php
// BUG - assumes $data is array
function processData($data) {
    foreach ($data as $item) {  // Error if $data not array!
        echo $item . "\n";
    }
}

// FIXED - validate first
function processData_safe($data) {
    if (!is_array($data)) {
        $data = [$data];  // Convert to array
    }
    
    foreach ($data as $item) {
        echo $item . "\n";
    }
}
?>
```

### Not Checking NULL

```php
<?php
// BUG - could crash
function getValue($id) {
    return getUserName($id);  // Could return NULL
}

// Use value without checking
echo getValue(999)->length;  // Error!

// FIXED - check for NULL
function getValue_safe($id) {
    $value = getUserName($id);
    
    if (is_null($value)) {
        return "Unknown";
    }
    
    return $value->name;
}
?>
```

## Best Practices

✓ **Always validate input** - check types for external data
✓ **Use is_numeric()** - for string numbers, is_int() for type
✓ **Check arrays before iteration** - prevent foreach errors
✓ **Use type hints** - PHP 7+ allows function type hints
✓ **Validate database results** - always check for NULL
✓ **Handle type conversion** - explicit casting safer
✓ **Document expected types** - in comments or type hints
✓ **Test type boundaries** - empty, zero, NULL cases
✓ **Use strict comparisons** - === instead of ==
✓ **Consider exceptions** - throw on invalid types

## Key Takeaways

✓ **is_int()** checks for integer type
✓ **is_float()** checks for floating-point type
✓ **is_string()** checks for string type
✓ **is_bool()** checks for boolean type
✓ **is_array()** checks for array type
✓ **is_null()** checks for NULL value
✓ **is_numeric()** checks if value can be used as number
✓ **is_scalar()** checks for basic types (int, float, string, bool)
✓ **is_callable()** checks if variable can be called as function
✓ **Always validate external input** - from forms, APIs, files
