# The $this Keyword in PHP

## Table of Contents
1. [Overview](#overview)
2. [Accessing Properties](#accessing-properties)
3. [Accessing Methods](#accessing-methods)
4. [Using $this in Different Contexts](#using-this-in-different-contexts)
5. [$this vs Static](#this-vs-static)
6. [Method Chaining with $this](#method-chaining-with-this)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

`$this` is a pseudo-variable that refers to the current object instance. It's only available within non-static methods and represents the object on which the method is being called. `$this` allows you to access other methods and properties of the same object.

**Key Concepts:**
- `$this` is a reference to the current object
- Only available in instance methods (not static methods)
- Allows access to properties and methods of the current object
- Essential for object-oriented programming

---

## Accessing Properties

### Basic Property Access

```php
<?php
class Person {
    public $name;
    public $age;
    public $email;
    
    public function setInfo($name, $age, $email) {
        // Use $this to access object properties
        $this->name = $name;
        $this->age = $age;
        $this->email = $email;
    }
    
    public function getInfo() {
        return "{$this->name} ({$this->age} years old)";
    }
    
    public function displayDetails() {
        echo "Name: {$this->name}\n";
        echo "Age: {$this->age}\n";
        echo "Email: {$this->email}\n";
    }
}

$person = new Person();
$person->setInfo('Alice', 28, 'alice@example.com');
echo $person->getInfo();            // Alice (28 years old)
$person->displayDetails();          // Shows all details
?>
```

### Conditional Property Access

```php
<?php
class BankAccount {
    private $balance = 0;
    private $overdraftLimit = -500;
    
    public function deposit($amount) {
        if ($amount > 0) {
            $this->balance += $amount;
            return true;
        }
        return false;
    }
    
    public function withdraw($amount) {
        if ($amount > 0 && ($this->balance - $amount) >= $this->overdraftLimit) {
            $this->balance -= $amount;
            return true;
        }
        return false;
    }
    
    public function getBalance() {
        return $this->balance;
    }
    
    public function isOverdrawn() {
        return $this->balance < 0;  // Access property with $this
    }
}

$account = new BankAccount();
$account->deposit(1000);
$account->withdraw(1200);
echo "Balance: " . $account->getBalance();           // -200
echo "Overdrawn: " . ($account->isOverdrawn() ? 'Yes' : 'No');  // Yes
?>
```

### Accessing Dynamic Properties

```php
<?php
class DataHolder {
    private $data = [];
    
    public function set($key, $value) {
        $this->data[$key] = $value;
    }
    
    public function get($key) {
        return $this->data[$key] ?? null;
    }
    
    public function has($key) {
        return isset($this->data[$key]);
    }
    
    public function getAll() {
        return $this->data;
    }
}

$holder = new DataHolder();
$holder->set('username', 'john');
$holder->set('age', 30);

if ($holder->has('username')) {
    echo "Username: " . $holder->get('username') . "\n";
}

print_r($holder->getAll());
?>
```

---

## Accessing Methods

### Calling Other Methods

```php
<?php
class EmailSender {
    private $from = 'noreply@example.com';
    
    public function send($to, $subject, $body) {
        if ($this->validateEmail($to)) {
            return $this->sendEmail($to, $subject, $body);
        }
        return false;
    }
    
    private function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    private function sendEmail($to, $subject, $body) {
        echo "Sending email to: $to\n";
        echo "Subject: $subject\n";
        echo "From: {$this->from}\n";
        return true;
    }
}

$mailer = new EmailSender();
$mailer->send('user@example.com', 'Hello', 'This is a test');
?>
```

### Method Calling Within Constructor

```php
<?php
class Logger {
    private $logLevel = 'INFO';
    private $logs = [];
    
    public function __construct($level = 'INFO') {
        $this->logLevel = $level;
        $this->initialize();  // Call method from constructor
    }
    
    private function initialize() {
        $this->log('Logger initialized');
    }
    
    public function log($message) {
        $this->logs[] = "[{$this->logLevel}] $message";
    }
    
    public function getLogs() {
        return $this->logs;
    }
}

$logger = new Logger('DEBUG');
$logger->log('Application started');
print_r($logger->getLogs());
?>
```

### Chaining Method Calls with $this

```php
<?php
class StringBuilder {
    private $content = '';
    
    public function append($text): self {
        $this->content .= $text;
        return $this;
    }
    
    public function appendLine($text): self {
        $this->content .= $text . "\n";
        return $this;
    }
    
    public function prepend($text): self {
        $this->content = $text . $this->content;
        return $this;
    }
    
    public function get(): string {
        return $this->content;
    }
}

$builder = new StringBuilder();
$result = $builder->append('Hello ')
                  ->append('World')
                  ->appendLine('!')
                  ->get();

echo $result;  // Hello World!
?>
```

---

## Using $this in Different Contexts

### In Conditional Logic

```php
<?php
class User {
    private $name;
    private $email;
    private $verified = false;
    
    public function __construct($name, $email) {
        $this->name = $name;
        $this->email = $email;
    }
    
    public function verify() {
        $this->verified = true;
    }
    
    public function canAccessPremium() {
        // Check current object's state
        if ($this->verified && strlen($this->name) > 0) {
            return true;
        }
        return false;
    }
    
    public function getStatus() {
        if ($this->verified) {
            return "Verified user: {$this->name}";
        }
        return "Unverified user: {$this->name}";
    }
}

$user = new User('Alice', 'alice@example.com');
echo $user->getStatus();           // Unverified user: Alice
$user->verify();
echo $user->canAccessPremium();    // true
?>
```

### In Loop Contexts

```php
<?php
class Collection {
    private $items = [];
    
    public function add($item): self {
        $this->items[] = $item;
        return $this;
    }
    
    public function filter($callback): self {
        $filtered = [];
        foreach ($this->items as $item) {
            if ($callback($item)) {
                $filtered[] = $item;
            }
        }
        $this->items = $filtered;
        return $this;
    }
    
    public function map($callback): self {
        $mapped = [];
        foreach ($this->items as $item) {
            $mapped[] = $callback($item);
        }
        $this->items = $mapped;
        return $this;
    }
    
    public function getItems() {
        return $this->items;
    }
}

$collection = new Collection();
$collection->add(1)->add(2)->add(3)->add(4)->add(5);

$result = $collection
    ->filter(function($n) { return $n % 2 == 0; })
    ->map(function($n) { return $n * 2; });

print_r($result->getItems());  // [4, 8]
?>
```

---

## $this vs Static

### Instance Methods vs Static Methods

```php
<?php
class Statistics {
    private $count = 0;                 // Instance property
    private static $totalCount = 0;     // Static property
    
    public function increment() {
        $this->count++;                 // Access instance property
        self::$totalCount++;            // Access static property
    }
    
    public function getInstanceCount() {
        return $this->count;            // Use $this for instance
    }
    
    public static function getTotalCount() {
        // return $this->count;          // Error! $this not available in static
        return self::$totalCount;       // Use self:: for static
    }
}

$stats1 = new Statistics();
$stats1->increment();
$stats1->increment();

$stats2 = new Statistics();
$stats2->increment();

echo $stats1->getInstanceCount();           // 2
echo $stats2->getInstanceCount();           // 1
echo Statistics::getTotalCount();          // 3
?>
```

### When to Use $this vs self::

```php
<?php
class ConfigManager {
    private $settings = [];
    private static $defaultSettings = [];
    
    public function setSetting($key, $value) {
        // Use $this for instance-specific settings
        $this->settings[$key] = $value;
    }
    
    public function getSetting($key) {
        return $this->settings[$key] ?? null;
    }
    
    public static function setDefaultSetting($key, $value) {
        // Use self:: for static properties
        self::$defaultSettings[$key] = $value;
    }
    
    public function getEffectiveSetting($key) {
        // Check instance first, then fall back to default
        return $this->settings[$key] ?? self::$defaultSettings[$key] ?? null;
    }
}

ConfigManager::setDefaultSetting('timeout', 30);
$config = new ConfigManager();
$config->setSetting('timeout', 60);

echo $config->getEffectiveSetting('timeout');  // 60
?>
```

---

## Method Chaining with $this

### Building Fluent Interfaces

```php
<?php
class HTMLBuilder {
    private $html = '';
    
    public function div(string $class = ''): self {
        $this->html .= "<div" . ($class ? " class=\"$class\"" : "") . ">";
        return $this;
    }
    
    public function p(string $text): self {
        $this->html .= "<p>$text</p>";
        return $this;
    }
    
    public function span(string $text): self {
        $this->html .= "<span>$text</span>";
        return $this;
    }
    
    public function closeDiv(): self {
        $this->html .= "</div>";
        return $this;
    }
    
    public function get(): string {
        return $this->html;
    }
}

$html = (new HTMLBuilder())
    ->div('container')
    ->p('Welcome')
    ->span('to my site')
    ->closeDiv()
    ->get();

echo $html;
// <div class="container"><p>Welcome</p><span>to my site</span></div>
?>
```

---

## Practical Examples

### Shopping Cart Class

```php
<?php
class ShoppingCart {
    private $items = [];
    private $taxRate = 0.10;
    
    public function addItem($name, $price, $quantity) {
        $this->items[] = [
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity
        ];
        return $this;
    }
    
    public function removeLastItem() {
        array_pop($this->items);
        return $this;
    }
    
    public function getSubtotal() {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
    
    public function getTax() {
        return $this->getSubtotal() * $this->taxRate;
    }
    
    public function getTotal() {
        return $this->getSubtotal() + $this->getTax();
    }
    
    public function getSummary() {
        return [
            'items' => count($this->items),
            'subtotal' => $this->getSubtotal(),
            'tax' => $this->getTax(),
            'total' => $this->getTotal()
        ];
    }
}

$cart = new ShoppingCart();
$cart->addItem('Laptop', 999.99, 1)
     ->addItem('Mouse', 25.99, 2)
     ->addItem('Keyboard', 79.99, 1);

$summary = $cart->getSummary();
echo "Total: $" . number_format($summary['total'], 2);
?>
```

### State Machine Class

```php
<?php
class OrderStatus {
    private $state = 'pending';
    private $history = [];
    
    public function submit() {
        if ($this->state === 'pending') {
            $this->updateState('submitted');
            return true;
        }
        return false;
    }
    
    public function process() {
        if ($this->state === 'submitted') {
            $this->updateState('processing');
            return true;
        }
        return false;
    }
    
    public function ship() {
        if ($this->state === 'processing') {
            $this->updateState('shipped');
            return true;
        }
        return false;
    }
    
    public function deliver() {
        if ($this->state === 'shipped') {
            $this->updateState('delivered');
            return true;
        }
        return false;
    }
    
    private function updateState($newState) {
        $this->history[] = [
            'from' => $this->state,
            'to' => $newState,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        $this->state = $newState;
    }
    
    public function getState() {
        return $this->state;
    }
    
    public function getHistory() {
        return $this->history;
    }
}

$order = new OrderStatus();
$order->submit();
$order->process();
$order->ship();
$order->deliver();

echo "Current state: " . $order->getState();  // delivered
print_r($order->getHistory());
?>
```

---

## Common Mistakes

### 1. Using $this in Static Methods

```php
<?php
// ❌ Wrong: Can't use $this in static method
class Math {
    private $value = 0;
    
    public static function calculate() {
        return $this->value * 2;  // Error! $this doesn't exist
    }
}

// ✓ Correct: Use self:: or static property
class Math {
    private static $value = 0;
    
    public static function calculate() {
        return self::$value * 2;  // Correct
    }
}
?>
```

### 2. Forgetting $this Arrow

```php
<?php
// ❌ Wrong: Missing arrow operator
class User {
    public $name = 'John';
    
    public function greet() {
        echo "Hello " . this->name;  // Error! Missing $
    }
}

// ✓ Correct: Use $this->
class User {
    public $name = 'John';
    
    public function greet() {
        echo "Hello " . $this->name;  // Correct
    }
}
?>
```

### 3. Not Returning $this for Chaining

```php
<?php
// ❌ Wrong: Methods don't return $this
class Builder {
    private $value = '';
    
    public function set($val) {
        $this->value = $val;
        // Missing return $this;
    }
}

// ✓ Correct: Return $this
class Builder {
    private $value = '';
    
    public function set($val): self {
        $this->value = $val;
        return $this;  // Allows chaining
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// User Profile Management System

class UserProfile {
    private $id;
    private $username;
    private $email;
    private $bio = '';
    private $followers = [];
    private $following = [];
    
    public function __construct($id, $username, $email) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
    }
    
    public function updateBio($bio): self {
        $this->bio = $bio;
        return $this;
    }
    
    public function followUser(UserProfile $user): bool {
        if ($this->isNotAlreadyFollowing($user)) {
            $this->following[] = $user->getId();
            return true;
        }
        return false;
    }
    
    public function addFollower(UserProfile $user): bool {
        if (!in_array($user->getId(), $this->followers)) {
            $this->followers[] = $user->getId();
            return true;
        }
        return false;
    }
    
    private function isNotAlreadyFollowing(UserProfile $user): bool {
        return !in_array($user->getId(), $this->following);
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function getProfile() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'bio' => $this->bio,
            'followers_count' => count($this->followers),
            'following_count' => count($this->following)
        ];
    }
}

// Usage
$alice = new UserProfile(1, 'alice', 'alice@example.com');
$alice->updateBio('Software Developer');

$bob = new UserProfile(2, 'bob', 'bob@example.com');
$bob->updateBio('Designer');

$alice->followUser($bob);
$bob->addFollower($alice);

print_r($alice->getProfile());
print_r($bob->getProfile());
?>
```

---

## Cross-References

- **Related Topic: [Classes](2-class.md)** - Understanding class structure
- **Related Topic: [Methods/Functions](5-function.md)** - Methods in classes
- **Related Topic: [Constructor](9-constructor.md)** - Using $this in constructor
- **Related Topic: [Self Keyword](8-self-keyword.md)** - Accessing static members
- **Related Topic: [Parent Keyword](16-parent-keyword.md)** - Calling parent methods
- **Related Topic: [Visibility/Access Modifiers](14-visibility.md)** - Controlling property access
- **Related Topic: [Method Chaining](#method-chaining-with-this)** - Returning $this for fluent interfaces
