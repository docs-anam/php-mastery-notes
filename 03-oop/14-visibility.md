# Visibility and Access Modifiers in PHP

## Table of Contents
1. [Overview](#overview)
2. [Public Visibility](#public-visibility)
3. [Protected Visibility](#protected-visibility)
4. [Private Visibility](#private-visibility)
5. [Visibility in Inheritance](#visibility-in-inheritance)
6. [Best Practices](#best-practices)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

Visibility modifiers (access modifiers) control where properties and methods can be accessed. There are three levels: public (accessible anywhere), protected (accessible in the class and subclasses), and private (accessible only in the class). These modifiers are essential for encapsulation and maintaining the integrity of your code.

**Key Concepts:**
- `public` - accessible everywhere
- `protected` - accessible in class and subclasses
- `private` - accessible only in the class
- Visibility applies to properties and methods
- Helps maintain encapsulation
- Changes visibility can break subclass contracts

---

## Public Visibility

### Public Properties

```php
<?php
class Car {
    public $brand;      // Anyone can access and modify
    public $color;
    public $year;
    
    public function __construct($brand, $color, $year) {
        $this->brand = $brand;
        $this->color = $color;
        $this->year = $year;
    }
}

$car = new Car('Toyota', 'Blue', 2023);
echo $car->brand;           // OK - accessible
$car->color = 'Red';        // OK - can modify
?>
```

### Public Methods

```php
<?php
class Calculator {
    public function add($a, $b) {
        return $a + $b;
    }
    
    public function multiply($a, $b) {
        return $a * $b;
    }
}

$calc = new Calculator();
echo $calc->add(5, 3);          // OK - public method
echo $calc->multiply(4, 2);     // OK - public method
?>
```

### When to Use Public

```php
<?php
class APIEndpoint {
    // Public for external interface
    public function getUser($id) {
        return $this->fetchFromDatabase($id);
    }
    
    public function createUser($data) {
        return $this->save($data);
    }
}

// External code can call public methods
$api = new APIEndpoint();
$user = $api->getUser(1);
?>
```

---

## Protected Visibility

### Protected Properties

```php
<?php
class Animal {
    protected $name;        // Only accessible in this class and subclasses
    protected $age;
    
    public function __construct($name, $age) {
        $this->name = $name;
        $this->age = $age;
    }
    
    public function getName() {
        return $this->name;
    }
}

class Dog extends Animal {
    public function bark() {
        // Can access protected property from subclass
        return "{$this->name} barks: Woof! Woof!";
    }
}

$dog = new Dog('Rex', 5);
echo $dog->bark();              // OK
// echo $dog->name;             // Error - not accessible outside class
?>
```

### Protected Methods

```php
<?php
class BaseService {
    protected function validate($data) {
        // Internal validation
        return !empty($data);
    }
    
    protected function sanitize($data) {
        // Internal sanitization
        return trim($data);
    }
    
    public function process($data) {
        if ($this->validate($data)) {
            return $this->sanitize($data);
        }
        return null;
    }
}

class ExtendedService extends BaseService {
    public function extendedProcess($data) {
        // Can call protected method from subclass
        if ($this->validate($data)) {
            $sanitized = $this->sanitize($data);
            return strtoupper($sanitized);
        }
        return null;
    }
}

$service = new ExtendedService();
echo $service->process('hello');        // OK - uses public method
echo $service->extendedProcess('test'); // OK - uses subclass public method
// $service->validate('data');          // Error - protected
?>
```

### When to Use Protected

```php
<?php
class Database {
    protected function connect() {
        // Can be overridden by subclasses
        echo "Connecting to database\n";
    }
    
    public function query($sql) {
        $this->connect();
        echo "Executing: $sql\n";
    }
}

class SecureDatabase extends Database {
    protected function connect() {
        // Override parent's connect
        echo "Securely connecting with encryption\n";
    }
}

$db = new SecureDatabase();
$db->query('SELECT * FROM users');  // Uses overridden connect method
?>
```

---

## Private Visibility

### Private Properties

```php
<?php
class BankAccount {
    private $balance = 0;   // Only accessible within this class
    private $accountNumber;
    private $pin;
    
    public function __construct($accountNumber, $pin) {
        $this->accountNumber = $accountNumber;
        $this->pin = $pin;
    }
    
    public function deposit($amount) {
        if ($amount > 0) {
            $this->balance += $amount;
            return true;
        }
        return false;
    }
    
    public function getBalance() {
        return $this->balance;  // Only way to access private property
    }
    
    private function validatePin($pin) {
        return $this->pin === $pin;
    }
}

$account = new BankAccount('123456', '1234');
$account->deposit(1000);
echo $account->getBalance();        // OK - public method
// echo $account->balance;          // Error - private
// $account->validatePin('1234');   // Error - private method
?>
```

### Private Methods

```php
<?php
class FileProcessor {
    private function readFile($path) {
        // Private implementation detail
        return file_get_contents($path);
    }
    
    private function parseData($content) {
        // Internal parsing logic
        return json_decode($content, true);
    }
    
    private function validateData($data) {
        // Internal validation
        return !empty($data);
    }
    
    // Public interface
    public function process($filePath) {
        $content = $this->readFile($filePath);
        $data = $this->parseData($content);
        
        if ($this->validateData($data)) {
            return $data;
        }
        return null;
    }
}

$processor = new FileProcessor();
$result = $processor->process('data.json');  // OK - public method only
// $processor->readFile('data.json');        // Error - private
?>
```

### When to Use Private

```php
<?php
class PasswordHasher {
    private $algorithm = PASSWORD_BCRYPT;
    private $cost = 12;
    
    private function generateSalt() {
        // Internal implementation
        return random_bytes(16);
    }
    
    private function validateStrength($password) {
        // Internal validation
        return strlen($password) >= 8;
    }
    
    public function hash($password) {
        if (!$this->validateStrength($password)) {
            throw new Exception('Password too weak');
        }
        
        return password_hash($password, $this->algorithm, [
            'cost' => $this->cost
        ]);
    }
}

$hasher = new PasswordHasher();
$hashed = $hasher->hash('SecurePass123');   // OK
// $hasher->generateSalt();                  // Error - private
?>
```

---

## Visibility in Inheritance

### Protected Members in Inheritance Chain

```php
<?php
class GrandParent {
    protected $value = 'grand parent';
    
    protected function getData() {
        return $this->value;
    }
}

class Parent1 extends GrandParent {
    protected function display() {
        echo $this->getData();  // OK - inherited protected
    }
}

class Child1 extends Parent1 {
    public function show() {
        echo $this->getData();  // OK - protected from ancestor
        $this->display();       // OK - protected from parent
    }
}

$child = new Child1();
$child->show();  // Accessible through inheritance chain
?>
```

### Cannot Reduce Visibility

```php
<?php
class Parent2 {
    public function publicMethod() {
        return 'public';
    }
    
    protected function protectedMethod() {
        return 'protected';
    }
}

class Child2 extends Parent2 {
    // ✓ Can increase visibility
    public function protectedMethod() {
        return parent::protectedMethod();
    }
    
    // ✗ Cannot reduce visibility (would be error in strict typing)
    // protected function publicMethod() {}  // Error
}
?>
```

---

## Best Practices

### Encapsulation with Getters and Setters

```php
<?php
class User {
    private $email;
    private $age;
    
    // Getter
    public function getEmail() {
        return $this->email;
    }
    
    // Setter with validation
    public function setEmail($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
            return true;
        }
        return false;
    }
    
    public function getAge() {
        return $this->age;
    }
    
    public function setAge($age) {
        if ($age > 0 && $age < 150) {
            $this->age = $age;
            return true;
        }
        return false;
    }
}

$user = new User();
if ($user->setEmail('john@example.com')) {
    echo $user->getEmail();
}
?>
```

### Interface-Based Design

```php
<?php
class PaymentProcessor {
    // Public interface
    public function process($amount) {
        $this->validate($amount);
        return $this->executePayment($amount);
    }
    
    // Protected for subclass extension
    protected function validate($amount) {
        if ($amount <= 0) {
            throw new Exception('Invalid amount');
        }
    }
    
    // Private implementation
    private function executePayment($amount) {
        // Actual payment logic
        return ['status' => 'success', 'amount' => $amount];
    }
}
?>
```

---

## Practical Examples

### Library System with Visibility

```php
<?php
class LibraryItem {
    private $id;
    private $title;
    private $author;
    protected $available = true;
    
    public function __construct($id, $title, $author) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
    }
    
    public function getInfo() {
        return "{$this->title} by {$this->author}";
    }
    
    protected function checkAvailability() {
        return $this->available;
    }
    
    public function isAvailable() {
        return $this->checkAvailability();
    }
}

class Book extends LibraryItem {
    private $pages;
    
    public function __construct($id, $title, $author, $pages) {
        parent::__construct($id, $title, $author);
        $this->pages = $pages;
    }
    
    public function borrow() {
        if ($this->checkAvailability()) {
            $this->available = false;
            return true;
        }
        return false;
    }
    
    public function return() {
        $this->available = true;
    }
}

$book = new Book(1, 'PHP Guide', 'John Doe', 350);
echo $book->getInfo();
if ($book->borrow()) {
    echo "Book borrowed\n";
}
?>
```

---

## Common Mistakes

### 1. Exposing Implementation Details

```php
<?php
// ❌ Wrong: Public properties expose internals
class BankAccount {
    public $balance = 0;  // Can be modified directly!
    
    public function deposit($amount) {
        $this->balance += $amount;
    }
}

$account = new BankAccount();
$account->balance = -1000;  // Hack! Account can go negative

// ✓ Correct: Private property with validation
class BankAccount {
    private $balance = 0;
    
    public function deposit($amount) {
        if ($amount > 0) {
            $this->balance += $amount;
        }
    }
    
    public function getBalance() {
        return $this->balance;
    }
}
?>
```

### 2. Over-Protecting Methods

```php
<?php
// ❌ Wrong: Making everything private
class Service {
    private function validate($data) {}
    private function process($data) {}
    private function saveResult($data) {}
    private function logActivity($msg) {}  // Prevents extending
}

// ✓ Correct: Make extension points protected
class Service {
    protected function validate($data) {}
    protected function process($data) {}
    
    public function execute($data) {
        $this->validate($data);
        return $this->process($data);
    }
}

class ExtendedService extends Service {
    protected function validate($data) {
        // Override validation
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// E-Commerce Product with Proper Visibility

class Product {
    // Private - implementation details
    private $id;
    private $internalName;
    private $costPrice;
    private $discount = 0;
    
    // Protected - for extension
    protected $published = false;
    protected $stock = 0;
    
    // Public - external interface
    public $sellingPrice;
    
    public function __construct($id, $internalName, $costPrice, $sellingPrice) {
        $this->id = $id;
        $this->internalName = $internalName;
        $this->costPrice = $costPrice;
        $this->sellingPrice = $sellingPrice;
    }
    
    // Public interface for getting display name
    public function getDisplayName() {
        return ucfirst($this->internalName);
    }
    
    // Public method with validation
    public function setDiscount($discountPercent) {
        if ($discountPercent >= 0 && $discountPercent <= 100) {
            $this->discount = $discountPercent;
            return true;
        }
        return false;
    }
    
    public function getFinalPrice() {
        $discountAmount = $this->sellingPrice * ($this->discount / 100);
        return $this->sellingPrice - $discountAmount;
    }
    
    // Protected method for subclass extension
    protected function getProfitMargin() {
        return $this->sellingPrice - $this->costPrice;
    }
    
    // Private method for internal use
    private function validateStock() {
        return $this->stock > 0;
    }
    
    public function isAvailable() {
        return $this->validateStock() && $this->published;
    }
    
    public function publish() {
        $this->published = true;
    }
    
    public function updateStock($quantity) {
        $this->stock = max(0, $this->stock + $quantity);
    }
}

class DigitalProduct extends Product {
    public function getProfitMargin() {
        // Override protected method from parent
        $margin = parent::getProfitMargin();
        return $margin * 1.5;  // Digital products have higher margin
    }
}

// Usage
$product = new Product(1, 'laptop computer', 500, 999.99);
$product->setDiscount(15);
$product->updateStock(50);
$product->publish();

echo $product->getDisplayName() . "\n";
echo "Final Price: $" . $product->getFinalPrice() . "\n";
echo "Available: " . ($product->isAvailable() ? "Yes" : "No") . "\n";
?>
```

---

## Cross-References

- **Related Topic: [Encapsulation](#encapsulation-with-getters-and-setters)** - Data hiding principle
- **Related Topic: [Inheritance Basics](11-inheritance.md)** - How visibility affects subclasses
- **Related Topic: [Properties](4-properties.md)** - Property declaration
- **Related Topic: [Methods/Functions](5-function.md)** - Method declaration
- **Related Topic: [Classes](2-class.md)** - Class structure
