# Context and Structured Logging

## Overview

Master context data and structured logging for searchable, analyzable logs that support log aggregation and monitoring.

---

## Table of Contents

1. Context Fundamentals
2. Structured Data
3. Context Strategies
4. Log Correlation
5. Context Performance
6. Common Patterns
7. Complete Examples

---

## Context Fundamentals

### What is Context

```
Context = Additional data accompanying log message

Example:
Message: "User logged in"
Context: {
  "user_id": 123,
  "email": "user@example.com",
  "ip": "192.168.1.1",
  "timestamp": 1704541845
}
```

### Context vs Message

```php
<?php
// Message = What happened
// Context = Details about what happened

$logger->info(
    'User logged in',           // Message
    [                           // Context
        'user_id' => 123,
        'email' => 'user@example.com',
        'ip' => '192.168.1.1',
    ]
);
```

### Structured Logging Benefits

```
✓ Queryable - Search by user_id, ip, etc.
✓ Parseable - Consistent JSON format
✓ Aggregatable - Combine data across logs
✓ Contextual - Rich information available
✓ Searchable - Works with ELK, Splunk, etc.
✓ Actionable - Metrics and alerts possible
```

---

## Structured Data

### Basic Structure

```php
<?php
// Simple context

$logger->info('User created', [
    'user_id' => 123,
    'email' => 'user@example.com',
    'status' => 'active',
]);

// Output (JSON):
// {
//   "message": "User created",
//   "context": {
//     "user_id": 123,
//     "email": "user@example.com",
//     "status": "active"
//   }
// }
```

### Nested Context

```php
<?php
// Complex nested data

$logger->info('Order completed', [
    'order' => [
        'id' => 456,
        'status' => 'shipped',
        'total' => 99.99,
    ],
    'customer' => [
        'id' => 123,
        'email' => 'customer@example.com',
        'vip' => true,
    ],
    'shipping' => [
        'method' => 'fedex',
        'tracking' => 'FX123456789',
        'eta' => '2025-01-10',
    ],
]);
```

### Array Context

```php
<?php
// List of items

$logger->info('Batch processing completed', [
    'batch_id' => 'batch_123',
    'items_processed' => 1000,
    'items' => [
        ['id' => 1, 'status' => 'success'],
        ['id' => 2, 'status' => 'success'],
        ['id' => 3, 'status' => 'failed', 'error' => 'Invalid'],
    ],
    'success_rate' => 99.7,
]);
```

---

## Context Strategies

### User Context

```php
<?php
// Always include user information

$logger->info('User action', [
    'user_id' => 123,
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'role' => 'admin',
    'ip_address' => '192.168.1.1',
    'user_agent' => 'Mozilla/5.0...',
]);
```

### Request Context

```php
<?php
// Identify requests

$logger->info('Request processed', [
    'request_id' => uniqid('req_', true),
    'method' => 'POST',
    'path' => '/api/users',
    'status_code' => 201,
    'duration_ms' => 145.2,
    'content_type' => 'application/json',
]);
```

### Business Context

```php
<?php
// Business-relevant information

$logger->info('Sale completed', [
    'order_id' => 456,
    'customer_id' => 123,
    'amount' => 99.99,
    'currency' => 'USD',
    'items' => 3,
    'discount' => 15.00,
    'tax' => 7.99,
    'payment_method' => 'credit_card',
    'status' => 'confirmed',
]);
```

### Technical Context

```php
<?php
// System-level information

$logger->info('Database operation', [
    'operation' => 'SELECT',
    'table' => 'users',
    'query' => 'SELECT * FROM users WHERE id = ?',
    'bindings' => [123],
    'duration_ms' => 45.2,
    'rows_returned' => 1,
    'connection' => 'primary',
]);
```

### Performance Context

```php
<?php
// Timing and resource usage

$logger->info('API request completed', [
    'endpoint' => '/api/users',
    'duration_ms' => 342.1,
    'cpu_seconds' => 0.28,
    'memory_mb' => 25.5,
    'network_ms' => 120,
    'database_ms' => 150,
    'cache_hits' => 3,
    'cache_misses' => 1,
]);
```

