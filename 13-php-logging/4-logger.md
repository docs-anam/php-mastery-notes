# Logger Implementation and Usage

## Overview

Learn how to properly implement loggers in your application and use them effectively across your codebase.

---

## Table of Contents

1. Creating Loggers
2. Using Loggers
3. Dependency Injection
4. Logger Traits
5. Best Practices
6. Common Patterns
7. Complete Examples

---

## Creating Loggers

### Basic Logger Creation

```php
<?php
// Simple logger creation

use Monolog\Logger;
use Monolog\Handlers\StreamHandler;

// Create logger with name
$logger = new Logger('app');

// Add handler
$handler = new StreamHandler('php://stdout');
$logger->pushHandler($handler);

// Use logger
$logger->info('Application started');
```

### Logger Factory

```php
<?php
// Factory for creating loggers

class LoggerFactory {
    private static $loggers = [];
    private static $config = [];
    
    public static function configure($config) {
        self::$config = $config;
    }
    
    public static function create($name = 'app') {
        if (isset(self::$loggers[$name])) {
            return self::$loggers[$name];
        }
        
        $logger = new Logger($name);
        
        // Load configuration
        if (isset(self::$config[$name])) {
            $config = self::$config[$name];
            
            foreach ($config['handlers'] as $handlerConfig) {
                $handler = self::createHandler($handlerConfig);
                $logger->pushHandler($handler);
            }
        }
        
        self::$loggers[$name] = $logger;
        return $logger;
    }
    
    private static function createHandler($config) {
        $class = $config['class'];
        $args = $config['args'] ?? [];
        return new $class(...$args);
    }
    
    public static function reset() {
        self::$loggers = [];
    }
}

// Configuration
LoggerFactory::configure([
    'app' => [
        'handlers' => [
            [
                'class' => StreamHandler::class,
                'args' => ['logs/app.log'],
            ],
        ],
    ],
    'errors' => [
        'handlers' => [
            [
                'class' => StreamHandler::class,
                'args' => ['logs/errors.log'],
            ],
        ],
    ],
]);

// Usage
$logger = LoggerFactory::create('app');
```

### Static Logger Helper

```php
<?php
// Static helper for quick logging

class Log {
    private static $logger;
    
    public static function setLogger(LoggerInterface $logger) {
        self::$logger = $logger;
    }
    
    public static function info($message, $context = []) {
        self::$logger->info($message, $context);
    }
    
    public static function error($message, $context = []) {
        self::$logger->error($message, $context);
    }
    
    public static function warning($message, $context = []) {
        self::$logger->warning($message, $context);
    }
    
    public static function debug($message, $context = []) {
        self::$logger->debug($message, $context);
    }
}

// Setup
Log::setLogger($logger);

// Usage
Log::info('User logged in', ['user_id' => 123]);
```

---

## Using Loggers

### In Classes

```php
<?php
// Inject logger into class

class UserService {
    public function __construct(
        private LoggerInterface $logger
    ) {}
    
    public function register($data) {
        $this->logger->info('User registration started', [
            'email' => $data['email'],
        ]);
        
        try {
            $user = User::create($data);
            
            $this->logger->info('User registered', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            
            return $user;
        } catch (Exception $e) {
            $this->logger->error('User registration failed', [
                'email' => $data['email'],
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
    
    public function delete($id) {
        $this->logger->warning('Deleting user', ['user_id' => $id]);
        User::destroy($id);
        $this->logger->info('User deleted', ['user_id' => $id]);
    }
}
```

### In Controllers

```php
<?php
// Logging in controller

class UserController {
    public function __construct(
        private UserService $service,
        private LoggerInterface $logger
    ) {}
    
    public function store($request) {
        $this->logger->debug('User store request', [
            'method' => 'POST',
            'path' => '/users',
        ]);
        
        try {
            $user = $this->service->register($request->all());
            
            $this->logger->info('User created via API', [
                'user_id' => $user->id,
                'ip' => $_SERVER['REMOTE_ADDR'],
            ]);
            
            return response()->json($user, 201);
        } catch (ValidationException $e) {
            $this->logger->warning('User validation failed', [
                'errors' => $e->errors(),
            ]);
            
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}
```

### In Database Layer

