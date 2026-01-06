# Understanding URLs and PATH_INFO

## Overview

Learn how URLs are processed, how PHP retrieves URL information, and how routing works with PATH_INFO.

---

## Table of Contents

1. URL Structure
2. Server Variables
3. PATH_INFO
4. Request URI
5. Query Strings
6. Router Implementation
7. Complete Examples

---

## URL Structure

### Parts of a URL

```
http://example.com:8080/users/5?page=2#section

Protocol: http://
Domain: example.com
Port: :8080
Path: /users/5
Query String: ?page=2
Fragment: #section
```

### Examples

```
http://example.com/
  - Protocol: http
  - Host: example.com
  - Path: /
  - Query: (empty)

http://example.com/users
  - Path: /users

http://example.com/users/5/edit
  - Path: /users/5/edit

http://example.com/products?category=electronics&sort=price
  - Path: /products
  - Query: category=electronics&sort=price
```

---

## Server Variables

### Accessing URL Information

```php
<?php

// Full request URI
$requestUri = $_SERVER['REQUEST_URI'];
// /users/5?page=2

// Request method
$method = $_SERVER['REQUEST_METHOD'];
// GET, POST, PUT, DELETE

// Query string
$queryString = $_SERVER['QUERY_STRING'];
// page=2

// Script name
$scriptName = $_SERVER['SCRIPT_NAME'];
// /index.php

// Server name
$serverName = $_SERVER['SERVER_NAME'];
// example.com

// Server port
$serverPort = $_SERVER['SERVER_PORT'];
// 8000

// Remote address
$remoteAddr = $_SERVER['REMOTE_ADDR'];
// 192.168.1.1
```

### Complete Information

```php
<?php

function getRequestInfo() {
    return [
        'method' => $_SERVER['REQUEST_METHOD'],
        'uri' => $_SERVER['REQUEST_URI'],
        'path' => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
        'query' => $_SERVER['QUERY_STRING'] ?? '',
        'scheme' => $_SERVER['REQUEST_SCHEME'],
        'host' => $_SERVER['HTTP_HOST'],
        'referer' => $_SERVER['HTTP_REFERER'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
    ];
}

var_dump(getRequestInfo());
```

---

## PATH_INFO

### What is PATH_INFO

```
PATH_INFO = Path after script name

Scenario 1: Direct script
  /index.php/users/5
  Script name: /index.php
  PATH_INFO: /users/5

Scenario 2: With .htaccess rewrite
  /users/5 (rewritten to /index.php)
  Script name: /index.php
  PATH_INFO: /users/5
```

### Accessing PATH_INFO

```php
<?php

$path = $_SERVER['PATH_INFO'] ?? '/';

// Typical output
// /users
// /users/5
// /products?search=phone
```

### Extracting Path

```php
<?php

function getPath() {
    // Method 1: Use PATH_INFO
    if (!empty($_SERVER['PATH_INFO'])) {
        return $_SERVER['PATH_INFO'];
    }
    
    // Method 2: Parse REQUEST_URI
    $uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($uri, PHP_URL_PATH);
    
    // Remove script name if present
    $script = $_SERVER['SCRIPT_NAME'];
    if (strpos($path, $script) === 0) {
        $path = substr($path, strlen($script));
    }
    
    return $path ?: '/';
}

echo getPath();  // /users/5
```

---

## Query Strings

### Parsing Query Strings

```php
<?php

// Raw query string
$query = $_SERVER['QUERY_STRING'];
// page=2&sort=name

// Parsed into array
$params = $_GET;
// ['page' => '2', 'sort' => 'name']

// Manual parsing
parse_str($query, $params);

// With defaults
$params = array_merge([
    'page' => 1,
    'sort' => 'name',
    'order' => 'asc',
], $_GET);
```

### Handling Multiple Values

```php
<?php

// URL: /search?tags[]=php&tags[]=mvc&tags[]=routing
$_GET['tags'];  // ['php', 'mvc', 'routing']

// URL: /filter?category=1&category=2
// Only last value captured in $_GET
// Solution: Use array notation
// /filter?category[]=1&category[]=2
```

---

## Router Implementation

### Simple Router

```php
<?php

class Router {
    private $routes = [];
    
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }
    
    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }
    
    public function dispatch($method, $uri) {
        // Extract path from URI
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Check for exact match
        if (isset($this->routes[$method][$path])) {
            return call_user_func($this->routes[$method][$path]);
        }
        
        return $this->notFound();
    }
    
    private function notFound() {
        http_response_code(404);
        return '404 Not Found';
    }
}

// Usage
$router = new Router();

$router->get('/', function() {
    return 'Home Page';
});

$router->get('/users', function() {
    return 'Users List';
});

$response = $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
```

