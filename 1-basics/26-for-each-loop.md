# For-Each Loop in PHP

## Overview

The foreach loop is one of the most commonly used loop constructs in PHP. It provides a simple way to iterate over arrays without worrying about array indices or the number of elements. It's designed specifically for working with arrays and is much cleaner than traditional for loops for array iteration.

## Basic For-Each Structure

### Iterating Over Indexed Arrays

```php
<?php
// Simple indexed array iteration
$fruits = ['Apple', 'Banana', 'Cherry', 'Date'];

foreach ($fruits as $fruit) {
    echo $fruit . "\n";
}
// Output:
// Apple
// Banana
// Cherry
// Date

// With array keys (index)
foreach ($fruits as $index => $fruit) {
    echo "$index: $fruit\n";
}
// Output:
// 0: Apple
// 1: Banana
// 2: Cherry
// 3: Date

// Accessing both key and value
$scores = [10, 20, 30, 40];
foreach ($scores as $key => $value) {
    echo "Position $key has score $value\n";
}
?>
```

### Iterating Over Associative Arrays

```php
<?php
// Simple associative array
$person = ['name' => 'John', 'age' => 30, 'city' => 'New York'];

foreach ($person as $info) {
    echo $info . "\n";
}
// Output:
// John
// 30
// New York

// With keys
foreach ($person as $key => $value) {
    echo "$key: $value\n";
}
// Output:
// name: John
// age: 30
// city: New York

// Array of arrays
$people = [
    ['name' => 'Alice', 'age' => 25],
    ['name' => 'Bob', 'age' => 28],
    ['name' => 'Charlie', 'age' => 35]
];

foreach ($people as $person) {
    echo "Name: " . $person['name'] . ", Age: " . $person['age'] . "\n";
}
?>
```

## Practical Examples

### Processing User Data

```php
<?php
function displayUserAccounts($users) {
    foreach ($users as $userId => $user) {
        echo "User ID: $userId\n";
        echo "Name: {$user['name']}\n";
        echo "Email: {$user['email']}\n";
        echo "Status: {$user['status']}\n";
        echo "---\n";
    }
}

$users = [
    1 => ['name' => 'John Doe', 'email' => 'john@example.com', 'status' => 'Active'],
    2 => ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'status' => 'Active'],
    3 => ['name' => 'Bob Johnson', 'email' => 'bob@example.com', 'status' => 'Inactive']
];

displayUserAccounts($users);
?>
```

### Building Configuration Array

```php
<?php
function applySettings($config) {
    foreach ($config as $setting => $value) {
        echo "Configuring $setting to ";
        
        if (is_bool($value)) {
            echo ($value ? 'enabled' : 'disabled') . "\n";
        } else {
            echo "$value\n";
        }
    }
}

$config = [
    'debug_mode' => true,
    'cache_enabled' => true,
    'database_host' => 'localhost',
    'max_connections' => 100,
    'timeout' => 30
];

applySettings($config);
?>
```

### Calculating Statistics

```php
<?php
function calculateStats($numbers) {
    $sum = 0;
    $count = 0;
    $max = PHP_INT_MIN;
    $min = PHP_INT_MAX;
    
    foreach ($numbers as $number) {
        $sum += $number;
        $count++;
        $max = max($max, $number);
        $min = min($min, $number);
    }
    
    $average = $count > 0 ? $sum / $count : 0;
    
    echo "Numbers: " . count($numbers) . "\n";
    echo "Sum: $sum\n";
    echo "Average: " . round($average, 2) . "\n";
    echo "Max: $max\n";
    echo "Min: $min\n";
}

calculateStats([45, 67, 23, 89, 56, 34, 78]);
?>
```

### Creating Index Mapping

```php
<?php
function createNameIndex($people) {
    $index = [];
    
    foreach ($people as $person) {
        $firstName = $person['first'];
        $lastName = $person['last'];
        
        // Build searchable index
        $index[$firstName . ' ' . $lastName] = $person;
    }
    
    return $index;
}

$people = [
    ['first' => 'John', 'last' => 'Doe', 'age' => 30],
    ['first' => 'Jane', 'last' => 'Smith', 'age' => 28],
    ['first' => 'Bob', 'last' => 'Johnson', 'age' => 35]
];

$nameIndex = createNameIndex($people);
echo "Found: John Doe, Age {$nameIndex['John Doe']['age']}\n";
?>
```