```php
<?php
// Logging in database operations

class UserRepository {
    public function __construct(
        private PDO $pdo,
        private LoggerInterface $logger
    ) {}
    
    public function find($id) {
        $this->logger->debug('Finding user', ['id' => $id]);
        
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($user) {
            $this->logger->debug('User found', ['id' => $id]);
        } else {
            $this->logger->debug('User not found', ['id' => $id]);
        }
        
        return $user;
    }
    
    public function create($data) {
        $this->logger->debug('Creating user', [
            'email' => $data['email'],
        ]);
        
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (email, password) VALUES (?, ?)'
        );
        
        $stmt->execute([
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
        ]);
        
        $id = $this->pdo->lastInsertId();
        
        $this->logger->info('User created', [
            'user_id' => $id,
            'email' => $data['email'],
        ]);
        
        return $this->find($id);
    }
}
```

---

## Dependency Injection

### Constructor Injection

```php
<?php
// Preferred: Constructor injection

class OrderService {
    public function __construct(
        private LoggerInterface $logger,
        private OrderRepository $repository,
        private PaymentGateway $payment
    ) {}
    
    public function process($order) {
        $this->logger->info('Processing order', [
            'order_id' => $order->id,
        ]);
        
        // Logic here
    }
}

// Container configuration
$container->register(OrderService::class, function($c) {
    return new OrderService(
        $c->get(LoggerInterface::class),
        $c->get(OrderRepository::class),
        $c->get(PaymentGateway::class)
    );
});
```

### Property Injection

```php
<?php
// Alternative: Property injection (less preferred)

class OrderService {
    private LoggerInterface $logger;
    
    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }
    
    public function process($order) {
        $this->logger?->info('Processing order', [
            'order_id' => $order->id,
        ]);
    }
}

// Usage
$service = new OrderService();
$service->setLogger($logger);
```

### Service Locator (Not Recommended)

```php
<?php
// Service locator pattern (anti-pattern, but sometimes used)

class Container {
    private static $instance;
    private $services = [];
    
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function register($id, $service) {
        $this->services[$id] = $service;
    }
    
    public function get($id) {
        return $this->services[$id] ?? null;
    }
}

// Usage (not recommended)
class OrderService {
    public function process($order) {
        $logger = Container::getInstance()->get(LoggerInterface::class);
        $logger->info('Processing order', ['order_id' => $order->id]);
    }
}
```

---

## Logger Traits

### Logging Trait

```php
<?php
// Reusable logging trait

trait Loggable {
    protected LoggerInterface $logger;
    
    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }
    
    protected function log($level, $message, $context = []) {
        if (isset($this->logger)) {
            $this->logger->log($level, $message, $context);
        }
    }
    
    protected function logInfo($message, $context = []) {
        $this->log('info', $message, $context);
    }
    
    protected function logError($message, $context = []) {
        $this->log('error', $message, $context);
    }
    
    protected function logWarning($message, $context = []) {
        $this->log('warning', $message, $context);
    }
    
    protected function logDebug($message, $context = []) {
        $this->log('debug', $message, $context);
    }
}

// Usage
class UserService {
    use Loggable;
    
    public function register($data) {
        $this->logInfo('User registration started', [
            'email' => $data['email'],
        ]);
        
        // Process registration
    }
}
```

### Context Trait

```php
<?php
// Add automatic context to logs

trait LogContext {
    private $logContext = [];
    
    public function withContext($key, $value) {
        $this->logContext[$key] = $value;
        return $this;
    }
    
    protected function getContext($context = []) {
        return array_merge($this->logContext, $context);
    }
}

// Usage
class OrderService {
    use Loggable;
    use LogContext;
    
    public function processOrder($order) {
        $this->withContext('order_id', $order->id);
        $this->withContext('user_id', $order->user_id);
        
        $this->logInfo('Order processing started', [
            'total' => $order->total,
        ]);
        // order_id and user_id automatically included
    }
}
```

---

## Best Practices

### Do's

```php
<?php
// DO: Log with context
$logger->info('Payment processed', [
    'order_id' => 123,
    'amount' => 99.99,
    'gateway' => 'stripe',
]);

// DO: Use appropriate levels
$logger->debug('Loop iteration');
$logger->info('User action');
$logger->warning('Unusual behavior');
$logger->error('Operation failed');

// DO: Log at service boundaries
$logger->info('API call started');
$result = $api->process();
$logger->info('API call completed');

// DO: Include timing information
$start = microtime(true);
$result = heavyOperation();
$duration = microtime(true) - $start;
$logger->info('Operation completed', [
    'duration_ms' => round($duration * 1000, 2),
]);
```

