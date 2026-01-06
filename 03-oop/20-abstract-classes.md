# Abstract Classes in PHP

## Table of Contents
1. [Overview](#overview)
2. [Abstract Class Basics](#abstract-class-basics)
3. [Abstract Methods](#abstract-methods)
4. [Concrete Methods in Abstract Classes](#concrete-methods-in-abstract-classes)
5. [Abstract Properties](#abstract-properties)
6. [Inheritance Chains](#inheritance-chains)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

Abstract classes are classes that cannot be instantiated directly. They serve as templates defining a contract that subclasses must follow. Abstract classes combine concrete implementations with abstract methods that force subclasses to provide specific implementations. Abstract classes are more restrictive than interfaces—they can contain state (properties) and concrete methods, while interfaces define only contracts.

**Key Concepts:**
- Cannot instantiate abstract classes directly
- Define structure and contract for subclasses
- Mix concrete and abstract methods
- Contain properties and state
- Enforce implementation of abstract methods
- Enable shared functionality across related classes

---

## Abstract Class Basics

### Declaring Abstract Classes

```php
<?php
// Cannot instantiate abstract class
abstract class Animal {
    protected $name;
    protected $age;
    
    public function __construct($name, $age) {
        $this->name = $name;
        $this->age = $age;
    }
    
    public function getName() {
        return $this->name;
    }
}

// This works - extend the abstract class
class Dog extends Animal {
    // Must implement all abstract methods (none in this case)
}

$dog = new Dog("Buddy", 5);
echo $dog->getName();  // Buddy

// This fails - cannot instantiate abstract class
// $animal = new Animal("Generic", 1);  // Fatal error
?>
```

### Combining Abstract and Concrete Methods

```php
<?php
abstract class DatabaseConnection {
    protected $host;
    protected $username;
    
    public function __construct($host, $username) {
        $this->host = $host;
        $this->username = $username;
    }
    
    // Concrete method - same for all databases
    public function getInfo() {
        return "Connected to {$this->host} as {$this->username}";
    }
    
    // Abstract method - must be implemented by subclass
    abstract public function connect();
    abstract public function query($sql);
    abstract public function disconnect();
}

class MySQLConnection extends DatabaseConnection {
    private $connection;
    
    public function connect() {
        $this->connection = "MySQL connection established";
        return true;
    }
    
    public function query($sql) {
        return "MySQL query executed: $sql";
    }
    
    public function disconnect() {
        $this->connection = null;
        return "MySQL disconnected";
    }
}

class PostgreSQLConnection extends DatabaseConnection {
    private $connection;
    
    public function connect() {
        $this->connection = "PostgreSQL connection established";
        return true;
    }
    
    public function query($sql) {
        return "PostgreSQL query executed: $sql";
    }
    
    public function disconnect() {
        $this->connection = null;
        return "PostgreSQL disconnected";
    }
}

// Usage
$mysql = new MySQLConnection("localhost", "root");
echo $mysql->getInfo() . "\n";
echo $mysql->connect() . "\n";
echo $mysql->query("SELECT * FROM users") . "\n";

$postgres = new PostgreSQLConnection("localhost", "postgres");
echo $postgres->getInfo() . "\n";
echo $postgres->connect() . "\n";
?>
```

---

## Abstract Methods

### Enforcing Implementation

```php
<?php
abstract class Shape {
    // Abstract methods - no implementation
    abstract public function getArea();
    abstract public function getPerimeter();
    
    // Optional: define return type
    abstract public function describe(): string;
}

// Must implement all abstract methods
class Circle extends Shape {
    private $radius;
    
    public function __construct($radius) {
        $this->radius = $radius;
    }
    
    public function getArea() {
        return pi() * $this->radius ** 2;
    }
    
    public function getPerimeter() {
        return 2 * pi() * $this->radius;
    }
    
    public function describe(): string {
        return "Circle with radius {$this->radius}";
    }
}

class Rectangle extends Shape {
    private $width;
    private $height;
    
    public function __construct($width, $height) {
        $this->width = $width;
        $this->height = $height;
    }
    
    public function getArea() {
        return $this->width * $this->height;
    }
    
    public function getPerimeter() {
        return 2 * ($this->width + $this->height);
    }
    
    public function describe(): string {
        return "Rectangle {$this->width}x{$this->height}";
    }
}

// Works
$circle = new Circle(5);
echo $circle->describe() . "\n";  // Circle with radius 5

// This fails - missing abstract method implementations
// class Triangle extends Shape { }  // Fatal error
?>
```

### Type Hints with Abstract Classes

```php
<?php
abstract class Repository {
    protected $data = [];
    
    // Return type declaration
    abstract public function find($id): ?array;
    abstract public function save(array $data): bool;
    abstract public function delete($id): bool;
    abstract public function getAll(): array;
}

class UserRepository extends Repository {
    public function find($id): ?array {
        return $this->data[$id] ?? null;
    }
    
    public function save(array $data): bool {
        if (empty($data['id'])) {
            return false;
        }
        $this->data[$data['id']] = $data;
        return true;
    }
    
    public function delete($id): bool {
        if (isset($this->data[$id])) {
            unset($this->data[$id]);
            return true;
        }
        return false;
    }
    
    public function getAll(): array {
        return $this->data;
    }
}

// Usage
$repo = new UserRepository();
$repo->save(['id' => 1, 'name' => 'John']);
$repo->save(['id' => 2, 'name' => 'Jane']);

print_r($repo->find(1));
print_r($repo->getAll());
?>
```

---

## Concrete Methods in Abstract Classes

### Shared Implementation

```php
<?php
abstract class Logger {
    protected $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    // Concrete method - shared by all loggers
    public function formatMessage($level, $message) {
        return "[" . date('Y-m-d H:i:s') . "] [$level] [$this->name] $message";
    }
    
    // Abstract methods - specific to each logger
    abstract public function log($level, $message);
    abstract public function debug($message);
    abstract public function info($message);
    abstract public function error($message);
}

class FileLogger extends Logger {
    private $filename;
    
    public function __construct($name, $filename) {
        parent::__construct($name);
        $this->filename = $filename;
    }
    
    public function log($level, $message) {
        $formatted = $this->formatMessage($level, $message);
        file_put_contents($this->filename, $formatted . "\n", FILE_APPEND);
    }
    
    public function debug($message) {
        $this->log("DEBUG", $message);
    }
    
    public function info($message) {
        $this->log("INFO", $message);
    }
    
    public function error($message) {
        $this->log("ERROR", $message);
    }
}

class ConsoleLogger extends Logger {
    public function log($level, $message) {
        echo $this->formatMessage($level, $message) . "\n";
    }
    
    public function debug($message) {
        $this->log("DEBUG", $message);
    }
    
    public function info($message) {
        $this->log("INFO", $message);
    }
    
    public function error($message) {
        $this->log("ERROR", $message);
    }
}

// Usage
$console = new ConsoleLogger("App");
$console->info("Application started");
$console->error("Something went wrong");
?>
```

### Template Method Pattern

```php
<?php
// Abstract class defines algorithm structure
abstract class ReportGenerator {
    // Template method - concrete, defines process
    final public function generate() {
        $this->prepareData();
        $this->validateData();
        $content = $this->formatContent();
        $this->saveReport($content);
        return $content;
    }
    
    // Concrete helper method
    protected function validateData() {
        echo "Validating data...\n";
    }
    
    // Abstract methods - subclasses implement specific steps
    abstract protected function prepareData();
    abstract protected function formatContent(): string;
    abstract protected function saveReport($content);
}

class PDFReport extends ReportGenerator {
    protected function prepareData() {
        echo "Preparing data for PDF...\n";
    }
    
    protected function formatContent(): string {
        return "PDF Report Content";
    }
    
    protected function saveReport($content) {
        echo "Saving as PDF: $content\n";
    }
}

class HTMLReport extends ReportGenerator {
    protected function prepareData() {
        echo "Preparing data for HTML...\n";
    }
    
    protected function formatContent(): string {
        return "<html><body>Report Content</body></html>";
    }
    
    protected function saveReport($content) {
        echo "Saving as HTML: $content\n";
    }
}

// Usage - template method ensures consistent process
$pdf = new PDFReport();
$pdf->generate();

echo "\n";

$html = new HTMLReport();
$html->generate();
?>
```

---

## Abstract Properties

### Using Protected Properties

```php
<?php
abstract class Vehicle {
    // Protected properties - accessible to subclasses
    protected $brand;
    protected $color;
    protected $speed = 0;
    protected $maxSpeed;
    
    public function __construct($brand, $color, $maxSpeed) {
        $this->brand = $brand;
        $this->color = $color;
        $this->maxSpeed = $maxSpeed;
    }
    
    // Abstract method - subclass must implement
    abstract public function accelerate();
    
    // Concrete method - uses protected properties
    public function getStatus() {
        return "$this->brand $this->color car - Speed: {$this->speed}/{$this->maxSpeed}";
    }
}

class Car extends Vehicle {
    public function accelerate() {
        if ($this->speed < $this->maxSpeed) {
            $this->speed += 10;
        }
        return $this->getStatus();
    }
}

class Bicycle extends Vehicle {
    public function accelerate() {
        if ($this->speed < $this->maxSpeed) {
            $this->speed += 3;
        }
        return $this->getStatus();
    }
}

// Usage
$car = new Car("Toyota", "Red", 200);
echo $car->accelerate() . "\n";
echo $car->accelerate() . "\n";

$bike = new Bicycle("Trek", "Blue", 50);
echo $bike->accelerate() . "\n";
?>
```

---

## Inheritance Chains

### Multi-level Abstract Classes

```php
<?php
// First abstract class
abstract class DataProcessor {
    protected $data;
    
    public function __construct(array $data) {
        $this->data = $data;
    }
    
    abstract public function process();
}

// Second abstract class - extends first abstract class
abstract class Validator extends DataProcessor {
    protected $errors = [];
    
    public function validate() {
        $this->errors = [];
        return $this->validateData();
    }
    
    abstract protected function validateData(): bool;
    
    public function getErrors() {
        return $this->errors;
    }
}

// Concrete class - extends abstract class
class UserValidator extends Validator {
    protected function validateData(): bool {
        if (empty($this->data['email'])) {
            $this->errors[] = "Email required";
            return false;
        }
        
        if (empty($this->data['name'])) {
            $this->errors[] = "Name required";
            return false;
        }
        
        return true;
    }
    
    public function process() {
        if ($this->validate()) {
            return "User data valid";
        }
        return "User data invalid";
    }
}

// Usage
$validator = new UserValidator(['name' => 'John']);
echo $validator->process() . "\n";
print_r($validator->getErrors());
?>
```

---

## Practical Examples

### Payment System

```php
<?php
abstract class Payment {
    protected $amount;
    protected $currency = 'USD';
    protected $status = 'pending';
    
    public function __construct($amount) {
        if ($amount <= 0) {
            throw new Exception("Amount must be positive");
        }
        $this->amount = $amount;
    }
    
    // Concrete method
    public function getAmount() {
        return "{$this->currency} {$this->amount}";
    }
    
    // Concrete method
    public function getStatus() {
        return $this->status;
    }
    
    // Abstract methods
    abstract public function process();
    abstract public function refund();
    abstract public function getFee();
}

class CreditCardPayment extends Payment {
    private $cardNumber;
    
    public function __construct($amount, $cardNumber) {
        parent::__construct($amount);
        $this->cardNumber = $cardNumber;
    }
    
    public function process() {
        $fee = $this->getFee();
        $total = $this->amount + $fee;
        $this->status = 'completed';
        return "Charged {$this->currency} {$total} to credit card";
    }
    
    public function refund() {
        $this->status = 'refunded';
        return "Refunded {$this->currency} {$this->amount} to card";
    }
    
    public function getFee() {
        return $this->amount * 0.03;  // 3% fee
    }
}

class BankTransferPayment extends Payment {
    private $accountNumber;
    
    public function __construct($amount, $accountNumber) {
        parent::__construct($amount);
        $this->accountNumber = $accountNumber;
    }
    
    public function process() {
        $this->status = 'pending_confirmation';
        return "Transfer of {$this->currency} {$this->amount} initiated";
    }
    
    public function refund() {
        $this->status = 'refunded';
        return "Refund initiated for {$this->currency} {$this->amount}";
    }
    
    public function getFee() {
        return $this->amount * 0.01;  // 1% fee
    }
}

// Usage
$cc = new CreditCardPayment(100, '4111111111111111');
echo $cc->process() . "\n";
echo $cc->getStatus() . "\n";

$bank = new BankTransferPayment(500, '123456789');
echo $bank->process() . "\n";
echo "Fee: " . $bank->getFee() . "\n";
?>
```

---

## Common Mistakes

### 1. Forgetting to Implement Abstract Methods

```php
<?php
// ❌ Wrong: Missing abstract method implementation
abstract class Parent1 {
    abstract public function test();
}

class Child1 extends Parent1 {
    // Missing test() implementation!
}

// This fails: Fatal error - must implement test()
// $obj = new Child1();

// ✓ Correct: Implement all abstract methods
class Child2 extends Parent1 {
    public function test() {
        return "Implemented";
    }
}

$obj = new Child2();
?>
```

### 2. Instantiating Abstract Class

```php
<?php
// ❌ Wrong: Cannot instantiate abstract class
abstract class Base {
    abstract public function method();
}

// $base = new Base();  // Fatal error

// ✓ Correct: Extend and instantiate concrete class
class Concrete extends Base {
    public function method() {
        return "Concrete implementation";
    }
}

$concrete = new Concrete();
?>
```

### 3. Wrong Method Signature

```php
<?php
// ❌ Wrong: Different method signature
abstract class Parent1 {
    abstract public function process($id): int;
}

class Child1 extends Parent1 {
    // Different return type - violates contract
    public function process($id): string {
        return "Processed";
    }
}

// ✓ Correct: Matching signature
class Child2 extends Parent1 {
    public function process($id): int {
        return $id * 2;
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// Content Management System

abstract class Content {
    protected $id;
    protected $title;
    protected $author;
    protected $createdAt;
    protected $status = 'draft';
    
    public function __construct($id, $title, $author) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->createdAt = date('Y-m-d H:i:s');
    }
    
    // Concrete methods
    public function getMetadata() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'created' => $this->createdAt,
            'status' => $this->status
        ];
    }
    
    public function publish() {
        $this->status = 'published';
        return "{$this->title} published";
    }
    
    // Abstract methods
    abstract public function getContent();
    abstract public function getWordCount();
    abstract public function validate(): bool;
}

class Article extends Content {
    private $body;
    private $tags;
    
    public function __construct($id, $title, $author, $body, $tags = []) {
        parent::__construct($id, $title, $author);
        $this->body = $body;
        $this->tags = $tags;
    }
    
    public function getContent() {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'tags' => $this->tags
        ];
    }
    
    public function getWordCount() {
        return count(explode(' ', $this->body));
    }
    
    public function validate(): bool {
        return !empty($this->title) && !empty($this->body);
    }
}

class BlogPost extends Content {
    private $excerpt;
    private $body;
    private $category;
    
    public function __construct($id, $title, $author, $excerpt, $body, $category) {
        parent::__construct($id, $title, $author);
        $this->excerpt = $excerpt;
        $this->body = $body;
        $this->category = $category;
    }
    
    public function getContent() {
        return [
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'category' => $this->category
        ];
    }
    
    public function getWordCount() {
        return count(explode(' ', $this->excerpt . ' ' . $this->body));
    }
    
    public function validate(): bool {
        return !empty($this->title) && 
               !empty($this->excerpt) && 
               !empty($this->body) &&
               !empty($this->category);
    }
}

// Usage
$article = new Article(1, "PHP OOP", "John", "Content about OOP...", ['php', 'oop']);
echo $article->validate() ? "Article valid\n" : "Invalid\n";
echo $article->getWordCount() . " words\n";
echo $article->publish() . "\n";

$post = new BlogPost(2, "Web Dev Tips", "Jane", "Brief intro...", "Full content...", "Technology");
if ($post->validate()) {
    print_r($post->getMetadata());
}
?>
```

---

## Cross-References

- **Related Topic: [Interfaces](23-interfaces.md)** - Alternative to abstract classes
- **Related Topic: [Inheritance Basics](11-inheritance.md)** - Abstract class inheritance
- **Related Topic: [Method Overriding](15-function-overriding.md)** - Implementing abstract methods
- **Related Topic: [Polymorphism](18-polymorphism.md)** - Polymorphic behavior with abstract classes
- **Related Topic: [Visibility](14-visibility.md)** - Protected/private properties in abstract classes
