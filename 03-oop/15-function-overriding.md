# Function Overriding in PHP

## Table of Contents
1. [Overview](#overview)
2. [Basic Method Overriding](#basic-method-overriding)
3. [Signature Compatibility](#signature-compatibility)
4. [Return Type Covariance](#return-type-covariance)
5. [Parameter Type Contravariance](#parameter-type-contravariance)
6. [Calling Parent Methods](#calling-parent-methods)
7. [Abstract Methods](#abstract-methods)
8. [Practical Examples](#practical-examples)
9. [Common Mistakes](#common-mistakes)
10. [Complete Working Example](#complete-working-example)
11. [Cross-References](#cross-references)

---

## Overview

Method overriding is the ability of a subclass to provide a specific implementation of a method that is already declared in its parent class. When an overridden method is called through the parent class reference or the child class instance, the child's version executes. This is a core principle of polymorphism.

**Key Concepts:**
- Subclass provides new implementation of parent method
- Signature must be compatible
- Enables polymorphic behavior
- Uses `parent::` to call parent version
- Return types must be compatible (covariance)
- Parameter types must be compatible (contravariance)

---

## Basic Method Overriding

### Simple Override

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
    // Override the speak method
    public function speak() {
        return "Woof! Woof!";
    }
    
    // Override the move method
    public function move() {
        return "Running on four legs";
    }
}

class Bird extends Animal {
    public function speak() {
        return "Tweet! Tweet!";
    }
    
    public function move() {
        return "Flying in the sky";
    }
}

$animal = new Animal();
$dog = new Dog();
$bird = new Bird();

echo $animal->speak();  // Some sound
echo $dog->speak();     // Woof! Woof!
echo $bird->speak();    // Tweet! Tweet!
?>
```

### Override with Different Behavior

```php
<?php
class Shape {
    protected $color;
    
    public function __construct($color) {
        $this->color = $color;
    }
    
    public function describe() {
        return "This is a {$this->color} shape";
    }
    
    public function getArea() {
        return 0;
    }
}

class Circle extends Shape {
    protected $radius;
    
    public function __construct($color, $radius) {
        parent::__construct($color);
        $this->radius = $radius;
    }
    
    // Override with specific implementation
    public function describe() {
        return parent::describe() . " - Circle with radius {$this->radius}";
    }
    
    public function getArea() {
        return pi() * $this->radius ** 2;
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
    
    public function describe() {
        return parent::describe() . " - Rectangle {$this->width}x{$this->height}";
    }
    
    public function getArea() {
        return $this->width * $this->height;
    }
}

$circle = new Circle('Red', 5);
echo $circle->describe();   // This is a Red shape - Circle with radius 5
echo $circle->getArea();    // ~78.54

$rect = new Rectangle('Blue', 10, 20);
echo $rect->describe();     // This is a Blue shape - Rectangle 10x20
echo $rect->getArea();      // 200
?>
```

---

## Signature Compatibility

### Matching Parameter Count

```php
<?php
class PaymentProcessor {
    public function process($amount) {
        return "Processing: \$$amount";
    }
}

class CreditCardProcessor extends PaymentProcessor {
    // ✓ Override with same signature
    public function process($amount) {
        $fee = $amount * 0.03;  // 3% fee
        return "Processing credit card: \$" . ($amount + $fee);
    }
}

$processor = new CreditCardProcessor();
echo $processor->process(100);  // Processing credit card: $103
?>
```

### Parameter Types Must Match

```php
<?php
class Logger {
    public function log($message) {
        echo "Logging: $message\n";
    }
}

class DatabaseLogger extends Logger {
    // ✓ Correct: Same parameter type (implicit string)
    public function log($message) {
        // Add database logging
        echo "DB: $message\n";
    }
}

// With type hints - must match exactly
class TypedLogger {
    public function log(string $message): void {
        echo "Logging: $message\n";
    }
}

class TypedDatabaseLogger extends TypedLogger {
    // ✓ Can make parameter less strict (contravariance)
    public function log($message): void {  // Less strict
        echo "DB: $message\n";
    }
}
?>
```

### Matching Visibility

```php
<?php
class BaseService {
    public function publish() {
        return "Publishing...";
    }
}

class ExtendedService extends BaseService {
    // ✓ Can increase visibility from public
    public function publish() {
        return "Extended publishing...";
    }
}

// Reducing visibility would be error (in strict mode)
class RestrictedService extends BaseService {
    // ✗ Would be error: protected function publish()
    // ✓ Must stay public or increase visibility
}
?>
```

---

## Return Type Covariance

### Compatible Return Types (PHP 7.4+)

```php
<?php
class DataRepository {
    public function findById($id) {
        return ['id' => $id, 'name' => 'Item'];
    }
}

class UserRepository extends DataRepository {
    // ✓ Can return more specific type (covariance)
    public function findById($id): array {
        return ['id' => $id, 'name' => 'User', 'role' => 'admin'];
    }
}

$repo = new UserRepository();
$result = $repo->findById(1);
print_r($result);
?>
```

### Return Type Narrowing

```php
<?php
interface AnimalInterface {
    public function create(): Animal;
}

class Animal {}
class Dog extends Animal {}

// ✓ Can return subclass (more specific)
class DogFactory implements AnimalInterface {
    public function create(): Dog {
        return new Dog();
    }
}

$factory = new DogFactory();
$dog = $factory->create();
?>
```

---

## Parameter Type Contravariance

### Accept Broader Types (PHP 7.4+)

```php
<?php
class Vehicle {
    public function refuel(Car $car) {
        echo "Refueling car\n";
    }
}

class UniversalStation extends Vehicle {
    // ✓ Can accept parent type (contravariance)
    public function refuel(Vehicle $vehicle) {
        echo "Refueling any vehicle\n";
    }
}

class Car extends Vehicle {}

$station = new UniversalStation();
$station->refuel(new Car());  // Accepts Car
// $station->refuel(new Vehicle());  // Also accepts Vehicle
?>
```

---

## Calling Parent Methods

### Using parent:: Keyword

```php
<?php
class FormValidator {
    public function validate($data) {
        if (empty($data)) {
            return "Data required";
        }
        return $this->validateFormat($data);
    }
    
    protected function validateFormat($data) {
        return "Valid";
    }
}

class EmailValidator extends FormValidator {
    public function validate($data) {
        // Call parent's validation first
        $parentResult = parent::validate($data);
        
        if ($parentResult !== "Valid") {
            return $parentResult;
        }
        
        // Add specific email validation
        return filter_var($data, FILTER_VALIDATE_EMAIL) 
            ? "Valid email" 
            : "Invalid email format";
    }
}

$validator = new EmailValidator();
echo $validator->validate('john@example.com');  // Valid email
echo $validator->validate('john');              // Invalid email format
?>
```

### Chain of Responsibility

```php
<?php
class BaseRequestHandler {
    protected $nextHandler;
    
    public function handle($request) {
        if ($this->canProcess($request)) {
            return $this->process($request);
        }
        
        if ($this->nextHandler) {
            return $this->nextHandler->handle($request);
        }
        
        return "No handler found";
    }
    
    protected function canProcess($request) {
        return false;
    }
    
    protected function process($request) {
        return "Processing...";
    }
}

class AdminHandler extends BaseRequestHandler {
    protected function canProcess($request) {
        return $request['role'] === 'admin';
    }
    
    protected function process($request) {
        return "Admin access granted";
    }
}

class UserHandler extends BaseRequestHandler {
    protected function canProcess($request) {
        return $request['role'] === 'user';
    }
    
    protected function process($request) {
        return "User access granted";
    }
}

$admin = new AdminHandler();
$user = new UserHandler();
$admin->nextHandler = $user;

echo $admin->handle(['role' => 'admin']);   // Admin access granted
echo $admin->handle(['role' => 'user']);    // User access granted
?>
```

---

## Abstract Methods

### Forcing Implementation

```php
<?php
abstract class PaymentGateway {
    // Abstract method - must be implemented by subclass
    abstract public function charge($amount);
    
    abstract public function refund($transactionId);
    
    // Concrete method
    public function getGatewayName() {
        return static::class;
    }
}

class PayPalGateway extends PaymentGateway {
    public function charge($amount) {
        return "Charged \$$amount via PayPal";
    }
    
    public function refund($transactionId) {
        return "Refunded transaction $transactionId via PayPal";
    }
}

class StripeGateway extends PaymentGateway {
    public function charge($amount) {
        return "Charged \$$amount via Stripe";
    }
    
    public function refund($transactionId) {
        return "Refunded transaction $transactionId via Stripe";
    }
}

$paypal = new PayPalGateway();
echo $paypal->charge(100);      // Charged $100 via PayPal

$stripe = new StripeGateway();
echo $stripe->charge(100);      // Charged $100 via Stripe
?>
```

---

## Practical Examples

### Plugin System Architecture

```php
<?php
abstract class Plugin {
    protected $name;
    protected $version = '1.0.0';
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    abstract public function activate();
    abstract public function deactivate();
    abstract public function execute();
    
    public function getName() {
        return $this->name;
    }
    
    public function getVersion() {
        return $this->version;
    }
}

class CachePlugin extends Plugin {
    private $cache = [];
    
    public function activate() {
        echo "Cache plugin activated\n";
        return true;
    }
    
    public function deactivate() {
        $this->cache = [];
        echo "Cache plugin deactivated\n";
        return true;
    }
    
    public function execute() {
        echo "Cache executing: " . count($this->cache) . " items\n";
    }
}

class SecurityPlugin extends Plugin {
    public function activate() {
        echo "Security plugin activated\n";
        return true;
    }
    
    public function deactivate() {
        echo "Security plugin deactivated\n";
        return true;
    }
    
    public function execute() {
        echo "Security plugin scanning...\n";
    }
}

$plugins = [
    new CachePlugin('Cache Manager'),
    new SecurityPlugin('Security Scanner')
];

foreach ($plugins as $plugin) {
    $plugin->activate();
    $plugin->execute();
    $plugin->deactivate();
}
?>
```

### Database Adapters

```php
<?php
abstract class DatabaseAdapter {
    abstract public function connect($config);
    abstract public function query($sql);
    abstract public function insert($table, $data);
    abstract public function escape($value);
}

class MySQLAdapter extends DatabaseAdapter {
    private $connection;
    
    public function connect($config) {
        echo "Connecting to MySQL: {$config['host']}\n";
        $this->connection = true;
    }
    
    public function query($sql) {
        echo "MySQL executing: $sql\n";
        return [];
    }
    
    public function insert($table, $data) {
        echo "MySQL inserting into $table\n";
        return true;
    }
    
    public function escape($value) {
        return "'" . addslashes($value) . "'";
    }
}

class PostgreSQLAdapter extends DatabaseAdapter {
    private $connection;
    
    public function connect($config) {
        echo "Connecting to PostgreSQL: {$config['host']}\n";
        $this->connection = true;
    }
    
    public function query($sql) {
        echo "PostgreSQL executing: $sql\n";
        return [];
    }
    
    public function insert($table, $data) {
        echo "PostgreSQL inserting into $table\n";
        return true;
    }
    
    public function escape($value) {
        return "E'" . pg_escape_string($value) . "'";
    }
}

$db = new MySQLAdapter();
$db->connect(['host' => 'localhost']);
$db->query('SELECT * FROM users');
?>
```

---

## Common Mistakes

### 1. Changing Method Signature

```php
<?php
// ❌ Wrong: Changed parameter count
class Parent1 {
    public function calculate($a, $b) {
        return $a + $b;
    }
}

class Child1 extends Parent1 {
    // Different signature - not a true override
    public function calculate($a, $b, $c) {
        return $a + $b + $c;
    }
}

// ✓ Correct: Same signature or compatible
class Parent2 {
    public function calculate($a, $b) {
        return $a + $b;
    }
}

class Child2 extends Parent2 {
    public function calculate($a, $b) {
        return ($a + $b) * 2;
    }
}
?>
```

### 2. Forgetting to Call Parent

```php
<?php
// ❌ Wrong: Lost parent's important logic
class Logger {
    protected $logFile;
    
    public function log($message) {
        $this->initializeFile();
        echo "[$message]\n";
    }
    
    protected function initializeFile() {
        // Setup code
    }
}

class FileLogger extends Logger {
    public function log($message) {
        echo "File: $message\n";
        // Lost initializeFile() call!
    }
}

// ✓ Correct: Call parent when needed
class FileLogger extends Logger {
    public function log($message) {
        parent::log($message);  // Call parent
        // Additional file logging
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// Message Queue System with Method Overriding

abstract class MessageQueue {
    protected $messages = [];
    
    abstract public function enqueue($message);
    abstract public function dequeue();
    
    public function count() {
        return count($this->messages);
    }
    
    public function isEmpty() {
        return empty($this->messages);
    }
}

class LIFOQueue extends MessageQueue {
    // Last-In-First-Out (Stack)
    public function enqueue($message) {
        array_push($this->messages, $message);
        echo "Enqueued (LIFO): $message\n";
    }
    
    public function dequeue() {
        if (!$this->isEmpty()) {
            return array_pop($this->messages);
        }
        return null;
    }
}

class FIFOQueue extends MessageQueue {
    // First-In-First-Out (Queue)
    public function enqueue($message) {
        array_unshift($this->messages, $message);
        echo "Enqueued (FIFO): $message\n";
    }
    
    public function dequeue() {
        if (!$this->isEmpty()) {
            return array_pop($this->messages);
        }
        return null;
    }
}

class PriorityQueue extends MessageQueue {
    public function enqueue($message) {
        // Insert based on priority
        $this->messages[] = $message;
        usort($this->messages, function($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });
        echo "Enqueued (Priority): {$message['text']}\n";
    }
    
    public function dequeue() {
        if (!$this->isEmpty()) {
            return array_shift($this->messages);
        }
        return null;
    }
}

// Usage
$lifo = new LIFOQueue();
$lifo->enqueue('Message 1');
$lifo->enqueue('Message 2');
echo "Dequeued: " . $lifo->dequeue() . "\n";  // Message 2

$fifo = new FIFOQueue();
$fifo->enqueue('Task A');
$fifo->enqueue('Task B');
echo "Dequeued: " . $fifo->dequeue() . "\n";  // Task A

$priority = new PriorityQueue();
$priority->enqueue(['text' => 'Low', 'priority' => 1]);
$priority->enqueue(['text' => 'High', 'priority' => 10]);
?>
```

---

## Cross-References

- **Related Topic: [Inheritance Basics](11-inheritance.md)** - Foundation for overriding
- **Related Topic: [Polymorphism](18-polymorphism.md)** - Using overridden methods polymorphically
- **Related Topic: [Parent Keyword](16-parent-keyword.md)** - Calling parent methods
- **Related Topic: [Abstract Classes](20-abstract-class.md)** - Defining abstract methods
- **Related Topic: [Interfaces](23-interface.md)** - Interface implementation
