# Properties in PHP

## Table of Contents
1. [Overview](#overview)
2. [Property Declaration](#property-declaration)
3. [Property Types](#property-types)
4. [Property Visibility](#property-visibility)
5. [Accessing Properties](#accessing-properties)
6. [Practical Examples](#practical-examples)
7. [Common Mistakes](#common-mistakes)

---

## Overview

**Properties** are variables that belong to a class. They store data/state for objects.

**Key characteristics:**
- Declared in class
- Can have visibility modifiers
- Can have type declarations
- Accessible via `$this` inside class

---

## Property Declaration

### Basic Properties

```php
<?php
class User {
    public $name;
    public $email;
    public $age;
    
    public function displayInfo() {
        echo $this->name . ' (' . $this->email . ')';
    }
}

$user = new User();
$user->name = 'John';
$user->email = 'john@example.com';
$user->age = 30;

$user->displayInfo();
?>
```

### Properties with Default Values

```php
<?php
class Settings {
    public $theme = 'light';
    public $language = 'en';
    public $notifications = true;
    public $timezone = 'UTC';
}

$settings = new Settings();
echo $settings->theme;  // light (default)

$settings->theme = 'dark';
echo $settings->theme;  // dark
?>
```

### Typed Properties (PHP 7.4+)

```php
<?php
class Product {
    public string $name;
    public float $price;
    public int $stock;
    public bool $active;
    public array $tags;
    
    public function setInfo($name, $price) {
        $this->name = $name;
        $this->price = $price;
    }
}

$product = new Product();
$product->setInfo('Laptop', 999.99);
$product->stock = 10;
$product->tags = ['electronics', 'computers'];

echo $product->name;  // Laptop
?>
```

### Nullable Properties

```php
<?php
class BlogPost {
    public string $title;
    public ?string $slug = null;      // Can be null
    public ?DateTime $publishedAt = null;
    
    public function publish() {
        if ($this->slug === null) {
            $this->slug = strtolower(str_replace(' ', '-', $this->title));
        }
        $this->publishedAt = new DateTime();
    }
}

$post = new BlogPost();
$post->title = 'My First Post';

var_dump($post->slug);  // NULL
$post->publish();
echo $post->slug;  // my-first-post
?>
```

---

## Property Types

### Scalar Types

```php
<?php
class Employee {
    public int $id;
    public string $name;
    public float $salary;
    public bool $active;
}

$emp = new Employee();
$emp->id = 1;
$emp->name = 'John';
$emp->salary = 50000.50;
$emp->active = true;

echo "{$emp->name}: ${$emp->salary}";
?>
```

### Compound Types

```php
<?php
class Config {
    public array $settings;
    public object $metadata;
    public mixed $value;  // PHP 8.0+
}

$config = new Config();
$config->settings = ['theme' => 'light', 'lang' => 'en'];
$config->metadata = (object)['version' => '1.0'];
$config->value = 'Can be any type';
?>
```

### Union Types (PHP 8.0+)

```php
<?php
class Message {
    public int|string $id;      // Can be int or string
    public string|null $content;  // String or null
}

$msg = new Message();
$msg->id = 123;         // int
$msg->id = 'msg-456';   // string
$msg->content = 'Hello';  // string
$msg->content = null;   // null
?>
```

---

## Property Visibility

### Public Properties

```php
<?php
class Car {
    public $color;      // Accessible anywhere
    public $brand;
}

$car = new Car();
$car->color = 'red';    // Direct access
echo $car->color;       // red
?>
```

### Private Properties

```php
<?php
class BankAccount {
    private $balance;   // Only in this class
    private $pin;
    
    public function deposit($amount) {
        $this->balance += $amount;  // Can access in methods
    }
    
    public function getBalance() {
        return $this->balance;
    }
}

$account = new BankAccount();
// echo $account->balance;  // Error!
$account->deposit(1000);
echo $account->getBalance();  // 1000
?>
```

### Protected Properties

```php
<?php
class Animal {
    protected $name;    // This class and subclasses
    private $age;       // Only this class
    public $type;       // Everywhere
    
    public function setName($name) {
        $this->name = $name;
    }
}

class Dog extends Animal {
    public function getName() {
        return $this->name;  // Can access protected
    }
}

$dog = new Dog();
$dog->setName('Rex');
echo $dog->getName();  // Rex
// echo $dog->age;      // Error! (private)
?>
```

### Readonly Properties (PHP 8.1+)

```php
<?php
class UserId {
    public readonly int $id;
    
    public function __construct($id) {
        $this->id = $id;
    }
}

$userId = new UserId(123);
echo $userId->id;      // 123
// $userId->id = 456;  // Error! Can't modify readonly
?>
```

---

## Accessing Properties

### Reading Properties

```php
<?php
class Book {
    public $title = 'Unknown';
    public $author = 'Unknown';
}

$book = new Book();
echo $book->title;      // Unknown
echo $book->author;     // Unknown

// Modify properties
$book->title = 'PHP Guide';
echo $book->title;      // PHP Guide
?>
```

### Dynamic Access

```php
<?php
class DataObject {
    public $firstName;
    public $lastName;
    public $email;
}

$obj = new DataObject();
$properties = ['firstName' => 'John', 'lastName' => 'Doe', 'email' => 'john@example.com'];

foreach ($properties as $key => $value) {
    $obj->$key = $value;  // Dynamic property access
}

echo $obj->firstName;  // John
echo $obj->lastName;   // Doe
?>
```

### get_object_vars()

```php
<?php
class Product {
    public $name = 'Laptop';
    public $price = 999.99;
    private $supplier = 'Secret';
}

$product = new Product();

// Get all public properties
$vars = get_object_vars($product);
print_r($vars);
// Array ( [name] => Laptop [price] => 999.99 )

// Note: private properties not included
?>
```

---

## Practical Examples

### User Profile

```php
<?php
class UserProfile {
    public string $username;
    public string $email;
    public ?string $bio = null;
    public ?string $avatar = null;
    public bool $verified = false;
    public int $followers = 0;
    public array $badges = [];
    
    public function __construct($username, $email) {
        $this->username = $username;
        $this->email = $email;
    }
    
    public function setBio($bio) {
        $this->bio = $bio;
    }
    
    public function addBadge($badge) {
        $this->badges[] = $badge;
    }
    
    public function verify() {
        $this->verified = true;
    }
}

$user = new UserProfile('john_doe', 'john@example.com');
$user->setBio('Software developer');
$user->addBadge('PHP Expert');
$user->verify();

echo $user->username;     // john_doe
echo $user->followers;    // 0
var_dump($user->badges);  // ['PHP Expert']
?>
```

### Database Model

```php
<?php
class Model {
    protected int $id = 0;
    protected DateTime $createdAt;
    protected ?DateTime $updatedAt = null;
    protected bool $deleted = false;
    
    public function __construct() {
        $this->createdAt = new DateTime();
    }
    
    public function getId(): int {
        return $this->id;
    }
    
    public function markAsDeleted(): void {
        $this->deleted = true;
    }
    
    public function update(): void {
        $this->updatedAt = new DateTime();
    }
}

$model = new Model();
$model->update();
echo $model->getId();  // 0
?>
```

---

## Common Mistakes

### 1. Missing Visibility Modifier

```php
<?php
// ❌ Wrong: No visibility (error in PHP 7.4+ with types)
class User {
    string $name;  // Error!
}

// ✓ Correct: Add visibility
class User {
    public string $name;
}
?>
```

### 2. Type Mismatch

```php
<?php
// ❌ Wrong: Wrong type assigned
class Product {
    public int $stock;
}

$product = new Product();
$product->stock = "ten";  // TypeError!

// ✓ Correct: Assign correct type
$product->stock = 10;
?>
```

### 3. Accessing Private Properties

```php
<?php
// ❌ Wrong: Accessing private
class Account {
    private $balance;
}

$account = new Account();
// echo $account->balance;  // Error!

// ✓ Correct: Use public method
class Account {
    private $balance;
    
    public function getBalance() {
        return $this->balance;
    }
}

echo $account->getBalance();
?>
```

### 4. Undefined Properties

```php
<?php
// ❌ Wrong: Using undefined property
class User {
    public $name;
}

$user = new User();
echo $user->phone;  // Warning! Undefined

// ✓ Correct: Declare or check existence
if (isset($user->phone)) {
    echo $user->phone;
}

// Or declare with default
class User {
    public $name;
    public ?string $phone = null;
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class Article {
    public int $id;
    public string $title;
    public string $content;
    public ?string $slug = null;
    public string $author;
    public array $tags = [];
    public int $views = 0;
    public bool $published = false;
    public DateTime $createdAt;
    public ?DateTime $publishedAt = null;
    
    public function __construct($title, $content, $author) {
        $this->title = $title;
        $this->content = $content;
        $this->author = $author;
        $this->createdAt = new DateTime();
        $this->generateSlug();
    }
    
    public function generateSlug(): void {
        $this->slug = strtolower(
            preg_replace('/[^a-z0-9]+/', '-', $this->title)
        );
    }
    
    public function addTag(string $tag): void {
        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
    }
    
    public function publish(): void {
        $this->published = true;
        $this->publishedAt = new DateTime();
    }
    
    public function incrementViews(): void {
        $this->views++;
    }
}

// Usage
$article = new Article('PHP OOP', 'Learn PHP OOP', 'John');
$article->addTag('PHP');
$article->addTag('OOP');
$article->publish();
$article->incrementViews();

echo "{$article->title} by {$article->author}";
echo " - {$article->views} views";
?>
```

---

## Next Steps

✅ Understand properties  
→ Learn [methods/functions](5-function.md)  
→ Study [$this keyword](6-this-keyword.md)  
→ Explore [constructors](9-constructor.md)
