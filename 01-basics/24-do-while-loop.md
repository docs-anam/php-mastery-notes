# Do-While Loop in PHP

## Overview

The do-while loop is similar to a while loop, except the code block executes at least once before the condition is checked. This loop is useful when you want to guarantee at least one execution of the loop body, regardless of the initial condition state.

## Basic Do-While Structure

### Simple Do-While Loop

```php
<?php
// Basic do-while loop
$i = 0;
do {
    echo $i . " ";
    $i++;
} while ($i < 5);
// Output: 0 1 2 3 4

// Always executes at least once
$x = 10;
do {
    echo "This runs at least once even though x is 10\n";
} while ($x < 5);
// Output: This runs at least once even though x is 10

// Counting down with do-while
$countdown = 5;
do {
    echo $countdown . " ";
    $countdown--;
} while ($countdown > 0);
echo "Liftoff!\n";
// Output: 5 4 3 2 1 Liftoff!
?>
```

## Practical Examples

### Menu System

```php
<?php
function displayMenu() {
    do {
        echo "\n=== Menu ===\n";
        echo "1. Start Game\n";
        echo "2. Load Game\n";
        echo "3. Settings\n";
        echo "4. Exit\n";
        echo "Choose option: ";
        
        $choice = 4;  // Simulated user input
        
        switch ($choice) {
            case 1:
                echo "Starting new game...\n";
                break;
            case 2:
                echo "Loading saved game...\n";
                break;
            case 3:
                echo "Showing settings...\n";
                break;
            case 4:
                echo "Thank you for playing!\n";
                return;
        }
    } while (true);
}

displayMenu();
?>
```

### Input Validation

```php
<?php
function getValidScore() {
    do {
        echo "Enter score (0-100): ";
        $score = 85;  // Simulated input
        
        if ($score < 0 || $score > 100) {
            echo "Invalid score. Please enter between 0-100.\n";
        }
    } while ($score < 0 || $score > 100);
    
    return $score;
}

$validScore = getValidScore();
echo "Score accepted: $validScore\n";
?>
```

### Retry Logic

```php
<?php
function attemptLogin($max_attempts = 3) {
    $attempt = 0;
    
    do {
        $attempt++;
        echo "Login attempt $attempt of $max_attempts: ";
        
        $password = "secret123";  // Simulated password check
        
        if ($password === "secret123") {
            echo "Login successful!\n";
            return true;
        } else {
            echo "Invalid password.\n";
        }
    } while ($attempt < $max_attempts);
    
    echo "Maximum login attempts exceeded. Account locked.\n";
    return false;
}

attemptLogin(3);
?>
```

### Ticket Processing

```php
<?php
function processTickets() {
    $total_tickets = 5;
    $current = 1;
    
    do {
        echo "Processing ticket #$current\n";
        // Simulate processing
        $current++;
    } while ($current <= $total_tickets);
    
    echo "All tickets processed!\n";
}

processTickets();
// Output:
// Processing ticket #1
// Processing ticket #2
// ... (all tickets)
// All tickets processed!
?>
```

### Form Submission Retry

```php
<?php
function submitFormWithRetry() {
    $success = false;
    $attempts = 0;
    
    do {
        $attempts++;
        echo "Form submission attempt $attempts\n";
        
        // Simulate form submission
        $network_ok = ($attempts >= 2);  // Succeeds on 2nd try
        
        if ($network_ok) {
            echo "Form submitted successfully!\n";
            $success = true;
        } else {
            echo "Network error. Retrying...\n";
        }
    } while (!$success && $attempts < 3);
    
    if (!$success) {
        echo "Failed to submit form after $attempts attempts.\n";
    }
}

submitFormWithRetry();
?>
```

## Do-While vs While

### Key Differences

```php
<?php
// While - condition checked FIRST
$x = 10;
while ($x < 5) {
    echo "Never executes\n";  // $x < 5 is false, so skips
}

// Do-While - condition checked AFTER
$x = 10;
do {
    echo "Executes at least once\n";  // Body runs first
} while ($x < 5);  // Then condition is checked

// Output:
// Executes at least once
?>
```

### When to Use Do-While

```php
<?php
// Good: Menu systems - show menu at least once
do {
    displayMenu();
    getUserChoice();
} while (!$userWantsToExit);

// Good: Input validation - ask for input at least once
do {
    $input = getUserInput();
    $valid = validateInput($input);
    if (!$valid) echo "Invalid input\n";
} while (!$valid);

// Good: Retry logic - try at least once
do {
    $result = attemptOperation();
    $success = $result !== false;
} while (!$success && $retries < $max_retries);
?>
```

## Common Pitfalls

### Infinite Loop

```php
<?php
// BUG - condition never becomes false
do {
    echo "Infinite loop\n";
    // $x is never updated
} while ($x < 10);

// FIXED - update the variable
do {
    echo $x . " ";
    $x++;
} while ($x < 10);
?>
```

### Forgetting the Condition

```php
<?php
// WRONG - missing semicolon after condition
do {
    echo "Error\n";
} while ($x < 5)  // Missing semicolon

// CORRECT
do {
    echo "Correct\n";
} while ($x < 5);
?>
```

### Logic Error in Condition

```php
<?php
// BUG - loop exits when we want to continue
do {
    echo "Processing\n";
} while ($status == "stop");  // Runs while STOP, not while running!

// FIXED
do {
    echo "Processing\n";
} while ($status != "stop");  // Runs while NOT stop
?>
```

## Best Practices

✓ **Use for guaranteed single execution** of initialization
✓ **Good for menu systems** - display menu at least once
✓ **Good for input validation** - ask for input at least once
✓ **Keep condition readable** - make loop termination clear
✓ **Update condition variables** in loop body
✓ **Add comments** explaining why do-while is needed
✓ **Use for retry logic** - always try once
✓ **Simpler than while(true) with break** for certain cases

## Key Takeaways

✓ **Do-while** executes body at least ONCE before checking condition
✓ **Condition checked AFTER** each iteration (including first)
✓ **Guaranteed execution** of loop body
✓ **Semicolon required** after while condition
✓ **Break exits** the loop immediately
✓ **Continue skips** to next iteration (then checks condition)
✓ **Less common** than while or for loops
✓ **Perfect for menus** and input validation
✓ **Perfect for retry logic** - must try at least once
✓ **Must update condition** to avoid infinite loops
