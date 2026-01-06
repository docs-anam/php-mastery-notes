# Middleware and Request Pipelines

## Overview

Implement middleware for request/response processing, filtering, authentication, and cross-cutting concerns in your application.

---

## Table of Contents

1. What is Middleware
2. Basic Middleware
3. Middleware Pipeline
4. Common Patterns
5. Authentication Middleware
6. Logging and Monitoring
7. Complete Examples

---

## What is Middleware

### Purpose

```
Middleware = Processing Layer

Responsibilities:
- Request validation
- Authentication
- Authorization
- Logging
- Compression
- Caching
- Error handling
- Request transformation
```

### Middleware Flow

```
Request
  ↓
Middleware 1
  ↓
Middleware 2
  ↓
Middleware 3
  ↓
Application (Controller)
  ↓
Middleware 3 (response)
  ↓
Middleware 2 (response)
  ↓
Middleware 1 (response)
  ↓
Response
```

---

## Basic Middleware

### Simple Middleware

```php
<?php
// Simple middleware interface

interface Middleware {
    public function handle($request, $next);
}

// Logging middleware
class LoggingMiddleware implements Middleware {
    public function handle($request, $next) {
        $startTime = microtime(true);
        
        echo "Incoming request: {$request['method']} {$request['path']}\n";
        
        $response = $next($request);
        
        $duration = microtime(true) - $startTime;
        echo "Completed in {$duration}ms\n";
        
        return $response;
    }
}

// Authentication middleware
class AuthMiddleware implements Middleware {
    public function handle($request, $next) {
        if (empty($_SESSION['user_id'])) {
            return ['status' => 401, 'body' => 'Unauthorized'];
        }
        
        $request['user_id'] = $_SESSION['user_id'];
        return $next($request);
    }
}

// CORS middleware
class CorsMiddleware implements Middleware {
    public function handle($request, $next) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if ($request['method'] === 'OPTIONS') {
            return ['status' => 200, 'body' => 'OK'];
        }
        
        return $next($request);
    }
}
```

### Middleware Stack

```php
<?php
// Execute middleware in order

class MiddlewareStack {
    private $middlewares = [];
    private $handler;
    
    public function add(Middleware $middleware) {
        $this->middlewares[] = $middleware;
        return $this;
    }
    
    public function setHandler($handler) {
        $this->handler = $handler;
        return $this;
    }
    
    public function execute($request) {
        // Build pipeline from end to start
        $pipeline = $this->handler;
        
        for ($i = count($this->middlewares) - 1; $i >= 0; $i--) {
            $middleware = $this->middlewares[$i];
            $previous = $pipeline;
            
            $pipeline = function($req) use ($middleware, $previous) {
                return $middleware->handle($req, $previous);
            };
        }
        
        return $pipeline($request);
    }
}

// Usage
$stack = new MiddlewareStack();
$stack->add(new LoggingMiddleware());
$stack->add(new AuthMiddleware());
$stack->add(new CorsMiddleware());

$stack->setHandler(function($request) {
    return ['status' => 200, 'body' => 'Hello World'];
});

$response = $stack->execute([
    'method' => 'GET',
    'path' => '/api/users',
]);

print_r($response);
```

---

## Middleware Pipeline

### Pipeline Builder

```php
<?php
// Pipeline pattern for chaining middleware

class Pipeline {
    private $middlewares = [];
    private $handler;
    private $request;
    
    public function __construct($request = []) {
        $this->request = $request;
    }
    
    public function through($middlewares) {
        $this->middlewares = array_merge($this->middlewares, (array) $middlewares);
        return $this;
    }
    
    public function then(callable $handler) {
        $this->handler = $handler;
        return $this->execute();
    }
    
    private function execute() {
        $pipeline = $this->handler;
        
        foreach (array_reverse($this->middlewares) as $middleware) {
            $pipeline = $this->createPipe($middleware, $pipeline);
        }
        
        return $pipeline($this->request);
    }
    
    private function createPipe($middleware, $next) {
        return function($request) use ($middleware, $next) {
            if (is_string($middleware)) {
                $middleware = new $middleware();
            }
            
            if (is_callable($middleware)) {
                return $middleware($request, $next);
            }
            
            return $middleware->handle($request, $next);
        };
    }
}

// Usage
$response = (new Pipeline([]))
    ->through([
        'LoggingMiddleware',
        'AuthMiddleware',
        'ValidationMiddleware',
    ])
    ->then(function($request) {
        return ['status' => 200, 'body' => 'Success'];
    });
```

