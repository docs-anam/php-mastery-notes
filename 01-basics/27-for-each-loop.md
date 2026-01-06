# Foreach Loop

## Table of Contents
1. [Overview](#overview)
2. [Basic Syntax](#basic-syntax)
3. [Working with Values](#working-with-values)
4. [Working with Keys](#working-with-keys)
5. [Reference Values](#reference-values)
6. [Nested Foreach](#nested-foreach)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)

---

## Overview

The `foreach` loop iterates through arrays and objects. It's simpler than `for` loops for arrays.

```php
foreach ($array as $value) {
    // Process $value
}

foreach ($array as $key => $value) {
    // Process $key and $value
}
```

---

## Basic Syntax

### Iterating Values

```php
<?php
$fruits = ['apple', 'banana', 'cherry'];

foreach ($fruits as $fruit) {
    echo $fruit . " ";  // apple banana cherry
}

// With index (but key not available)
$numbers = [10, 20, 30];
foreach ($numbers as $number) {
    echo $number . " ";  // 10 20 30
}
?>
```

### Iterating Key and Value

```php
<?php
$person = [
    'name' => 'John',
    'age' => 30,
    'email' => 'john@example.com',
];

foreach ($person as $key => $value) {
    echo "$key: $value\n";
}
// Output:
// name: John
// age: 30
// email: john@example.com
?>
```

---

## Working with Values

### Simple Value Iteration

```php
<?php
$items = ['a', 'b', 'c', 'd'];

foreach ($items as $item) {
    echo ucfirst($item);  // A B C D
}

// Count iterations
$items = [1, 2, 3, 4, 5];
$count = 0;
foreach ($items as $item) {
    $count++;
}
echo "Total: $count";  // Total: 5
?>
```

### Building Results

```php
<?php
$prices = [10.99, 20.50, 15.75];
$total = 0;

foreach ($prices as $price) {
    $total += $price;
}

echo "Total: $" . number_format($total, 2);  // Total: $47.24
?>
```

### Transforming Values

```php
<?php
$original = ['hello', 'world', 'php'];
$uppercase = [];

foreach ($original as $word) {
    $uppercase[] = strtoupper($word);
}

print_r($uppercase);
// Array ( [0] => HELLO [1] => WORLD [2] => PHP )
?>
```

---

## Working with Keys

### Key and Value Together

```php
<?php
$user = [
    'id' => 1,
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'role' => 'admin',
];

foreach ($user as $key => $value) {
    echo "$key: $value\n";
}
?>
```

### Using Keys for Logic

```php
<?php
$config = [
    'host' => 'localhost',
    'port' => 3306,
    'username' => 'root',
];

foreach ($config as $setting => $value) {
    if ($setting === 'username' && $value === 'root') {
        echo "Warning: Default username detected!";
    }
}
?>
```

### Numeric Keys

```php
<?php
$items = ['apple', 'banana', 'cherry'];

foreach ($items as $index => $item) {
    echo "Item " . ($index + 1) . ": $item\n";
}
// Output:
// Item 1: apple
// Item 2: banana
// Item 3: cherry
?>
```

---

## Reference Values

Using `&` allows you to modify array values directly.

### Reference Modification

```php
<?php
$numbers = [1, 2, 3, 4, 5];

// Without reference (copy values)
foreach ($numbers as $number) {
    $number = $number * 2;  // Modifies copy, not original
}
echo implode(', ', $numbers);  // 1, 2, 3, 4, 5 (unchanged)

// With reference (modify original)
foreach ($numbers as &$number) {
    $number = $number * 2;  // Modifies original
}
echo implode(', ', $numbers);  // 2, 4, 6, 8, 10
?>
```

### Careful with References

```php
<?php
$data = ['a', 'b', 'c'];

// Using reference
foreach ($data as &$item) {
    $item = strtoupper($item);  // A, B, C
}
unset($item);  // IMPORTANT: unset the reference!

// Without unset(), $item still references last array element
foreach ($data as $item) {
    echo $item;  // A B C
}

// Now $data is ['A', 'B', 'C']
?>
```

### Modifying Nested Arrays

```php
<?php
$users = [
    ['name' => 'John', 'active' => false],
    ['name' => 'Jane', 'active' => false],
    ['name' => 'Bob', 'active' => false],
];

// Activate all users
foreach ($users as &$user) {
    $user['active'] = true;
}
unset($user);  // Unset reference

print_r($users);
// All users now have active => true
?>
```

---

## Nested Foreach

Loops within loops.

### Two-Level Nesting

```php
<?php
$students = [
    'John' => [85, 90, 88],
    'Jane' => [92, 95, 93],
    'Bob' => [78, 82, 80],
];

foreach ($students as $name => $scores) {
    $average = array_sum($scores) / count($scores);
    echo "$name: " . number_format($average, 1) . "\n";
}
?>
```

### Building HTML Tables

```php
<?php
$data = [
    ['id' => 1, 'name' => 'Alice', 'score' => 95],
    ['id' => 2, 'name' => 'Bob', 'score' => 87],
    ['id' => 3, 'name' => 'Charlie', 'score' => 92],
];

echo "<table border='1'>";
foreach ($data as $row) {
    echo "<tr>";
    foreach ($row as $cell) {
        echo "<td>$cell</td>";
    }
    echo "</tr>";
}
echo "</table>";
?>
```

### Multidimensional Navigation

```php
<?php
$matrix = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9],
];

foreach ($matrix as $row) {
    foreach ($row as $value) {
        echo "$value ";  // 1 2 3 4 5 6 7 8 9
    }
}
?>
```

---

## Practical Examples

### Processing Form Data

```php
<?php
// Simulate form data
$_POST = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => '25',
    'country' => 'USA',
];

function processForm($data) {
    $cleaned = [];
    
    foreach ($data as $key => $value) {
        // Remove whitespace
        $value = trim($value);
        
        // Sanitize
        $value = htmlspecialchars($value, ENT_QUOTES);
        
        $cleaned[$key] = $value;
    }
    
    return $cleaned;
}

$cleaned = processForm($_POST);
print_r($cleaned);
?>
```

### Applying Discounts

```php
<?php
$cart = [
    ['product' => 'Laptop', 'price' => 999.99, 'qty' => 1],
    ['product' => 'Mouse', 'price' => 29.99, 'qty' => 2],
    ['product' => 'Keyboard', 'price' => 79.99, 'qty' => 1],
];

$discount = 0.10;  // 10% discount
$total = 0;

foreach ($cart as &$item) {
    $original = $item['price'] * $item['qty'];
    $item['discount_amount'] = $original * $discount;
    $item['final_price'] = $original - $item['discount_amount'];
    $total += $item['final_price'];
}
unset($item);

echo "Subtotal: $" . number_format($total / (1 - $discount), 2) . "\n";
echo "Discount: $" . number_format($total * $discount / (1 - $discount), 2) . "\n";
echo "Total: $" . number_format($total, 2) . "\n";
?>
```

### Filtering and Mapping

```php
<?php
$scores = [85, 92, 78, 95, 88, 91, 76];

// Filter: only scores above 80
$high_scores = [];
foreach ($scores as $score) {
    if ($score >= 80) {
        $high_scores[] = $score;
    }
}

echo "High scores: " . implode(', ', $high_scores);  // 85, 92, 95, 88, 91

// Map: convert to grades
$grades = [];
foreach ($scores as $score) {
    if ($score >= 90) {
        $grades[] = 'A';
    } elseif ($score >= 80) {
        $grades[] = 'B';
    } elseif ($score >= 70) {
        $grades[] = 'C';
    } else {
        $grades[] = 'F';
    }
}

echo "Grades: " . implode(', ', $grades);  // A, A, C, A, B, A, C
?>
```

### Validation

```php
<?php
function validateUsers($users) {
    $errors = [];
    
    foreach ($users as $index => $user) {
        $user_errors = [];
        
        // Validate each field
        if (empty($user['name'])) {
            $user_errors[] = "Name is required";
        }
        
        if (empty($user['email']) || !filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            $user_errors[] = "Valid email is required";
        }
        
        if ($user['age'] < 18 || $user['age'] > 120) {
            $user_errors[] = "Age must be 18-120";
        }
        
        if ($user_errors) {
            $errors["user_$index"] = $user_errors;
        }
    }
    
    return $errors;
}

$users = [
    ['name' => 'John', 'email' => 'john@example.com', 'age' => 25],
    ['name' => '', 'email' => 'invalid', 'age' => 15],
];

$errors = validateUsers($users);
print_r($errors);
?>
```

---

## Common Mistakes

### 1. Modifying Array While Iterating

```php
<?php
// ❌ Dangerous: modifying array during iteration
$items = ['a', 'b', 'c', 'd'];
foreach ($items as $item) {
    if ($item === 'b') {
        unset($items[1]);  // Removes element
    }
}
// Unpredictable behavior!

// ✓ Safe: collect items to remove, then remove
$items = ['a', 'b', 'c', 'd'];
$to_remove = [];
foreach ($items as $index => $item) {
    if ($item === 'b') {
        $to_remove[] = $index;
    }
}
foreach ($to_remove as $index) {
    unset($items[$index]);
}
?>
```

### 2. Forgetting to Unset Reference

```php
<?php
// ❌ Reference lingers
$array = [1, 2, 3];
foreach ($array as &$value) {
    $value = $value * 2;
}
// $value still references last element!

$another = [10, 20, 30];
foreach ($another as $item) {
    echo $item;  // Works, but $value is still active
}

// ✓ Always unset reference
foreach ($array as &$value) {
    $value = $value * 2;
}
unset($value);  // Unset the reference
?>
```

### 3. Confusion with Continue/Break

```php
<?php
$items = [1, 2, 3, 4, 5];

foreach ($items as $item) {
    if ($item === 2) {
        continue;  // Skip item 2
    }
    if ($item === 4) {
        break;  // Stop at item 4
    }
    echo $item . " ";  // 1 3
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class OrderProcessor {
    private array $orders;
    
    public function __construct(array $orders = []) {
        $this->orders = $orders;
    }
    
    public function calculateTotals(): array {
        $results = [];
        
        foreach ($this->orders as $order_id => &$order) {
            $subtotal = 0;
            
            // Calculate items total
            foreach ($order['items'] as &$item) {
                $item['total'] = $item['price'] * $item['quantity'];
                $subtotal += $item['total'];
            }
            unset($item);
            
            // Apply taxes and shipping
            $tax = $subtotal * 0.08;
            $shipping = $subtotal > 100 ? 0 : 10;
            
            $order['subtotal'] = $subtotal;
            $order['tax'] = $tax;
            $order['shipping'] = $shipping;
            $order['total'] = $subtotal + $tax + $shipping;
            
            $results[$order_id] = $order['total'];
        }
        unset($order);
        
        return $results;
    }
    
    public function getOrders(): array {
        return $this->orders;
    }
}

// Usage
$orders = [
    'ORD001' => [
        'customer' => 'John',
        'items' => [
            ['product' => 'Laptop', 'price' => 999.99, 'quantity' => 1],
            ['product' => 'Mouse', 'price' => 29.99, 'quantity' => 2],
        ],
    ],
    'ORD002' => [
        'customer' => 'Jane',
        'items' => [
            ['product' => 'Monitor', 'price' => 299.99, 'quantity' => 1],
        ],
    ],
];

$processor = new OrderProcessor($orders);
$totals = $processor->calculateTotals();

echo "Order Totals:\n";
foreach ($totals as $order_id => $total) {
    echo "$order_id: $" . number_format($total, 2) . "\n";
}

// Show detailed orders
$orders_data = $processor->getOrders();
foreach ($orders_data as $order_id => $order) {
    echo "\nOrder: $order_id\n";
    echo "Total: $" . number_format($order['total'], 2) . "\n";
}
?>
```

---

## Next Steps

✅ Understand foreach loops  
→ Learn [for loops](22-for-loop.md)  
→ Study [arrays](9-data-type-array.md)  
→ Master [loop control](25-break-and-continue.md)