### Router with Parameters

```php
<?php

class AdvancedRouter {
    private $routes = [];
    private $params = [];
    
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }
    
    public function dispatch($method, $uri) {
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Try exact match first
        if (isset($this->routes[$method][$path])) {
            $callback = $this->routes[$method][$path];
            return call_user_func($callback, $this->params);
        }
        
        // Try pattern matching
        foreach ($this->routes[$method] ?? [] as $pattern => $callback) {
            if ($this->matches($pattern, $path)) {
                return call_user_func($callback, $this->params);
            }
        }
        
        return $this->notFound();
    }
    
    private function matches($pattern, $path) {
        // Convert /users/{id} to regex
        $regex = preg_replace(
            '/\{(\w+)\}/',
            '(?P<$1>\d+)',
            $pattern
        );
        
        if (preg_match('#^' . $regex . '$#', $path, $matches)) {
            // Extract numeric matches as parameters
            foreach ($matches as $key => $value) {
                if (!is_numeric($key)) {
                    $this->params[$key] = $value;
                }
            }
            return true;
        }
        
        return false;
    }
    
    private function notFound() {
        http_response_code(404);
        return '404 Not Found';
    }
}
```

---

## Complete Examples

### Example 1: Basic URL Parsing

```php
<?php
// index.php

// Get request information
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$query = $_SERVER['QUERY_STRING'] ?? '';

// Simple routing
switch ($path) {
    case '/':
        echo 'Home Page';
        break;
    
    case '/users':
        echo 'Users List';
        if (!empty($query)) {
            parse_str($query, $params);
            echo " (Page: {$params['page']})";
        }
        break;
    
    case '/users/new':
        echo 'Create User Form';
        break;
    
    default:
        http_response_code(404);
        echo '404 Not Found';
}
```

### Example 2: Dynamic Routing

```php
<?php
// Router with controller support

class Router {
    private $routes = ['GET' => [], 'POST' => []];
    
    public function get($path, $controller, $action) {
        $this->routes['GET'][$path] = ['controller' => $controller, 'action' => $action];
    }
    
    public function post($path, $controller, $action) {
        $this->routes['POST'][$path] = ['controller' => $controller, 'action' => $action];
    }
    
    public function dispatch($method, $uri) {
        $path = parse_url($uri, PHP_URL_PATH);
        
        if (!isset($this->routes[$method][$path])) {
            return ['error' => 'Route not found'];
        }
        
        $route = $this->routes[$method][$path];
        $controller = "App\\Controllers\\" . ucfirst($route['controller']);
        $action = $route['action'];
        
        return [
            'controller' => $controller,
            'action' => $action,
        ];
    }
}

$router = new Router();
$router->get('/', 'home', 'index');
$router->get('/users', 'user', 'index');
$router->post('/users', 'user', 'store');
```

### Example 3: RESTful Routing

```php
<?php

class Request {
    public function method() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public function path() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return rtrim($path, '/') ?: '/';
    }
    
    public function query($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
    
    public function input($key = null, $default = null) {
        $data = $this->method() === 'POST' ? $_POST : $_GET;
        
        if ($key === null) {
            return $data;
        }
        return $data[$key] ?? $default;
    }
}

$request = new Request();

// RESTful endpoints
switch ($request->path()) {
    case '/api/users':
        match ($request->method()) {
            'GET' => respondJson(getUsersList()),
            'POST' => respondJson(createUser($request->input())),
            default => respondJson(['error' => 'Method not allowed'], 405),
        };
        break;
}
```

---

## Key Takeaways

**URL/PATH_INFO Checklist:**

1. ✅ Understand URL structure and components
2. ✅ Use $_SERVER for request information
3. ✅ Extract path from REQUEST_URI or PATH_INFO
4. ✅ Parse query strings into parameters
5. ✅ Implement basic routing logic
6. ✅ Handle dynamic path parameters
7. ✅ Support multiple request methods
8. ✅ Return appropriate HTTP status codes
9. ✅ Create reusable Router class
10. ✅ Test routing with different URLs

---

## See Also

- [Simple Routing](4-simple-route.md)
- [Advanced Routing](5-route.md)
- [Controllers](6-controller.md)
