# Break and Continue Statements in PHP

## Overview

The `break` and `continue` statements provide fine-grained control over loop execution. `break` exits a loop entirely, while `continue` skips the current iteration and jumps to the next one. These statements are essential for handling complex loop scenarios and improving code readability.

## Break Statement

### Basic Break Structure

```php
<?php
// Exit loop when condition is met
for ($i = 0; $i < 10; $i++) {
    if ($i == 5) {
        break;  // Exit the loop when i equals 5
    }
    echo $i . " ";
}
echo "\nLoop ended\n";
// Output: 0 1 2 3 4
//         Loop ended

// Break with array search
$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
foreach ($numbers as $number) {
    if ($number == 6) {
        break;
    }
    echo $number . " ";
}
// Output: 1 2 3 4 5

// Break nested loops with level
for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
        if ($i == 1 && $j == 1) {
            break 2;  // Exit both loops
        }
        echo "($i,$j) ";
    }
}
echo "\nDone\n";
// Output: (0,0) (0,1) (0,2) (1,0)
//         Done
?>
```

## Continue Statement

### Basic Continue Structure

```php
<?php
// Skip iteration when condition is met
for ($i = 0; $i < 10; $i++) {
    if ($i == 5) {
        continue;  // Skip this iteration
    }
    echo $i . " ";
}
echo "\nLoop ended\n";
// Output: 0 1 2 3 4 6 7 8 9
//         Loop ended

// Continue with arrays
$numbers = [1, 2, 3, 4, 5];
foreach ($numbers as $number) {
    if ($number == 3) {
        continue;  // Skip printing 3
    }
    echo $number . " ";
}
// Output: 1 2 4 5

// Continue with nesting
for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
        if ($j == 1) {
            continue;  // Skip j=1 in inner loop
        }
        echo "($i,$j) ";
    }
}
// Output: (0,0) (0,2) (1,0) (1,2) (2,0) (2,2)
?>
```

## Practical Examples

### Finding Data in Array

```php
<?php
function findFirstPositiveNumber($numbers) {
    foreach ($numbers as $num) {
        if ($num > 0) {
            echo "Found positive number: $num\n";
            break;  // Exit once found
        }
    }
}

findFirstPositiveNumber([-5, -3, 2, 8]);
// Output: Found positive number: 2
?>
```

### Filtering and Processing Data

```php
<?php
function processValidScores($scores) {
    foreach ($scores as $score) {
        // Skip invalid scores
        if ($score < 0 || $score > 100) {
            continue;
        }
        
        // Process only valid scores
        if ($score >= 80) {
            echo "$score - Pass\n";
        } else {
            echo "$score - Fail\n";
        }
    }
}

processValidScores([85, -5, 92, 150, 65, 45]);
// Output:
// 85 - Pass
// 92 - Pass
// 65 - Fail
// 45 - Fail
?>
```

### User Input Validation Loop

```php
<?php
function getUserChoice() {
    while (true) {
        echo "Enter your choice (1-3): ";
        $choice = 2;  // Simulated input
        
        if ($choice < 1 || $choice > 3) {
            echo "Invalid choice. Try again.\n";
            continue;  // Skip to next iteration
        }
        
        return $choice;
    }
}

$choice = getUserChoice();
echo "You chose: $choice\n";
?>
```

### Processing with Early Exit

```php
<?php
function searchInMultipleFiles($keyword) {
    $files = ['file1.txt', 'file2.txt', 'file3.txt'];
    
    foreach ($files as $file) {
        $content = "file1 has $keyword here";  // Simulated
        
        if (strpos($content, $keyword) !== false) {
            echo "Found '$keyword' in $file\n";
            break;  // Exit search after finding in first file
        }
    }
}

searchInMultipleFiles("keyword");
// Output: Found 'keyword' in file1.txt
?>
```

### Skipping Empty Records

```php
<?php
function processRecords($records) {
    foreach ($records as $record) {
        // Skip empty records
        if (empty($record['name']) || empty($record['email'])) {
            echo "Skipping invalid record\n";
            continue;
        }
        
        echo "Processing: {$record['name']} ({$record['email']})\n";
    }
}

$records = [
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => '', 'email' => 'invalid@example.com'],
    ['name' => 'Jane', 'email' => 'jane@example.com'],
    ['name' => 'Bob', 'email' => ''],
];

processRecords($records);
// Output:
// Processing: John (john@example.com)
// Skipping invalid record
// Processing: Jane (jane@example.com)
// Skipping invalid record
?>
```

### Nested Loop with Level Control

```php
<?php
function findInMatrix($target) {
    $matrix = [
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9]
    ];
    
    foreach ($matrix as $row => $values) {
        foreach ($values as $col => $value) {
            if ($value == $target) {
                echo "Found $target at position [$row][$col]\n";
                break 2;  // Exit both loops
            }
        }
    }
}

findInMatrix(5);
// Output: Found 5 at position [1][1]
?>
```

## Break vs Continue Comparison

### Side-by-Side Example

```php
<?php
echo "Using BREAK:\n";
for ($i = 0; $i < 5; $i++) {
    if ($i == 3) {
        break;
    }
    echo $i . " ";
}
echo "\n";
// Output: 0 1 2

echo "Using CONTINUE:\n";
for ($i = 0; $i < 5; $i++) {
    if ($i == 3) {
        continue;
    }
    echo $i . " ";
}
echo "\n";
// Output: 0 1 2 4
?>
```

## Common Pitfalls

### Using Break in Wrong Context

```php
<?php
// WRONG - break outside loop causes error
if ($condition) {
    break;  // Fatal error!
}

// CORRECT - break only in loops or switch
while ($true) {
    if ($condition) {
        break;
    }
}
?>
```

### Infinite Loop Despite Continue

```php
<?php
// BUG - never increments, infinite loop
while ($i < 5) {
    if ($i == 2) {
        continue;  // Skips $i++
    }
    $i++;
}

// FIXED - increment before continue check
while ($i < 5) {
    $i++;
    if ($i == 3) {
        continue;
    }
}
?>
```

### Wrong Break Level

```php
<?php
// BUG - tries to break 3 levels but only 2 exist
for ($i = 0; $i < 2; $i++) {
    for ($j = 0; $j < 2; $j++) {
        break 3;  // Error: only 2 levels!
    }
}

// FIXED - use correct level
for ($i = 0; $i < 2; $i++) {
    for ($j = 0; $j < 2; $j++) {
        break 2;  // Exit both loops
    }
}
?>
```

## Best Practices

✓ **Use break** to exit loop when goal is achieved
✓ **Use continue** to skip unwanted iterations
✓ **Avoid deep break levels** - keep nesting simple
✓ **Make conditions clear** - document why you're breaking
✓ **Avoid complex nested breaks** - refactor into functions
✓ **Update variables properly** - prevent infinite loops
✓ **Test all paths** - ensure break/continue work as intended
✓ **Use meaningful variable names** - makes logic clearer
✓ **Consider refactoring** - break indicates loop complexity
✓ **Use early returns** - simpler than break in functions

## Key Takeaways

✓ **Break** exits the loop completely
✓ **Continue** skips current iteration, goes to next
✓ **Break levels** control which nested loop to exit
✓ **Continue levels** skip iterations in nested loops
✓ **Break in switch** also exits the switch statement
✓ **Both keywords** improve code readability when used properly
✓ **Break/continue in functions** should be reconsidered
✓ **Improper use** causes infinite loops or errors
✓ **Useful for filters** - skip invalid records
✓ **Essential for searches** - exit when found
