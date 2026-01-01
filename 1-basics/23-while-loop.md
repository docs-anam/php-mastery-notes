# While Loop in PHP

## Overview

The while loop executes a block of code as long as a specified condition is true. It's ideal when you don't know in advance how many times the loop will run, or when the number of iterations depends on changing conditions rather than a fixed count.

## Basic While Loop Structure

### Simple While Loop

```php
<?php
// Basic while loop
$i = 0;
while ($i < 5) {
    echo $i . " ";  // Output: 0 1 2 3 4
    $i++;
}

// String condition
$status = "processing";
while ($status == "processing") {
    echo "Still processing...\n";
    $status = "done";  // Change condition to exit
}

// Counting down
$count = 5;
while ($count > 0) {
    echo $count . " ";  // Output: 5 4 3 2 1
    $count--;
}
echo "Blastoff!\n";
?>
```

### While Loop with Break

```php
<?php
// Break statement to exit early
$i = 0;
while (true) {  // Infinite loop
    echo $i . " ";
    $i++;
    if ($i == 5) {
        break;  // Exit when condition met
    }
}
// Output: 0 1 2 3 4

// Break on condition
$total = 0;
$price = 50;
$budget = 200;

while ($total + $price <= $budget) {
    $total += $price;
    echo "Purchased. Total: $" . $total . "\n";
    if ($total == $budget) {
        echo "Budget exhausted\n";
        break;
    }
}
?>
```

## Practical Examples

### User Input Processing

```php
<?php
// Process users until empty name found
$users = ["John", "Alice", "Bob", ""];
$i = 0;

while ($i < count($users) && !empty($users[$i])) {
    echo "User " . ($i + 1) . ": " . $users[$i] . "\n";
    $i++;
}
// Output:
// User 1: John
// User 2: Alice
// User 3: Bob
?>
```

### Countdown Timer

```php
<?php
function countdown($start) {
    $current = $start;
    while ($current >= 0) {
        if ($current == 0) {
            echo "Blastoff!\n";
        } else {
            echo $current . "... ";
        }
        $current--;
    }
}

countdown(5);
// Output: 5... 4... 3... 2... 1... 0... Blastoff!
?>
```

### Reading File Until End

```php
<?php
// Simulate reading lines until marker found
$lines = ["line 1", "line 2", "line 3", "END"];
$index = 0;

while ($index < count($lines) && $lines[$index] !== "END") {
    echo $lines[$index] . "\n";
    $index++;
}
echo "File processing complete\n";
// Output:
// line 1
// line 2
// line 3
// File processing complete
?>
```

### Bank Balance Processing

```php
<?php
function processTransactions($balance, $daily_withdrawal) {
    $day = 1;
    while ($balance >= $daily_withdrawal) {
        echo "Day $day: Balance = \$" . $balance . "\n";
        $balance -= $daily_withdrawal;
        $day++;
    }
    echo "Final balance: \$" . $balance . "\n";
}

processTransactions(1000, 100);
// Output:
// Day 1: Balance = $1000
// Day 2: Balance = $900
// ... etc
?>
```

### Password Retry System

```php
<?php
function loginWithRetries($max_attempts = 3) {
    $attempts = 0;
    $logged_in = false;
    
    while ($attempts < $max_attempts && !$logged_in) {
        $attempts++;
        echo "Attempt $attempts: ";
        
        // Simulated password check
        $password = "secret";  // User input would go here
        
        if ($password === "secret") {
            echo "Login successful!\n";
            $logged_in = true;
        } else {
            echo "Invalid password. Try again.\n";
        }
    }
    
    if (!$logged_in) {
        echo "Too many failed attempts. Account locked.\n";
    }
}

loginWithRetries(3);
?>
```

### Data Processing Until Condition

```php
<?php
// Process inventory until stock is low
$inventory = 100;
$daily_sales = 15;
$reorder_point = 20;
$day = 0;

while ($inventory > $reorder_point) {
    $day++;
    $inventory -= $daily_sales;
    echo "Day $day: Inventory = $inventory\n";
}

echo "Reorder required! Current stock: $inventory\n";
// Output shows days until reorder needed
?>
```

## While vs For Loop

### Use While When:
```php
<?php
// Unknown number of iterations
$input = "start";
while ($input != "exit") {
    // Get user input
    $input = "exit";  // Eventually becomes exit
}

// Condition is complex
$tries = 0;
while ($tries < 5 && !$success && $data_valid) {
    // Complex logic
}
?>
```

### Use For When:
```php
<?php
// Known number of iterations
for ($i = 0; $i < 10; $i++) {
    // Loop exactly 10 times
}

// Simple counter-based looping
for ($day = 1; $day <= 30; $day++) {
    // Process 30 days
}
?>
```

## Common Pitfalls

### Infinite Loop

```php
<?php
// WRONG - condition never becomes false
while (true) {
    echo "This runs forever!";
    // No break or condition change
}

// CORRECT - condition changes
while ($count < 10) {
    echo $count;
    $count++;  // Count increases
}

// CORRECT - with break
while (true) {
    if ($user_exits) {
        break;
    }
}
?>
```

### Forgetting to Update Condition Variable

```php
<?php
// BUG - infinite loop
$i = 0;
while ($i < 5) {
    echo $i;  // $i never changes!
}

// FIXED
$i = 0;
while ($i < 5) {
    echo $i;
    $i++;  // Update the condition variable
}
?>
```

### Performance Issues

```php
<?php
// SLOW - count() called every iteration
while ($i < count($large_array)) {
    // process
}

// BETTER - count() once
$count = count($large_array);
while ($i < $count) {
    // process
}

// OR use foreach
foreach ($large_array as $item) {
    // process - simpler and faster
}
?>
```

## Best Practices

✓ **Always update the condition variable** to avoid infinite loops
✓ **Use for loops** when you know the iteration count
✓ **Use while loops** for condition-based iterations
✓ **Add comments** explaining loop termination condition
✓ **Test exit conditions** thoroughly
✓ **Keep loop bodies simple** - extract complex logic to functions
✓ **Use break** to exit early when appropriate
✓ **Avoid nested while loops** - they can be confusing

## Key Takeaways

✓ **While loop** repeats while condition is true
✓ **Condition checked before each iteration** - may not execute at all
✓ **Infinite loops** possible with while(true)
✓ **Break exits** the loop completely
✓ **Continue skips** to next iteration
✓ **Must update condition variable** to avoid infinite loops
✓ **Good for conditional looping** and unknown iteration counts
✓ **Less common than for** for simple counting
✓ **Useful for menu systems** and input validation
✓ **Performance matters** - store count in variable