---

## Common Patterns

### Authentication Middleware

```php
<?php
// Authentication middleware

class AuthenticationMiddleware implements Middleware {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function handle($request, $next) {
        // Check for token in header
        $token = null;
        
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $parts = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);
            
            if (count($parts) === 2 && $parts[0] === 'Bearer') {
                $token = $parts[1];
            }
        }
        
        if (!$token) {
            return ['status' => 401, 'body' => 'Missing token'];
        }
        
        // Verify token
        $user = $this->verifyToken($token);
        
        if (!$user) {
            return ['status' => 401, 'body' => 'Invalid token'];
        }
        
        // Add user to request
        $request['user'] = $user;
        $request['authenticated'] = true;
        
        return $next($request);
    }
    
    private function verifyToken($token) {
        // Verify JWT or session token
        // This is simplified - use proper JWT libraries
        
        return $this->db->query("SELECT * FROM users WHERE token = ?", [$token])->fetch();
    }
}

// Authorization middleware
class AuthorizationMiddleware implements Middleware {
    private $requiredRole;
    
    public function __construct($requiredRole = 'user') {
        $this->requiredRole = $requiredRole;
    }
    
    public function handle($request, $next) {
        if (empty($request['user'])) {
            return ['status' => 401, 'body' => 'Not authenticated'];
        }
        
        $userRole = $request['user']['role'] ?? 'guest';
        
        if (!$this->hasRole($userRole, $this->requiredRole)) {
            return ['status' => 403, 'body' => 'Forbidden'];
        }
        
        return $next($request);
    }
    
    private function hasRole($userRole, $required) {
        $hierarchy = ['guest' => 0, 'user' => 1, 'moderator' => 2, 'admin' => 3];
        
        return ($hierarchy[$userRole] ?? 0) >= ($hierarchy[$required] ?? 0);
    }
}
```

### Rate Limiting Middleware

```php
<?php
// Rate limiting middleware

class RateLimitMiddleware implements Middleware {
    private $maxRequests = 100;
    private $windowSeconds = 3600;
    
    public function handle($request, $next) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = "rate_limit:{$ip}";
        
        // Use cache/Redis for distributed systems
        $cache = new SimpleCache();
        $count = $cache->get($key, 0);
        
        if ($count >= $this->maxRequests) {
            return [
                'status' => 429,
                'body' => 'Too Many Requests',
                'headers' => [
                    'Retry-After' => $this->windowSeconds,
                ]
            ];
        }
        
        $cache->increment($key);
        $cache->expire($key, $this->windowSeconds);
        
        $response = $next($request);
        
        // Add rate limit headers
        $response['headers']['X-RateLimit-Limit'] = $this->maxRequests;
        $response['headers']['X-RateLimit-Remaining'] = $this->maxRequests - $count - 1;
        
        return $response;
    }
}

// Simple cache
class SimpleCache {
    private $cache = [];
    
    public function get($key, $default = null) {
        return $this->cache[$key] ?? $default;
    }
    
    public function set($key, $value, $ttl = null) {
        $this->cache[$key] = $value;
    }
    
    public function increment($key) {
        $this->cache[$key] = ($this->cache[$key] ?? 0) + 1;
    }
    
    public function expire($key, $seconds) {
        // Simple TTL implementation
        // For production, use Redis
    }
}
```

### CORS Middleware

