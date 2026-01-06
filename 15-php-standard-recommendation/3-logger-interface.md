# PSR-3: Logger Interface

## Overview

Learn about PSR-3, the standardized logging interface that enables interoperable logging implementations across PHP libraries and frameworks.

---

## Table of Contents

1. What is PSR-3
2. Logger Interface
3. Log Levels
4. Message Format
5. Context Data
6. Implementation Requirements
7. Using PSR-3 Loggers
8. Real-world Patterns
9. Complete Examples

---

## What is PSR-3

### Purpose

```php
<?php
// Problem: Every library has different logger

// Logger 1
class Logger1 {
    public function log_message($msg) {}
    public function log_error($msg) {}
}

// Logger 2
class Logger2 {
    public function write($msg) {}
    public function writeError($msg) {}
}

// Problem: Cannot swap implementations
// Each library has different interface

// Solution: PSR-3 standardizes logging interface

use Psr\Log\LoggerInterface;

interface LoggerInterface
{
    public function log($level, $message, array $context = array());
    public function emergency($message, array $context = array());
    public function alert($message, array $context = array());
    public function critical($message, array $context = array());
    public function error($message, array $context = array());
    public function warning($message, array $context = array());
    public function notice($message, array $context = array());
    public function info($message, array $context = array());
    public function debug($message, array $context = array());
}

// All loggers implement same interface
// Applications can swap loggers easily
```

### Benefits

```
✓ Interoperability - Mix libraries freely
✓ Flexibility - Swap logging implementations
✓ Consistency - Same method names everywhere
✓ Context - Rich structured logging
✓ Levels - RFC 5424 standard levels
✓ Easy testing - Mock PSR-3 loggers
```

---

## Logger Interface

### Core Methods

```php
<?php
use Psr\Log\LoggerInterface;

interface LoggerInterface
{
    // Main method
    public function log($level, $message, array $context = array());

    // Convenience methods (call log() internally)
    public function emergency($message, array $context = array());
    public function alert($message, array $context = array());
    public function critical($message, array $context = array());
    public function error($message, array $context = array());
    public function warning($message, array $context = array());
    public function notice($message, array $context = array());
    public function info($message, array $context = array());
    public function debug($message, array $context = array());
}
```

### Log Levels

```php
<?php
// RFC 5424 Log Levels (in order of severity)

use Psr\Log\LogLevel;

const EMERGENCY = 'emergency';  // System unusable
const ALERT = 'alert';          // Action required immediately
const CRITICAL = 'critical';    // Critical condition
const ERROR = 'error';          // Error condition
const WARNING = 'warning';      // Warning condition
const NOTICE = 'notice';        // Normal but significant
const INFO = 'info';            // Informational
const DEBUG = 'debug';          // Debug information

// Higher numbers = more severe
// 0: DEBUG (lowest)
// 7: EMERGENCY (highest)
```

### Basic Usage

```php
<?php
use Psr\Log\LoggerInterface;

class UserService
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function createUser(string $email): void
    {
        try {
            $this->logger->debug('Creating user', ['email' => $email]);

            // Validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->logger->warning('Invalid email format', ['email' => $email]);
                throw new InvalidEmailException();
            }

            // Business logic
            $this->logger->info('User created successfully', ['email' => $email]);

        } catch (Exception $e) {
            $this->logger->error('Failed to create user', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
```

---

## Log Levels

### Understanding Levels

```
Emergency (0)  - System is unusable - must take action now
Alert (1)      - Immediate action required
Critical (2)   - Critical condition
Error (3)      - Error condition (application can continue)
Warning (4)    - Warning condition (potential problem)
Notice (5)     - Normal but significant event
Info (6)       - Informational message
Debug (7)      - Debug-level information
```

### When to Use Each

```php
<?php
// EMERGENCY: System down
$logger->emergency('Database connection lost permanently');

// ALERT: Immediate action needed
$logger->alert('Suspicious activity detected');

// CRITICAL: Critical error
$logger->critical('Payment processing failed, data corruption detected');

// ERROR: Error condition
$logger->error('Failed to send email', ['recipient' => $email]);

// WARNING: Potential issue
$logger->warning('Retry attempt 3 of 5', ['operation' => 'api_call']);

// NOTICE: Significant but normal
$logger->notice('User login from new location', ['ip' => $ip]);

// INFO: General info
$logger->info('User created', ['email' => $email]);

// DEBUG: Development info
$logger->debug('Query executed', ['query' => $sql, 'time' => 0.15]);
```

### Conditional Logging

