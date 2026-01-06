# While and Do-While Loops

## Table of Contents
1. [Overview](#overview)
2. [While Loop](#while-loop)
3. [Do-While Loop](#do-while-loop)
4. [Comparison with For Loop](#comparison-with-for-loop)
5. [Practical Examples](#practical-examples)
6. [Common Mistakes](#common-mistakes)

---

## Overview

While and do-while loops execute code blocks repeatedly based on a condition.

- **while**: Checks condition before executing
- **do-while**: Executes at least once, then checks condition

---

## While Loop

Executes as long as the condition is true.

### Basic While

```php
<?php
// Simple countdown
$count = 5;
while ($count > 0) {
    echo $count . " ";
    $count--;
}
// Output: 5 4 3 2 1

// String iteration
$text = "hello";
$i = 0;
while ($i < strlen($text)) {
    echo $text[$i];
    $i++;
}
// Output: hello
?>
```

### Condition Checking

```php
<?php
// Loop while condition is true
$x = 1;
while ($x <= 5) {
    echo $x . " ";
    $x++;
}
// Output: 1 2 3 4 5

// Boolean condition
$active = true;
while ($active) {
    // Process
    if (someConditionMet()) {
        $active = false;
    }
}

// Multiple conditions
$count = 0;
$max = 10;
while ($count < $max && !$error) {
    // Process
    $count++;
}
?>
```

### Break and Continue

```php
<?php
// Break: exit loop immediately
$i = 0;
while ($i < 10) {
    if ($i === 5) {
        break;  // Exit loop
    }
    echo $i . " ";
    $i++;
}
// Output: 0 1 2 3 4

// Continue: skip to next iteration
$i = 0;
while ($i < 5) {
    $i++;
    if ($i === 2 || $i === 4) {
        continue;  // Skip this iteration
    }
    echo $i . " ";
}
// Output: 1 3 5
?>
```

---

## Do-While Loop

Executes the block first, then checks the condition.

### Basic Do-While

```php
<?php
// Guaranteed to run at least once
$count = 0;
do {
    echo $count . " ";
    $count++;
} while ($count < 5);
// Output: 0 1 2 3 4

// Even if condition is false initially
$x = 10;
do {
    echo "Runs once";
    $x--;
} while ($x > 20);
// Output: Runs once (executes even though $x > 20 is false)
?>
```

### User Input Validation

```php
<?php
// Typical use case: validate input, keep asking until valid
$valid = false;
do {
    echo "Enter a number between 1-10: ";
    $input = readline();
    
    if (is_numeric($input) && $input >= 1 && $input <= 10) {
        $valid = true;
        echo "Valid input!";
    } else {
        echo "Invalid. Try again.\n";
    }
} while (!$valid);
?>
```

---

## Comparison with For Loop

Different loops for different purposes.

### While vs For

```php
<?php
// For loop: fixed iterations, counter managed
for ($i = 0; $i < 5; $i++) {
    echo $i . " ";
}

// While loop: dynamic iterations, manual counter
$i = 0;
while ($i < 5) {
    echo $i . " ";
    $i++;
}

// Both output: 0 1 2 3 4
?>
```

### When to Use Each

```php
<?php
// Use for loop when you know number of iterations
for ($i = 0; $i < 10; $i++) {
    echo $i;
}

// Use while loop when condition is complex
$data = fetchData();
while ($data && $data['continue']) {
    process($data);
    $data = fetchData();
}

// Use do-while for user input validation
do {
    $input = getUserInput();
} while (!isValid($input));
?>
```

---

## Practical Examples

### Reading File Lines

```php
<?php
$file = fopen("data.txt", "r");
if ($file) {
    while (($line = fgets($file)) !== false) {
        echo trim($line) . "\n";
    }
    fclose($file);
}
?>
```

### Database Result Processing

```php
<?php
// Simulated database query
$results = getQueryResults();
$index = 0;

while ($index < count($results)) {
    $row = $results[$index];
    
    echo "Processing: " . $row['name'] . "\n";
    
    // Update record
    updateRecord($row['id']);
    
    $index++;
}
?>
```

### Menu System

```php
<?php
function showMenu() {
    $exit = false;
    
    while (!$exit) {
        echo "\n=== MENU ===\n";
        echo "1. Create\n";
        echo "2. Read\n";
        echo "3. Update\n";
        echo "4. Delete\n";
        echo "5. Exit\n";
        echo "Select: ";
        
        $choice = trim(readline());
        
        switch ($choice) {
            case '1':
                create();
                break;
            case '2':
                read();
                break;
            case '3':
                update();
                break;
            case '4':
                delete();
                break;
            case '5':
                $exit = true;
                break;
            default:
                echo "Invalid choice";
        }
    }
}

showMenu();
?>
```

### API Pagination

```php
<?php
$page = 1;
$has_more = true;
$all_results = [];

while ($has_more) {
    // Fetch page of results
    $response = callAPI("users", ["page" => $page]);
    
    if (!$response['success']) {
        break;
    }
    
    // Add results
    $all_results = array_merge($all_results, $response['data']);
    
    // Check if more pages
    $has_more = $response['has_more'];
    $page++;
}

echo "Total results: " . count($all_results);
?>
```

---

## Common Mistakes

### 1. Infinite Loop

```php
<?php
// ❌ Infinite loop: condition never becomes false
$x = 0;
while ($x < 10) {
    echo $x;
    // Forgot to increment $x!
}

// ✓ Correct: increment to eventually exit
$x = 0;
while ($x < 10) {
    echo $x;
    $x++;
}

// ✓ Or use for loop (clearer)
for ($x = 0; $x < 10; $x++) {
    echo $x;
}
?>
```

### 2. Off-by-One Errors

```php
<?php
// ❌ Wrong count
$i = 1;
while ($i <= 5) {
    echo $i . " ";
    $i++;
}
// Output: 1 2 3 4 5 (5 iterations, correct)

// ✓ Clear boundaries
$i = 0;
while ($i < 5) {
    echo $i . " ";
    $i++;
}
// Output: 0 1 2 3 4 (5 iterations, 0-4)
?>
```

### 3. Not Updating Loop Condition

```php
<?php
// ❌ Infinite loop: $count not updated in loop
$count = 0;
while ($count < 5) {
    echo "Hello";
    // Missing: $count++;
}

// ✓ Update condition variable
$count = 0;
while ($count < 5) {
    echo "Hello";
    $count++;
}
?>
```

### 4. Do-While False Assumption

```php
<?php
// Do-while ALWAYS runs at least once
$x = 100;
do {
    echo "Runs";  // Executes even though $x > 10
} while ($x < 10);

// Make sure this is intended behavior
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class UserInputHandler {
    public function getValidInteger(int $min, int $max): int {
        $valid = false;
        $input = null;
        
        do {
            echo "Enter a number between $min and $max: ";
            $input = trim(readline());
            
            if (!is_numeric($input)) {
                echo "Error: Must be a number\n";
                continue;
            }
            
            $input = (int)$input;
            
            if ($input < $min || $input > $max) {
                echo "Error: Must be between $min and $max\n";
                continue;
            }
            
            $valid = true;
            
        } while (!$valid);
        
        return $input;
    }
    
    public function processDataStream($dataCallback): int {
        $count = 0;
        
        while (true) {
            $data = $dataCallback();
            
            if ($data === null) {
                break;  // No more data
            }
            
            process($data);
            $count++;
        }
        
        return $count;
    }
    
    public function retryOperation($operation, int $max_attempts = 3): bool {
        $attempt = 0;
        
        do {
            try {
                $operation();
                return true;
            } catch (Exception $e) {
                $attempt++;
                if ($attempt >= $max_attempts) {
                    return false;
                }
                sleep(1);  // Wait before retry
            }
        } while ($attempt < $max_attempts);
        
        return false;
    }
}

// Usage
$handler = new UserInputHandler();
$age = $handler->getValidInteger(0, 120);
echo "You entered: $age\n";
?>
```

---

## Next Steps

✅ Understand while and do-while loops  
→ Learn [for loops](22-for-loop.md)  
→ Study [foreach loops](26-for-each-loop.md)  
→ Master [loop control](25-break-and-continue.md)
