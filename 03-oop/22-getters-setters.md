# Getters and Setters in PHP

## Table of Contents
1. [Overview](#overview)
2. [Basic Getters and Setters](#basic-getters-and-setters)
3. [Property Validation](#property-validation)
4. [Fluent Interfaces](#fluent-interfaces)
5. [Magic Methods](#magic-methods)
6. [Advanced Patterns](#advanced-patterns)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

Getters and setters are methods that provide controlled access to private or protected properties. They enable encapsulation by preventing direct property access while allowing validation, transformation, and side effects. Getters retrieve property values, and setters assign values with validation. Modern PHP offers magic methods (`__get`, `__set`) and typed properties for cleaner syntax.

**Key Concepts:**
- Encapsulation through controlled access
- Validation before assignment
- Property transformation
- Side effects management
- Getters return values
- Setters validate and assign
- Fluent interfaces with setters

---

## Basic Getters and Setters

### Simple Getter/Setter Pattern

```php
<?php
class Person {
    private $name;
    private $age;
    private $email;
    
    // Getter for name
    public function getName() {
        return $this->name;
    }
    
    // Setter for name
    public function setName($name) {
        if (is_string($name) && !empty($name)) {
            $this->name = $name;
            return true;
        }
        return false;
    }
    
    // Getter for age
    public function getAge() {
        return $this->age;
    }
    
    // Setter for age with validation
    public function setAge($age) {
        if (is_int($age) && $age > 0 && $age < 150) {
            $this->age = $age;
            return true;
        }
        return false;
    }
    
    // Getter for email
    public function getEmail() {
        return $this->email;
    }
    
    // Setter for email with validation
    public function setEmail($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
            return true;
        }
        return false;
    }
}

$person = new Person();
$person->setName("John Doe");
$person->setAge(30);
$person->setEmail("john@example.com");

echo $person->getName() . "\n";      // John Doe
echo $person->getAge() . "\n";       // 30
echo $person->getEmail() . "\n";     // john@example.com

// Invalid values are rejected
$person->setAge(-5);    // Returns false
echo $person->getAge(); // Still 30
?>
```

### Read-Only Properties

```php
<?php
class Product {
    private $id;
    private $sku;
    private $name;
    private $price;
    
    public function __construct($id, $sku, $name, $price) {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->price = $price;
    }
    
    // Read-only getters (no setters)
    public function getId() {
        return $this->id;
    }
    
    public function getSku() {
        return $this->sku;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    // Can only be set via constructor
    // Id and SKU cannot be changed after creation
}

$product = new Product(1, 'LAPTOP-001', 'MacBook Pro', 1299.99);
echo $product->getId() . "\n";       // 1
echo $product->getName() . "\n";     // MacBook Pro

// Cannot modify - read-only
// $product->setId(2);  // No method exists
?>
```

### Write-Only Properties

```php
<?php
class User {
    private $id;
    private $username;
    private $passwordHash;
    
    public function __construct($id, $username) {
        $this->id = $id;
        $this->username = $username;
        $this->passwordHash = null;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    // Write-only setter for password (no getter)
    public function setPassword($password) {
        if (strlen($password) < 8) {
            throw new Exception("Password too short");
        }
        $this->passwordHash = password_hash($password, PASSWORD_BCRYPT);
    }
    
    // Separate method to check password
    public function verifyPassword($password) {
        return password_verify($password, $this->passwordHash);
    }
    
    // No getPassword() method - password hash never exposed
}

$user = new User(1, "johndoe");
$user->setPassword("SecurePassword123");

echo $user->getUsername() . "\n";        // johndoe
echo $user->verifyPassword("SecurePassword123") ? "Valid\n" : "Invalid\n";  // Valid
?>
```

---

## Property Validation

### Type and Value Validation

```php
<?php
class BankAccount {
    private $accountNumber;
    private $balance;
    private $accountType;
    
    public function __construct($accountNumber, $initialBalance) {
        $this->setAccountNumber($accountNumber);
        $this->setBalance($initialBalance);
        $this->accountType = 'checking';
    }
    
    public function setAccountNumber($number) {
        if (!preg_match('/^\d{10,12}$/', $number)) {
            throw new Exception("Invalid account number format");
        }
        $this->accountNumber = $number;
        return $this;
    }
    
    public function getAccountNumber() {
        // Never expose full number
        $num = $this->accountNumber;
        return substr_replace($num, '****', 0, 6);
    }
    
    public function setBalance($amount) {
        if (!is_numeric($amount) || $amount < 0) {
            throw new Exception("Balance must be positive number");
        }
        $this->balance = round($amount, 2);
        return $this;
    }
    
    public function getBalance() {
        return $this->balance;
    }
    
    public function deposit($amount) {
        if ($amount <= 0) {
            throw new Exception("Deposit must be positive");
        }
        $this->balance += $amount;
        return $this;
    }
    
    public function withdraw($amount) {
        if ($amount <= 0) {
            throw new Exception("Withdrawal must be positive");
        }
        if ($amount > $this->balance) {
            throw new Exception("Insufficient funds");
        }
        $this->balance -= $amount;
        return $this;
    }
}

$account = new BankAccount('12345678901', 1000);
echo $account->getAccountNumber() . "\n";  // ****678901
echo $account->getBalance() . "\n";        // 1000

$account->deposit(500);
echo $account->getBalance() . "\n";        // 1500

$account->withdraw(200);
echo $account->getBalance() . "\n";        // 1300
?>
```

### Cascading Validation

```php
<?php
class Email {
    private $address;
    private $verified = false;
    
    public function setAddress($address) {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        // Reset verification when email changes
        $this->verified = false;
        $this->address = strtolower($address);
        
        return $this;
    }
    
    public function getAddress() {
        return $this->address;
    }
    
    public function verify() {
        // In real app, send verification email
        $this->verified = true;
        return $this;
    }
    
    public function isVerified() {
        return $this->verified;
    }
}

$email = new Email();
$email->setAddress("john@example.com");
echo $email->getAddress() . "\n";        // john@example.com
echo $email->isVerified() ? "Yes" : "No" . "\n";  // No

$email->verify();
echo $email->isVerified() ? "Yes" : "No" . "\n";  // Yes

// Changing email resets verification
$email->setAddress("jane@example.com");
echo $email->isVerified() ? "Yes" : "No" . "\n";  // No
?>
```

---

## Fluent Interfaces

### Method Chaining with Setters

```php
<?php
class QueryBuilder {
    private $select = [];
    private $from = '';
    private $where = [];
    private $orderBy = [];
    private $limit = null;
    
    public function select(...$columns) {
        $this->select = $columns;
        return $this;  // Return $this for chaining
    }
    
    public function from($table) {
        $this->from = $table;
        return $this;
    }
    
    public function where($condition) {
        $this->where[] = $condition;
        return $this;
    }
    
    public function orderBy($column, $direction = 'ASC') {
        $this->orderBy[] = "$column $direction";
        return $this;
    }
    
    public function limit($count) {
        $this->limit = $count;
        return $this;
    }
    
    public function build() {
        $sql = "SELECT " . implode(', ', $this->select);
        $sql .= " FROM {$this->from}";
        
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }
        
        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }
        
        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        return $sql;
    }
}

// Fluent interface - chain methods
$query = (new QueryBuilder())
    ->select('id', 'name', 'email')
    ->from('users')
    ->where('age > 18')
    ->where('status = "active"')
    ->orderBy('name', 'ASC')
    ->limit(10);

echo $query->build() . "\n";
// SELECT id, name, email FROM users WHERE age > 18 AND status = "active" 
// ORDER BY name ASC LIMIT 10
?>
```

### Builder Pattern with Fluent Interface

```php
<?php
class URLBuilder {
    private $scheme = 'https';
    private $host = '';
    private $port = null;
    private $path = '';
    private $query = [];
    private $fragment = '';
    
    public function scheme($scheme) {
        $this->scheme = $scheme;
        return $this;
    }
    
    public function host($host) {
        $this->host = $host;
        return $this;
    }
    
    public function port($port) {
        $this->port = $port;
        return $this;
    }
    
    public function path($path) {
        $this->path = '/' . ltrim($path, '/');
        return $this;
    }
    
    public function query($key, $value) {
        $this->query[$key] = $value;
        return $this;
    }
    
    public function fragment($fragment) {
        $this->fragment = $fragment;
        return $this;
    }
    
    public function build() {
        $url = "{$this->scheme}://{$this->host}";
        
        if ($this->port) {
            $url .= ":{$this->port}";
        }
        
        $url .= $this->path;
        
        if (!empty($this->query)) {
            $url .= '?' . http_build_query($this->query);
        }
        
        if ($this->fragment) {
            $url .= "#{$this->fragment}";
        }
        
        return $url;
    }
}

$url = (new URLBuilder())
    ->host('example.com')
    ->path('api/users')
    ->query('page', 1)
    ->query('limit', 10)
    ->query('sort', 'name')
    ->build();

echo $url . "\n";
// https://example.com/api/users?page=1&limit=10&sort=name
?>
```

---

## Magic Methods

### __get and __set

```php
<?php
class DynamicProperties {
    private $data = [];
    
    // Called when accessing undefined property
    public function __get($name) {
        echo "Getting property: $name\n";
        return $this->data[$name] ?? null;
    }
    
    // Called when setting undefined property
    public function __set($name, $value) {
        echo "Setting property: $name to $value\n";
        $this->data[$name] = $value;
    }
    
    // Called when checking isset() on undefined property
    public function __isset($name) {
        return isset($this->data[$name]);
    }
    
    // Called when unsetting undefined property
    public function __unset($name) {
        unset($this->data[$name]);
    }
}

$obj = new DynamicProperties();
$obj->name = "John";       // Triggers __set
$obj->age = 30;             // Triggers __set
echo $obj->name . "\n";     // Triggers __get
echo isset($obj->age) ? "Age is set\n" : "No age\n";  // Triggers __isset
?>
```

### Typed Magic Getters/Setters

```php
<?php
class StrictObject {
    private $data = [];
    private $allowedFields = ['name', 'email', 'phone'];
    
    public function __set($name, $value) {
        if (!in_array($name, $this->allowedFields)) {
            throw new Exception("Property '$name' not allowed");
        }
        
        // Validate based on property
        if ($name === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email");
        }
        
        $this->data[$name] = $value;
    }
    
    public function __get($name) {
        if (!in_array($name, $this->allowedFields)) {
            throw new Exception("Property '$name' not allowed");
        }
        return $this->data[$name] ?? null;
    }
}

$obj = new StrictObject();
$obj->name = "John";
$obj->email = "john@example.com";

echo $obj->name . "\n";

// This throws exception
// $obj->invalid = "value";
?>
```

---

## Advanced Patterns

### Lazy Loading

```php
<?php
class User {
    private $id;
    private $name;
    private $profile = null;
    private $profileLoaded = false;
    
    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
    
    public function getProfile() {
        // Load only when accessed
        if (!$this->profileLoaded) {
            $this->profile = $this->loadProfile();
            $this->profileLoaded = true;
        }
        return $this->profile;
    }
    
    private function loadProfile() {
        // Expensive database query
        return [
            'bio' => 'User biography',
            'avatar' => 'avatar.jpg',
            'createdAt' => date('Y-m-d')
        ];
    }
    
    public function getName() {
        return $this->name;
    }
}

$user = new User(1, "John");
echo $user->getName() . "\n";      // Doesn't load profile

// Profile only loaded when accessed
$profile = $user->getProfile();     // Loads here
print_r($profile);
?>
```

### Computed Properties

```php
<?php
class Rectangle {
    private $width;
    private $height;
    
    public function __construct($width, $height) {
        $this->width = $width;
        $this->height = $height;
    }
    
    public function setWidth($width) {
        $this->width = $width;
        return $this;
    }
    
    public function getWidth() {
        return $this->width;
    }
    
    public function setHeight($height) {
        $this->height = $height;
        return $this;
    }
    
    public function getHeight() {
        return $this->height;
    }
    
    // Computed property - calculated on demand
    public function getArea() {
        return $this->width * $this->height;
    }
    
    public function getPerimeter() {
        return 2 * ($this->width + $this->height);
    }
}

$rect = new Rectangle(5, 3);
echo $rect->getArea() . "\n";          // 15
echo $rect->getPerimeter() . "\n";     // 16

$rect->setWidth(10);
echo $rect->getArea() . "\n";          // 30 - updated automatically
?>
```

---

## Practical Examples

### Configuration Object

```php
<?php
class Config {
    private $settings = [];
    
    public function set($key, $value) {
        if (is_string($key) && !empty($key)) {
            $this->settings[$key] = $value;
            return $this;
        }
        throw new Exception("Invalid key");
    }
    
    public function get($key, $default = null) {
        return $this->settings[$key] ?? $default;
    }
    
    public function has($key) {
        return isset($this->settings[$key]);
    }
    
    public function getAll() {
        return $this->settings;
    }
}

$config = new Config();
$config
    ->set('database.host', 'localhost')
    ->set('database.port', 5432)
    ->set('app.debug', true)
    ->set('app.timezone', 'UTC');

echo $config->get('database.host') . "\n";     // localhost
echo $config->get('missing', 'default') . "\n"; // default
?>
```

---

## Common Mistakes

### 1. Over-Encapsulation

```php
<?php
// ❌ Wrong: Unnecessary encapsulation
class Point {
    private $x;
    private $y;
    
    public function getX() { return $this->x; }
    public function setX($x) { $this->x = $x; return $this; }
    public function getY() { return $this->y; }
    public function setY($y) { $this->y = $y; return $this; }
}

// ✓ Correct: Public properties for simple data
class Point {
    public $x;
    public $y;
}

// Or use typed properties without magic
class Point {
    public function __construct(public int $x, public int $y) {}
}
?>
```

### 2. Validation Logic Not in Setter

```php
<?php
// ❌ Wrong: No validation in setter
class Age {
    private $value;
    
    public function setValue($value) {
        $this->value = $value;  // No validation!
    }
    
    public function getValue() {
        return $this->value;
    }
}

// ✓ Correct: Validate in setter
class Age {
    private $value;
    
    public function setValue($value) {
        if (is_int($value) && $value > 0 && $value < 150) {
            $this->value = $value;
            return true;
        }
        return false;
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// User Account Management

class UserAccount {
    private $id;
    private $username;
    private $email;
    private $passwordHash;
    private $firstName;
    private $lastName;
    private $createdAt;
    private $isActive;
    
    public function __construct($id, $username, $email) {
        $this->id = $id;
        $this->setUsername($username);
        $this->setEmail($email);
        $this->createdAt = new DateTime();
        $this->isActive = true;
    }
    
    // Username getter/setter
    public function getUsername() {
        return $this->username;
    }
    
    public function setUsername($username) {
        if (strlen($username) < 3 || strlen($username) > 20) {
            throw new Exception("Username must be 3-20 characters");
        }
        $this->username = $username;
        return $this;
    }
    
    // Email getter/setter
    public function getEmail() {
        return $this->email;
    }
    
    public function setEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        $this->email = strtolower($email);
        return $this;
    }
    
    // Password setter (no getter!)
    public function setPassword($password) {
        if (strlen($password) < 8) {
            throw new Exception("Password must be 8+ characters");
        }
        $this->passwordHash = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }
    
    public function verifyPassword($password) {
        return password_verify($password, $this->passwordHash);
    }
    
    // Name getters/setters
    public function getFullName() {
        return trim("{$this->firstName} {$this->lastName}");
    }
    
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
        return $this;
    }
    
    public function setLastName($lastName) {
        $this->lastName = $lastName;
        return $this;
    }
    
    // Active status
    public function isActive() {
        return $this->isActive;
    }
    
    public function activate() {
        $this->isActive = true;
        return $this;
    }
    
    public function deactivate() {
        $this->isActive = false;
        return $this;
    }
    
    // Info method
    public function getInfo() {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'fullName' => $this->getFullName(),
            'active' => $this->isActive,
            'created' => $this->createdAt->format('Y-m-d')
        ];
    }
}

// Usage
$user = new UserAccount(1, 'johndoe', 'john@example.com');
$user
    ->setPassword('SecurePass123')
    ->setFirstName('John')
    ->setLastName('Doe');

echo $user->getFullName() . "\n";  // John Doe
echo $user->getEmail() . "\n";     // john@example.com

if ($user->verifyPassword('SecurePass123')) {
    echo "Password is correct\n";
}

print_r($user->getInfo());
?>
```

---

## Cross-References

- **Related Topic: [Properties](4-properties.md)** - Class properties
- **Related Topic: [Magic Methods](34-magic-methods.md)** - __get, __set, __call
- **Related Topic: [Visibility](14-visibility.md)** - Private/protected access
- **Related Topic: [Method Overloading](35-overloading.md)** - Dynamic method behavior
