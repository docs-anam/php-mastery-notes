# Importing Namespaces (use keyword)

## Table of Contents
1. [Overview](#overview)
2. [Basic use Statement](#basic-use-statement)
3. [Importing Classes](#importing-classes)
4. [Importing Functions](#importing-functions)
5. [Importing Constants](#importing-constants)
6. [Aliasing with as](#aliasing-with-as)
7. [Group use Statements](#group-use-statements)
8. [Practical Examples](#practical-examples)
9. [Common Mistakes](#common-mistakes)
10. [Complete Working Example](#complete-working-example)
11. [Cross-References](#cross-references)

---

## Overview

The `use` statement allows you to import classes, functions, and constants from other namespaces into the current namespace. This makes code more readable by avoiding fully qualified names throughout the file. The `use` statement must appear after the namespace declaration and before any other code.

**Key Concepts:**
- `use` imports classes, functions, or constants
- Must appear after namespace declaration
- Shortens fully qualified names
- Can use `as` keyword to create aliases
- Only affects current file
- Case-insensitive but follow conventions

---

## Basic use Statement

### Importing Classes

```php
<?php
namespace App;

// Import class from another namespace
use App\Models\User;
use App\Models\Product;
use App\Services\UserService;

// Now can use short names
$user = new User('Alice');
$product = new Product(1, 'Laptop', 999.99);
$service = new UserService();

echo $user->name;
?>
```

### Without use - Fully Qualified

```php
<?php
namespace App;

// Without use, must use full path
$user = new \App\Models\User('Alice');
$product = new \App\Models\Product(1, 'Laptop', 999.99);
$service = new \App\Services\UserService();

// More verbose and harder to read
echo $user->name;
?>
```

### Accessing Same Namespace

```php
<?php
namespace App\Services;

// Classes in same namespace don't need use
class Database {
    public function connect() {
        return 'Connected';
    }
}

class UserService {
    private $db;
    
    public function __construct() {
        // Direct access to same namespace
        $this->db = new Database();
    }
}
?>
```

---

## Importing Classes

### Single Class Import

```php
<?php
namespace App\Controllers;

use App\Services\UserService;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Database\Eloquent\Model;

class UserController {
    private $userService;
    
    public function __construct() {
        $this->userService = new UserService();
    }
    
    public function handleRequest(Request $request) {
        // Use imported classes directly
        return $this->userService->getUser(1);
    }
}
?>
```

### Nested Class Import

```php
<?php
namespace App\Api;

use App\Database\Connection\Manager;
use App\Services\Payment\Processors\CreditCard;

class PaymentController {
    private $manager;
    private $processor;
    
    public function __construct() {
        $this->manager = new Manager();
        $this->processor = new CreditCard();
    }
}
?>
```

### Importing from Different Projects

```php
<?php
namespace MyApp;

// Import from vendor packages
use PDO;
use DateTime;
use ArrayObject;
use Composer\Autoload\ClassLoader;

class Application {
    private $db;
    private $startTime;
    
    public function __construct() {
        $this->db = new PDO('sqlite::memory:');
        $this->startTime = new DateTime();
    }
}
?>
```

---

## Importing Functions

### Importing Built-in Functions

```php
<?php
namespace App\Utilities;

// Import functions
use function strlen;
use function json_encode;
use function array_merge;

class StringHelper {
    public function getLength($str) {
        // Use function without namespace prefix
        return strlen($str);
    }
    
    public function toJson($data) {
        return json_encode($data);
    }
    
    public function merge(...$arrays) {
        return array_merge(...$arrays);
    }
}

$helper = new StringHelper();
echo $helper->getLength('Hello');  // 5
?>
```

### Custom Function Import

```php
<?php
// File: app/helpers.php
namespace App\Helpers;

function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

// File: app/services.php
namespace App\Services;

use function App\Helpers\formatPrice;
use function App\Helpers\formatDate;

class ProductService {
    public function displayProduct($product) {
        echo $product['name'] . ' - ' . formatPrice($product['price']) . "\n";
    }
    
    public function displayDate($date) {
        echo formatDate($date) . "\n";
    }
}

$service = new ProductService();
$service->displayProduct(['name' => 'Laptop', 'price' => 999.99]);
?>
```

---

## Importing Constants

### Importing Class Constants

```php
<?php
namespace App\Config;

// Define constants in namespace
const APP_NAME = 'MyApplication';
const APP_VERSION = '1.0.0';
const DEBUG_MODE = true;

// Import constants
use const App\Config\APP_NAME;
use const App\Config\APP_VERSION;
use const App\Config\DEBUG_MODE;

class Settings {
    public function getInfo() {
        return APP_NAME . ' v' . APP_VERSION;
    }
    
    public function isDebug() {
        return DEBUG_MODE;
    }
}

echo new Settings()->getInfo();
?>
```

### Global Constants

```php
<?php
namespace MyApp;

// Using global constants
// PHP_VERSION, PHP_OS, true, false, null don't need importing
class SystemInfo {
    public function getInfo() {
        return [
            'version' => PHP_VERSION,
            'os' => PHP_OS,
            'debug' => true  // Global constant
        ];
    }
}
?>
```

---

## Aliasing with as

### Class Aliasing

```php
<?php
namespace App;

// Importing with alias to avoid conflicts
use App\Models\User as UserModel;
use App\ViewModels\User as UserViewModel;

class Controller {
    public function process() {
        // Can use both with different names
        $model = new UserModel('Alice');
        $viewModel = new UserViewModel();
        
        return [
            'model' => $model,
            'view' => $viewModel
        ];
    }
}
?>
```

### Function Aliasing

```php
<?php
namespace App\Services;

// Alias conflicting functions
use function strlen as stringLength;
use function count as countItems;
use function array_map as mapArray;

class DataProcessor {
    public function process($data) {
        echo "String length: " . stringLength("Hello") . "\n";
        echo "Item count: " . countItems($data) . "\n";
        
        $mapped = mapArray(function($item) {
            return strtoupper($item);
        }, $data);
        
        return $mapped;
    }
}

$processor = new DataProcessor();
$processor->process(['apple', 'banana', 'cherry']);
?>
```

### Constant Aliasing

```php
<?php
namespace App;

// Alias constants with same-named imports
use const App\Config\ERROR as CONFIG_ERROR;
use const App\Errors\ERROR as SYSTEM_ERROR;

class ErrorHandler {
    public function handleError($code) {
        if ($code === CONFIG_ERROR) {
            echo "Configuration error";
        } elseif ($code === SYSTEM_ERROR) {
            echo "System error";
        }
    }
}
?>
```

---

## Group use Statements

### Importing Multiple from Same Namespace

```php
<?php
namespace App\Controllers;

// Instead of:
// use App\Services\UserService;
// use App\Services\ProductService;
// use App\Services\OrderService;

// Use group import (PHP 5.6+)
use App\Services\{UserService, ProductService, OrderService};

class DashboardController {
    private $userService;
    private $productService;
    private $orderService;
    
    public function __construct() {
        $this->userService = new UserService();
        $this->productService = new ProductService();
        $this->orderService = new OrderService();
    }
}
?>
```

### Group Import with Mixed Types

```php
<?php
namespace App;

// Mix classes and functions in group import
use App\Services\{
    UserService,
    ProductService,
    function formatPrice,
    const MAX_ITEMS
};

class Application {
    public function run() {
        $userService = new UserService();
        echo formatPrice(99.99);
        echo "Max items: " . MAX_ITEMS;
    }
}
?>
```

### Multiple Group Imports

```php
<?php
namespace MyApp;

// Multiple groups
use App\Services\{UserService, ProductService};
use App\Models\{User, Product, Order};
use App\Utilities\{Logger, Cache};

class Application {
    private $users;
    private $products;
    private $logger;
    
    public function __construct() {
        $this->users = new UserService();
        $this->products = new ProductService();
        $this->logger = new Logger();
    }
}
?>
```

---

## Practical Examples

### API Endpoint with Multiple Imports

```php
<?php
namespace App\Api\V1;

use App\Models\{User, Product, Order};
use App\Services\{UserService, ProductService, OrderService};
use App\Exceptions\{ValidationException, ResourceNotFoundException};
use Exception;

class ProductController {
    private $productService;
    
    public function __construct() {
        $this->productService = new ProductService();
    }
    
    public function show($id) {
        try {
            $product = $this->productService->find($id);
            
            if (!$product) {
                throw new ResourceNotFoundException("Product not found");
            }
            
            return ['success' => true, 'data' => $product];
        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        } catch (Exception $e) {
            return ['error' => 'Server error'];
        }
    }
}
?>
```

### Framework Bootstrap with use

```php
<?php
namespace Application;

use PDO;
use RuntimeException;
use Dotenv\Dotenv;
use App\Config\{Database, Cache};
use App\Services\{Logger, Router};
use App\Middleware\{AuthMiddleware, CorsMiddleware};

class Application {
    private $container = [];
    
    public function bootstrap() {
        $this->registerBindings();
        $this->registerMiddleware();
        $this->run();
    }
    
    private function registerBindings() {
        $this->container['db'] = new PDO(Database::getDsn());
        $this->container['cache'] = new Cache();
        $this->container['logger'] = new Logger();
    }
    
    private function registerMiddleware() {
        $this->container['auth'] = new AuthMiddleware();
        $this->container['cors'] = new CorsMiddleware();
    }
    
    private function run() {
        $router = new Router($this->container);
        $router->dispatch();
    }
}

$app = new Application();
$app->bootstrap();
?>
```

---

## Common Mistakes

### 1. Importing from Same Namespace

```php
<?php
namespace App\Services;

// ❌ Wrong: Unnecessary import from same namespace
use App\Services\Database;

// ✓ Correct: Direct access in same namespace
class UserService {
    private $db;
    
    public function __construct() {
        $this->db = new Database();  // Direct, no use needed
    }
}
?>
```

### 2. use Statement in Wrong Position

```php
<?php
// ❌ Wrong: use after code
namespace App;

class SomeClass {}

use App\Services\UserService;  // Error! use must be after namespace

// ✓ Correct: use right after namespace
namespace App;

use App\Services\UserService;

class SomeClass {}
?>
```

### 3. Forgetting Alias with Conflicts

```php
<?php
namespace MyApp;

// ❌ Wrong: Two classes with same name without alias
use DateTime;
use App\Models\DateTime;  // Error! Name conflict

// ✓ Correct: Use alias
use DateTime as PHPDateTime;
use App\Models\DateTime;

class Application {
    public function process() {
        $phpDate = new PHPDateTime();
        $customDate = new DateTime();
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// File: src/API/UserEndpoint.php
namespace App\Api;

use App\Models\User;
use App\Services\{UserService, EmailService, ValidationService};
use App\Repositories\UserRepository;
use App\Exceptions\{ValidationException, ResourceNotFoundException};
use function json_encode;
use const App\Config\MAX_RESULTS;

class UserEndpoint {
    private $userService;
    private $emailService;
    private $validationService;
    private $repository;
    
    public function __construct() {
        $this->userService = new UserService();
        $this->emailService = new EmailService();
        $this->validationService = new ValidationService();
        $this->repository = new UserRepository();
    }
    
    public function list() {
        try {
            $users = $this->repository->all(MAX_RESULTS);
            return $this->response('success', $users);
        } catch (Exception $e) {
            return $this->response('error', null, $e->getMessage());
        }
    }
    
    public function create($data) {
        try {
            $this->validationService->validate($data, [
                'name' => 'required|string',
                'email' => 'required|email'
            ]);
            
            $user = $this->userService->create($data);
            $this->emailService->sendWelcome($user);
            
            return $this->response('success', $user, 'User created');
        } catch (ValidationException $e) {
            return $this->response('error', null, $e->getMessage());
        }
    }
    
    public function show($id) {
        try {
            $user = $this->repository->find($id);
            
            if (!$user) {
                throw new ResourceNotFoundException("User not found");
            }
            
            return $this->response('success', $user);
        } catch (ResourceNotFoundException $e) {
            return $this->response('error', null, $e->getMessage(), 404);
        }
    }
    
    private function response($status, $data = null, $message = '', $code = 200) {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message,
            'code' => $code
        ];
    }
}

// Usage
$endpoint = new UserEndpoint();
header('Content-Type: application/json');
echo json_encode($endpoint->list());
?>
```

---

## Cross-References

- **Related Topic: [Namespaces](12-namespace.md)** - Organizing code with namespaces
- **Related Topic: [Classes](2-class.md)** - Class structure
- **Related Topic: [Traits](25-trait.md)** - Reusable code blocks
- **Related Topic: [Interfaces](23-interface.md)** - Contracts for implementations
