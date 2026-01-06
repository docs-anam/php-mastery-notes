# Constructor (__construct) in PHP

## Table of Contents
1. [Overview](#overview)
2. [Basic Constructor](#basic-constructor)
3. [Constructor Parameters](#constructor-parameters)
4. [Constructor with Type Hints](#constructor-with-type-hints)
5. [Property Promotion (PHP 8.0+)](#property-promotion-php-80)
6. [Multiple Constructors (PHP 8.1+)](#multiple-constructors-php-81)
7. [Constructor Overloading](#constructor-overloading)
8. [Practical Examples](#practical-examples)
9. [Common Mistakes](#common-mistakes)
10. [Complete Working Example](#complete-working-example)
11. [Cross-References](#cross-references)

---

## Overview

A constructor is a special method automatically called when an object is created. It's used to initialize object properties and perform setup operations. The constructor method is named `__construct` and is optional - if you don't define one, PHP provides a default empty constructor.

**Key Concepts:**
- Constructor is called automatically with `new` keyword
- Named `__construct` (magic method)
- Used for initialization
- Can accept parameters
- Cannot return values
- Each class can have only one constructor (in PHP < 8.1)

---

## Basic Constructor

### Simple Constructor

```php
<?php
class Person {
    public $name;
    public $age;
    
    // Constructor
    public function __construct() {
        $this->name = 'Unknown';
        $this->age = 0;
    }
    
    public function getInfo() {
        return $this->name . ' (' . $this->age . ' years old)';
    }
}

// Constructor is called automatically
$person = new Person();
echo $person->getInfo();  // Unknown (0 years old)
?>
```

### Constructor with Parameters

```php
<?php
class Car {
    public $brand;
    public $model;
    public $year;
    
    public function __construct($brand, $model, $year) {
        $this->brand = $brand;
        $this->model = $model;
        $this->year = $year;
    }
    
    public function getInfo() {
        return $this->year . ' ' . $this->brand . ' ' . $this->model;
    }
}

// Pass arguments to constructor
$car = new Car('Toyota', 'Camry', 2023);
echo $car->getInfo();  // 2023 Toyota Camry
?>
```

### Default Parameter Values

```php
<?php
class User {
    private $username;
    private $email;
    private $role = 'user';
    private $active = true;
    
    public function __construct($username, $email, $role = 'user', $active = true) {
        $this->username = $username;
        $this->email = $email;
        $this->role = $role;
        $this->active = $active;
    }
    
    public function getDetails() {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'active' => $this->active
        ];
    }
}

$user1 = new User('alice', 'alice@example.com');
$user2 = new User('bob', 'bob@example.com', 'admin');
$user3 = new User('charlie', 'charlie@example.com', 'moderator', false);

print_r($user1->getDetails());
print_r($user2->getDetails());
print_r($user3->getDetails());
?>
```

---

## Constructor with Type Hints

### Type Declarations

```php
<?php
class Product {
    private $id;
    private $name;
    private $price;
    private $inStock;
    
    public function __construct(int $id, string $name, float $price, bool $inStock = true) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->inStock = $inStock;
    }
    
    public function getInfo() {
        return "{$this->name} - \${$this->price}";
    }
}

$product = new Product(1, 'Laptop', 999.99);
echo $product->getInfo();  // Laptop - $999.99

// Type mismatch will throw error
// new Product('invalid', 'Laptop', 999.99);  // TypeError
?>
```

### Nullable Types

```php
<?php
class Post {
    private $title;
    private $content;
    private $author;
    private $imageUrl;
    
    public function __construct(string $title, string $content, ?string $author = null, ?string $imageUrl = null) {
        $this->title = $title;
        $this->content = $content;
        $this->author = $author ?? 'Anonymous';
        $this->imageUrl = $imageUrl;
    }
    
    public function getDetails() {
        return [
            'title' => $this->title,
            'author' => $this->author,
            'has_image' => $this->imageUrl !== null
        ];
    }
}

$post1 = new Post('Hello', 'This is a post');
$post2 = new Post('Hello', 'This is a post', 'John', 'image.jpg');

print_r($post1->getDetails());
print_r($post2->getDetails());
?>
```

### Union Types (PHP 8.0+)

```php
<?php
class Logger {
    private $level;
    
    public function __construct(int|string $level = 'info') {
        $this->level = is_int($level) ? $level : strtolower($level);
    }
    
    public function getLevel() {
        return $this->level;
    }
}

$log1 = new Logger(2);
$log2 = new Logger('ERROR');

echo $log1->getLevel();  // 2
echo $log2->getLevel();  // error
?>
```

### Mixed Type and Return Types

```php
<?php
class DataProcessor {
    private $data;
    
    public function __construct(mixed $data) {
        $this->data = $data;
    }
    
    public function getData(): mixed {
        return $this->data;
    }
}

$proc1 = new DataProcessor('string');
$proc2 = new DataProcessor(123);
$proc3 = new DataProcessor([1, 2, 3]);
?>
```

---

## Property Promotion (PHP 8.0+)

### Declaring and Initializing Properties in Constructor

```php
<?php
// Old way - PHP < 8.0
class PersonOld {
    private $name;
    private $email;
    private $age;
    
    public function __construct($name, $email, $age) {
        $this->name = $name;
        $this->email = $email;
        $this->age = $age;
    }
}

// New way - PHP 8.0+ with Constructor Property Promotion
class Person {
    public function __construct(
        private string $name,
        private string $email,
        private int $age
    ) {}
    
    public function getInfo() {
        return $this->name . ' (' . $this->age . ')';
    }
}

$person = new Person('Alice', 'alice@example.com', 30);
echo $person->getInfo();  // Alice (30)
?>
```

### Mixed Visibility with Property Promotion

```php
<?php
class User {
    public function __construct(
        public string $id,
        private string $password,
        protected string $email,
        public int $createdAt = 0
    ) {
        if ($createdAt === 0) {
            $this->createdAt = time();
        }
    }
    
    public function verify($pass) {
        return password_verify($pass, $this->password);
    }
}

$user = new User('123', 'hash', 'user@example.com');
echo $user->id;  // 123
// echo $user->password;  // Error - private
?>
```

---

## Multiple Constructors (PHP 8.1+)

### Named Constructors with Static Methods

```php
<?php
class DateTime2 {
    private $day;
    private $month;
    private $year;
    
    public function __construct(int $day, int $month, int $year) {
        $this->day = $day;
        $this->month = $month;
        $this->year = $year;
    }
    
    // Alternative constructor
    public static function fromString(string $date) {
        [$day, $month, $year] = explode('-', $date);
        return new self((int)$day, (int)$month, (int)$year);
    }
    
    // Another alternative constructor
    public static function today() {
        [$day, $month, $year] = explode('-', date('d-m-Y'));
        return new self((int)$day, (int)$month, (int)$year);
    }
    
    public function getFormatted() {
        return "{$this->day}/{$this->month}/{$this->year}";
    }
}

$date1 = new DateTime2(15, 3, 2023);
$date2 = DateTime2::fromString('25-12-2023');
$date3 = DateTime2::today();

echo $date1->getFormatted();  // 15/3/2023
echo $date2->getFormatted();  // 25/12/2023
echo $date3->getFormatted();  // Current date
?>
```

---

## Constructor Overloading

### Using Variable Arguments

```php
<?php
class Point {
    public $x;
    public $y;
    public $z;
    
    public function __construct(...$args) {
        match (count($args)) {
            1 => $this->initFrom1D($args[0]),
            2 => $this->initFrom2D($args[0], $args[1]),
            3 => $this->initFrom3D($args[0], $args[1], $args[2]),
            default => throw new Exception('Invalid number of arguments')
        };
    }
    
    private function initFrom1D($value) {
        $this->x = $value;
        $this->y = 0;
        $this->z = 0;
    }
    
    private function initFrom2D($x, $y) {
        $this->x = $x;
        $this->y = $y;
        $this->z = 0;
    }
    
    private function initFrom3D($x, $y, $z) {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }
    
    public function getInfo() {
        return "({$this->x}, {$this->y}, {$this->z})";
    }
}

$p1 = new Point(5);
$p2 = new Point(5, 10);
$p3 = new Point(5, 10, 15);

echo $p1->getInfo();  // (5, 0, 0)
echo $p2->getInfo();  // (5, 10, 0)
echo $p3->getInfo();  // (5, 10, 15)
?>
```

### Constructor with Array Argument

```php
<?php
class Config {
    private $settings;
    
    public function __construct(array $config = []) {
        $this->settings = array_merge($this->getDefaults(), $config);
    }
    
    private function getDefaults() {
        return [
            'debug' => false,
            'timeout' => 30,
            'max_retries' => 3,
            'cache_enabled' => true
        ];
    }
    
    public function get($key) {
        return $this->settings[$key] ?? null;
    }
    
    public function getAll() {
        return $this->settings;
    }
}

$config1 = new Config();  // Uses defaults
$config2 = new Config(['debug' => true, 'timeout' => 60]);  // Override some

print_r($config1->getAll());
print_r($config2->getAll());
?>
```

---

## Practical Examples

### Database Connection Class

```php
<?php
class DatabaseConnection {
    private $host;
    private $port;
    private $database;
    private $username;
    private $password;
    private $connection;
    
    public function __construct(
        string $host = 'localhost',
        int $port = 3306,
        string $database = 'test',
        string $username = 'root',
        string $password = ''
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        
        // Validate and set up connection
        $this->connect();
    }
    
    private function connect() {
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->database}";
        try {
            $this->connection = new PDO($dsn, $this->username, $this->password);
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }
    
    public function query($sql) {
        return $this->connection->query($sql);
    }
}

$db = new DatabaseConnection('localhost', 3306, 'myapp');
?>
```

### Email Builder Class

```php
<?php
class EmailBuilder {
    private $to;
    private $subject;
    private $body;
    private $headers = [];
    private $attachments = [];
    
    public function __construct(string $to, string $subject = '') {
        $this->to = $to;
        $this->subject = $subject;
        $this->headers['From'] = 'noreply@example.com';
        $this->headers['Content-Type'] = 'text/html; charset=UTF-8';
    }
    
    public function setBody(string $body) {
        $this->body = $body;
        return $this;
    }
    
    public function addHeader(string $name, string $value) {
        $this->headers[$name] = $value;
        return $this;
    }
    
    public function addAttachment(string $path) {
        if (file_exists($path)) {
            $this->attachments[] = $path;
        }
        return $this;
    }
    
    public function send() {
        $headers = implode("\r\n", array_map(
            fn($k, $v) => "$k: $v",
            array_keys($this->headers),
            array_values($this->headers)
        ));
        
        return mail($this->to, $this->subject, $this->body, $headers);
    }
}

$email = new EmailBuilder('user@example.com', 'Welcome!')
    ->setBody('<p>Hello user!</p>')
    ->addHeader('Reply-To', 'support@example.com');

// $email->send();
?>
```

---

## Common Mistakes

### 1. Forgetting Constructor Definition

```php
<?php
// ❌ Wrong: Assuming properties are initialized
class User {
    public $name;
    public $email;
}

$user = new User();
echo $user->name;  // Notice: Undefined property

// ✓ Correct: Initialize in constructor
class User {
    public $name;
    public $email;
    
    public function __construct($name = '', $email = '') {
        $this->name = $name;
        $this->email = $email;
    }
}

$user = new User('John', 'john@example.com');
?>
```

### 2. Missing Type Declaration

```php
<?php
// ❌ Wrong: No type hints
class Product {
    public function __construct($id, $name, $price) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }
}

// Can receive wrong types
$product = new Product('abc', 123, 'not a number');

// ✓ Correct: Add type hints
class Product {
    public function __construct(
        int $id,
        string $name,
        float $price
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }
}

// Type checking enforced
$product = new Product(1, 'Laptop', 999.99);
?>
```

### 3. Not Calling Parent Constructor

```php
<?php
// ❌ Wrong: Child constructor doesn't call parent
class Animal {
    protected $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
}

class Dog extends Animal {
    private $breed;
    
    public function __construct($name, $breed) {
        $this->breed = $breed;
        // Missing parent::__construct($name)
    }
}

$dog = new Dog('Rex', 'Golden Retriever');
echo $dog->name;  // Undefined

// ✓ Correct: Call parent constructor
class Dog extends Animal {
    private $breed;
    
    public function __construct($name, $breed) {
        parent::__construct($name);  // Call parent
        $this->breed = $breed;
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// User Registration and Authentication System

class UserAccount {
    private $id;
    private $username;
    private $email;
    private $password;
    private $createdAt;
    private $isActive;
    
    public function __construct(
        string $username,
        string $email,
        string $password,
        int $id = 0
    ) {
        if (strlen($username) < 3) {
            throw new Exception('Username must be at least 3 characters');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        if (strlen($password) < 8) {
            throw new Exception('Password must be at least 8 characters');
        }
        
        $this->id = $id ?: $this->generateId();
        $this->username = $username;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->createdAt = date('Y-m-d H:i:s');
        $this->isActive = true;
    }
    
    private function generateId() {
        return rand(1000, 9999);
    }
    
    public function authenticate($password) {
        return password_verify($password, $this->password);
    }
    
    public function getProfile() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'created' => $this->createdAt,
            'active' => $this->isActive
        ];
    }
    
    public function deactivate() {
        $this->isActive = false;
    }
    
    public function activate() {
        $this->isActive = true;
    }
}

// Usage
try {
    $user = new UserAccount('john_doe', 'john@example.com', 'SecurePassword123');
    echo "User created successfully\n";
    print_r($user->getProfile());
    
    if ($user->authenticate('SecurePassword123')) {
        echo "Authentication successful\n";
    }
    
    // Invalid credentials
    if (!$user->authenticate('WrongPassword')) {
        echo "Authentication failed\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

---

## Cross-References

- **Related Topic: [Classes](2-class.md)** - Understanding class structure
- **Related Topic: [Destructor](10-destructor.md)** - Cleanup when object is destroyed
- **Related Topic: [Constructor Overriding](17-constructor-overriding.md)** - Overriding constructor in child classes
- **Related Topic: [Properties](4-properties.md)** - Object properties
- **Related Topic: [Inheritance Basics](11-inheritance.md)** - Parent constructor
- **Related Topic: [Parent Keyword](16-parent-keyword.md)** - Calling parent constructor
