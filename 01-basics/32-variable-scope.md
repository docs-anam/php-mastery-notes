# Variable Scope in PHP

## Overview

Variable scope defines where a variable is accessible and can be used in your code. Understanding scope is crucial for writing maintainable code and avoiding bugs. PHP has three main scope levels: local, global, and static. Additionally, function parameters have their own scope.

## Global vs Local Scope

### Global Scope

```php
<?php
// Global scope - accessible everywhere
$globalVar = "I'm global";

function testGlobal() {
    // Can't access $globalVar directly here
    echo isset($globalVar) ? $globalVar : "Not accessible\n";  // Not accessible
}

testGlobal();
// Output: Not accessible

// Access via GLOBALS array
function testGlobalArray() {
    echo $GLOBALS['globalVar'] . "\n";  // Accessible
}

testGlobalArray();
// Output: I'm global
?>
```

### Local Scope

```php
<?php
function testLocal() {
    $localVar = "I'm local";
    echo $localVar . "\n";  // Accessible here
}

testLocal();
// Output: I'm local

echo $localVar;  // Error - not accessible outside function
?>
```

### Using the Global Keyword

```php
<?php
$globalCount = 0;

function increment() {
    global $globalCount;  // Declare variable as global
    $globalCount++;
    echo "Count: $globalCount\n";
}

increment();  // Output: Count: 1
increment();  // Output: Count: 2
increment();  // Output: Count: 3

echo "Final count: $globalCount\n";  // Output: Final count: 3
?>
```

## Function Parameters and Return Values

### Function Parameter Scope

```php
<?php
function greet($name, $greeting = "Hello") {
    // $name and $greeting are local to function
    echo "$greeting, $name!\n";
}

greet("John");  // Output: Hello, John!
greet("Jane", "Hi");  // Output: Hi, Jane!

echo $name;  // Error - $name not defined outside function
?>
```

### Returning Values (Better Than Globals)

```php
<?php
// BAD - modifying global
$total = 0;

function addToTotal($amount) {
    global $total;
    $total += $amount;
}

// GOOD - return value
function addAmount($current, $amount) {
    return $current + $amount;
}

$total = 0;
$total = addAmount($total, 10);
$total = addAmount($total, 20);
echo "Total: $total\n";  // Output: Total: 30
?>
```

## Static Variables

### Static Variable Persistence

```php
<?php
function counter() {
    static $count = 0;  // Initialized only once
    $count++;
    echo "Count: $count\n";
}

counter();  // Output: Count: 1
counter();  // Output: Count: 2
counter();  // Output: Count: 3
counter();  // Output: Count: 4

// $count persists between function calls
?>
```

### Static for Class Properties

```php
<?php
class User {
    private static $userCount = 0;
    public $name;
    
    public function __construct($name) {
        $this->name = $name;
        self::$userCount++;
    }
    
    public static function getTotalUsers() {
        return self::$userCount;
    }
}

new User("John");
new User("Jane");
new User("Bob");

echo "Total users: " . User::getTotalUsers() . "\n";
// Output: Total users: 3
?>
```

## Superglobal Variables

### Built-in Superglobals

```php
<?php
// Superglobals are accessible everywhere
echo "Server info: " . $_SERVER['PHP_SELF'] . "\n";
echo "Current URL: " . $_SERVER['REQUEST_URI'] . "\n";

// $_GET - URL query parameters
$id = isset($_GET['id']) ? $_GET['id'] : null;

// $_POST - Form data
$username = isset($_POST['username']) ? $_POST['username'] : null;

// $_REQUEST - GET + POST combined
$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : null;

// $_COOKIE - Cookie data
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';

// $_SESSION - Session data
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = 'guest';
}

// $_FILES - Uploaded files
// $_ENV - Environment variables
// $GLOBALS - All global variables
?>
```

## Practical Examples

### Encapsulation with Functions

```php
<?php
// BAD - exposed global variables
$userData = null;

function loadUser($id) {
    global $userData;
    $userData = ['id' => $id, 'name' => 'John'];
}

loadUser(5);
echo $userData['name'];  // Dependent on global state

// GOOD - encapsulated
function getUserData($id) {
    return ['id' => $id, 'name' => 'John'];
}

$userData = getUserData(5);
echo $userData['name'];  // Clear input/output
?>
```

### Static for Configuration

```php
<?php
class Config {
    private static $settings = null;
    
    public static function load($file) {
        if (self::$settings === null) {
            self::$settings = require $file;
        }
        return self::$settings;
    }
    
    public static function get($key) {
        $settings = self::load('config.php');
        return isset($settings[$key]) ? $settings[$key] : null;
    }
}

$dbHost = Config::get('database_host');
$dbName = Config::get('database_name');
?>
```

### Session Management

```php
<?php
session_start();

class SessionManager {
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    public static function has($key) {
        return isset($_SESSION[$key]);
    }
    
    public static function remove($key) {
        unset($_SESSION[$key]);
    }
}

SessionManager::set('user_id', 42);
echo SessionManager::get('user_id');  // Output: 42
?>
```

## Common Pitfalls

### Misunderstanding Global Keyword

```php
<?php
$x = 10;

function changeX() {
    global $x;  // Must declare as global
    $x = 20;    // Now modifies the global $x
}

changeX();
echo $x;  // Output: 20

// WITHOUT global keyword
function changeX_bad() {
    $x = 30;  // Creates local $x, doesn't change global
}

changeX_bad();
echo $x;  // Output: still 20
?>
```

### Static Variable Confusion

```php
<?php
// Each function has its own static
function counter1() {
    static $count = 0;
    return ++$count;
}

function counter2() {
    static $count = 0;
    return ++$count;
}

echo counter1() . " ";  // 1
echo counter2() . " ";  // 1
echo counter1() . " ";  // 2
echo counter2() . " ";  // 2
// Output: 1 1 2 2
?>
```

### Variable Scope in Loops

```php
<?php
// Variables in loops are not block-scoped
for ($i = 0; $i < 5; $i++) {
    $result = $i * 2;
}

echo $i . "\n";       // Output: 5 (accessible!)
echo $result . "\n";  // Output: 8 (accessible!)

// PHP doesn't have block scope for variables
// Only function scope
?>
```

## Best Practices

✓ **Minimize global variables** - use function parameters
✓ **Return values** - cleaner than modifying globals
✓ **Use classes** - better encapsulation
✓ **Document scope** - explain where variables are used
✓ **Static for constants** - class configuration
✓ **Superglobals carefully** - validate input
✓ **Session data safely** - verify user session
✓ **Avoid relying on global state** - makes code hard to test
✓ **Use dependency injection** - modern approach
✓ **Test scope boundaries** - ensure proper isolation

## Key Takeaways

✓ **Global scope** - accessible everywhere (with `global` keyword)
✓ **Local scope** - only accessible in function
✓ **Function parameters** - local to function
✓ **Static variables** - retain value between calls
✓ **Global keyword** - declare variable as global inside function
✓ **$GLOBALS array** - access global variables
✓ **Superglobals** - $_GET, $_POST, $_SESSION, etc.
✓ **No block scope** - variables in loops accessible after
✓ **Class scope** - static for class-level properties
✓ **Better design** - avoid global variables when possible
