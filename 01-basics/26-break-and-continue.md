# Break and Continue Statements

## Table of Contents
1. [Overview](#overview)
2. [Break Statement](#break-statement)
3. [Continue Statement](#continue-statement)
4. [Loop Control](#loop-control)
5. [Practical Examples](#practical-examples)
6. [Common Mistakes](#common-mistakes)

---

## Overview

Break and continue statements control loop execution flow.

- **break**: Exit the loop immediately
- **continue**: Skip to next iteration

---

## Break Statement

Terminates the loop and continues after it.

### Basic Break

```php
<?php
// Exit loop when condition met
for ($i = 0; $i < 10; $i++) {
    if ($i === 5) {
        break;  // Exit loop
    }
    echo $i . " ";
}
// Output: 0 1 2 3 4
?>
```

### Breaking Nested Loops

```php
<?php
// Break level 1 (inner loop only)
for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
        if ($j === 1) {
            break;  // Breaks inner loop only
        }
        echo "$i-$j ";
    }
}
// Output: 0-0 1-0 2-0

// Break level 2 (outer loop)
for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
        if ($j === 1) {
            break 2;  // Breaks both loops
        }
        echo "$i-$j ";
    }
}
// Output: 0-0
?>
```

### In Switch Statements

```php
<?php
// Break exits the switch
$choice = 2;
switch ($choice) {
    case 1:
        echo "One";
        break;  // Exit switch
    case 2:
        echo "Two";
        break;
    default:
        echo "Other";
}
?>
```

---

## Continue Statement

Skips remaining code in current iteration and moves to next.

### Basic Continue

```php
<?php
// Skip even numbers
for ($i = 0; $i < 10; $i++) {
    if ($i % 2 === 0) {
        continue;  // Skip this iteration
    }
    echo $i . " ";
}
// Output: 1 3 5 7 9
?>
```

### Continue with Levels

```php
<?php
// Continue level 1 (inner loop only)
for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
        if ($j === 1) {
            continue;  // Skip inner iteration
        }
        echo "$i-$j ";
    }
}
// Output: 0-0 0-2 1-0 1-2 2-0 2-2

// Continue level 2 (outer loop)
for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
        if ($j === 1) {
            continue 2;  // Skip to next outer iteration
        }
        echo "$i-$j ";
    }
}
// Output: 0-0 1-0 2-0
?>
```

---

## Loop Control

### Break vs Continue

```php
<?php
// BREAK: exit completely
$i = 0;
while ($i < 10) {
    if ($i === 5) {
        break;  // Exit loop
    }
    echo $i . " ";
    $i++;
}
// Output: 0 1 2 3 4

// CONTINUE: skip to next iteration
$i = 0;
while ($i < 10) {
    $i++;
    if ($i === 5) {
        continue;  // Skip this iteration
    }
    echo $i . " ";
}
// Output: 1 2 3 4 6 7 8 9 10
?>
```

### Foreach with Break/Continue

```php
<?php
$array = ['a', 'b', 'c', 'd', 'e'];

// Break: exit loop
foreach ($array as $item) {
    if ($item === 'c') {
        break;
    }
    echo $item . " ";  // a b
}

// Continue: skip to next item
foreach ($array as $item) {
    if ($item === 'c') {
        continue;
    }
    echo $item . " ";  // a b d e
}
?>
```

---

## Practical Examples

### Search Loop

```php
<?php
// Find item and break
$items = ['apple', 'banana', 'cherry', 'date'];
$search = 'cherry';
$found = false;

foreach ($items as $index => $item) {
    if ($item === $search) {
        echo "Found at index $index";
        $found = true;
        break;  // Exit once found
    }
}

if (!$found) {
    echo "Not found";
}
?>
```

### Filter Processing

```php
<?php
// Process valid items, skip invalid
$records = [
    ['name' => 'John', 'age' => 25],
    ['name' => '', 'age' => 30],        // Invalid
    ['name' => 'Jane', 'age' => 28],
    ['name' => 'Bob', 'age' => 22],
];

foreach ($records as $record) {
    // Skip if invalid
    if (empty($record['name']) || $record['age'] < 18) {
        continue;
    }
    
    // Process valid record
    echo $record['name'] . " is " . $record['age'] . "\n";
}
// Output:
// John is 25
// Jane is 28
// Bob is 22
?>
```

### Nested Loop with Break

```php
<?php
// Find item in matrix
$matrix = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9],
];

$search = 5;
$found = false;

for ($i = 0; $i < count($matrix); $i++) {
    for ($j = 0; $j < count($matrix[$i]); $j++) {
        if ($matrix[$i][$j] === $search) {
            echo "Found at [$i][$j]";
            $found = true;
            break 2;  // Break both loops
        }
    }
}
?>
```

### Input Validation Loop

```php
<?php
$valid_inputs = [];
$invalid_count = 0;

foreach ($user_inputs as $input) {
    // Skip invalid
    if (!isValid($input)) {
        $invalid_count++;
        continue;
    }
    
    // Process valid
    $valid_inputs[] = sanitize($input);
    
    // Stop if too many invalid
    if ($invalid_count > 5) {
        break;
    }
}
?>
```

---

## Common Mistakes

### 1. Using Break in Nested Loop

```php
<?php
// ❌ Wrong: break only exits inner loop
for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
        if ($condition) {
            break;  // Exits inner loop only!
        }
    }
    // Outer loop continues
}

// ✓ Correct: use break with level
for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
        if ($condition) {
            break 2;  // Exits both loops
        }
    }
}

// ✓ Or use flag
$exit = false;
for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
        if ($condition) {
            $exit = true;
            break;
        }
    }
    if ($exit) break;
}
?>
```

### 2. Continue Instead of Break

```php
<?php
// ❌ Wrong: continues instead of exits
while ($count < 10) {
    $count++;
    if ($count === 5) {
        continue;  // Just skips this iteration
    }
    echo $count;
}
// Outputs: 1 2 3 4 6 7 8 9 10

// ✓ Correct: use break to exit
while ($count < 10) {
    $count++;
    if ($count === 5) {
        break;  // Exits loop
    }
    echo $count;
}
// Output: 1 2 3 4
?>
```

### 3. Forgetting Break in Switch

```php
<?php
// ❌ Fall-through (common bug)
$choice = 1;
switch ($choice) {
    case 1:
        echo "One";
        // Missing break!
    case 2:
        echo "Two";  // Also executes!
        break;
}
// Output: OneTwo

// ✓ Correct: add break
switch ($choice) {
    case 1:
        echo "One";
        break;  // Exit switch
    case 2:
        echo "Two";
        break;
}
// Output: One
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class DataProcessor {
    public function processRecords(array $records): array {
        $valid = [];
        $invalid = [];
        $max_errors = 10;
        $error_count = 0;
        
        foreach ($records as $index => $record) {
            // Stop if too many errors
            if ($error_count > $max_errors) {
                echo "Too many errors, stopping\n";
                break;
            }
            
            // Skip empty records
            if (empty($record)) {
                continue;
            }
            
            // Validate record
            $errors = $this->validate($record);
            
            if (!empty($errors)) {
                $invalid[$index] = $errors;
                $error_count++;
                continue;  // Skip processing
            }
            
            // Process valid record
            $valid[$index] = $this->process($record);
        }
        
        return [
            'valid' => $valid,
            'invalid' => $invalid,
            'errors' => $error_count,
        ];
    }
    
    public function findFirst(array $items, callable $predicate) {
        foreach ($items as $key => $item) {
            if ($predicate($item)) {
                return ['key' => $key, 'value' => $item];
            }
        }
        return null;
    }
    
    private function validate($record): array {
        $errors = [];
        if (empty($record['name'])) $errors[] = 'Name required';
        if (empty($record['email'])) $errors[] = 'Email required';
        return $errors;
    }
    
    private function process($record) {
        return array_map('strtoupper', $record);
    }
}

// Usage
$processor = new DataProcessor();
$records = [
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => '', 'email' => 'jane@example.com'],
    ['name' => 'Bob', 'email' => 'bob@example.com'],
];

$result = $processor->processRecords($records);
print_r($result);
?>
```

---

## Next Steps

✅ Understand break and continue  
→ Learn [loops](22-for-loop.md)  
→ Study [loop types](23-while-loop.md)  
→ Master [foreach loops](26-for-each-loop.md)
