# PHP Logging - Application Observability & Monolog

## Table of Contents
1. [Overview](#overview)
2. [Why Logging Matters](#why-logging-matters)
3. [Logging Concepts](#logging-concepts)
4. [Monolog Framework](#monolog-framework)
5. [Log Levels](#log-levels)
6. [Handlers & Formatters](#handlers--formatters)
7. [Best Practices](#best-practices)
8. [Learning Path](#learning-path)
9. [Prerequisites](#prerequisites)

---

## Overview

Logging is critical for production applications. It allows you to:
- Track application behavior
- Debug issues in production
- Monitor performance
- Audit user actions
- Detect security threats

Without logging, troubleshooting production issues is nearly impossible.

## Why Logging Matters

### Debugging Without Logs

```
Production server stops working
↓
No visible error
↓
No logs to check
↓
Panic and desperation
↓
Hours of debugging
```

### With Proper Logging

```
Production server stops working
↓
Check logs
↓
Find exact error and stack trace
↓
Understand what happened
↓
Fix immediately
```

### Real-World Examples

**Payment Processing Failure**
```
2024-01-06 15:32:45 ERROR Payment gateway timeout for order #12345
2024-01-06 15:32:46 WARNING Retrying payment processing
2024-01-06 15:32:47 INFO Payment succeeded on retry
```

**Security Incident**
```
2024-01-06 16:15:22 WARNING Failed login attempt from 192.168.1.100
2024-01-06 16:15:23 WARNING Failed login attempt from 192.168.1.100
2024-01-06 16:15:24 ALERT Account locked after 3 failed attempts
```

**Performance Issue**
```
2024-01-06 17:20:10 WARNING Slow query detected: 5.2s
Database query: SELECT * FROM orders WHERE status = 'pending'
Execution time: 5234ms
```

## Logging Concepts

### Log Levels (Severity)

From least to most severe:

```
DEBUG    ← Development information
INFO     ← Normal operations
NOTICE   ← Important but not error
WARNING  ← Something unexpected
ERROR    ← Error occurred
CRITICAL ← Critical problem
ALERT    ← Action required immediately
EMERGENCY ← System unusable
```

### Log Channels

Different outputs for different purposes:

```
Single Channel:
└── All logs → Single file

Multiple Channels:
├── Application logs → File
├── Error logs → Email
├── Security logs → Database
└── Performance logs → Dashboard
```

### Structured Logging

Logs with consistent format for analysis:

```
Unstructured:
"User alice logged in at 2024-01-06 15:32:45"

Structured (JSON):
{
    "timestamp": "2024-01-06T15:32:45Z",
    "level": "info",
    "message": "User logged in",
    "user_id": 123,
    "user_name": "alice",
    "ip_address": "192.168.1.100"
}
```

## Monolog Framework

### What is Monolog?

Monolog is the standard PHP logging library with:
- Multiple output handlers
- Configurable formatters
- Log processors
- Channel support
- PSR-3 interface

### Installation

```bash
composer require monolog/monolog
```

### Basic Usage

```php
<?php
require_once 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handlers\StreamHandler;

// Create logger
$log = new Logger('app');

// Add handler (where logs go)
$log->pushHandler(new StreamHandler('app.log'));

// Log messages
$log->info('User login', ['user_id' => 123]);
$log->error('Database connection failed', ['error' => 'timeout']);
```

### Multiple Handlers

```php
use Monolog\Handlers\StreamHandler;
use Monolog\Handlers\RotatingFileHandler;
use Monolog\Handlers\NativeMailerHandler;

$log = new Logger('app');

// File handler
$log->pushHandler(new StreamHandler('app.log'));

// Rotating file handler (daily files)
$log->pushHandler(new RotatingFileHandler('logs/app.log', maxFiles: 30));

// Error emails for critical issues
$log->pushHandler(new NativeMailerHandler(
    to: 'admin@example.com',
    subject: 'Critical Error Alert',
    from: 'alerts@example.com',
    level: Logger::CRITICAL
));
```

## Log Levels

### Severity Scale

| Level | Value | Use Case |
|-------|-------|----------|
| DEBUG | 100 | Detailed development information |
| INFO | 200 | Important events, normal operation |
| NOTICE | 250 | Unusual but not error |
| WARNING | 300 | Potentially problematic |
| ERROR | 400 | Error condition |
| CRITICAL | 500 | Critical condition |
| ALERT | 550 | Alert condition |
| EMERGENCY | 600 | Emergency |

### Filtering by Level

```php
use Monolog\Logger;
use Monolog\Handlers\StreamHandler;

$log = new Logger('app');

// Log everything to file
$fileHandler = new StreamHandler('app.log');
$log->pushHandler($fileHandler);

// Only errors and above via email
$errorHandler = new NativeMailerHandler(
    to: 'admin@example.com',
    level: Logger::ERROR
);
$log->pushHandler($errorHandler);

// DEBUG and above to console (development)
$consoleHandler = new StreamHandler('php://stdout');
$log->pushHandler($consoleHandler);
```

## Handlers & Formatters

### Common Handlers

```php
// File handler
new StreamHandler('app.log');

// Rotating files
new RotatingFileHandler('logs/app.log', maxFiles: 30);

// Daily files
new SlackHandler($webhookUrl, 'app', Logger::WARNING);

// Email alerts
new NativeMailerHandler($to, $subject, $from, Logger::CRITICAL);

// Database logging
new MongoDBHandler($mongoConnection, 'logs');

// Syslog
new SyslogHandler('php');

// Browser console (development)
new FirePHPHandler();
```

### Formatters

Control log output format:

```php
use Monolog\Handlers\StreamHandler;
use Monolog\Formatters\LineFormatter;

$handler = new StreamHandler('app.log');

// Simple format
$formatter = new LineFormatter(
    format: "[%datetime%] %level_name%: %message%\n",
    dateFormat: 'Y-m-d H:i:s'
);

$handler->setFormatter($formatter);
$log->pushHandler($handler);
```

### Processors

Add context to logs:

```php
$log->pushProcessor(function ($record) {
    $record['extra']['user_id'] = $_SESSION['user_id'] ?? null;
    $record['extra']['ip_address'] = $_SERVER['REMOTE_ADDR'];
    $record['extra']['request_id'] = uniqid();
    return $record;
});
```

## Best Practices

### 1. Use Context Data

```php
// ❌ Don't - confusing
$log->info('User ' . $username . ' logged in');

// ✅ Do - structured
$log->info('User logged in', [
    'user_id' => $userId,
    'username' => $username,
    'ip_address' => $ipAddress,
    'timestamp' => date('c'),
]);
```

### 2. Log at Correct Level

```php
// ✅ Correct levels
$log->debug('Processing item 5 of 100');  // Development
$log->info('User registered successfully');  // Normal event
$log->warning('Slow query detected: 3.5s');  // Unexpected
$log->error('Failed to connect to API');  // Error
$log->critical('Out of memory');  // Critical
```

### 3. Log Security Events

```php
$log->warning('Failed login attempt', [
    'username' => $username,
    'ip_address' => $_SERVER['REMOTE_ADDR'],
    'timestamp' => time(),
]);

$log->critical('Multiple failed login attempts', [
    'username' => $username,
    'attempt_count' => 5,
    'action' => 'account_locked',
]);
```

### 4. Log Exceptions Completely

```php
try {
    $user = User::findOrFail($id);
} catch (Exception $e) {
    $log->error('User not found', [
        'exception' => $e,
        'user_id' => $id,
        'trace' => $e->getTraceAsString(),
    ]);
    
    throw $e;
}
```

### 5. Sensitive Data Handling

```php
// ❌ Don't log passwords
$log->info('User login', ['password' => $password]);

// ✅ Do - omit sensitive data
$log->info('User login', [
    'user_id' => $user->id,
    'method' => 'email',  // Not the actual email
    'success' => true,
]);
```

### 6. Performance Monitoring

```php
$startTime = microtime(true);

// ... do something

$duration = microtime(true) - $startTime;

if ($duration > 1.0) {  // Slower than 1 second
    $log->warning('Slow operation', [
        'operation' => 'database_query',
        'duration_seconds' => $duration,
        'query' => $query,
    ]);
}
```

### 7. Separate Concerns

```php
// Application logs
$appLog = new Logger('app');
$appLog->pushHandler(new StreamHandler('logs/app.log'));

// Error logs
$errorLog = new Logger('error');
$errorLog->pushHandler(new StreamHandler('logs/error.log'));

// Security logs
$securityLog = new Logger('security');
$securityLog->pushHandler(new StreamHandler('logs/security.log'));

// Use appropriately
$appLog->info('User registered');
$errorLog->error('Database connection failed');
$securityLog->warning('Unauthorized access attempt');
```

## Learning Path

Master logging progressively:

1. **Fundamentals** - Why logging matters
2. **Monolog Setup** - Install and configure
3. **Log Levels** - Understanding severity
4. **Handlers** - Where logs go
5. **Formatters** - How logs look
6. **Processors** - Adding context
7. **Channels** - Multiple loggers
8. **Rotating Files** - Log rotation
9. **Integration** - With MVC apps
10. **Monitoring** - Using logs to monitor

## Practical Example

```php
<?php
// config/logging.php
use Monolog\Logger;
use Monolog\Handlers\RotatingFileHandler;
use Monolog\Handlers\NativeMailerHandler;
use Monolog\Formatters\JsonFormatter;

function createLogger($name) {
    $log = new Logger($name);
    
    // Regular logs
    $handler = new RotatingFileHandler(
        'logs/' . $name . '.log',
        maxFiles: 30
    );
    $handler->setFormatter(new JsonFormatter());
    $log->pushHandler($handler);
    
    // Errors via email
    if (PRODUCTION) {
        $errorHandler = new NativeMailerHandler(
            to: 'admin@example.com',
            subject: "[$name] Error Alert",
            from: 'alerts@example.com',
            level: Logger::ERROR
        );
        $log->pushHandler($errorHandler);
    }
    
    // Add context
    $log->pushProcessor(function ($record) {
        $record['extra']['user_id'] = $_SESSION['user_id'] ?? null;
        $record['extra']['request_id'] = $_REQUEST['_request_id'] ?? uniqid();
        return $record;
    });
    
    return $log;
}

// Usage in application
$log = createLogger('app');
$log->info('Application started');
$log->error('Something went wrong', ['details' => $details]);
```

## Prerequisites

Before learning logging:

✅ **Required:**
- PHP basics (classes, functions)
- Understanding of error handling
- Familiarity with try/catch

✅ **Helpful:**
- Experience with file operations
- Understanding of JSON format
- Knowledge of HTTP request/response

## Resources

- **Monolog**: [github.com/Seldaek/monolog](https://github.com/Seldaek/monolog)
- **PSR-3 Logger**: [PHP-FIG PSR-3](https://www.php-fig.org/psr/psr-3/)
- **Structured Logging**: [12factor.net/logs](https://12factor.net/logs)
- **ELK Stack**: [elastic.co/what-is/elk-stack](https://www.elastic.co/what-is/elk-stack)
