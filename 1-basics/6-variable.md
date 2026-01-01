# Variables & Variable Scope

## What are Variables?

A variable is a container that stores a data value. Variables allow you to store, manipulate, and reuse data in your PHP programs.

Think of a variable like a labeled box that holds a piece of information:

```
┌──────────────────┐
│   $name = "Bob"  │  Box labeled "$name" contains "Bob"
└──────────────────┘
```

## Creating Variables

### Variable Syntax

```php
<?php
// Variable declaration: $name = value;
$name = "Alice";         // String
$age = 25;               // Integer
$height = 5.8;           // Float
$isActive = true;        // Boolean
$colors = ["red", "blue"]; // Array

// Display variables
echo $name;              // Alice
echo $age;               // 25
?>
```

### Naming Rules

Variables in PHP must follow specific naming conventions:

```php
<?php
// VALID variable names
$name = "John";          // ✓ Starts with letter
$_name = "Jane";         // ✓ Starts with underscore
$name2 = "Bob";          // ✓ Contains numbers
$myVariableName = "Tom"; // ✓ Camelcase (common convention)
$my_variable_name = "Sam"; // ✓ Snake_case (also used)

// INVALID variable names
// $2name = "Invalid";    // ✗ Cannot start with number
// $-name = "Invalid";    // ✗ Cannot contain hyphen
// $my name = "Invalid";  // ✗ Cannot contain space
// $my-name = "Invalid";  // ✗ Cannot contain hyphen
?>
```

**Key Rules:**
- Must start with dollar sign (`$`)
- Must start with letter or underscore after `$`
- Can only contain alphanumeric characters and underscores
- Variable names are **case-sensitive**: `$name` and `$Name` are different

### Naming Conventions

```php
<?php
// camelCase - Common in PHP
$firstName = "John";
$lastName = "Doe";
$totalPrice = 99.99;

// snake_case - Also used
$first_name = "John";
$last_name = "Doe";
$total_price = 99.99;

// CONSTANTS - All uppercase (not variables, but related)
define("MAX_USERS", 100);
echo MAX_USERS; // 100

// Best practice: Pick one style and stick with it
?>
```

## Variable Types

PHP has 8 data types. Variables can hold any of these:

```php
<?php
// String
$text = "Hello";

// Integer
$count = 42;

// Float
$price = 19.99;

// Boolean
$active = true;

// Array
$items = ["apple", "banana", "orange"];

// Object
class User {}
$user = new User();

// Resource
$file = fopen("test.txt", "r");

// NULL
$empty = null;
?>
```

## Checking Variables

### Type Checking Functions

```php
<?php
$name = "Alice";
$age = 25;
$price = 19.99;
$active = true;

// Check specific type
is_string($name);       // true
is_int($age);           // true
is_float($price);       // true
is_bool($active);       // true
is_array([1, 2, 3]);    // true

// Check if numeric (int or float)
is_numeric(42);         // true
is_numeric(3.14);       // true
is_numeric("42");       // true (numeric string)

// Get variable type
gettype($name);         // "string"
gettype($age);          // "integer"
gettype($price);        // "double"

// Check if variable exists
isset($name);           // true
isset($undefined);      // false

// Check if empty
empty("");              // true
empty(0);               // true
empty(null);            // true
empty([]);              // true
?>
```

## Variable Scope

Variable scope refers to the area of your code where a variable can be accessed.

### Global Scope

Variables declared outside any function are **global** - accessible from anywhere:

```php
<?php
$globalVar = "I'm global";  // Declared outside function

function myFunction() {
    echo $globalVar;  // ERROR! Not accessible in function scope
}

myFunction();  // Causes error
echo $globalVar;  // Works fine - at global scope
?>
```

### Function Scope (Local Scope)

Variables declared inside a function are **local** - only accessible within that function:

```php
<?php
function greet() {
    $message = "Hello!";  // Local variable
    echo $message;         // Works - inside function
}

greet();           // Outputs: Hello!
echo $message;     // ERROR! Not accessible outside function
?>
```

### Accessing Global Variables from Functions

To use a global variable inside a function, use the `global` keyword:

