# PHP Object-Oriented Programming - Introduction & Overview

## Table of Contents
1. [Overview](#overview)
2. [OOP Concepts at a Glance](#oop-concepts-at-a-glance)
3. [Why OOP?](#why-oop)
4. [Core OOP Principles](#core-oop-principles)
5. [Learning Path](#learning-path)
6. [Comparison: Procedural vs OOP](#comparison-procedural-vs-oop)
7. [Prerequisites](#prerequisites)
8. [Next Steps](#next-steps)

---

## Overview

Object-Oriented Programming (OOP) is a programming paradigm that organizes code into reusable objects, each containing data (properties) and behavior (methods). OOP enables you to build scalable, maintainable applications by modeling real-world entities as classes and their interactions.

### Why This Matters

Modern PHP frameworks (Laravel, Symfony, WordPress) all use OOP extensively. Understanding OOP is essential for professional PHP development.

## OOP Concepts at a Glance

### 1. **Class** - Blueprint for Objects
A template that defines what an object looks like and how it behaves.

```php
class Car {
    public $brand;
    
    public function drive() {
        echo "Driving a {$this->brand}";
    }
}
```

### 2. **Object** - Instance of a Class
An individual thing created from a class template.

```php
$myCar = new Car();  // $myCar is an object
$myCar->brand = "Toyota";
$myCar->drive();  // Output: Driving a Toyota
```

### 3. **Property** - Data/Attributes
Variables that belong to an object.

```php
class Person {
    public $name;      // Property
    public $age;       // Property
}
```

### 4. **Method** - Behavior/Functions
Functions that belong to an object.

```php
class Calculator {
    public function add($a, $b) {  // Method
        return $a + $b;
    }
}
```

### 5. **Inheritance** - Code Reuse
A class can inherit properties and methods from a parent class.

```php
class Animal {
    public function eat() { echo "Eating..."; }
}

class Dog extends Animal {
    public function bark() { echo "Woof!"; }
}

$dog = new Dog();
$dog->eat();   // Inherited from Animal
$dog->bark();  // From Dog
```

### 6. **Encapsulation** - Access Control
Controlling which properties/methods are accessible from outside.

```php
class BankAccount {
    private $balance = 0;  // Can't access directly from outside
    
    public function deposit($amount) {
        $this->balance += $amount;
    }
}
```

### 7. **Polymorphism** - Multiple Forms
Objects of different classes responding differently to the same method call.

```php
class Dog {
    public function sound() { echo "Woof!"; }
}

class Cat {
    public function sound() { echo "Meow!"; }
}

// Both respond to sound() differently
$dog = new Dog();
$cat = new Cat();
$dog->sound();  // Woof!
$cat->sound();  // Meow!
```

## Why OOP?

### Without OOP (Procedural)
```php
// Scattered functions and global variables
$users = [];

function addUser($name, $email) {
    global $users;
    $users[] = ['name' => $name, 'email' => $email];
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Hard to maintain, reuse, and test
```

### With OOP
```php
class User {
    private $name;
    private $email;
    
    public function __construct($name, $email) {
        $this->setName($name);
        $this->setEmail($email);
    }
    
    public function setEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email");
        }
        $this->email = $email;
    }
    
    // ... other methods
}

// Easy to test, reuse, maintain
$user = new User("John", "john@example.com");
```

**Benefits:**
- ✅ **Reusability**: Write once, use everywhere
- ✅ **Maintainability**: Clear structure, easier to fix bugs
- ✅ **Scalability**: Handle large codebases
- ✅ **Testability**: Easier to test individual components
- ✅ **Security**: Encapsulation protects data

## Core OOP Principles

### 1. **Encapsulation**
Hide internal details, expose only what's necessary.

**Benefits:**
- Protect data from incorrect manipulation
- Can change internal implementation without affecting users

```php
class Temperature {
    private $celsius;
    
    public function setCelsius($value) {
        if ($value < -273.15) {
            throw new Exception("Invalid temperature");
        }
        $this->celsius = $value;
    }
    
    public function getFahrenheit() {
        return ($this->celsius * 9/5) + 32;
    }
}
```

### 2. **Inheritance**
A class can inherit from a parent class to reuse code.

**Benefits:**
- Avoid code duplication
- Create hierarchies of related classes
- Establish "is-a" relationships

```php
class Vehicle {
    protected $speed = 0;
    
    public function accelerate() {
        $this->speed += 10;
    }
}

class Car extends Vehicle {
    public function honk() {
        echo "Honk!";
    }
}
```

### 3. **Polymorphism**
Objects of different types can respond to the same method call differently.

**Benefits:**
- Flexible, extensible code
- Write code that works with multiple types

```php
interface Animal {
    public function makeSound();
}

class Dog implements Animal {
    public function makeSound() { return "Woof!"; }
}

class Cat implements Animal {
    public function makeSound() { return "Meow!"; }
}

function getAnimalSound(Animal $animal) {
    return $animal->makeSound();  // Works with any Animal
}
```

### 4. **Abstraction**
Reduce complexity by hiding unnecessary details.

**Benefits:**
- Focus on what matters
- Hide implementation details
- Define clear contracts (interfaces)

```php
abstract class Shape {
    abstract public function getArea();  // Must be implemented
}

class Circle extends Shape {
    private $radius;
    
    public function __construct($radius) {
        $this->radius = $radius;
    }
    
    public function getArea() {
        return pi() * $this->radius ** 2;
    }
}
```

## Learning Path

Master OOP progressively:

1. **[Classes & Objects](2-class.md)** - Define and instantiate classes
2. **[Properties](4-properties.md)** - Add data to objects
3. **[Methods](5-function.md)** - Add behavior to objects
4. **[Constructors](9-constructor.md)** - Initialize objects
5. **[Visibility/Access Modifiers](14-visibility.md)** - public, private, protected
6. **[Inheritance](11-inheritance.md)** - Extend classes
7. **[Namespaces](12-namespace.md)** - Organize code
8. **[Interfaces](23-interface.md)** - Define contracts
9. **[Abstract Classes](20-abstract-class.md)** - Incomplete classes
10. **[Traits](25-trait.md)** - Code reuse without inheritance
11. **[Magic Methods](34-magic-function.md)** - Special methods like __get, __set
12. **[Static Members](28-static-keyword.md)** - Class-level properties/methods
13. **[Exceptions](38-exception.md)** - Error handling
14. **[Type Hints & Casting](19-type-check-and-casts.md)** - Type safety

## Comparison: Procedural vs OOP

### Procedural Approach
```php
// Functions scattered around
function calculateTotal($items) {
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'];
    }
    return $total;
}

function applyDiscount($total, $percent) {
    return $total * (1 - $percent / 100);
}

// Usage
$items = [
    ['price' => 10],
    ['price' => 20],
];

$total = calculateTotal($items);
$final = applyDiscount($total, 10);
```

### OOP Approach
```php
class ShoppingCart {
    private $items = [];
    
    public function addItem($price) {
        $this->items[] = $price;
    }
    
    public function getTotal() {
        return array_sum($this->items);
    }
    
    public function applyDiscount($percent) {
        $discount = $this->getTotal() * ($percent / 100);
        return $this->getTotal() - $discount;
    }
}

// Usage
$cart = new ShoppingCart();
$cart->addItem(10);
$cart->addItem(20);
echo $cart->getTotal();        // 30
echo $cart->applyDiscount(10); // 27
```

**Advantages of OOP:**
- State is encapsulated in the object
- Related functions are grouped together
- Easier to test
- More intuitive to model real-world entities

## Prerequisites

Before diving into OOP, you should understand:

✅ **From Basics:**
- Variables and data types
- Functions and parameters
- Arrays and loops
- Control structures (if/else)
- String operations

✅ **Core Concepts:**
- How functions work
- Scope and variable lifetime
- Passing variables by value vs reference

✅ **Mindset:**
- Think in terms of objects, not just functions
- Model real-world entities as classes
- Think about relationships between objects

## Real-World OOP Example

```php
class Employee {
    private $name;
    private $salary;
    private $department;
    
    public function __construct($name, $salary, $department) {
        $this->name = $name;
        $this->salary = $salary;
        $this->department = $department;
    }
    
    public function raiseSalary($percent) {
        $this->salary *= (1 + $percent / 100);
    }
    
    public function getSalary() {
        return $this->salary;
    }
    
    public function getInfo() {
        return "{$this->name} works in {$this->department}";
    }
}

// Usage
$employee = new Employee("Alice", 50000, "Engineering");
$employee->raiseSalary(10);  // 10% raise
echo $employee->getInfo();    // Alice works in Engineering
```

## Next Steps

1. Start with [Classes](2-class.md) to understand the basics
2. Progress through [Objects](3-object.md)
3. Add [Properties](4-properties.md)  
4. Implement [Methods](5-function.md)
5. Continue building from there

Each topic builds on the previous one, so follow the sequence for best learning!

## Quick Reference

| Concept | Symbol/Keyword | Purpose |
|---------|----------|---------|
| Class | `class Name {}` | Define a blueprint |
| Object | `new ClassName()` | Create an instance |
| Property | `$prop` inside class | Store data |
| Method | `public function()` | Define behavior |
| Constructor | `__construct()` | Initialize objects |
| Inheritance | `extends` | Reuse code |
| Interface | `interface` | Define contract |
| Abstract | `abstract` | Incomplete classes |
| Encapsulation | `public/private/protected` | Control access |

## Resources

- **PHP Manual - OOP**: [php.net/manual/en/language.oop5.php](https://www.php.net/manual/en/language.oop5.php)
- **PSR-1 (Standards)**: [PSR-1 Basic Coding Standard](https://www.php-fig.org/psr/psr-1/)
- **Design Patterns**: [refactoring.guru](https://refactoring.guru/design-patterns/php)
