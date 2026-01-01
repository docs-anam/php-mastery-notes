# Nested Loops in PHP

## Overview

Nested loops are loops within loops. An inner loop is completely executed for each iteration of the outer loop. This is a powerful technique for working with multi-dimensional arrays, matrices, and complex data structures. However, nested loops can significantly impact performance if not used carefully.

## Basic Nested Loop Structure

### Simple Nested For Loops

```php
<?php
// Basic nested for loops
for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
        echo "($i,$j) ";
    }
    echo "\n";
}
// Output:
// (0,0) (0,1) (0,2)
// (1,0) (1,1) (1,2)
// (2,0) (2,1) (2,2)

// Multiplication table
for ($i = 1; $i <= 5; $i++) {
    for ($j = 1; $j <= 5; $j++) {
        echo str_pad($i * $j, 3) . " ";
    }
    echo "\n";
}

// Triangle pattern
for ($i = 1; $i <= 5; $i++) {
    for ($j = 0; $j < $i; $j++) {
        echo "* ";
    }
    echo "\n";
}
// Output:
// *
// * *
// * * *
// * * * *
// * * * * *
?>
```

### Nested Foreach Loops

```php
<?php
// Nested foreach with arrays
$students = [
    'Class A' => ['John', 'Jane', 'Bob'],
    'Class B' => ['Alice', 'Charlie', 'Diana'],
    'Class C' => ['Eve', 'Frank']
];

foreach ($students as $class => $names) {
    echo "$class:\n";
    foreach ($names as $name) {
        echo "  - $name\n";
    }
}

// Nested arrays with keys
$courses = [
    'Math' => ['Chapter 1' => 45, 'Chapter 2' => 60],
    'Physics' => ['Chapter 1' => 30, 'Chapter 2' => 50]
];

foreach ($courses as $subject => $chapters) {
    echo "$subject:\n";
    foreach ($chapters as $chapter => $pages) {
        echo "  $chapter: $pages pages\n";
    }
}
?>
```

## Practical Examples

### Processing 2D Arrays (Matrices)

```php
<?php
function processMatrix($matrix) {
    $result = 0;
    
    foreach ($matrix as $row) {
        foreach ($row as $value) {
            $result += $value;
        }
    }
    
    return $result;
}

$matrix = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9]
];

echo "Sum of all elements: " . processMatrix($matrix) . "\n";
// Output: Sum of all elements: 45
?>
```

### Printing CSV/Table Data

```php
<?php
function printTable($headers, $data) {
    echo "┌" . str_repeat("────┬", count($headers) - 1) . "────┐\n";
    
    // Print headers
    echo "│ ";
    foreach ($headers as $header) {
        echo str_pad($header, 4) . " │ ";
    }
    echo "\n";
    echo "├" . str_repeat("────┼", count($headers) - 1) . "────┤\n";
    
    // Print data rows
    foreach ($data as $row) {
        echo "│ ";
        foreach ($row as $value) {
            echo str_pad($value, 4) . " │ ";
        }
        echo "\n";
    }
    
    echo "└" . str_repeat("────┴", count($headers) - 1) . "────┘\n";
}

$headers = ['Name', 'Age', 'City'];
$data = [
    ['John', '30', 'NYC'],
    ['Jane', '28', 'LA'],
    ['Bob', '35', 'CHI']
];

printTable($headers, $data);
?>
```

### Finding Element in Matrix

```php
<?php
function findInMatrix($matrix, $target) {
    foreach ($matrix as $row => $values) {
        foreach ($values as $col => $value) {
            if ($value == $target) {
                return "Found at [$row][$col]";
            }
        }
    }
    return "Not found";
}

$matrix = [
    [10, 20, 30],
    [40, 50, 60],
    [70, 80, 90]
];

echo findInMatrix($matrix, 50) . "\n";
// Output: Found at [1][1]
?>
```

### Building Product Combinations

```php
<?php
function createCombinations($colors, $sizes) {
    $combinations = [];
    
    foreach ($colors as $color) {
        foreach ($sizes as $size) {
            $combinations[] = "{$color} - {$size}";
        }
    }
    
    return $combinations;
}

$colors = ['Red', 'Blue', 'Green'];
$sizes = ['S', 'M', 'L', 'XL'];

$products = createCombinations($colors, $sizes);

foreach ($products as $product) {
    echo $product . "\n";
}
// Output: All color-size combinations
?>
```

### Hierarchical Data Processing

