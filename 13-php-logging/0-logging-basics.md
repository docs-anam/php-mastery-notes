# Logging Basics and Concepts

## Overview

Learn the fundamentals of application logging, including why logging matters, what to log, and core logging concepts.

---

## Table of Contents

1. What is Logging
2. Why Logging Matters
3. Log Levels
4. Structured Logging
5. Logging Best Practices
6. Common Mistakes
7. Complete Examples

---

## What is Logging

### Definition

```
Logging = Recording application events

Purpose:
- Track application behavior
- Debug issues
- Monitor system health
- Audit user actions
- Performance analysis
- Error tracking
```

### Logging vs Debugging

```
Debugging:
- Interactive process
- Development phase
- Breakpoints and inspection
- Real-time analysis

Logging:
- Asynchronous process
- All environments
- Persistent records
- Historical analysis
```

### Log Structure

```
Timestamp: 2025-01-06 14:30:45.123
Level: ERROR
Channel: database
Message: Connection failed
Context:
  - host: db.example.com
  - error: timeout
  - duration: 5000ms
```

---

## Why Logging Matters

### Problem Solving

```php
<?php
// Without logging
$result = $db->query($sql);
if (!$result) {
    // What failed? Why? When?
    echo "Error";
}

// With logging
$logger->info('Executing query', ['sql' => $sql]);
$result = $db->query($sql);
if (!$result) {
    $logger->error('Query failed', [
        'sql' => $sql,
        'error' => $db->error(),
        'time' => time(),
    ]);
}
```

### Production Monitoring

```
Without Logging:
User reports "something broke"
↓
No visibility
↓
Can't diagnose
↓
Users suffer

With Logging:
User reports "something broke"
↓
Check logs for that time period
↓
Find error message and context
↓
Identify and fix issue
```

### Security and Compliance

```php
// Audit trail for regulatory requirements
$logger->info('User logged in', [
    'user_id' => $user->id,
    'ip' => $_SERVER['REMOTE_ADDR'],
    'timestamp' => time(),
]);

$logger->warning('Failed login attempt', [
    'username' => $_POST['username'],
    'ip' => $_SERVER['REMOTE_ADDR'],
]);

$logger->critical('Sensitive data accessed', [
    'user_id' => $user->id,
    'resource' => 'credit_cards',
    'action' => 'export',
]);
```

---

## Log Levels

### Standard Log Levels

```php
<?php
// RFC 5424 (Syslog)

// DEBUG (100)
// Diagnostic information
$logger->debug('Processing started', $data);

// INFO (200)
// Informational messages
$logger->info('User logged in', ['user_id' => 123]);

// NOTICE (250)
// Normal but significant
$logger->notice('Configuration changed', ['setting' => 'value']);

// WARNING (300)
// Potential problems
$logger->warning('High memory usage', ['usage' => '80%']);

// ERROR (400)
// Error occurred, function failed
$logger->error('Database connection lost', ['error' => 'timeout']);

// CRITICAL (500)
// Critical condition
$logger->critical('System failure', ['service' => 'payment']);

// ALERT (550)
// Must take action immediately
$logger->alert('Security breach detected', ['type' => 'sql_injection']);

// EMERGENCY (600)
// System unusable
$logger->emergency('Disk full', ['free_space' => 0]);
```

### Choosing Log Levels

```php
<?php
// What level should I use?

// DEBUG - Detailed flow information
$logger->debug('Loop iteration', ['iteration' => $i, 'value' => $item]);

// INFO - Confirmation that things are working
$logger->info('Order created', ['order_id' => 123, 'total' => 99.99]);

// WARNING - Something unexpected or degraded
$logger->warning('Cache miss', ['key' => 'user:123']);

// ERROR - Serious problem, function failed
$logger->error('Payment processing failed', ['order_id' => 123]);

// CRITICAL - System unstable
$logger->critical('Out of memory', ['peak' => '1024MB']);
```

---

## Structured Logging

### Context Data

```php
<?php
// Unstructured logging
$logger->info("User 123 logged in from 192.168.1.1 at 2025-01-06 14:30:45");

// Structured logging
$logger->info('User logged in', [
    'user_id' => 123,
    'ip_address' => '192.168.1.1',
    'timestamp' => 1704541845,
    'session_id' => 'abc123',
]);
```

### Benefits of Structured Logging

```
1. Queryable
   - Search by user_id: user_id = 123
   - Filter by time: timestamp >= 1704541845
   - Aggregate: count by ip_address

2. Parseable
   - Consistent format
   - Easy to parse
   - Works with log aggregation tools

3. Correlatable
   - Trace request across services
   - Follow user journey
   - Connect related events

4. Contextual
   - Rich information
   - Easier debugging
   - Better monitoring
```

### Context Examples

```php
<?php
// Request context
$logger->info('Request received', [
    'method' => 'POST',
    'path' => '/api/users',
    'ip' => '192.168.1.1',
    'user_agent' => 'Mozilla/5.0...',
]);

// Database context
$logger->info('Query executed', [
    'query' => 'SELECT * FROM users WHERE id = ?',
    'bindings' => [123],
    'duration_ms' => 45.2,
    'rows_affected' => 1,
]);

// Business context
$logger->info('Order processed', [
    'order_id' => 456,
    'user_id' => 123,
    'total' => 199.99,
    'items' => 3,
    'payment_method' => 'credit_card',
]);

// Error context
$logger->error('API call failed', [
    'endpoint' => 'https://api.payment.com/charge',
    'status_code' => 500,
    'error_message' => 'Internal Server Error',
    'retry_count' => 2,
    'response_time_ms' => 5000,
]);
```

---

## Logging Best Practices

### What to Log

