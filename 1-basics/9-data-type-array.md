# Array Data Type

## What is an Array?

An array is a data structure that stores multiple values under a single variable name. Arrays are one of the most powerful and useful data types in PHP.

Think of an array like a numbered or labeled list of items:

```
Array:
┌─────────────────┐
│ [0] => Apple    │
│ [1] => Banana   │
│ [2] => Orange   │
└─────────────────┘
```

## Types of Arrays

PHP has three main types of arrays:

### 1. Indexed Arrays (Numeric Keys)

Arrays with numeric index starting from 0:

```php
<?php
// Using array() function
$fruits = array("Apple", "Banana", "Orange");

// Using short syntax [] (PHP 5.4+)
$colors = ["Red", "Green", "Blue"];

// Access by index
echo $fruits[0];    // Apple
echo $fruits[1];    // Banana
echo $fruits[2];    // Orange

// Get array size
echo count($fruits);  // 3
?>
```

### 2. Associative Arrays (Named Keys)

Arrays with named keys instead of numeric indices:

```php
<?php
// Using array() function
$person = array(
    "name" => "John",
    "age" => 30,
    "city" => "New York"
);

// Using short syntax []
$student = [
    "id" => 1001,
    "name" => "Alice",
    "grade" => "A"
];

// Access by key
echo $person["name"];   // John
echo $person["age"];    // 30
echo $student["grade"]; // A
?>
```

### 3. Multidimensional Arrays

Arrays that contain other arrays:

```php
<?php
// Array of associative arrays
$employees = [
    [
        "name" => "John",
        "salary" => 50000
    ],
    [
        "name" => "Alice",
        "salary" => 60000
    ],
    [
        "name" => "Bob",
        "salary" => 55000
    ]
];

// Access nested values
echo $employees[0]["name"];     // John
echo $employees[1]["salary"];   // 60000
echo $employees[2]["name"];     // Bob

// Three-dimensional array
$grades = [
    "math" => [
        "quiz" => [80, 85, 90],
        "exam" => 92
    ],
    "english" => [
        "quiz" => [75, 80],
        "exam" => 88
    ]
];

echo $grades["math"]["quiz"][2];  // 90
?>
```

## Creating and Adding Elements

### Creating Arrays

```php
<?php
// Empty array
$empty = [];

// With initial values
$numbers = [1, 2, 3, 4, 5];

// With keys
$settings = ["theme" => "dark", "language" => "en"];

// Mixed (not recommended)
$mixed = [0 => "apple", "name" => "fruit"];
?>
```

### Adding Elements

```php
<?php
$fruits = ["Apple", "Banana"];

// Add to end (auto-increment key)
$fruits[] = "Orange";      // Index 2
$fruits[] = "Mango";       // Index 3

// Add with specific key
$fruits[10] = "Grape";

// Add to associative array
$person = ["name" => "John"];
$person["age"] = 30;
$person["email"] = "john@example.com";

var_dump($fruits);
var_dump($person);
?>
```

## Array Functions

### Count and Size

```php
<?php
$items = ["apple", "banana", "orange"];

// Get array length
echo count($items);         // 3
echo sizeof($items);        // 3 (alias for count)

// Check if empty
if (empty($items)) {
    echo "Array is empty";
}

// Check if key exists
if (isset($items[0])) {
    echo "Item exists";
}
?>
```

### Iterating Through Arrays

```php
<?php
$fruits = ["Apple", "Banana", "Orange"];

// Method 1: foreach
foreach ($fruits as $fruit) {
    echo $fruit . "\n";
}

// Method 2: foreach with keys
$person = ["name" => "John", "age" => 30];
foreach ($person as $key => $value) {
    echo "$key: $value\n";
}

// Method 3: for loop (indexed arrays)
for ($i = 0; $i < count($fruits); $i++) {
    echo $fruits[$i] . "\n";
}

// Method 4: while loop
$index = 0;
while ($index < count($fruits)) {
    echo $fruits[$index] . "\n";
    $index++;
}
?>
```

### Checking Contents

```php
<?php
$fruits = ["Apple", "Banana", "Orange"];

// Check if value exists
if (in_array("Apple", $fruits)) {
    echo "Apple is in the array";
}

// Search for value and get key
$key = array_search("Banana", $fruits);
echo $key;  // 1

// Get all keys
$keys = array_keys($person);

// Get all values
$values = array_values($person);
?>
```

### Modifying Arrays

```php
<?php
$numbers = [1, 2, 3, 4, 5];

// Add element to end
array_push($numbers, 6, 7);
// $numbers = [1, 2, 3, 4, 5, 6, 7]

// Remove last element
$last = array_pop($numbers);
// $last = 7, $numbers = [1, 2, 3, 4, 5, 6]

// Add element to beginning
array_unshift($numbers, 0);
// $numbers = [0, 1, 2, 3, 4, 5, 6]

// Remove first element
$first = array_shift($numbers);
// $first = 0, $numbers = [1, 2, 3, 4, 5, 6]

// Combine arrays
$arr1 = [1, 2];
$arr2 = [3, 4];
$combined = array_merge($arr1, $arr2);
// $combined = [1, 2, 3, 4]

// Remove by key/value
unset($numbers[2]);
$numbers = array_values($numbers);  // Re-index
?>
```

