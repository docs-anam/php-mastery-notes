# Repository Pattern

## Overview

The Repository Pattern is a structural design pattern that provides an abstraction layer between your application and data access logic. It encapsulates database operations, making code more maintainable, testable, and decoupled from specific database implementations.

---

## Table of Contents

1. What is the Repository Pattern?
2. Benefits of Repository Pattern
3. Basic Repository Implementation
4. Generic Repository
5. Concrete Repositories
6. Service Layer Integration
7. Testing Repositories
8. Complete Examples

---

## What is the Repository Pattern?

The Repository Pattern treats the database as an in-memory collection of objects. Your application queries the repository instead of the database directly.

### Traditional Approach (Problematic)

```php
<?php
// Data access scattered throughout application
class UserController {
    private $pdo;
    
    public function show($id) {
        // SQL directly in controller
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function store($data) {
        // Another query directly in controller
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$data['name'], $data['email']]);
    }
}

// Problems:
// - Logic repeated in multiple controllers
// - Hard to test (requires database)
// - Difficult to switch databases
// - SQL mixed with business logic
?>
```

### Repository Approach (Better)

```php
<?php
// Encapsulated data access
class UserRepository {
    private $pdo;
    
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return new User($stmt->fetch());
    }
    
    public function save(User $user) {
        // Encapsulated save logic
    }
}

class UserController {
    private $userRepository;
    
    public function show($id) {
        // Get user through repository
        return $this->userRepository->find($id);
    }
}

// Benefits:
// - Centralized data access
// - Easy to test with mock repository
// - Database-agnostic
// - Clean separation of concerns
?>
```

---

## Benefits of Repository Pattern

### 1. Separation of Concerns

```php
<?php
// Before: Logic mixed together
class ProductController {
    public function index() {
        // Database query
        $stmt = $pdo->query("SELECT * FROM products");
        $products = $stmt->fetchAll();
        
        // Business logic
        $filtered = array_filter($products, function($p) {
            return $p['price'] > 100;
        });
        
        return view('products', $filtered);
    }
}

// After: Separated concerns
class ProductController {
    private $productRepository;
    
    public function index() {
        // Repository handles data access
        $products = $this->productRepository->findExpensive(100);
        return view('products', $products);
    }
}

class ProductRepository {
    // Data access isolated here
    public function findExpensive($minPrice) {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE price > ?");
        $stmt->execute([$minPrice]);
        return $stmt->fetchAll();
    }
}
?>
```

### 2. Testability

```php
<?php
// Easy to test with mock repository
class ProductService {
    private $repository;
    
    public function __construct(ProductRepository $repository) {
        $this->repository = $repository;
    }
    
    public function applyDiscount($productId, $discount) {
        $product = $this->repository->find($productId);
        $product->price *= (1 - $discount / 100);
        $this->repository->save($product);
        return $product;
    }
}

// In tests
class MockProductRepository implements ProductRepository {
    public function find($id) {
        return new Product(['id' => 1, 'price' => 100]);
    }
    
    public function save($product) {
        // Do nothing - just track calls
    }
}

// Test without database
$mock = new MockProductRepository();
$service = new ProductService($mock);
$result = $service->applyDiscount(1, 10);
assert($result->price === 90);
?>
```

### 3. Database Independence

```php
<?php
// Easy to switch databases
interface UserRepository {
    public function find($id);
    public function save(User $user);
}

// MySQL implementation
class MySQLUserRepository implements UserRepository {
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        // ...
    }
}

// PostgreSQL implementation
class PostgreSQLUserRepository implements UserRepository {
    public function find($id) {
        $stmt = $this->postgres->prepare("SELECT * FROM users WHERE id = ?");
        // ...
    }
}

// Switch with single line
$repository = new MySQLUserRepository($pdo);
// or
$repository = new PostgreSQLUserRepository($postgres);
?>
```

---

## Basic Repository Implementation

### Simple Repository

```php
<?php
class User {
    public $id;
    public $name;
    public $email;
    public $created_at;
    
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
    }
}

class UserRepository {
    private $pdo;
    private $table = 'users';
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    // READ
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM $this->table WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data) : null;
    }
    
    public function findAll() {
        $stmt = $this->pdo->query("SELECT * FROM $this->table");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => new User($row), $data);
    }
    
    public function findBy($column, $value) {
        $stmt = $this->pdo->prepare("SELECT * FROM $this->table WHERE $column = ?");
        $stmt->execute([$value]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data) : null;
    }
    
    // CREATE
    public function create(User $user) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO $this->table (name, email, created_at) VALUES (?, ?, NOW())"
        );
        $stmt->execute([$user->name, $user->email]);
        return $this->pdo->lastInsertId();
    }
    
    // UPDATE
    public function update(User $user) {
        $stmt = $this->pdo->prepare(
            "UPDATE $this->table SET name = ?, email = ? WHERE id = ?"
        );
        return $stmt->execute([$user->name, $user->email, $user->id]);
    }
    
    // DELETE
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM $this->table WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

// Usage
$repository = new UserRepository($pdo);

$user = $repository->find(1);
echo $user->name;

$users = $repository->findAll();
foreach ($users as $u) {
    echo $u->name;
}
?>
```