```php
<?php
// DO: Log significant events
$logger->info('User registration completed', [
    'user_id' => $user->id,
    'email' => $user->email,
]);

$logger->error('Payment processing failed', [
    'order_id' => $order->id,
    'amount' => $order->total,
    'error' => $error->message,
]);

// DON'T: Log sensitive data
$logger->error('Login failed', [
    'username' => $username,  // Don't log credentials
    'password' => $password,  // Don't log secrets
]);

// DO: Log safely
$logger->error('Login failed', [
    'username_hash' => hash('sha256', $username),
    'attempts' => 3,
    'ip' => $_SERVER['REMOTE_ADDR'],
]);
```

### Performance Considerations

```php
<?php
// Avoid expensive operations in logs
// BAD: Will serialize entire user object
$logger->info('User action', ['user' => $user]);

// GOOD: Log only necessary data
$logger->info('User action', [
    'user_id' => $user->id,
    'username' => $user->username,
]);

// Lazy evaluation
// BAD: Expensive operation always runs
$logger->debug('Processing item', [
    'data' => json_encode($largeArray),
]);

// GOOD: Expensive operation only if debug enabled
$logger->debug('Processing item', [
    'data' => fn() => json_encode($largeArray),
]);
```

### Log Rotation

```php
// Prevent logs from growing too large
// - Rotate by size (10MB)
// - Rotate by time (daily)
// - Keep 30 days of history
// - Compress older logs
```

---

## Common Mistakes

### Mistake 1: Not Logging Enough

```php
<?php
// BAD: Insufficient logging
try {
    $result = $api->call();
} catch (Exception $e) {
    echo "Error";  // No visibility
}

// GOOD: Comprehensive logging
try {
    $logger->debug('Starting API call', ['endpoint' => $endpoint]);
    $result = $api->call();
    $logger->info('API call succeeded', ['status' => $result->status]);
} catch (Exception $e) {
    $logger->error('API call failed', [
        'endpoint' => $endpoint,
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
    ]);
}
```

### Mistake 2: Logging Sensitive Data

```php
<?php
// BAD: Logging passwords and tokens
$logger->info('User logged in', [
    'username' => $username,
    'password' => $password,  // NEVER!
    'api_key' => $apiKey,     // NEVER!
]);

// GOOD: Hash sensitive data
$logger->info('User logged in', [
    'username_hash' => hash('sha256', $username),
    'session_id' => substr(hash('sha256', $password), 0, 8),
    'ip' => $_SERVER['REMOTE_ADDR'],
]);
```

### Mistake 3: Poor Log Organization

```php
<?php
// BAD: All logs in one file
// 1000 MB log file
// Mix of everything
// Impossible to debug

// GOOD: Organized logging
// logs/
// ├── error.log (errors only)
// ├── debug.log (debug info)
// ├── access.log (requests)
// ├── payments.log (payment events)
// ├── security.log (security events)
```

### Mistake 4: Inconsistent Log Format

```php
<?php
// BAD: Different formats
$logger->info("User 123 logged in");
$logger->error("Connection failed: " . $error);
$logger->warning('Config missing');

// GOOD: Consistent format
$logger->info('User logged in', ['user_id' => 123]);
$logger->error('Connection failed', ['error' => $error]);
$logger->warning('Config missing', ['config' => 'email']);
```

---

## Complete Examples

### Example 1: Application Logger

```php
<?php
// Logger wrapper with best practices

class AppLogger {
    private $logger;
    private $requestId;
    
    public function __construct($logger) {
        $this->logger = $logger;
        $this->requestId = uniqid('req_', true);
    }
    
    public function info($message, $context = []) {
        $context['request_id'] = $this->requestId;
        $this->logger->info($message, $context);
    }
    
    public function error($message, $context = []) {
        $context['request_id'] = $this->requestId;
        $this->logger->error($message, $context);
    }
    
    public function warning($message, $context = []) {
        $context['request_id'] = $this->requestId;
        $this->logger->warning($message, $context);
    }
    
    public function debug($message, $context = []) {
        if (getenv('APP_DEBUG')) {
            $context['request_id'] = $this->requestId;
            $this->logger->debug($message, $context);
        }
    }
}

// Usage
$logger = new AppLogger($baseLogger);
$logger->info('Request started', ['method' => 'GET', 'path' => '/users']);
// All logs will have same request_id
```

### Example 2: Contextual Logging

```php
<?php
// Logger with automatic context

class ContextualLogger {
    private $logger;
    private $context = [];
    
    public function pushContext($key, $value) {
        $this->context[$key] = $value;
        return $this;
    }
    
    public function popContext($key) {
        unset($this->context[$key]);
        return $this;
    }
    
    public function info($message, $context = []) {
        $merged = array_merge($this->context, $context);
        $this->logger->info($message, $merged);
    }
    
    public function error($message, $context = []) {
        $merged = array_merge($this->context, $context);
        $this->logger->error($message, $merged);
    }
}

// Usage
$logger->pushContext('user_id', 123);
$logger->pushContext('request_id', 'abc123');

$logger->info('Processing order', ['order_id' => 456]);
// Logs will include user_id and request_id automatically
```

---

## Key Takeaways

**Logging Checklist:**

1. ✅ Log significant events
2. ✅ Use appropriate log levels
3. ✅ Include relevant context
4. ✅ Don't log sensitive data
5. ✅ Use structured format
6. ✅ Organize logs by type
7. ✅ Implement log rotation
8. ✅ Make logs searchable
9. ✅ Monitor log growth
10. ✅ Test logging in development

---

## See Also

- [Logging Libraries](2-logging-library.md)
- [Handlers and Processors](5-handler.md)
- [Log Levels](7-level.md)
