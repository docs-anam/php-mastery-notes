# Path Variables and Route Parameters

## Overview

Implement and handle dynamic route parameters extracted from URL paths for flexible and clean URL structures.

---

## Table of Contents

1. What are Path Variables
2. Basic Path Variables
3. Regular Expressions
4. Multiple Parameters
5. Optional Parameters
6. Parameter Types
7. Complete Examples

---

## What are Path Variables

### Purpose

```
Path Variables = Dynamic URL segments

Examples:
/users/123              - Variable: id = 123
/posts/42/comments/10   - Variables: post_id = 42, comment_id = 10
/categories/tech/page/2 - Variables: category = tech, page = 2
```

### Variable Extraction

```
URL: /users/123
Route: /users/{id}
Variable: id = 123

URL: /users/john/profile
Route: /users/{username}/profile
Variable: username = john
```

---

## Basic Path Variables

### Simple Router with Variables

```php
<?php
// Simple router with path variables

class SimpleRouter {
    private $routes = [];
    
    public function route($method, $path, $handler) {
        $pattern = $this->pathToRegex($path);
        $this->routes[$method][$path] = [
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }
    
    private function pathToRegex($path) {
        // Convert /users/{id} to regex
        $regex = preg_quote($path, '#');
        $regex = preg_replace('#\\\{(\w+)\\\}#', '(\w+)', $regex);
        return "#^$regex$#";
    }
    
    public function match($method, $path) {
        if (!isset($this->routes[$method])) {
            return null;
        }
        
        foreach ($this->routes[$method] as $routePath => $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                // Extract variables from regex matches
                $variables = $this->extractVariables($routePath, $matches);
                
                return [
                    'handler' => $route['handler'],
                    'variables' => $variables,
                ];
            }
        }
        
        return null;
    }
    
    private function extractVariables($path, $matches) {
        $variables = [];
        preg_match_all('#\{(\w+)\}#', $path, $paramNames);
        
        // Skip first match (entire string)
        for ($i = 0; $i < count($paramNames[1]); $i++) {
            $variables[$paramNames[1][$i]] = $matches[$i + 1];
        }
        
        return $variables;
    }
}

// Usage
$router = new SimpleRouter();

$router->route('GET', '/users/{id}', function($id) {
    echo "User ID: $id";
});

$result = $router->match('GET', '/users/123');
print_r($result);
// Array (
//     [handler] => Closure
//     [variables] => Array ([id] => 123)
// )
```

### Extracting Variables

```php
<?php
// Extract from URL path

function extractPathVariables($pattern, $path) {
    // /users/{id}/posts/{post_id}
    // /users/123/posts/456
    
    $regex = preg_quote($pattern, '#');
    $regex = preg_replace('#\\\{(\w+)\\\}#', '(?P<$1>[^/]+)', $regex);
    
    if (preg_match("#^$regex$#", $path, $matches)) {
        $variables = [];
        preg_match_all('#\{(\w+)\}#', $pattern, $names);
        
        foreach ($names[1] as $name) {
            if (isset($matches[$name])) {
                $variables[$name] = $matches[$name];
            }
        }
        
        return $variables;
    }
    
    return null;
}

// Example
$variables = extractPathVariables(
    '/users/{id}/posts/{post_id}',
    '/users/123/posts/456'
);

print_r($variables);
// Array (
//     [id] => 123
//     [post_id] => 456
// )
```

---

## Regular Expressions

### Pattern Matching

```php
<?php
// Route patterns with regex

class RoutePattern {
    public static function toRegex($pattern) {
        $regex = preg_quote($pattern, '#');
        
        // {id} -> named group matching digits
        $regex = preg_replace('#\\\{(\w+)\\\}#', '(?P<$1>[^/]+)', $regex);
        
        return "#^$regex$#";
    }
    
    public static function match($pattern, $path) {
        $regex = self::toRegex($pattern);
        return preg_match($regex, $path) === 1;
    }
    
    public static function extract($pattern, $path) {
        $regex = self::toRegex($pattern);
        
        if (preg_match($regex, $path, $matches)) {
            $variables = [];
            
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $variables[$key] = $value;
                }
            }
            
            return $variables;
        }
        
        return null;
    }
}

// Examples
echo RoutePattern::match('/users/{id}', '/users/123') ? 'Match' : 'No match';
// Output: Match

$vars = RoutePattern::extract('/users/{id}', '/users/123');
print_r($vars);
// Array ([id] => 123)
```

### Type Constraints

