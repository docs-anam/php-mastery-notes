# Operators - Increment and Decrement

## Table of Contents
1. [Overview](#overview)
2. [Increment Operator](#increment-operator)
3. [Decrement Operator](#decrement-operator)
4. [Prefix vs Postfix](#prefix-vs-postfix)
5. [With Different Data Types](#with-different-data-types)
6. [Common Patterns](#common-patterns)
7. [Common Mistakes](#common-mistakes)

---

## Overview

Increment (`++`) and decrement (`--`) operators change variable values by 1.

| Operator | Name | Example | Returns |
|----------|------|---------|---------|
| `++$x` | Pre-increment | `++$x` | New value |
| `$x++` | Post-increment | `$x++` | Old value |
| `--$x` | Pre-decrement | `--$x` | New value |
| `$x--` | Post-decrement | `$x--` | Old value |

---

## Increment Operator

Adds 1 to the variable.

### Pre-Increment (++$x)

```php
<?php
$count = 5;
$result = ++$count;  // Increment first, then return

echo $count;    // 6
echo $result;   // 6 (same as $count)
?>
```

### Post-Increment ($x++)

```php
<?php
$count = 5;
$result = $count++;  // Return first, then increment

echo $count;    // 6
echo $result;   // 5 (old value)
?>
```

### Practical Difference

```php
<?php
// Pre-increment: increment first
$items = 0;
if (++$items > 0) {
    echo "Item added";  // Executes: $items is now 1
}

// Post-increment: return old value
$items = 0;
if ($items++ > 0) {
    echo "Item added";  // Doesn't execute: returned 0
}
echo $items;  // 1 (incremented, but check used old value)
?>
```

### Simple Loops

```php
<?php
// Using increment
for ($i = 0; $i < 5; $i++) {
    echo $i . " ";  // 0 1 2 3 4
}

// Infinite loop prevention
$count = 0;
while ($count < 5) {
    echo $count . " ";
    $count++;  // Must increment to exit loop
}
?>
```

---

## Decrement Operator

Subtracts 1 from the variable.

### Pre-Decrement (--$x)

```php
<?php
$count = 5;
$result = --$count;  // Decrement first, then return

echo $count;    // 4
echo $result;   // 4 (same as $count)
?>
```

### Post-Decrement ($x--)

```php
<?php
$count = 5;
$result = $count--;  // Return first, then decrement

echo $count;    // 4
echo $result;   // 5 (old value)
?>
```

### Countdown Example

```php
<?php
// Countdown
$countdown = 5;
while ($countdown > 0) {
    echo $countdown . " ";
    $countdown--;
}
echo "Blastoff!";
// Output: 5 4 3 2 1 Blastoff!

// Using pre-decrement
$countdown = 5;
while (--$countdown >= 0) {
    echo $countdown . " ";
}
// Output: 4 3 2 1 0
?>
```

---

## Prefix vs Postfix

The key difference is what value is returned.

### When Used Alone

```php
<?php
// Makes no difference (return value ignored)
$x = 5;
$x++;     // Same as ++$x
$x--;     // Same as --$x

// Both increment/decrement correctly
$a = 0;
$a++;
echo $a;  // 1

$b = 0;
++$b;
echo $b;  // 1
?>
```

### When Return Value Matters

```php
<?php
// Prefix: returns new value
$items = [];
$items[] = ++$counter;  // Increment, then use

// Postfix: returns old value
$items[] = $counter++;  // Use, then increment

$counter = 5;
$arr1[] = ++$counter;  // Append 6
$arr2[] = $counter++;  // Append 6

echo $counter;  // 7
?>
```

### Performance Consideration

```php
<?php
// In loops, pre-increment is slightly more efficient
// (though modern PHP optimizes this)

// Pre-increment: recommended
for ($i = 0; $i < 1000000; $i++) {
    // Process
}

// Post-increment: also fine
for ($j = 0; $j < 1000000; $j++) {
    // Process
}
?>
```

---

## With Different Data Types

### With Strings

```php
<?php
// PHP has special string increment behavior
$char = 'a';
$char++;
echo $char;  // 'b'

// Increments through alphabet
$letter = 'z';
$letter++;
echo $letter;  // 'aa' (wraps around!)

// With alphanumeric
$code = 'A9';
$code++;
echo $code;  // 'B0'

// Not just numbers
$str = '99';
$str++;
echo $str;  // '100'
?>
```

### With Booleans

```php
<?php
// true and false are treated as 1 and 0
$flag = true;
$flag++;
echo $flag;  // 1 (int)

$bool = false;
$bool++;
echo $bool;  // 1 (int)

// Decrement
$flag = true;
$flag--;
echo $flag;  // 0 (int)
?>
```

### With NULL

```php
<?php
// NULL becomes 1 when incremented
$value = null;
$value++;
echo $value;  // 1

// When decremented
$value = null;
$value--;
echo $value;  // -1
?>
```

### With Arrays

```php
<?php
// Can't increment arrays
$arr = [1, 2, 3];
$arr++;  // Fatal error!

// Instead, manipulate elements
$arr[0]++;
echo $arr[0];  // 2
?>
```

---

## Common Patterns

### Loop Counters

```php
<?php
// Forward loop
for ($i = 0; $i < 10; $i++) {
    echo $i;
}

// Backward loop
for ($i = 10; $i > 0; $i--) {
    echo $i;
}

// Skip by 2
for ($i = 0; $i < 10; $i += 2) {
    echo $i;  // 0 2 4 6 8
}
?>
```

### Counter Accumulation

```php
<?php
$page_views = 0;
$user_clicks = 0;
$form_submissions = 0;

// Track events
while (true) {
    // User views page
    $page_views++;
    
    // User clicks button
    if (rand(0, 1)) {
        $user_clicks++;
    }
    
    // User submits form (less frequent)
    if (rand(0, 5) == 0) {
        $form_submissions++;
        break;  // Exit loop
    }
}

echo "Views: $page_views, Clicks: $user_clicks, Submissions: $form_submissions";
?>
```

### Array Index Management

```php
<?php
$items = ['apple', 'banana', 'cherry'];
$index = 0;

// Process and move to next
while ($index < count($items)) {
    echo $items[$index] . "\n";
    $index++;  // Move to next item
}

// Or with pre-increment
$index = -1;
while (++$index < count($items)) {
    echo $items[$index] . "\n";
}
?>
```

### Skip Zero Pattern

```php
<?php
// Sometimes you want to start counting at 1
$count = 0;

// Method 1: pre-increment in condition
while (++$count <= 5) {
    echo $count . " ";  // 1 2 3 4 5
}

// Method 2: initialize to different value
$count = 1;
while ($count <= 5) {
    echo $count . " ";
    $count++;
}
?>
```

---

## Common Mistakes

### 1. Assuming Prefix/Postfix Don't Matter

```php
<?php
// ❌ Wrong expectation
$queue = [];
$id = 0;

$queue[] = $id++;   // Appends 0
$queue[] = ++$id;   // Appends 2!

print_r($queue);    // [0, 2] - not [0, 1]!

// ✅ Correct
$queue = [];
$id = 0;

$queue[] = $id++;   // Post-increment: use 0, then increment
$queue[] = $id++;   // Post-increment: use 1, then increment

print_r($queue);    // [0, 1]
?>
```

### 2. Increment in Loop Condition

```php
<?php
// ❌ Can be confusing
$items = [];
$count = 0;
while ($count++ < 3) {
    $items[] = $count;  // [1, 2, 3] - not [0, 1, 2]!
}

// ✅ Clearer
$items = [];
$count = 0;
while ($count < 3) {
    $items[] = $count;
    $count++;  // Increment at end
}
// [0, 1, 2]

// ✅ Or use for loop (clearer intent)
$items = [];
for ($i = 0; $i < 3; $i++) {
    $items[] = $i;
}
// [0, 1, 2]
?>
```

### 3. Forgetting to Increment Loop Variable

```php
<?php
// ❌ Infinite loop!
$count = 0;
while ($count < 5) {
    echo $count . " ";
    // Missing: $count++;
}
// This runs forever

// ✅ Always increment
$count = 0;
while ($count < 5) {
    echo $count . " ";
    $count++;
}
?>
```

### 4. Incrementing Beyond Safe Integer

```php
<?php
// ❌ Integer overflow
$num = PHP_INT_MAX;  // Largest integer
$num++;              // Converts to float!

// ✅ Check limits
$max = 1000000;
if ($counter < $max) {
    $counter++;
} else {
    echo "Limit reached";
}

// Or use type checking
if (is_int($counter) && $counter < PHP_INT_MAX) {
    $counter++;
}
?>
```

### 5. Using on Non-Existent Variables

```php
<?php
// ❌ Works but creates variable with value 1
$undefined++;
echo $undefined;  // 1

// ✅ Initialize first
$counter = 0;
$counter++;
echo $counter;  // 1

// ✅ Or use isset check
if (isset($data['count'])) {
    $data['count']++;
} else {
    $data['count'] = 1;
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class Counter {
    private int $count = 0;
    private array $history = [];
    
    public function increment(): int {
        // Pre-increment before recording
        return ++$this->count;
    }
    
    public function decrement(): int {
        // Pre-decrement before recording
        return --$this->count;
    }
    
    public function addHistory(): void {
        // Record current value
        $this->history[] = [
            'count' => $this->count,
            'timestamp' => time()
        ];
    }
    
    public function getCount(): int {
        return $this->count;
    }
    
    public function getHistory(): array {
        return $this->history;
    }
}

// Usage
$counter = new Counter();

// Track increments
for ($i = 0; $i < 5; $i++) {
    $counter->increment();
    $counter->addHistory();
}

// Track decrements
for ($j = 0; $j < 2; $j++) {
    $counter->decrement();
    $counter->addHistory();
}

echo "Final count: " . $counter->getCount() . "\n";  // 3

// Show history
$history = $counter->getHistory();
foreach ($history as $index => $entry) {
    echo "Step " . ($index + 1) . ": " . $entry['count'] . "\n";
}

// Output:
// Step 1: 1
// Step 2: 2
// Step 3: 3
// Step 4: 4
// Step 5: 5
// Step 6: 4
// Step 7: 3
?>
```

---

## Next Steps

✅ Understand increment/decrement operators  
→ Learn [loops (for, while, foreach)](22-for-loop.md)  
→ Study [assignment operators](11-operators-assignment.md)  
→ Master [array operations](9-data-type-array.md)