```php
<?php
class Logger implements LoggerInterface
{
    private string $minimumLevel = LogLevel::INFO;

    public function log($level, $message, array $context = []): void
    {
        $levels = [
            LogLevel::DEBUG => 0,
            LogLevel::INFO => 1,
            LogLevel::NOTICE => 2,
            LogLevel::WARNING => 3,
            LogLevel::ERROR => 4,
            LogLevel::CRITICAL => 5,
            LogLevel::ALERT => 6,
            LogLevel::EMERGENCY => 7,
        ];

        // Don't log below minimum level
        if ($levels[$level] < $levels[$this->minimumLevel]) {
            return;
        }

        // Log message
        $this->writeLog($level, $message, $context);
    }

    private function writeLog($level, $message, $context): void
    {
        // Implementation
    }
}
```

---

## Message Format

### Message Structure

```php
<?php
// Message is a string with optional placeholders

$logger->info('User {user} created account', [
    'user' => 'john@example.com'
]);

// Placeholders follow {placeholder} format
// Interpolation is optional (logger can leave as-is)
```

### Placeholder Interpolation

```php
<?php
class Logger implements LoggerInterface
{
    public function log($level, $message, array $context = []): void
    {
        // Option 1: Don't interpolate (store context separately)
        $this->write($level, $message, $context);

        // Option 2: Interpolate placeholders
        $interpolated = $this->interpolate($message, $context);
        $this->write($level, $interpolated, []);
    }

    private function interpolate(string $message, array $context): string
    {
        $replace = [];

        foreach ($context as $key => $value) {
            $replace['{' . $key . '}'] = $value;
        }

        return strtr($message, $replace);
    }
}

// Usage
$logger->info('User {email} logged in from {ip}', [
    'email' => 'john@example.com',
    'ip' => '192.168.1.1',
]);

// Result: "User john@example.com logged in from 192.168.1.1"
```

---

## Context Data

### Structured Logging

```php
<?php
// Context contains structured data about the event

// Without context: Hard to parse
$logger->info('Processing payment');

// With context: Structured data
$logger->info('Processing payment', [
    'user_id' => 123,
    'amount' => 99.99,
    'currency' => 'USD',
    'method' => 'credit_card',
    'timestamp' => date('Y-m-d H:i:s'),
]);

// Can be parsed and analyzed programmatically
```

### Exception Context

```php
<?php
try {
    // Operation
} catch (Exception $e) {
    // Log exception in context
    $logger->error('Operation failed', [
        'exception' => $e,
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
}

// Some loggers automatically extract exception details
```

### User and Request Context

```php
<?php
class RequestLogger
{
    private LoggerInterface $logger;

    public function logRequest($method, $path, $userId = null): void
    {
        $this->logger->info('HTTP Request', [
            'method' => $method,
            'path' => $path,
            'user_id' => $userId,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}
```

---

## Implementation Requirements

### Required Interface

```php
<?php
namespace Psr\Log;

interface LoggerInterface
{
    public function log($level, $message, array $context = array());
    public function emergency($message, array $context = array());
    public function alert($message, array $context = array());
    public function critical($message, array $context = array());
    public function error($message, array $context = array());
    public function warning($message, array $context = array());
    public function notice($message, array $context = array());
    public function info($message, array $context = array());
    public function debug($message, array $context = array());
}
```

### Basic Implementation

```php
<?php
declare(strict_types=1);

namespace App\Logging;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class FileLogger implements LoggerInterface
{
    private string $filePath;

    public function __construct(string $filePath = 'app.log')
    {
        $this->filePath = $filePath;
    }

    public function log($level, $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $formatted = "[$timestamp] $level: $message";

        if (!empty($context)) {
            $formatted .= ' ' . json_encode($context);
        }

        file_put_contents($this->filePath, $formatted . "\n", FILE_APPEND);
    }

    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}
```

---

## Using PSR-3 Loggers

### With Popular Implementations

```php
<?php
// Monolog
use Monolog\Logger;
use Monolog\Handlers\StreamHandler;

$logger = new Logger('app');
$logger->pushHandler(new StreamHandler('app.log'));

$logger->info('User created', ['email' => 'john@example.com']);

// Implements PSR-3
assert($logger instanceof LoggerInterface);
```

### Dependency Injection

```php
<?php
use Psr\Log\LoggerInterface;

class UserService
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function createUser(string $email): void
    {
        $this->logger->info('Creating user', ['email' => $email]);
    }
}

// Injection handles implementation
$service = new UserService($logger);
```

### With Container

```php
<?php
// Define in container
$container->set(LoggerInterface::class, function() {
    return new FileLogger();
});

// Inject into service
$service = $container->get(UserService::class);
// Container auto-injects logger
```

---

## Real-world Patterns

### Application Logging

