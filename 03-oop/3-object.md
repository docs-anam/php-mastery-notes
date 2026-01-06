# Objects in PHP

## Table of Contents
1. [Overview](#overview)
2. [Object Creation](#object-creation)
3. [Accessing Properties](#accessing-properties)
4. [Accessing Methods](#accessing-methods)
5. [Object Comparison](#object-comparison)
6. [Practical Examples](#practical-examples)
7. [Common Mistakes](#common-mistakes)

---

## Overview

An **object** is an instance of a class. It contains data (properties) and behavior (methods).

**Key differences from arrays:**
- Objects have methods (functions)
- Objects have defined structure (class)
- Objects support inheritance
- Objects have visibility (public/private)

---

## Object Creation

### Instantiating Objects

```php
<?php
class Car {
    public $color;
    public $brand;
    
    public function honk() {
        return "Beep beep!";
    }
}

// Create object using new keyword
$car = new Car();
$car->color = 'red';
$car->brand = 'Toyota';

echo $car->honk();  // Beep beep!
?>
```

### Using Constructor

```php
<?php
class Person {
    public $name;
    public $age;
    
    public function __construct($name, $age) {
        $this->name = $name;
        $this->age = $age;
    }
}

// Create object with initial values
$person = new Person('John', 30);

echo $person->name;  // John
echo $person->age;   // 30
?>
```

### Creating from Another Object

```php
<?php
class User {
    public $id;
    public $name;
}

$user1 = new User();
$user1->id = 1;
$user1->name = 'John';

// Objects are passed by reference
$user2 = $user1;  // Reference, not copy
$user2->name = 'Jane';

echo $user1->name;  // Jane (same object!)
echo $user2->name;  // Jane

// Check if same object
var_dump($user1 === $user2);  // true
?>
```

---

## Accessing Properties

### Reading Properties

```php
<?php
class Book {
    public $title;
    public $author;
    public $year;
}

$book = new Book();
$book->title = 'PHP Guide';
$book->author = 'John';
$book->year = 2024;

// Read properties
echo $book->title;   // PHP Guide
echo $book->author;  // John
echo $book->year;    // 2024

// Check if property exists
if (isset($book->title)) {
    echo "Title exists";
}

if (property_exists($book, 'author')) {
    echo "Author property found";
}
?>
```

### Modifying Properties

```php
<?php
class Product {
    public $name;
    public $price;
    public $stock;
}

$product = new Product();
$product->name = 'Laptop';
$product->price = 999.99;
$product->stock = 10;

// Modify properties
$product->price = 899.99;
$product->stock -= 1;

echo $product->name . ': $' . $product->price;
?>
```

### Dynamic Properties

```php
<?php
class DynamicObject {
    public $name;
}

$obj = new DynamicObject();
$obj->name = 'Object';
$obj->extra = 'Added dynamically';  // Add property on the fly
$obj->flag = true;

echo $obj->extra;  // Added dynamically
?>
```

### Checking Properties

```php
<?php
class Account {
    public $balance;
    private $password;
}

$account = new Account();
$account->balance = 1000;

// isset - checks if property exists and is not null
var_dump(isset($account->balance));    // true
var_dump(isset($account->password));   // true (but private)
var_dump(isset($account->unknown));    // false

// property_exists - checks if property is defined
var_dump(property_exists($account, 'balance'));   // true
var_dump(property_exists($account, 'password'));  // true (even private)
var_dump(property_exists($account, 'unknown'));   // false
?>
```

---

## Accessing Methods

### Calling Methods

```php
<?php
class Calculator {
    public function add($a, $b) {
        return $a + $b;
    }
    
    public function multiply($a, $b) {
        return $a * $b;
    }
}

$calc = new Calculator();

// Call methods with -> operator
echo $calc->add(5, 3);        // 8
echo $calc->multiply(4, 2);   // 8
?>
```

### Methods with Return Values

```php
<?php
class User {
    public $name;
    public $email;
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
        return $this;  // For chaining
    }
    
    public function getInfo() {
        return "{$this->name} ({$this->email})";
    }
}

$user = new User();
$user->name = 'John';
$user->email = 'john@example.com';

echo $user->getName();      // John
echo $user->getInfo();      // John (john@example.com)
echo $user->setName('Jane')->getName();  // Jane (method chaining)
?>
```

### Checking Methods

```php
<?php
class DataProcessor {
    public function process() {}
    private function validate() {}
}

$processor = new DataProcessor();

// Check if method exists
var_dump(method_exists($processor, 'process'));   // true
var_dump(method_exists($processor, 'validate'));  // true (even private)
var_dump(method_exists($processor, 'unknown'));   // false

// Check if callable
$method = 'process';
if (is_callable([$processor, $method])) {
    $processor->$method();
}
?>
```

---

## Object Comparison

### Equality vs Identity

```php
<?php
class User {
    public $id;
    public $name;
}

$user1 = new User();
$user1->id = 1;
$user1->name = 'John';

$user2 = new User();
$user2->id = 1;
$user2->name = 'John';

// == (equality) - same properties
var_dump($user1 == $user2);   // true

// === (identity) - same object
var_dump($user1 === $user2);  // false (different instances)

// Same reference
$user3 = $user1;
var_dump($user1 === $user3);  // true
?>
```

### Comparing Objects

```php
<?php
class Product {
    public $id;
    public $name;
}

$prod1 = new Product();
$prod1->id = 1;

$prod2 = new Product();
$prod2->id = 1;

// Check equality
if ($prod1 == $prod2) {
    echo "Same properties";
}

// Check identity
if ($prod1 === $prod2) {
    echo "Same object";
} else {
    echo "Different objects";
}

// Different classes
$obj = new stdClass();
var_dump($prod1 == $obj);  // false
?>
```

---

## Practical Examples

### Shopping Cart

```php
<?php
class CartItem {
    public $name;
    public $price;
    public $quantity;
    
    public function __construct($name, $price, $quantity) {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }
    
    public function getSubtotal() {
        return $this->price * $this->quantity;
    }
}

class ShoppingCart {
    private $items = [];
    
    public function addItem($name, $price, $quantity) {
        $item = new CartItem($name, $price, $quantity);
        $this->items[] = $item;
    }
    
    public function getTotal() {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getSubtotal();
        }
        return $total;
    }
    
    public function getItems() {
        return $this->items;
    }
}

// Usage
$cart = new ShoppingCart();
$cart->addItem('Laptop', 999.99, 1);
$cart->addItem('Mouse', 25.99, 2);

foreach ($cart->getItems() as $item) {
    echo "{$item->name}: ${$item->getSubtotal()}\n";
}

echo "Total: $" . $cart->getTotal();
?>
```

### Configuration Manager

```php
<?php
class Config {
    private $settings = [];
    
    public function set($key, $value) {
        $this->settings[$key] = $value;
    }
    
    public function get($key, $default = null) {
        return $this->settings[$key] ?? $default;
    }
    
    public function all() {
        return $this->settings;
    }
    
    public function has($key) {
        return isset($this->settings[$key]);
    }
}

// Usage
$config = new Config();
$config->set('db_host', 'localhost');
$config->set('db_name', 'myapp');
$config->set('debug', true);

echo $config->get('db_host');        // localhost
echo $config->get('unknown', 'N/A');  // N/A

var_dump($config->has('db_name'));   // true
?>
```

---

## Common Mistakes

### 1. Confusing Objects with Arrays

```php
<?php
// ❌ Wrong: Treating object like array
class User {
    public $name;
}

$user = new User();
// echo $user['name'];  // Error!

// ✓ Correct: Use arrow operator
$user->name = 'John';
echo $user->name;  // John
?>
```

### 2. Forgetting new Keyword

```php
<?php
// ❌ Wrong: No instantiation
class Car {
    public $color;
}

// $car = Car();  // Error!

// ✓ Correct: Use new
$car = new Car();
$car->color = 'red';
?>
```

### 3. Passing Objects by Value

```php
<?php
// Objects are references
class User {
    public $name;
}

$user1 = new User();
$user1->name = 'John';

$user2 = $user1;  // Reference, not copy
$user2->name = 'Jane';

echo $user1->name;  // Jane (modified!)

// To create a copy, use clone
$user3 = clone $user1;
$user3->name = 'Bob';
echo $user1->name;  // Jane (unchanged)
?>
```

### 4. Wrong Property Access

```php
<?php
// ❌ Wrong: Using $ and -> incorrectly
class Item {
    public $id;
}

$item = new Item();
// echo $item$id;      // Error!
// echo $item->$id;    // Wrong!

// ✓ Correct
$item->id = 1;
echo $item->id;  // 1
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class Library {
    private $books = [];
    
    public function addBook($title, $author, $isbn) {
        $book = (object)[
            'title' => $title,
            'author' => $author,
            'isbn' => $isbn,
            'borrowed' => false,
            'borrowedBy' => null
        ];
        
        $this->books[$isbn] = $book;
        return $book;
    }
    
    public function borrowBook($isbn, $borrower) {
        if (isset($this->books[$isbn])) {
            $book = $this->books[$isbn];
            if (!$book->borrowed) {
                $book->borrowed = true;
                $book->borrowedBy = $borrower;
                return true;
            }
        }
        return false;
    }
    
    public function returnBook($isbn) {
        if (isset($this->books[$isbn])) {
            $book = $this->books[$isbn];
            $book->borrowed = false;
            $book->borrowedBy = null;
            return true;
        }
        return false;
    }
    
    public function getBook($isbn) {
        return $this->books[$isbn] ?? null;
    }
}

// Usage
$library = new Library();
$lib1 = $library->addBook('Clean Code', 'Robert Martin', 'ISBN001');
$lib2 = $library->addBook('Design Patterns', 'Gang of Four', 'ISBN002');

$library->borrowBook('ISBN001', 'John');
$book = $library->getBook('ISBN001');

echo "{$book->title} borrowed by {$book->borrowedBy}";
?>
```

---

## Next Steps

✅ Understand objects  
→ Learn [properties](4-properties.md)  
→ Study [methods/functions](5-function.md)  
→ Explore [constructors](9-constructor.md)
