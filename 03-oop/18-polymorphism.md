# Polymorphism in PHP

## Table of Contents
1. [Overview](#overview)
2. [Method Overriding](#method-overriding)
3. [Polymorphic Behavior](#polymorphic-behavior)
4. [Duck Typing](#duck-typing)
5. [Interfaces and Contracts](#interfaces-and-contracts)
6. [Abstract Classes](#abstract-classes)
7. [Type Hinting](#type-hinting)
8. [Practical Examples](#practical-examples)
9. [Common Mistakes](#common-mistakes)
10. [Complete Working Example](#complete-working-example)
11. [Cross-References](#cross-references)

---

## Overview

Polymorphism means "many forms" and allows objects of different types to be treated as objects of a common parent type. This enables you to write flexible, maintainable code that works with multiple object types without knowing their exact classes. Polymorphism is achieved through inheritance, interfaces, and abstract classes.

**Key Concepts:**
- Single interface, multiple implementations
- Write code for base type, works with all subtypes
- Reduces coupling between classes
- Enables extensibility without modification
- Core principle of OOP design patterns

---

## Method Overriding

### Simple Method Overriding

```php
<?php
class Animal {
    public function speak() {
        return "Some sound";
    }
    
    public function move() {
        return "Moving...";
    }
}

class Dog extends Animal {
    public function speak() {
        return "Woof! Woof!";
    }
}

class Cat extends Animal {
    public function speak() {
        return "Meow...";
    }
}

// Polymorphism: same method, different behaviors
$animals = [new Dog(), new Cat(), new Animal()];

foreach ($animals as $animal) {
    echo $animal->speak() . "\n";  // Each responds differently
}
// Output:
// Woof! Woof!
// Meow...
// Some sound
?>
```

### Extending Parent Behavior

```php
<?php
class Person {
    protected $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function greet() {
        return "Hi, I'm {$this->name}";
    }
}

class Employee extends Person {
    protected $employeeId;
    
    public function __construct($name, $employeeId) {
        parent::__construct($name);
        $this->employeeId = $employeeId;
    }
    
    public function greet() {
        // Extend parent behavior
        return parent::greet() . " (Employee #{$this->employeeId})";
    }
}

class Manager extends Employee {
    protected $department;
    
    public function __construct($name, $employeeId, $department) {
        parent::__construct($name, $employeeId);
        $this->department = $department;
    }
    
    public function greet() {
        return parent::greet() . " - {$this->department}";
    }
}

$person = new Person("Alice");
$employee = new Employee("Bob", "E123");
$manager = new Manager("Charlie", "M456", "Engineering");

echo $person->greet() . "\n";
echo $employee->greet() . "\n";
echo $manager->greet() . "\n";
?>
```

---

## Polymorphic Behavior

### Working with Base Type

```php
<?php
class Shape {
    public function getArea() {
        return 0;
    }
    
    public function getPerimeter() {
        return 0;
    }
    
    public function describe() {
        return "Unknown shape";
    }
}

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
    
    public function describe() {
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
    
    public function describe() {
        return "Rectangle {$this->width}x{$this->height}";
    }
}

// Polymorphic function - works with any Shape
function printShapeInfo(Shape $shape) {
    echo $shape->describe() . "\n";
    echo "Area: " . round($shape->getArea(), 2) . "\n";
    echo "Perimeter: " . round($shape->getPerimeter(), 2) . "\n\n";
}

$shapes = [
    new Circle(5),
    new Rectangle(4, 6),
    new Circle(3),
];

foreach ($shapes as $shape) {
    printShapeInfo($shape);  // Polymorphic call
}
?>
```

### Payment Processing Example

```php
<?php
abstract class PaymentMethod {
    protected $amount;
    
    public function __construct($amount) {
        $this->amount = $amount;
    }
    
    abstract public function process();
    abstract public function refund();
    abstract public function getTransactionFee();
}

class CreditCard extends PaymentMethod {
    private $cardNumber;
    
    public function __construct($amount, $cardNumber) {
        parent::__construct($amount);
        $this->cardNumber = $cardNumber;
    }
    
    public function process() {
        $fee = $this->getTransactionFee();
        return [
            'success' => true,
            'method' => 'Credit Card',
            'amount' => $this->amount,
            'fee' => $fee,
            'total' => $this->amount + $fee
        ];
    }
    
    public function refund() {
        return "Refund of \${$this->amount} to credit card";
    }
    
    public function getTransactionFee() {
        return $this->amount * 0.03;  // 3% fee
    }
}

class PayPal extends PaymentMethod {
    private $email;
    
    public function __construct($amount, $email) {
        parent::__construct($amount);
        $this->email = $email;
    }
    
    public function process() {
        $fee = $this->getTransactionFee();
        return [
            'success' => true,
            'method' => 'PayPal',
            'amount' => $this->amount,
            'fee' => $fee,
            'total' => $this->amount + $fee
        ];
    }
    
    public function refund() {
        return "Refund of \${$this->amount} to PayPal account {$this->email}";
    }
    
    public function getTransactionFee() {
        return 0.30 + ($this->amount * 0.029);  // Fixed + percentage
    }
}

// Polymorphic payment processing
function processPayment(PaymentMethod $payment) {
    $result = $payment->process();
    echo "Processing {$result['method']}\n";
    echo "Amount: \${$result['amount']}\n";
    echo "Fee: \${$result['fee']}\n";
    echo "Total: \${$result['total']}\n\n";
}

$card = new CreditCard(100, '4111111111111111');
$paypal = new PayPal(100, 'user@example.com');

processPayment($card);
processPayment($paypal);
?>
```

---

## Duck Typing

### Interface-like Behavior Without Interface

```php
<?php
// PHP allows duck typing - if it quacks like a duck, it is a duck

class Logger {
    public function log($message) {
        echo "[LOG] $message\n";
    }
}

class DatabaseLogger {
    public function log($message) {
        // Would log to database
        echo "[DB] $message\n";
    }
}

class FileLogger {
    public function log($message) {
        // Would log to file
        echo "[FILE] $message\n";
    }
}

// Works with any object that has log() method
function logEvent($logger, $event) {
    $logger->log("Event occurred: $event");
}

logEvent(new Logger(), "User login");
logEvent(new DatabaseLogger(), "User logout");
logEvent(new FileLogger(), "Database error");

// But this is risky - better to use interfaces
?>
```

---

## Interfaces and Contracts

### Interface-Based Polymorphism

```php
<?php
interface Saveable {
    public function save();
    public function load($id);
}

interface Serializable {
    public function serialize();
    public function unserialize($data);
}

class User implements Saveable, Serializable {
    private $id;
    private $name;
    private $email;
    
    public function __construct($id, $name, $email) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }
    
    public function save() {
        return "Saving user {$this->id}: {$this->name}";
    }
    
    public function load($id) {
        // Load user from storage
        return "Loading user $id";
    }
    
    public function serialize() {
        return json_encode([
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email
        ]);
    }
    
    public function unserialize($data) {
        $arr = json_decode($data, true);
        $this->id = $arr['id'];
        $this->name = $arr['name'];
        $this->email = $arr['email'];
    }
}

class Product implements Saveable, Serializable {
    private $id;
    private $name;
    private $price;
    
    public function __construct($id, $name, $price) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }
    
    public function save() {
        return "Saving product {$this->id}: {$this->name}";
    }
    
    public function load($id) {
        return "Loading product $id";
    }
    
    public function serialize() {
        return json_encode([
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price
        ]);
    }
    
    public function unserialize($data) {
        $arr = json_decode($data, true);
        $this->id = $arr['id'];
        $this->name = $arr['name'];
        $this->price = $arr['price'];
    }
}

// Type-hinted to interface - works with any implementation
function saveThenSerialize(Saveable $object) {
    echo $object->save() . "\n";
    return $object->serialize();
}

$user = new User(1, "John", "john@example.com");
$product = new Product(101, "Laptop", 999.99);

$json1 = saveThenSerialize($user);
$json2 = saveThenSerialize($product);

echo $json1 . "\n";
echo $json2 . "\n";
?>
```

---

## Abstract Classes

### Abstract Method Enforcement

```php
<?php
abstract class Report {
    protected $title;
    protected $data;
    
    public function __construct($title, array $data) {
        $this->title = $title;
        $this->data = $data;
    }
    
    // Abstract methods - must be implemented by subclasses
    abstract public function generate();
    abstract public function export();
    
    // Concrete method - shared by all reports
    public function getTitle() {
        return $this->title;
    }
}

class PDFReport extends Report {
    public function generate() {
        return "Generating PDF report: {$this->title}";
    }
    
    public function export() {
        return "Exporting to PDF...";
    }
}

class CSVReport extends Report {
    public function generate() {
        return "Generating CSV report: {$this->title}";
    }
    
    public function export() {
        $csv = "";
        foreach ($this->data as $row) {
            $csv .= implode(',', $row) . "\n";
        }
        return $csv;
    }
}

function generateAndExport(Report $report) {
    echo $report->generate() . "\n";
    echo $report->export() . "\n";
}

$pdf = new PDFReport("Monthly Sales", [['Jan', 1000], ['Feb', 1500]]);
$csv = new CSVReport("Monthly Sales", [['Jan', 1000], ['Feb', 1500]]);

generateAndExport($pdf);
generateAndExport($csv);
?>
```

---

## Type Hinting

### Type-Safe Polymorphism

```php
<?php
interface NotificationService {
    public function send($message, $recipient);
}

class EmailNotification implements NotificationService {
    public function send($message, $recipient) {
        return "Email sent to $recipient: $message";
    }
}

class SMSNotification implements NotificationService {
    public function send($message, $recipient) {
        return "SMS sent to $recipient: $message";
    }
}

class SlackNotification implements NotificationService {
    public function send($message, $recipient) {
        return "Slack message sent to $recipient: $message";
    }
}

// Type hinted to interface
class NotificationManager {
    private $service;
    
    public function __construct(NotificationService $service) {
        $this->service = $service;
    }
    
    public function notify($message, $recipient) {
        return $this->service->send($message, $recipient);
    }
    
    public function setService(NotificationService $service) {
        $this->service = $service;
        return $this;  // Fluent interface
    }
}

// Usage - polymorphism in action
$manager = new NotificationManager(new EmailNotification());
echo $manager->notify("Hello", "user@example.com") . "\n";

$manager->setService(new SMSNotification());
echo $manager->notify("Hello", "+1234567890") . "\n";

$manager->setService(new SlackNotification());
echo $manager->notify("Hello", "@channel") . "\n";
?>
```

---

## Practical Examples

### Database Query Builder

```php
<?php
abstract class QueryBuilder {
    protected $table;
    protected $where = [];
    protected $select = ['*'];
    
    public function from($table) {
        $this->table = $table;
        return $this;
    }
    
    public function select(...$fields) {
        $this->select = $fields;
        return $this;
    }
    
    abstract public function build();
    abstract public function execute();
}

class MySQLQueryBuilder extends QueryBuilder {
    public function build() {
        $sql = "SELECT " . implode(', ', $this->select) . " FROM {$this->table}";
        return $sql;
    }
    
    public function execute() {
        $sql = $this->build();
        return "Executing MySQL: $sql";
    }
}

class PostgreSQLQueryBuilder extends QueryBuilder {
    public function build() {
        $sql = "SELECT " . implode(', ', $this->select) . " FROM \"{$this->table}\"";
        return $sql;
    }
    
    public function execute() {
        $sql = $this->build();
        return "Executing PostgreSQL: $sql";
    }
}

// Use any builder polymorphically
$mysql = (new MySQLQueryBuilder())->from('users')->select('id', 'name');
$postgres = (new PostgreSQLQueryBuilder())->from('users')->select('id', 'name');

echo $mysql->execute() . "\n";
echo $postgres->execute() . "\n";
?>
```

---

## Common Mistakes

### 1. Not Using Type Hints

```php
<?php
// ❌ Wrong: No type hinting - unclear what's accepted
function processData($object) {
    return $object->process();  // What if $object doesn't have process()?
}

// ✓ Correct: Type hinted to interface/class
interface Processable {
    public function process();
}

function processData(Processable $object) {
    return $object->process();  // Guaranteed to have process()
}
?>
```

### 2. Breaking Liskov Substitution Principle

```php
<?php
// ❌ Wrong: Child has incompatible behavior
class Bird {
    public function fly() {
        return "Flying...";
    }
}

class Penguin extends Bird {
    public function fly() {
        throw new Exception("Penguins can't fly!");  // Violates contract
    }
}

function makeBirdFly(Bird $bird) {
    return $bird->fly();  // Fails for Penguin!
}

// ✓ Correct: Use composition instead
interface Flyable {
    public function fly();
}

class FlyingBird implements Flyable {
    public function fly() {
        return "Flying...";
    }
}

class Penguin {
    public function swim() {
        return "Swimming...";
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// E-Commerce Shopping System

interface PaymentProcessor {
    public function validatePayment();
    public function processPayment($amount);
    public function getPaymentMethod();
}

interface ShippingProvider {
    public function calculateCost($weight, $distance);
    public function createShipment($items);
    public function trackShipment($trackingId);
}

class CreditCardProcessor implements PaymentProcessor {
    private $cardNumber;
    
    public function __construct($cardNumber) {
        $this->cardNumber = $cardNumber;
    }
    
    public function validatePayment() {
        return strlen($this->cardNumber) === 16;
    }
    
    public function processPayment($amount) {
        if ($this->validatePayment()) {
            return "Charged \${$amount} to credit card";
        }
        return "Payment failed";
    }
    
    public function getPaymentMethod() {
        return "Credit Card";
    }
}

class StandardShipping implements ShippingProvider {
    public function calculateCost($weight, $distance) {
        return ($weight * 0.5) + ($distance * 0.01);
    }
    
    public function createShipment($items) {
        return "Standard shipment created with " . count($items) . " items";
    }
    
    public function trackShipment($trackingId) {
        return "Tracking $trackingId via standard shipping";
    }
}

class ExpressShipping implements ShippingProvider {
    public function calculateCost($weight, $distance) {
        return ($weight * 1) + ($distance * 0.05);
    }
    
    public function createShipment($items) {
        return "Express shipment created with " . count($items) . " items";
    }
    
    public function trackShipment($trackingId) {
        return "Tracking $trackingId via express shipping (faster updates)";
    }
}

class Order {
    private $payment;
    private $shipping;
    private $items = [];
    private $total = 0;
    
    public function __construct(PaymentProcessor $payment, ShippingProvider $shipping) {
        $this->payment = $payment;
        $this->shipping = $shipping;
    }
    
    public function addItem($item, $price) {
        $this->items[] = $item;
        $this->total += $price;
        return $this;
    }
    
    public function checkout() {
        echo "Order Summary:\n";
        echo "Payment: " . $this->payment->getPaymentMethod() . "\n";
        echo "Amount: \${$this->total}\n";
        echo $this->payment->processPayment($this->total) . "\n";
        echo $this->shipping->createShipment($this->items) . "\n";
    }
}

// Polymorphic usage
$order1 = new Order(
    new CreditCardProcessor('4111111111111111'),
    new StandardShipping()
);
$order1->addItem('Laptop', 999.99)->addItem('Mouse', 25.99)->checkout();

echo "\n---\n\n";

$order2 = new Order(
    new CreditCardProcessor('4111111111111111'),
    new ExpressShipping()
);
$order2->addItem('Phone', 799.99)->checkout();
?>
```

---

## Cross-References

- **Related Topic: [Method Overriding](15-function-overriding.md)** - Overriding methods polymorphically
- **Related Topic: [Interfaces](23-interfaces.md)** - Interface contracts
- **Related Topic: [Abstract Classes](20-abstract-classes.md)** - Abstract class polymorphism
- **Related Topic: [Inheritance Basics](11-inheritance.md)** - Inheritance foundation
- **Related Topic: [Type Checking](19-type-checking-casting.md)** - Type validation