### Sorting Arrays

```php
<?php
$fruits = ["Banana", "Apple", "Orange"];

// Sort (modifies original)
sort($fruits);
// $fruits = ["Apple", "Banana", "Orange"]

// Reverse sort
rsort($fruits);
// $fruits = ["Orange", "Banana", "Apple"]

// Sort associative arrays by key
ksort($person);

// Sort by value
asort($person);

// Sort numbers
$numbers = [3, 1, 4, 1, 5, 9];
sort($numbers);
// $numbers = [1, 1, 3, 4, 5, 9]
?>
```

### Transforming Arrays

```php
<?php
$numbers = [1, 2, 3, 4, 5];

// Map: Apply function to each element
$squared = array_map(function($n) {
    return $n * $n;
}, $numbers);
// $squared = [1, 4, 9, 16, 25]

// Filter: Keep only elements that match
$evens = array_filter($numbers, function($n) {
    return $n % 2 == 0;
});
// $evens = [2, 4]

// Reduce: Combine to single value
$sum = array_reduce($numbers, function($carry, $item) {
    return $carry + $item;
}, 0);
// $sum = 15
?>
```

### Slicing Arrays

```php
<?php
$items = ["A", "B", "C", "D", "E"];

// Get subset
$slice = array_slice($items, 1, 3);
// $slice = ["B", "C", "D"]

// Get last N elements
$last = array_slice($items, -2);
// $last = ["D", "E"]

// Remove and replace
array_splice($items, 2, 2, ["X", "Y"]);
// $items = ["A", "B", "X", "Y", "E"]
?>
```

## Checking Array Type

```php
<?php
$arr = [1, 2, 3];
$str = "hello";

// Check if variable is array
if (is_array($arr)) {
    echo "It's an array";
}

// Type check
var_dump(is_array($arr));   // bool(true)
var_dump(is_array($str));   // bool(false)

// Get type
gettype($arr);              // "array"
?>
```

## Practical Examples

### Student Grades

```php
<?php
$students = [
    [
        "name" => "Alice",
        "grades" => [85, 90, 92]
    ],
    [
        "name" => "Bob",
        "grades" => [78, 82, 88]
    ]
];

// Calculate average for each student
foreach ($students as $student) {
    $average = array_sum($student['grades']) / count($student['grades']);
    echo $student['name'] . ": " . round($average, 1) . "\n";
}
?>
```

### Product Inventory

```php
<?php
$inventory = [
    "laptop" => 5,
    "mouse" => 15,
    "keyboard" => 8,
    "monitor" => 3
];

// Display low stock items
foreach ($inventory as $product => $quantity) {
    if ($quantity < 5) {
        echo "$product: LOW STOCK ($quantity)\n";
    }
}

// Update inventory
$inventory["laptop"] -= 1;  // Sold one
$inventory["mouse"] += 10;  // Restocked

// Get total items
$total = array_sum($inventory);
echo "Total items: $total\n";
?>
```

### Shopping Cart

```php
<?php
$cart = [
    ["item" => "Book", "price" => 15.99, "quantity" => 2],
    ["item" => "Pen", "price" => 2.50, "quantity" => 5],
    ["item" => "Notebook", "price" => 5.00, "quantity" => 1]
];

$total = 0;
foreach ($cart as $product) {
    $subtotal = $product["price"] * $product["quantity"];
    $total += $subtotal;
    echo $product["item"] . ": " . $product["quantity"] . " @ " . 
         $product["price"] . " = $" . number_format($subtotal, 2) . "\n";
}

echo "Total: $" . number_format($total, 2) . "\n";
?>
```

## Array Methods Summary

| Function | Purpose |
|----------|---------|
| `count()` | Get array size |
| `in_array()` | Check if value exists |
| `array_search()` | Find key by value |
| `array_keys()` | Get all keys |
| `array_values()` | Get all values |
| `array_push()` | Add to end |
| `array_pop()` | Remove from end |
| `array_shift()` | Remove from start |
| `array_unshift()` | Add to start |
| `array_merge()` | Combine arrays |
| `array_slice()` | Get subset |
| `array_splice()` | Remove and replace |
| `sort()` | Sort array |
| `rsort()` | Reverse sort |
| `ksort()` | Sort by keys |
| `asort()` | Sort by values |
| `array_map()` | Transform elements |
| `array_filter()` | Filter elements |
| `array_reduce()` | Combine to single value |

## Key Takeaways

✓ **Indexed arrays**: Numeric keys starting from 0
✓ **Associative arrays**: Named keys (key => value)
✓ **Multidimensional arrays**: Arrays within arrays
✓ Use `[]` syntax (modern) or `array()` function (older)
✓ Access by index: `$array[0]` or by key: `$array['name']`
✓ Add elements with `$array[] = value` or specific keys
✓ Use `foreach` to iterate through arrays
✓ `count()` returns array size
✓ `in_array()` checks if value exists
✓ `array_merge()` combines arrays
✓ `array_map()` and `array_filter()` transform arrays
✓ Arrays are passed by value (copied) by default
✓ Use `&$array` to pass by reference for modifications