---

## Generic Repository

### Base Repository Class

```php
<?php
abstract class BaseRepository {
    protected $pdo;
    protected $table;
    protected $entityClass;
    
    public function __construct(PDO $pdo, $table, $entityClass) {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->entityClass = $entityClass;
    }
    
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) return null;
        
        return new $this->entityClass($data);
    }
    
    public function findAll() {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(
            fn($row) => new $this->entityClass($row),
            $data
        );
    }
    
    public function create($entity) {
        $properties = get_object_vars($entity);
        $columns = implode(', ', array_keys($properties));
        $placeholders = implode(', ', array_fill(0, count($properties), '?'));
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($properties));
        
        return $this->pdo->lastInsertId();
    }
    
    public function update($entity) {
        $properties = get_object_vars($entity);
        $id = $properties['id'];
        unset($properties['id']);
        
        $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($properties)));
        $sql = "UPDATE {$this->table} SET $set WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([...array_values($properties), $id]);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

// Usage
class Product {
    public $id;
    public $name;
    public $price;
}

class ProductRepository extends BaseRepository {
    public function __construct(PDO $pdo) {
        parent::__construct($pdo, 'products', Product::class);
    }
    
    public function findByPriceRange($min, $max) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table} WHERE price BETWEEN ? AND ? ORDER BY price"
        );
        $stmt->execute([$min, $max]);
        
        return array_map(
            fn($row) => new Product($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}

$repository = new ProductRepository($pdo);
$products = $repository->findByPriceRange(10, 100);
?>
```

---

## Concrete Repositories

### User Repository

```php
<?php
class User {
    public $id;
    public $name;
    public $email;
    public $password_hash;
    public $status;
    public $created_at;
}

class UserRepository extends BaseRepository {
    public function __construct(PDO $pdo) {
        parent::__construct($pdo, 'users', User::class);
    }
    
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new User($data) : null;
    }
    
    public function findActiveUsers() {
        $stmt = $this->pdo->query("SELECT * FROM users WHERE status = 'active'");
        
        return array_map(
            fn($row) => new User($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
    
    public function countByStatus($status) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE status = ?");
        $stmt->execute([$status]);
        
        return $stmt->fetchColumn();
    }
}
?>
```

### Order Repository with Relationships

```php
<?php
class Order {
    public $id;
    public $user_id;
    public $total;
    public $status;
    public $created_at;
    public $items = [];
}

class OrderRepository extends BaseRepository {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        parent::__construct($pdo, 'orders', Order::class);
        $this->pdo = $pdo;
    }
    
    public function find($id) {
        $order = parent::find($id);
        
        if ($order) {
            // Load related items
            $stmt = $this->pdo->prepare(
                "SELECT * FROM order_items WHERE order_id = ?"
            );
            $stmt->execute([$id]);
            $order->items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $order;
    }
    
    public function findByUserId($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        
        return array_map(
            fn($row) => $this->loadItems(new Order($row)),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
    
    private function loadItems(Order $order) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM order_items WHERE order_id = ?"
        );
        $stmt->execute([$order->id]);
        $order->items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $order;
    }
    
    public function create($entity) {
        try {
            $this->pdo->beginTransaction();
            
            // Create order
            $id = parent::create($entity);
            $entity->id = $id;
            
            // Create order items
            foreach ($entity->items as $item) {
                $stmt = $this->pdo->prepare(
                    "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)"
                );
                $stmt->execute([$id, $item['product_id'], $item['quantity']]);
            }
            
            $this->pdo->commit();
            return $id;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
?>
```

---

## Service Layer Integration

### Service Using Repository

```php
<?php
class UserService {
    private $userRepository;
    
    public function __construct(UserRepository $repository) {
        $this->userRepository = $repository;
    }
    
    public function register($name, $email, $password) {
        // Check email doesn't exist
        if ($this->userRepository->findByEmail($email)) {
            throw new Exception("Email already registered");
        }
        
        // Create user
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password_hash = password_hash($password, PASSWORD_BCRYPT);
        $user->status = 'active';
        
        // Save
        return $this->userRepository->create($user);
    }
    
    public function authenticate($email, $password) {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            throw new Exception("User not found");
        }
        
        if (!password_verify($password, $user->password_hash)) {
            throw new Exception("Invalid password");
        }
        
        return $user;
    }
    
    public function getActiveUsers() {
        return $this->userRepository->findActiveUsers();
    }
}

// Usage
$service = new UserService($userRepository);
$userId = $service->register('John', 'john@example.com', 'password');
$user = $service->authenticate('john@example.com', 'password');
?>
```

