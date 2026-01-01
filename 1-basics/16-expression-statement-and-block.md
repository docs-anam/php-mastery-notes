# Expressions, Statements, and Code Blocks in PHP

## Understanding the Basics

Before diving into control structures, it's crucial to understand three fundamental concepts: expressions, statements, and blocks. These form the foundation of all PHP code.

## What is an Expression?

An expression is a combination of values, variables, and operators that evaluates to a result. Expressions produce a value that can be used elsewhere in your code.

```php
<?php
// Simple expressions
5 + 3;                    // Expression evaluates to 8
$x = 10;                  // Expression evaluates to 10
$y = $x + 5;              // Expression evaluates to 15
"Hello " . "World";       // Expression evaluates to "Hello World"

// Expressions can be complex
($a + $b) * ($c - $d);
true && false;
$x > 10 && $y < 20;

// Function calls are expressions
strlen("hello");          // Expression evaluates to 5
array_sum([1, 2, 3]);     // Expression evaluates to 6

// Assignment is an expression
$result = ($x = 5) + 3;   // $x = 5, then adds 3, result is 8
echo ($result);           // Expression echoes value
?>
```

## What is a Statement?

A statement is a complete instruction that performs an action. Statements typically end with a semicolon and instruct the program to do something specific. A statement can contain one or more expressions.

```php
<?php
// Simple statements
$x = 5;                   // Assignment statement
echo $x;                  // Output statement
$x++;                     // Increment statement

// Compound statements (contain other statements)
if ($x > 0) {
    echo "Positive";      // Statement inside if
}

// Loop statements
for ($i = 0; $i < 10; $i++) {
    echo $i;              // Statements inside loop
}

// Function call statement
calculate($a, $b);
?>
```

## What is a Block?

A block (or code block) is a group of statements enclosed in curly braces `{}`. Blocks are used to group statements together, especially in control structures and functions.

```php
<?php
// Basic block
{
    $x = 5;
    $y = 10;
    echo $x + $y;
}

// If block
if ($condition) {
    // Statements in this block execute if condition is true
    $result = "yes";
    echo $result;
}

// Function block
function greet() {
    // All statements inside braces form the function block
    echo "Hello";
    return true;
}

// Loop block
while ($count < 10) {
    // Statements in this block repeat
    $count++;
    echo $count;
}

// Class block
class User {
    // Properties and methods in this block define the class
    public $name;
    public function getName() {
        return $this->name;
    }
}
?>
```

## Block Scope

Variables defined in a block are generally accessible throughout the block and nested blocks, but not before the block or outside it (with exceptions).

```php
<?php
// Variable before block
$x = 5;

// Block
{
    $y = 10;              // Defined in block
    echo $x;              // Can access $x (5)
    echo $y;              // Can access $y (10)
}

echo $x;                  // Can access $x (still 5)
// echo $y;              // ERROR! $y not accessible outside block

// Nested blocks
if (true) {
    $a = 1;
    if (true) {
        $b = 2;
        echo $a;          // Can access $a from outer block
        echo $b;          // Can access $b from this block
    }
    // echo $b;          // ERROR! $b not accessible here
}
?>
```

## Expression vs Statement Examples

```php
<?php
// Expression (produces a value)
$x + 5;

// Statement (expression with semicolon)
$x + 5;                   // Valid but does nothing
$y = $x + 5;              // More useful: assigns expression result

// Some statements without expressions
if ($x > 0) { }
while ($x < 10) { }
function test() { }

// Assignment statement (contains expression)
$result = calculate($a, $b);  // Assignment statement
                              // calculate($a, $b) is expression

// Function call can be statement or expression
echo strlen("hello");     // Function call as statement
$len = strlen("hello");   // Function call as expression in assignment
?>
```

## Code Organization with Blocks

### Conditional Blocks

```php
<?php
if ($age >= 18) {
    // This block executes if condition is true
    echo "Adult";
    $status = "eligible";
} elseif ($age >= 13) {
    // This block executes if above is false and this is true
    echo "Teenager";
    $status = "partial";
} else {
    // This block executes if all above are false
    echo "Child";
    $status = "restricted";
}

// Variables defined in blocks are accessible after
echo $status;
?>
```

### Loop Blocks

```php
<?php
for ($i = 0; $i < 5; $i++) {
    // This block repeats 5 times
    echo $i;
    $total += $i;
}

// Variable from loop still accessible
echo "Total: " . $total;

// Nested blocks
for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
        echo "($i, $j) ";  // Can access both $i and $j
    }
    echo "\n";
}
?>
```

### Function Blocks

