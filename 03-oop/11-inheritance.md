# Inheritance Basics in PHP

## Table of Contents
1. [Overview](#overview)
2. [Basic Inheritance](#basic-inheritance)
3. [Parent and Child Classes](#parent-and-child-classes)
4. [Extending Properties](#extending-properties)
5. [Extending Methods](#extending-methods)
6. [Method Overriding](#method-overriding)
7. [Using Parent Methods](#using-parent-methods)
8. [Practical Examples](#practical-examples)
9. [Common Mistakes](#common-mistakes)
10. [Complete Working Example](#complete-working-example)
11. [Cross-References](#cross-references)

---

## Overview

Inheritance is a fundamental OOP concept that allows a class (child) to inherit properties and methods from another class (parent). It enables code reuse and establishes an "is-a" relationship between classes. A child class extends a parent class and can use and override its functionality.

**Key Concepts:**
- Single inheritance - one child class extends one parent
- PHP doesn't support multiple inheritance (use traits instead)
- Child classes inherit public and protected members
- Private members are not inherited
- Child can override parent methods
- `extends` keyword establishes inheritance relationship

---

## Basic Inheritance

### Simple Inheritance

```php
<?php
// Parent class
class Animal {
    public $name;
    public $age;
    
    public function __construct($name, $age) {
        $this->name = $name;
        $this->age = $age;
    }
    
    public function sleep() {
        echo "{$this->name} is sleeping\n";
    }
    
    public function eat() {
        echo "{$this->name} is eating\n";
    }
}

// Child class inherits from Animal
class Dog extends Animal {
    public function bark() {
        echo "{$this->name} says: Woof! Woof!\n";
    }
}

// Dog has all Animal methods plus its own
$dog = new Dog('Rex', 3);
$dog->sleep();      // Inherited method
$dog->eat();        // Inherited method
$dog->bark();       // Own method
?>
```

### Inheritance Chain

```php
<?php
class Vehicle {
    protected $brand;
    protected $color;
    
    public function __construct($brand, $color) {
        $this->brand = $brand;
        $this->color = $color;
    }
    
    public function start() {
        echo "{$this->brand} vehicle started\n";
    }
}

class Car extends Vehicle {
    private $doors;
    
    public function __construct($brand, $color, $doors) {
        parent::__construct($brand, $color);
        $this->doors = $doors;
    }
    
    public function openTrunk() {
        echo "{$this->brand} trunk opened\n";
    }
}

class SportsCar extends Car {
    private $topSpeed;
    
    public function __construct($brand, $color, $doors, $topSpeed) {
        parent::__construct($brand, $color, $doors);
        $this->topSpeed = $topSpeed;
    }
    
    public function accelerate() {
        echo "Accelerating to {$this->topSpeed} mph\n";
    }
}

$sports = new SportsCar('Ferrari', 'Red', 2, 220);
$sports->start();       // From Vehicle
$sports->openTrunk();   // From Car
$sports->accelerate();  // From SportsCar
?>
```

---

## Parent and Child Classes

### Accessing Parent Properties

```php
<?php
class Shape {
    protected $color;
    protected $filled;
    
    public function __construct($color, $filled = true) {
        $this->color = $color;
        $this->filled = $filled;
    }
    
    public function describe() {
        return "Color: {$this->color}, Filled: " . ($this->filled ? 'Yes' : 'No');
    }
}

class Rectangle extends Shape {
    protected $width;
    protected $height;
    
    public function __construct($color, $width, $height) {
        parent::__construct($color);
        $this->width = $width;
        $this->height = $height;
    }
    
    public function area() {
        return $this->width * $this->height;
    }
    
    public function describe() {
        $parentInfo = parent::describe();
        return $parentInfo . ", Width: {$this->width}, Height: {$this->height}";
    }
}

$rect = new Rectangle('Blue', 10, 20);
echo $rect->area();         // 200
echo $rect->describe();     // Color: Blue, Filled: Yes, Width: 10, Height: 20
?>
```

### Visibility in Inheritance

```php
<?php
class BaseClass {
    public $publicProp = 'public';       // Accessible everywhere
    protected $protectedProp = 'protected';  // Accessible in class and subclasses
    private $privateProp = 'private';   // Only in this class
    
    public function getInfo() {
        return $this->publicProp . ', ' . $this->protectedProp . ', ' . $this->privateProp;
    }
}

class ChildClass extends BaseClass {
    public function showInheritance() {
        echo $this->publicProp . "\n";        // OK - public
        echo $this->protectedProp . "\n";     // OK - protected
        // echo $this->privateProp;            // Error - private
    }
}

$obj = new ChildClass();
echo $obj->publicProp . "\n";               // OK - public
// echo $obj->protectedProp . "\n";         // Error - protected
// echo $obj->privateProp . "\n";           // Error - private
$obj->showInheritance();
?>
```

---

## Extending Properties

### Adding Properties in Child Class

```php
<?php
class Person {
    protected $name;
    protected $age;
    
    public function __construct($name, $age) {
        $this->name = $name;
        $this->age = $age;
    }
    
    public function getBasicInfo() {
        return "{$this->name} ({$this->age})";
    }
}

class Employee extends Person {
    protected $employeeId;
    protected $department;
    protected $salary;
    
    public function __construct($name, $age, $employeeId, $department, $salary) {
        parent::__construct($name, $age);
        $this->employeeId = $employeeId;
        $this->department = $department;
        $this->salary = $salary;
    }
    
    public function getFullInfo() {
        $basic = $this->getBasicInfo();
        return $basic . " - ID: {$this->employeeId}, Dept: {$this->department}, Salary: \${$this->salary}";
    }
}

$emp = new Employee('Alice', 30, 'EMP001', 'Engineering', 75000);
echo $emp->getFullInfo();
?>
```

### Typed Properties in Inheritance

```php
<?php
class Animal {
    protected string $name;
    protected int $age;
    
    public function __construct(string $name, int $age) {
        $this->name = $name;
        $this->age = $age;
    }
}

class Cat extends Animal {
    protected string $breed;
    
    public function __construct(string $name, int $age, string $breed) {
        parent::__construct($name, $age);
        $this->breed = $breed;
    }
}

$cat = new Cat('Whiskers', 5, 'Persian');
?>
```

---

## Extending Methods

### Inheriting Methods

```php
<?php
class DatabaseOperations {
    protected $tableName;
    
    public function __construct($tableName) {
        $this->tableName = $tableName;
    }
    
    protected function connect() {
        echo "Connected to database\n";
    }
    
    public function select($id) {
        $this->connect();
        echo "Selecting from {$this->tableName} where id = $id\n";
    }
    
    public function insert($data) {
        $this->connect();
        echo "Inserting into {$this->tableName}: " . json_encode($data) . "\n";
    }
}

class UserRepository extends DatabaseOperations {
    public function __construct() {
        parent::__construct('users');
    }
    
    // Inherits select and insert methods
    // Can add user-specific methods
    public function findByEmail($email) {
        echo "Finding user with email: $email\n";
    }
}

$repo = new UserRepository();
$repo->select(1);                   // Inherited
$repo->insert(['name' => 'John']);  // Inherited
$repo->findByEmail('john@example.com');
?>
```

### Calling Parent Methods from Child

```php
<?php
class Logger {
    protected function log($level, $message) {
        echo "[{$level}] {$message}\n";
    }
    
    public function info($message) {
        $this->log('INFO', $message);
    }
}

class DatabaseLogger extends Logger {
    private $logFile;
    
    public function __construct($logFile) {
        $this->logFile = $logFile;
    }
    
    public function log($level, $message) {
        // Call parent's log method
        parent::log($level, $message);
        
        // Add database-specific logging
        file_put_contents($this->logFile, "[{$level}] {$message}\n", FILE_APPEND);
    }
    
    public function database($message) {
        $this->log('DATABASE', $message);
    }
}

$logger = new DatabaseLogger('/tmp/db.log');
$logger->info('User logged in');
$logger->database('Query executed');
?>
```

---

## Method Overriding

### Complete Method Override

```php
<?php
class Shape {
    public function getArea() {
        return 0;
    }
    
    public function getDescription() {
        return 'I am a shape';
    }
}

class Circle extends Shape {
    private $radius;
    
    public function __construct($radius) {
        $this->radius = $radius;
    }
    
    // Override getArea
    public function getArea() {
        return pi() * $this->radius ** 2;
    }
    
    // Override getDescription
    public function getDescription() {
        return "I am a circle with radius {$this->radius}";
    }
}

$shape = new Shape();
echo $shape->getArea();             // 0
echo $shape->getDescription();      // I am a shape

$circle = new Circle(5);
echo $circle->getArea();            // ~78.54
echo $circle->getDescription();     // I am a circle with radius 5
?>
```

### Partial Override with Parent Call

```php
<?php
class BaseController {
    protected $data = [];
    
    public function render() {
        $this->data['timestamp'] = time();
        return json_encode($this->data);
    }
}

class ApiController extends BaseController {
    private $version = '1.0';
    
    public function render() {
        // Call parent
        $output = parent::render();
        
        // Add additional processing
        $decoded = json_decode($output, true);
        $decoded['api_version'] = $this->version;
        
        return json_encode($decoded);
    }
}

$api = new ApiController();
echo $api->render();
?>
```

---

## Using Parent Methods

### The parent:: Keyword

```php
<?php
class Payment {
    protected $amount;
    protected $currency = 'USD';
    
    public function __construct($amount) {
        $this->amount = $amount;
    }
    
    public function validate() {
        return $this->amount > 0;
    }
    
    public function process() {
        if ($this->validate()) {
            return "Processing {$this->currency} {$this->amount}";
        }
        return 'Invalid amount';
    }
}

class CreditCardPayment extends Payment {
    private $cardNumber;
    
    public function __construct($amount, $cardNumber) {
        parent::__construct($amount);
        $this->cardNumber = $cardNumber;
    }
    
    public function validate() {
        // Call parent validation
        if (!parent::validate()) {
            return false;
        }
        
        // Add additional validation
        return strlen($this->cardNumber) === 16;
    }
}

$payment = new CreditCardPayment(100, '1234567890123456');
echo $payment->validate();  // true
echo $payment->process();   // Processing USD 100
?>
```

---

## Practical Examples

### Bank Account Hierarchy

```php
<?php
class Account {
    protected $accountNumber;
    protected $balance = 0;
    protected $owner;
    
    public function __construct($accountNumber, $owner) {
        $this->accountNumber = $accountNumber;
        $this->owner = $owner;
    }
    
    public function deposit($amount) {
        if ($amount > 0) {
            $this->balance += $amount;
            return true;
        }
        return false;
    }
    
    public function getBalance() {
        return $this->balance;
    }
}

class SavingsAccount extends Account {
    private $interestRate = 0.03;
    
    public function applyInterest() {
        $interest = $this->balance * $this->interestRate;
        $this->balance += $interest;
        return $interest;
    }
}

class CheckingAccount extends Account {
    private $monthlyFee = 5;
    
    public function chargeMonthlyFee() {
        if ($this->balance >= $this->monthlyFee) {
            $this->balance -= $this->monthlyFee;
        }
    }
}

$savings = new SavingsAccount('SAV001', 'Alice');
$savings->deposit(1000);
echo $savings->applyInterest();  // 30
echo $savings->getBalance();     // 1030
?>
```

### Document Hierarchy

```php
<?php
class Document {
    protected $title;
    protected $content;
    protected $author;
    
    public function __construct($title, $author) {
        $this->title = $title;
        $this->author = $author;
    }
    
    public function setContent($content) {
        $this->content = $content;
    }
    
    public function getInfo() {
        return "Title: {$this->title}, Author: {$this->author}";
    }
    
    public function display() {
        echo $this->getInfo() . "\n";
        echo "Content: {$this->content}\n";
    }
}

class PdfDocument extends Document {
    public function generatePdf() {
        echo "Generating PDF: {$this->title}\n";
        // PDF generation logic
    }
}

class WordDocument extends Document {
    public function generateWord() {
        echo "Generating Word document: {$this->title}\n";
        // Word generation logic
    }
}

$pdf = new PdfDocument('Report', 'John');
$pdf->setContent('This is a report');
$pdf->display();
$pdf->generatePdf();
?>
```

---

## Common Mistakes

### 1. Forgetting extends Keyword

```php
<?php
// ❌ Wrong: Not explicitly inheriting
class Parent1 {
    public function hello() {
        echo "Hello from parent";
    }
}

class Child1 {  // Should extend Parent1
    // Doesn't inherit parent's methods
}

// ✓ Correct: Use extends
class Child2 extends Parent1 {
    // Inherits from Parent1
}
?>
```

### 2. Accessing Private Parent Properties

```php
<?php
// ❌ Wrong: Private properties not inherited
class Parent1 {
    private $secret = 'hidden';
    
    public function getSecret() {
        return $this->secret;
    }
}

class Child1 extends Parent1 {
    public function revealSecret() {
        echo $this->secret;  // Error! Can't access private
    }
}

// ✓ Correct: Use protected or public
class Parent2 {
    protected $data = 'accessible';
}

class Child2 extends Parent2 {
    public function getData() {
        echo $this->data;  // OK - protected
    }
}
?>
```

### 3. Forgetting parent:: in Overridden Methods

```php
<?php
// ❌ Wrong: Not calling parent method when overriding
class Base {
    protected $initialized = false;
    
    public function initialize() {
        $this->initialized = true;
        echo "Base initialized\n";
    }
}

class Child extends Base {
    public function initialize() {
        echo "Child initializing\n";
        // Missing parent::initialize();
    }
}

$child = new Child();
$child->initialize();  // Base initialization skipped

// ✓ Correct: Call parent when needed
class ChildFixed extends Base {
    public function initialize() {
        parent::initialize();  // Call parent first
        echo "Child specific initialization\n";
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// Library Management System with Inheritance

abstract class LibraryItem {
    protected $id;
    protected $title;
    protected $author;
    protected $year;
    
    public function __construct($id, $title, $author, $year) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->year = $year;
    }
    
    public function getInfo() {
        return "{$this->title} by {$this->author} ({$this->year})";
    }
    
    abstract public function getType();
}

class Book extends LibraryItem {
    private $pages;
    
    public function __construct($id, $title, $author, $year, $pages) {
        parent::__construct($id, $title, $author, $year);
        $this->pages = $pages;
    }
    
    public function getType() {
        return 'Book';
    }
    
    public function getInfo() {
        return parent::getInfo() . " - Pages: {$this->pages}";
    }
}

class Magazine extends LibraryItem {
    private $issue;
    
    public function __construct($id, $title, $author, $year, $issue) {
        parent::__construct($id, $title, $author, $year);
        $this->issue = $issue;
    }
    
    public function getType() {
        return 'Magazine';
    }
    
    public function getInfo() {
        return parent::getInfo() . " - Issue: {$this->issue}";
    }
}

class DVD extends LibraryItem {
    private $duration;
    
    public function __construct($id, $title, $author, $year, $duration) {
        parent::__construct($id, $title, $author, $year);
        $this->duration = $duration;
    }
    
    public function getType() {
        return 'DVD';
    }
    
    public function getInfo() {
        return parent::getInfo() . " - Duration: {$this->duration} mins";
    }
}

class Library {
    private $items = [];
    
    public function addItem(LibraryItem $item) {
        $this->items[] = $item;
    }
    
    public function displayCatalog() {
        foreach ($this->items as $item) {
            echo "[{$item->getType()}] " . $item->getInfo() . "\n";
        }
    }
}

// Usage
$library = new Library();
$library->addItem(new Book(1, 'PHP Mastery', 'John Doe', 2023, 500));
$library->addItem(new Magazine(2, 'Tech Monthly', 'Jane Smith', 2023, 45));
$library->addItem(new DVD(3, 'Tutorial', 'Bob Johnson', 2023, 120));

$library->displayCatalog();
?>
```

---

## Cross-References

- **Related Topic: [Classes](2-class.md)** - Class fundamentals
- **Related Topic: [Parent Keyword](16-parent-keyword.md)** - Accessing parent class members
- **Related Topic: [Method Overriding](15-function-overriding.md)** - Overriding parent methods
- **Related Topic: [Abstract Classes](20-abstract-class.md)** - Creating extensible base classes
- **Related Topic: [Interfaces](23-interface.md)** - Defining contracts
- **Related Topic: [Visibility/Access Modifiers](14-visibility.md)** - Understanding protected/private
