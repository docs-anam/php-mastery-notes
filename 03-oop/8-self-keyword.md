# The Self Keyword in PHP

## Table of Contents
1. [Overview](#overview)
2. [Accessing Class Constants](#accessing-class-constants)
3. [Accessing Static Properties](#accessing-static-properties)
4. [Accessing Static Methods](#accessing-static-methods)
5. [self:: vs $this](#self-vs-this)
6. [self:: vs static::](#self-vs-static)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

The `self` keyword refers to the current class, not an instance of the class. It's used to access static properties, static methods, and class constants. Unlike `$this`, which requires an object instance, `self::` works at the class level and can be used in static methods.

**Key Concepts:**
- `self::` refers to the class itself
- Used to access constants, static properties, and static methods
- Available in both static and instance methods
- Does not require object instantiation
- Resolved at compile time (early binding)

---

## Accessing Class Constants

### Using self:: with Constants

```php
<?php
class Configuration {
    const APP_NAME = 'MyApp';
    const VERSION = '1.0.0';
    const MAX_USERS = 100;
    
    // Access constant within class
    public function getAppInfo() {
        return self::APP_NAME . ' v' . self::VERSION;
    }
    
    // Static method accessing constant
    public static function getMaxUsers() {
        return self::MAX_USERS;
    }
    
    // Conditional logic using constants
    public function isMaxUsersExceeded($currentUsers) {
        return $currentUsers >= self::MAX_USERS;
    }
}

// Access from outside
echo Configuration::APP_NAME;           // MyApp
echo Configuration::VERSION;            // 1.0.0

// Access via instance
$config = new Configuration();
echo $config->getAppInfo();             // MyApp v1.0.0
echo Configuration::getMaxUsers();      // 100
?>
```

### Constants in Conditional Logic

```php
<?php
class Status {
    const PENDING = 'pending';
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const DELETED = 'deleted';
    
    private $currentStatus = self::ACTIVE;
    
    public function activate() {
        if ($this->currentStatus === self::INACTIVE) {
            $this->currentStatus = self::ACTIVE;
            return true;
        }
        return false;
    }
    
    public function deactivate() {
        if ($this->currentStatus === self::ACTIVE) {
            $this->currentStatus = self::INACTIVE;
            return true;
        }
        return false;
    }
    
    public function isActive() {
        return $this->currentStatus === self::ACTIVE;
    }
}

$status = new Status();
echo $status->isActive() ? 'Active' : 'Inactive';  // Active
?>
```

---

## Accessing Static Properties

### Self with Static Properties

```php
<?php
class Counter {
    private static $count = 0;
    
    public function increment() {
        self::$count++;
    }
    
    public static function getCount() {
        return self::$count;
    }
    
    public static function reset() {
        self::$count = 0;
    }
}

$counter1 = new Counter();
$counter1->increment();
$counter1->increment();

$counter2 = new Counter();
$counter2->increment();

echo Counter::getCount();  // 3 (shared across all instances)
Counter::reset();
echo Counter::getCount();  // 0
?>
```

### Static Arrays and Collections

```php
<?php
class Registry {
    private static $instances = [];
    
    public static function set($key, $value) {
        self::$instances[$key] = $value;
    }
    
    public static function get($key) {
        return self::$instances[$key] ?? null;
    }
    
    public static function has($key) {
        return isset(self::$instances[$key]);
    }
    
    public static function getAll() {
        return self::$instances;
    }
    
    public static function clear() {
        self::$instances = [];
    }
}

Registry::set('database', ['host' => 'localhost', 'port' => 3306]);
Registry::set('cache', ['driver' => 'redis', 'ttl' => 3600]);

echo Registry::get('database')['host'];  // localhost
print_r(Registry::getAll());
?>
```

### Static Configuration

```php
<?php
class DatabaseConnection {
    private static $instance;
    private static $config = [];
    
    public static function configure($host, $port, $database) {
        self::$config = compact('host', 'port', 'database');
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new PDO(
                'mysql:host=' . self::$config['host'] . 
                ';dbname=' . self::$config['database']
            );
        }
        return self::$instance;
    }
}

DatabaseConnection::configure('localhost', 3306, 'mydb');
// $db = DatabaseConnection::getInstance();
?>
```

---

## Accessing Static Methods

### Calling Static Methods with self::

```php
<?php
class Validator {
    public static function isEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function isPhoneNumber($phone) {
        return preg_match('/^\d{10}$/', $phone);
    }
    
    public static function validate($email, $phone) {
        // Call other static methods using self::
        if (!self::isEmail($email)) {
            return "Invalid email";
        }
        
        if (!self::isPhoneNumber($phone)) {
            return "Invalid phone";
        }
        
        return "Valid";
    }
}

echo Validator::validate('user@example.com', '1234567890');  // Valid
echo Validator::validate('invalid', '123');                   // Invalid email
?>
```

### Static Factory Methods

```php
<?php
class User {
    private $id;
    private $name;
    private $email;
    private $role = 'user';
    
    private function __construct($id, $name, $email) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }
    
    // Factory method using self::
    public static function createRegularUser($id, $name, $email) {
        $user = new self($id, $name, $email);
        $user->role = 'user';
        return $user;
    }
    
    // Another factory method
    public static function createAdminUser($id, $name, $email) {
        $user = new self($id, $name, $email);
        $user->role = 'admin';
        return $user;
    }
    
    public function getInfo() {
        return "{$this->name} ({$this->role})";
    }
}

$user1 = User::createRegularUser(1, 'John', 'john@example.com');
$user2 = User::createAdminUser(2, 'Alice', 'alice@example.com');

echo $user1->getInfo();  // John (user)
echo $user2->getInfo();  // Alice (admin)
?>
```

### Recursive Static Methods

```php
<?php
class MathUtils {
    // Factorial using self:: for recursion
    public static function factorial($n) {
        if ($n <= 1) {
            return 1;
        }
        return $n * self::factorial($n - 1);
    }
    
    // Fibonacci using self::
    public static function fibonacci($n) {
        if ($n <= 1) {
            return $n;
        }
        return self::fibonacci($n - 1) + self::fibonacci($n - 2);
    }
    
    // Power function using self::
    public static function power($base, $exponent) {
        if ($exponent === 0) {
            return 1;
        }
        return $base * self::power($base, $exponent - 1);
    }
}

echo MathUtils::factorial(5);       // 120
echo MathUtils::fibonacci(7);       // 13
echo MathUtils::power(2, 8);        // 256
?>
```

---

## self:: vs $this

### Key Differences

```php
<?php
class Example {
    const CONST_VALUE = 'constant';
    public $instanceProp = 'property';
    private static $staticProp = 'static';
    
    public function instanceMethod() {
        echo self::CONST_VALUE;          // ✓ OK - access constant
        echo self::$staticProp;          // ✓ OK - access static property
        // echo self::$instanceProp;     // ✗ Error - can't access instance via self::
        
        echo $this->instanceProp;        // ✓ OK - access instance property
        // echo $this->CONST_VALUE;      // ✗ Error - weird behavior
    }
}
?>
```

### Using Both Together

```php
<?php
class UserSession {
    const SESSION_TIMEOUT = 3600;  // Class constant
    private static $activeUsers = [];  // Static property
    private $userId;  // Instance property
    private $loginTime;  // Instance property
    
    public function __construct($userId) {
        $this->userId = $userId;
        $this->loginTime = time();
        self::$activeUsers[$userId] = $this->loginTime;
    }
    
    public function isExpired() {
        return (time() - $this->loginTime) > self::SESSION_TIMEOUT;
    }
    
    public static function getActiveCount() {
        return count(self::$activeUsers);
    }
    
    public function getUserInfo() {
        return [
            'id' => $this->userId,
            'login_time' => $this->loginTime,
            'timeout' => self::SESSION_TIMEOUT,
            'active_users' => self::getActiveCount()
        ];
    }
}

$session = new UserSession(123);
echo $session->getUserInfo()['timeout'];  // 3600
?>
```

---

## self:: vs static::

### Early Binding vs Late Binding

```php
<?php
class Parent1 {
    public static function who() {
        return 'Parent';
    }
    
    public static function testSelf() {
        return self::who();     // Early binding - always Parent
    }
    
    public static function testStatic() {
        return static::who();   // Late binding - respects child override
    }
}

class Child1 extends Parent1 {
    public static function who() {
        return 'Child';
    }
}

echo Parent1::testSelf();      // Parent
echo Parent1::testStatic();    // Parent
echo Child1::testSelf();       // Parent (still early bound)
echo Child1::testStatic();     // Child (late bound - respects override)
?>
```

### When to Use Which

```php
<?php
class BaseLogger {
    const LOG_LEVEL = 'INFO';
    
    public static function log($message) {
        echo "[" . self::LOG_LEVEL . "] $message\n";
    }
    
    public static function logUsingStatic($message) {
        echo "[" . static::LOG_LEVEL . "] $message\n";
    }
}

class ErrorLogger extends BaseLogger {
    const LOG_LEVEL = 'ERROR';
}

BaseLogger::log('Test');              // [INFO] Test
BaseLogger::logUsingStatic('Test');   // [INFO] Test
ErrorLogger::log('Error');            // [INFO] Error (self:: not overridden)
ErrorLogger::logUsingStatic('Error'); // [ERROR] Error (static:: respects override)
?>
```

---

## Practical Examples

### Singleton Pattern

```php
<?php
class Database {
    private static $instance;
    private $connection;
    
    private function __construct() {
        $this->connection = new PDO('sqlite::memory:');
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function query($sql) {
        return $this->connection->query($sql);
    }
}

$db1 = Database::getInstance();
$db2 = Database::getInstance();
var_dump($db1 === $db2);  // true - same instance
?>
```

### Factory Class

```php
<?php
class Transport {
    public static function create($type) {
        return match($type) {
            'car' => self::createCar(),
            'bike' => self::createBike(),
            'bus' => self::createBus(),
            default => null
        };
    }
    
    private static function createCar() {
        return new Car();
    }
    
    private static function createBike() {
        return new Bike();
    }
    
    private static function createBus() {
        return new Bus();
    }
}

class Car {}
class Bike {}
class Bus {}

$car = Transport::create('car');
var_dump($car instanceof Car);  // true
?>
```

### Event Dispatcher

```php
<?php
class EventDispatcher {
    private static $listeners = [];
    
    public static function on($event, $callback) {
        self::$listeners[$event][] = $callback;
    }
    
    public static function dispatch($event, ...$args) {
        if (!isset(self::$listeners[$event])) {
            return;
        }
        
        foreach (self::$listeners[$event] as $callback) {
            call_user_func_array($callback, $args);
        }
    }
    
    public static function remove($event) {
        unset(self::$listeners[$event]);
    }
}

EventDispatcher::on('user_login', function($username) {
    echo "User $username logged in\n";
});

EventDispatcher::on('user_logout', function($username) {
    echo "User $username logged out\n";
});

EventDispatcher::dispatch('user_login', 'alice');
EventDispatcher::dispatch('user_logout', 'alice');
?>
```

---

## Common Mistakes

### 1. Using $this for Static Members

```php
<?php
// ❌ Wrong: Using $this for static
class Counter {
    private static $count = 0;
    
    public function increment() {
        $this->count++;  // Wrong! This modifies instance property
    }
}

// ✓ Correct: Use self:: for static
class Counter {
    private static $count = 0;
    
    public function increment() {
        self::$count++;  // Correct
    }
}
?>
```

### 2. Using self:: for Instance Properties

```php
<?php
// ❌ Wrong: Using self:: for instance property
class User {
    private $name;
    
    public function setName($name) {
        self::$name = $name;  // Error! Can't access instance via self::
    }
}

// ✓ Correct: Use $this for instance
class User {
    private $name;
    
    public function setName($name) {
        $this->name = $name;  // Correct
    }
}
?>
```

### 3. Forgetting self:: in Constructor

```php
<?php
// ❌ Wrong: Forgetting self:: for constant
class Product {
    const DEFAULT_STATUS = 'active';
    private $status;
    
    public function __construct() {
        $this->status = DEFAULT_STATUS;  // Error! Undefined constant
    }
}

// ✓ Correct: Use self:: for constant
class Product {
    const DEFAULT_STATUS = 'active';
    private $status;
    
    public function __construct() {
        $this->status = self::DEFAULT_STATUS;  // Correct
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// Logging System with self::

class LogManager {
    const LOG_DEBUG = 'DEBUG';
    const LOG_INFO = 'INFO';
    const LOG_WARNING = 'WARNING';
    const LOG_ERROR = 'ERROR';
    
    private static $logs = [];
    private static $maxLogs = 1000;
    private static $minLevel = self::LOG_INFO;
    
    public static function setMinLevel($level) {
        if (self::isValidLevel($level)) {
            self::$minLevel = $level;
        }
    }
    
    private static function isValidLevel($level) {
        return in_array($level, [
            self::LOG_DEBUG,
            self::LOG_INFO,
            self::LOG_WARNING,
            self::LOG_ERROR
        ]);
    }
    
    public static function debug($message) {
        self::logMessage(self::LOG_DEBUG, $message);
    }
    
    public static function info($message) {
        self::logMessage(self::LOG_INFO, $message);
    }
    
    public static function warning($message) {
        self::logMessage(self::LOG_WARNING, $message);
    }
    
    public static function error($message) {
        self::logMessage(self::LOG_ERROR, $message);
    }
    
    private static function logMessage($level, $message) {
        if (self::shouldLog($level)) {
            $entry = [
                'level' => $level,
                'message' => $message,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            self::$logs[] = $entry;
            
            if (count(self::$logs) > self::$maxLogs) {
                array_shift(self::$logs);
            }
        }
    }
    
    private static function shouldLog($level) {
        $levels = [
            self::LOG_DEBUG => 0,
            self::LOG_INFO => 1,
            self::LOG_WARNING => 2,
            self::LOG_ERROR => 3
        ];
        
        return $levels[$level] >= $levels[self::$minLevel];
    }
    
    public static function getLogs() {
        return self::$logs;
    }
    
    public static function clearLogs() {
        self::$logs = [];
    }
}

// Usage
LogManager::setMinLevel(LogManager::LOG_DEBUG);
LogManager::debug('Application started');
LogManager::info('Processing request');
LogManager::warning('Cache miss');
LogManager::error('Database connection failed');

foreach (LogManager::getLogs() as $log) {
    echo "[{$log['level']}] {$log['message']} - {$log['timestamp']}\n";
}
?>
```

---

## Cross-References

- **Related Topic: [$this Keyword](6-this-keyword.md)** - Instance access
- **Related Topic: [Constants](7-constant.md)** - Class constants
- **Related Topic: [Static Keyword](28-static-keyword.md)** - Static properties and methods
- **Related Topic: [Parent Keyword](16-parent-keyword.md)** - Parent class access
- **Related Topic: [Inheritance Basics](11-inheritance.md)** - Understanding inheritance and self::