```php
<?php
// CORS middleware

class CorsMiddleware implements Middleware {
    private $allowedOrigins = ['https://example.com'];
    private $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE'];
    private $allowedHeaders = ['Content-Type', 'Authorization'];
    
    public function handle($request, $next) {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        
        if (!in_array($origin, $this->allowedOrigins)) {
            return ['status' => 403, 'body' => 'CORS not allowed'];
        }
        
        // Handle preflight request
        if ($request['method'] === 'OPTIONS') {
            return $this->preflight();
        }
        
        $response = $next($request);
        $response['headers'] = $response['headers'] ?? [];
        
        $response['headers']['Access-Control-Allow-Origin'] = $origin;
        $response['headers']['Access-Control-Allow-Credentials'] = 'true';
        
        return $response;
    }
    
    private function preflight() {
        return [
            'status' => 200,
            'headers' => [
                'Access-Control-Allow-Origin' => $_SERVER['HTTP_ORIGIN'] ?? '*',
                'Access-Control-Allow-Methods' => implode(', ', $this->allowedMethods),
                'Access-Control-Allow-Headers' => implode(', ', $this->allowedHeaders),
                'Access-Control-Max-Age' => '86400',
            ],
        ];
    }
}
```

---

## Logging and Monitoring

### Logging Middleware

```php
<?php
// Request/response logging

class LoggingMiddleware implements Middleware {
    private $logger;
    
    public function __construct($logger) {
        $this->logger = $logger;
    }
    
    public function handle($request, $next) {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Log incoming request
        $this->logger->info('Incoming request', [
            'method' => $request['method'],
            'path' => $request['path'],
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
        ]);
        
        $response = $next($request);
        
        // Log response
        $duration = (microtime(true) - $startTime) * 1000;
        $memory = memory_get_usage() - $startMemory;
        
        $level = (($response['status'] ?? 200) >= 400) ? 'error' : 'info';
        
        $this->logger->log($level, 'Completed request', [
            'status' => $response['status'] ?? 200,
            'duration_ms' => round($duration, 2),
            'memory_bytes' => $memory,
        ]);
        
        return $response;
    }
}

// Simple logger
class Logger {
    private $filename;
    
    public function __construct($filename) {
        $this->filename = $filename;
    }
    
    public function info($message, $context = []) {
        $this->log('INFO', $message, $context);
    }
    
    public function error($message, $context = []) {
        $this->log('ERROR', $message, $context);
    }
    
    public function log($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = json_encode($context);
        
        $line = "[$timestamp] $level: $message $contextStr\n";
        file_put_contents($this->filename, $line, FILE_APPEND);
    }
}
```

---

## Complete Examples

### Example 1: API Framework with Middleware

```php
<?php
// Complete API framework

class ApiFramework {
    private $stack;
    private $routes = [];
    
    public function __construct() {
        $this->stack = new MiddlewareStack();
        
        // Add global middleware
        $this->stack->add(new LoggingMiddleware(new Logger('app.log')));
        $this->stack->add(new CorsMiddleware());
        $this->stack->add(new AuthenticationMiddleware());
    }
    
    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
        return $this;
    }
    
    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
        return $this;
    }
    
    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $handler = $this->findRoute($method, $path);
        
        if (!$handler) {
            $handler = function() {
                return ['status' => 404, 'body' => 'Not Found'];
            };
        }
        
        $this->stack->setHandler($handler);
        
        $request = [
            'method' => $method,
            'path' => $path,
            'query' => $_GET,
            'body' => json_decode(file_get_contents('php://input'), true),
        ];
        
        $response = $this->stack->execute($request);
        
        http_response_code($response['status'] ?? 200);
        
        if (is_array($response['body'])) {
            echo json_encode($response['body']);
        } else {
            echo $response['body'];
        }
    }
    
    private function findRoute($method, $path) {
        return $this->routes[$method][$path] ?? null;
    }
}

// Usage
$app = new ApiFramework();

$app->get('/api/users', function($request) {
    return ['status' => 200, 'body' => ['users' => []]];
});

$app->post('/api/users', function($request) {
    // $request['user'] available from auth middleware
    return ['status' => 201, 'body' => ['id' => 1]];
});

$app->run();
```

---

## Key Takeaways

**Middleware Checklist:**

1. ✅ Define clear middleware interface
2. ✅ Implement before and after logic
3. ✅ Build middleware pipeline
4. ✅ Maintain request/response chain
5. ✅ Handle errors gracefully
6. ✅ Add authentication/authorization
7. ✅ Log requests and responses
8. ✅ Test middleware in isolation

---

## See Also

- [MVC Basics](0-mvc-basics.md)
- [Controllers](6-controller.md)
- [Routing](5-route.md)
