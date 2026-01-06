# Constants in PHP Classes

## Table of Contents
1. [Overview](#overview)
2. [Defining Class Constants](#defining-class-constants)
3. [Accessing Constants](#accessing-constants)
4. [Constants vs Properties](#constants-vs-properties)
5. [Constant Types](#constant-types)
6. [Visibility of Constants](#visibility-of-constants)
7. [Magic Constants](#magic-constants)
8. [Practical Examples](#practical-examples)
9. [Common Mistakes](#common-mistakes)
10. [Complete Working Example](#complete-working-example)
11. [Cross-References](#cross-references)

---

## Overview

Class constants are identifiers for values that cannot be changed once defined. They're useful for storing fixed values like configuration settings, status codes, or mathematical constants. Constants are defined at class level and can be accessed without creating an object instance.

**Key Concepts:**
- Constants are immutable values
- Defined with the `const` keyword
- Accessible via `ClassName::CONSTANT`
- No dollar sign prefix
- By convention, written in UPPERCASE
- Cannot be redefined or undefined
- Available at class load time

---

## Defining Class Constants

### Basic Constant Definition

```php
<?php
class Configuration {
    const APP_NAME = 'MyApplication';
    const APP_VERSION = '1.0.0';
    const MAX_USERS = 100;
    const DEFAULT_TIMEOUT = 30;
    
    public function displayInfo() {
        echo "App: " . self::APP_NAME . " v" . self::APP_VERSION;
    }
}

echo Configuration::APP_NAME;        // MyApplication
echo Configuration::APP_VERSION;     // 1.0.0
echo Configuration::MAX_USERS;       // 100
?>
```

### Types of Constant Values

```php
<?php
class DatabaseConfig {
    // String constant
    const HOST = 'localhost';
    
    // Integer constant
    const PORT = 3306;
    
    // Float constant
    const TIMEOUT = 5.5;
    
    // Boolean constant
    const DEBUG = true;
    
    // Array constant (PHP 5.6+)
    const ALLOWED_HOSTS = ['localhost', '127.0.0.1', '192.168.1.1'];
    
    // Null constant
    const DEFAULT_VALUE = null;
}

echo DatabaseConfig::HOST;           // localhost
echo DatabaseConfig::PORT;           // 3306
print_r(DatabaseConfig::ALLOWED_HOSTS);
?>
```

### Typed Constants (PHP 8.3+)

```php
<?php
class TypedConfig {
    // Typed constants require explicit type declaration
    const string APP_ENV = 'production';
    const int MAX_CONNECTIONS = 100;
    const float PI = 3.14159;
    const bool CACHE_ENABLED = true;
    const array ALLOWED_METHODS = ['GET', 'POST'];
}

echo TypedConfig::APP_ENV;           // production
echo TypedConfig::MAX_CONNECTIONS;   // 100
?>
```

---

## Accessing Constants

### Accessing from Outside the Class

```php
<?php
class HttpStatus {
    const OK = 200;
    const CREATED = 201;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const SERVER_ERROR = 500;
}

echo HttpStatus::OK;                 // 200
echo HttpStatus::NOT_FOUND;          // 404

// Using in conditionals
if ($statusCode === HttpStatus::OK) {
    echo "Request successful";
}
?>
```

### Accessing from Within the Class

```php
<?php
class Logger {
    const LOG_ERROR = 'ERROR';
    const LOG_WARNING = 'WARNING';
    const LOG_INFO = 'INFO';
    const LOG_DEBUG = 'DEBUG';
    
    private $level = self::LOG_INFO;
    
    public function setLevel($level) {
        $this->level = $level;
    }
    
    public function log($message) {
        echo "[{$this->level}] $message\n";
    }
    
    public function getAvailableLevels() {
        return [
            self::LOG_ERROR,
            self::LOG_WARNING,
            self::LOG_INFO,
            self::LOG_DEBUG
        ];
    }
}

$logger = new Logger();
$logger->log('Application started');
print_r($logger->getAvailableLevels());
?>
```

### Using constant() Function

```php
<?php
class Database {
    const TYPE_MYSQL = 'mysql';
    const TYPE_POSTGRES = 'postgres';
    const TYPE_SQLITE = 'sqlite';
}

// Dynamic constant access with constant()
$dbType = 'TYPE_MYSQL';
$fullConstant = 'Database::' . $dbType;

echo constant($fullConstant);        // mysql

// Useful when the constant name is dynamic
$types = ['TYPE_MYSQL', 'TYPE_POSTGRES'];
foreach ($types as $type) {
    echo constant('Database::' . $type) . "\n";
}
?>
```

---

## Constants vs Properties

### Key Differences

```php
<?php
class Configuration {
    // Constant - immutable, shared across instances
    const VERSION = '1.0.0';
    const MAX_SIZE = 1024;
    
    // Property - mutable, unique per instance
    public $apiKey;
    public $username;
    
    public function __construct($apiKey, $username) {
        $this->apiKey = $apiKey;
        $this->username = $username;
    }
    
    public function showInfo() {
        echo "Version: " . self::VERSION . "\n";      // Constant
        echo "Max Size: " . self::MAX_SIZE . "\n";    // Constant
        echo "API Key: " . $this->apiKey . "\n";      // Property
        echo "Username: " . $this->username . "\n";   // Property
    }
}

$config1 = new Configuration('key123', 'alice');
$config2 = new Configuration('key456', 'bob');

$config1->showInfo();
// Version: 1.0.0 (same for all)
// Max Size: 1024 (same for all)
// API Key: key123 (different per instance)
// Username: alice (different per instance)

$config2->showInfo();
// Version: 1.0.0 (same for all)
// Max Size: 1024 (same for all)
// API Key: key456 (different per instance)
// Username: bob (different per instance)
?>
```

### When to Use Constants vs Properties

```php
<?php
// Use constants for:
// - Shared, unchanging values
// - Configuration defaults
// - Status codes, enumerations
// - Mathematical constants

class OrderStatus {
    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const SHIPPED = 'shipped';
    const DELIVERED = 'delivered';
    const CANCELLED = 'cancelled';
}

// Use properties for:
// - Data that varies per instance
// - Information loaded from database
// - State that changes over time

class Order {
    public $id;
    public $status = OrderStatus::PENDING;
    public $createdDate;
    public $totalAmount;
    
    public function __construct($id) {
        $this->id = $id;
        $this->createdDate = date('Y-m-d H:i:s');
    }
}
?>
```

---

## Constant Types

### Enumeration Constants

```php
<?php
class PaymentMethod {
    const CREDIT_CARD = 'credit_card';
    const DEBIT_CARD = 'debit_card';
    const PAYPAL = 'paypal';
    const BANK_TRANSFER = 'bank_transfer';
    const CRYPTOCURRENCY = 'crypto';
    
    public static function isValid($method) {
        return in_array($method, [
            self::CREDIT_CARD,
            self::DEBIT_CARD,
            self::PAYPAL,
            self::BANK_TRANSFER,
            self::CRYPTOCURRENCY
        ]);
    }
    
    public static function getAll() {
        return [
            self::CREDIT_CARD,
            self::DEBIT_CARD,
            self::PAYPAL,
            self::BANK_TRANSFER,
            self::CRYPTOCURRENCY
        ];
    }
}

echo PaymentMethod::isValid('paypal');      // true
echo PaymentMethod::isValid('invalid');     // false
print_r(PaymentMethod::getAll());
?>
```

### Configuration Constants

```php
<?php
class EmailConfig {
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = 587;
    const SENDER_NAME = 'No Reply';
    const SENDER_EMAIL = 'noreply@example.com';
    const RETRIES = 3;
    const TIMEOUT = 30;
    const MAX_ATTACHMENTS = 5;
    const MAX_SIZE_MB = 25;
}

class Mailer {
    public function send($to, $subject, $body) {
        echo "SMTP Config: " . EmailConfig::SMTP_HOST . ":" . EmailConfig::SMTP_PORT . "\n";
        echo "From: " . EmailConfig::SENDER_EMAIL . "\n";
        echo "To: $to\n";
        echo "Subject: $subject\n";
        echo "Body: $body\n";
    }
}

$mailer = new Mailer();
$mailer->send('user@example.com', 'Hello', 'Welcome!');
?>
```

### Mathematical and Physical Constants

```php
<?php
class Math {
    const PI = 3.14159265359;
    const E = 2.71828182846;
    const GOLDEN_RATIO = 1.61803398875;
    const TAU = 6.28318530718;
    
    public static function circleArea($radius) {
        return self::PI * $radius * $radius;
    }
    
    public static function circleCircumference($radius) {
        return self::TAU * $radius;
    }
}

echo "Circle area (r=5): " . Math::circleArea(5) . "\n";
echo "Circle circumference (r=5): " . Math::circleCircumference(5) . "\n";
?>
```

---

## Visibility of Constants

### Public, Protected, Private Constants (PHP 7.1+)

```php
<?php
class AccessControl {
    // Public constant - accessible anywhere (default)
    public const PUBLIC_CONST = 'public value';
    
    // Protected constant - accessible in class and subclasses
    protected const PROTECTED_CONST = 'protected value';
    
    // Private constant - accessible only in this class
    private const PRIVATE_CONST = 'private value';
    
    public function showAll() {
        echo self::PUBLIC_CONST . "\n";
        echo self::PROTECTED_CONST . "\n";
        echo self::PRIVATE_CONST . "\n";
    }
}

class ChildClass extends AccessControl {
    public function showFromChild() {
        echo self::PUBLIC_CONST . "\n";        // OK - public
        echo self::PROTECTED_CONST . "\n";     // OK - protected
        // echo self::PRIVATE_CONST;            // Error - private
    }
}

echo AccessControl::PUBLIC_CONST;              // OK - public
// echo AccessControl::PROTECTED_CONST;        // Error - protected
// echo AccessControl::PRIVATE_CONST;          // Error - private

$obj = new AccessControl();
$obj->showAll();

$child = new ChildClass();
$child->showFromChild();
?>
```

---

## Magic Constants

### __CLASS__, __NAMESPACE__, etc.

```php
<?php
namespace MyApp\Utilities;

class MagicConstants {
    const REGULAR_CONST = 'value';
    
    public function showMagic() {
        echo "__CLASS__: " . __CLASS__ . "\n";        // MyApp\Utilities\MagicConstants
        echo "__METHOD__: " . __METHOD__ . "\n";      // MyApp\Utilities\MagicConstants::showMagic
        echo "__FUNCTION__: " . __FUNCTION__ . "\n";  // showMagic
        echo "__NAMESPACE__: " . __NAMESPACE__ . "\n"; // MyApp\Utilities
        echo "__FILE__: " . __FILE__ . "\n";          // file path
        echo "__LINE__: " . __LINE__ . "\n";          // line number
        echo "CONSTANT: " . self::REGULAR_CONST . "\n";
    }
}

$obj = new MagicConstants();
$obj->showMagic();
?>
```

---

## Practical Examples

### Feature Flags Class

```php
<?php
class FeatureFlags {
    private $flags = [];
    
    public function __construct() {
        $this->initializeDefaults();
    }
    
    private function initializeDefaults() {
        $this->flags = [
            'enable_dark_mode' => true,
            'enable_beta_features' => false,
            'enable_analytics' => true,
            'enable_notifications' => true,
            'max_upload_size' => 5242880,  // 5MB
        ];
    }
    
    public function isEnabled($flag) {
        return $this->flags[$flag] ?? false;
    }
    
    public function getValue($key) {
        return $this->flags[$key] ?? null;
    }
    
    public function setFlag($flag, $value) {
        $this->flags[$flag] = $value;
    }
}

$flags = new FeatureFlags();
echo $flags->isEnabled('enable_dark_mode') ? 'Dark mode ON' : 'Dark mode OFF';
?>
```

### Validation Rules Class

```php
<?php
class ValidationRules {
    const MIN_PASSWORD_LENGTH = 8;
    const MAX_PASSWORD_LENGTH = 128;
    const MIN_USERNAME_LENGTH = 3;
    const MAX_USERNAME_LENGTH = 50;
    const MIN_EMAIL_LENGTH = 5;
    const MAX_EMAIL_LENGTH = 254;
    const ALLOWED_IMAGE_TYPES = ['jpg', 'png', 'gif', 'webp'];
    const MAX_FILE_SIZE_MB = 10;
    
    public static function validatePassword($password) {
        $length = strlen($password);
        if ($length < self::MIN_PASSWORD_LENGTH) {
            return "Password must be at least " . self::MIN_PASSWORD_LENGTH . " characters";
        }
        if ($length > self::MAX_PASSWORD_LENGTH) {
            return "Password cannot exceed " . self::MAX_PASSWORD_LENGTH . " characters";
        }
        return true;
    }
    
    public static function validateUsername($username) {
        $length = strlen($username);
        if ($length < self::MIN_USERNAME_LENGTH || $length > self::MAX_USERNAME_LENGTH) {
            return "Username must be between " . self::MIN_USERNAME_LENGTH . 
                   " and " . self::MAX_USERNAME_LENGTH . " characters";
        }
        return true;
    }
    
    public static function isValidImageType($extension) {
        return in_array(strtolower($extension), self::ALLOWED_IMAGE_TYPES);
    }
}

echo ValidationRules::validatePassword('abc');      // Error message
echo ValidationRules::validatePassword('secure123'); // true
?>
```

---

## Common Mistakes

### 1. Using $ Before Constant Name

```php
<?php
// ❌ Wrong: Constants don't have $
class Config {
    const API_KEY = 'secret123';
    
    public function getKey() {
        return $this->API_KEY;  // Error! Constants don't use $
    }
}

// ✓ Correct: No $ for constants
class Config {
    const API_KEY = 'secret123';
    
    public function getKey() {
        return self::API_KEY;   // Correct
    }
}
?>
```

### 2. Trying to Reassign Constants

```php
<?php
// ❌ Wrong: Can't reassign constants
class Settings {
    const VERSION = '1.0.0';
}

Settings::VERSION = '2.0.0';  // Error! Can't reassign

// ✓ Use properties for mutable values
class Settings {
    public $version = '1.0.0';
}

$settings = new Settings();
$settings->version = '2.0.0';  // OK
?>
```

### 3. Confusing self:: with $this->

```php
<?php
// ❌ Wrong: Using $this for constants
class Database {
    const HOST = 'localhost';
    
    public function connect() {
        return $this->HOST;  // Error or unexpected behavior
    }
}

// ✓ Correct: Use self:: for constants
class Database {
    const HOST = 'localhost';
    
    public function connect() {
        return self::HOST;   // Correct
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// E-Commerce Product System with Constants

class Product {
    private $id;
    private $name;
    private $price;
    private $status;
    
    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DISCONTINUED = 'discontinued';
    
    // Category constants
    const CATEGORY_ELECTRONICS = 'electronics';
    const CATEGORY_CLOTHING = 'clothing';
    const CATEGORY_BOOKS = 'books';
    
    // Business rules as constants
    const MIN_PRICE = 0.01;
    const MAX_PRICE = 99999.99;
    const DISCOUNT_PERCENTAGE = 15;
    const TAX_RATE = 0.08;
    
    public function __construct($id, $name, $price) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->status = self::STATUS_ACTIVE;
    }
    
    public function getPriceWithTax() {
        return $this->price * (1 + self::TAX_RATE);
    }
    
    public function getPriceWithDiscount() {
        $discountAmount = $this->price * (self::DISCOUNT_PERCENTAGE / 100);
        return $this->price - $discountAmount;
    }
    
    public function setStatus($status) {
        if ($this->isValidStatus($status)) {
            $this->status = $status;
            return true;
        }
        return false;
    }
    
    private function isValidStatus($status) {
        return in_array($status, [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_DISCONTINUED
        ]);
    }
    
    public static function getAvailableStatuses() {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_DISCONTINUED
        ];
    }
    
    public function getDetails() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'price_with_tax' => $this->getPriceWithTax(),
            'discounted_price' => $this->getPriceWithDiscount(),
            'status' => $this->status
        ];
    }
}

// Usage
$laptop = new Product(1, 'Gaming Laptop', 1299.99);
$laptop->setStatus(Product::STATUS_ACTIVE);

$mouse = new Product(2, 'Wireless Mouse', 49.99);

print_r($laptop->getDetails());
print_r($mouse->getDetails());

echo "Available statuses: " . implode(', ', Product::getAvailableStatuses());
?>
```

---

## Cross-References

- **Related Topic: [Properties](4-properties.md)** - Storing instance data
- **Related Topic: [Self Keyword](8-self-keyword.md)** - Accessing class members including constants
- **Related Topic: [$this Keyword](6-this-keyword.md)** - Instance variable access
- **Related Topic: [Static Keyword](28-static-keyword.md)** - Static properties and constants
- **Related Topic: [Visibility/Access Modifiers](14-visibility.md)** - Controlling constant access