```php
<?php
function addNumbers($a, $b) {
    // Everything in braces is the function block
    $sum = $a + $b;       // Local to this block
    $message = "Result: ";
    return $message . $sum;
}

// Variables inside function not accessible outside
// echo $sum;            // ERROR!
// echo $message;        // ERROR!

// But return value can be used
$result = addNumbers(5, 3);
echo $result;             // Output: Result: 8
?>
```

### Class Blocks

```php
<?php
class Calculator {
    // Class block contains properties and methods
    public $result = 0;
    
    public function add($a, $b) {
        // Method block
        $this->result = $a + $b;
        return $this->result;
    }
    
    public function getResult() {
        return $this->result;
    }
}

$calc = new Calculator();
$calc->add(5, 3);
echo $calc->getResult();  // Output: 8
?>
```

## Statement Types

### 1. Declaration Statements

```php
<?php
// Variable declaration
$name = "John";

// Function declaration
function greet() {
    echo "Hello";
}

// Class declaration
class User {
    public $id;
}

// Constant declaration
const MAX_SIZE = 100;
define("MIN_SIZE", 10);
?>
```

### 2. Control Statements

```php
<?php
// Conditional
if ($x > 0) { }
switch ($type) { }

// Looping
for ($i = 0; $i < 10; $i++) { }
while ($condition) { }
foreach ($array as $item) { }

// Jumping
break;
continue;
return $value;
goto label;
?>
```

### 3. Expression Statements

```php
<?php
// Any expression followed by semicolon
$x = 5;           // Assignment expression statement
$x++;             // Increment expression statement
echo $x;          // Function call expression statement
calculate(5, 3);  // Function call expression statement
?>
```

## Empty Statements and Blocks

```php
<?php
// Empty statement (just semicolon)
;                 // Valid but useless

// Empty block
if ($condition) { }           // Does nothing

// Block with only one statement (braces optional)
if ($condition)
    echo "No braces needed";

// Empty loop body
for ($i = 0; $i < 10; $i++)
    ;             // Loop does nothing (but still runs)
?>
```

## Best Practices

### 1. Always Use Braces

```php
<?php
// Good: explicit braces
if ($x > 0) {
    echo "Positive";
}

// Avoid: no braces (error-prone)
if ($x > 0)
    echo "Positive";
    echo "Always shows";  // This always executes!
?>
```

### 2. Consistent Indentation

```php
<?php
// Good: clear indentation
if ($condition) {
    for ($i = 0; $i < 10; $i++) {
        if ($i % 2 == 0) {
            echo $i;
        }
    }
}

// Bad: inconsistent indentation
if($condition){
for($i=0;$i<10;$i++){
if($i%2==0){
echo $i;
}
}
}
?>
```

### 3. One Statement Per Line

```php
<?php
// Good: readable
$x = 5;
$y = 10;
$z = $x + $y;
echo $z;

// Avoid: multiple statements per line
$x = 5; $y = 10; $z = $x + $y; echo $z;
?>
```

### 4. Clear Block Organization

```php
<?php
// Good: organized blocks
function validateUser($data) {
    // Validation block
    if (!isset($data['email'])) {
        return false;
    }
    
    // Processing block
    $user = new User();
    $user->setEmail($data['email']);
    
    // Return block
    return true;
}

// Bad: disorganized
function validateUser($data) {
    if (!isset($data['email'])) return false;
    $user = new User(); $user->setEmail($data['email']); return true;
}
?>
```

## Common Pitfalls

### Missing Semicolon

```php
<?php
// ERROR: missing semicolon
$x = 5

// WRONG: semicolon after expression (does nothing)
5 + 3;  // This is valid but pointless

// CORRECT
$x = 5;
echo $x;
?>
```

### Forgetting Braces in Complex Conditions

```php
<?php
$x = 5;
$y = 10;

// WRONG: only $y increments
if ($x > 0)
    $x++;
    $y++;    // Always executes!

// CORRECT: both increment inside if
if ($x > 0) {
    $x++;
    $y++;
}
?>
```

### Nested Blocks Without Clear Indentation

```php
<?php
// HARD TO READ
if($x>0){
if($y<10){
echo "Value";
}
}

// READABLE
if ($x > 0) {
    if ($y < 10) {
        echo "Value";
    }
}
?>
```

## Key Takeaways

✓ **Expression** is code that produces a value
✓ **Statement** is a complete instruction (expression + semicolon or control structure)
✓ **Block** is code enclosed in braces `{}`
✓ **Scope** - variables in blocks accessible within and nested blocks
✓ **Always use braces** in control structures for clarity
✓ **Consistent indentation** makes code readable
✓ **One statement per line** is cleaner
✓ **Empty statements** (just `;`) are valid but useless
✓ **Blocks organize code** logically
✓ **Nested blocks** can access outer block variables
