# Variables - Declaration, Naming, and Scope

## Table of Contents
1. [What are Variables?](#what-are-variables)
2. [Variable Declaration](#variable-declaration)
3. [Naming Conventions](#naming-conventions)
4. [Variable Scope](#variable-scope)
5. [Variable Variables](#variable-variables)
6. [Common Mistakes](#common-mistakes)

---

## What are Variables?

A variable is a named storage location that holds a value. Think of it as a labeled box where you can store data.

```php
<?php
// Variable: $name stores the value "Alice"
$name = "Alice";

// Variable: $age stores the value 25
$age = 25;

// Variable: $scores stores multiple values
$scores = [90, 85, 88];
?>
```

### Key Points

- Variables are **dynamic** - can hold any data type
- Variables are **mutable** - can be changed
- Variables are **case-sensitive** - `$name` ≠ `$Name`
- Must start with `$` symbol

---

## Variable Declaration

### Creating Variables

```php
<?php
// Simple assignment
$name = "Alice";      // String
$age = 25;            // Integer
$height = 5.6;        // Float
$active = true;       // Boolean

// Multiple declarations
$x = $y = $z = 0;     // All equal to 0

// Without initial value (PHP will initialize to NULL)
$empty = null;        // Explicitly null
?>
```

### Initializing Different Types

```php
<?php
// String
$message = "Hello, World!";

// Integer
$count = 42;

// Float
$price = 19.99;

// Boolean
$is_active = true;

// Array
$colors = ["red", "green", "blue"];

// Null
$nothing = null;

// Object
$date = new DateTime();
?>
```

---

## Naming Conventions

### Rules (Must Follow)

1. **Must start with letter or underscore** (_), not a number
2. **Can only contain alphanumeric characters** (A-Z, a-z, 0-9) and underscores (_)
3. **Case-sensitive** - `$name`, `$Name`, `$NAME` are different
4. **No spaces** in variable names

```php
<?php
// ✅ Valid names
$name = "Alice";
$_name = "Bob";
$name2 = "Charlie";
$firstName = "Diana";

// ❌ Invalid names
$2name = "Error";        // Starts with number
$first-name = "Error";   // Contains hyphen
$first name = "Error";   // Contains space
$first$name = "Error";   // Contains $
?>
```

### Naming Style - camelCase (PHP Convention)

```php
<?php
// PHP convention: camelCase for variables
$firstName = "Alice";      // ✅ Recommended
$firstName_lastName = "Alice Smith";  // ✅ Also acceptable

// NOT snake_case (that's for functions)
$first_name = "Bob";       // Valid but not idiomatic for variables

// Use meaningful names
$x = 5;           // ❌ Unclear
$count = 5;       // ✅ Clear

$n = "Alice";     // ❌ Unclear  
$name = "Alice";  // ✅ Clear
?>
```

### Naming Best Practices

```php
<?php
// ✅ DO: Use descriptive names
$userName = "alice";
$userAge = 25;
$isActive = true;

// ❌ DON'T: Use ambiguous abbreviations
$u = "alice";    // What is u?
$a = 25;         // What is a?
$ia = true;      // What does ia mean?

// ✅ DO: Use full words
$totalPrice = 99.99;
$maxAttempts = 3;
$hasPermission = false;

// ❌ DON'T: Use single letters (except in loops)
$p = 99.99;      // What is p?
$m = 3;          // What is m?
$h = false;      // What is h?

// Exception: Loop variables can be short
for ($i = 0; $i < 10; $i++) {
    echo $i;  // i is fine here
}
?>
```

---

## Variable Scope

Variable scope determines where a variable can be accessed. PHP has several scope types:

### 1. Local Scope (Inside Functions)

Variables declared inside a function are only accessible within that function:

```php
<?php
function greet() {
    $name = "Alice";       // Local to this function
    echo $name;            // Works: Alice
}

greet();
echo $name;                // Error: $name not defined in global scope
?>
```

### 2. Global Scope (Outside Functions)

Variables declared outside functions are accessible globally:

```php
<?php
$global_var = "I'm global";

function accessGlobal() {
    echo $global_var;  // Error: not accessible without 'global' keyword
}

accessGlobal();  // Error: Undefined variable
?>
```

### 3. Using `global` Keyword

To access global variables inside a function, use the `global` keyword:

```php
<?php
$name = "Alice";  // Global

function greet() {
    global $name;  // Access the global variable
    echo "Hello, $name";  // Works: Hello, Alice
}

greet();
?>
```

### 4. Super Globals (Available Everywhere)

Special arrays available everywhere:

```php
<?php
// Super globals - always available
$GLOBALS['user'] = "Alice";  // Access any global

$_GET;      // URL parameters
$_POST;     // Form submission
$_COOKIE;   // Cookies
$_SESSION;  // Session data
$_SERVER;   // Server information
$_ENV;      // Environment variables
$_FILES;    // Uploaded files
$_REQUEST;  // GET, POST, COOKIE combined

// Use super globals instead of global keyword
function checkUser() {
    if (isset($_SESSION['user'])) {
        echo "User: " . $_SESSION['user'];
    }
}
?>
```

### 5. Static Scope

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
// Value persists between calls!
?>
```

### Scope Example with Function Parameters

```php
<?php
$name = "Global Alice";

function greet($name) {  // Parameter creates local variable
    echo "Hello, $name";  // Uses parameter, not global
}

greet("Local Bob");      // Outputs: Hello, Local Bob
echo $name;              // Outputs: Global Alice (unchanged)
?>
```

---

## Variable Variables

Variable variables let you use one variable's value as another variable's name:

```php
<?php
$var_name = "message";
$$var_name = "Hello!";  // Creates $message = "Hello!"

echo $$var_name;  // Outputs: Hello!
echo $message;    // Outputs: Hello!
?>
```

### Real-World Example

```php
<?php
// Build variables dynamically
$fields = ['name', 'email', 'phone'];

foreach ($fields as $field) {
    // Create: $name, $email, $phone
    $$field = null;
}

// Now we have:
// $name = null;
// $email = null;
// $phone = null;

// Assign values
$name = "Alice";
$email = "alice@example.com";
$phone = "123-456-7890";
?>
```

### Caution

```php
<?php
// Variable variables can be confusing!
$data = [1, 2, 3];

// ❌ Avoid this in production code
$$data[0] = "value";  // Unclear what's happening

// ✅ Better approach
$array_name = 'config';
$$array_name = ['host' => 'localhost', 'port' => 3306];

// Access
echo $config['host'];  // localhost
?>
```

---

## Common Mistakes

### 1. Forgetting the $ Sign

```php
<?php
name = "Alice";   // ❌ Error: Undefined constant 'name'
$name = "Alice";  // ✅ Correct
?>
```

### 2. Case Sensitivity

```php
<?php
$name = "Alice";
echo $Name;    // ❌ Error: Undefined variable
echo $NAME;    // ❌ Error: Undefined variable
echo $name;    // ✅ Correct
?>
```

### 3. Using Variables Without Initializing

```php
<?php
echo $count;   // ❌ Warning: Undefined variable
$count = 0;
$count++;      // ✅ Now it's safe
?>
```

### 4. Scope Issues

```php
<?php
$global = "I'm global";

function test() {
    echo $global;  // ❌ Error: Undefined variable
}

test();

function test2() {
    global $global;  // ✅ Access global
    echo $global;    // Works: I'm global
}

test2();
?>
```

### 5. Mixing Up Variables with Constants

```php
<?php
// Variables (mutable)
$name = "Alice";
$name = "Bob";    // Can change

// Constants (immutable)
define('SITE_NAME', 'MyApp');
// SITE_NAME = 'NewApp';  // ❌ Error - can't change

// Use constants for values that never change
// Use variables for values that change
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

// Global scope
$appName = "MyApplication";
$version = "1.0.0";

function getUserProfile() {
    global $appName;
    
    // Local scope
    $userName = "Alice";
    $userEmail = "alice@example.com";
    $userAge = 25;
    
    // Static counter
    static $viewCount = 0;
    $viewCount++;
    
    // Build output
    $output = [
        'app' => $appName,
        'name' => $userName,
        'email' => $userEmail,
        'age' => $userAge,
        'views' => $viewCount,
    ];
    
    return $output;
}

// First call
print_r(getUserProfile());

// Second call
print_r(getUserProfile());  // viewCount = 2

echo "App Name (global): $appName\n";
?>
```

**Output:**
```
Array (
    [app] => MyApplication
    [name] => Alice
    [email] => alice@example.com
    [age] => 25
    [views] => 1
)
Array (
    [app] => MyApplication
    [name] => Alice
    [email] => alice@example.com
    [age] => 25
    [views] => 2
)
App Name (global): MyApplication
```

---

## Next Steps

✅ Understand variables and naming  
→ Learn [string manipulation](17-string-manipulation.md)  
→ Study [operators](10-operators-arithmetic.md)  
→ Master [functions](28-functions.md)