---

## Log Correlation

### Request ID Propagation

```php
<?php
// Trace requests across services

class RequestIdMiddleware {
    public function process($request, $next) {
        // Get or create request ID
        $requestId = $request->header('X-Request-ID')
            ?? uniqid('req_', true);
        
        // Add to context
        $request->requestId = $requestId;
        
        // Pass through
        $response = $next($request);
        
        // Include in response
        $response->header('X-Request-ID', $requestId);
        
        return $response;
    }
}

// Usage in services
$logger->info('User service called', [
    'request_id' => $request->requestId,
    'action' => 'create_user',
]);

// All logs with same request_id can be correlated
```

### Trace IDs for Distributed Systems

```php
<?php
// Correlation across microservices

class Service {
    public function process($data, $traceId = null) {
        $traceId = $traceId ?? uniqid('trace_', true);
        
        $this->logger->info('Processing started', [
            'trace_id' => $traceId,
            'service' => 'payment_service',
            'action' => 'process_payment',
        ]);
        
        // Call another service
        $result = $this->otherService->execute(
            $data,
            $traceId  // Pass trace ID
        );
        
        $this->logger->info('Processing completed', [
            'trace_id' => $traceId,
            'service' => 'payment_service',
            'result' => 'success',
        ]);
        
        return $result;
    }
}

// In log aggregation tool, query by trace_id to see full flow
```

### Session Context

```php
<?php
// Track user session

class SessionMiddleware {
    public function process($request, $next) {
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? null;
        
        // Add to request context
        $request->context = [
            'session_id' => $sessionId,
            'user_id' => $userId,
        ];
        
        return $next($request);
    }
}

// Use in all logs
$logger->info('User action', array_merge(
    $request->context,
    ['action' => 'view_profile']
));
```

---

## Context Performance

### Avoid Expensive Computations

```php
<?php
// DON'T: Serialize large objects

// BAD
$logger->debug('Processing', [
    'object' => json_encode($largeObject),
]);

// GOOD: Only what's needed
$logger->debug('Processing', [
    'object_id' => $largeObject->id,
    'type' => get_class($largeObject),
]);
```

### Lazy Evaluation

```php
<?php
// Use closures for expensive computations

// BAD: Always computes
$logger->debug('Metrics', [
    'data' => heavy_calculation(),
]);

// GOOD: Only if level is enabled
if ($logger->isHandling(Logger::DEBUG)) {
    $logger->debug('Metrics', [
        'data' => heavy_calculation(),
    ]);
}

// BETTER: Closure evaluated if logged
$logger->debug('Metrics', [
    'data' => fn() => heavy_calculation(),
]);
```

### Context Size Limits

```php
<?php
// Don't include excessive context

// BAD: Too much data
$logger->debug('Processing', [
    'all_records' => $records,  // Could be 1000s
    'entire_config' => $config,
    'full_response' => $response,
]);

// GOOD: Relevant data only
$logger->debug('Processing', [
    'record_count' => count($records),
    'config_version' => $config['version'],
    'response_status' => $response->status,
]);
```

---

## Common Patterns

### Error Context Pattern

```php
<?php
// Consistent error logging

try {
    $result = $operation();
} catch (ValidationException $e) {
    $logger->warning('Validation failed', [
        'operation' => 'create_user',
        'errors' => $e->errors(),
        'input' => ['email' => 'invalid@'],
    ]);
} catch (DatabaseException $e) {
    $logger->error('Database error', [
        'operation' => 'save_user',
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
        'query' => $e->getQuery(),
    ]);
} catch (Exception $e) {
    $logger->critical('Unexpected error', [
        'exception_class' => get_class($e),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
}
```

### Transaction Context

