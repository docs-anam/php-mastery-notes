# Constructor Overriding in PHP

## Table of Contents
1. [Overview](#overview)
2. [Overriding Constructors](#overriding-constructors)
3. [Parent Constructor Calls](#parent-constructor-calls)
4. [Different Parameters](#different-parameters)
5. [Factory Methods](#factory-methods)
6. [Constructor Alternatives](#constructor-alternatives)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

Constructor overriding allows a child class to define its own `__construct` method different from its parent. This is necessary when a child class needs to initialize additional properties or perform different initialization logic. You must explicitly call `parent::__construct()` to initialize parent properties, otherwise they remain uninitialized.

**Key Concepts:**
- Child constructor must call `parent::__construct()`
- Can accept different parameters than parent
- Initializes child-specific properties
- Maintains initialization chain
- Essential for proper object creation

---

## Overriding Constructors

### Simple Override

```php
<?php
class Vehicle {
    protected $brand;
    protected $year;
    
    public function __construct($brand, $year) {
        $this->brand = $brand;
        $this->year = $year;
        echo "Vehicle created: $brand\n";
    }
    
    public function getInfo() {
        return "$this->year $this->brand";
    }
}

class Car extends Vehicle {
    protected $doors;
    
    // Override constructor
    public function __construct($brand, $year, $doors) {
        parent::__construct($brand, $year);  // Call parent
        $this->doors = $doors;
        echo "Car created with $doors doors\n";
    }
    
    public function getInfo() {
        return parent::getInfo() . " ($this->doors doors)";
    }
}

$car = new Car('Toyota', 2023, 4);
echo $car->getInfo();  // 2023 Toyota (4 doors)
?>
```

### Inheritance Chain Constructor

```php
<?php
class Animal {
    protected $name;
    
    public function __construct($name) {
        $this->name = $name;
        echo "Animal: $name\n";
    }
}

class Mammal extends Animal {
    protected $warm_blooded = true;
    
    public function __construct($name) {
        parent::__construct($name);
        echo "Mammal created\n";
    }
}

class Dog extends Mammal {
    protected $breed;
    
    public function __construct($name, $breed) {
        parent::__construct($name);  // Calls Mammal::__construct
        $this->breed = $breed;
        echo "Dog created: $breed\n";
    }
}

$dog = new Dog('Rex', 'Golden Retriever');
// Output:
// Animal: Rex
// Mammal created
// Dog created: Golden Retriever
?>
```

---

## Parent Constructor Calls

### Calling parent::__construct()

```php
<?php
class Person {
    protected $firstName;
    protected $lastName;
    protected $email;
    
    public function __construct($firstName, $lastName, $email) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->onInitialize();  // Hook method
    }
    
    protected function onInitialize() {
        // Override in subclasses
    }
    
    public function getFullName() {
        return "$this->firstName $this->lastName";
    }
}

class Employee extends Person {
    protected $employeeId;
    protected $department;
    
    public function __construct($firstName, $lastName, $email, $employeeId, $department) {
        parent::__construct($firstName, $lastName, $email);
        $this->employeeId = $employeeId;
        $this->department = $department;
    }
    
    protected function onInitialize() {
        // Employee-specific initialization
        echo "Employee initialized\n";
    }
    
    public function getFullInfo() {
        return $this->getFullName() . " - Dept: $this->department";
    }
}

$emp = new Employee('John', 'Doe', 'john@example.com', 'EMP001', 'Engineering');
echo $emp->getFullInfo();
?>
```

### Initialization Order

```php
<?php
class Base {
    protected $initialized = false;
    protected $timestamp;
    
    public function __construct() {
        $this->timestamp = time();
        $this->initialize();
        $this->initialized = true;
    }
    
    protected function initialize() {
        echo "Base initializing\n";
    }
}

class Extended extends Base {
    protected $data = [];
    
    public function __construct() {
        parent::__construct();  // Base initializes first
        // Then Extended initializes
        $this->setupData();
    }
    
    protected function initialize() {
        parent::initialize();  // Not called automatically - must call
        echo "Extended initializing\n";
    }
    
    private function setupData() {
        $this->data = ['initialized_at' => $this->timestamp];
    }
}

$obj = new Extended();
?>
```

---

## Different Parameters

### Child Constructor with Different Signature

```php
<?php
class Database {
    protected $host;
    protected $port;
    protected $database;
    
    public function __construct($host = 'localhost', $port = 3306) {
        $this->host = $host;
        $this->port = $port;
    }
}

class MySQLDatabase extends Database {
    protected $charset = 'utf8mb4';
    protected $username;
    protected $password;
    
    // Different signature - more parameters
    public function __construct($username, $password, $host = 'localhost', $port = 3306) {
        parent::__construct($host, $port);
        $this->username = $username;
        $this->password = $password;
    }
    
    public function getConnection() {
        return "mysql://{$this->username}@{$this->host}:{$this->port}";
    }
}

$db = new MySQLDatabase('root', 'password');
echo $db->getConnection();
?>
```

### Factory Constructor Pattern

```php
<?php
class User {
    protected $id;
    protected $name;
    protected $email;
    
    protected function __construct($id, $name, $email) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }
    
    // Factory methods instead of overloading
    public static function fromArray($data) {
        return new static($data['id'], $data['name'], $data['email']);
    }
    
    public static function fromJSON($json) {
        $data = json_decode($json, true);
        return new static($data['id'], $data['name'], $data['email']);
    }
    
    public function getName() {
        return $this->name;
    }
}

class Admin extends User {
    protected $role = 'admin';
    
    // Can extend factory methods
    public static function create($name, $email) {
        static $id = 0;
        return new static(++$id, $name, $email);
    }
}

$admin = Admin::create('Alice', 'alice@example.com');
echo $admin->getName();
?>
```

---

## Factory Methods

### Multiple Construction Routes

```php
<?php
class DateTime2 {
    private $timestamp;
    
    private function __construct($timestamp) {
        $this->timestamp = $timestamp;
    }
    
    // Factory method for current time
    public static function now() {
        return new static(time());
    }
    
    // Factory method for from string
    public static function fromString($dateString) {
        $timestamp = strtotime($dateString);
        return new static($timestamp);
    }
    
    // Factory method for from components
    public static function from($year, $month, $day) {
        $timestamp = mktime(0, 0, 0, $month, $day, $year);
        return new static($timestamp);
    }
    
    public function format($format = 'Y-m-d') {
        return date($format, $this->timestamp);
    }
}

$date1 = DateTime2::now();
$date2 = DateTime2::fromString('2023-12-25');
$date3 = DateTime2::from(2024, 1, 1);

echo $date1->format() . "\n";
echo $date2->format() . "\n";
echo $date3->format() . "\n";
?>
```

---

## Constructor Alternatives

### Named Constructors

```php
<?php
class Color {
    private $red;
    private $green;
    private $blue;
    
    private function __construct($r, $g, $b) {
        $this->red = $r;
        $this->green = $g;
        $this->blue = $b;
    }
    
    public static function fromRGB($r, $g, $b) {
        return new static($r, $g, $b);
    }
    
    public static function fromHex($hex) {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return new static($r, $g, $b);
    }
    
    public static function fromName($name) {
        $colors = [
            'red' => [255, 0, 0],
            'green' => [0, 255, 0],
            'blue' => [0, 0, 255]
        ];
        
        if (isset($colors[$name])) {
            list($r, $g, $b) = $colors[$name];
            return new static($r, $g, $b);
        }
        
        return null;
    }
    
    public function toHex() {
        return sprintf('#%02x%02x%02x', $this->red, $this->green, $this->blue);
    }
}

$color1 = Color::fromRGB(255, 0, 0);
$color2 = Color::fromHex('#FF0000');
$color3 = Color::fromName('red');

echo $color1->toHex();  // #ff0000
?>
```

---

## Practical Examples

### Authentication System

```php
<?php
class User {
    protected $id;
    protected $email;
    protected $role = 'user';
    
    public function __construct($id, $email) {
        $this->id = $id;
        $this->email = $email;
    }
    
    public function getRole() {
        return $this->role;
    }
}

class AdminUser extends User {
    protected $permissions = [];
    
    public function __construct($id, $email, array $permissions = []) {
        parent::__construct($id, $email);
        $this->role = 'admin';
        $this->permissions = $permissions;
    }
    
    public function hasPermission($permission) {
        return in_array($permission, $this->permissions);
    }
}

class GuestUser extends User {
    protected $sessionId;
    
    public function __construct($sessionId) {
        parent::__construct(0, null);
        $this->sessionId = $sessionId;
        $this->role = 'guest';
    }
}

// Usage
$admin = new AdminUser(1, 'admin@example.com', ['delete_users', 'edit_settings']);
$guest = new GuestUser('session123');

echo $admin->getRole();   // admin
echo $guest->getRole();   // guest
?>
```

### Configuration Builder

```php
<?php
class Config {
    protected $settings = [];
    
    public function __construct(array $defaults = []) {
        $this->settings = array_merge($this->getDefaults(), $defaults);
    }
    
    protected function getDefaults() {
        return [
            'debug' => false,
            'timeout' => 30,
            'cache' => true
        ];
    }
    
    public function get($key) {
        return $this->settings[$key] ?? null;
    }
}

class ProductionConfig extends Config {
    public function __construct(array $overrides = []) {
        parent::__construct($overrides);
        // Force production settings
        $this->settings['debug'] = false;
        $this->settings['cache'] = true;
    }
    
    protected function getDefaults() {
        $defaults = parent::getDefaults();
        $defaults['timeout'] = 60;  // Longer timeout for production
        return $defaults;
    }
}

class DevelopmentConfig extends Config {
    public function __construct(array $overrides = []) {
        parent::__construct($overrides);
        $this->settings['debug'] = true;
    }
}

$prod = new ProductionConfig(['timeout' => 90]);
$dev = new DevelopmentConfig();

echo "Production timeout: " . $prod->get('timeout') . "\n";
echo "Development debug: " . ($dev->get('debug') ? 'yes' : 'no') . "\n";
?>
```

---

## Common Mistakes

### 1. Forgetting parent::__construct()

```php
<?php
// ❌ Wrong: Parent not initialized
class Parent1 {
    protected $id;
    protected $name;
    
    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
}

class Child1 extends Parent1 {
    protected $value;
    
    public function __construct($id, $name, $value) {
        $this->value = $value;
        // Missing parent::__construct($id, $name)
    }
}

$child = new Child1(1, 'test', 100);
// $child->id and $child->name are uninitialized!

// ✓ Correct: Call parent constructor
class Child2 extends Parent1 {
    protected $value;
    
    public function __construct($id, $name, $value) {
        parent::__construct($id, $name);  // Initialize parent
        $this->value = $value;
    }
}
?>
```

### 2. Wrong Parameter Order

```php
<?php
// ❌ Wrong: Parameters in wrong order
class Parent1 {
    protected $first;
    protected $second;
    
    public function __construct($first, $second) {
        $this->first = $first;
        $this->second = $second;
    }
}

class Child1 extends Parent1 {
    public function __construct($second, $first) {
        parent::__construct($first, $second);  // Wrong order!
    }
}

// ✓ Correct: Maintain logical order
class Child2 extends Parent1 {
    public function __construct($first, $second, $third) {
        parent::__construct($first, $second);
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// E-Commerce Product Hierarchy

abstract class Product {
    protected $id;
    protected $name;
    protected $price;
    protected $taxRate = 0.08;
    
    public function __construct($id, $name, $price) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function getPriceWithTax() {
        return $this->price * (1 + $this->taxRate);
    }
    
    abstract public function getType();
}

class PhysicalProduct extends Product {
    protected $weight;
    protected $shippingCost;
    
    public function __construct($id, $name, $price, $weight) {
        parent::__construct($id, $name, $price);
        $this->weight = $weight;
        $this->calculateShipping();
    }
    
    private function calculateShipping() {
        $this->shippingCost = $this->weight * 0.5;
    }
    
    public function getType() {
        return 'Physical Product';
    }
    
    public function getTotalPrice() {
        return $this->getPriceWithTax() + $this->shippingCost;
    }
}

class DigitalProduct extends Product {
    protected $fileSize;
    protected $downloadLimit;
    
    public function __construct($id, $name, $price, $fileSize, $downloadLimit = 10) {
        parent::__construct($id, $name, $price);
        $this->fileSize = $fileSize;
        $this->downloadLimit = $downloadLimit;
    }
    
    public function getType() {
        return 'Digital Product';
    }
    
    public function getTotalPrice() {
        return $this->getPriceWithTax();  // No shipping for digital
    }
}

// Usage
$laptop = new PhysicalProduct(1, 'Laptop', 999.99, 2.5);
echo "Physical: " . $laptop->getTotalPrice() . "\n";

$eBook = new DigitalProduct(2, 'E-Book', 9.99, 5.2);
echo "Digital: " . $eBook->getTotalPrice() . "\n";
?>
```

---

## Cross-References

- **Related Topic: [Constructor](9-constructor.md)** - Constructor basics
- **Related Topic: [Method Overriding](15-function-overriding.md)** - Overriding methods
- **Related Topic: [Parent Keyword](16-parent-keyword.md)** - Calling parent methods
- **Related Topic: [Inheritance Basics](11-inheritance.md)** - Understanding inheritance
