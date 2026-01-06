# The Parent Keyword in PHP

## Table of Contents
1. [Overview](#overview)
2. [Accessing Parent Methods](#accessing-parent-methods)
3. [Accessing Parent Properties](#accessing-parent-properties)
4. [Accessing Parent Constants](#accessing-parent-constants)
5. [parent:: in Constructor](#parent-in-constructor)
6. [Method Resolution](#method-resolution)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

The `parent` keyword refers to the parent class of the current class. It allows you to call parent class methods, access parent properties, and call the parent constructor. `parent::` is used to explicitly invoke methods and access constants from the parent class, which is essential when you override a parent method but still need its functionality.

**Key Concepts:**
- `parent::` refers to parent class
- Calls overridden parent methods
- Calls parent constructor
- Accesses parent constants
- Essential in method overriding
- Must be within a class context

---

## Accessing Parent Methods

### Calling Overridden Methods

```php
<?php
class Animal {
    public function speak() {
        return "Some sound";
    }
    
    public function eat() {
        return "Animal is eating";
    }
}

class Dog extends Animal {
    public function speak() {
        // Call parent's speak method first
        $parentSound = parent::speak();
        return $parentSound . " + Woof! Woof!";
    }
}

$dog = new Dog();
echo $dog->speak();  // Some sound + Woof! Woof!
?>
```

### Extending Parent Functionality

```php
<?php
class BaseController {
    protected function beforeAction() {
        echo "Authentication check\n";
    }
    
    protected function afterAction() {
        echo "Logging action\n";
    }
    
    public function handleRequest() {
        $this->beforeAction();
        echo "Handling request\n";
        $this->afterAction();
    }
}

class AdminController extends BaseController {
    protected function beforeAction() {
        // Call parent's check
        parent::beforeAction();
        // Add additional admin checks
        echo "Admin authorization check\n";
    }
    
    protected function afterAction() {
        parent::afterAction();
        echo "Admin action logging\n";
    }
}

$admin = new AdminController();
$admin->handleRequest();
// Output:
// Authentication check
// Admin authorization check
// Handling request
// Logging action
// Admin action logging
?>
```

### Calling Multiple Levels Up

```php
<?php
class Level1 {
    public function process() {
        return "Level 1 processing";
    }
}

class Level2 extends Level1 {
    public function process() {
        return parent::process() . " -> Level 2";
    }
}

class Level3 extends Level2 {
    public function process() {
        // Can only call direct parent (Level2)
        // Level2 will call its parent (Level1) via parent::
        return parent::process() . " -> Level 3";
    }
}

$level3 = new Level3();
echo $level3->process();
// Output: Level 1 processing -> Level 2 -> Level 3
?>
```

---

## Accessing Parent Properties

### Accessing Protected Properties via parent::

```php
<?php
class Vehicle {
    protected $brand;
    protected $color;
    
    public function __construct($brand, $color) {
        $this->brand = $brand;
        $this->color = $color;
    }
}

class Car extends Vehicle {
    private $doors;
    
    public function __construct($brand, $color, $doors) {
        parent::__construct($brand, $color);  // Call parent constructor
        $this->doors = $doors;
    }
    
    public function getDescription() {
        // Access parent's protected properties
        return $this->brand . " " . $this->color . " car with {$this->doors} doors";
    }
}

$car = new Car('Toyota', 'Blue', 4);
echo $car->getDescription();
?>
```

### Property Initialization Chain

```php
<?php
class Base {
    protected $name;
    protected $initialized = false;
    
    public function __construct($name) {
        $this->name = $name;
        $this->initialize();
    }
    
    protected function initialize() {
        $this->initialized = true;
    }
}

class Extended extends Base {
    protected $version;
    
    public function __construct($name, $version) {
        parent::__construct($name);  // Initialize parent first
        $this->version = $version;
    }
    
    public function getStatus() {
        return "{$this->name} v{$this->version} - " . 
               ($this->initialized ? "Ready" : "Not ready");
    }
}

$obj = new Extended('MyApp', '2.0');
echo $obj->getStatus();  // MyApp v2.0 - Ready
?>
```

---

## Accessing Parent Constants

### Using parent:: for Constants

```php
<?php
class Config {
    const DATABASE_HOST = 'localhost';
    const DATABASE_PORT = 3306;
    const TIMEOUT = 30;
}

class ExtendedConfig extends Config {
    const DATABASE_HOST = 'remote.server.com';  // Override
    
    public static function getSettings() {
        return [
            'host' => self::DATABASE_HOST,          // This class's constant
            'parent_host' => parent::DATABASE_HOST, // Parent's constant
            'port' => parent::DATABASE_PORT,        // Inherited
            'timeout' => self::TIMEOUT              // Inherited
        ];
    }
}

print_r(ExtendedConfig::getSettings());
// [
//     'host' => 'remote.server.com',
//     'parent_host' => 'localhost',
//     'port' => 3306,
//     'timeout' => 30
// ]
?>
```

---

## parent:: in Constructor

### Initializing Parent State

```php
<?php
class Person {
    protected $name;
    protected $age;
    protected $email;
    
    public function __construct($name, $age, $email) {
        $this->name = $name;
        $this->age = $age;
        $this->email = $email;
    }
    
    public function getInfo() {
        return "{$this->name}, {$this->age} years old";
    }
}

class Employee extends Person {
    protected $employeeId;
    protected $department;
    protected $salary;
    
    public function __construct($name, $age, $email, $employeeId, $department, $salary) {
        parent::__construct($name, $age, $email);  // Must call parent first
        
        $this->employeeId = $employeeId;
        $this->department = $department;
        $this->salary = $salary;
    }
    
    public function getFullInfo() {
        $base = parent::getInfo();  // Call parent method
        return $base . " - Employee #{$this->employeeId} - {$this->department}";
    }
}

$emp = new Employee('John', 30, 'john@example.com', 'EMP001', 'Engineering', 75000);
echo $emp->getFullInfo();
?>
```

### Constructor Chaining

```php
<?php
class Animal {
    protected $name;
    
    public function __construct($name) {
        $this->name = $name;
        echo "Animal created: $name\n";
    }
}

class Mammal extends Animal {
    protected $warmBlooded = true;
    
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
// Animal created: Rex
// Mammal created
// Dog created: Golden Retriever
?>
```

---

## Method Resolution

### Dynamic Method Binding

```php
<?php
class Base {
    public function run() {
        return $this->work();
    }
    
    protected function work() {
        return "Base work";
    }
}

class Child extends Base {
    protected function work() {
        return "Child work";  // Overrides parent
    }
}

$child = new Child();
echo $child->run();  // "Child work" - polymorphism in action
?>
```

### Late Static Binding with parent::

```php
<?php
class Parent1 {
    public static function who() {
        return 'Parent';
    }
    
    public static function test() {
        return parent::who();  // Always Parent
    }
}

class Child1 extends Parent1 {
    public static function who() {
        return 'Child';
    }
}

echo Parent1::test();  // Parent
echo Child1::test();   // Parent (parent:: is early bound)
?>
```

---

## Practical Examples

### Payment Processing Hierarchy

```php
<?php
abstract class PaymentProcessor {
    protected $transactionId;
    protected $amount;
    protected $fee = 0;
    
    public function __construct($amount) {
        $this->amount = $amount;
        $this->transactionId = uniqid();
    }
    
    protected function calculateFee() {
        return 0;
    }
    
    public function process() {
        $this->fee = $this->calculateFee();
        return [
            'id' => $this->transactionId,
            'amount' => $this->amount,
            'fee' => $this->fee,
            'total' => $this->amount + $this->fee
        ];
    }
}

class CreditCardProcessor extends PaymentProcessor {
    protected function calculateFee() {
        return $this->amount * 0.03;  // 3% fee
    }
}

class StripeProcessor extends CreditCardProcessor {
    protected function calculateFee() {
        $baseFee = parent::calculateFee();  // Get credit card fee
        return $baseFee + 0.30;  // Add fixed fee
    }
    
    public function process() {
        $result = parent::process();
        $result['processor'] = 'Stripe';
        return $result;
    }
}

$stripe = new StripeProcessor(100);
print_r($stripe->process());
?>
```

### Template Method Pattern

```php
<?php
abstract class DataImporter {
    protected $data = [];
    
    final public function import($source) {
        $this->loadData($source);
        $this->validateData();
        $this->transformData();
        $this->saveData();
    }
    
    abstract protected function loadData($source);
    
    protected function validateData() {
        // Default validation
        echo "Validating data\n";
    }
    
    protected function transformData() {
        // Default transformation
        echo "Transforming data\n";
    }
    
    abstract protected function saveData();
}

class CSVImporter extends DataImporter {
    protected function loadData($source) {
        echo "Loading CSV from $source\n";
    }
    
    protected function validateData() {
        parent::validateData();  // Call parent's validation
        echo "Additional CSV validation\n";
    }
    
    protected function saveData() {
        echo "Saving CSV data to database\n";
    }
}

class JSONImporter extends DataImporter {
    protected function loadData($source) {
        echo "Loading JSON from $source\n";
    }
    
    protected function saveData() {
        echo "Saving JSON data to database\n";
    }
}

$csv = new CSVImporter();
$csv->import('data.csv');
?>
```

---

## Common Mistakes

### 1. Forgetting parent:: in Constructor

```php
<?php
// ❌ Wrong: Parent constructor not called
class Parent1 {
    protected $initialized = false;
    
    public function __construct() {
        $this->initialized = true;
    }
}

class Child1 extends Parent1 {
    public function __construct() {
        // Missing parent::__construct()
    }
}

$child = new Child1();
var_dump($child->initialized);  // null instead of true

// ✓ Correct: Call parent constructor
class Child2 extends Parent1 {
    public function __construct() {
        parent::__construct();  // Call parent first
    }
}
?>
```

### 2. Accessing Private Parent Properties

```php
<?php
// ❌ Wrong: Can't access private properties
class Parent1 {
    private $secret = 'hidden';  // Private
}

class Child1 extends Parent1 {
    public function reveal() {
        echo $this->secret;  // Error - private not inherited
        // echo parent::$secret;  // Error - syntax error
    }
}

// ✓ Correct: Use protected for inheritance
class Parent2 {
    protected $data = 'accessible';  // Protected
}

class Child2 extends Parent2 {
    public function getData() {
        return $this->data;  // OK - protected is inherited
    }
}
?>
```

### 3. Infinite Loop with parent::

```php
<?php
// ❌ Wrong: Potential infinite recursion
class Parent1 {
    public function process() {
        return "Parent processing";
    }
}

class Child1 extends Parent1 {
    public function process() {
        // This is OK - different behavior
        return parent::process() . " + Child processing";
    }
}

// But beware of this pattern:
class BadChild extends Parent1 {
    public function process() {
        return $this->process();  // Infinite loop! Calls itself
    }
}

// ✓ Correct: parent:: breaks the cycle
class GoodChild extends Parent1 {
    public function process() {
        return parent::process() . " + Child";  // Calls parent, not self
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// Library Management System with parent::

abstract class LibraryItem {
    protected $id;
    protected $title;
    protected $author;
    protected $year;
    protected $available = true;
    
    public function __construct($id, $title, $author, $year) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->year = $year;
    }
    
    public function getInfo() {
        return "{$this->title} by {$this->author} ({$this->year})";
    }
    
    public function borrow() {
        if ($this->available) {
            $this->available = false;
            return true;
        }
        return false;
    }
    
    public function return() {
        $this->available = true;
    }
    
    abstract public function getDescription();
}

class Book extends LibraryItem {
    protected $pages;
    
    public function __construct($id, $title, $author, $year, $pages) {
        parent::__construct($id, $title, $author, $year);
        $this->pages = $pages;
    }
    
    public function getDescription() {
        $baseInfo = parent::getInfo();
        return "$baseInfo - Book with $this->pages pages";
    }
}

class Magazine extends LibraryItem {
    protected $issue;
    
    public function __construct($id, $title, $author, $year, $issue) {
        parent::__construct($id, $title, $author, $year);
        $this->issue = $issue;
    }
    
    public function getDescription() {
        $baseInfo = parent::getInfo();
        return "$baseInfo - Issue $this->issue";
    }
}

class SpecialEdition extends Magazine {
    protected $specialFeatures;
    
    public function __construct($id, $title, $author, $year, $issue, $features) {
        parent::__construct($id, $title, $author, $year, $issue);
        $this->specialFeatures = $features;
    }
    
    public function getDescription() {
        $baseDescription = parent::getDescription();
        return $baseDescription . " - Special Edition with: " . implode(', ', $this->specialFeatures);
    }
}

// Usage
$book = new Book(1, 'PHP Guide', 'John Doe', 2023, 450);
echo $book->getDescription() . "\n";
if ($book->borrow()) {
    echo "Book borrowed\n";
}

$special = new SpecialEdition(2, 'Tech Monthly', 'Jane Smith', 2023, 45, ['Interview', 'Tutorial']);
echo $special->getDescription() . "\n";
?>
```

---

## Cross-References

- **Related Topic: [Inheritance Basics](11-inheritance.md)** - Understanding inheritance
- **Related Topic: [Method Overriding](15-function-overriding.md)** - Overriding parent methods
- **Related Topic: [Constructor](9-constructor.md)** - Constructor initialization
- **Related Topic: [Self Keyword](8-self-keyword.md)** - Accessing current class
- **Related Topic: [Visibility/Access Modifiers](14-visibility.md)** - Protected properties
