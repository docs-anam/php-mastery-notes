# Controller Implementation

## Overview

Implement controllers to handle requests, process data, and coordinate between models and views.

---

## Table of Contents

1. What are Controllers
2. Basic Controller
3. Controller Methods
4. Request Handling
5. Dependency Injection
6. Controller Organization
7. Complete Examples

---

## What are Controllers

### Purpose

```
Controller = Request Handler

Responsibilities:
- Receive requests
- Validate input
- Call models
- Pass data to views
- Return responses
```

### Controller vs Action

```php
// Controller class
class UserController {
    // Action methods
    public function index() { }      // List users
    public function create() { }     // Show create form
    public function store() { }      // Save new user
    public function show() { }       // Show single user
    public function edit() { }       // Show edit form
    public function update() { }     // Update user
    public function destroy() { }    // Delete user
}
```

---

## Basic Controller

### Simple Controller

```php
<?php

class HomeController {
    
    public function index() {
        $data = [
            'title' => 'Welcome',
            'message' => 'Hello World',
        ];
        
        return view('home.index', $data);
    }
}

// Routing
$router->get('/', 'HomeController@index');

// Usage
$controller = new HomeController();
echo $controller->index();
```

### View Rendering

```php
<?php

function view($path, $data = []) {
    // Extract data into variables
    extract($data);
    
    // Start output buffering
    ob_start();
    
    // Include view file
    include BASE_PATH . "/resources/views/{$path}.php";
    
    // Return rendered content
    return ob_get_clean();
}

// Example view: resources/views/home/index.php
<h1><?php echo $title; ?></h1>
<p><?php echo $message; ?></p>
```

---

## Controller Methods

### CRUD Operations

```php
<?php

class ProductController {
    
    private $product;
    
    public function __construct() {
        $this->product = new Product();
    }
    
    // READ: List all
    public function index() {
        $products = $this->product->all();
        return view('product.index', ['products' => $products]);
    }
    
    // READ: Single
    public function show($id) {
        $product = $this->product->find($id);
        
        if (!$product) {
            http_response_code(404);
            return 'Product not found';
        }
        
        return view('product.show', ['product' => $product]);
    }
    
    // CREATE: Form
    public function create() {
        return view('product.create');
    }
    
    // CREATE: Save
    public function store() {
        $data = [
            'name' => $_POST['name'],
            'price' => $_POST['price'],
            'category' => $_POST['category'],
        ];
        
        $id = $this->product->create($data);
        
        header("Location: /products/$id");
        exit;
    }
    
    // UPDATE: Form
    public function edit($id) {
        $product = $this->product->find($id);
        return view('product.edit', ['product' => $product]);
    }
    
    // UPDATE: Save
    public function update($id) {
        $data = [
            'name' => $_POST['name'],
            'price' => $_POST['price'],
        ];
        
        $this->product->update($id, $data);
        
        header("Location: /products/$id");
        exit;
    }
    
    // DELETE
    public function destroy($id) {
        $this->product->delete($id);
        
        header("Location: /products");
        exit;
    }
}
```

---

## Request Handling

### Request Object

```php
<?php

class Request {
    
    public function method() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public function path() {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
    
    public function input($key = null, $default = null) {
        $data = $this->method() === 'POST' ? $_POST : $_GET;
        
        if ($key === null) {
            return $data;
        }
        
        return $data[$key] ?? $default;
    }
    
    public function all() {
        return array_merge($_GET, $_POST);
    }
    
    public function only($keys) {
        $data = $this->all();
        return array_intersect_key($data, array_flip($keys));
    }
    
    public function except($keys) {
        $data = $this->all();
        return array_diff_key($data, array_flip($keys));
    }
    
    public function has($key) {
        return isset($_GET[$key]) || isset($_POST[$key]);
    }
    
    public function validate($rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            if ($rule === 'required' && empty($this->input($field))) {
                $errors[$field] = "$field is required";
            }
        }
        
        return $errors;
    }
}

// Usage in controller
public function store() {
    $request = new Request();
    
    $data = $request->only(['name', 'email', 'password']);
    
    if ($request->has('subscribe')) {
        // Handle subscription
    }
    
    $this->model->create($data);
}
```

---

## Dependency Injection

