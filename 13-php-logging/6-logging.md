# Basic Logging Operations and API

## Overview

Master the core logging operations and the PSR-3 logger API for different log levels and use cases.

---

## Table of Contents

1. Log Levels Overview
2. Logging Methods
3. Message Formatting
4. Context Usage
5. Error Handling
6. Performance Considerations
7. Complete Examples

---

## Log Levels Overview

### RFC 5424 Levels

```php
<?php
use Monolog\Logger;

// Log level constants
Logger::DEBUG      = 100    // Detailed debug information
Logger::INFO       = 200    // General information
Logger::NOTICE     = 250    // Normal but significant
Logger::WARNING    = 300    // Warning condition
Logger::ERROR      = 400    // Error condition
Logger::CRITICAL   = 500    // Critical condition
Logger::ALERT      = 550    // Must take action
Logger::EMERGENCY  = 600    // System unusable
```

### Level Hierarchy

```
DEBUG       (lowest priority)
  ↓
INFO
  ↓
NOTICE
  ↓
WARNING
  ↓
ERROR
  ↓
CRITICAL
  ↓
ALERT
  ↓
EMERGENCY   (highest priority)
```

---

## Logging Methods

### Info Method

```php
<?php
// General informational messages

$logger->info('User logged in', [
    'user_id' => 123,
    'ip' => '192.168.1.1',
]);

$logger->info('Order created', [
    'order_id' => 456,
    'total' => 99.99,
]);

$logger->info('Email sent', [
    'recipient' => 'user@example.com',
    'subject' => 'Welcome',
]);
```

### Debug Method

```php
<?php
// Detailed debugging information

$logger->debug('Processing item', [
    'item_id' => 1,
    'value' => $value,
    'processed' => true,
]);

$logger->debug('Query executed', [
    'sql' => 'SELECT * FROM users WHERE id = ?',
    'bindings' => [123],
    'time_ms' => 45.2,
]);

$logger->debug('Loop iteration', [
    'iteration' => $i,
    'count' => $total,
    'progress' => ($i / $total) * 100,
]);
```

### Warning Method

```php
<?php
// Warning conditions

$logger->warning('High memory usage', [
    'current' => '768MB',
    'limit' => '1024MB',
    'percent' => 75,
]);

$logger->warning('Cache miss', [
    'key' => 'user:123',
    'fallback' => 'database',
]);

$logger->warning('Slow query', [
    'query' => 'SELECT...',
    'time_ms' => 5000,
]);
```

### Error Method

```php
<?php
// Error conditions

$logger->error('Database connection failed', [
    'host' => 'db.example.com',
    'error' => 'Connection timeout',
    'attempts' => 3,
]);

$logger->error('Payment processing failed', [
    'order_id' => 456,
    'reason' => 'Insufficient funds',
]);

$logger->error('File not found', [
    'path' => '/path/to/file.txt',
    'function' => 'loadConfig',
]);
```

### Critical Method

```php
<?php
// Critical conditions

$logger->critical('System out of memory', [
    'available' => '0MB',
    'required' => '50MB',
]);

$logger->critical('Database unavailable', [
    'host' => 'db.example.com',
    'error' => 'Connection refused',
]);

$logger->critical('Configuration file missing', [
    'path' => '/config/app.php',
    'impact' => 'Application cannot start',
]);
```

### Alert and Emergency

```php
<?php
// Use sparingly - highest severity

$logger->alert('Security breach detected', [
    'type' => 'SQL injection attempt',
    'source_ip' => '192.168.1.100',
    'timestamp' => time(),
]);

$logger->emergency('Disk full - no space left', [
    'filesystem' => '/',
    'free_space' => '0KB',
    'impact' => 'Application cannot write logs',
]);
```

---

## Message Formatting

### Simple Messages

```php
<?php
// Clear, concise messages

$logger->info('User logged in');
$logger->error('Connection failed');
$logger->warning('Configuration deprecated');
```

### Parameterized Messages

```php
<?php
// Messages with interpolation (PSR-3 style)

$logger->info('User {user_id} logged in', [
    'user_id' => 123,
]);

$logger->error('Failed to process {action}', [
    'action' => 'payment',
]);

// Message: "User 123 logged in"
// Context: ["user_id" => 123]
```

### Descriptive Messages

```php
<?php
// Messages that describe the action

// Good
$logger->info('User registration completed', [
    'user_id' => 123,
    'email' => 'user@example.com',
]);

// Less good
$logger->info('User event', [
    'user_id' => 123,
]);

// Good
$logger->error('Payment gateway request timeout', [
    'gateway' => 'stripe',
    'timeout' => 30,
]);

// Less good
$logger->error('Request error', ['error' => 'timeout']);
```

---

## Context Usage

### Context Arrays

```php
<?php
// Context contains relevant data

$logger->info('Order processed', [
    'order_id' => 456,
    'user_id' => 123,
    'items' => 3,
    'total' => 99.99,
    'payment_method' => 'credit_card',
    'shipping_address' => '123 Main St',
]);
```

### Nested Context

```php
<?php
// Complex context structures

$logger->info('API request', [
    'request' => [
        'method' => 'POST',
        'url' => 'https://api.example.com/users',
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ],
    'response' => [
        'status' => 201,
        'time_ms' => 145,
    ],
]);
```

### Exception Context

```php
<?php
// Include exception information

try {
    // operation
} catch (Exception $e) {
    $logger->error('Operation failed', [
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
}

// or use exception parameter
$logger->error('Operation failed', ['exception' => $e]);
```

---

## Error Handling

### Try-Catch Logging

