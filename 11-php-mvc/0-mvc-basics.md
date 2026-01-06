# Building MVC Applications in PHP

## Overview

This document covers creating Model-View-Controller (MVC) applications from scratch, understanding core concepts, and implementing a complete MVC framework.

---

## Table of Contents

1. What is MVC
2. MVC Architecture
3. Model Layer
4. View Layer
5. Controller Layer
6. Request Flow
7. Complete Examples

---

## What is MVC

### Definition

```
MVC = Architectural Pattern

Components:
- Model: Data and business logic
- View: Presentation layer
- Controller: Request handling

Benefits:
- Separation of concerns
- Testability
- Maintainability
- Scalability
- Flexibility
```

### Traditional Approach

```php
<?php
// Single file application
$user = $_GET['id'];
$data = mysqli_query("SELECT * FROM users WHERE id=$user");
$result = mysqli_fetch_assoc($data);

?>
<html>
<body>
    <h1><?php echo $result['name']; ?></h1>
    <p><?php echo $result['email']; ?></p>
</body>
</html>
```

### MVC Approach

```php
// Model: src/Models/User.php
class User {
    public function getById($id) {
        return Database::query("SELECT * FROM users WHERE id=?", [$id]);
    }
}

// Controller: src/Controllers/UserController.php
class UserController {
    public function show($id) {
        $user = (new User())->getById($id);
        return view('user.show', ['user' => $user]);
    }
}

// View: resources/views/user/show.php
<h1><?php echo $user->name; ?></h1>
<p><?php echo $user->email; ?></p>
```

---

## MVC Architecture

### Diagram

```
User Request
    ↓
Router (URL Mapping)
    ↓
Controller (Logic)
    ↓
Model (Data)
    ↓
Database
    ↓
Model (Returns Data)
    ↓
View (Render)
    ↓
Response (HTML)
```

### Directory Structure

```
app/
├── Controllers/
│   ├── HomeController.php
│   ├── UserController.php
│   └── ProductController.php
├── Models/
│   ├── User.php
│   ├── Product.php
│   └── Order.php
├── Views/
│   ├── home/
│   │   └── index.php
│   ├── user/
│   │   ├── show.php
│   │   └── list.php
│   └── product/
│       └── show.php
└── Routes/
    └── web.php
```

---

## Model Layer

### Purpose

```
Model = Data and Business Logic

Responsibilities:
- Database operations
- Data validation
- Business rules
- Data transformations
```

### Basic Model

```php
<?php

class User {
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    
    public function __construct(private Database $db) {
    }
    
    public function all() {
        return $this->db->query("SELECT * FROM {$this->table}");
    }
    
    public function find($id) {
        return $this->db->query(
            "SELECT * FROM {$this->table} WHERE id = ?",
            [$id]
        );
    }
    
    public function create($data) {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        
        return $this->db->execute(
            "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)",
            array_values($data)
        );
    }
    
    public function update($id, $data) {
        $sets = implode(',', array_map(fn($k) => "$k=?", array_keys($data)));
        
        return $this->db->execute(
            "UPDATE {$this->table} SET $sets WHERE id=?",
            [...array_values($data), $id]
        );
    }
    
    public function delete($id) {
        return $this->db->execute(
            "DELETE FROM {$this->table} WHERE id=?",
            [$id]
        );
    }
}
```

### Model with Validation

```php
<?php

class Product {
    
    public function validate($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }
        
        if (empty($data['price']) || !is_numeric($data['price'])) {
            $errors['price'] = 'Valid price is required';
        }
        
        if ($data['price'] < 0) {
            $errors['price'] = 'Price must be positive';
        }
        
        return $errors;
    }
    
    public function create($data) {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        
        // Proceed with creation
        return Database::insert('products', $data);
    }
}
```

---

## View Layer

### Purpose

```
View = Presentation

Responsibilities:
- HTML rendering
- Template processing
- User display
- Form generation
```

### Simple View

```php
<?php
// resources/views/user/show.php

?>
<div class="user-profile">
    <h1><?php echo htmlspecialchars($user->name); ?></h1>
    <p>Email: <?php echo htmlspecialchars($user->email); ?></p>
    <p>Member since: <?php echo $user->created_at; ?></p>
</div>
```

### View with Includes

```php
<?php include 'layouts/header.php'; ?>

<div class="container">
    <h1><?php echo $title; ?></h1>
    
    <?php foreach ($items as $item): ?>
        <div class="item">
            <h2><?php echo $item->name; ?></h2>
            <p><?php echo $item->description; ?></p>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'layouts/footer.php'; ?>
```