```php
<?php
// Pattern with type constraints

class TypedRoutePattern {
    private static $patterns = [
        'id' => '(\d+)',           // digits only
        'slug' => '([a-z0-9-]+)',  // lowercase, numbers, hyphens
        'username' => '(\w+)',     // word characters
        'any' => '([^/]+)',        // anything except slash
    ];
    
    public static function addType($name, $pattern) {
        self::$patterns[$name] = $pattern;
    }
    
    public static function toRegex($pattern) {
        // /users/{id:numeric}/posts/{slug:slug}
        
        $regex = preg_quote($pattern, '#');
        
        // Replace {name:type} or {name}
        $regex = preg_replace_callback(
            '#\\\{(\w+)(?::(\w+))?\\\}#',
            function($matches) {
                $name = $matches[1];
                $type = $matches[2] ?? 'any';
                
                $pattern = self::$patterns[$type] ?? self::$patterns['any'];
                
                return "(?P<$name>$pattern)";
            },
            $regex
        );
        
        return "#^$regex$#";
    }
    
    public static function extract($pattern, $path) {
        $regex = self::toRegex($pattern);
        
        if (preg_match($regex, $path, $matches)) {
            $variables = [];
            
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $variables[$key] = $value;
                }
            }
            
            return $variables;
        }
        
        return null;
    }
}

// Usage
$vars = TypedRoutePattern::extract(
    '/users/{id:numeric}/posts/{slug:slug}',
    '/users/123/posts/my-post'
);
print_r($vars);
// Array ([id] => 123, [slug] => my-post)

// Won't match with wrong type
$vars = TypedRoutePattern::extract(
    '/users/{id:numeric}',
    '/users/abc'
);
var_dump($vars);
// NULL
```

---

## Multiple Parameters

### Extracting Multiple Parameters

```php
<?php
// Route with multiple parameters

class MultiParamRouter {
    private $routes = [];
    
    public function get($pattern, $handler) {
        $this->routes['GET'][] = [
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }
    
    public function dispatch($method, $path) {
        if (!isset($this->routes[$method])) {
            return null;
        }
        
        foreach ($this->routes[$method] as $route) {
            $variables = $this->extractVariables($route['pattern'], $path);
            
            if ($variables !== null) {
                return [
                    'handler' => $route['handler'],
                    'variables' => $variables,
                ];
            }
        }
        
        return null;
    }
    
    private function extractVariables($pattern, $path) {
        // Convert pattern to regex
        $regex = preg_quote($pattern, '#');
        $regex = preg_replace_callback(
            '#\\\{(\w+)\\\}#',
            function($m) { return "(?P<{$m[1]}>[^/]+)"; },
            $regex
        );
        
        if (preg_match("#^$regex$#", $path, $matches)) {
            $variables = [];
            
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $variables[$key] = $value;
                }
            }
            
            return $variables;
        }
        
        return null;
    }
}

// Usage
$router = new MultiParamRouter();

$router->get('/users/{id}/posts/{post_id}/comments/{comment_id}', function($id, $post_id, $comment_id) {
    // Handler receives parameters
});

$result = $router->dispatch('GET', '/users/1/posts/5/comments/10');
print_r($result['variables']);
// Array (
//     [id] => 1
//     [post_id] => 5
//     [comment_id] => 10
// )
```

---

## Optional Parameters

### Making Parameters Optional

```php
<?php
// Optional path segments

class OptionalParamRouter {
    private $routes = [];
    
    public function get($pattern, $handler) {
        $this->routes['GET'][] = [
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }
    
    public function dispatch($method, $path) {
        foreach ($this->routes[$method] ?? [] as $route) {
            $variables = $this->tryMatch($route['pattern'], $path);
            
            if ($variables !== null) {
                return [
                    'handler' => $route['handler'],
                    'variables' => $variables,
                ];
            }
        }
        
        return null;
    }
    
    private function tryMatch($pattern, $path) {
        // /users/all
        // /users/{id}
        // /users/{id}/posts
        // /users/{id}/posts/{post_id}
        
        $regex = preg_quote($pattern, '#');
        $regex = preg_replace_callback(
            '#\\\{(\w+)\\\}#',
            function($m) { return "(?P<{$m[1]}>[^/]+)"; },
            $regex
        );
        
        if (preg_match("#^$regex$#", $path, $matches)) {
            $variables = [];
            
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $variables[$key] = $value;
                }
            }
            
            return $variables;
        }
        
        return null;
    }
}

// Usage
$router = new OptionalParamRouter();

$router->get('/users', function() {
    echo "User list";
});

$router->get('/users/{id}', function($id) {
    echo "User $id";
});

$router->get('/users/{id}/posts/{post_id}', function($id, $post_id) {
    echo "Post $post_id by user $id";
});

// Each path matches different route
$router->dispatch('GET', '/users');
$router->dispatch('GET', '/users/123');
$router->dispatch('GET', '/users/123/posts/456');
```

---

## Parameter Types

### Strict Type Validation