## Modifying Arrays During Iteration

### By Reference

```php
<?php
// Modifying array values by reference
$numbers = [1, 2, 3, 4, 5];

foreach ($numbers as &$number) {
    $number = $number * 2;
}

print_r($numbers);
// Output:
// Array ( [0] => 2 [1] => 4 [2] => 6 [3] => 8 [4] => 10 )

// IMPORTANT: Unset reference after loop
unset($number);

// Modifying nested arrays
$users = [
    ['name' => 'John', 'status' => 'pending'],
    ['name' => 'Jane', 'status' => 'pending']
];

foreach ($users as &$user) {
    $user['status'] = 'approved';
}
unset($user);

print_r($users);
?>
```

### Direct Assignment (Creates Copy)

```php
<?php
// This does NOT modify the original array
$values = [10, 20, 30];

foreach ($values as $value) {
    $value = $value * 10;  // Changes only $value variable, not array
}

print_r($values);
// Output: Array ( [0] => 10 [1] => 20 [2] => 30 )
// Original unchanged!
?>
```

## Advanced Foreach Patterns

### Multiple Arrays Together

```php
<?php
// Comparing or combining arrays
$names = ['John', 'Jane', 'Bob'];
$ages = [30, 28, 35];
$cities = ['NYC', 'LA', 'Chicago'];

for ($i = 0; $i < count($names); $i++) {
    echo "{$names[$i]} is {$ages[$i]} and lives in {$cities[$i]}\n";
}
// Output:
// John is 30 and lives in NYC
// Jane is 28 and lives in LA
// Bob is 35 and lives in Chicago
?>
```

### Breaking from Foreach

```php
<?php
// Exit loop early
$items = ['apple', 'banana', 'cherry', 'date', 'elderberry'];

foreach ($items as $item) {
    if ($item == 'cherry') {
        break;  // Stop iteration
    }
    echo $item . " ";
}
// Output: apple banana

// Skip iterations
foreach ($items as $item) {
    if ($item == 'banana') {
        continue;  // Skip banana
    }
    echo $item . " ";
}
// Output: apple cherry date elderberry
?>
```

## Common Pitfalls

### Modifying Array While Iterating

```php
<?php
// BUG - modifying array during iteration
$numbers = [1, 2, 3, 4, 5];

foreach ($numbers as $number) {
    if ($number == 2) {
        array_push($numbers, 99);  // Danger!
    }
}

// BETTER - collect items first, then process
$toAdd = [];
foreach ($numbers as $number) {
    if ($number == 2) {
        $toAdd[] = 99;
    }
}
array_merge($numbers, $toAdd);
?>
```

### Forgot to Unset Reference

```php
<?php
// BUG - reference persists after loop
$data = [1, 2, 3];

foreach ($data as &$item) {
    $item = $item * 2;
}

// $item still references last element!
$item = 100;  // This modifies $data[2]!

// FIXED - always unset
foreach ($data as &$item) {
    $item = $item * 2;
}
unset($item);  // Clean up reference

// Now safe
$item = 100;  // Doesn't affect $data
?>
```

### Wrong Variable Usage

```php
<?php
// BUG - confusing what $key is
$items = ['apple', 'banana', 'cherry'];

foreach ($items as $key => $fruit) {
    echo $key . "\n";  // Prints 0, 1, 2 (indices)
}

// CORRECT - be clear about what you need
foreach ($items as $fruit) {
    echo $fruit . "\n";  // Just the values
}
?>
```

## Best Practices

✓ **Use foreach** for array iteration (not traditional for)
✓ **Name variables clearly** - $fruit not $f
✓ **Use key => value** only when you need both
✓ **Unset references** after modifying by reference
✓ **Avoid modifying array** while iterating over it
✓ **Use break/continue** sparingly for clarity
✓ **Consider array_map()** for transformations
✓ **Use array_filter()** for filtering
✓ **Collect changes first** - apply after iteration
✓ **Test with empty arrays** - edge cases matter

## Key Takeaways

✓ **Foreach** is the standard way to iterate arrays
✓ **Indexed and associative** arrays both work
✓ **Key => value** syntax for accessing both
✓ **By reference** with & modifies original array
✓ **Without reference** creates a copy
✓ **Must unset** reference variable after loop
✓ **Break exits** the loop entirely
✓ **Continue skips** to next iteration
✓ **Simpler than for loops** for array work
✓ **Most readable** loop construct for arrays
