# Methods/Functions in PHP Classes

## Table of Contents
1. [Overview](#overview)
2. [Method Basics](#method-basics)
3. [Method Parameters and Return Types](#method-parameters-and-return-types)
4. [Access Modifiers](#access-modifiers)
5. [Method Chaining](#method-chaining)
6. [Static Methods](#static-methods)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

Methods are functions defined inside classes that define the behavior of objects. They operate on the object's data (properties) and can be called on object instances. Methods are the primary way objects interact with their data and the outside world.

**Key Concepts:**
- Methods encapsulate behavior
- Methods access object properties via `$this`
- Methods can have visibility levels (public, protected, private)
- Methods can return values, accept parameters
- PHP 8+ supports typed parameters and return types

---

## Method Basics

### Defining Methods

```php
<?php
class Calculator {
    // Method without parameters
    public function welcome() {
        echo "Welcome to Calculator!";
    }
    
    // Method with parameters
    public function add($a, $b) {
        return $a + $b;
    }
    
    // Method with return type
    public function multiply(int $a, int $b): int {
        return $a * $b;
    }
}

$calc = new Calculator();
$calc->welcome();                  // Welcome to Calculator!
echo $calc->add(5, 3);             // 8
echo $calc->multiply(4, 2);        // 8
?>
```

### Using $this in Methods

The `$this` variable refers to the current object instance and allows you to access properties and call other methods.

```php
<?php
class Person {
    public $name;
    public $age;
    
    public function setInfo($name, $age) {
        $this->name = $name;       // Set current object's property
        $this->age = $age;
    }
    
    public function getInfo() {
        return $this->name . ' is ' . $this->age . ' years old';
    }
    
    public function greet() {
        echo "Hello, I'm {$this->name}";  // Access another method's result
        echo "\n";
        $this->displayAge();        // Call another method
    }
    
    private function displayAge() {
        echo "Age: {$this->age}";
    }
}

$person = new Person();
$person->setInfo('Alice', 30);
echo $person->getInfo();            // Alice is 30 years old
$person->greet();                   // Hello, I'm Alice
                                    // Age: 30
?>
```

### Method Visibility

```php
<?php
class BankAccount {
    private $balance = 0;
    
    // Public method - can be called from anywhere
    public function deposit($amount) {
        if ($this->isValidAmount($amount)) {
            $this->balance += $amount;
            $this->logTransaction('deposit', $amount);
            return true;
        }
        return false;
    }
    
    // Protected method - can be called by this class and subclasses
    protected function logTransaction($type, $amount) {
        echo "Transaction: $type of $$amount\n";
    }
    
    // Private method - only callable within this class
    private function isValidAmount($amount) {
        return $amount > 0;
    }
    
    // Public getter
    public function getBalance() {
        return $this->balance;
    }
}

$account = new BankAccount();
$account->deposit(100);              // OK - public method
echo $account->getBalance();         // 100
// $account->logTransaction('test', 50);  // Error - protected
// $account->isValidAmount(100);      // Error - private
?>
```

---

## Method Parameters and Return Types

### Parameter Declaration with Type Hints

```php
<?php
class FileHandler {
    // Multiple parameters with types
    public function copyFile(string $source, string $destination, bool $overwrite = false): bool {
        if (file_exists($destination) && !$overwrite) {
            return false;
        }
        return copy($source, $destination);
    }
    
    // Array parameter
    public function saveData(array $data): string {
        $json = json_encode($data);
        file_put_contents('data.json', $json);
        return $json;
    }
    
    // Object type parameter
    public function processUser(User $user): void {
        echo "Processing user: " . $user->getName();
    }
    
    // Union types (PHP 8.0+)
    public function getValue(int|string $id): mixed {
        return $id;
    }
}

class User {
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
}

$handler = new FileHandler();
$user = new User('John');
$handler->processUser($user);
?>
```

### Return Types

```php
<?php
class QueryBuilder {
    private $query = '';
    
    // Void return (no return value)
    public function setTable(string $table): void {
        $this->query .= "FROM $table";
    }
    
    // String return type
    public function getQuery(): string {
        return $this->query;
    }
    
    // Array return type
    public function execute(): array {
        return [];
    }
    
    // Nullable return type
    public function findById(int $id): ?array {
        return null; // or array
    }
    
    // Return self for method chaining
    public function select(string $columns): self {
        $this->query = "SELECT $columns";
        return $this;
    }
}

$builder = new QueryBuilder();
$builder->select('*')->setTable('users');
echo $builder->getQuery();  // SELECT * FROM users
?>
```

### Variadic Parameters

```php
<?php
class Logger {
    // Accept variable number of parameters
    public function log(string $level, string ...$messages): void {
        $output = "[$level] " . implode(' ', $messages);
        echo $output . "\n";
    }
}

$logger = new Logger();
$logger->log('INFO', 'User', 'logged', 'in');     // [INFO] User logged in
$logger->log('ERROR', 'Database', 'connection', 'failed');  // [ERROR] Database connection failed
?>
```

---

## Access Modifiers

```php
<?php
class Document {
    // Public - accessible everywhere
    public function displayContent() {
        echo "Public content";
    }
    
    // Protected - accessible in this class and subclasses
    protected function validate() {
        return true;
    }
    
    // Private - accessible only in this class
    private function encrypt() {
        return md5('data');
    }
    
    // Public method calling private method
    public function save() {
        if ($this->validate()) {
            $encrypted = $this->encrypt();
            echo "Saved: $encrypted";
        }
    }
}

class SecureDocument extends Document {
    public function secureDisplay() {
        $this->displayContent();      // OK - public
        $this->validate();            // OK - protected
        // $this->encrypt();           // Error - private
    }
}

$doc = new Document();
$doc->displayContent();              // OK
$doc->save();                        // OK
// $doc->validate();                 // Error - protected
// $doc->encrypt();                  // Error - private

$secure = new SecureDocument();
$secure->secureDisplay();
?>
```

---

## Method Chaining

Method chaining allows you to call multiple methods on an object sequentially by returning `$this`.

```php
<?php
class QueryBuilder {
    private $sql = '';
    private $bindings = [];
    
    public function select(string $columns): self {
        $this->sql = "SELECT $columns";
        return $this;
    }
    
    public function from(string $table): self {
        $this->sql .= " FROM $table";
        return $this;
    }
    
    public function where(string $condition, $value): self {
        $this->sql .= " WHERE $condition";
        $this->bindings[] = $value;
        return $this;
    }
    
    public function orderBy(string $column, string $direction = 'ASC'): self {
        $this->sql .= " ORDER BY $column $direction";
        return $this;
    }
    
    public function limit(int $count): self {
        $this->sql .= " LIMIT $count";
        return $this;
    }
    
    public function get(): string {
        return $this->sql;
    }
}

// Fluent interface
$query = (new QueryBuilder())
    ->select('*')
    ->from('users')
    ->where('age > ?', 18)
    ->orderBy('name')
    ->limit(10);

echo $query->get();
// SELECT * FROM users WHERE age > ? ORDER BY name LIMIT 10
?>
```

---

## Static Methods

Static methods belong to the class itself, not to object instances, and are called using the class name.

```php
<?php
class MathUtils {
    // Static method
    public static function factorial(int $n): int {
        if ($n <= 1) {
            return 1;
        }
        return $n * self::factorial($n - 1);
    }
    
    // Accessing static method from within class
    public static function fibonacci(int $n): int {
        if ($n <= 1) {
            return $n;
        }
        return self::fibonacci($n - 1) + self::fibonacci($n - 2);
    }
    
    // Static property
    private static $calculations = 0;
    
    public static function increment(): void {
        self::$calculations++;
    }
    
    public static function getCalculations(): int {
        return self::$calculations;
    }
}

echo MathUtils::factorial(5);         // 120
echo MathUtils::fibonacci(6);         // 8
MathUtils::increment();
echo MathUtils::getCalculations();    // 1
?>
```

### Static Methods with Inheritance

```php
<?php
class Logger {
    public static function info(string $message): void {
        echo "[INFO] $message\n";
    }
}

class FileLogger extends Logger {
    public static function info(string $message): void {
        parent::info($message);
        // Write to file
        file_put_contents('log.txt', "[INFO] $message\n", FILE_APPEND);
    }
}

FileLogger::info('Application started');
?>
```

---

## Practical Examples

### User Authentication Class

```php
<?php
class AuthManager {
    private $users = [];
    
    public function register(string $email, string $password): bool {
        if ($this->userExists($email)) {
            return false;
        }
        $this->users[$email] = [
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d H:i:s')
        ];
        return true;
    }
    
    public function authenticate(string $email, string $password): bool {
        if (!$this->userExists($email)) {
            return false;
        }
        return password_verify($password, $this->users[$email]['password']);
    }
    
    private function userExists(string $email): bool {
        return isset($this->users[$email]);
    }
    
    public function getUser(string $email): ?array {
        return $this->users[$email] ?? null;
    }
}

$auth = new AuthManager();
$auth->register('john@example.com', 'secret123');
var_dump($auth->authenticate('john@example.com', 'secret123'));  // true
var_dump($auth->authenticate('john@example.com', 'wrong'));      // false
?>
```

### API Response Handler

```php
<?php
class APIResponse {
    private $status;
    private $data = [];
    private $errors = [];
    
    public function success($data = null): self {
        $this->status = 'success';
        if ($data !== null) {
            $this->data = $data;
        }
        return $this;
    }
    
    public function error(string $message, string $code = null): self {
        $this->status = 'error';
        $this->errors[] = ['message' => $message, 'code' => $code];
        return $this;
    }
    
    public function addData(string $key, $value): self {
        $this->data[$key] = $value;
        return $this;
    }
    
    public function addError(string $field, string $message): self {
        $this->errors[] = ['field' => $field, 'message' => $message];
        return $this;
    }
    
    public function toArray(): array {
        return [
            'status' => $this->status,
            'data' => $this->data,
            'errors' => $this->errors,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    public function toJSON(): string {
        return json_encode($this->toArray());
    }
}

$response = (new APIResponse())
    ->success()
    ->addData('user_id', 123)
    ->addData('username', 'johndoe');

echo $response->toJSON();
?>
```

---

## Common Mistakes

### 1. Missing Visibility Modifiers

```php
<?php
// ❌ Wrong: Implicit public (confusing in large codebases)
class User {
    function getName() {
        return 'John';
    }
}

// ✓ Correct: Explicit visibility
class User {
    public function getName(): string {
        return 'John';
    }
}
?>
```

### 2. Forgetting Return Type

```php
<?php
// ❌ Wrong: Unclear what method returns
class Calculator {
    public function add($a, $b) {
        return $a + $b;
    }
}

// ✓ Correct: Clear return type
class Calculator {
    public function add(int $a, int $b): int {
        return $a + $b;
    }
}
?>
```

### 3. Not Returning $this for Chaining

```php
<?php
// ❌ Wrong: Can't chain methods
class Builder {
    public function setName($name) {
        echo "Name: $name";
    }
    
    public function setAge($age) {
        echo "Age: $age";
    }
}

$builder = new Builder();
// $builder->setName('John')->setAge(30);  // Error!

// ✓ Correct: Return $this for chaining
class Builder {
    public function setName($name): self {
        echo "Name: $name\n";
        return $this;
    }
    
    public function setAge($age): self {
        echo "Age: $age\n";
        return $this;
    }
}

$builder = new Builder();
$builder->setName('John')->setAge(30);  // Works!
?>
```

### 4. Confusing Static and Instance Methods

```php
<?php
// ❌ Wrong: Can't access instance property from static method
class User {
    public $name = 'John';
    
    public static function greet() {
        echo "Hello " . $this->name;  // Error! $this doesn't exist in static
    }
}

// ✓ Correct: Use static properties in static methods
class User {
    public static $count = 0;
    
    public static function incrementCount(): void {
        self::$count++;
    }
}

User::incrementCount();
echo User::$count;  // 1
?>
```

---

## Complete Working Example

```php
<?php
// Product Management System

class Product {
    private $id;
    private $name;
    private $price;
    private $stock;
    
    public function __construct(int $id, string $name, float $price, int $stock) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }
    
    public function getId(): int {
        return $this->id;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getPrice(): float {
        return $this->price;
    }
    
    public function getStock(): int {
        return $this->stock;
    }
    
    public function setPrice(float $price): void {
        if ($price > 0) {
            $this->price = $price;
        }
    }
    
    public function purchaseStock(int $quantity): bool {
        if ($quantity > 0 && $quantity <= $this->stock) {
            $this->stock -= $quantity;
            return true;
        }
        return false;
    }
    
    public function restockItem(int $quantity): void {
        if ($quantity > 0) {
            $this->stock += $quantity;
        }
    }
    
    public function calculateTotal(int $quantity): float {
        return $this->price * $quantity;
    }
    
    public function getDetails(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock
        ];
    }
}

class Inventory {
    private $products = [];
    
    public function addProduct(Product $product): void {
        $this->products[$product->getId()] = $product;
    }
    
    public function getProduct(int $id): ?Product {
        return $this->products[$id] ?? null;
    }
    
    public function getTotalValue(): float {
        $total = 0;
        foreach ($this->products as $product) {
            $total += $product->getPrice() * $product->getStock();
        }
        return $total;
    }
    
    public function listAll(): array {
        $items = [];
        foreach ($this->products as $product) {
            $items[] = $product->getDetails();
        }
        return $items;
    }
    
    public function purchaseProduct(int $id, int $quantity): bool {
        $product = $this->getProduct($id);
        if ($product && $product->purchaseStock($quantity)) {
            return true;
        }
        return false;
    }
}

// Usage
$inventory = new Inventory();

$laptop = new Product(1, 'Laptop', 999.99, 10);
$mouse = new Product(2, 'Mouse', 25.99, 50);
$keyboard = new Product(3, 'Keyboard', 79.99, 30);

$inventory->addProduct($laptop);
$inventory->addProduct($mouse);
$inventory->addProduct($keyboard);

echo "Total Inventory Value: $" . number_format($inventory->getTotalValue(), 2) . "\n";

$inventory->purchaseProduct(1, 2);
echo "\nAfter selling 2 laptops:\n";
echo "Total Inventory Value: $" . number_format($inventory->getTotalValue(), 2) . "\n";

echo "\nProduct Details:\n";
foreach ($inventory->listAll() as $item) {
    echo "- {$item['name']}: ${$item['price']}, Stock: {$item['stock']}\n";
}
?>
```

---

## Cross-References

- **Related Topic: [$this Keyword](6-this-keyword.md)** - Understanding how to access object properties
- **Related Topic: [Constructor](9-constructor.md)** - Methods called during object creation
- **Related Topic: [Visibility/Access Modifiers](14-visibility.md)** - Controlling method accessibility
- **Related Topic: [Static Keyword](28-static-keyword.md)** - Methods that belong to classes, not instances
- **Related Topic: [Method Overriding](15-function-overriding.md)** - Overriding methods in child classes
- **Related Topic: [Magic Methods](34-magic-function.md)** - Special methods with automatic triggers
- **Related Topic: [Interfaces](23-interface.md)** - Defining method contracts
