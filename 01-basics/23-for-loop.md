# Loops - Iterating Code

## Table of Contents
1. [Overview](#overview)
2. [for Loop](#for-loop)
3. [while Loop](#while-loop)
4. [do/while Loop](#dowhile-loop)
5. [foreach Loop](#foreach-loop)
6. [Loop Control](#loop-control)
7. [Common Patterns](#common-patterns)
8. [Common Mistakes](#common-mistakes)

---

## Overview

Loops let you execute code multiple times. PHP has four main loop types:

1. **for** - When you know how many times to repeat
2. **while** - When condition becomes false
3. **do/while** - While loop, but always runs once
4. **foreach** - For arrays and collections

---

## for Loop

Repeat code a specific number of times:

```php
<?php
// Print 1 to 5
for ($i = 1; $i <= 5; $i++) {
    echo $i . " ";  // Output: 1 2 3 4 5
}
?>
```

### Syntax

```php
<?php
for (initialization; condition; increment) {
    // Code to repeat
}
?>
```

### Components

1. **Initialization** - Set starting value
2. **Condition** - Check before each iteration
3. **Increment** - Update after each iteration

### Examples

```php
<?php
// Count down
for ($i = 5; $i >= 1; $i--) {
    echo $i . " ";  // Output: 5 4 3 2 1
}

// Count by 2s
for ($i = 0; $i <= 10; $i += 2) {
    echo $i . " ";  // Output: 0 2 4 6 8 10
}

// Sum numbers 1-100
$sum = 0;
for ($i = 1; $i <= 100; $i++) {
    $sum += $i;
}
echo $sum;  // 5050

// Iterate over array by index
$colors = ["red", "green", "blue"];
for ($i = 0; $i < count($colors); $i++) {
    echo $colors[$i] . " ";
}
?>
```

---

## while Loop

Repeat while a condition is true:

```php
<?php
$i = 1;
while ($i <= 5) {
    echo $i . " ";
    $i++;
}
// Output: 1 2 3 4 5
?>
```

### Syntax

```php
<?php
while (condition) {
    // Code to repeat
    // Must eventually make condition false!
}
?>
```

### When to Use

```php
<?php
// Process user input until valid
$input = "";
while ($input === "") {
    $input = trim(readline("Enter something: "));
}

// Read lines from a file
$file = fopen("data.txt", "r");
while (($line = fgets($file)) !== false) {
    echo $line;
}
fclose($file);

// Database queries
$result = $mysqli->query("SELECT * FROM users");
while ($row = $result->fetch_assoc()) {
    echo $row['name'];
}
?>
```

### Infinite Loop Warning

```php
<?php
// ❌ This loops forever!
while (true) {
    echo "Infinite";  // Never stops
}

// ✅ Break when condition met
while (true) {
    echo "Working";
    break;  // Stops the loop
}
?>
```

---

## do/while Loop

Always runs at least once, then checks condition:

```php
<?php
$i = 1;
do {
    echo $i . " ";
    $i++;
} while ($i <= 5);
// Output: 1 2 3 4 5
?>
```

### Syntax

```php
<?php
do {
    // Code runs at least once
} while (condition);
?>
```

### When to Use

```php
<?php
// Menu that always shows once
do {
    echo "1. Start\n";
    echo "2. Settings\n";
    echo "3. Exit\n";
    $choice = readline("Choose: ");
} while ($choice !== "3");

// Form validation (check then ask again)
do {
    $password = readline("Enter password: ");
    if (strlen($password) < 8) {
        echo "Too short\n";
    }
} while (strlen($password) < 8);
?>
```

---

## foreach Loop

Best for iterating arrays:

```php
<?php
$colors = ["red", "green", "blue"];

foreach ($colors as $color) {
    echo $color . " ";
}
// Output: red green blue
?>
```

### With Associative Arrays

```php
<?php
$user = [
    'name' => 'Alice',
    'age' => 25,
    'email' => 'alice@example.com'
];

foreach ($user as $key => $value) {
    echo "$key: $value\n";
}
// name: Alice
// age: 25
// email: alice@example.com
?>
```

### Multidimensional Arrays

```php
<?php
$students = [
    ['name' => 'Alice', 'grade' => 'A'],
    ['name' => 'Bob', 'grade' => 'B'],
    ['name' => 'Charlie', 'grade' => 'C'],
];

foreach ($students as $student) {
    echo $student['name'] . ": " . $student['grade'] . "\n";
}
?>
```

---

## Loop Control

### break Statement

Exit loop immediately:

```php
<?php
for ($i = 1; $i <= 10; $i++) {
    if ($i === 5) {
        break;  // Exit loop when i = 5
    }
    echo $i . " ";  // Output: 1 2 3 4
}

// Find element in array
$colors = ["red", "green", "blue", "yellow"];
foreach ($colors as $color) {
    if ($color === "blue") {
        echo "Found blue!";
        break;
    }
}
?>
```

### continue Statement

Skip to next iteration:

```php
<?php
for ($i = 1; $i <= 5; $i++) {
    if ($i === 3) {
        continue;  // Skip when i = 3
    }
    echo $i . " ";  // Output: 1 2 4 5
}

// Skip even numbers
for ($i = 1; $i <= 10; $i++) {
    if ($i % 2 === 0) {
        continue;  // Skip even numbers
    }
    echo $i . " ";  // Output: 1 3 5 7 9
}
?>
```

---

## Common Patterns

### Summing Values

```php
<?php
$numbers = [10, 20, 30, 40, 50];
$sum = 0;

foreach ($numbers as $num) {
    $sum += $num;
}

echo $sum;  // 150
```

### Counting Items

```php
<?php
$scores = [85, 90, 78, 92, 88];
$count = 0;

foreach ($scores as $score) {
    if ($score >= 90) {
        $count++;
    }
}

echo "$count students scored 90+";  // 2 students scored 90+
?>
```

### Building Results

```php
<?php
$names = ["Alice", "Bob", "Charlie"];
$greetings = [];

foreach ($names as $name) {
    $greetings[] = "Hello, $name!";
}

print_r($greetings);
// Array ( [0] => Hello, Alice! [1] => Hello, Bob! [2] => Hello, Charlie! )
?>
```

---

## Common Mistakes

### 1. Infinite Loops

```php
<?php
// ❌ Never increments
$i = 0;
while ($i < 10) {
    echo $i;
    // Missing: $i++
}

// ✅ Always increment
$i = 0;
while ($i < 10) {
    echo $i;
    $i++;
}
?>
```

### 2. Off-by-One Errors

```php
<?php
$colors = ["red", "green", "blue"];

// ❌ Accesses undefined index 3
for ($i = 0; $i <= count($colors); $i++) {
    echo $colors[$i];
}

// ✅ Stop before count
for ($i = 0; $i < count($colors); $i++) {
    echo $colors[$i];
}
?>
```

### 3. Modifying Array During Iteration

```php
<?php
// ❌ Can cause unexpected behavior
$nums = [1, 2, 3, 4, 5];
foreach ($nums as $num) {
    if ($num === 3) {
        unset($nums[2]);  // Modifying during iteration
    }
}

// ✅ Filter to new array instead
$nums = [1, 2, 3, 4, 5];
$filtered = array_filter($nums, fn($n) => $n !== 3);
?>
```

### 4. Wrong Loop Type

```php
<?php
// ❌ Awkward: using for loop for array
$user = ['name' => 'Alice', 'age' => 25];
for ($i = 0; $i < count($user); $i++) {
    // Can't easily access key
}

// ✅ Better: use foreach
foreach ($user as $key => $value) {
    echo "$key: $value";
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

// Calculate statistics
$scores = [85, 92, 78, 95, 88, 90, 76, 89, 94, 87];

$sum = 0;
$highest = $scores[0];
$lowest = $scores[0];
$count_90_plus = 0;

foreach ($scores as $score) {
    // Sum
    $sum += $score;
    
    // Highest
    if ($score > $highest) {
        $highest = $score;
    }
    
    // Lowest
    if ($score < $lowest) {
        $lowest = $score;
    }
    
    // Count 90+
    if ($score >= 90) {
        $count_90_plus++;
    }
}

$average = $sum / count($scores);

echo "Test Results:\n";
echo "Average: " . number_format($average, 2) . "\n";
echo "Highest: $highest\n";
echo "Lowest: $lowest\n";
echo "Scores 90+: $count_90_plus\n";
?>
```

**Output:**
```
Test Results:
Average: 87.40
Highest: 95
Lowest: 76
Scores 90+: 4
```

---

## Next Steps

✅ Master loops  
→ Learn [break and continue](25-break-and-continue.md)  
→ Study [arrays](9-data-type-array.md)  
→ Practice [functions](28-functions.md)
