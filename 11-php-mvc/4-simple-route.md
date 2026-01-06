# Simple Routing Implementation

## Overview

Create a basic router that maps URLs to controllers and handles simple routing scenarios without external dependencies.

---

## Table of Contents

1. Basic Router
2. Exact Route Matching
3. Static Routes
4. Route Registration
5. Dispatching Requests
6. Error Handling
7. Complete Examples

---

## Basic Router

### Simple Route Handler

```php
<?php

class Router {
    private $routes = [];
    
    public function register($method, $path, $handler) {
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }
        $this->routes[$method][$path] = $handler;
    }
    
    public function get($path, $handler) {
        $this->register('GET', $path, $handler);
    }
    
    public function post($path, $handler) {
        $this->register('POST', $path, $handler);
    }
    
    public function dispatch($method, $path) {
        if (!isset($this->routes[$method][$path])) {
            return $this->handleNotFound();
        }
        
        $handler = $this->routes[$method][$path];
        return $this->call($handler);
    }
    
    private function call($handler) {
        if (is_callable($handler)) {
            return $handler();
        }
        
        if (is_string($handler)) {
            [$controller, $action] = explode('@', $handler);
            return $this->callController($controller, $action);
        }
        
        return 'Invalid handler';
    }
    
    private function callController($controller, $action) {
        $class = "App\\Controllers\\$controller";
        $instance = new $class();
        return $instance->$action();
    }
    
    private function handleNotFound() {
        http_response_code(404);
        return '404 Not Found';
    }
}
```

---

## Exact Route Matching

### Direct Path Matching

```php
<?php

$router = new Router();

// Define exact routes
$router->get('/', function() {
    return 'Home Page';
});

$router->get('/about', function() {
    return 'About Page';
});

$router->get('/contact', function() {
    return 'Contact Page';
});

// Dispatch
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
echo $router->dispatch($method, $path);

// Test URLs:
// GET /          -> Home Page
// GET /about     -> About Page
// GET /contact   -> Contact Page
// GET /unknown   -> 404 Not Found
```

---

## Static Routes

### Registering Static Routes

```php
<?php

class StaticRouter {
    private $routes = [];
    
    public function route($method, $path, $callback) {
        $key = "$method:$path";
        $this->routes[$key] = $callback;
    }
    
    public function dispatch($method, $path) {
        $key = "$method:$path";
        
        if (!isset($this->routes[$key])) {
            http_response_code(404);
            return 'Not Found';
        }
        
        $callback = $this->routes[$key];
        return call_user_func($callback);
    }
}

$router = new StaticRouter();

// Routes
$router->route('GET', '/', function() { return 'Home'; });
$router->route('GET', '/users', function() { return 'Users'; });
$router->route('POST', '/users', function() { return 'User created'; });

// Dispatch
$response = $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
echo $response;
```

---

## Route Registration

### Fluent Interface

```php
<?php

class FluentRouter {
    private $routes = [];
    private $currentPrefix = '';
    
    public function group($prefix, $callback) {
        $previousPrefix = $this->currentPrefix;
        $this->currentPrefix = $previousPrefix . $prefix;
        
        $callback($this);
        
        $this->currentPrefix = $previousPrefix;
        return $this;
    }
    
    public function get($path, $handler) {
        $fullPath = $this->currentPrefix . $path;
        $this->routes['GET'][$fullPath] = $handler;
        return $this;
    }
    
    public function post($path, $handler) {
        $fullPath = $this->currentPrefix . $path;
        $this->routes['POST'][$fullPath] = $handler;
        return $this;
    }
}

// Usage
$router = new FluentRouter();

$router
    ->group('/api', function($router) {
        $router->get('/users', 'ApiUserController@index');
        $router->post('/users', 'ApiUserController@store');
        
        $router->group('/admin', function($router) {
            $router->get('/settings', 'AdminController@settings');
        });
    });

// Routes created:
// GET /api/users
// POST /api/users
// GET /api/admin/settings
```

---

## Dispatching Requests

### Request Dispatcher

```php
<?php

class RequestDispatcher {
    private $router;
    
    public function __construct(Router $router) {
        $this->router = $router;
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        
        // Remove query string
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Remove script name
        if (strpos($path, $_SERVER['SCRIPT_NAME']) === 0) {
            $path = substr($path, strlen($_SERVER['SCRIPT_NAME']));
        }
        
        // Default to /
        $path = $path ?: '/';
        
        return $this->router->dispatch($method, $path);
    }
}

// Usage in public/index.php
$router = new Router();
$router->get('/', 'HomeController@index');
$router->get('/users', 'UserController@index');

$dispatcher = new RequestDispatcher($router);
echo $dispatcher->dispatch();
```

