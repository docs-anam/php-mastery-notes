# Advanced Routing with Parameters

## Overview

Implement advanced routing with dynamic path parameters, parameter extraction, regex matching, and route constraints.

---

## Table of Contents

1. Route Parameters
2. Parameter Extraction
3. Regex Matching
4. Named Routes
5. Route Groups
6. Middleware Integration
7. Complete Examples

---

## Route Parameters

### Basic Parameters

```php
<?php

class ParameterRouter {
    private $routes = [];
    private $params = [];
    
    public function get($pattern, $handler) {
        $this->routes['GET'][$pattern] = $handler;
    }
    
    public function dispatch($method, $path) {
        // Try exact match first
        if (isset($this->routes[$method][$path])) {
            return call_user_func($this->routes[$method][$path]);
        }
        
        // Try pattern matching
        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            if ($this->match($pattern, $path)) {
                return call_user_func($handler, $this->params);
            }
        }
        
        http_response_code(404);
        return 'Not Found';
    }
    
    private function match($pattern, $path) {
        // Convert /users/{id} to regex
        $regex = preg_replace_callback(
            '/\{(\w+)(?::([^}]+))?\}/',
            function($matches) {
                $param = $matches[1];
                $constraint = $matches[2] ?? '\d+';
                
                return "(?P<$param>$constraint)";
            },
            $pattern
        );
        
        if (preg_match("#^$regex$#", $path, $matches)) {
            foreach ($matches as $key => $value) {
                if (!is_numeric($key)) {
                    $this->params[$key] = $value;
                }
            }
            return true;
        }
        
        return false;
    }
}

// Usage
$router = new ParameterRouter();

$router->get('/', function() {
    return 'Home';
});

$router->get('/users/{id}', function($params) {
    return "User ID: {$params['id']}";
});

$router->get('/posts/{slug}', function($params) {
    return "Post: {$params['slug']}";
});

// Test: /users/5 -> "User ID: 5"
// Test: /posts/hello-world -> "Post: hello-world"
```

---

## Parameter Extraction

### Multiple Parameters

```php
<?php

class MultiParameterRouter {
    private $routes = [];
    
    public function register($pattern, $handler) {
        $this->routes[$pattern] = $handler;
    }
    
    public function match($uri) {
        foreach ($this->routes as $pattern => $handler) {
            $params = $this->extract($pattern, $uri);
            
            if ($params !== null) {
                return [$handler, $params];
            }
        }
        
        return null;
    }
    
    private function extract($pattern, $uri) {
        $regex = preg_replace_callback(
            '/\{(\w+)\}/',
            fn($m) => "(?P<{$m[1]}>[^/]+)",
            preg_quote($pattern, '#')
        );
        
        if (preg_match("#^$regex$#", $uri, $matches)) {
            $params = [];
            foreach ($matches as $key => $value) {
                if (!is_numeric($key)) {
                    $params[$key] = $value;
                }
            }
            return $params;
        }
        
        return null;
    }
}

// Usage
$router = new MultiParameterRouter();

$router->register('/users/{id}/posts/{postId}', function($params) {
    return "User {$params['id']} Post {$params['postId']}";
});

// Test: /users/5/posts/123 -> extracts id=5, postId=123
```

---

## Regex Matching

### Constraint-Based Routing

```php
<?php

class ConstraintRouter {
    private $routes = [];
    
    public function get($pattern, $handler, $constraints = []) {
        $this->routes['GET'][$pattern] = [
            'handler' => $handler,
            'constraints' => $constraints,
        ];
    }
    
    public function dispatch($method, $path) {
        foreach ($this->routes[$method] ?? [] as $pattern => $route) {
            $params = $this->match($pattern, $path, $route['constraints']);
            
            if ($params !== false) {
                return call_user_func($route['handler'], $params);
            }
        }
        
        return '404 Not Found';
    }
    
    private function match($pattern, $path, $constraints) {
        $regex = $pattern;
        
        // Replace {param} with constraints or default
        $regex = preg_replace_callback(
            '/\{(\w+)\}/',
            function($matches) use ($constraints) {
                $param = $matches[1];
                $constraint = $constraints[$param] ?? '\d+';
                return "(?P<$param>$constraint)";
            },
            $regex
        );
        
        if (preg_match("#^$regex$#", $path, $matches)) {
            $params = [];
            foreach ($matches as $key => $value) {
                if (!is_numeric($key)) {
                    $params[$key] = $value;
                }
            }
            return $params;
        }
        
        return false;
    }
}

// Usage
$router = new ConstraintRouter();

$router->get(
    '/users/{id}',
    function($params) { return "User {$params['id']}"; },
    ['id' => '\d+']  // id must be numeric
);

$router->get(
    '/posts/{slug}',
    function($params) { return "Post {$params['slug']}"; },
    ['slug' => '[a-z0-9-]+']  // slug must be lowercase, numbers, hyphens
);

$router->get(
    '/api/v{version}/users/{id}',
    function($params) { return "API v{$params['version']} User {$params['id']}"; },
    ['version' => '[1-9]', 'id' => '\d+']
);
```

---

## Named Routes

### Route Naming and URL Generation

