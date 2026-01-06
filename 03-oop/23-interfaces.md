# Interfaces in PHP

## Table of Contents
1. [Overview](#overview)
2. [Interface Basics](#interface-basics)
3. [Implementing Interfaces](#implementing-interfaces)
4. [Interface Contracts](#interface-contracts)
5. [Multiple Interfaces](#multiple-interfaces)
6. [Extending Interfaces](#extending-interfaces)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

Interfaces define contracts that classes must follow. They specify what methods a class must implement, but not how. An interface cannot be instantiated; it only declares method signatures. Interfaces enforce a consistent API across unrelated classes, enabling polymorphism. Unlike abstract classes, interfaces cannot contain properties or concrete method implementations (though PHP 8.1+ allows constants and static methods).

**Key Concepts:**
- Define method contracts
- Cannot be instantiated
- No properties (except constants)
- Classes implement interfaces
- Multiple interface implementation
- Interface inheritance
- Enable duck typing with contracts

---

## Interface Basics

### Declaring Interfaces

```php
<?php
// Define an interface
interface Drawable {
    public function draw();
    public function erase();
}

// Implement interface
class Circle implements Drawable {
    private $radius;
    
    public function __construct($radius) {
        $this->radius = $radius;
    }
    
    // Must implement all interface methods
    public function draw() {
        echo "Drawing circle with radius {$this->radius}\n";
    }
    
    public function erase() {
        echo "Erasing circle\n";
    }
}

class Rectangle implements Drawable {
    private $width;
    private $height;
    
    public function __construct($width, $height) {
        $this->width = $width;
        $this->height = $height;
    }
    
    public function draw() {
        echo "Drawing rectangle {$this->width}x{$this->height}\n";
    }
    
    public function erase() {
        echo "Erasing rectangle\n";
    }
}

// Usage - polymorphic
$shapes = [new Circle(5), new Rectangle(4, 6)];

foreach ($shapes as $shape) {
    $shape->draw();
    $shape->erase();
}

// Cannot instantiate interface
// $drawable = new Drawable();  // Fatal error
?>
```

### Interface Type Hinting

```php
<?php
interface Logger {
    public function log($message);
}

class ConsoleLogger implements Logger {
    public function log($message) {
        echo "[Console] $message\n";
    }
}

class FileLogger implements Logger {
    private $filename;
    
    public function __construct($filename) {
        $this->filename = $filename;
    }
    
    public function log($message) {
        file_put_contents($this->filename, "$message\n", FILE_APPEND);
    }
}

// Type hint to interface - works with any implementation
function logEvent(Logger $logger, $event) {
    $logger->log("Event: $event");
}

logEvent(new ConsoleLogger(), "User login");
logEvent(new FileLogger('/tmp/app.log'), "User logout");
?>
```

---

## Implementing Interfaces

### Single Interface

```php
<?php
interface Serializable {
    public function serialize();
    public function unserialize($data);
}

class User implements Serializable {
    private $name;
    private $email;
    
    public function __construct($name, $email) {
        $this->name = $name;
        $this->email = $email;
    }
    
    public function serialize() {
        return json_encode([
            'name' => $this->name,
            'email' => $this->email
        ]);
    }
    
    public function unserialize($data) {
        $arr = json_decode($data, true);
        $this->name = $arr['name'];
        $this->email = $arr['email'];
    }
}

$user = new User("John", "john@example.com");
$json = $user->serialize();
echo $json . "\n";  // {"name":"John","email":"john@example.com"}

$user2 = new User("Jane", "jane@example.com");
$user2->unserialize($json);
?>
```

### Interface with Type Hints

```php
<?php
interface Repository {
    public function find($id): ?array;
    public function findAll(): array;
    public function save(array $data): bool;
    public function delete($id): bool;
}

class UserRepository implements Repository {
    private $users = [];
    
    public function find($id): ?array {
        return $this->users[$id] ?? null;
    }
    
    public function findAll(): array {
        return $this->users;
    }
    
    public function save(array $data): bool {
        $this->users[$data['id']] = $data;
        return true;
    }
    
    public function delete($id): bool {
        if (isset($this->users[$id])) {
            unset($this->users[$id]);
            return true;
        }
        return false;
    }
}

$repo = new UserRepository();
$repo->save(['id' => 1, 'name' => 'John']);
$repo->save(['id' => 2, 'name' => 'Jane']);

print_r($repo->find(1));
print_r($repo->findAll());
?>
```

---

## Interface Contracts

### Contract Definition

```php
<?php
// Define payment gateway contract
interface PaymentGateway {
    public function authorize($amount, $token);
    public function capture($transactionId, $amount);
    public function refund($transactionId);
}

// Stripe implementation
class StripeGateway implements PaymentGateway {
    public function authorize($amount, $token) {
        return "Stripe: Authorized $amount with token";
    }
    
    public function capture($transactionId, $amount) {
        return "Stripe: Captured $amount for transaction";
    }
    
    public function refund($transactionId) {
        return "Stripe: Refunded transaction";
    }
}

// PayPal implementation
class PayPalGateway implements PaymentGateway {
    public function authorize($amount, $token) {
        return "PayPal: Authorized $amount with token";
    }
    
    public function capture($transactionId, $amount) {
        return "PayPal: Captured $amount for transaction";
    }
    
    public function refund($transactionId) {
        return "PayPal: Refunded transaction";
    }
}

// Payment processor accepts any gateway implementing contract
class PaymentProcessor {
    private $gateway;
    
    public function __construct(PaymentGateway $gateway) {
        $this->gateway = $gateway;
    }
    
    public function process($amount, $token) {
        $authResult = $this->gateway->authorize($amount, $token);
        echo $authResult . "\n";
        return $authResult;
    }
}

$stripe = new PaymentProcessor(new StripeGateway());
$stripe->process(100, 'token123');

$paypal = new PaymentProcessor(new PayPalGateway());
$paypal->process(100, 'token456');
?>
```

---

## Multiple Interfaces

### Implementing Multiple Interfaces

```php
<?php
interface Renderable {
    public function render();
}

interface Cacheable {
    public function getCache();
    public function setCache($data);
}

interface Sortable {
    public function compare($other);
}

// Class implementing three interfaces
class Product implements Renderable, Cacheable, Sortable {
    private $name;
    private $price;
    private $cache;
    
    public function __construct($name, $price) {
        $this->name = $name;
        $this->price = $price;
    }
    
    // Renderable implementation
    public function render() {
        return "<product>{$this->name}: \${$this->price}</product>";
    }
    
    // Cacheable implementation
    public function getCache() {
        return $this->cache;
    }
    
    public function setCache($data) {
        $this->cache = $data;
    }
    
    // Sortable implementation
    public function compare($other) {
        return $this->price <=> $other->price;
    }
}

// Check interfaces
$product = new Product("Laptop", 999.99);

echo ($product instanceof Renderable) ? "Is Renderable\n" : "";
echo ($product instanceof Cacheable) ? "Is Cacheable\n" : "";
echo ($product instanceof Sortable) ? "Is Sortable\n" : "";

echo $product->render() . "\n";
?>
```

### Interface Segregation Principle

```php
<?php
// Bad: One large interface
// interface Worker {
//     public function work();
//     public function eat();  // Not all workers eat!
// }

// Good: Segregated interfaces
interface Worker {
    public function work();
}

interface Eatable {
    public function eat();
}

// Robot only implements Worker
class Robot implements Worker {
    public function work() {
        return "Robot is working";
    }
}

// Human implements both
class Human implements Worker, Eatable {
    public function work() {
        return "Human is working";
    }
    
    public function eat() {
        return "Human is eating";
    }
}

$robot = new Robot();
echo $robot->work() . "\n";

$human = new Human();
echo $human->work() . "\n";
echo $human->eat() . "\n";
?>
```

---

## Extending Interfaces

### Interface Inheritance

```php
<?php
// Base interface
interface Animal {
    public function move();
    public function makeSound();
}

// Extended interface
interface Flyable extends Animal {
    public function fly();
    public function land();
}

// Implement extended interface
class Bird implements Flyable {
    public function move() {
        return "Walking";
    }
    
    public function makeSound() {
        return "Chirping";
    }
    
    public function fly() {
        return "Flying high";
    }
    
    public function land() {
        return "Landing safely";
    }
}

// Must implement all methods from interface hierarchy
$bird = new Bird();
echo $bird->move() . "\n";
echo $bird->fly() . "\n";
?>
```

### Multiple Interface Inheritance

```php
<?php
interface Named {
    public function getName();
}

interface Dated {
    public function getDate();
}

interface Identified {
    public function getId();
}

// Interface extending multiple interfaces
interface Entity extends Named, Dated, Identified {
    public function validate();
}

class Post implements Entity {
    private $id;
    private $title;
    private $date;
    
    public function __construct($id, $title, $date) {
        $this->id = $id;
        $this->title = $title;
        $this->date = $date;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getName() {
        return $this->title;
    }
    
    public function getDate() {
        return $this->date;
    }
    
    public function validate() {
        return !empty($this->title) && !empty($this->date);
    }
}

$post = new Post(1, "Post Title", date('Y-m-d'));
echo $post->getName() . "\n";
echo $post->getDate() . "\n";
echo $post->validate() ? "Valid\n" : "Invalid\n";
?>
```

---

## Practical Examples

### Plugin System

```php
<?php
interface Plugin {
    public function getName();
    public function getVersion();
    public function activate();
    public function deactivate();
}

class AuthPlugin implements Plugin {
    public function getName() {
        return "Authentication Plugin";
    }
    
    public function getVersion() {
        return "1.0.0";
    }
    
    public function activate() {
        echo "Auth plugin activated\n";
    }
    
    public function deactivate() {
        echo "Auth plugin deactivated\n";
    }
}

class CachePlugin implements Plugin {
    public function getName() {
        return "Cache Plugin";
    }
    
    public function getVersion() {
        return "2.1.0";
    }
    
    public function activate() {
        echo "Cache plugin activated\n";
    }
    
    public function deactivate() {
        echo "Cache plugin deactivated\n";
    }
}

class PluginManager {
    private $plugins = [];
    
    public function register(Plugin $plugin) {
        $this->plugins[] = $plugin;
    }
    
    public function activateAll() {
        foreach ($this->plugins as $plugin) {
            echo "Loading: " . $plugin->getName() . " v" . $plugin->getVersion() . "\n";
            $plugin->activate();
        }
    }
    
    public function deactivateAll() {
        foreach ($this->plugins as $plugin) {
            $plugin->deactivate();
        }
    }
}

$manager = new PluginManager();
$manager->register(new AuthPlugin());
$manager->register(new CachePlugin());
$manager->activateAll();
?>
```

---

## Common Mistakes

### 1. Forgetting to Implement Methods

```php
<?php
// ❌ Wrong: Missing interface method
interface Saveable {
    public function save();
    public function load();
}

class Data implements Saveable {
    public function save() {
        // Missing load() implementation!
    }
}

// Error: Class Data contains 1 abstract method
// $data = new Data();

// ✓ Correct: Implement all methods
class Data implements Saveable {
    public function save() {
        return "Saved";
    }
    
    public function load() {
        return "Loaded";
    }
}
?>
```

### 2. Wrong Method Signature

```php
<?php
// ❌ Wrong: Different method signature
interface Processor {
    public function process($data): array;
}

class DataProcessor implements Processor {
    public function process($data, $options): array {  // Extra parameter
        return [];
    }
}

// ✓ Correct: Match interface signature
class DataProcessor implements Processor {
    public function process($data): array {
        return [];
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// E-Commerce Product System

interface Product {
    public function getName();
    public function getPrice();
    public function getDescription();
    public function isAvailable();
}

interface Shippable {
    public function getWeight();
    public function getDimensions();
    public function canShip($country);
}

interface Discountable {
    public function applyDiscount($percent);
    public function getFinalPrice();
}

class PhysicalProduct implements Product, Shippable, Discountable {
    private $name;
    private $price;
    private $description;
    private $available;
    private $weight;
    private $width;
    private $height;
    private $depth;
    private $discount = 0;
    
    public function __construct($name, $price, $weight, $width, $height, $depth) {
        $this->name = $name;
        $this->price = $price;
        $this->weight = $weight;
        $this->width = $width;
        $this->height = $height;
        $this->depth = $depth;
        $this->available = true;
        $this->description = "Physical product";
    }
    
    // Product interface
    public function getName() {
        return $this->name;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function isAvailable() {
        return $this->available;
    }
    
    // Shippable interface
    public function getWeight() {
        return $this->weight;
    }
    
    public function getDimensions() {
        return "{$this->width}x{$this->height}x{$this->depth}";
    }
    
    public function canShip($country) {
        // Business logic for shipping
        return $country !== 'Restricted';
    }
    
    // Discountable interface
    public function applyDiscount($percent) {
        $this->discount = $percent;
        return $this;
    }
    
    public function getFinalPrice() {
        return $this->price * (1 - $this->discount / 100);
    }
}

class DigitalProduct implements Product, Discountable {
    private $name;
    private $price;
    private $description;
    private $available;
    private $downloadUrl;
    private $discount = 0;
    
    public function __construct($name, $price, $downloadUrl) {
        $this->name = $name;
        $this->price = $price;
        $this->downloadUrl = $downloadUrl;
        $this->available = true;
        $this->description = "Digital product";
    }
    
    // Product interface
    public function getName() {
        return $this->name;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function isAvailable() {
        return $this->available;
    }
    
    // Discountable interface
    public function applyDiscount($percent) {
        $this->discount = $percent;
        return $this;
    }
    
    public function getFinalPrice() {
        return $this->price * (1 - $this->discount / 100);
    }
    
    public function getDownloadUrl() {
        return $this->downloadUrl;
    }
}

// Usage
$laptop = new PhysicalProduct("Laptop", 999.99, 2.5, 35, 24, 2);
$ebook = new DigitalProduct("PHP Guide", 29.99, "https://example.com/php-guide.pdf");

echo "Physical Product:\n";
echo $laptop->getName() . "\n";
echo "Price: \$" . $laptop->getPrice() . "\n";
echo "Weight: " . $laptop->getWeight() . " kg\n";
echo "Can ship to US: " . ($laptop->canShip('US') ? "Yes" : "No") . "\n";

$laptop->applyDiscount(10);
echo "After 10% discount: \$" . $laptop->getFinalPrice() . "\n";

echo "\nDigital Product:\n";
echo $ebook->getName() . "\n";
echo "Price: \$" . $ebook->getPrice() . "\n";
$ebook->applyDiscount(20);
echo "After 20% discount: \$" . $ebook->getFinalPrice() . "\n";
?>
```

---

## Cross-References

- **Related Topic: [Polymorphism](18-polymorphism.md)** - Polymorphic behavior with interfaces
- **Related Topic: [Abstract Classes](20-abstract-classes.md)** - Alternative to interfaces
- **Related Topic: [Type Checking](19-type-checking-casting.md)** - Instanceof with interfaces
- **Related Topic: [Interface Inheritance](24-interface-inheritance.md)** - Extending interfaces
- **Related Topic: [Multiple Interfaces](23-interfaces.md)** - Implementing multiple interfaces
