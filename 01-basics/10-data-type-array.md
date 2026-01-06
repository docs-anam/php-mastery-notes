# Data Types - Array

## Overview

An array is a data structure that stores multiple values in a single variable. Arrays are crucial in PHP and come in three types:

1. **Indexed Arrays** - Arrays with numeric indexes
2. **Associative Arrays** - Arrays with named keys  
3. **Multidimensional Arrays** - Arrays containing one or more arrays

```php
<?php
// Indexed array
$fruits = ["Apple", "Banana", "Orange"];

// Associative array
$person = ["name" => "John", "age" => 30, "city" => "New York"];

// Multidimensional array
$contacts = [
    ["name" => "Alice", "phone" => "123456"],
    ["name" => "Bob", "phone" => "654321"]
];
?>
```

## Indexed Arrays

Indexed arrays use numeric indices starting from 0:

```php
<?php
// Creating indexed arrays
$fruits = array("Apple", "Banana", "Orange");
// Or using short syntax (PHP 5.4+)
$fruits = ["Apple", "Banana", "Orange"];

// Accessing elements
echo $fruits[0]; // Output: Apple
echo $fruits[1]; // Output: Banana

// Modifying elements
$fruits[1] = "Grape";

// Adding elements
$fruits[] = "Mango"; // Appends to the end
$fruits[5] = "Peach"; // Adds at index 5

// Iterate through indexed array
foreach ($fruits as $fruit) {
    echo $fruit . "\n";
}

// Get array length
echo count($fruits); // Output: number of elements
?>
```

## Associative Arrays

Associative arrays use named keys (strings):

```php
<?php
// Creating associative arrays
$person = array("name" => "John", "age" => 30, "city" => "New York");
// Or using short syntax
$person = ["name" => "John", "age" => 30, "city" => "New York"];

// Accessing elements
echo $person["name"]; // Output: John
echo $person["age"];  // Output: 30

// Modifying elements
$person["age"] = 31;

// Adding elements
$person["email"] = "john@example.com";

// Iterate through associative array
foreach ($person as $key => $value) {
    echo "$key: $value\n";
}
// Output:
// name: John
// age: 31
// city: New York
// email: john@example.com

// Check if key exists
if (isset($person["phone"])) {
    echo $person["phone"];
} else {
    echo "Phone not set";
}
?>
```

## Multidimensional Arrays

Arrays containing one or more arrays:

```php
<?php
// 2D associative array
$contacts = [
    ["name" => "Alice", "phone" => "123456"],
    ["name" => "Bob", "phone" => "654321"],
    ["name" => "Charlie", "phone" => "789012"]
];

// Accessing elements
echo $contacts[0]["name"];   // Output: Alice
echo $contacts[1]["phone"];  // Output: 654321

// Adding a new contact
$contacts[] = ["name" => "Diana", "phone" => "345678"];

// Iterate through multidimensional array
foreach ($contacts as $contact) {
    foreach ($contact as $key => $value) {
        echo "$key: $value, ";
    }
    echo "\n";
}
?>
```

## Array Functions

### Adding and Removing Elements

```php
<?php
$fruits = ["Apple", "Banana"];

// Add to end
array_push($fruits, "Orange", "Mango");
// Or use shorter syntax
$fruits[] = "Grape";

// Remove from end
$last = array_pop($fruits); // Returns "Grape"

// Add to beginning
array_unshift($fruits, "Pineapple");

// Remove from beginning
$first = array_shift($fruits); // Returns "Pineapple"

// Remove specific element
$fruits = ["Apple", "Banana", "Orange"];
unset($fruits[1]); // Removes "Banana", but keeps key
// Result: [0 => "Apple", 2 => "Orange"]

// Re-index array after deletion
$fruits = array_values($fruits);
// Result: [0 => "Apple", 1 => "Orange"]
?>
```

### Array Information

```php
<?php
$person = ["name" => "John", "age" => 30, "city" => "New York"];

// Get array length
echo count($person); // Output: 3

// Get all keys
$keys = array_keys($person);
// Result: ["name", "age", "city"]

// Get all values
$values = array_values($person);
// Result: ["John", 30, "New York"]

// Check if key exists
var_dump(isset($person["age"])); // true
var_dump(array_key_exists("age", $person)); // true

// Check if value exists
var_dump(in_array("John", $person)); // true

// Search for value
$key = array_search("John", $person); // Returns "name"
?>
```

### Array Manipulation

