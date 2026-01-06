# PHP MVC Pattern - Application Architecture

## Table of Contents
1. [Overview](#overview)
2. [What is MVC?](#what-is-mvc)
3. [MVC Components](#mvc-components)
4. [How MVC Works](#how-mvc-works)
5. [MVC Flow](#mvc-flow)
6. [Real-World Example](#real-world-example)
7. [MVC Benefits](#mvc-benefits)
8. [MVC vs Other Patterns](#mvc-vs-other-patterns)
9. [Learning Path](#learning-path)
10. [Best Practices](#best-practices)
11. [Prerequisites](#prerequisites)

---

## Overview

MVC (Model-View-Controller) is an architectural pattern that separates application logic into three interconnected components. It's the foundation of modern web frameworks and enterprise applications.

### Why MVC?

**Without MVC (Spaghetti Code)**
```php
// All mixed together - hard to maintain
<?php
$conn = mysqli_connect("localhost", "user", "pass", "db");
$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
$user = mysqli_fetch_assoc($result);
?>
<h1><?php echo $user['name']; ?></h1>
<p><?php echo $user['email']; ?></p>
```

**With MVC (Clean Separation)**
```
Model (getData)     → View (display)
↑                        ↑
|                        |
←←←← Controller (handle request) ←←←←
```

Each part has a single responsibility, making code:
- **Testable**: Test logic independently
- **Maintainable**: Changes in one area don't break others
- **Scalable**: Grow without complexity
- **Reusable**: Share logic across views

## What is MVC?

### Model (Business Logic)

The **data and logic** layer:
- Manages application state
- Interacts with database
- Performs calculations
- Validates data
- No knowledge of views or controllers

```php
class UserModel {
    public function getUserById($id) {
        // Database logic
        return $this->db->query("SELECT * FROM users WHERE id = ?", [$id]);
    }
    
    public function createUser($name, $email) {
        // Validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email");
        }
        
        // Create
        return $this->db->query(
            "INSERT INTO users (name, email) VALUES (?, ?)",
            [$name, $email]
        );
    }
}
```

### View (Presentation)

The **display** layer:
- HTML templates
- Display data to user
- No business logic
- No database queries
- No request handling

```html
<!-- views/users/show.php -->
<div class="user-profile">
    <h1><?php echo htmlspecialchars($user['name']); ?></h1>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
    <a href="/users/<?php echo $user['id']; ?>/edit">Edit</a>
</div>
```

### Controller (Request Handler)

The **traffic director**:
- Receives HTTP requests
- Calls appropriate models
- Passes data to views
- No database logic
- No HTML (mostly)

```php
class UserController {
    public function show($id) {
        // Get data from model
        $user = $this->userModel->getUserById($id);
        
        // Pass to view
        return $this->render('users/show', ['user' => $user]);
    }
    
    public function store($request) {
        // Get data from request
        $name = $request->post('name');
        $email = $request->post('email');
        
        // Call model
        $user = $this->userModel->createUser($name, $email);
        
        // Return response (redirect or render)
        return redirect('/users/' . $user['id']);
    }
}
```

## MVC Components

### Model Layer Architecture

```
Application
    │
    └─ Models/
        ├─ User
        ├─ Product
        ├─ Order
        └─ Repository/Database Interfaces
```

**Responsibilities:**
- Define entity relationships
- Validate business rules
- Query/persist data
- Calculate derived values
- Handle state transitions

### View Layer Architecture

```
Application
    │
    └─ Views/
        ├─ layouts/
        │   └─ main.php
        ├─ users/
        │   ├─ index.php
        │   ├─ show.php
        │   ├─ edit.php
        │   └─ create.php
        └─ products/
            ├─ index.php
            └─ show.php
```

**Responsibilities:**
- Render HTML
- Display variables
- Format data for display
- Handle user interaction
- No direct model/database access

### Controller Layer Architecture

```
Application
    │
    └─ Controllers/
        ├─ UserController
        ├─ ProductController
        └─ HomeController
```

**Responsibilities:**
- Parse requests
- Call models
- Select views
- Handle redirects
- Basic validation

## How MVC Works

### User Request Flow

```
1. User Action
   └─ Clicks link: /users/5/edit

2. Routing
   └─ Matches to: UserController->edit(5)

3. Controller Processes
   ├─ Get request data
   ├─ Call Model methods
   └─ Prepare view data

4. Model Executes
   ├─ Query database
   ├─ Apply business logic
   └─ Return results

5. View Renders
   ├─ Use model data
   ├─ Generate HTML
   └─ Return to browser

6. Response
   └─ User sees rendered page
```

### Example: User Edit Page

```
Request: /users/5/edit
         │
         ▼
Router: Identify UserController->edit(5)
         │
         ▼
Controller: 
  $user = $userModel->getUserById(5);
  render('users/edit', ['user' => $user]);
         │
         ▼
Model: Query database
  SELECT * FROM users WHERE id = 5;
         │
         ▼
View: Render edit form
  <form method="POST" action="/users/5">
    <input name="name" value="<?php echo $user['name']; ?>">
    ...
  </form>
         │
         ▼
Response: HTML sent to browser
```

## MVC Flow

### Complete Request Cycle

```
┌──────────────────────────────────────────────────────┐
│                 User's Browser                       │
│              (HTML/CSS/JavaScript)                   │
└────────────────────┬─────────────────────────────────┘
                     │ HTTP Request
                     ▼
┌──────────────────────────────────────────────────────┐
│                  Router                              │
│           Matches URL to Controller                  │
└────────────────────┬─────────────────────────────────┘
                     │
                     ▼
┌──────────────────────────────────────────────────────┐
│              CONTROLLER                              │
│  • Parse request ($_GET, $_POST)                    │
│  • Validate input                                    │
│  • Call Model methods                                │
│  • Select View                                       │
│  • Pass data to View                                 │
└────────────────────┬─────────────────────────────────┘
                     │
        ┌────────────┴────────────┐
        │                         │
        ▼                         ▼
   ┌─────────┐              ┌──────────┐
   │  MODEL  │              │   VIEW   │
   ├─────────┤              ├──────────┤
   │• Query  │              │• Template│
   │  DB     │  data        │• Display │
   │• Logic  │ ────────────>│• HTML    │
   │• Rules  │              │          │
   └─────────┘              └────┬─────┘
        ▲                        │
        │                        │
        └────────────────────────┘
                
                     │
                     ▼ Rendered HTML
                     
┌──────────────────────────────────────────────────────┐
│                 User's Browser                       │
│              (Displays to user)                      │
└──────────────────────────────────────────────────────┘
```

## Real-World Example

### Complete User Registration Flow

**1. View (Form)**
```html
<!-- views/auth/register.php -->
<form method="POST" action="/register">
    <input type="text" name="name" required>
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Register</button>
</form>
```

**2. Controller**
```php
class AuthController {
    public function register() {
        // Display form
        return $this->render('auth/register');
    }
    
    public function store($request) {
        try {
            // Get data from request
            $data = $request->only(['name', 'email', 'password']);
            
            // Call model to create user
            $user = $this->userModel->create($data);
            
            // Redirect to success page
            return redirect('/users/' . $user->id);
        } catch (ValidationException $e) {
            // Go back to form with errors
            return back()->withErrors($e->errors());
        }
    }
}
```

**3. Model**
```php
class User {
    public static function create($data) {
        // Validate
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException(['email' => 'Invalid email']);
        }
        
        // Check if exists
        if (self::where('email', $data['email'])->exists()) {
            throw new ValidationException(['email' => 'Already registered']);
        }
        
        // Create
        return self::query()->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
        ]);
    }
}
```

## MVC Benefits

### 1. Separation of Concerns
```
Model ≠ View ≠ Controller

Each has single responsibility
Easy to understand and modify
```

### 2. Testability
```php
// Can test model independently
$this->userModel->create(['name' => 'John', 'email' => 'john@example.com']);
// No need for browser, routes, or HTML
```

### 3. Code Reusability
```php
// Model used by multiple controllers
class UserController { use $userModel; }
class AdminController { use $userModel; }
class ApiController { use $userModel; }
```

### 4. Team Development
```
Designer/Frontend → Views
Backend Developer → Models & Controllers
Database Admin → Data structures
```

### 5. Easy to Maintain
```
Bug in layout? Check Views.
Logic error? Check Models.
Form not submitting? Check Controllers.
```

## MVC vs Other Patterns

### MVP (Model-View-Presenter)
- Controller replaced by Presenter
- Presenter handles all UI logic
- More view-heavy testing
- Less common in PHP

### MVVM (Model-View-ViewModel)
- ViewModel bridges Model and View
- Popular in JavaScript (Vue, Angular)
- More complex binding
- Overkill for simple PHP apps

### Clean Architecture
- More layers (Entities, Use Cases, Gateways)
- Enterprise applications
- Slower development initially
- Better for large teams

### MVC is Best For
- **Rapid development**
- **Team projects**
- **Standard web apps**
- **Easy to understand**

## Learning Path

Master MVC progressively:

1. **[MVC Overview](1-intro.md)** - Understand the pattern
2. **[Project Setup](2-create-project.md)** - Create MVC structure
3. **[Routing](4-simple-route.md)** - URL to Controller
4. **[Controllers](6-controller.md)** - Request handling
5. **[Models](8-model.md)** - Business logic
6. **[Views](9-view.md)** - HTML rendering
7. **[Advanced Routing](5-route.md)** - Dynamic routes
8. **[Middleware](10-middleware.md)** - Request intercepting
9. **[Complete Project](99-mvc-project/)** - Build real app

## Best Practices

### 1. Keep Controllers Thin
```php
// ❌ Too much logic
class ProductController {
    public function create() {
        $name = $_POST['name'];
        $price = $_POST['price'];
        
        // Validation here?
        if (strlen($name) < 3) { ... }
        
        // Database here?
        $db->query("INSERT INTO products...");
        
        // Rendering?
        echo "<h1>Created</h1>";
    }
}

// ✅ Thin controller
class ProductController {
    public function create() {
        // Delegate to model
        $product = $this->productModel->create($_POST);
        
        // Render view
        return $this->render('products/created', ['product' => $product]);
    }
}
```

### 2. Fat Models, Thin Controllers
```php
// ✅ Logic belongs in Model
class ProductModel {
    public function create($data) {
        // Validation
        $this->validate($data);
        
        // Database
        return $this->db->query("INSERT INTO...", $data);
    }
    
    private function validate($data) {
        // Comprehensive validation
    }
}
```

### 3. Views Should Be Dumb
```php
// ❌ Don't
<p><?php if ($user->role === 'admin') { ... } ?></p>

// ✅ Do - let controller decide what data to pass
<p><?php echo $user->role; ?></p>
```

### 4. DRY (Don't Repeat Yourself)
```php
// ❌ Repeated across controllers
public function index() {
    $auth = $this->auth->user();
    if (!$auth) { redirect('/login'); }
}

// ✅ Use middleware
class AuthMiddleware {
    public function handle($request, $next) {
        if (!$this->auth->user()) {
            return redirect('/login');
        }
        return $next($request);
    }
}
```

## Prerequisites

Before learning MVC:

✅ **Required:**
- PHP OOP (classes, inheritance, interfaces)
- Routing basics (URLs)
- Database fundamentals
- HTML/CSS (for views)

✅ **Helpful:**
- Web development concepts
- Design patterns
- Understanding of separation of concerns

## Quick Start Example

```php
// 1. Model
class TodoModel {
    public function getAll() {
        return $this->db->query("SELECT * FROM todos");
    }
}

// 2. Controller
class TodoController {
    public function index() {
        $todos = $this->todoModel->getAll();
        return $this->render('todos/index', ['todos' => $todos]);
    }
}

// 3. View (todos/index.php)
<ul>
    <?php foreach ($todos as $todo): ?>
        <li><?php echo $todo['title']; ?></li>
    <?php endforeach; ?>
</ul>

// 4. Route
$router->get('/todos', [TodoController::class, 'index']);
```

## Common Mistakes

❌ **Too much logic in Controller**
```php
// Bad
public function store() {
    $validation = ...
    $processing = ...
    $database = ...
}

// Good
public function store() {
    $model->create($_POST);
}
```

❌ **Too much logic in View**
```php
// Bad
<p><?php foreach (...) { ... } ?></p>

// Good
<p><?php echo $formatted_data; ?></p>
```

❌ **Database queries in View**
```php
// Bad
<?php
$user = User::find($id);
echo $user->name;
?>

// Good
// Controller passes data
<?php echo $user['name']; ?>
```

## Resources

- **MVC in Action**: [martinfowler.com/eaaDev/uiArchs.html](https://martinfowler.com/eaaDev/uiArchs.html)
- **Design Patterns**: [refactoring.guru/design-patterns/mvc](https://refactoring.guru/design-patterns/mvc)
- **Laravel (MVC Framework)**: [laravel.com](https://laravel.com)
- **Symfony (MVC Framework)**: [symfony.com](https://symfony.com)
