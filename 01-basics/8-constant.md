# Constants

## Table of Contents
1. [Overview](#overview)
2. [Defining Constants](#defining-constants)
3. [Using Constants](#using-constants)
4. [Global Constants](#global-constants)
5. [Class Constants](#class-constants)
6. [Magic Constants](#magic-constants)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)

---

## Overview

Constants are identifiers for values that cannot be changed. Unlike variables, constants:
- Don't have a `$` prefix
- Can only be defined once
- Are globally scoped
- Have no scope restrictions

---

## Defining Constants

### Using define()

```php
<?php
// Basic definition
define("SITE_NAME", "My Website");
define("DB_HOST", "localhost");
define("MAX_USERS", 100);
define("PI", 3.14159);

// Can't redefine
define("SITE_NAME", "New Name");  // Fatal error!

// Can only be set with scalar values
define("NUMBERS", [1, 2, 3]);  // PHP 7.0+
?>
```

### Using const (PHP 5.3+)

```php
<?php
// Class level
class Database {
    const HOST = "localhost";
    const PORT = 3306;
    const CHARSET = "utf8mb4";
}

// Global level (PHP 5.6+)
const APP_VERSION = "1.0.0";
const AUTHOR = "John Doe";

// Access
echo Database::HOST;   // localhost
echo APP_VERSION;      // 1.0.0
?>
```

### define() vs const

```php
<?php
// const: resolved at compile time
const COMPILE_TIME = 123;

// define(): resolved at runtime
define("RUNTIME", time());

// const: Can be conditional in classes
class Config {
    const ENV = "production";  // OK
}

// define: Can be conditional
if ($is_development) {
    define("DEBUG", true);
} else {
    define("DEBUG", false);
}

// const: only scalar or array (PHP 7.0+)
const SCALAR = [1, 2, 3];  // OK

// define: needs PHP 7.0+
define("ARRAY", [1, 2, 3]);  // PHP 7.0+
?>
```

---

## Using Constants

### Basic Usage

```php
<?php
define("MAX_LOGIN_ATTEMPTS", 5);
define("SESSION_TIMEOUT", 3600);

// Use directly
$attempts = 0;
while ($attempts < MAX_LOGIN_ATTEMPTS) {
    $attempts++;
}

// In strings (double-quoted with curly braces)
echo "Max attempts: {MAX_LOGIN_ATTEMPTS}";  // Works
echo "Max attempts: ".MAX_LOGIN_ATTEMPTS;   // Also works
?>
```

### Checking if Defined

```php
<?php
// Check if constant exists
if (defined("API_KEY")) {
    $key = API_KEY;
} else {
    echo "API_KEY not defined";
}

// List all defined constants
$constants = get_defined_constants();
print_r($constants);
?>
```

### Case Sensitivity

```php
<?php
define("NAME", "John", false);  // Case-sensitive (default)
echo NAME;    // John

// Case-insensitive (not recommended)
define("AGE", 25, true);
echo age;     // 25 (also works)
echo AGE;     // 25 (also works)

// Better: always use consistent case
define("PRODUCTION", true);
echo PRODUCTION;  // true
?>
```

---

## Global Constants

### Built-in PHP Constants

```php
<?php
// Directory and file
echo __FILE__;       // Full path to current file
echo __DIR__;        // Directory of current file
echo __LINE__;       // Current line number
echo __FUNCTION__;   // Current function name
echo __CLASS__;      // Current class name
echo __METHOD__;     // Current method name
echo __NAMESPACE__; // Current namespace

// PHP version
echo PHP_VERSION;         // 8.1.0
echo PHP_OS;              // LINUX
echo PHP_OS_FAMILY;       // Linux

// Boolean and NULL
echo true;            // 1
echo false;           // (empty)
echo null;            // (empty)

// Integer limits
echo PHP_INT_MAX;     // 9223372036854775807
echo PHP_INT_MIN;     // -9223372036854775808
?>
```

### Pre-defined Constants

```php
<?php
// TRUE, FALSE, NULL are predefined
echo TRUE;    // 1
echo FALSE;   // (empty)
echo NULL;    // (empty)

// M_PI, M_E (math constants)
echo M_PI;    // 3.1415926535898

// HTTP response codes
echo HTTP_OK;           // 200 (if defined)

// File operations
echo __FILE__;          // Current file path
echo __DIR__;           // Current directory
?>
```

---

## Class Constants

### Defining Class Constants

```php
<?php
class User {
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';
    const ROLE_GUEST = 'guest';
    
    const MAX_USERNAME_LENGTH = 50;
    const MIN_PASSWORD_LENGTH = 8;
    
    private string $role;
    
    public function __construct(string $role) {
        if (!in_array($role, [self::ROLE_ADMIN, self::ROLE_USER, self::ROLE_GUEST])) {
            throw new InvalidArgumentException("Invalid role: $role");
        }
        $this->role = $role;
    }
}

// Access class constants
echo User::ROLE_ADMIN;  // admin
echo User::MAX_USERNAME_LENGTH;  // 50
?>
```

### Visibility Modifiers (PHP 7.1+)

```php
<?php
class Database {
    public const HOST = 'localhost';      // Accessible everywhere
    protected const PASSWORD = 'secret';  // Accessible in class and subclasses
    private const API_KEY = 'key123';     // Only in this class
    
    public function connect() {
        echo self::HOST;      // Can access private
        echo self::PASSWORD;  // Can access protected
        echo self::API_KEY;   // Can access private
    }
}

// From outside
echo Database::HOST;       // OK (public)
// echo Database::PASSWORD; // Error (protected)
// echo Database::API_KEY;  // Error (private)
?>
```

---

## Magic Constants

Special constants that change based on context.

### File and Line Information

```php
<?php
// __FILE__: Full path of the file
echo __FILE__;  // /var/www/html/index.php

// __DIR__: Directory containing the file
echo __DIR__;   // /var/www/html

// __LINE__: Current line number
function showLine() {
    echo __LINE__;  // 8
}

// Example usage
class Logger {
    public function log($message) {
        echo "[{$GLOBALS['HTTP_HOST']}] [{__FILE__}:{__LINE__}] $message";
    }
}
?>
```

### Function and Class Information

```php
<?php
// __FUNCTION__: Current function name
function myFunction() {
    echo __FUNCTION__;  // myFunction
}

// __CLASS__: Current class name
class MyClass {
    public function test() {
        echo __CLASS__;  // MyClass
    }
}

// __METHOD__: Class and method name
class Demo {
    public function method() {
        echo __METHOD__;  // Demo::method
    }
}

// __NAMESPACE__: Current namespace
namespace MyNamespace;
echo __NAMESPACE__;  // MyNamespace
?>
```

### Debug Usage

```php
<?php
function debugInfo() {
    echo "File: " . __FILE__ . "\n";
    echo "Line: " . __LINE__ . "\n";
    echo "Function: " . __FUNCTION__ . "\n";
    echo "Class: " . __CLASS__ . "\n";
    echo "Method: " . __METHOD__ . "\n";
}

class Debugger {
    public function trace() {
        debugInfo();  // Shows context
    }
}
?>
```

---

## Practical Examples

### Configuration Management

```php
<?php
// config.php
define("APP_NAME", "MyApp");
define("APP_VERSION", "1.0.0");
define("ENVIRONMENT", "production");

define("DB_HOST", "db.example.com");
define("DB_NAME", "myapp_db");
define("DB_USER", "dbuser");
define("DB_PASS", "secure_password");

define("API_URL", "https://api.example.com");
define("API_KEY", "secret_key_123");

// Usage in other files
class Database {
    private string $host = DB_HOST;
    private string $name = DB_NAME;
    
    public function connect() {
        // echo "Connecting to {$this->host}/{$this->name}";
    }
}
?>
```

### Status Codes

```php
<?php
class OrderStatus {
    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const SHIPPED = 'shipped';
    const DELIVERED = 'delivered';
    const CANCELLED = 'cancelled';
    const REFUNDED = 'refunded';
    
    public static function isValid($status) {
        return in_array($status, [
            self::PENDING,
            self::PROCESSING,
            self::SHIPPED,
            self::DELIVERED,
            self::CANCELLED,
            self::REFUNDED,
        ]);
    }
}

// Usage
$order_status = OrderStatus::PENDING;
if (OrderStatus::isValid($order_status)) {
    echo "Valid status";
}
?>
```

### Application Settings

```php
<?php
class AppConfig {
    const TIMEZONE = 'America/New_York';
    const LANGUAGE = 'en';
    const CURRENCY = 'USD';
    const DATE_FORMAT = 'Y-m-d';
    const TIME_FORMAT = 'H:i:s';
    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    
    const MAX_FILE_UPLOAD = 5242880;  // 5MB
    const ALLOWED_EXTENSIONS = ['jpg', 'png', 'pdf'];
    
    public static function getDateTime() {
        return date(self::DATETIME_FORMAT);
    }
}

// Usage
date_default_timezone_set(AppConfig::TIMEZONE);
echo AppConfig::getDateTime();
?>
```

---

## Common Mistakes

### 1. Trying to Redefine Constants

```php
<?php
// ❌ Fatal error
define("NAME", "John");
define("NAME", "Jane");  // Can't redefine

// ✓ OK: Check first
if (!defined("NAME")) {
    define("NAME", "John");
}

// ✓ Use variables instead if you need to change
$name = "John";
$name = "Jane";  // OK
?>
```

### 2. Using $ with Constants

```php
<?php
// ❌ Wrong
define("MESSAGE", "Hello");
echo $MESSAGE;  // Undefined variable

// ✓ Correct
echo MESSAGE;   // Hello
?>
```

### 3. Undefined Constants

```php
<?php
// ❌ PHP treats undefined constants as strings
echo UNDEFINED_CONSTANT;  // Outputs: UNDEFINED_CONSTANT (with warning)

// ✓ Check first
if (defined("UNDEFINED_CONSTANT")) {
    echo UNDEFINED_CONSTANT;
} else {
    echo "Constant not defined";
}
?>
```

### 4. Scope Issues with define()

```php
<?php
// Constants defined in function
function setupConstants() {
    define("CONST_A", "value");  // Global scope!
}

setupConstants();
echo CONST_A;  // Works (global)

// ✗ Don't rely on this - it's confusing
// Better: define at top level
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class PaymentConfig {
    public const PAYMENT_METHOD_CREDIT = 'credit_card';
    public const PAYMENT_METHOD_DEBIT = 'debit_card';
    public const PAYMENT_METHOD_PAYPAL = 'paypal';
    
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';
    
    public const MIN_AMOUNT = 0.01;
    public const MAX_AMOUNT = 999999.99;
    public const TRANSACTION_FEE = 0.029;  // 2.9%
    
    public const VALID_METHODS = [
        self::PAYMENT_METHOD_CREDIT,
        self::PAYMENT_METHOD_DEBIT,
        self::PAYMENT_METHOD_PAYPAL,
    ];
    
    public const VALID_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_COMPLETED,
        self::STATUS_FAILED,
        self::STATUS_REFUNDED,
    ];
}

class Payment {
    private float $amount;
    private string $method;
    private string $status = PaymentConfig::STATUS_PENDING;
    
    public function __construct(float $amount, string $method) {
        if ($amount < PaymentConfig::MIN_AMOUNT || 
            $amount > PaymentConfig::MAX_AMOUNT) {
            throw new InvalidArgumentException("Invalid amount");
        }
        
        if (!in_array($method, PaymentConfig::VALID_METHODS)) {
            throw new InvalidArgumentException("Invalid payment method");
        }
        
        $this->amount = $amount;
        $this->method = $method;
    }
    
    public function process(): array {
        $fee = $this->amount * PaymentConfig::TRANSACTION_FEE;
        $total = $this->amount + $fee;
        
        $this->status = PaymentConfig::STATUS_COMPLETED;
        
        return [
            'amount' => $this->amount,
            'fee' => $fee,
            'total' => $total,
            'method' => $this->method,
            'status' => $this->status
        ];
    }
}

// Usage
$payment = new Payment(100.00, PaymentConfig::PAYMENT_METHOD_CREDIT);
$result = $payment->process();
print_r($result);
?>
```

---

## Next Steps

✅ Understand constants  
→ Learn [variables](6-variable.md)  
→ Study [data types](2-hello-world.md)  
→ Master [classes and objects](../03-oop/)
