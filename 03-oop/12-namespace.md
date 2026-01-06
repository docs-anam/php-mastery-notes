# Namespaces in PHP

## Table of Contents
1. [Overview](#overview)
2. [Declaring Namespaces](#declaring-namespaces)
3. [Namespace Basics](#namespace-basics)
4. [Multiple Namespaces](#multiple-namespaces)
5. [Global Namespace](#global-namespace)
6. [Sub-namespaces](#sub-namespaces)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

Namespaces provide a way to encapsulate code and avoid naming conflicts. They allow you to organize code into logical groups and use the same class names in different namespaces. Every PHP file can have at most one namespace declaration, which must be the first statement (except for `declare` statements).

**Key Concepts:**
- Namespaces prevent naming collisions
- Namespace declaration must be first (except declare)
- Case-insensitive but convention is to follow file structure
- Sub-namespaces use backslash separator
- Global namespace has no explicit declaration
- `use` keyword imports namespaces

---

## Declaring Namespaces

### Basic Namespace

```php
<?php
// File: src/User.php
namespace App;

class User {
    public $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function greet() {
        return "Hello, I'm {$this->name}";
    }
}

// When using classes from same namespace, no prefix needed
$user = new User('Alice');
echo $user->greet();
?>
```

### Fully Qualified Names

```php
<?php
// File: src/Config.php
namespace MyApp\Config;

class Settings {
    private $values = [];
    
    public function set($key, $value) {
        $this->values[$key] = $value;
    }
    
    public function get($key) {
        return $this->values[$key] ?? null;
    }
}

// Access with fully qualified name
$settings = new \MyApp\Config\Settings();
$settings->set('debug', true);
?>
```

### Namespace Declaration Rules

```php
<?php
// ✓ Correct: namespace is first statement
namespace MyApp;

// Must be at the very top (except declare)

class FirstClass {}

// ✗ Wrong: not showing here but namespace must be first
// namespace AnotherNamespace;  // Error!
// declare(strict_types=1);  // OK - declare can be before namespace

// ✓ Correct: declare with namespace
declare(strict_types=1);
namespace MyApp;

class AnotherClass {}
?>
```

---

## Namespace Basics

### Accessing Classes in Same Namespace

```php
<?php
namespace App\Services;

// Both classes in same namespace
class Database {
    public function connect() {
        echo "Connected to database\n";
    }
}

class UserService {
    private $db;
    
    public function __construct() {
        // Access without namespace prefix
        $this->db = new Database();
    }
    
    public function getUser($id) {
        $this->db->connect();
        return ['id' => $id, 'name' => 'User ' . $id];
    }
}

// Usage from same namespace
$service = new UserService();
$user = $service->getUser(1);
print_r($user);
?>
```

### Accessing Global Namespace

```php
<?php
// In a namespace, to access global functions/classes, use backslash
namespace MyApp;

class MyException extends \Exception {
    // Extends PHP's global Exception class
}

class Logger {
    public function log($message) {
        // Access global function with backslash
        $date = \date('Y-m-d H:i:s');
        echo "[$date] $message\n";
    }
}

// Usage
$logger = new Logger();
$logger->log('Application started');

try {
    throw new MyException('Custom error');
} catch (MyException $e) {
    echo $e->getMessage();
}
?>
```

### Relative Namespace Access

```php
<?php
namespace App\Models;

class User {
    public function getData() {
        return 'User data';
    }
}

namespace App\Services;

// To access User from Models in Services namespace
class UserService {
    private $user;
    
    public function __construct() {
        // Option 1: Relative (won't work, would look for App\Services\User)
        // $this->user = new User();  // Error!
        
        // Option 2: Absolute path
        $this->user = new \App\Models\User();
    }
}
?>
```

---

## Multiple Namespaces

### Multiple Namespaces in One File (Not Recommended)

```php
<?php
namespace App\Models {
    class User {
        public function getName() {
            return 'User Model';
        }
    }
}

namespace App\Services {
    class UserService {
        public function getUser() {
            // Must use full path
            return new \App\Models\User();
        }
    }
}

// From global namespace
$service = new \App\Services\UserService();
$user = $service->getUser();
echo $user->getName();
?>
```

### Organizing by File (Recommended)

```php
// File: src/Models/User.php
<?php
namespace App\Models;

class User {
    public $id;
    public $name;
    
    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
}

// File: src/Services/UserService.php
<?php
namespace App\Services;

use App\Models\User;

class UserService {
    public function createUser($name) {
        return new User(uniqid(), $name);
    }
}

// File: index.php (global namespace)
<?php
require 'vendor/autoload.php';

use App\Services\UserService;

$service = new UserService();
$user = $service->createUser('Alice');
echo $user->name;
?>
```

---

## Global Namespace

### Defining in Global Namespace

```php
<?php
// No namespace declaration = global namespace
class GlobalClass {
    public function hello() {
        return 'From global namespace';
    }
}

function globalFunction() {
    return 'Global function';
}

// Accessible directly
$obj = new GlobalClass();
echo globalFunction();
?>
```

### Accessing Global Classes from Namespace

```php
<?php
// File: global_functions.php
function Helper($x) {
    return $x * 2;
}

// File: app.php
namespace MyApp;

class Processor {
    public function process($value) {
        // Must use backslash for global function
        return \Helper($value);
    }
}

$proc = new Processor();
echo $proc->process(5);  // 10
?>
```

---

## Sub-namespaces

### Organizing with Sub-namespaces

```php
<?php
// File: src/Database/Connection.php
namespace App\Database;

class Connection {
    public function connect() {
        echo "Connecting to database\n";
    }
}

// File: src/Database/Query.php
namespace App\Database;

class Query {
    private $connection;
    
    public function __construct(Connection $conn) {
        $this->connection = $conn;
    }
}

// File: src/Http/Request.php
namespace App\Http;

class Request {
    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
}

// Usage
use App\Database\Connection;
use App\Database\Query;
use App\Http\Request;

$conn = new Connection();
$query = new Query($conn);
$request = new Request();
?>
```

### Deep Nesting

```php
<?php
// Very deep nesting (avoid if possible)
namespace App\Services\Payment\Processors\CreditCard;

class Processor {
    public function process($amount) {
        echo "Processing credit card payment: \$$amount";
    }
}

// Usage with full path
$processor = new \App\Services\Payment\Processors\CreditCard\Processor();
$processor->process(99.99);

// Better with use statement
use App\Services\Payment\Processors\CreditCard\Processor;
$proc = new Processor();
?>
```

---

## Practical Examples

### Application Architecture with Namespaces

```php
<?php
// File: src/Config/Database.php
namespace App\Config;

class Database {
    public static function getConnection() {
        return 'mysql:host=localhost;dbname=myapp';
    }
}

// File: src/Database/Connection.php
namespace App\Database;

use App\Config\Database;

class Connection {
    private $pdo;
    
    public function __construct() {
        $dsn = Database::getConnection();
        // Connect using DSN
    }
}

// File: src/Models/User.php
namespace App\Models;

use App\Database\Connection;

class User {
    private $db;
    
    public function __construct() {
        $this->db = new Connection();
    }
    
    public function find($id) {
        // Query user
        return ['id' => $id, 'name' => 'User'];
    }
}

// File: index.php
use App\Models\User;

$user = new User();
$data = $user->find(1);
print_r($data);
?>
```

### API Namespace Organization

```php
<?php
// File: src/Api/V1/Controller.php
namespace App\Api\V1;

abstract class Controller {
    protected function success($data, $message = 'Success') {
        return [
            'status' => 'success',
            'data' => $data,
            'message' => $message
        ];
    }
    
    protected function error($message, $code = 400) {
        return [
            'status' => 'error',
            'message' => $message,
            'code' => $code
        ];
    }
}

// File: src/Api/V1/UserController.php
namespace App\Api\V1;

class UserController extends Controller {
    public function getUser($id) {
        $user = ['id' => $id, 'name' => 'Alice'];
        return $this->success($user);
    }
    
    public function createUser($data) {
        if (empty($data['name'])) {
            return $this->error('Name is required');
        }
        return $this->success($data, 'User created');
    }
}

// API endpoint
use App\Api\V1\UserController;

$controller = new UserController();
header('Content-Type: application/json');
echo json_encode($controller->getUser(1));
?>
```

---

## Common Mistakes

### 1. Forgetting Namespace in Class

```php
<?php
// ❌ Wrong: Creating globally while expecting namespaced
// File: User.php (no namespace declared)
class User {}

// Then trying to use namespaced
namespace App;
$user = new User();  // Error! Looks for App\User

// ✓ Correct: Declare namespace
// File: App/User.php
namespace App;

class User {}

// Then use
$user = new User();  // OK
?>
```

### 2. Not Using Backslash for Global Namespace

```php
<?php
namespace MyApp;

// ❌ Wrong: Forgot backslash
class MyException extends Exception {}  // Looking for MyApp\Exception

// ✓ Correct: Use backslash
class MyException extends \Exception {}  // PHP's global Exception
?>
```

### 3. Inconsistent File Structure

```php
<?php
// ❌ Wrong: Namespace doesn't match file structure
// File: src/User.php
namespace App\Repository;  // Doesn't match folder structure

class User {}

// ✓ Correct: Match folder structure
// File: src/App/Repository/User.php
namespace App\Repository;

class User {}
?>
```

---

## Complete Working Example

```php
<?php
// E-Commerce Application with Namespaces

// File: src/Product/Product.php
namespace App\Product;

class Product {
    public $id;
    public $name;
    public $price;
    
    public function __construct($id, $name, $price) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }
}

// File: src/Cart/Cart.php
namespace App\Cart;

use App\Product\Product;

class Cart {
    private $items = [];
    private $total = 0;
    
    public function add(Product $product, $quantity = 1) {
        $this->items[] = [
            'product' => $product,
            'quantity' => $quantity
        ];
        $this->total += $product->price * $quantity;
    }
    
    public function getItems() {
        return $this->items;
    }
    
    public function getTotal() {
        return $this->total;
    }
}

// File: src/Order/Order.php
namespace App\Order;

use App\Cart\Cart;

class Order {
    private $cart;
    private $status = 'pending';
    
    public function __construct(Cart $cart) {
        $this->cart = $cart;
    }
    
    public function checkout() {
        $this->status = 'completed';
        return [
            'status' => $this->status,
            'total' => $this->cart->getTotal(),
            'items' => count($this->cart->getItems())
        ];
    }
}

// File: index.php
use App\Product\Product;
use App\Cart\Cart;
use App\Order\Order;

$product1 = new Product(1, 'Laptop', 999.99);
$product2 = new Product(2, 'Mouse', 25.99);

$cart = new Cart();
$cart->add($product1);
$cart->add($product2, 2);

$order = new Order($cart);
$result = $order->checkout();

echo "Order Status: " . $result['status'] . "\n";
echo "Total: $" . number_format($result['total'], 2) . "\n";
echo "Items: " . $result['items'] . "\n";
?>
```

---

## Cross-References

- **Related Topic: [Importing Namespaces (use)](13-import.md)** - Using the use keyword
- **Related Topic: [Classes](2-class.md)** - Class organization
- **Related Topic: [Traits](25-trait.md)** - Organizing reusable code
- **Related Topic: [Autoloading](#)** - PSR-4 autoloading with namespaces
