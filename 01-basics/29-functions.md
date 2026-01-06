# Functions - Defining, Calling, and Working with Functions

## Table of Contents
1. [What are Functions?](#what-are-functions)
2. [Defining Functions](#defining-functions)
3. [Calling Functions](#calling-functions)
4. [Parameters and Arguments](#parameters-and-arguments)
5. [Return Values](#return-values)
6. [Function Scope](#function-scope)
7. [Type Declarations](#type-declarations)
8. [Variable Functions](#variable-functions)
9. [Common Mistakes](#common-mistakes)

---

## What are Functions?

Functions are reusable blocks of code that perform specific tasks. They:

- **Reduce code duplication** - Write once, use many times
- **Improve readability** - Name describes what it does
- **Enable organization** - Break code into manageable pieces
- **Support testing** - Test individual functions

```php
<?php
// Define a function
function greet($name) {
    return "Hello, $name!";
}

// Call the function
echo greet("Alice");  // Output: Hello, Alice!
echo greet("Bob");    // Output: Hello, Bob!
?>
```

---

## Defining Functions

### Basic Function Structure

```php
<?php
function functionName() {
    // Function body
    echo "This is a function";
}

// Call it
functionName();
?>
```

### Function with Parameters

```php
<?php
function add($a, $b) {
    return $a + $b;
}

$result = add(5, 3);  // 8
?>
```

### Function Naming Rules

- Use **camelCase** (PHP convention)
- Start with a **verb** if it performs an action
- Be **descriptive**

```php
<?php
// ✅ Good names
function calculateTotal($price, $quantity) {}
function getUserData($userId) {}
function sendEmail($to, $subject) {}
function validateEmail($email) {}

// ❌ Poor names
function f($x) {}           // Too vague
function do_something() {}  // Unclear
function x() {}             // Meaningless
?>
```

---

## Calling Functions

### Simple Function Call

```php
<?php
function sayHello() {
    echo "Hello, World!";
}

sayHello();  // Calls the function
?>
```

### Storing Return Value

```php
<?php
function add($a, $b) {
    return $a + $b;
}

$result = add(10, 5);
echo $result;  // 15
?>
```

### Using Function Result Directly

```php
<?php
function getCurrentDate() {
    return date('Y-m-d');
}

echo "Today: " . getCurrentDate();
?>
```

### Calling Before Definition

PHP allows calling functions before they're defined (in the same file):

```php
<?php
// Call function before definition (works!)
echo greet("Alice");

// Define it later
function greet($name) {
    return "Hello, $name";
}
?>
```

---

## Parameters and Arguments

### Parameters vs Arguments

```php
<?php
// Parameters: defined in function signature
function add($a, $b) {  // $a and $b are parameters
    return $a + $b;
}

// Arguments: actual values passed
$result = add(5, 3);   // 5 and 3 are arguments
?>
```

### Multiple Parameters

```php
<?php
function buildUser($firstName, $lastName, $email) {
    return [
        'first' => $firstName,
        'last' => $lastName,
        'email' => $email,
    ];
}

$user = buildUser("Alice", "Smith", "alice@example.com");
?>
```

### Default Parameters

```php
<?php
function greet($name = "Guest") {
    return "Hello, $name!";
}

echo greet();           // Hello, Guest!
echo greet("Alice");    // Hello, Alice!
?>
```

### Variable Number of Arguments

```php
<?php
// Using ... (splat operator)
function sum(...$numbers) {
    $total = 0;
    foreach ($numbers as $num) {
        $total += $num;
    }
    return $total;
}

echo sum(1, 2, 3, 4, 5);     // 15
echo sum(10, 20);             // 30
echo sum(100);                // 100
?>
```

### Pass by Reference

```php
<?php
// Normal: pass by value (copy)
function increment($num) {
    $num++;
    return $num;
}

$x = 5;
increment($x);
echo $x;  // Still 5 (not modified)

// Pass by reference: modify original
function incrementRef(&$num) {
    $num++;
}

$x = 5;
incrementRef($x);
echo $x;  // Now 6 (modified!)
?>
```

---

## Return Values

### Returning Single Values

```php
<?php
function double($number) {
    return $number * 2;
}

echo double(5);  // 10
?>
```

### Returning Multiple Values (Array)

```php
<?php
function getDimensions() {
    return [
        'width' => 100,
        'height' => 50,
        'depth' => 30,
    ];
}

$dims = getDimensions();
echo $dims['width'];  // 100
?>
```

### Returning Nothing

Functions automatically return `NULL` if no return statement:

```php
<?php
function printMessage($msg) {
    echo $msg;
    // No return statement
}

$result = printMessage("Hello");
var_dump($result);  // NULL
?>
```

### Early Return

```php
<?php
function validateUser($user) {
    // Validate and return early if invalid
    if (!isset($user['name'])) {
        return false;
    }
    
    if (strlen($user['name']) < 2) {
        return false;
    }
    
    // Continue with other checks...
    return true;
}
?>
```

---

## Function Scope

Functions have their own scope:

```php
<?php
$global_var = "Global";

function test() {
    $local_var = "Local";
    
    echo $local_var;    // Works
    echo $global_var;   // Error: Undefined
}

test();

echo $local_var;        // Error: Undefined
echo $global_var;       // Works
?>
```

### Accessing Global Variables

```php
<?php
$count = 0;

function increment() {
    global $count;      // Access global
    $count++;
}

increment();
increment();
echo $count;  // 2
?>
```

---

## Type Declarations

### Parameter Type Hints

```php
<?php
declare(strict_types=1);

// Specify parameter types
function add(int $a, int $b): int {
    return $a + $b;
}

echo add(5, 3);         // 8
echo add(5.9, 3);       // Error: float not allowed
?>
```

### Supported Types

```php
<?php
// Scalar types
function processInt(int $x) {}
function processFloat(float $x) {}
function processString(string $x) {}
function processBoolean(bool $x) {}

// Compound types
function processArray(array $items) {}

// Objects
function processUser(User $user) {}

// Nullable types
function processOptional(?string $value) {}

// Union types (PHP 8+)
function process(int|string $value) {}
?>
```

### Return Type Hints

```php
<?php
declare(strict_types=1);

function getName(): string {
    return "Alice";
}

function getAge(): int {
    return 25;
}

function isActive(): bool {
    return true;
}

function getScores(): array {
    return [90, 85, 88];
}
?>
```

---

## Variable Functions

### Calling Function by Name

```php
<?php
function greet($name) {
    return "Hello, $name";
}

$functionName = 'greet';  // Store function name in variable
echo $functionName("Alice");  // Calls greet()
?>
```

### Practical Example

```php
<?php
function add($a, $b) { return $a + $b; }
function subtract($a, $b) { return $a - $b; }
function multiply($a, $b) { return $a * $b; }

function calculate($op, $a, $b) {
    // Determine which function to call
    if ($op === 'add') {
        $func = 'add';
    } elseif ($op === 'subtract') {
        $func = 'subtract';
    } else {
        $func = 'multiply';
    }
    
    // Call the function dynamically
    return $func($a, $b);
}

echo calculate('add', 10, 5);       // 15
echo calculate('subtract', 10, 5);  // 5
echo calculate('multiply', 10, 5);  // 50
?>
```

---

## Common Mistakes

### 1. Forgetting Return Statement

```php
<?php
function add($a, $b) {
    $result = $a + $b;
    // Forgot to return!
}

$sum = add(5, 3);
var_dump($sum);  // NULL - not what we wanted!
?>
```

### 2. Too Many Parameters

```php
<?php
// ❌ Too many parameters
function buildUser($fn, $ln, $e, $p, $c, $st, $z, $ph) {}

// ✅ Better: Use array
function buildUser($data) {
    // $data contains all info
}
?>
```

### 3. Side Effects

```php
<?php
// ❌ Bad: Function has side effects
function increment($num) {
    global $counter;
    $counter++;  // Modifying global state
    return $num + 1;
}

// ✅ Better: No side effects
function add($a, $b) {
    return $a + $b;  // Pure function
}
?>
```

### 4. Not Using Type Hints

```php
<?php
// ❌ No type information
function processData($data) {
    return $data + 10;  // What type is $data?
}

// ✅ Clear types
function processData(int $data): int {
    return $data + 10;
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

// Function with parameters and return type
function calculateGrade(array $scores): string {
    if (empty($scores)) {
        return 'No scores';
    }
    
    $average = array_sum($scores) / count($scores);
    
    if ($average >= 90) return 'A';
    if ($average >= 80) return 'B';
    if ($average >= 70) return 'C';
    if ($average >= 60) return 'D';
    return 'F';
}

// Call with different arguments
$scores1 = [95, 92, 98];
$scores2 = [75, 78, 80];
$scores3 = [65, 68];

echo "Student 1: " . calculateGrade($scores1) . "\n";  // A
echo "Student 2: " . calculateGrade($scores2) . "\n";  // C
echo "Student 3: " . calculateGrade($scores3) . "\n";  // D
?>
```

**Output:**
```
Student 1: A
Student 2: C
Student 3: D
```

---

## Next Steps

✅ Understand functions  
→ Learn [arrays](9-data-type-array.md)  
→ Study [control flow](18-if-statement.md)  
→ Master [OOP](../03-oop/1-intro.md)