```php
<?php
class Application
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function handle($request): void
    {
        try {
            $this->logger->info('Request received', [
                'method' => $request->getMethod(),
                'path' => $request->getPath(),
            ]);

            $response = $this->process($request);

            $this->logger->info('Request processed', [
                'status' => $response->getStatus(),
            ]);

        } catch (Exception $e) {
            $this->logger->error('Request failed', [
                'exception' => $e,
                'method' => $request->getMethod(),
                'path' => $request->getPath(),
            ]);

            throw $e;
        }
    }
}
```

### Error Handling

```php
<?php
class ErrorHandler
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function handleException(Exception $e): void
    {
        switch (true) {
            case $e instanceof ValidationException:
                $this->logger->warning('Validation failed', [
                    'message' => $e->getMessage(),
                ]);
                break;

            case $e instanceof DatabaseException:
                $this->logger->error('Database error', [
                    'exception' => $e,
                ]);
                break;

            default:
                $this->logger->critical('Unhandled exception', [
                    'exception' => $e,
                ]);
        }
    }
}
```

---

## Complete Examples

### Example 1: Complete Logger Implementation

```php
<?php
// File: src/Logging/FileLogger.php

declare(strict_types=1);

namespace App\Logging;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class FileLogger implements LoggerInterface
{
    private string $filePath;
    private bool $interpolateContext = true;

    public function __construct(
        string $filePath = 'app.log',
        bool $interpolate = true
    ) {
        $this->filePath = $filePath;
        $this->interpolateContext = $interpolate;
    }

    public function log($level, $message, array $context = []): void
    {
        if (!$this->isValidLevel($level)) {
            throw new InvalidArgumentException("Invalid log level: $level");
        }

        $timestamp = date('Y-m-d H:i:s');

        // Interpolate message
        $formattedMessage = $this->interpolate($message, $context);

        // Format log entry
        $entry = sprintf(
            "[%s] [%s] %s",
            $timestamp,
            strtoupper($level),
            $formattedMessage
        );

        // Add context if not interpolated
        if (!$this->interpolateContext && !empty($context)) {
            $entry .= ' ' . json_encode($context);
        }

        // Write to file
        file_put_contents(
            $this->filePath,
            $entry . "\n",
            FILE_APPEND | LOCK_EX
        );
    }

    private function interpolate(string $message, array $context): string
    {
        if (empty($context)) {
            return $message;
        }

        $replace = [];
        foreach ($context as $key => $value) {
            if (!is_scalar($value) && !is_null($value)) {
                $value = json_encode($value);
            }

            $replace['{' . $key . '}'] = $value;
        }

        return strtr($message, $replace);
    }

    private function isValidLevel(string $level): bool
    {
        return in_array($level, [
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::ERROR,
            LogLevel::WARNING,
            LogLevel::NOTICE,
            LogLevel::INFO,
            LogLevel::DEBUG,
        ], true);
    }

    // Convenience methods
    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}
```

### Example 2: Service Using Logger

```php
<?php
// File: src/Services/PaymentService.php

declare(strict_types=1);

namespace App\Services;

use Psr\Log\LoggerInterface;

class PaymentService
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function processPayment(
        int $userId,
        float $amount,
        string $method
    ): bool {
        $this->logger->info('Payment processing started', [
            'user_id' => $userId,
            'amount' => $amount,
            'method' => $method,
        ]);

        try {
            // Validate
            if ($amount <= 0) {
                $this->logger->warning('Invalid payment amount', [
                    'amount' => $amount,
                ]);
                return false;
            }

            // Process payment
            $this->logger->debug('Contacting payment gateway', [
                'method' => $method,
            ]);

            $result = $this->callPaymentGateway($method, $amount);

            if ($result) {
                $this->logger->info('Payment processed successfully', [
                    'user_id' => $userId,
                    'amount' => $amount,
                ]);
                return true;
            } else {
                $this->logger->error('Payment gateway rejected', [
                    'user_id' => $userId,
                    'amount' => $amount,
                ]);
                return false;
            }

        } catch (Exception $e) {
            $this->logger->critical('Payment processing failed', [
                'user_id' => $userId,
                'exception' => $e,
            ]);
            return false;
        }
    }

    private function callPaymentGateway(string $method, float $amount): bool
    {
        // Implementation
        return true;
    }
}
```

---

## Key Takeaways

**PSR-3 Logger Interface Checklist:**

1. ✅ Implement LoggerInterface
2. ✅ Support all 8 log levels
3. ✅ Handle context data
4. ✅ Interpolate placeholders (optional)
5. ✅ Use in dependency injection
6. ✅ Log at appropriate levels
7. ✅ Include context with messages
8. ✅ Test with mock loggers

---

## See Also

- [PSR Overview](0-psr-overview.md)
- [Container Interface (PSR-11)](7-container-interface.md)
- [Logging Basics (PHP Logging)](../13-php-logging/0-logging-basics.md)