```php
<?php
// Proper error logging

try {
    $user = $repository->find($id);
    $logger->debug('User found', ['id' => $id]);
} catch (UserNotFoundException $e) {
    $logger->warning('User not found', [
        'id' => $id,
        'searched_at' => time(),
    ]);
} catch (DatabaseException $e) {
    $logger->error('Database error', [
        'error' => $e->getMessage(),
        'query' => 'SELECT * FROM users',
    ]);
} catch (Exception $e) {
    $logger->critical('Unexpected error', [
        'error' => $e->getMessage(),
        'type' => get_class($e),
    ]);
}
```

### Exception Logging

```php
<?php
// Log exceptions with context

function handleException(Exception $e) {
    $context = [
        'exception_class' => get_class($e),
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'previous' => $e->getPrevious() ? [
            'message' => $e->getPrevious()->getMessage(),
            'class' => get_class($e->getPrevious()),
        ] : null,
    ];
    
    // Determine level based on exception type
    if ($e instanceof ValidationException) {
        $logger->warning('Validation failed', $context);
    } elseif ($e instanceof DatabaseException) {
        $logger->error('Database error', $context);
    } else {
        $logger->critical('Unhandled exception', $context);
    }
}
```

---

## Performance Considerations

### Lazy Evaluation

```php
<?php
// Don't compute expensive values unnecessarily

// BAD: Always computes serialized data
$logger->debug('Processing', [
    'data' => json_encode($largeArray),
]);

// GOOD: Only compute if debug is enabled
if ($logger->isHandling(Logger::DEBUG)) {
    $logger->debug('Processing', [
        'data' => json_encode($largeArray),
    ]);
}

// BETTER: Use callable
$logger->debug('Processing', [
    'data' => fn() => json_encode($largeArray),
]);
```

### Level Checks

```php
<?php
// Check log level before expensive operations

// Avoid unnecessary work
if ($logger->isHandling(Logger::DEBUG)) {
    $metrics = calculateMetrics($data);
    $logger->debug('Metrics', $metrics);
}

// Check multiple levels
if ($logger->isHandling(Logger::WARNING)) {
    $logger->warning('High resource usage', [
        'memory' => memory_get_usage(),
        'time' => microtime(true) - $start,
    ]);
}
```

### Log Sampling

```php
<?php
// Sample logs to reduce volume

class SamplingLogger {
    private $sampleRate = 0.1; // 10% of logs
    
    public function logIfSample($level, $message, $context = []) {
        if (mt_rand(1, 100) <= ($this->sampleRate * 100)) {
            $this->logger->log($level, $message, $context);
        }
    }
}

// Usage
$sampler = new SamplingLogger($logger, 0.1);
for ($i = 0; $i < 1000; $i++) {
    $sampler->logIfSample(Logger::DEBUG, 'Item processed');
}
// Only ~100 logs recorded
```

---

## Complete Examples

### Example 1: Request-Response Logging

```php
<?php
// Log complete request-response cycle

class RequestLogger {
    public function __construct(
        private LoggerInterface $logger
    ) {}
    
    public function handleRequest($request) {
        $requestId = uniqid('req_', true);
        $start = microtime(true);
        
        $this->logger->info('Request started', [
            'request_id' => $requestId,
            'method' => $request->method,
            'path' => $request->path,
            'query' => $request->query,
            'ip' => $request->ip,
        ]);
        
        try {
            $response = $this->process($request);
            $duration = microtime(true) - $start;
            
            $this->logger->info('Request completed', [
                'request_id' => $requestId,
                'status' => $response->status,
                'duration_ms' => round($duration * 1000, 2),
            ]);
            
            return $response;
        } catch (Exception $e) {
            $duration = microtime(true) - $start;
            
            $this->logger->error('Request failed', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'duration_ms' => round($duration * 1000, 2),
            ]);
            
            throw $e;
        }
    }
}
```

### Example 2: Business Operation Logging

```php
<?php
// Log business-level operations

class OrderProcessor {
    public function __construct(
        private LoggerInterface $logger
    ) {}
    
    public function process($order) {
        $this->logger->info('Order processing started', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'items' => count($order->items),
            'total' => $order->total,
        ]);
        
        // Validate
        if (!$this->validate($order)) {
            $this->logger->warning('Order validation failed', [
                'order_id' => $order->id,
                'reason' => 'Invalid payment method',
            ]);
            return false;
        }
        
        // Process payment
        $this->logger->debug('Processing payment', [
            'order_id' => $order->id,
            'gateway' => 'stripe',
            'amount' => $order->total,
        ]);
        
        try {
            $payment = $this->payment->charge($order);
            
            $this->logger->info('Payment successful', [
                'order_id' => $order->id,
                'transaction_id' => $payment->id,
                'amount' => $order->total,
            ]);
        } catch (PaymentException $e) {
            $this->logger->error('Payment failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
        
        // Send confirmation
        $this->notification->send($order);
        
        $this->logger->info('Order completed', [
            'order_id' => $order->id,
            'status' => 'shipped',
        ]);
        
        return true;
    }
}
```

---

## Key Takeaways

**Logging Operations Checklist:**

1. ✅ Use correct log level
2. ✅ Include relevant context
3. ✅ Write clear messages
4. ✅ Log exceptions properly
5. ✅ Check log levels for expensive ops
6. ✅ Avoid redundant logging
7. ✅ Keep logs searchable
8. ✅ Review logs regularly

---

## See Also

- [Logging Basics](0-logging-basics.md)
- [Log Levels](7-level.md)
- [Handlers](5-handler.md)
