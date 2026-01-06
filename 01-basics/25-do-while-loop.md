# Do-While Loop

## Table of Contents
1. [Overview](#overview)
2. [Basic Do-While](#basic-do-while)
3. [Comparison with While](#comparison-with-while)
4. [Practical Use Cases](#practical-use-cases)
5. [Common Patterns](#common-patterns)
6. [Common Mistakes](#common-mistakes)

---

## Overview

The do-while loop executes a block of code at least once, then repeats while a condition is true.

```php
do {
    // Code executes first
} while (condition);
```

**Key difference from while loop**: Code executes before condition is checked.

---

## Basic Do-While

### Guaranteed Execution

```php
<?php
// Executes at least once
$count = 0;
do {
    echo $count . " ";
    $count++;
} while ($count < 3);
// Output: 0 1 2

// Executes even if condition is false
$x = 100;
do {
    echo "This runs once";  // Always executes
} while ($x < 10);  // Condition is false!
// Output: This runs once
?>
```

### Typical Pattern

```php
<?php
do {
    // Get input
    $input = getUserInput();
    
    // Check validity
    if (isValid($input)) {
        // Valid: process and exit
        process($input);
        break;  // Or $valid = true
    } else {
        // Invalid: show error and loop
        echo "Invalid input, try again";
    }
} while (true);
?>
```

---

## Comparison with While

### Key Differences

```php
<?php
// WHILE: checks condition FIRST
$x = 100;
while ($x < 10) {
    echo "Never executes";  // Condition is false
}

// DO-WHILE: executes FIRST, checks condition SECOND
$x = 100;
do {
    echo "Executes once";  // Always runs
} while ($x < 10);  // Condition is false
?>
```

### When to Use Each

```php
<?php
// Use WHILE when you might not need to execute
if (hasData()) {
    while (hasMoreData()) {
        process();
    }
}

// Use DO-WHILE when you always need at least one execution
do {
    $user_input = getInput();
} while (!isValid($user_input));
?>
```

---

## Practical Use Cases

### User Input Validation

```php
<?php
function getValidAge() {
    do {
        echo "Enter age (1-120): ";
        $age = (int)readline();
        
        if ($age < 1 || $age > 120) {
            echo "Invalid age\n";
        }
    } while ($age < 1 || $age > 120);
    
    return $age;
}

$age = getValidAge();
echo "Your age: $age\n";
?>
```

### Menu System

```php
<?php
function interactiveMenu() {
    do {
        echo "\n=== MENU ===\n";
        echo "1. Start\n";
        echo "2. Settings\n";
        echo "3. Exit\n";
        echo "Choice: ";
        
        $choice = readline();
        
        switch ($choice) {
            case '1':
                echo "Starting...\n";
                break;
            case '2':
                echo "Opening settings...\n";
                break;
            case '3':
                echo "Exiting...\n";
                return;  // Exit function
            default:
                echo "Invalid choice\n";
        }
    } while (true);
}

interactiveMenu();
?>
```

### Processing with Retry

```php
<?php
function downloadWithRetry($url, $max_retries = 3) {
    $attempt = 0;
    $success = false;
    
    do {
        try {
            $data = downloadFile($url);
            echo "Download successful\n";
            $success = true;
        } catch (Exception $e) {
            $attempt++;
            echo "Attempt $attempt failed\n";
            
            if ($attempt >= $max_retries) {
                echo "Max retries reached\n";
                return null;
            }
            
            echo "Retrying...\n";
            sleep(1);
        }
    } while (!$success && $attempt < $max_retries);
    
    return $data ?? null;
}
?>
```

### Game Loop

```php
<?php
function playGame() {
    $game_running = true;
    $score = 0;
    
    do {
        // Display game state
        echo "Score: $score\n";
        
        // Get player action
        $action = getPlayerInput();
        
        // Process action
        switch ($action) {
            case 'attack':
                $score += attack();
                break;
            case 'defend':
                defend();
                break;
            case 'quit':
                echo "Final score: $score\n";
                $game_running = false;
                break;
        }
    } while ($game_running);
}

playGame();
?>
```

---

## Common Patterns

### Input Validation Pattern

```php
<?php
// Standard validation pattern
do {
    // Get input
    $input = getUserInput();
    
    // Validate
    $error = validateInput($input);
    
    // Show error if invalid
    if ($error) {
        echo "Error: $error\n";
    }
} while ($error);  // Repeat if error exists

// Result: $input is guaranteed to be valid
?>
```

### Retry Pattern

```php
<?php
// Attempt operation, retry on failure
$success = false;
$attempts = 0;

do {
    try {
        performOperation();
        $success = true;
    } catch (Exception $e) {
        $attempts++;
        echo "Attempt $attempts failed\n";
    }
} while (!$success && $attempts < 3);

if (!$success) {
    echo "Operation failed after 3 attempts\n";
}
?>
```

### Menu Pattern

```php
<?php
// Show menu, get choice, repeat until exit
do {
    displayMenu();
    $choice = getMenuChoice();
    processChoice($choice);
} while ($choice !== 'exit');
?>
```

---

## Common Mistakes

### 1. Infinite Loop

```php
<?php
// ❌ Infinite loop: condition never becomes false
do {
    echo "Loop";
    // Forgot to update condition!
} while (true);

// ✓ Update condition to exit
$count = 0;
do {
    echo "Loop ";
    $count++;
} while ($count < 5);
?>
```

### 2. Wrong Variable Scope

```php
<?php
// ❌ Variable used in while not defined in do block
$valid = true;
do {
    $input = getInput();
    // $valid not updated in loop
} while (!$valid);  // Uses original $valid value

// ✓ Update variable in loop
$valid = true;
do {
    $input = getInput();
    $valid = isValid($input);  // Update it!
} while (!$valid);
?>
```

### 3. Forgetting Semicolon

```php
<?php
// ❌ Syntax error: missing semicolon after while()
do {
    echo "Hello";
} while (true)  // Missing ;

// ✓ Correct syntax
do {
    echo "Hello";
} while (true);
?>
```

### 4. Not Understanding Guaranteed Execution

```php
<?php
// Might assume condition is checked first
$data = null;
do {
    process($data);  // Executes with null!
} while ($data !== null);

// If you need to check first, use while instead
$data = null;
if ($data !== null) {
    do {
        process($data);
    } while ($data !== null);
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class FormValidator {
    private string $name = '';
    private string $email = '';
    private int $age = 0;
    
    public function collectUserData(): void {
        $this->collectName();
        $this->collectEmail();
        $this->collectAge();
    }
    
    private function collectName(): void {
        do {
            echo "Enter name: ";
            $name = trim(readline());
            
            if (strlen($name) < 2) {
                echo "Name must be at least 2 characters\n";
                continue;
            }
            
            if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
                echo "Name can only contain letters\n";
                continue;
            }
            
            $this->name = $name;
            break;
        } while (true);
    }
    
    private function collectEmail(): void {
        do {
            echo "Enter email: ";
            $email = trim(readline());
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Invalid email format\n";
                continue;
            }
            
            $this->email = $email;
            break;
        } while (true);
    }
    
    private function collectAge(): void {
        do {
            echo "Enter age: ";
            $age_input = trim(readline());
            
            if (!is_numeric($age_input)) {
                echo "Age must be a number\n";
                continue;
            }
            
            $age = (int)$age_input;
            
            if ($age < 0 || $age > 150) {
                echo "Age must be between 0 and 150\n";
                continue;
            }
            
            $this->age = $age;
            break;
        } while (true);
    }
    
    public function getData(): array {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'age' => $this->age,
        ];
    }
}

// Usage
$validator = new FormValidator();
$validator->collectUserData();

$data = $validator->getData();
echo "\nData collected:\n";
print_r($data);
?>
```

---

## Next Steps

✅ Understand do-while loops  
→ Learn [while loops](23-while-loop.md)  
→ Study [for loops](22-for-loop.md)  
→ Master [loop control (break/continue)](25-break-and-continue.md)
