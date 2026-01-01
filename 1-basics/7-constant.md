# Constants in PHP

## What is a Constant?

A constant is an identifier (name) for a simple value that **cannot be changed or undefined** once it's defined. Unlike variables, constants do not use the `$` prefix and maintain their value throughout your entire script execution.

Think of constants like unchangeable settings:

```
Variable: Can change during execution
$name = "Alice";
$name = "Bob";      // ✓ Allowed

Constant: Never changes
define("MAX_USERS", 100);
MAX_USERS = 200;    // ✗ ERROR! Cannot change
```

## Defining Constants

### Using the `define()` Function

```php
<?php
// Syntax: define(name, value, case-insensitive)

// Basic constant
define("SITE_NAME", "My Website");
define("DB_HOST", "localhost");
define("MAX_ATTEMPTS", 5);

// Access constants (no $ sign!)
echo SITE_NAME;     // My Website
echo DB_HOST;       // localhost
echo MAX_ATTEMPTS;  // 5

// Constants work globally
function showSite() {
    echo SITE_NAME;  // Works! Constants are global
}

showSite();         // My Website
?>
```

### Using the `const` Keyword (PHP 5.3+)

```php
<?php
// At top-level scope (not inside functions/classes)
const VERSION = "1.0.0";
const AUTHOR = "John Doe";
const TIMEOUT = 30;

echo VERSION;       // 1.0.0
echo AUTHOR;        // John Doe
echo TIMEOUT;       // 30

// Note: const is evaluated at compile-time, define() at runtime
// Inside classes:
class Config {
    const API_KEY = "secret123";
    const API_URL = "https://api.example.com";
}

echo Config::API_KEY;   // secret123
?>
```

## Difference Between `define()` and `const`

| Feature | `define()` | `const` |
|---------|-----------|--------|
| When evaluated | Runtime | Compile-time |
| Scope | Always global | Class scope with `const` |
| Conditional definition | ✓ Yes | ✗ No |
| Expressions | ✓ Yes (PHP 5.6+) | ✓ Yes (PHP 5.6+) |
| Case-insensitive | ✓ Optional 3rd param | ✗ No |
| Dynamic names | ✓ Yes | ✗ No |
| Visibility | Public | Public/Private/Protected |

### When to Use Which

```php
<?php
// Use define() for:
// 1. Conditional definitions
if (DEBUG_MODE) {
    define("LOG_LEVEL", "DEBUG");
} else {
    define("LOG_LEVEL", "ERROR");
}

// 2. Dynamic constant names
define("USER_" . $userId, $userData);

// 3. Case-insensitive constants (3rd parameter = true)
define("GREETING", "Hello", true);
echo greeting;  // Hello (works even with different case)

// Use const for:
// 1. Class constants
class User {
    const MIN_AGE = 18;
    const MAX_AGE = 120;
}

// 2. Simple, permanent values
const DB_NAME = "myapp";
const DB_USER = "admin";
?>
```

## Important Characteristics

### 1. No Dollar Sign

```php
<?php
// Constants do NOT use $
define("COUNTRY", "USA");

echo COUNTRY;   // ✓ Correct
echo $COUNTRY;  // ✗ Wrong (treats $COUNTRY as a variable)
?>
```

### 2. Case-Sensitive by Default

```php
<?php
define("GREETING", "Hello");

echo GREETING;  // Hello (correct)
echo greeting;  // ERROR! Undefined constant

// Make case-insensitive (not recommended)
define("FAREWELL", "Goodbye", true);
echo FAREWELL;  // Goodbye
echo farewell;  // Goodbye (works!)
?>
```

### 3. Globally Accessible

```php
<?php
define("SITE_VERSION", "2.0");

function displayInfo() {
    // No 'global' keyword needed!
    echo "Version: " . SITE_VERSION;
}

class App {
    public function getVersion() {
        return SITE_VERSION;  // Still accessible
    }
}

displayInfo();          // Version: 2.0
echo new App()->getVersion();  // 2.0
?>
```

