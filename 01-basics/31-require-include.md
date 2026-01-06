# File Inclusion (require, include, require_once, include_once)

## Table of Contents
1. [Overview](#overview)
2. [require vs include](#require-vs-include)
3. [Single vs Multiple](#single-vs-multiple)
4. [File Path Resolution](#file-path-resolution)
5. [Practical Patterns](#practical-patterns)
6. [Best Practices](#best-practices)
7. [Common Mistakes](#common-mistakes)

---

## Overview

File inclusion allows reusing code from other PHP files.

**Four statements:**
- `require` - Include file, error if missing (fatal)
- `include` - Include file, warning if missing (continues)
- `require_once` - Require once, prevent re-inclusion
- `include_once` - Include once, prevent re-inclusion

---

## require vs include

### require: Mandatory

```php
<?php
// If file not found: Fatal error (execution stops)
require 'config.php';  // File must exist

// Code after error doesn't execute
echo "After require";
?>
```

### include: Optional

```php
<?php
// If file not found: Warning (execution continues)
include 'optional.php';  // File missing is warning

// Code continues despite missing file
echo "After include";  // This executes even if file missing
?>
```

### Comparison

```php
<?php
// Use require for critical files
require 'database.php';     // DB connection essential
require 'config.php';       // Config required
require 'functions.php';    // Core functions needed

// Use include for optional files
include 'analytics.php';    // Nice to have
include 'tracking.php';     // Optional feature
include 'social.php';       // Optional feature

// Real example
function loadTemplate($name) {
    $path = "templates/$name.php";
    
    // Include template (might not exist for all pages)
    if (file_exists($path)) {
        include $path;
    } else {
        echo "Template not found";
    }
}
?>
```

---

## Single vs Multiple

### require: Every Time

```php
<?php
// require includes file every time
// If file included multiple times: code executes multiple times

require 'functions.php';   // Executes
require 'functions.php';   // Executes again!

// Functions might be redeclared (error if not using namespaces)
function helper() {}
function helper() {}  // Error: already defined
?>
```

### require_once: First Time Only

```php
<?php
// require_once includes file only once
require_once 'functions.php';   // Executes
require_once 'functions.php';   // Skipped
require_once 'functions.php';   // Skipped

// File included only once, safe to call multiple times
function helper() {}
// Redeclaration prevented
?>
```

### include vs include_once

```php
<?php
// include: every time
include 'data.php';         // Executes
include 'data.php';         // Executes again

// include_once: first time only
include_once 'data.php';    // Executes
include_once 'data.php';    // Skipped
?>
```

---

## File Path Resolution

### Relative Paths

```php
<?php
// Relative to current directory
require 'config.php';           // Same directory
require './config.php';         // Same directory (explicit)
require '../config.php';        // Parent directory
require 'includes/helpers.php'; // Subdirectory

// From /var/www/html/index.php:
require 'config.php';  // Looks for /var/www/html/config.php
require 'lib/db.php';  // Looks for /var/www/html/lib/db.php
?>
```

### Absolute Paths

```php
<?php
// Absolute paths (recommended)
require __DIR__ . '/config.php';          // Current file directory
require dirname(__FILE__) . '/config.php'; // Same (older syntax)

// From any location, same file loaded
require '/var/www/html/config.php';
require '/home/user/app/database.php';
?>
```

### Using Constants

```php
<?php
// Define base path once
define('BASE_PATH', __DIR__ . '/');
define('APP_PATH', BASE_PATH . 'app/');
define('LIB_PATH', BASE_PATH . 'lib/');

// Use throughout application
require APP_PATH . 'config.php';
require APP_PATH . 'functions.php';
require LIB_PATH . 'database.php';
?>
```

### Search Paths

```php
<?php
// PHP searches include_path if path not found
// ini_get('include_path') shows search paths

// With include_path, this works:
include 'functions.php';  // Searches include_path

// Set include_path
set_include_path(
    __DIR__ . '/lib' . PATH_SEPARATOR .
    get_include_path()
);

// Now searches /app/lib first, then default paths
include 'database.php';
?>
```

---

## Practical Patterns

### Application Structure

```
app/
├── index.php        // Entry point
├── config.php       // Configuration
├── functions.php    // Helper functions
├── app/
│   ├── User.php
│   ├── Product.php
│   └── Order.php
├── lib/
│   ├── Database.php
│   └── Auth.php
└── templates/
    ├── header.php
    ├── footer.php
    └── products.php
```

### index.php Example

```php
<?php
// index.php - Entry point

// Include required files
require __DIR__ . '/config.php';          // Configuration
require __DIR__ . '/functions.php';       // Helpers
require __DIR__ . '/lib/Database.php';    // Database class
require __DIR__ . '/app/User.php';        // User class
require __DIR__ . '/app/Product.php';     // Product class

// Now everything is available
$db = new Database();
$user = new User($db);
$product = new Product($db);

// Process request
$action = $_GET['action'] ?? 'home';

if ($action === 'product') {
    include __DIR__ . '/templates/header.php';
    include __DIR__ . '/templates/products.php';
    include __DIR__ . '/templates/footer.php';
}
?>
```

### config.php Example

```php
<?php
// config.php - Configuration file

// Database settings
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'myapp');

// Application settings
define('APP_NAME', 'My Application');
define('DEBUG', true);

// Paths
define('BASE_PATH', dirname(__FILE__) . '/');
define('APP_PATH', BASE_PATH . 'app/');
define('LIB_PATH', BASE_PATH . 'lib/');
define('TEMPLATE_PATH', BASE_PATH . 'templates/');

// Environment
define('ENVIRONMENT', getenv('APP_ENV') ?? 'development');

// Error reporting
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>
```

### functions.php Example

```php
<?php
// functions.php - Helper functions

function debug($data) {
    if (DEBUG) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }
}

function sanitize($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function get_template_path($template) {
    $path = TEMPLATE_PATH . $template . '.php';
    if (!file_exists($path)) {
        throw new Exception("Template not found: $template");
    }
    return $path;
}

function render_template($template, $data = []) {
    extract($data);  // Convert array to variables
    include get_template_path($template);
}
?>
```

### Template System

```php
<?php
// templates/header.php
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($title ?? APP_NAME) ?></title>
</head>
<body>
    <header>
        <h1><?= APP_NAME ?></h1>
        <nav>
            <a href="index.php?action=home">Home</a>
            <a href="index.php?action=products">Products</a>
            <a href="index.php?action=about">About</a>
        </nav>
    </header>
```

```php
<?php
// templates/footer.php
?>
    <footer>
        <p>&copy; 2024 <?= APP_NAME ?></p>
    </footer>
</body>
</html>
```

```php
<?php
// Usage in index.php
function render($template, $data = []) {
    include __DIR__ . '/templates/header.php';
    extract($data);
    include __DIR__ . "/templates/{$template}.php";
    include __DIR__ . '/templates/footer.php';
}

// In page handler
render('products', ['title' => 'Our Products']);
?>
```

---

## Best Practices

### 1. Use Absolute Paths with __DIR__

```php
<?php
// ❌ Bad: Relative path (works in some contexts, fails in others)
require '../config.php';

// ✓ Good: Absolute path (always works)
require __DIR__ . '/../config.php';

// ✓ Best: Define base path constant
define('BASE_PATH', dirname(__DIR__) . '/');
require BASE_PATH . 'config.php';
?>
```

### 2. Use require_once for Classes/Functions

```php
<?php
// ❌ Bad: Can't include twice (functions redeclared)
require 'functions.php';
require 'functions.php';  // Error

// ✓ Good: Safe to include multiple times
require_once 'functions.php';
require_once 'functions.php';  // Skipped
require_once 'functions.php';  // Skipped
?>
```

### 3. Organize Code by Type

```php
<?php
// ✓ Good organization
require_once BASE_PATH . 'config.php';           // Configuration
require_once BASE_PATH . 'functions.php';       // Functions
require_once LIB_PATH . 'Database.php';         // Classes
require_once LIB_PATH . 'Logger.php';           // Classes

// include for templates/content that may vary
include TEMPLATE_PATH . $page . '.php';
?>
```

### 4. Check File Existence

```php
<?php
// ❌ Risky: Assumes file exists
include 'optional.php';

// ✓ Safe: Check before including
if (file_exists('optional.php')) {
    include 'optional.php';
}

// ✓ For required files, use require (fails loudly)
require 'essential.php';  // Error if missing
?>
```

### 5. Use Namespaces with Classes

```php
<?php
// config.php
namespace App;

define('BASE_PATH', __DIR__ . '/');
define('DB_HOST', 'localhost');

// app/User.php
namespace App;

class User {
    // Class definition
}

// index.php
require __DIR__ . '/config.php';
require __DIR__ . '/app/User.php';

$user = new App\User();
?>
```

---

## Common Mistakes

### 1. Using Relative Paths

```php
<?php
// ❌ Bad: Works only from specific directory
require '../config.php';  // Breaks if called from different directory

// ✓ Good: Works from anywhere
require __DIR__ . '/../config.php';

// Example problem:
// /app/index.php calls /app/functions.php
// functions.php does: require '../config.php'
// Works fine

// But /app/api/request.php calls functions.php
// functions.php does: require '../config.php'
// Now looks for /config.php (wrong location!)

// Always use __DIR__ to be safe
?>
```

### 2. Re-including Functions

```php
<?php
// ❌ Bad: Functions redeclared (error)
require 'functions.php';
require 'functions.php';

// ✓ Good: Use require_once
require_once 'functions.php';
require_once 'functions.php';  // Skipped

// ✓ Or use namespaces to avoid conflicts
?>
```

### 3. Assuming Global Scope

```php
<?php
// ❌ Wrong: Variables not automatically global
// file1.php
$name = 'John';
require 'file2.php';

// file2.php
echo $name;  // Works (included in same scope)

// But in function:
function process() {
    require 'file2.php';
    echo $name;  // Error: $name not defined in function scope
}

// ✓ Correct: Pass variables explicitly
function process() {
    $name = 'John';
    include 'file2.php';  // Now $name available
}

// Or return values
function getData() {
    return include 'data.php';
}
?>
```

### 4. Not Checking Include Status

```php
<?php
// ❌ Assumes include succeeded
include 'optional.php';
callFunctionFromOptional();  // Error if file missing

// ✓ Check result
$included = include 'optional.php';
if ($included) {
    callFunctionFromOptional();
}

// Or check for returned value
$config = include 'config.php';
if (!$config) {
    die('Config not found');
}
?>
```

### 5. Security Issues

```php
<?php
// ❌ Bad: Including user input (dangerous!)
$page = $_GET['page'];
include $page . '.php';  // Can include arbitrary files

// ✓ Good: Whitelist allowed files
$allowed = ['home', 'products', 'contact'];
$page = $_GET['page'] ?? 'home';

if (!in_array($page, $allowed)) {
    $page = 'home';
}

include __DIR__ . "/pages/{$page}.php";

// ✓ Or use switch/if instead of dynamic includes
switch ($_GET['page'] ?? 'home') {
    case 'products':
        include __DIR__ . '/pages/products.php';
        break;
    case 'contact':
        include __DIR__ . '/pages/contact.php';
        break;
    default:
        include __DIR__ . '/pages/home.php';
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

// Application Bootstrap
define('BASE_PATH', dirname(__FILE__) . '/');
define('APP_PATH', BASE_PATH . 'app/');
define('LIB_PATH', BASE_PATH . 'lib/');
define('TEMPLATE_PATH', BASE_PATH . 'templates/');
define('DEBUG', true);

// Load core files (once)
require_once BASE_PATH . 'config.php';
require_once BASE_PATH . 'functions.php';
require_once LIB_PATH . 'Database.php';
require_once LIB_PATH . 'Logger.php';
require_once APP_PATH . 'User.php';
require_once APP_PATH . 'Product.php';

// Initialize application
class Application {
    private $db;
    private $logger;
    
    public function __construct() {
        $this->db = new Database();
        $this->logger = new Logger();
    }
    
    public function run() {
        $action = $_GET['action'] ?? 'home';
        
        // Whitelist actions
        $allowed = ['home', 'products', 'contact', 'login'];
        
        if (!in_array($action, $allowed)) {
            $action = 'home';
        }
        
        // Include template (verified safe)
        include TEMPLATE_PATH . $action . '.php';
    }
}

// Start application
$app = new Application();
$app->run();
?>
```

---

## Next Steps

✅ Understand file inclusion  
→ Study [functions](28-functions.md)  
→ Learn [variable scope](31-variable-scope.md)  
→ Explore [OOP](../03-oop/)