```php
<?php
// Parameters with type constraints

class TypeConstraints {
    private $routes = [];
    
    public function addRoute($method, $pattern, $handler) {
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }
    
    public function dispatch($method, $path) {
        foreach ($this->routes[$method] ?? [] as $route) {
            $variables = $this->extract($route['pattern'], $path);
            
            if ($variables !== false) {
                return [
                    'handler' => $route['handler'],
                    'variables' => $variables,
                ];
            }
        }
        
        return null;
    }
    
    private function extract($pattern, $path) {
        // Parse constraints: {id:int}, {slug:string}, {page:int?}
        
        preg_match_all('#\{(\w+)(?::([^}]+))?\}#', $pattern, $matches);
        
        $paramNames = $matches[1];
        $constraints = $matches[2];
        
        // Build regex
        $regex = preg_quote($pattern, '#');
        
        foreach ($constraints as $i => $constraint) {
            $name = $paramNames[$i];
            $pattern_part = $this->constraintToRegex($constraint);
            $regex = preg_replace(
                "#\\\{$name(?::[^}]+)?\\\}#",
                "(?P<$name>$pattern_part)",
                $regex
            );
        }
        
        if (preg_match("#^$regex$#", $path, $matches)) {
            $variables = [];
            
            foreach ($paramNames as $name) {
                if (isset($matches[$name])) {
                    $variables[$name] = $matches[$name];
                }
            }
            
            return $variables;
        }
        
        return false;
    }
    
    private function constraintToRegex($constraint) {
        $patterns = [
            'int' => '\d+',
            'integer' => '\d+',
            'string' => '[a-zA-Z]+',
            'slug' => '[a-z0-9-]+',
            'uuid' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}',
        ];
        
        return $patterns[$constraint] ?? '[^/]+';
    }
}

// Usage
$router = new TypeConstraints();

$router->addRoute('GET', '/users/{id:int}', function($id) {
    echo "User $id";
});

$router->addRoute('GET', '/posts/{slug:slug}', function($slug) {
    echo "Post: $slug";
});

// Match
$result = $router->dispatch('GET', '/users/123');
print_r($result['variables']); // Array ([id] => 123)

// Won't match
$result = $router->dispatch('GET', '/users/abc');
var_dump($result); // NULL
```

---

## Complete Examples

### Example 1: Full Router with Parameters

```php
<?php
// Complete router implementation

class Router {
    private $routes = [];
    
    public function route($method, $pattern, $handler) {
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }
        
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'handler' => $handler,
            'regex' => $this->patternToRegex($pattern),
        ];
    }
    
    private function patternToRegex($pattern) {
        $regex = preg_quote($pattern, '#');
        $regex = preg_replace_callback(
            '#\\\{(\w+)(?::([^}]+))?\\\}#',
            function($m) {
                $name = $m[1];
                $type = $m[2] ?? 'any';
                $pattern = $this->typePattern($type);
                return "(?P<$name>$pattern)";
            },
            $regex
        );
        return "#^$regex$#";
    }
    
    private function typePattern($type) {
        $patterns = [
            'int' => '\d+',
            'slug' => '[a-z0-9-]+',
            'any' => '[^/]+',
        ];
        return $patterns[$type] ?? $patterns['any'];
    }
    
    public function match($method, $path) {
        $method = strtoupper($method);
        
        if (!isset($this->routes[$method])) {
            return null;
        }
        
        foreach ($this->routes[$method] as $route) {
            if (preg_match($route['regex'], $path, $matches)) {
                $variables = [];
                
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $variables[$key] = $value;
                    }
                }
                
                return [
                    'handler' => $route['handler'],
                    'variables' => $variables,
                ];
            }
        }
        
        return null;
    }
    
    public function dispatch($method, $path) {
        $route = $this->match($method, $path);
        
        if ($route) {
            $handler = $route['handler'];
            $variables = $route['variables'];
            
            // Call handler with variables
            return call_user_func_array($handler, $variables);
        }
        
        http_response_code(404);
        echo '404 Not Found';
    }
}

// Usage
$router = new Router();

$router->route('GET', '/users/{id:int}', function($id) {
    echo "User #$id";
});

$router->route('GET', '/posts/{slug:slug}', function($slug) {
    echo "Post: $slug";
});

$router->route('GET', '/api/users/{id:int}/posts/{post_id:int}', function($id, $post_id) {
    echo "Post $post_id by user $id";
});

// Dispatch requests
$router->dispatch('GET', '/users/123');
$router->dispatch('GET', '/posts/hello-world');
$router->dispatch('GET', '/api/users/1/posts/5');
```

---

## Key Takeaways

**Parameter Handling Checklist:**

1. ✅ Define variable patterns (e.g., {id}, {slug})
2. ✅ Convert patterns to regex
3. ✅ Extract variables from matched path
4. ✅ Support multiple parameters
5. ✅ Validate parameter types
6. ✅ Handle optional segments
7. ✅ Pass variables to handlers
8. ✅ Test edge cases

---

## See Also

- [Simple Routes](4-simple-route.md)
- [Advanced Routes](5-route.md)
- [Controllers](6-controller.md)