### 4. Cannot Be Changed or Unset

```php
<?php
define("API_KEY", "abc123");

// ERROR: Cannot change
// API_KEY = "xyz789";

// ERROR: Cannot unset
// unset(API_KEY);

// ERROR: Cannot redefine
// define("API_KEY", "new_value");

// But you CAN define it conditionally
if (!defined("API_KEY")) {
    define("API_KEY", "default_key");
}
?>
```

## Checking for Constants

### Using `defined()`

```php
<?php
define("WEBSITE", "example.com");

// Check if constant exists
if (defined("WEBSITE")) {
    echo "WEBSITE is defined";  // Executes
}

if (defined("UNKNOWN")) {
    echo "UNKNOWN is defined";  // Does NOT execute
}

// Get constant value dynamically
$name = "WEBSITE";
echo constant($name);  // example.com
?>
```

### Using `get_defined_constants()`

```php
<?php
define("APP_NAME", "MyApp");
define("APP_VERSION", "1.0");

// Get all defined constants
$constants = get_defined_constants(true);

// Show user-defined constants only
echo "User Constants:\n";
foreach ($constants['user'] as $name => $value) {
    echo "$name = $value\n";
}

// APP_NAME = MyApp
// APP_VERSION = 1.0
?>
```

## Magic Constants

PHP provides special constants that change based on where they're used:

```php
<?php
// __LINE__ - Current line number
echo __LINE__;              // 2

// __FILE__ - Full path of the file
echo __FILE__;              // /path/to/script.php

// __DIR__ - Directory of the file
echo __DIR__;               // /path/to

// __FUNCTION__ - Function name
function myFunc() {
    echo __FUNCTION__;      // myFunc
}

// __CLASS__ - Class name
class MyClass {
    public function test() {
        echo __CLASS__;     // MyClass
    }
}

// __METHOD__ - Class method name
class Demo {
    public function greet() {
        echo __METHOD__;    // Demo::greet
    }
}

// __NAMESPACE__ - Current namespace
namespace MyApp;
echo __NAMESPACE__;         // MyApp
?>
```

## Practical Examples

### Configuration Constants

```php
<?php
// Database configuration
define("DB_HOST", "localhost");
define("DB_USER", "admin");
define("DB_PASS", "secret123");
define("DB_NAME", "myapp");

// Application settings
define("SITE_NAME", "My Website");
define("SITE_URL", "https://example.com");
define("ADMIN_EMAIL", "admin@example.com");

// Debug settings
define("DEBUG_MODE", true);
define("LOG_LEVEL", "INFO");

// Feature flags
define("ENABLE_CACHE", true);
define("ENABLE_CDN", true);

// Usage
function connectDatabase() {
    $conn = "Server: " . DB_HOST . ", User: " . DB_USER;
    return $conn;
}

echo connectDatabase();  // Server: localhost, User: admin
?>
```

### Class Constants for Enums

```php
<?php
class OrderStatus {
    const PENDING = "pending";
    const PROCESSING = "processing";
    const COMPLETED = "completed";
    const CANCELLED = "cancelled";
}

class Payment {
    const METHOD_CARD = "card";
    const METHOD_PAYPAL = "paypal";
    const METHOD_TRANSFER = "transfer";
}

// Usage
$order = [
    "id" => 1001,
    "status" => OrderStatus::PENDING,
    "payment" => Payment::METHOD_CARD
];

if ($order['status'] === OrderStatus::COMPLETED) {
    echo "Order is complete";
}
?>
```

### Math and Science Constants