```php
<?php
$name = "Alice";  // Global scope

function greet() {
    global $name;  // Access global variable
    echo "Hello, $name!";
}

greet();  // Outputs: Hello, Alice!
?>
```

### Using $GLOBALS Array

Alternative way to access global variables:

```php
<?php
$name = "Bob";
$age = 25;

function displayUser() {
    echo $GLOBALS['name'];  // Bob
    echo $GLOBALS['age'];   // 25
}

displayUser();
?>
```

### Static Variables

Static variables retain their value between function calls:

```php
<?php
function counter() {
    static $count = 0;  // Initialized only once
    $count++;
    echo $count;
}

counter();  // Outputs: 1
counter();  // Outputs: 2
counter();  // Outputs: 3
?>
```

## Superglobals

PHP provides special variables accessible everywhere, called superglobals:

```php
<?php
// $GLOBALS - All global variables
$GLOBALS['myVar'] = "value";

// $_SERVER - Server and execution environment information
$_SERVER['HTTP_HOST'];      // example.com
$_SERVER['REQUEST_METHOD']; // GET, POST, etc.
$_SERVER['PHP_SELF'];       // Current script path

// $_GET - URL parameters (http://example.com?name=John)
$_GET['name'];              // John

// $_POST - Form submission data
$_POST['username'];         // From form

// $_REQUEST - Both GET and POST
$_REQUEST['data'];

// $_FILES - Uploaded files
$_FILES['upload'];

// $_COOKIE - Browser cookies
$_COOKIE['user_preference'];

// $_SESSION - Session data
$_SESSION['user_id'];

// $_ENV - Environment variables
$_ENV['PATH'];

// $ARGV - Command-line arguments
$ARGV[0];  // Script name
?>
```

## Variable Variables

PHP allows using the value of one variable as the name of another variable:

```php
<?php
$varname = "message";
$message = "Hello, World!";

$$varname = "New message";  // Creates/modifies $message

echo $message;      // New message
echo $$varname;     // New message
?>
```

**Practical Example:**

```php
<?php
$field1 = "name";
$field2 = "email";

$name = "Alice";
$email = "alice@example.com";

// Access using variable variables
echo $$field1;   // Alice
echo $$field2;   // alice@example.com
?>
```

## Unsetting Variables

Remove a variable from memory:

```php
<?php
$temp = "This will be deleted";
echo $temp;      // This will be deleted

unset($temp);
echo $temp;      // ERROR! Variable no longer exists
?>
```

## Variable Interpolation

Use variables directly in strings:

```php
<?php
$name = "Alice";
$age = 25;

// Simple interpolation (double quotes)
echo "Name: $name";           // Name: Alice
echo "Age: $age";             // Age: 25

// Complex interpolation (use braces)
$person = ['name' => 'Bob'];
echo "Name: {$person['name']}";  // Name: Bob

// Single quotes - NO interpolation
echo 'Name: $name';           // Name: $name (literal)
?>
```

## Practical Examples

### User Profile

```php
<?php
// User information
$firstName = "John";
$lastName = "Doe";
$email = "john@example.com";
$age = 30;
$isActive = true;

// Display profile
echo "User: $firstName $lastName\n";
echo "Email: $email\n";
echo "Age: $age\n";
echo "Active: " . ($isActive ? "Yes" : "No") . "\n";
?>
```

### Function with Local and Global Variables

```php
<?php
$globalCount = 0;  // Global variable

function increment() {
    global $globalCount;
    $localCount = 5;  // Local variable
    
    $globalCount++;
    $localCount++;
    
    echo "Global: $globalCount, Local: $localCount\n";
}

increment();  // Global: 1, Local: 6
increment();  // Global: 2, Local: 6
?>
```

## Key Takeaways

✓ Variables store data values with a `$` prefix
✓ Variable names must start with letter or underscore
✓ PHP is **case-sensitive**: `$name` ≠ `$Name`
✓ **Global scope**: Outside functions, accessible everywhere
✓ **Local scope**: Inside functions, only accessible within that function
✓ Use `global` keyword to access global variables in functions
✓ **Superglobals** like `$_GET`, `$_POST`, `$_SERVER` are always accessible
✓ Static variables retain values between function calls
✓ Unset variables with `unset()` to free memory
✓ Interpolate variables directly in double-quoted strings