```php
<?php
// Track operations within transaction

$transactionId = uniqid('txn_', true);

$logger->info('Transaction started', [
    'transaction_id' => $transactionId,
    'amount' => 99.99,
]);

try {
    // Step 1
    $this->debitAccount($from);
    $logger->debug('Debit completed', [
        'transaction_id' => $transactionId,
        'account' => $from,
    ]);
    
    // Step 2
    $this->creditAccount($to);
    $logger->debug('Credit completed', [
        'transaction_id' => $transactionId,
        'account' => $to,
    ]);
    
    // Commit
    $this->commit();
    $logger->info('Transaction committed', [
        'transaction_id' => $transactionId,
        'status' => 'success',
    ]);
} catch (Exception $e) {
    $this->rollback();
    $logger->error('Transaction failed', [
        'transaction_id' => $transactionId,
        'status' => 'rolled back',
        'error' => $e->getMessage(),
    ]);
}
```

---

## Complete Examples

### Example 1: E-Commerce Order Processing

```php
<?php
// Comprehensive context for order processing

class OrderService {
    public function process($order, $requestId) {
        $logger->info('Order processing started', [
            'request_id' => $requestId,
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'items_count' => count($order->items),
            'total' => $order->total,
        ]);
        
        try {
            // Validate
            $this->validate($order);
            $logger->debug('Order validated', [
                'request_id' => $requestId,
                'order_id' => $order->id,
            ]);
            
            // Process payment
            $payment = $this->processPayment($order);
            $logger->info('Payment processed', [
                'request_id' => $requestId,
                'order_id' => $order->id,
                'transaction_id' => $payment->id,
                'amount' => $order->total,
                'method' => $order->payment_method,
            ]);
            
            // Create shipment
            $shipment = $this->createShipment($order);
            $logger->info('Shipment created', [
                'request_id' => $requestId,
                'order_id' => $order->id,
                'shipment_id' => $shipment->id,
                'tracking_number' => $shipment->tracking,
            ]);
            
            // Send confirmation
            $this->sendConfirmation($order);
            $logger->info('Confirmation sent', [
                'request_id' => $requestId,
                'order_id' => $order->id,
                'email' => $order->customer->email,
            ]);
            
            $logger->info('Order completed', [
                'request_id' => $requestId,
                'order_id' => $order->id,
                'status' => 'success',
                'duration_seconds' => $startTime->diff(new DateTime())->s,
            ]);
            
            return true;
        } catch (Exception $e) {
            $logger->error('Order processing failed', [
                'request_id' => $requestId,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'phase' => $this->getPhase(),
            ]);
            throw $e;
        }
    }
}
```

### Example 2: API Request Tracing

```php
<?php
// Complete request-to-response tracing

class ApiLogger {
    private $requestId;
    private $startTime;
    
    public function logRequest($method, $path, $query = []) {
        $this->requestId = uniqid('api_', true);
        $this->startTime = microtime(true);
        
        $this->logger->info('API request', [
            'request_id' => $this->requestId,
            'method' => $method,
            'path' => $path,
            'query' => $query,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        ]);
    }
    
    public function logResponse($statusCode, $data = []) {
        $duration = microtime(true) - $this->startTime;
        
        $this->logger->info('API response', array_merge([
            'request_id' => $this->requestId,
            'status_code' => $statusCode,
            'duration_ms' => round($duration * 1000, 2),
            'memory_mb' => round(memory_get_usage() / 1024 / 1024, 2),
        ], $data));
    }
    
    public function logError($statusCode, $error, $details = []) {
        $duration = microtime(true) - $this->startTime;
        
        $this->logger->error('API error', array_merge([
            'request_id' => $this->requestId,
            'status_code' => $statusCode,
            'error' => $error,
            'duration_ms' => round($duration * 1000, 2),
        ], $details));
    }
}
```

---

## Key Takeaways

**Context Checklist:**

1. ✅ Include identifying information
2. ✅ Use consistent context keys
3. ✅ Include business context
4. ✅ Add performance metrics
5. ✅ Use request/trace IDs
6. ✅ Avoid sensitive data
7. ✅ Keep context size reasonable
8. ✅ Structure for querying

---

## See Also

- [Logging Basics](0-logging-basics.md)
- [Basic Logging](6-logging.md)
- [Processors](9-processor.md)