---

## Error Handling

### HTTP Status Codes

```php
<?php

class StatusRouter {
    private $routes = [];
    private $notFoundHandler;
    private $errorHandler;
    
    public function setNotFoundHandler($handler) {
        $this->notFoundHandler = $handler;
        return $this;
    }
    
    public function setErrorHandler($handler) {
        $this->errorHandler = $handler;
        return $this;
    }
    
    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
        return $this;
    }
    
    public function dispatch($method, $path) {
        if (isset($this->routes[$method][$path])) {
            try {
                return call_user_func($this->routes[$method][$path]);
            } catch (Exception $e) {
                return $this->handleError($e);
            }
        }
        
        return $this->handleNotFound();
    }
    
    private function handleNotFound() {
        http_response_code(404);
        
        if ($this->notFoundHandler) {
            return call_user_func($this->notFoundHandler);
        }
        
        return '404 Not Found';
    }
    
    private function handleError(Exception $e) {
        http_response_code(500);
        
        if ($this->errorHandler) {
            return call_user_func($this->errorHandler, $e);
        }
        
        return 'Error: ' . $e->getMessage();
    }
}

// Usage
$router = new StatusRouter();

$router
    ->setNotFoundHandler(function() {
        return 'The page you requested was not found.';
    })
    ->setErrorHandler(function($error) {
        return 'An error occurred: ' . $error->getMessage();
    });
```

---

## Complete Examples

### Example 1: Basic Blog Router

```php
<?php

class BlogRouter {
    private $routes = [];
    
    public function register($method, $path, $controller, $action) {
        $this->routes["$method:$path"] = compact('controller', 'action');
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $key = "$method:$path";
        
        if (!isset($this->routes[$key])) {
            http_response_code(404);
            echo '404 - Page Not Found';
            return;
        }
        
        $route = $this->routes[$key];
        $class = "App\\Controllers\\" . $route['controller'];
        $controller = new $class();
        $action = $route['action'];
        
        echo $controller->$action();
    }
}

$router = new BlogRouter();

// Routes
$router->register('GET', '/', 'HomeController', 'index');
$router->register('GET', '/posts', 'PostController', 'index');
$router->register('GET', '/posts/new', 'PostController', 'create');
$router->register('POST', '/posts', 'PostController', 'store');
$router->register('GET', '/about', 'PageController', 'about');

// Dispatch
$router->dispatch();
```

### Example 2: RESTful Router

```php
<?php

class ResourceRouter {
    private $routes = [];
    
    public function resource($name, $controller) {
        // Index
        $this->routes['GET'][$name] = ["$controller@index"];
        
        // Create form
        $this->routes['GET']["$name/create"] = ["$controller@create"];
        
        // Store
        $this->routes['POST'][$name] = ["$controller@store"];
        
        // Edit form
        $this->routes['GET']["$name/{id}/edit"] = ["$controller@edit"];
        
        // Update
        $this->routes['PUT']["$name/{id}"] = ["$controller@update"];
        
        // Delete
        $this->routes['DELETE']["$name/{id}"] = ["$controller@delete"];
        
        return $this;
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);
            return;
        }
        
        [$controller, $action] = explode('@', $this->routes[$method][$path][0]);
        $class = "App\\Controllers\\$controller";
        
        echo (new $class())->$action();
    }
}

// Usage
$router = new ResourceRouter();
$router->resource('users', 'UserController');
$router->resource('posts', 'PostController');
$router->dispatch();

// Creates routes:
// GET /users, /users/create
// POST /users
// GET /users/{id}/edit
// PUT /users/{id}
// DELETE /users/{id}
```

---

## Key Takeaways

**Simple Router Checklist:**

1. ✅ Implement basic route registration
2. ✅ Support multiple HTTP methods
3. ✅ Exact path matching
4. ✅ Call controller methods
5. ✅ Handle 404 errors
6. ✅ Extract path from REQUEST_URI
7. ✅ Support closures as handlers
8. ✅ Support controller@action strings
9. ✅ Return responses cleanly
10. ✅ Keep router simple and focused

---

## See Also

- [Advanced Routing](5-route.md)
- [Path Information](3-path-info.md)
- [Controllers](6-controller.md)