```php
<?php
// Merge arrays
$arr1 = ["a" => "red", "b" => "green"];
$arr2 = ["c" => "blue"];
$merged = array_merge($arr1, $arr2);
// Result: ["a" => "red", "b" => "green", "c" => "blue"]

// Combine arrays (keys and values)
$keys = ["name", "age", "city"];
$values = ["John", 30, "New York"];
$combined = array_combine($keys, $values);
// Result: ["name" => "John", "age" => 30, "city" => "New York"]

// Slice array
$arr = ["a", "b", "c", "d", "e"];
$slice = array_slice($arr, 1, 3);
// Result: ["b", "c", "d"]

// Splice array
$arr = ["a", "b", "c", "d"];
array_splice($arr, 1, 2, ["x", "y"]);
// Result: ["a", "x", "y", "d"]

// Reverse array
$arr = [1, 2, 3, 4];
$reversed = array_reverse($arr);
// Result: [4, 3, 2, 1]

// Sort array
$arr = [3, 1, 4, 1, 5];
sort($arr);
// Result: [1, 1, 3, 4, 5]
?>
```

### Array Transformations

```php
<?php
// Map - apply function to each element
$numbers = [1, 2, 3, 4];
$squared = array_map(function($n) {
    return $n * $n;
}, $numbers);
// Result: [1, 4, 9, 16]

// Filter - select elements matching condition
$numbers = [1, 2, 3, 4, 5, 6];
$even = array_filter($numbers, function($n) {
    return $n % 2 == 0;
});
// Result: [1 => 2, 3 => 4, 5 => 6]

// Reduce - combine array into single value
$numbers = [1, 2, 3, 4, 5];
$sum = array_reduce($numbers, function($carry, $item) {
    return $carry + $item;
}, 0);
// Result: 15

// Walk - apply function to each element (modifies in place)
$fruits = ["apple", "banana", "orange"];
array_walk($fruits, function(&$fruit) {
    $fruit = strtoupper($fruit);
});
// Result: ["APPLE", "BANANA", "ORANGE"]
?>
```

## Practical Examples

### Shopping Cart

```php
<?php
$cart = [
    ["name" => "Laptop", "price" => 999.99, "qty" => 1],
    ["name" => "Mouse", "price" => 29.99, "qty" => 2],
    ["name" => "Keyboard", "price" => 79.99, "qty" => 1]
];

// Calculate total
$total = array_reduce($cart, function($carry, $item) {
    return $carry + ($item["price"] * $item["qty"]);
}, 0);

echo "Total: $" . number_format($total, 2);

// Display cart
foreach ($cart as $item) {
    echo $item["name"] . " - $" . $item["price"] . " x " . $item["qty"] . "\n";
}
?>
```

### User Validation

```php
<?php
$userData = [
    "name" => "John",
    "email" => "john@example.com",
    "age" => 30
];

$requiredFields = ["name", "email", "age"];

// Check all required fields are present
$valid = true;
foreach ($requiredFields as $field) {
    if (!isset($userData[$field]) || empty($userData[$field])) {
        echo "Missing: $field\n";
        $valid = false;
    }
}

if ($valid) {
    echo "All fields valid!";
}
?>
```

## Array Iteration Methods

```php
<?php
$arr = ["a" => 1, "b" => 2, "c" => 3];

// foreach - most common
foreach ($arr as $key => $value) {
    echo "$key: $value\n";
}

// for loop with indexed arrays
for ($i = 0; $i < count($arr); $i++) {
    // Limited to indexed arrays
}

// array_walk
array_walk($arr, function($value, $key) {
    echo "$key: $value\n";
});

// Recursive iteration for multidimensional
function recursiveIterate($arr) {
    foreach ($arr as $item) {
        if (is_array($item)) {
            recursiveIterate($item);
        } else {
            echo $item . "\n";
        }
    }
}
?>
```

## Best Practices

✅ **Do:**
- Use `isset()` before accessing array keys to avoid notices
- Use `array_key_exists()` to check for key existence
- Use `foreach` for iterating over arrays
- Use built-in array functions instead of manual loops
- Choose appropriate array type (indexed vs associative)

❌ **Don't:**
- Access array elements without checking existence
- Mix indexed and associative arrays unnecessarily
- Use for loops with associative arrays
- Create deeply nested multidimensional arrays
- Use arrays as objects

## Common Pitfalls

### Missing Key Access

```php
// ❌ Generates notice if key doesn't exist
$value = $arr["key"];

// ✅ Safe access
$value = isset($arr["key"]) ? $arr["key"] : null;

// ✅ Modern way (PHP 8.0+)
$value = $arr["key"] ?? null;
```

### Unset Doesn't Re-index

```php
$arr = [1, 2, 3];
unset($arr[1]);
// Result: [0 => 1, 2 => 3] - NOT [0 => 1, 1 => 3]

// Use array_values() to re-index
$arr = array_values($arr); // [0 => 1, 1 => 3]
```

## Next Steps

- [Variable Scope](31-variable-scope.md) - Understanding variable scope
- [Loops - Foreach](26-for-each-loop.md) - Iterating arrays
- [Functions](28-functions.md) - Reusable code blocks
- [String Manipulation](17-string-manipulation.md) - String operations