### Don'ts

```php
<?php
// DON'T: Log without context
$logger->info('Something happened');

// DON'T: Log sensitive data
$logger->info('User logged in', [
    'username' => $username,
    'password' => $password,  // NEVER!
]);

// DON'T: Log large objects
$logger->debug('Processing', ['object' => $largeObject]);

// DON'T: Use wrong level
$logger->critical('User viewed page');  // Should be info or debug

// DON'T: Ignore errors
try {
    // operation
} catch (Exception $e) {
    // Silent failure - BAD!
}
```

---

## Complete Examples

### Example 1: Service with Comprehensive Logging

```php
<?php
// Service with proper logging

class PaymentService {
    public function __construct(
        private LoggerInterface $logger,
        private PaymentGateway $gateway,
        private OrderRepository $orders
    ) {}
    
    public function process($orderId) {
        $this->logger->info('Payment processing started', [
            'order_id' => $orderId,
        ]);
        
        $start = microtime(true);
        
        try {
            // Fetch order
            $order = $this->orders->find($orderId);
            
            if (!$order) {
                $this->logger->warning('Order not found', [
                    'order_id' => $orderId,
                ]);
                return false;
            }
            
            // Process payment
            $this->logger->debug('Calling payment gateway', [
                'gateway' => $this->gateway->name(),
                'amount' => $order->total,
            ]);
            
            $result = $this->gateway->charge(
                $order->total,
                $order->payment_method
            );
            
            $duration = microtime(true) - $start;
            
            if ($result->success) {
                $this->logger->info('Payment successful', [
                    'order_id' => $orderId,
                    'transaction_id' => $result->id,
                    'amount' => $order->total,
                    'duration_ms' => round($duration * 1000, 2),
                ]);
                
                return true;
            } else {
                $this->logger->error('Payment failed', [
                    'order_id' => $orderId,
                    'reason' => $result->error,
                    'duration_ms' => round($duration * 1000, 2),
                ]);
                
                return false;
            }
        } catch (Exception $e) {
            $duration = microtime(true) - $start;
            
            $this->logger->critical('Payment exception', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'duration_ms' => round($duration * 1000, 2),
            ]);
            
            throw $e;
        }
    }
}
```

### Example 2: Request Handler with Logging

```php
<?php
// HTTP handler with comprehensive logging

class RequestHandler {
    public function __construct(
        private LoggerInterface $logger,
        private Router $router,
        private Container $container
    ) {}
    
    public function handle() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestId = uniqid('req_', true);
        
        $this->logger->info('Request started', [
            'request_id' => $requestId,
            'method' => $method,
            'path' => $path,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        ]);
        
        $start = microtime(true);
        $statusCode = 500;
        
        try {
            // Route request
            $route = $this->router->match($method, $path);
            
            if (!$route) {
                $statusCode = 404;
                $this->logger->warning('Route not found', [
                    'request_id' => $requestId,
                    'method' => $method,
                    'path' => $path,
                ]);
                return $this->respond(404, 'Not Found');
            }
            
            $this->logger->debug('Route matched', [
                'request_id' => $requestId,
                'handler' => $route->handler,
            ]);
            
            // Execute handler
            $response = $route->handler();
            $statusCode = $response->statusCode ?? 200;
            
            $duration = microtime(true) - $start;
            
            $this->logger->info('Request completed', [
                'request_id' => $requestId,
                'status' => $statusCode,
                'duration_ms' => round($duration * 1000, 2),
            ]);
            
            return $response;
        } catch (Exception $e) {
            $statusCode = 500;
            $duration = microtime(true) - $start;
            
            $this->logger->critical('Unhandled exception', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'duration_ms' => round($duration * 1000, 2),
            ]);
            
            return $this->respond(500, 'Server Error');
        }
    }
    
    private function respond($status, $body) {
        http_response_code($status);
        return (object)['statusCode' => $status, 'body' => $body];
    }
}
```

---

## Key Takeaways

**Logger Usage Checklist:**

1. ✅ Inject logger as dependency
2. ✅ Use appropriate log levels
3. ✅ Include relevant context
4. ✅ Log at boundaries (services, APIs)
5. ✅ Include timing information
6. ✅ Don't log sensitive data
7. ✅ Handle exceptions properly
8. ✅ Test logging in development

---

## See Also

- [Logging Basics](0-logging-basics.md)
- [Logging Libraries](2-logging-library.md)
- [Handlers](5-handler.md)