```php
<?php
// Mathematical constants
define("PI", 3.14159265359);
define("E", 2.71828182846);
define("GOLDEN_RATIO", 1.61803398875);

// Physics constants (if needed)
define("SPEED_OF_LIGHT", 299792458);  // m/s
define("GRAVITY", 9.81);              // m/s²

// Application constants
define("ITEMS_PER_PAGE", 20);
define("MAX_FILE_SIZE", 5242880);     // 5 MB
define("SESSION_TIMEOUT", 1800);      // 30 minutes
?>
```

### Using Constants in Conditions

```php
<?php
define("MIN_PASSWORD_LENGTH", 8);
define("MAX_LOGIN_ATTEMPTS", 5);

function validatePassword($password) {
    if (strlen($password) < MIN_PASSWORD_LENGTH) {
        return false;
    }
    return true;
}

function checkLoginAttempts($attempts) {
    return $attempts <= MAX_LOGIN_ATTEMPTS;
}

// Usage
if (validatePassword("secret123")) {
    echo "Password is valid";
}

if (checkLoginAttempts(3)) {
    echo "Still have login attempts";
}
?>
```

## Best Practices

### 1. Use UPPERCASE Names

```php
<?php
// Good
define("DATABASE_HOST", "localhost");

// Avoid
define("database_host", "localhost");
define("DatabaseHost", "localhost");
?>
```

### 2. Group Related Constants

```php
<?php
// Database constants
define("DB_HOST", "localhost");
define("DB_USER", "admin");
define("DB_PASS", "password");

// Email constants
define("MAIL_HOST", "smtp.gmail.com");
define("MAIL_PORT", 587);

// Application constants
define("APP_NAME", "MyApp");
define("APP_VERSION", "1.0");
?>
```

### 3. Use Configuration Files

```php
<?php
// config.php
define("SITE_NAME", "My Website");
define("SITE_URL", "https://example.com");
define("DEBUG", true);

// app.php
require 'config.php';

echo SITE_NAME;  // Works anywhere
?>
```

### 4. Check Before Using

```php
<?php
// Safe approach
if (defined("API_KEY")) {
    $key = API_KEY;
} else {
    $key = "default_key";
}

// Or use conditional define
if (!defined("API_KEY")) {
    define("API_KEY", "default_key");
}
?>
```

## Constants vs Variables

| Aspect | Constant | Variable |
|--------|----------|----------|
| Prefix | None | `$` |
| Change | ✗ Cannot | ✓ Can change |
| Scope | Global | Local/Global |
| Runtime | Cannot unset | Can unset |
| Memory | Lighter | Heavier |
| Case | Sensitive (by default) | Sensitive |
| When to use | Fixed values | Changeable values |

## Common Mistakes

### Mistake 1: Using `$` with Constants

```php
<?php
define("NAME", "John");

echo NAME;      // ✓ Correct
echo $NAME;     // ✗ Wrong (undefined variable)
?>
```

### Mistake 2: Trying to Change Constants

```php
<?php
define("MAX_USERS", 100);

// ERROR: Cannot reassign
// MAX_USERS = 200;

// But you CAN work with the value
$total_users = MAX_USERS * 2;
echo $total_users;  // 200
?>
```

### Mistake 3: Case Sensitivity

```php
<?php
define("GREETING", "Hello");

echo GREETING;  // ✓ Hello
echo greeting;  // ✗ WARNING: Undefined constant
echo Greeting;  // ✗ WARNING: Undefined constant
?>
```

## Key Takeaways

✓ Constants are values that **cannot be changed** after definition
✓ Use `define()` function or `const` keyword
✓ Constants are **globally accessible** without `global` keyword
✓ Constants use **UPPERCASE_WITH_UNDERSCORES** naming
✓ **No dollar sign (`$`)** before constant names
✓ Use `defined()` to check if constant exists
✓ Use `constant()` to get value dynamically
✓ **Magic constants** (`__FILE__`, `__LINE__`, etc.) provide special info
✓ Perfect for **configuration values** and **settings**
✓ Better than variables for **fixed values** that shouldn't change
✓ **Class constants** organize related constants together