### View Helper Functions

```php
<?php

function view($path, $data = []) {
    extract($data);
    ob_start();
    include "resources/views/{$path}.php";
    return ob_get_clean();
}

function redirect($url) {
    header("Location: {$url}");
    exit;
}

function route($name, $params = []) {
    $routes = [
        'user.show' => '/users/{id}',
        'user.edit' => '/users/{id}/edit',
    ];
    
    $url = $routes[$name] ?? '';
    foreach ($params as $key => $value) {
        $url = str_replace("{$key}", $value, $url);
    }
    return $url;
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
```

---

## Controller Layer

### Purpose

```
Controller = Request Handler

Responsibilities:
- Route handling
- Request processing
- Model interaction
- View rendering
- Response generation
```

### Basic Controller

```php
<?php

class UserController {
    
    public function __construct(private User $user) {
    }
    
    public function index() {
        $users = $this->user->all();
        return view('user.index', ['users' => $users]);
    }
    
    public function show($id) {
        $user = $this->user->find($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }
        
        return view('user.show', ['user' => $user]);
    }
    
    public function create() {
        return view('user.create');
    }
    
    public function store() {
        $data = request()->all();
        
        try {
            $id = $this->user->create($data);
            redirect(route('user.show', ['id' => $id]));
        } catch (ValidationException $e) {
            return view('user.create', ['errors' => $e->errors]);
        }
    }
    
    public function edit($id) {
        $user = $this->user->find($id);
        return view('user.edit', ['user' => $user]);
    }
    
    public function update($id) {
        $data = request()->all();
        
        $this->user->update($id, $data);
        redirect(route('user.show', ['id' => $id]));
    }
    
    public function delete($id) {
        $this->user->delete($id);
        redirect(route('user.index'));
    }
}
```

### Controller with Middleware

```php
<?php

class AdminController {
    
    protected $middleware = [
        'auth',           // Must be logged in
        'admin',          // Must be admin
    ];
    
    public function __construct(private User $user) {
    }
    
    public function deleteUser($id) {
        $this->user->delete($id);
        return json_response(['success' => true]);
    }
}
```

---

## Request Flow

### Complete Flow Example

```
1. User accesses: GET /users/5

2. Router matches route:
   Route::get('/users/{id}', 'UserController@show')

3. Router extracts id = 5

4. Container instantiates UserController:
   $controller = new UserController(new User(new Database()))

5. Controller method called:
   $response = $controller->show(5)

6. Controller retrieves data:
   $user = $this->user->find(5)

7. Controller renders view:
   return view('user.show', ['user' => $user])

8. View rendered to HTML

9. HTML sent to browser

10. Browser displays page
```

---

## Complete Examples

### Example 1: Product Management MVC

```php
<?php
// app/Models/Product.php
class Product {
    public function __construct(private Database $db) {}
    
    public function all() {
        return $this->db->query("SELECT * FROM products");
    }
    
    public function find($id) {
        return $this->db->query("SELECT * FROM products WHERE id=?", [$id]);
    }
    
    public function create($data) {
        return $this->db->execute(
            "INSERT INTO products (name, price, category) VALUES (?,?,?)",
            [$data['name'], $data['price'], $data['category']]
        );
    }
}

// app/Controllers/ProductController.php
class ProductController {
    public function __construct(private Product $product) {}
    
    public function index() {
        $products = $this->product->all();
        return view('product.index', ['products' => $products]);
    }
    
    public function show($id) {
        $product = $this->product->find($id);
        return view('product.show', ['product' => $product]);
    }
    
    public function store() {
        $data = request()->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
        ]);
        
        $id = $this->product->create($data);
        return redirect(route('product.show', ['id' => $id]));
    }
}

// resources/views/product/show.php
<h1><?php echo $product->name; ?></h1>
<p>Price: $<?php echo $product->price; ?></p>
<p>Category: <?php echo $product->category; ?></p>
```

---

## Key Takeaways

**MVC Checklist:**

1. ✅ Separate concerns: Model, View, Controller
2. ✅ Models handle data and business logic
3. ✅ Views handle presentation
4. ✅ Controllers handle requests and responses
5. ✅ Keep controllers thin and focused
6. ✅ Keep models responsible for business logic
7. ✅ Keep views focused on display
8. ✅ Use dependency injection
9. ✅ Follow single responsibility principle
10. ✅ Test each layer independently

---

## See Also

- [Creating MVC Project](2-create-project.md)
- [Routing](5-route.md)
- [Controllers](6-controller.md)
- [Models](8-model.md)
- [Views](9-view.md)