```php
<?php
function printOrganization($departments) {
    foreach ($departments as $dept_name => $dept_data) {
        echo "Department: $dept_name\n";
        
        foreach ($dept_data['teams'] as $team_name => $members) {
            echo "  Team: $team_name\n";
            foreach ($members as $member) {
                echo "    - $member\n";
            }
        }
    }
}

$org = [
    'Engineering' => [
        'teams' => [
            'Backend' => ['Alice', 'Bob'],
            'Frontend' => ['Charlie', 'Diana']
        ]
    ],
    'Sales' => [
        'teams' => [
            'Enterprise' => ['Eve'],
            'SMB' => ['Frank', 'Grace']
        ]
    ]
];

printOrganization($org);
?>
```

## Break and Continue in Nested Loops

### Using Break Levels

```php
<?php
// Break only inner loop
for ($i = 0; $i < 3; $i++) {
    echo "Outer: $i\n";
    for ($j = 0; $j < 3; $j++) {
        if ($j == 1) {
            break;  // Exits only inner loop
        }
        echo "  Inner: $j\n";
    }
}

// Break both loops
for ($i = 0; $i < 3; $i++) {
    echo "Outer: $i\n";
    for ($j = 0; $j < 3; $j++) {
        if ($i == 1 && $j == 1) {
            break 2;  // Exits both loops
        }
        echo "  Inner: $j\n";
    }
}

// Continue with levels
for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
        if ($j == 1) {
            continue 2;  // Skip to next outer iteration
        }
        echo "($i,$j) ";
    }
}
?>
```

## Performance Considerations

### Time Complexity

```php
<?php
// O(n²) complexity
for ($i = 0; $i < 1000; $i++) {
    for ($j = 0; $j < 1000; $j++) {
        // This block runs 1,000,000 times!
        // Be careful with nested loops on large datasets
    }
}

// Better approach - use array_map or array_filter
$data = range(1, 1000);

// Instead of nested foreach
$result = array_map(function($item) {
    return $item * 2;
}, $data);
?>
```

### Optimization Techniques

```php
<?php
// Bad: Repeated function calls in loops
for ($i = 0; $i < 100; $i++) {
    for ($j = 0; $j < count($array); $j++) {
        // count() called 10,000 times!
    }
}

// Better: Cache the count
$count = count($array);
for ($i = 0; $i < 100; $i++) {
    for ($j = 0; $j < $count; $j++) {
        // Much faster
    }
}

// Best: Use foreach for arrays
foreach ($array as $item) {
    // No need to count, simpler and faster
}
?>
```

## Common Pitfalls

### Wrong Variable Tracking

```php
<?php
// BUG - using same variable
for ($i = 0; $i < 3; $i++) {
    for ($i = 0; $i < 3; $i++) {  // Overwrites outer $i
        // Confusion and bugs!
    }
}

// FIXED - different variables
for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
        echo "($i,$j) ";
    }
}
?>
```

### Forgetting Array Bounds

```php
<?php
// BUG - accessing non-existent array elements
for ($i = 0; $i < 5; $i++) {
    for ($j = 0; $j < 5; $j++) {
        echo $matrix[$i][$j];  // May not exist!
    }
}

// FIXED - verify array structure
foreach ($matrix as $row) {
    foreach ($row as $value) {
        echo $value;  // Safe iteration
    }
}
?>
```

### Performance Degradation

```php
<?php
// BAD - O(n³) or worse
for ($i = 0; $i < $n; $i++) {
    for ($j = 0; $j < $n; $j++) {
        for ($k = 0; $k < $n; $k++) {
            // Extremely slow for large n
        }
    }
}

// BETTER - reduce nesting when possible
// Or use appropriate data structures
// Or consider database queries for large datasets
?>
```

## Best Practices

✓ **Keep nesting shallow** - 2-3 levels maximum
✓ **Use foreach** for array iteration
✓ **Cache counts** - don't call count() in loop
✓ **Use meaningful variable names** - $row, $col not $i, $k
✓ **Comment complex logic** - explain why nested
✓ **Test edge cases** - empty arrays, single elements
✓ **Consider performance** - nested loops O(n²) or worse
✓ **Refactor deep nesting** - extract to functions
✓ **Use break/continue levels carefully** - can be confusing
✓ **Profile code** - identify bottlenecks

## Key Takeaways

✓ **Nested loops** execute inner loop for each outer iteration
✓ **Syntax** - same as single loops, just indented
✓ **Foreach** is preferred for array traversal
✓ **Variable names** must be different for each level
✓ **Break levels** control which loop to exit
✓ **Performance** can degrade quickly (O(n²) or worse)
✓ **Common use** - matrices, product combinations, hierarchies
✓ **Matrix access** - $matrix[$row][$col]
✓ **Iteration** - foreach is safer than indexed for
✓ **Avoid triple nesting** - refactor for readability
