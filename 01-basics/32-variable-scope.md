# Variable Scope

## Table of Contents
1. [Overview](#overview)
2. [Scope Types](#scope-types)
3. [Function Scope](#function-scope)
4. [Global Keyword](#global-keyword)
5. [Static Variables](#static-variables)
6. [Superglobals](#superglobals)
7. [Class Scope](#class-scope)
8. [Practical Examples](#practical-examples)
9. [Common Mistakes](#common-mistakes)

---

## Overview

Variable scope determines where a variable is accessible.

**Scope types:**
- **Local scope**: Inside function (not accessible outside)
- **Global scope**: Outside functions (accessible everywhere by default)
- **Function parameters**: Only in function
- **Static scope**: Persistent across calls
- **Superglobal scope**: Accessible everywhere ($GLOBALS, $_GET, etc.)

---

## Scope Types

### Global Scope

```php
<?php
// Global scope
$global_var = "I'm global";

function test() {
    // Can't access $global_var directly
    echo $global_var;  // Undefined variable!
}

test();

// Accessible outside function
echo $global_var;  // Works: I'm global
?>
```

### Local Scope (Function)

```php
<?php
// Global
$global = "global";

function test() {
    // Local scope
    $local = "local";
    
    echo $global;  // Undefined! (not accessible)
    echo $local;   // Works: local
}

test();

echo $local;  // Undefined! (not accessible outside)
?>
```

### Function Parameters

```php
<?php
function greet($name) {
    // $name only exists in this function
    echo "Hello, $name";
}

greet("John");
// echo $name;  // Undefined! (not accessible)

// Parameter is local to function
function add($a, $b) {
    return $a + $b;
}

echo add(5, 3);  // OK
// echo $a;  // Undefined!
?>
```

### Block Scope

```php
<?php
// PHP doesn't have block scope like JavaScript
// Variables defined in blocks are accessible outside

if (true) {
    $inside_block = "I'm in if block";
}

echo $inside_block;  // Works! (not in block scope)

// This is different than JavaScript
for ($i = 0; $i < 3; $i++) {
    $loop_var = $i;
}

echo $loop_var;  // Works! (not in block scope)
echo $i;        // Works! (not in block scope)
?>
```

---

## Function Scope

### Local Variables

```php
<?php
function createUser() {
    $name = "John";      // Local
    $email = "john@example.com";  // Local
    $age = 30;           // Local
    
    // All accessible here
    return "$name <$email> ($age)";
}

echo createUser();  // John <john@example.com> (30)

// Outside function
echo $name;   // Undefined!
echo $email;  // Undefined!
echo $age;    // Undefined!
?>
```

### Nested Functions

```php
<?php
function outer() {
    $outer_var = "I'm outer";
    
    function inner() {
        // Can't access $outer_var directly
        echo $outer_var;  // Undefined!
    }
    
    inner();  // Call inner
    echo $outer_var;  // Works: I'm outer
}

outer();
// inner();  // Undefined! (inner function not in global scope until outer called)
?>
```

### Variable Visibility

```php
<?php
$global = "global";

function test() {
    $local = "local";
    
    function nested() {
        // Can't see $global or $local
        echo $global;  // Undefined!
        echo $local;   // Undefined!
    }
    
    nested();  // Call from here
    echo $local;  // Works
}

test();
// echo $global;  // Scoping rule: works at global level
?>
```

---

## Global Keyword

### Accessing Global Variables

```php
<?php
$global_var = "I'm global";
$counter = 0;

function increment() {
    global $counter;  // Access global variable
    
    $counter++;
}

increment();
increment();
increment();

echo $counter;  // 3 (global was modified)
?>
```

### Multiple Global Variables

```php
<?php
$name = "John";
$age = 30;
$email = "john@example.com";

function displayUser() {
    global $name, $age, $email;  // Access multiple globals
    
    echo "$name is $age years old\n";
    echo "Email: $email\n";
}

displayUser();
// Output:
// John is 30 years old
// Email: john@example.com
?>
```

### $GLOBALS Array

```php
<?php
$global_var = "global";

function test() {
    // Access via $GLOBALS array
    echo $GLOBALS['global_var'];  // Works: global
    
    // Modify global
    $GLOBALS['global_var'] = "modified";
}

test();
echo $global_var;  // modified
?>
```

### Best Practice: Pass Parameters

```php
<?php
// ❌ Bad: Using global
$database = null;

function getUserGlobal($id) {
    global $database;  // Dependency not obvious
    return $database->query("SELECT * FROM users WHERE id = $id");
}

// ✓ Good: Pass as parameter
function getUser($id, $database) {
    return $database->query("SELECT * FROM users WHERE id = $id");
}

// ✓ Best: Use dependency injection
class UserService {
    private $database;
    
    public function __construct($database) {
        $this->database = $database;
    }
    
    public function getUser($id) {
        return $this->database->query("SELECT * FROM users WHERE id = $id");
    }
}
?>
```

---

## Static Variables

### Persistent State

```php
<?php
function counter() {
    static $count = 0;  // Initialized once only
    
    $count++;
    return $count;
}

echo counter();  // 1
echo counter();  // 2
echo counter();  // 3
echo counter();  // 4
?>
```

### Static Initialization

```php
<?php
function initialize() {
    static $initialized = false;
    
    if (!$initialized) {
        echo "Initializing...\n";
        $initialized = true;  // Set to true, stays for next call
    }
    
    echo "Ready\n";
}

initialize();  // Initializing... Ready
initialize();  // Ready (initialization skipped)
initialize();  // Ready (initialization skipped)
?>
```

### Multiple Static Variables

```php
<?php
function userManager() {
    static $user_count = 0;
    static $last_user = null;
    
    $user_count++;
    $last_user = "User $user_count";
    
    return "$last_user (Total: $user_count)";
}

echo userManager();  // User 1 (Total: 1)
echo userManager();  // User 2 (Total: 2)
echo userManager();  // User 3 (Total: 3)
?>
```

### Static in Loops

```php
<?php
function generateId() {
    static $id = 0;
    return ++$id;
}

// Each call increments
for ($i = 0; $i < 3; $i++) {
    echo generateId() . "\n";  // 1 2 3
}

for ($i = 0; $i < 3; $i++) {
    echo generateId() . "\n";  // 4 5 6 (continues)
}
?>
```

---

## Superglobals

Superglobals are accessible everywhere.

### $_GET

```php
<?php
// URL: page.php?name=John&age=30

// In any function
function displayUserInfo() {
    echo $_GET['name'];  // John (accessible)
    echo $_GET['age'];   // 30 (accessible)
}

displayUserInfo();

// At global level
echo $_GET['name'];  // John (accessible)
?>
```

### $_POST

```php
<?php
// Form submission
// <form method="POST">
//     <input name="username">
//     <input name="password">
// </form>

function validateLogin() {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username && $password) {
        // Process login
        return true;
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateLogin();
}
?>
```

### $_SESSION

```php
<?php
// Start session (must be first line)
session_start();

// Set session variable (global)
$_SESSION['user_id'] = 123;
$_SESSION['username'] = 'john';

function getUserInfo() {
    // Accessible in any function
    return $_SESSION['user_id'];
}

echo getUserInfo();  // 123
?>
```

### $_SERVER

```php
<?php
// Server information (global)

// Current file path
echo $_SERVER['PHP_SELF'];       // /index.php

// Request method
echo $_SERVER['REQUEST_METHOD']; // GET or POST

// Server hostname
echo $_SERVER['HTTP_HOST'];      // example.com

// Can be accessed in functions
function getRequestMethod() {
    return $_SERVER['REQUEST_METHOD'];
}
?>
```

### Other Superglobals

```php
<?php
// $_ENV - Environment variables
echo $_ENV['HOME'];

// $_COOKIE - HTTP cookies
echo $_COOKIE['user_id'];

// $_FILES - Uploaded files
$_FILES['avatar']['tmp_name'];

// $_REQUEST - GET, POST, or COOKIE combined
echo $_REQUEST['search'];

// $GLOBALS - All global variables
$GLOBALS['my_var'] = 'value';
?>
```

---

## Class Scope

### Property Visibility

```php
<?php
class User {
    private $id;         // Only in this class
    protected $name;     // In this class and subclasses
    public $email;       // Everywhere
    
    public function __construct($id, $name, $email) {
        $this->id = $id;      // Access in class
        $this->name = $name;
        $this->email = $email;
    }
    
    public function getId() {
        return $this->id;  // Access in class method
    }
}

$user = new User(1, 'John', 'john@example.com');

// Access public
echo $user->email;      // john@example.com

// Can't access private/protected
echo $user->id;         // Error!
echo $user->name;       // Error!
?>
```

### Static Properties and Methods

```php
<?php
class Config {
    public static $app_name = "My App";
    private static $database = null;
    
    public static function getDatabase() {
        return self::$database;
    }
    
    public static function setDatabase($db) {
        self::$database = $db;
    }
}

// Access static from anywhere
echo Config::$app_name;           // My App
Config::setDatabase($db);

// In function
function getAppName() {
    return Config::$app_name;  // My App
}
?>
```

---

## Practical Examples

### Counter Service

```php
<?php
class Counter {
    private static $count = 0;
    
    public static function increment() {
        return ++self::$count;
    }
    
    public static function get() {
        return self::$count;
    }
    
    public static function reset() {
        self::$count = 0;
    }
}

echo Counter::increment();  // 1
echo Counter::increment();  // 2
echo Counter::get();        // 2
Counter::reset();
echo Counter::get();        // 0
?>
```

### Dependency Injection

```php
<?php
class Database {
    // Implementation
}

class UserRepository {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db;  // Stored in class scope
    }
    
    public function find($id) {
        return $this->db->query("SELECT * FROM users WHERE id = $id");
    }
}

function processUser($userId) {
    $db = new Database();
    $repo = new UserRepository($db);  // Dependency injected
    return $repo->find($userId);
}

processUser(123);
?>
```

### Configuration Management

```php
<?php
class AppConfig {
    private static $config = [];
    
    public static function set($key, $value) {
        self::$config[$key] = $value;
    }
    
    public static function get($key, $default = null) {
        return self::$config[$key] ?? $default;
    }
    
    public static function all() {
        return self::$config;
    }
}

// Initialize
AppConfig::set('db_host', 'localhost');
AppConfig::set('db_name', 'myapp');
AppConfig::set('debug', true);

// Access anywhere
function connectDatabase() {
    $host = AppConfig::get('db_host');
    $name = AppConfig::get('db_name');
    
    return new Database($host, $name);
}
?>
```

---

## Common Mistakes

### 1. Forgetting Global Keyword

```php
<?php
$counter = 0;

function increment() {
    // ❌ Wrong: Creates local variable instead of modifying global
    $counter++;
    echo $counter;  // 1
}

increment();
increment();
echo $counter;  // 0 (global unchanged!)

// ✓ Correct: Use global keyword
function incrementCorrect() {
    global $counter;
    $counter++;
}

incrementCorrect();
incrementCorrect();
echo $counter;  // 2
?>
```

### 2. Using Global Instead of Parameters

```php
<?php
// ❌ Bad: Hidden dependency
$database = null;

function getUserBad($id) {
    global $database;  // Where does $database come from?
    return $database->find($id);
}

// ✓ Good: Clear dependency
function getUser($id, $database) {
    return $database->find($id);
}

// ✓ Better: Class-based
class UserService {
    private $database;
    
    public function __construct($database) {
        $this->database = $database;
    }
    
    public function getUser($id) {
        return $this->database->find($id);
    }
}
?>
```

### 3. Static Variables Persisting

```php
<?php
// ❌ Problem: Static persists across tests
function getRandomId() {
    static $count = 0;
    return ++$count;
}

echo getRandomId();  // 1
echo getRandomId();  // 2
// Can't reset easily

// ✓ Solution: Use class with reset
class IdGenerator {
    private static $count = 0;
    
    public static function next() {
        return ++self::$count;
    }
    
    public static function reset() {
        self::$count = 0;
    }
}

echo IdGenerator::next();  // 1
IdGenerator::reset();
echo IdGenerator::next();  // 1
?>
```

### 4. Modifying Superglobals Carelessly

```php
<?php
// ❌ Bad: Directly using $_GET (security issue)
$name = $_GET['name'];  // Not sanitized!
echo "Hello $name";

// ✓ Good: Validate and sanitize
$name = $_GET['name'] ?? '';
$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
echo "Hello $name";

// ✓ Better: Use function
function getQueryParam($name, $default = '') {
    $value = $_GET[$name] ?? $default;
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

echo "Hello " . getQueryParam('name');
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class ScopeDemo {
    private static $instances = 0;
    private static $config = [];
    
    private $id;
    private $name;
    public $public_var = "public";
    
    public function __construct($name) {
        self::$instances++;
        $this->id = self::$instances;
        $this->name = $name;
    }
    
    public static function getInstanceCount() {
        return self::$instances;
    }
    
    public static function setConfig($key, $value) {
        self::$config[$key] = $value;
    }
    
    public static function getConfig($key) {
        return self::$config[$key] ?? null;
    }
    
    public function display() {
        echo "ID: $this->id\n";
        echo "Name: $this->name\n";
    }
    
    public function getId() {
        return $this->id;  // Private, accessed via method
    }
}

// Usage
ScopeDemo::setConfig('app', 'MyApp');

$obj1 = new ScopeDemo('Object 1');
$obj2 = new ScopeDemo('Object 2');
$obj3 = new ScopeDemo('Object 3');

echo "Total instances: " . ScopeDemo::getInstanceCount() . "\n";
echo "Config: " . ScopeDemo::getConfig('app') . "\n";

$obj1->display();

// Can't access private
// echo $obj1->id;    // Error!

// Can access via method
echo $obj1->getId() . "\n";
?>
```

---

## Next Steps

✅ Understand variable scope  
→ Study [references](32-reference.md)  
→ Learn [functions](28-functions.md)  
→ Explore [OOP](../03-oop/)
