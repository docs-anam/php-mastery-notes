# Abstract Methods in PHP

## Table of Contents
1. [Overview](#overview)
2. [Abstract Method Basics](#abstract-method-basics)
3. [Method Contracts](#method-contracts)
4. [Signature Compatibility](#signature-compatibility)
5. [Access Modifiers](#access-modifiers)
6. [Type Declarations](#type-declarations)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

Abstract methods define a contract that subclasses must fulfill. They have no implementation in the abstract class—only a signature. When a class inherits from a class with abstract methods, it must implement all abstract methods or itself be declared abstract. Abstract methods enforce a consistent interface across related classes while allowing each subclass to provide its own specific implementation.

**Key Concepts:**
- No implementation, only signature
- Subclass must implement or be abstract
- Define interface contract
- Can have access modifiers
- Support type declarations
- Enable polymorphic behavior

---

## Abstract Method Basics

### Simple Abstract Methods

```php
<?php
abstract class Shape {
    // Abstract method - no body
    abstract public function getArea();
    
    // Another abstract method
    abstract public function getPerimeter();
}

// Implementing subclass
class Circle extends Shape {
    private $radius;
    
    public function __construct($radius) {
        $this->radius = $radius;
    }
    
    // Must implement abstract method
    public function getArea() {
        return pi() * $this->radius ** 2;
    }
    
    // Must implement second abstract method
    public function getPerimeter() {
        return 2 * pi() * $this->radius;
    }
}

$circle = new Circle(5);
echo round($circle->getArea(), 2) . "\n";         // 78.54
echo round($circle->getPerimeter(), 2) . "\n";    // 31.42

// Cannot instantiate abstract class
// $shape = new Shape();  // Fatal error
?>
```

### Multiple Abstract Methods

```php
<?php
abstract class Document {
    abstract public function validate();
    abstract public function save();
    abstract public function load($id);
    abstract public function delete($id);
    abstract public function getContent();
}

class Article extends Document {
    private $title;
    private $content;
    private $id;
    
    public function validate() {
        return !empty($this->title) && !empty($this->content);
    }
    
    public function save() {
        // Implementation to save article
        return "Article saved";
    }
    
    public function load($id) {
        $this->id = $id;
        // Load from storage
        return "Article loaded";
    }
    
    public function delete($id) {
        // Delete from storage
        return "Article deleted";
    }
    
    public function getContent() {
        return ['title' => $this->title, 'content' => $this->content];
    }
}

// All abstract methods must be implemented
$article = new Article();
echo $article->validate() . "\n";
echo $article->save() . "\n";
?>
```

---

## Method Contracts

### Contract Definition

```php
<?php
// Abstract class defines contract
abstract class PaymentGateway {
    // Abstract methods - contract for subclasses
    abstract public function authorize($amount, $token);
    abstract public function capture($transactionId, $amount);
    abstract public function refund($transactionId);
    abstract public function getStatus($transactionId);
}

// Each payment gateway implements the same contract
class StripePaymentGateway extends PaymentGateway {
    public function authorize($amount, $token) {
        return "Stripe: Authorized {$amount}";
    }
    
    public function capture($transactionId, $amount) {
        return "Stripe: Captured {$amount}";
    }
    
    public function refund($transactionId) {
        return "Stripe: Refunded transaction";
    }
    
    public function getStatus($transactionId) {
        return "Stripe: Transaction status";
    }
}

class PayPalPaymentGateway extends PaymentGateway {
    public function authorize($amount, $token) {
        return "PayPal: Authorized {$amount}";
    }
    
    public function capture($transactionId, $amount) {
        return "PayPal: Captured {$amount}";
    }
    
    public function refund($transactionId) {
        return "PayPal: Refunded transaction";
    }
    
    public function getStatus($transactionId) {
        return "PayPal: Transaction status";
    }
}

// Code works with any gateway implementing the contract
function processPayment(PaymentGateway $gateway, $amount) {
    echo $gateway->authorize($amount, 'token') . "\n";
    echo $gateway->capture('txn123', $amount) . "\n";
}

processPayment(new StripePaymentGateway(), 100);
processPayment(new PayPalPaymentGateway(), 100);
?>
```

### Polymorphic Behavior via Contracts

```php
<?php
abstract class DataStore {
    abstract public function get($key);
    abstract public function set($key, $value);
    abstract public function has($key): bool;
    abstract public function remove($key): bool;
}

class MemoryStore extends DataStore {
    private $data = [];
    
    public function get($key) {
        return $this->data[$key] ?? null;
    }
    
    public function set($key, $value) {
        $this->data[$key] = $value;
    }
    
    public function has($key): bool {
        return isset($this->data[$key]);
    }
    
    public function remove($key): bool {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
            return true;
        }
        return false;
    }
}

class FileStore extends DataStore {
    private $directory;
    
    public function __construct($directory) {
        $this->directory = $directory;
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
    
    public function get($key) {
        $file = $this->directory . '/' . $key . '.json';
        if (file_exists($file)) {
            return json_decode(file_get_contents($file), true);
        }
        return null;
    }
    
    public function set($key, $value) {
        $file = $this->directory . '/' . $key . '.json';
        file_put_contents($file, json_encode($value));
    }
    
    public function has($key): bool {
        $file = $this->directory . '/' . $key . '.json';
        return file_exists($file);
    }
    
    public function remove($key): bool {
        $file = $this->directory . '/' . $key . '.json';
        if (file_exists($file)) {
            unlink($file);
            return true;
        }
        return false;
    }
}

// Usage - same interface, different implementations
$memory = new MemoryStore();
$memory->set('user:1', ['name' => 'John']);
echo $memory->get('user:1')['name'] . "\n";  // John

$file = new FileStore('/tmp/store');
$file->set('product:1', ['name' => 'Laptop', 'price' => 999]);
echo $file->get('product:1')['price'] . "\n";  // 999
?>
```

---

## Signature Compatibility

### Parameter Compatibility (Contravariance)

```php
<?php
abstract class Logger {
    // Abstract method with specific parameter type
    abstract public function log(string $message);
}

class ConsoleLogger extends Logger {
    // Correct - same parameter type
    public function log(string $message) {
        echo "[CONSOLE] $message\n";
    }
}

class DatabaseLogger extends Logger {
    // Can accept more general type (array, which includes string-like)
    // This is contravariance - accepting broader types
    public function log($message) {
        // Handles any type
        if (is_array($message)) {
            echo "[DB] Array: " . json_encode($message) . "\n";
        } else {
            echo "[DB] $message\n";
        }
    }
}

$console = new ConsoleLogger();
$console->log("Error occurred");

$db = new DatabaseLogger();
$db->log("Error occurred");
$db->log(['type' => 'error', 'code' => 500]);
?>
```

### Return Type Compatibility (Covariance)

```php
<?php
abstract class Repository {
    // Abstract method returns parent type
    abstract public function find($id): ?object;
}

class User {}
class Admin extends User {}

class UserRepository extends Repository {
    // Returns same type - valid
    public function find($id): ?User {
        return new User();
    }
}

class AdminRepository extends Repository {
    // Returns more specific type (Admin) - valid covariance
    public function find($id): ?Admin {
        return new Admin();
    }
}

$userRepo = new UserRepository();
$user = $userRepo->find(1);  // Returns User

$adminRepo = new AdminRepository();
$admin = $adminRepo->find(1);  // Returns Admin (more specific)
?>
```

---

## Access Modifiers

### Protected Abstract Methods

```php
<?php
abstract class BaseClass {
    protected $data;
    
    // Protected abstract method - only accessible to subclasses
    abstract protected function processData();
    
    public function getData() {
        return $this->data;
    }
    
    // Can call protected abstract method from concrete method
    final public function execute() {
        $this->processData();
        return $this->data;
    }
}

class ConcreteClass extends BaseClass {
    public function __construct(array $data) {
        $this->data = $data;
    }
    
    // Implement protected abstract method
    protected function processData() {
        // Process data
        $this->data = array_map(fn($v) => $v * 2, $this->data);
    }
}

$obj = new ConcreteClass([1, 2, 3]);
echo implode(',', $obj->execute()) . "\n";  // 2,4,6
?>
```

### Private vs Abstract

```php
<?php
abstract class Template {
    // Cannot be abstract AND private - no point
    // Would be private - inaccessible to subclasses
    
    // Protected - accessible to subclasses
    abstract protected function step1();
    abstract protected function step2();
    
    // Public - part of public interface
    abstract public function execute();
    
    // Concrete protected helper
    final protected function helper() {
        return "Helper";
    }
}

class Implementation extends Template {
    protected function step1() {
        echo "Step 1\n";
    }
    
    protected function step2() {
        echo "Step 2\n";
        echo $this->helper() . "\n";
    }
    
    public function execute() {
        $this->step1();
        $this->step2();
    }
}

$impl = new Implementation();
$impl->execute();  // Step 1, Step 2, Helper
?>
```

---

## Type Declarations

### Return Type Hints

```php
<?php
abstract class DataProcessor {
    // Must return array
    abstract public function process(): array;
    
    // Must return string
    abstract public function stringify(): string;
    
    // Can return null
    abstract public function validate(): ?bool;
}

class JSONProcessor extends DataProcessor {
    private $data;
    
    public function __construct($jsonString) {
        $this->data = json_decode($jsonString, true) ?? [];
    }
    
    public function process(): array {
        return $this->data;
    }
    
    public function stringify(): string {
        return json_encode($this->data);
    }
    
    public function validate(): ?bool {
        return !empty($this->data);
    }
}

$processor = new JSONProcessor('{"name":"John","age":30}');
print_r($processor->process());
echo $processor->stringify() . "\n";
echo $processor->validate() ? "Valid\n" : "Invalid\n";
?>
```

### Parameter Type Hints

```php
<?php
abstract class RequestHandler {
    abstract public function handle(array $params): bool;
    abstract public function validate(string $input): bool;
    abstract public function process(int $id, ?string $data): array;
}

class UserHandler extends RequestHandler {
    public function handle(array $params): bool {
        return !empty($params);
    }
    
    public function validate(string $input): bool {
        return strlen($input) > 0;
    }
    
    public function process(int $id, ?string $data): array {
        return [
            'id' => $id,
            'data' => $data,
            'processed' => true
        ];
    }
}

$handler = new UserHandler();
print_r($handler->process(1, "test"));
?>
```

---

## Practical Examples

### Event System with Abstract Handlers

```php
<?php
abstract class EventHandler {
    protected $eventName;
    
    public function __construct($eventName) {
        $this->eventName = $eventName;
    }
    
    abstract public function handle(array $eventData);
    abstract public function supports($eventName): bool;
}

class UserCreatedHandler extends EventHandler {
    public function handle(array $eventData) {
        echo "Handling user creation event\n";
        echo "User: " . $eventData['email'] . "\n";
        // Send welcome email, etc.
    }
    
    public function supports($eventName): bool {
        return $eventName === 'user.created';
    }
}

class OrderCreatedHandler extends EventHandler {
    public function handle(array $eventData) {
        echo "Handling order creation event\n";
        echo "Order total: " . $eventData['total'] . "\n";
        // Update inventory, etc.
    }
    
    public function supports($eventName): bool {
        return $eventName === 'order.created';
    }
}

class EventDispatcher {
    private $handlers = [];
    
    public function register(EventHandler $handler) {
        $this->handlers[] = $handler;
    }
    
    public function dispatch($eventName, array $eventData) {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($eventName)) {
                $handler->handle($eventData);
            }
        }
    }
}

$dispatcher = new EventDispatcher();
$dispatcher->register(new UserCreatedHandler('user.created'));
$dispatcher->register(new OrderCreatedHandler('order.created'));

$dispatcher->dispatch('user.created', ['email' => 'john@example.com']);
$dispatcher->dispatch('order.created', ['total' => 99.99]);
?>
```

---

## Common Mistakes

### 1. Missing Implementation

```php
<?php
// ❌ Wrong: Abstract method not implemented
abstract class Parent1 {
    abstract public function test();
}

class Child1 extends Parent1 {
    // Forgot to implement test()
}

// Error: Method test must be declared or class made abstract
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

### 2. Wrong Method Signature

```php
<?php
// ❌ Wrong: Different parameter list
abstract class Parent1 {
    abstract public function process($id);
}

class Child1 extends Parent1 {
    public function process($id, $name) {  // Wrong signature
        return "Processed";
    }
}

// ✓ Correct: Match exact signature
class Child2 extends Parent1 {
    public function process($id) {
        return "Processed";
    }
}
?>
```

### 3. Wrong Return Type

```php
<?php
// ❌ Wrong: Return type doesn't match
abstract class Parent1 {
    abstract public function getData(): array;
}

class Child1 extends Parent1 {
    public function getData(): string {  // Should return array
        return "data";
    }
}

// ✓ Correct: Match return type
class Child2 extends Parent1 {
    public function getData(): array {
        return ['data'];
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// Queue System with Abstract Methods

abstract class Queue {
    protected $items = [];
    
    abstract public function enqueue($item);
    abstract public function dequeue();
    abstract public function peek();
    abstract public function isEmpty(): bool;
    abstract public function getSize(): int;
}

// FIFO Queue
class FIFOQueue extends Queue {
    public function enqueue($item) {
        $this->items[] = $item;
        return $this;
    }
    
    public function dequeue() {
        if ($this->isEmpty()) {
            return null;
        }
        return array_shift($this->items);
    }
    
    public function peek() {
        return $this->items[0] ?? null;
    }
    
    public function isEmpty(): bool {
        return empty($this->items);
    }
    
    public function getSize(): int {
        return count($this->items);
    }
}

// LIFO Queue (Stack)
class LIFOQueue extends Queue {
    public function enqueue($item) {
        array_push($this->items, $item);
        return $this;
    }
    
    public function dequeue() {
        if ($this->isEmpty()) {
            return null;
        }
        return array_pop($this->items);
    }
    
    public function peek() {
        return end($this->items);
    }
    
    public function isEmpty(): bool {
        return empty($this->items);
    }
    
    public function getSize(): int {
        return count($this->items);
    }
}

// Priority Queue
class PriorityQueue extends Queue {
    public function enqueue($item, $priority = 0) {
        $this->items[] = ['item' => $item, 'priority' => $priority];
        usort($this->items, function($a, $b) {
            return $b['priority'] - $a['priority'];
        });
        return $this;
    }
    
    public function dequeue() {
        if ($this->isEmpty()) {
            return null;
        }
        $first = array_shift($this->items);
        return $first['item'];
    }
    
    public function peek() {
        if ($this->isEmpty()) {
            return null;
        }
        return $this->items[0]['item'];
    }
    
    public function isEmpty(): bool {
        return empty($this->items);
    }
    
    public function getSize(): int {
        return count($this->items);
    }
}

// Usage
echo "FIFO Queue:\n";
$fifo = new FIFOQueue();
$fifo->enqueue('First')->enqueue('Second')->enqueue('Third');
echo $fifo->dequeue() . "\n";  // First
echo $fifo->dequeue() . "\n";  // Second

echo "\nLIFO Queue (Stack):\n";
$lifo = new LIFOQueue();
$lifo->enqueue('First')->enqueue('Second')->enqueue('Third');
echo $lifo->dequeue() . "\n";  // Third
echo $lifo->dequeue() . "\n";  // Second

echo "\nPriority Queue:\n";
$priority = new PriorityQueue();
$priority->enqueue('Low priority', 1);
$priority->enqueue('High priority', 10);
$priority->enqueue('Medium priority', 5);
echo $priority->dequeue() . "\n";  // High priority
echo $priority->dequeue() . "\n";  // Medium priority
?>
```

---

## Cross-References

- **Related Topic: [Abstract Classes](20-abstract-classes.md)** - Defining abstract methods
- **Related Topic: [Method Overriding](15-function-overriding.md)** - Implementing abstract methods
- **Related Topic: [Interfaces](23-interfaces.md)** - Similar to abstract methods in interfaces
- **Related Topic: [Polymorphism](18-polymorphism.md)** - Polymorphic behavior through abstract methods
- **Related Topic: [Type Checking](19-type-checking-casting.md)** - Type hints in abstract methods
