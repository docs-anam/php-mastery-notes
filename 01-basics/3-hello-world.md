# Hello, World! - Your First PHP Program

## Table of Contents
1. [PHP Tags](#php-tags)
2. [Basic Syntax Rules](#basic-syntax-rules)
3. [Hello World Program](#hello-world-program)
4. [Output Methods](#output-methods)
5. [Comments](#comments)
6. [Common Mistakes](#common-mistakes)

---

## PHP Tags

PHP code must be enclosed in proper opening and closing tags:

### Standard PHP Tags
```php
<?php
    // Your PHP code here
?>
```

### Important Rules

| Rule | Example | Valid? |
|------|---------|--------|
| Use `<?php` and `?>` | `<?php echo "Hi"; ?>` | ✅ Yes |
| Short tag (if enabled) | `<? echo "Hi"; ?>` | ⚠️ Not recommended |
| ASP-style tags | `<% echo "Hi"; %>` | ❌ No |
| Script tags | `<script language="php">` | ❌ No |

### Best Practice
Always use `<?php` and `?>` tags. They work everywhere and are the standard.

---

## Basic Syntax Rules

### 1. Statements End with Semicolon (;)

Every statement in PHP must end with a semicolon:

```php
<?php
$name = "Alice";              // ✅ Correct - ends with ;
$age = 25;                     // ✅ Correct
echo "Hello World"             // ❌ Wrong - missing ;
?>
```

### 2. Variables Start with $ Sign

Variables must be prefixed with a dollar sign:

```php
<?php
$message = "Hello";            // ✅ Correct
$count = 10;                   // ✅ Correct
message = "Hello";             // ❌ Wrong - missing $
?>
```

### 3. PHP is Case-Sensitive

Variable names are case-sensitive, but keywords are not:

```php
<?php
$name = "Alice";
$Name = "Bob";
$NAME = "Charlie";
// All three are different variables!

echo $name;  // Alice
echo $Name;  // Bob
echo $NAME;  // Charlie

// Keywords are case-insensitive
echo "Hello";   // ✅ Works
ECHO "Hello";   // ✅ Works
Echo "Hello";   // ✅ Works
?>
```

### 4. String Quotes

Strings can use single ('') or double ("") quotes:

```php
<?php
// Single quotes - literal strings
$message1 = 'Hello, World!';

// Double quotes - allows variable interpolation
$name = "Alice";
$message2 = "Hello, $name!";  // Includes variable value

echo $message1;  // Hello, World!
echo $message2;  // Hello, Alice!
?>
```

---

## Hello World Program

The simplest PHP program:

```php
<?php
echo "Hello, World!";
?>
```

**Output:**
```
Hello, World!
```

### Running PHP Programs

**In Terminal:**
```bash
php hello.php
```

**With Built-in Server:**
```bash
# Create hello.php with PHP code
php -S localhost:8000

# Then visit http://localhost:8000/hello.php
```

### Complete Hello World File

Save as `hello.php`:

```php
<?php
// My first PHP program
echo "Hello, World!";
?>
```

Run it:
```bash
php hello.php
```

---

## Output Methods

### 1. `echo` - Output Text (Most Common)

Outputs one or more strings:

```php
<?php
echo "Hello, World!";
echo "This is PHP";

// Can output multiple items
echo "Sum: ", 5 + 3;  // Sum: 8

// With variables
$greeting = "Hello";
echo $greeting;  // Hello
?>
```

**Characteristics:**
- No return value
- Can take multiple parameters
- Slightly faster than print

### 2. `print` - Output Text

Similar to echo, but only one argument:

```php
<?php
print "Hello, World!";

// Can only output one thing
print "Sum: " . (5 + 3);  // Needs concatenation

// Returns 1 (can be used in expressions)
$result = print "Hello";  // Works but unusual
?>
```

### 3. `var_dump()` - Debug Output

Displays variable type and value:

```php
<?php
$name = "Alice";
$age = 25;
$scores = [90, 85, 88];

var_dump($name);    // string(5) "Alice"
var_dump($age);     // int(25)
var_dump($scores);  // array(3) { [0]=> int(90) ... }
?>
```

### 4. `print_r()` - Human-Readable Output

Displays variable information in readable format:

```php
<?php
$person = [
    'name' => 'Alice',
    'age' => 25,
    'city' => 'NYC'
];

print_r($person);
// Output:
// Array
// (
//     [name] => Alice
//     [age] => 25
//     [city] => NYC
// )
?>
```

### 5. `die()` / `exit()` - Stop Execution

Stops the script and optionally outputs a message:

```php
<?php
if ($error) {
    die("An error occurred!");  // Stops execution
}

exit("Goodbye!");  // Same as die()
?>
```

---

## Comments

### Single-Line Comments

```php
<?php
// This is a single-line comment
echo "Hello";  // This comment is on the same line

# Hash marks also work as comments
echo "World";
?>
```

### Multi-Line Comments

```php
<?php
/*
    This is a multi-line comment
    It can span multiple lines
    Useful for longer explanations
*/
echo "Hello, World!";

/* This also works */ echo "On one line";
?>
```

### Best Practices for Comments

```php
<?php
// DO: Explain WHY, not WHAT
// We need to check age >= 18 because of legal requirements
if ($age >= 18) {
    // Process adult user
}

// DON'T: State the obvious
// Add 1 to count (obviously you're adding 1!)
$count = $count + 1;

// DO: Comment complex logic
// Fibonacci sequence - each number is sum of previous two
$fib = $previous + $current;

// DO: Use comments for TODO items
// TODO: Add error handling for database connection
$db = new Database();
?>
```

---

## Common Mistakes

### 1. Forgetting the Opening Tag

```php
echo "Hello";  // ❌ Wrong - no <?php tag

<?php echo "Hello"; ?>  // ✅ Correct
```

### 2. Closing Tag in PHP-Only Files

In PHP-only files, omit the closing `?>`:

```php
<?php
// File: config.php

define('DB_HOST', 'localhost');
define('DB_USER', 'admin');

// DON'T close with ?>
// If you do, accidental whitespace after ?> causes issues
```

### 3. Mixing Quotes Incorrectly

```php
<?php
echo 'Hello, $name';  // ❌ Shows literal $name, not its value
echo "Hello, $name";  // ✅ Shows the value of $name

echo "It's raining";  // ❌ Syntax error
echo "It\'s raining"; // ✅ Escape the apostrophe
echo 'It\'s raining'; // ✅ Escape the apostrophe

// Or use the other quote type
echo "It's raining";  // ✅ No escape needed
?>
```

### 4. Missing Semicolons

```php
<?php
echo "Hello"   // ❌ Missing semicolon
echo "World";

$count = 10    // ❌ Missing semicolon
$count = $count + 1;
?>
```

### 5. Case Sensitivity Errors

```php
<?php
$myVar = "Hello";

echo $myvar;   // ❌ Undefined variable (different case)
echo $myVar;   // ✅ Correct
?>
```

---

## Complete Example

Save as `first-program.php`:

```php
<?php
declare(strict_types=1);

// My first PHP program
echo "Welcome to PHP!\n";

// Variables
$name = "Alice";
$age = 25;

// Output
echo "Name: $name\n";
echo "Age: $age\n";

// Simple calculation
$birth_year = date('Y') - $age;
echo "Birth Year: $birth_year\n";

// Conditional
if ($age >= 18) {
    echo "You are an adult.\n";
} else {
    echo "You are a minor.\n";
}
?>
```

**Run it:**
```bash
php first-program.php
```

**Output:**
```
Welcome to PHP!
Name: Alice
Age: 25
Birth Year: 1999
You are an adult.
```

---

## Quick Reference

| Concept | Example | Notes |
|---------|---------|-------|
| Opening tag | `<?php` | Required |
| Closing tag | `?>` | Optional in PHP-only files |
| Statement end | `;` | Required |
| Variable prefix | `$` | Required |
| Echo output | `echo "text";` | Most common output |
| Comment line | `// comment` | Single-line |
| Comment block | `/* ... */` | Multi-line |

---

## Next Steps

✅ Master basic syntax  
→ Learn [variables and data types](3-data-type-number.md)  
→ Understand [operators](10-operators-arithmetic.md)  
→ Study [control flow](18-if-statement.md)