### Injecting Dependencies

```php
<?php

class UserController {
    
    public function __construct(
        private UserRepository $repository,
        private UserValidator $validator,
        private Logger $logger
    ) {
    }
    
    public function store() {
        $data = request()->all();
        
        // Validate
        $errors = $this->validator->validate($data);
        if (!empty($errors)) {
            return response()->back()->withErrors($errors);
        }
        
        // Create
        $user = $this->repository->create($data);
        
        // Log
        $this->logger->info("User created: {$user->id}");
        
        return redirect("/users/{$user->id}");
    }
}

// Container registration
$container = new Container();

$container->bind(UserRepository::class, function() {
    return new UserRepository(new Database());
});

$container->bind(UserValidator::class, function() {
    return new UserValidator();
});

$container->bind(Logger::class, function() {
    return new Logger();
});

// Instantiation
$controller = $container->make(UserController::class);
```

---

## Controller Organization

### Base Controller

```php
<?php

abstract class Controller {
    
    protected function view($path, $data = []) {
        return view($path, $data);
    }
    
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    protected function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }
    
    protected function abort($code, $message = '') {
        http_response_code($code);
        die($message);
    }
    
    protected function json($data) {
        header('Content-Type: application/json');
        return json_encode($data);
    }
}

// Usage
class PostController extends Controller {
    
    public function show($id) {
        $post = Post::find($id);
        return $this->view('post.show', ['post' => $post]);
    }
    
    public function destroy($id) {
        Post::delete($id);
        return $this->redirect('/posts');
    }
}
```

---

## Complete Examples

### Example 1: User Management Controller

```php
<?php

class UserController extends Controller {
    
    public function __construct(private User $user) {
    }
    
    public function index() {
        $users = $this->user->paginate(15);
        return $this->view('user.index', ['users' => $users]);
    }
    
    public function create() {
        return $this->view('user.create');
    }
    
    public function store() {
        $data = request()->only(['name', 'email', 'password']);
        
        // Validate
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return redirect('/users/create')->withErrors($errors);
        }
        
        // Create
        $user = $this->user->create($data);
        
        return redirect("/users/{$user->id}")->with('message', 'User created successfully');
    }
    
    public function show($id) {
        $user = $this->user->find($id);
        
        if (!$user) {
            return $this->abort(404, 'User not found');
        }
        
        return $this->view('user.show', ['user' => $user]);
    }
    
    public function edit($id) {
        $user = $this->user->find($id);
        return $this->view('user.edit', ['user' => $user]);
    }
    
    public function update($id) {
        $data = request()->only(['name', 'email']);
        
        $this->user->update($id, $data);
        
        return redirect("/users/{$id}")->with('message', 'User updated');
    }
    
    public function destroy($id) {
        $this->user->delete($id);
        
        return redirect('/users')->with('message', 'User deleted');
    }
    
    private function validate($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        }
        
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        }
        
        return $errors;
    }
}
```

### Example 2: API Controller

```php
<?php

class ApiProductController {
    
    public function __construct(private Product $product) {
    }
    
    public function index() {
        $products = $this->product->all();
        return $this->json(['data' => $products]);
    }
    
    public function show($id) {
        $product = $this->product->find($id);
        
        if (!$product) {
            return $this->jsonError('Product not found', 404);
        }
        
        return $this->json(['data' => $product]);
    }
    
    public function store() {
        $data = request()->all();
        
        $product = $this->product->create($data);
        
        return $this->json(['data' => $product], 201);
    }
    
    private function json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        return json_encode($data);
    }
    
    private function jsonError($message, $code = 400) {
        return $this->json(['error' => $message], $code);
    }
}
```

---

## Key Takeaways

**Controller Checklist:**

1. ✅ Keep controllers thin and focused
2. ✅ Use dependency injection
3. ✅ Handle one resource per controller
4. ✅ Use consistent action names
5. ✅ Validate input early
6. ✅ Return responses consistently
7. ✅ Handle errors appropriately
8. ✅ Separate concerns
9. ✅ Test controller behavior
10. ✅ Use base controller for common methods

---

## See Also

- [MVC Basics](0-mvc-basics.md)
- [Models](8-model.md)
- [Views](9-view.md)
- [Routing](5-route.md)