---

## Testing Repositories

### Mock Repository for Testing

```php
<?php
interface UserRepositoryInterface {
    public function find($id);
    public function findByEmail($email);
    public function create(User $user);
}

class MockUserRepository implements UserRepositoryInterface {
    private $users = [];
    
    public function __construct() {
        $this->users = [
            1 => new User(['id' => 1, 'name' => 'John', 'email' => 'john@example.com']),
            2 => new User(['id' => 2, 'name' => 'Jane', 'email' => 'jane@example.com']),
        ];
    }
    
    public function find($id) {
        return $this->users[$id] ?? null;
    }
    
    public function findByEmail($email) {
        foreach ($this->users as $user) {
            if ($user->email === $email) {
                return $user;
            }
        }
        return null;
    }
    
    public function create(User $user) {
        $id = max(array_keys($this->users)) + 1;
        $user->id = $id;
        $this->users[$id] = $user;
        return $id;
    }
}

// Test without database
class UserServiceTest {
    private $repository;
    private $service;
    
    public function setup() {
        $this->repository = new MockUserRepository();
        $this->service = new UserService($this->repository);
    }
    
    public function testAuthenticate() {
        $user = $this->service->authenticate('john@example.com', 'password');
        assert($user->name === 'John');
    }
    
    public function testRegisterDuplicateEmail() {
        // Should throw exception
        try {
            $this->service->register('Johnny', 'john@example.com', 'password');
            assert(false, "Should throw exception");
        } catch (Exception $e) {
            assert($e->getMessage() === "Email already registered");
        }
    }
}
?>
```

---

## Complete Examples

### Full Application Example

```php
<?php
// Models
class Product {
    public $id;
    public $name;
    public $price;
    public $stock;
}

class Cart {
    public $id;
    public $user_id;
    public $items = [];
    public $total;
}

// Repositories
class ProductRepository extends BaseRepository {
    public function __construct(PDO $pdo) {
        parent::__construct($pdo, 'products', Product::class);
    }
    
    public function findInStock() {
        $stmt = $this->pdo->query("SELECT * FROM products WHERE stock > 0");
        return array_map(
            fn($row) => new Product($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}

class CartRepository extends BaseRepository {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        parent::__construct($pdo, 'carts', Cart::class);
        $this->pdo = $pdo;
    }
    
    public function findByUserId($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM carts WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) return null;
        
        $cart = new Cart($data);
        $this->loadItems($cart);
        
        return $cart;
    }
    
    private function loadItems(Cart $cart) {
        $stmt = $this->pdo->prepare("SELECT * FROM cart_items WHERE cart_id = ?");
        $stmt->execute([$cart->id]);
        $cart->items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cart->total = array_sum(array_column($cart->items, 'total'));
    }
}

// Service
class ShoppingService {
    private $productRepository;
    private $cartRepository;
    
    public function __construct(ProductRepository $pr, CartRepository $cr) {
        $this->productRepository = $pr;
        $this->cartRepository = $cr;
    }
    
    public function addToCart($userId, $productId, $quantity) {
        // Get or create cart
        $cart = $this->cartRepository->findByUserId($userId);
        if (!$cart) {
            $cart = new Cart();
            $cart->user_id = $userId;
            $cart->id = $this->cartRepository->create($cart);
        }
        
        // Get product
        $product = $this->productRepository->find($productId);
        if (!$product || $product->stock < $quantity) {
            throw new Exception("Product not available");
        }
        
        // Add item to cart
        // ... implementation
    }
}

// Usage
$productRepo = new ProductRepository($pdo);
$cartRepo = new CartRepository($pdo);
$service = new ShoppingService($productRepo, $cartRepo);

$service->addToCart(1, 5, 2);
?>
```

---

## Key Principles

1. **Single Responsibility**: Repository handles only data access
2. **Dependency Injection**: Inject repositories into services
3. **Interface-Based**: Define contracts repositories must follow
4. **Model Objects**: Use objects instead of raw arrays
5. **Method Naming**: Use clear, semantic method names
6. **Testability**: Easy to mock for unit tests
7. **Consistency**: Same patterns across repositories

---

## See Also

- [Database Connection](4-database-connection.md)
- [Executing SQL Statements](5-execute-sql.md)
- [Prepared Statements](8-prepare-statement.md)
- [Database Transactions](11-database-transaction.md)
