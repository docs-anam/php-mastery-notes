# Expressions, Statements, and Blocks

## Table of Contents
1. [Overview](#overview)
2. [Expressions](#expressions)
3. [Statements](#statements)
4. [Blocks](#blocks)
5. [Code Structure](#code-structure)
6. [Common Patterns](#common-patterns)
7. [Common Mistakes](#common-mistakes)

---

## Overview

Understanding the difference between expressions, statements, and blocks is fundamental to PHP programming.

- **Expression**: Code that produces a value
- **Statement**: Complete instruction that performs an action
- **Block**: Group of statements enclosed in curly braces

---

## Expressions

An expression is any piece of code that evaluates to a value.

### Simple Expressions

```php
<?php
// Literal expressions
5;                  // Evaluates to 5
"hello";           // Evaluates to "hello"
true;              // Evaluates to true

// Variable expressions
$x;                // Evaluates to value of $x
$y = 10;           // Assignment expression evaluates to 10

// Arithmetic expressions
5 + 3;             // Evaluates to 8
10 * 2;            // Evaluates to 20
$a - $b;           // Evaluates to difference

// String expressions
"Hello" . " " . "World";  // Evaluates to "Hello World"
$name . "!";              // Concatenates $name with "!"
?>
```

### Function Call Expressions

```php
<?php
// Function calls are expressions
strlen("hello");   // Evaluates to 5
array_sum([1, 2, 3]);  // Evaluates to 6
strtoupper("test");     // Evaluates to "TEST"

// Return value matters
$length = strlen("PHP");  // $length = 3

// Can be nested
echo strlen(strtoupper("hello"));  // Output: 5
?>
```

### Logical Expressions

```php
<?php
// Boolean expressions
true && true;      // Evaluates to true
5 > 3;            // Evaluates to true
$x == $y;         // Evaluates to true or false

// Short-circuit expressions
$x || $y;         // Stops evaluating if $x is true
$a && $b;         // Stops evaluating if $a is false
?>
```

### Ternary and Null Coalescing

```php
<?php
// Ternary expression
$age >= 18 ? "Adult" : "Minor";

// Null coalescing expression
$name ?? "Guest";

// Both produce values
$status = $is_active ? "Active" : "Inactive";
$username = $input['username'] ?? "Anonymous";
?>
```

---

## Statements

A statement is a complete instruction that tells PHP to do something.

### Simple Statements

```php
<?php
// Declaration statements
$x = 5;           // Variable declaration
define("NAME", "John");  // Constant declaration

// Expression statements
echo "Hello";     // Expression with side effect
$count++;         // Increment statement

// Empty statement
;                 // Valid but does nothing
?>
```

### Control Structure Statements

```php
<?php
// Conditional statements
if ($condition) {
    echo "True";
}

// Loop statements
for ($i = 0; $i < 10; $i++) {
    echo $i;
}

// Jump statements
break;            // Exit loop
continue;         // Skip to next iteration
return $value;    // Exit function
?>
```

### Compound Statements

```php
<?php
// Multiple statements together
$x = 5;
$y = 10;
echo $x + $y;

// Statements in blocks
if ($x > 0) {
    $result = "positive";
    echo $result;
    $count++;
}
?>
```

---

## Blocks

A block is a group of statements enclosed in curly braces `{}`.

### Basic Blocks

```php
<?php
// If block
if ($condition) {
    // Block of statements
    $x = 5;
    $y = 10;
    echo $x + $y;
}

// Loop block
for ($i = 0; $i < 5; $i++) {
    // Block statements
    echo $i;
    $total += $i;
}

// Function block
function calculate() {
    // Block of statements
    $result = 10 + 5;
    return $result;
}
?>
```

### Nested Blocks

```php
<?php
// Blocks within blocks
if ($age >= 18) {
    if ($has_license) {
        if ($is_sober) {
            echo "Can drive";
        } else {
            echo "Cannot drive (not sober)";
        }
    } else {
        echo "No license";
    }
} else {
    echo "Too young";
}

// Better with logical operators
if ($age >= 18 && $has_license && $is_sober) {
    echo "Can drive";
}
?>
```

### Scope and Blocks

```php
<?php
// Variables defined in blocks
$global = "global";

if (true) {
    $local = "local";  // Accessible in this block
    echo $global;      // Can access global variables
}

// echo $local;  // Error! Not accessible here

// Function scope
function test() {
    $function_var = "function";  // Only in this function
}

echo $function_var;  // Error! Not accessible
?>
```

---

## Code Structure

### Statement Separator

Statements are separated by semicolons.

```php
<?php
// Each statement ends with ;
$x = 5;
$y = 10;
$z = $x + $y;
echo $z;

// Last statement before closing tag doesn't need ;
echo "done"
?>
```

### Whitespace and Formatting

```php
<?php
// Whitespace doesn't matter
$x=5;$y=10;echo $x+$y;  // Valid but hard to read

// Better formatting
$x = 5;
$y = 10;
echo $x + $y;

// Multiple lines don't matter
$result = 
    $value1 + 
    $value2 + 
    $value3;
?>
```

### Indentation

```php
<?php
// Proper indentation improves readability
function process() {
    if ($condition) {
        for ($i = 0; $i < 10; $i++) {
            echo $i;  // Indented 3 levels
        }
    }
}
?>
```

---

## Common Patterns

### Expression as Statement

```php
<?php
// Expression statements (expression followed by ;)
5 + 3;                  // Evaluated but result discarded
strlen("hello");        // Function called, result discarded
$x++;                   // Incremented but result discarded

// Useful only for side effects
echo "Hello";           // Side effect: outputs text
$count++;               // Side effect: increments variable
$data = fetchData();    // Side effect: calls function
?>
```

### Compound Expressions

```php
<?php
// Multiple operators in one expression
$result = ($a + $b) * $c - $d / $e;

// Multiple function calls
$text = strtoupper(trim($input));

// Method chaining
$text = $obj->method1()->method2()->method3();
?>
```

### Statement Blocks in Practice

```php
<?php
// Typical control flow structure
if ($user_authenticated) {
    // Block 1: Load user data
    $user = getUserById($id);
    $preferences = getUserPreferences($id);
    
    // Block 2: Process data
    $settings = array_merge($defaults, $preferences);
    
    // Block 3: Output
    echo renderTemplate('user', $user);
} else {
    // Alternative block
    header("Location: /login");
}
?>
```

---

## Common Mistakes

### 1. Missing Semicolons

```php
<?php
// ❌ Error: missing semicolon
$x = 5
$y = 10;

// ✓ Correct
$x = 5;
$y = 10;

// Exception: no semicolon needed before closing brace in some contexts
if ($condition) {
    $x = 5  // OK, next line has statement
}
?>
```

### 2. Block vs Single Statement

```php
<?php
// ❌ Confusing: missing braces
if ($condition)
    $x = 5;
    $y = 10;  // This executes regardless of condition!

// ✓ Correct: always use braces
if ($condition) {
    $x = 5;
    $y = 10;
}

// ✓ Single statement (acceptable but inconsistent)
if ($condition)
    $x = 5;
?>
```

### 3. Scope Confusion

```php
<?php
// ❌ Confused scope
if ($condition) {
    $value = 5;
}
echo $value;  // Works! (variable is accessible)

// Note: PHP has function scope, not block scope
for ($i = 0; $i < 5; $i++) {
    // $i is accessible here
}
echo $i;  // 4 (still accessible after loop!)

// Function scope
function test() {
    $local = 5;
}
// echo $local;  // Error!
?>
```

### 4. Expression vs Statement

```php
<?php
// ❌ Forgetting semicolon (expression becomes invalid statement)
echo "Hello"  // Missing ;
$x = 5;       // Parse error

// ✓ Correct
echo "Hello";
$x = 5;

// ❌ Expression on same line confuses intent
$x = 5; 10;   // Valid but confusing

// ✓ Clear intent
$x = 5;
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class DataProcessor {
    private array $data = [];
    
    public function process($input) {
        // Block 1: Validate input
        if (empty($input)) {
            return ['error' => 'Input cannot be empty'];
        }
        
        // Block 2: Transform data
        $processed = array_map(function($item) {
            return strtoupper(trim($item));
        }, $input);
        
        // Block 3: Filter results
        $filtered = array_filter($processed, function($item) {
            return strlen($item) > 0;
        });
        
        // Block 4: Build response
        if (count($filtered) === 0) {
            return ['error' => 'No valid data'];
        }
        
        return [
            'success' => true,
            'count' => count($filtered),
            'data' => $filtered
        ];
    }
    
    public function validate($data) {
        // Expression chain
        return is_array($data) && 
               count($data) > 0 && 
               isset($data[0]);
    }
}

// Usage
$processor = new DataProcessor();
$result = $processor->process(['  hello  ', '', '  WORLD  ']);
print_r($result);
?>
```

---

## Next Steps

✅ Understand expressions, statements, and blocks  
→ Learn [control flow](18-if-statement.md)  
→ Study [loops](22-for-loop.md)  
→ Master [functions](28-functions.md)
