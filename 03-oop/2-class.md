# Classes and Objects in PHP

## Table of Contents
1. [Overview](#overview)
2. [Creating Classes](#creating-classes)
3. [Class Properties](#class-properties)
4. [Class Methods](#class-methods)
5. [Instantiation](#instantiation)
6. [Practical Examples](#practical-examples)
7. [Common Mistakes](#common-mistakes)

---

## Overview

A **class** is a blueprint for creating objects. It defines properties (data) and methods (functions).

**Key concepts:**
- Classes are templates
- Objects are instances of classes
- Properties store data
- Methods perform actions

---

## Creating Classes

### Basic Class Structure

```php
<?php
// Define a class
class Car {
    // Properties
    public $color;
    public $brand;
    
    // Methods
    public function drive() {
        return "Car is driving";
    }
}

// Create an object (instance)
$car = new Car();
echo $car->drive();  // Car is driving
?>
```

### Class with Multiple Methods

```php
<?php
class Calculator {
    public function add($a, $b) {
        return $a + $b;
    }
    
    public function subtract($a, $b) {
        return $a - $b;
    }
    
    public function multiply($a, $b) {
        return $a * $b;
    }
}

$calc = new Calculator();
echo $calc->add(5, 3);      // 8
echo $calc->multiply(4, 2);  // 8
?>
```

### Class Definition Rules

```php
<?php
// ✓ Class name conventions
class MyClass {}           // PascalCase (recommended)
class myclass {}          // lowercase (works but not recommended)
class My_Class {}         // snake_case (not recommended)

// ✓ Multiple classes in one file (but not recommended)
class User {}
class Product {}

// Properties must have visibility
class Person {
    public $name;         // Public (accessible anywhere)
    private $age;         // Private (only in this class)
    protected $email;     // Protected (in this class and children)
}
?>
```

---

## Class Properties

### Declaring Properties

```php
<?php
class User {
    // Typed properties (PHP 7.4+)
    public string $name;
    public int $age;
    public float $salary;
    public array $roles;
    public bool $active;
    
    // Nullable properties
    public ?string $email = null;
    
    // Property with default value
    public string $status = 'active';
}

$user = new User();
$user->name = 'John';
$user->age = 30;
echo $user->name;  // John
?>
```

### Visibility Levels

```php
<?php
class Account {
    public $username;      // Accessible anywhere
    private $password;     // Only in this class
    protected $balance;    // This class and subclasses
    
    public function setPassword($pass) {
        $this->password = $pass;  // Can access private inside class
    }
    
    public function getPassword() {
        return $this->password;
    }
}

$account = new Account();
$account->username = 'john';  // OK
// $account->password = 'secret';  // Error!
// $account->balance = 1000;  // Error!
?>
```

### Readonly Properties (PHP 8.1+)

```php
<?php
class Product {
    public readonly int $id;      // Can't be changed after initialization
    public readonly string $name;
    
    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
}

$product = new Product(1, 'Laptop');
echo $product->id;      // 1
// $product->id = 2;    // Error! Can't modify readonly
?>
```

---

## Class Methods

### Method Definition

```php
<?php
class Calculator {
    // Simple method
    public function add($a, $b) {
        return $a + $b;
    }
    
    // Method with type hints
    public function multiply(int $a, int $b): int {
        return $a * $b;
    }
    
    // Method returning nothing
    public function display(string $text): void {
        echo $text;
    }
    
    // Method with optional parameters
    public function greet(string $name, string $greeting = 'Hello'): string {
        return "$greeting, $name!";
    }
}

$calc = new Calculator();
echo $calc->add(5, 3);              // 8
echo $calc->greet('John');          // Hello, John!
echo $calc->greet('Jane', 'Hi');    // Hi, Jane!
?>
```

### Using $this

```php
<?php
class User {
    public $name;
    public $email;
    
    public function setInfo($name, $email) {
        $this->name = $name;      // $this refers to current object
        $this->email = $email;
    }
    
    public function getInfo() {
        return $this->name . ' (' . $this->email . ')';
    }
}

$user = new User();
$user->setInfo('John', 'john@example.com');
echo $user->getInfo();  // John (john@example.com)
?>
```

---

## Instantiation

### Creating Objects

```php
<?php
class Dog {
    public $name;
    public $breed;
    
    public function bark() {
        return $this->name . ' barks!';
    }
}

// Create instance
$dog = new Dog();
$dog->name = 'Rex';
$dog->breed = 'Golden Retriever';

// Another instance
$dog2 = new Dog();
$dog2->name = 'Max';
$dog2->breed = 'Labrador';

echo $dog->bark();   // Rex barks!
echo $dog2->bark();  // Max barks!
?>
```

### Multiple Instances

```php
<?php
class BankAccount {
    public $owner;
    public $balance = 0;
    
    public function deposit($amount) {
        $this->balance += $amount;
        return "Deposited: $amount";
    }
    
    public function withdraw($amount) {
        if ($amount <= $this->balance) {
            $this->balance -= $amount;
            return "Withdrawn: $amount";
        }
        return "Insufficient funds";
    }
}

// Create multiple accounts
$account1 = new BankAccount();
$account1->owner = 'Alice';
$account1->deposit(1000);

$account2 = new BankAccount();
$account2->owner = 'Bob';
$account2->deposit(500);

echo $account1->owner . ': $' . $account1->balance;  // Alice: $1000
echo $account2->owner . ': $' . $account2->balance;  // Bob: $500
?>
```

---

## Practical Examples

### User Management Class

```php
<?php
class UserManager {
    private $users = [];
    
    public function addUser($id, $name, $email) {
        $this->users[$id] = [
            'name' => $name,
            'email' => $email,
            'created' => date('Y-m-d H:i:s')
        ];
    }
    
    public function getUser($id) {
        return $this->users[$id] ?? null;
    }
    
    public function getAllUsers() {
        return $this->users;
    }
    
    public function updateUser($id, $name, $email) {
        if (isset($this->users[$id])) {
            $this->users[$id]['name'] = $name;
            $this->users[$id]['email'] = $email;
            return true;
        }
        return false;
    }
    
    public function deleteUser($id) {
        if (isset($this->users[$id])) {
            unset($this->users[$id]);
            return true;
        }
        return false;
    }
}

// Usage
$manager = new UserManager();
$manager->addUser(1, 'John', 'john@example.com');
$manager->addUser(2, 'Jane', 'jane@example.com');

print_r($manager->getUser(1));
print_r($manager->getAllUsers());
?>
```

### Product Inventory Class

```php
<?php
class Inventory {
    private $items = [];
    
    public function addItem($sku, $name, $quantity, $price) {
        $this->items[$sku] = [
            'name' => $name,
            'quantity' => $quantity,
            'price' => $price,
            'total_value' => $quantity * $price
        ];
    }
    
    public function getStock($sku) {
        return $this->items[$sku]['quantity'] ?? 0;
    }
    
    public function getTotalValue() {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['total_value'];
        }
        return $total;
    }
    
    public function reduceStock($sku, $qty) {
        if (isset($this->items[$sku]) && $this->items[$sku]['quantity'] >= $qty) {
            $this->items[$sku]['quantity'] -= $qty;
            $this->items[$sku]['total_value'] = $this->items[$sku]['quantity'] * $this->items[$sku]['price'];
            return true;
        }
        return false;
    }
}

$inventory = new Inventory();
$inventory->addItem('SKU001', 'Laptop', 10, 999.99);
$inventory->addItem('SKU002', 'Mouse', 50, 25.99);

echo 'Total Value: $' . $inventory->getTotalValue();
?>
```

---

## Common Mistakes

### 1. Forgetting $this

```php
<?php
// ❌ Wrong: Missing $this
class User {
    public $name;
    
    public function getName() {
        return name;  // Error: undefined constant
    }
}

// ✓ Correct: Use $this
class User {
    public $name;
    
    public function getName() {
        return $this->name;  // Correct
    }
}
?>
```

### 2. Confusing Class and Instance

```php
<?php
// ❌ Wrong: Modifying class instead of instance
class Counter {
    public $count = 0;
}

$c1 = new Counter();
$c1->count = 5;

$c2 = new Counter();
echo $c2->count;  // 0 (not 5 - separate instance)

// ✓ Correct: Understand that each instance is separate
?>
```

### 3. Wrong Visibility

```php
<?php
// ❌ Wrong: Accessing private property
class Account {
    private $password;
    
    public function setPassword($pass) {
        $this->password = $pass;
    }
}

$account = new Account();
// echo $account->password;  // Error!

// ✓ Correct: Use public method
echo $account->setPassword('secret');
?>
```

### 4. Property Without Visibility

```php
<?php
// ❌ Wrong: No visibility modifier (PHP 7.4+ requires it with typed properties)
class User {
    string $name;  // Error in PHP 7.4+
}

// ✓ Correct: Add visibility
class User {
    public string $name;
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class BankAccount {
    private string $owner;
    private float $balance;
    private array $transactions = [];
    
    public function __construct(string $owner, float $initialBalance = 0) {
        $this->owner = $owner;
        $this->balance = $initialBalance;
        $this->recordTransaction('Initial', $initialBalance);
    }
    
    public function deposit(float $amount): bool {
        if ($amount <= 0) {
            return false;
        }
        
        $this->balance += $amount;
        $this->recordTransaction('Deposit', $amount);
        return true;
    }
    
    public function withdraw(float $amount): bool {
        if ($amount <= 0 || $amount > $this->balance) {
            return false;
        }
        
        $this->balance -= $amount;
        $this->recordTransaction('Withdrawal', -$amount);
        return true;
    }
    
    public function getBalance(): float {
        return $this->balance;
    }
    
    public function getOwner(): string {
        return $this->owner;
    }
    
    public function getTransactions(): array {
        return $this->transactions;
    }
    
    private function recordTransaction(string $type, float $amount): void {
        $this->transactions[] = [
            'type' => $type,
            'amount' => $amount,
            'balance' => $this->balance,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}

// Usage
$account = new BankAccount('John Doe', 1000);
$account->deposit(500);
$account->withdraw(200);

echo "Owner: " . $account->getOwner() . "\n";
echo "Balance: $" . $account->getBalance() . "\n";
print_r($account->getTransactions());
?>
```

---

## Next Steps

✅ Understand classes and objects  
→ Learn [properties](4-properties.md)  
→ Study [methods/functions](5-function.md)  
→ Explore [constructors](9-constructor.md)