```php
<?php

class NamedRouteRouter {
    private $routes = [];
    private $named = [];
    
    public function get($name, $pattern, $handler) {
        $this->routes[$pattern] = $handler;
        $this->named[$name] = $pattern;
    }
    
    public function url($name, $params = []) {
        if (!isset($this->named[$name])) {
            throw new Exception("Route '$name' not found");
        }
        
        $pattern = $this->named[$name];
        
        // Replace parameters
        foreach ($params as $key => $value) {
            $pattern = str_replace("{$key}", $value, $pattern);
        }
        
        return $pattern;
    }
    
    public function dispatch($method, $path) {
        foreach ($this->routes as $pattern => $handler) {
            if ($this->match($pattern, $path, $params)) {
                return call_user_func($handler, $params);
            }
        }
        
        return '404 Not Found';
    }
    
    private function match($pattern, $path, &$params) {
        $regex = preg_replace_callback(
            '/\{(\w+)\}/',
            fn($m) => "(?P<{$m[1]}>[^/]+)",
            preg_quote($pattern, '#')
        );
        
        if (preg_match("#^$regex$#", $path, $matches)) {
            $params = [];
            foreach ($matches as $key => $value) {
                if (!is_numeric($key)) {
                    $params[$key] = $value;
                }
            }
            return true;
        }
        
        return false;
    }
}

// Usage
$router = new NamedRouteRouter();

$router->get('home', '/', function() { return 'Home'; });
$router->get('user.show', '/users/{id}', function($params) { return "User {$params['id']}"; });
$router->get('user.edit', '/users/{id}/edit', function($params) { return "Edit user {$params['id']}"; });

// Generate URLs
echo $router->url('home');  // /
echo $router->url('user.show', ['id' => 5]);  // /users/5
echo $router->url('user.edit', ['id' => 5]);  // /users/5/edit
```

---

## Route Groups

### Grouping Routes

```php
<?php

class GroupRouter {
    private $routes = [];
    private $groupPrefix = '';
    private $groupMiddleware = [];
    
    public function group($prefix, $middleware, $callback) {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;
        
        $this->groupPrefix = $previousPrefix . $prefix;
        $this->groupMiddleware = array_merge($this->groupMiddleware, $middleware);
        
        $callback($this);
        
        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }
    
    public function get($pattern, $handler) {
        $fullPattern = $this->groupPrefix . $pattern;
        $this->routes['GET'][$fullPattern] = [
            'handler' => $handler,
            'middleware' => $this->groupMiddleware,
        ];
    }
}

// Usage
$router = new GroupRouter();

$router->group('/api', ['auth'], function($router) {
    $router->get('/users', 'ApiUserController@index');
    $router->get('/users/{id}', 'ApiUserController@show');
    
    $router->group('/admin', ['admin'], function($router) {
        $router->get('/users', 'AdminUserController@index');
    });
});

// Creates routes:
// GET /api/users (with auth middleware)
// GET /api/users/{id} (with auth middleware)
// GET /api/admin/users (with auth and admin middleware)
```

---

## Complete Examples

### Example 1: E-commerce Router

```php
<?php

class EcommerceRouter {
    private $routes = [];
    
    public function setup() {
        // Public routes
        $this->get('/', 'HomeController@index');
        $this->get('/products', 'ProductController@index');
        $this->get('/products/{id}', 'ProductController@show');
        $this->get('/categories/{category}', 'CategoryController@show');
        
        // User routes
        $this->get('/cart', 'CartController@view');
        $this->post('/cart/add/{productId}', 'CartController@add');
        
        // Admin routes
        $this->get('/admin/products', 'AdminProductController@index');
        $this->get('/admin/products/{id}/edit', 'AdminProductController@edit');
        
        return $this;
    }
    
    private function get($pattern, $handler) {
        $this->routes['GET'][$pattern] = $handler;
    }
    
    private function post($pattern, $handler) {
        $this->routes['POST'][$pattern] = $handler;
    }
}
```

### Example 2: Blog Router with Nested Parameters

```php
<?php

$router = new ConstraintRouter();

// Blog routes
$router->get(
    '/',
    'HomeController@index'
);

$router->get(
    '/blog',
    'BlogController@index'
);

$router->get(
    '/blog/{year}/{month}/{day}/{slug}',
    'BlogController@show',
    [
        'year' => '20\d{2}',
        'month' => '0[1-9]|1[0-2]',
        'day' => '0[1-9]|[12]\d|3[01]',
        'slug' => '[a-z0-9-]+',
    ]
);

$router->get(
    '/blog/author/{author}',
    'BlogController@byAuthor',
    ['author' => '[a-z0-9-]+']
);

$router->get(
    '/blog/tag/{tag}',
    'BlogController@byTag',
    ['tag' => '[a-z0-9-]+']
);
```

---

## Key Takeaways

**Advanced Routing Checklist:**

1. ✅ Extract parameters from URL patterns
2. ✅ Use regex for flexible matching
3. ✅ Apply constraints to parameters
4. ✅ Support named routes
5. ✅ Generate URLs from route names
6. ✅ Group routes with prefixes
7. ✅ Apply middleware to groups
8. ✅ Handle multiple parameters
9. ✅ Support nested parameters
10. ✅ Maintain clean route definitions

---

## See Also

- [Simple Routing](4-simple-route.md)
- [Path Information](3-path-info.md)
- [Controllers](6-controller.md)
