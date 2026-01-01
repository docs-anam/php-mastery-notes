# Hello World & PHP Basics

## What is PHP?

PHP (Hypertext Preprocessor) is a server-side scripting language used to create dynamic web pages. When a user requests a PHP file, the server executes the PHP code and sends the result to the user's browser.

## Your First PHP Program

### Basic Structure

```php
<?php
echo "Hello, World!";
?>
```

**Output:**
```
Hello, World!
```

### How It Works

1. `<?php` - Opening tag that tells the server this is PHP code
2. `echo "Hello, World!";` - Statement that outputs text
3. `?>` - Closing tag (optional in modern PHP)
4. `;` - Semicolon ends the statement (required!)

## Key PHP Coding Rules

### 1. PHP Tags

Every PHP script must be enclosed in PHP tags:

```php
<?php
// Your PHP code here
?>
```

**Variants** (Not recommended, but exist):
```php
<? // Short tag - may not be enabled on all servers
?> 

<?= // Short echo tag
?>
```

**Best Practice**: Always use `<?php ... ?>` tags.

### 2. Statements End with Semicolon

Each statement must end with a semicolon (`;`):

```php
<?php
echo "Statement 1";  // Correct
echo "Statement 2"   // ERROR! Missing semicolon
?>
```

**Why?** The semicolon tells PHP where one statement ends and another begins.

### 3. Variables Start with Dollar Sign

Variables hold data and always begin with `$`:

```php
<?php
$name = "Alice";
$age = 25;
$price = 19.99;

echo $name;  // Outputs: Alice
echo $age;   // Outputs: 25
?>
```

**Naming Rules:**
- Must start with letter or underscore: `$_name`, `$name123` ✓
- Cannot start with number: `$123name` ✗
- Case-sensitive: `$name` and `$Name` are different variables

### 4. Comments

Comments are ignored by PHP and help explain your code:

**Single-line comments:**
```php
<?php
// This is a single-line comment
$x = 5;  // You can also comment at the end of a line

# This is also a single-line comment (less common)
?>
```

**Multi-line comments:**
```php
<?php
/* This is a multi-line comment
   that can span across
   multiple lines */

/**
 * This is a DocBlock comment
 * Used for documenting functions and classes
 * @param $name string
 * @return void
 */
function sayHello($name) {
    echo "Hello, $name!";
}
?>
```

**Best Practice**: Use `//` for most comments, `/* */` for larger blocks, and DocBlocks for function documentation.

### 5. Output with `echo` and `print`

Both `echo` and `print` output text, but have differences:

```php
<?php
// echo - slightly faster, no return value
echo "Hello";
echo "World";

// print - can be used in expressions (returns 1)
print "Hello";
$result = print "Hello";  // Valid, $result = 1

// Multiple outputs with echo
echo "Hello", " ", "World";  // Outputs: Hello World

// Single output with print
print "Hello";
?>
```

| Feature | `echo` | `print` |
|---------|--------|--------|
| Output | Text | Text |
| Return value | None | 1 |
| Multiple params | Yes | No |
| Speed | Slightly faster | Slightly slower |
| Best for | Most cases | Expressions |

### 6. Whitespace & Indentation

PHP ignores extra whitespace, but proper formatting improves readability:

```php
<?php
// All of these are equivalent:
echo "Hello";

echo    "Hello";

echo
"Hello";

// But this is unreadable - avoid:
echo "Hello";echo "World";echo "!";

// Better:
echo "Hello";
echo "World";
echo "!";
?>
```

**Indentation Standards:**
```php
<?php
function example() {
    // Content indented 4 spaces
    if (true) {
        // Nested content indented further
        echo "Properly formatted";
    }
}
?>
```

## Data Types Overview

```php
<?php
// String
$name = "Alice";
$greeting = 'Hello';

// Integer (whole numbers)
$age = 25;
$count = -10;

// Float (decimal numbers)
$price = 19.99;
$pi = 3.14159;

// Boolean (true/false)
$isActive = true;
$isDeleted = false;

// Array (collection of values)
$colors = ["red", "blue", "green"];
$person = ["name" => "Bob", "age" => 30];

// NULL (no value)
$empty = NULL;
?>
```

## Running Your PHP Code

### Using Web Server (XAMPP/MAMP)
```bash
# 1. Create file in htdocs:
# /xampp/htdocs/hello.php or /Applications/MAMP/htdocs/hello.php

<?php
echo "Hello, World!";
?>

# 2. Start Apache via XAMPP/MAMP
# 3. Visit: http://localhost/hello.php or http://localhost:8888/hello.php
```

### Using PHP Built-in Server
```bash
# Create file: hello.php
<?php
echo "Hello, World!";
?>

# Run in terminal:
php -S localhost:8000

# Visit: http://localhost:8000/hello.php
```

### Using Terminal Directly
```bash
# Create file: hello.php
<?php
echo "Hello, World!\n";
?>

# Run in terminal:
php hello.php

# Output:
# Hello, World!
```

## Complete Example

```php
<?php
// hello.php - A complete example

// Variables
$name = "PHP Developer";
$version = 8.2;

// Comments
/* This program demonstrates
   basic PHP syntax and output */

// Output
echo "Welcome, " . $name . "!\n";
echo "Learning PHP " . $version . "\n";

// Arithmetic
$a = 10;
$b = 20;
$sum = $a + $b;
echo "Sum: " . $sum;
?>
```

**Output:**
```
Welcome, PHP Developer!
Learning PHP 8.2
Sum: 30
```

## Common Mistakes to Avoid

| Mistake | Wrong | Correct |
|---------|-------|---------|
| Missing semicolon | `echo "Hi"` | `echo "Hi";` |
| Missing $ sign | `name = "Bob"` | `$name = "Bob";` |
| Wrong quotes | `$x = 'He said "hi"'` | `$x = "He said \"hi\"";` |
| Case sensitivity | `ECHO "Hi"` | `echo "Hi"` |
| No tags | `echo "Hi"` (if in .php file) | `<?php echo "Hi"; ?>` |
| Missing closing brace | `if (true) {` | `if (true) { }` |

## Next Steps

- **Variables**: [6-variable.md](6-variable.md) - Learn to store and use data
- **Data Types**: [3-data-type-number.md](3-data-type-number.md) - Understand different data types
- **Operators**: [10-operators-arithmetic.md](10-operators-arithmetic.md) - Perform calculations
- **Control Structures**: [18-if-statement.md](18-if-statement.md) - Make decisions in code

## Key Takeaways

✓ PHP code is enclosed in `<?php ?>` tags
✓ Every statement ends with a semicolon (`;`)
✓ Use `echo` or `print` to output text
✓ Variables start with `$` and are case-sensitive
✓ Comments explain your code (ignored by PHP)
✓ Proper formatting makes code readable and maintainable
✓ PHP runs on the server; clients only see the output

