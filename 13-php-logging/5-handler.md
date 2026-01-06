# Handlers and Where Logs Go

## Overview

Learn about log handlers that determine where your logs are written and how to configure multiple handlers for different outputs.

---

## Table of Contents

1. What are Handlers
2. File Handlers
3. Stream Handlers
4. Network Handlers
5. Multiple Handlers
6. Handler Configuration
7. Complete Examples

---

## What are Handlers

### Purpose

```
Handler = Where logs are written

Responsibilities:
- Write to files
- Send to remote services
- Push to databases
- Send email notifications
- Display in console
- Filter by level
- Format output
```

### Handler Stack

```
Logger
  ↓
[Handlers Stack]
  ├─ FileHandler (all logs)
  ├─ SlackHandler (errors only)
  └─ EmailHandler (critical only)
  ↓
Outputs
  ├─ logs/app.log
  ├─ Slack channel
  └─ Email alert
```

---

## File Handlers

### StreamHandler (Files)

```php
<?php
// Write to file

use Monolog\Logger;
use Monolog\Handlers\StreamHandler;

$logger = new Logger('app');
$handler = new StreamHandler('logs/app.log');
$logger->pushHandler($handler);

// All logs go to logs/app.log
$logger->info('User logged in');
$logger->error('Connection failed');
```

### RotatingFileHandler

```php
<?php
// Rotate logs by size or date

use Monolog\Handlers\RotatingFileHandler;

$logger = new Logger('app');

// Rotate daily
$handler = new RotatingFileHandler('logs/app.log', 30);
// Creates: app.log, app-2025-01-06.log, app-2025-01-05.log, etc.

$logger->pushHandler($handler);
```

### Handler Configuration

```php
<?php
// Configure handler behavior

$handler = new StreamHandler('logs/app.log');

// Set minimum log level
$handler->setLevel(Logger::WARNING);

// Only log warnings and above, ignore debug/info

// Set file permissions
$handler->chmod(0644);

// Configure bubble (pass to next handler)
$handler->setBubble(true);
```

---

## Stream Handlers

### Standard Output/Error

```php
<?php
// Log to console

use Monolog\Handlers\StreamHandler;

$logger = new Logger('app');

// Write to stdout (normal output)
$handler = new StreamHandler('php://stdout');
$logger->pushHandler($handler);

// or stderr (error output)
$handler = new StreamHandler('php://stderr');
$logger->pushHandler($handler);

$logger->info('Hello World');
// Output: [2025-01-06T14:30:45.123456+00:00] app.INFO: Hello World
```

### Error Log Handler

```php
<?php
// Use system error log

use Monolog\Handlers\ErrorLogHandler;

$logger = new Logger('app');
$handler = new ErrorLogHandler();
$logger->pushHandler($handler);

// Logs to system error_log
$logger->error('System error');
```

---

## Network Handlers

### Slack Handler

```php
<?php
// Send to Slack

use Monolog\Handlers\SlackHandler;

$logger = new Logger('app');

$webhookUrl = 'https://hooks.slack.com/services/YOUR/WEBHOOK/URL';
$handler = new SlackHandler($webhookUrl, 'alerts');

// Only send errors to Slack
$handler->setLevel(Logger::ERROR);

$logger->pushHandler($handler);

// Usage
$logger->error('Payment processor down', [
    'service' => 'stripe',
    'impact' => 'Users cannot checkout',
]);
```

### Email Handler

```php
<?php
// Send critical logs via email

use Monolog\Handlers\NativeMailerHandler;

$logger = new Logger('app');

$handler = new NativeMailerHandler(
    'admin@example.com',      // recipient
    'Critical Error Alert',    // subject
    'errors@example.com'       // from
);

// Only send critical+ logs
$handler->setLevel(Logger::CRITICAL);

$logger->pushHandler($handler);
```

### Syslog Handler

```php
<?php
// Send to system syslog

use Monolog\Handlers\SyslogHandler;

$logger = new Logger('app');

$handler = new SyslogHandler('my-app', LOG_USER);
$logger->pushHandler($handler);

// Logs to /var/log/syslog (Linux/Mac)
$logger->info('Application event');
```

---

## Multiple Handlers

### Handler Stack

```php
<?php
// Multiple handlers, different purposes

use Monolog\Logger;
use Monolog\Handlers\{
    RotatingFileHandler,
    SlackHandler,
    NativeMailerHandler,
};

$logger = new Logger('production');

// 1. All logs to rotating file
$fileHandler = new RotatingFileHandler('logs/app.log', 30);
$logger->pushHandler($fileHandler);

// 2. Errors to Slack
$slackHandler = new SlackHandler(
    'https://hooks.slack.com/services/...',
    'errors'
);
$slackHandler->setLevel(Logger::ERROR);
$logger->pushHandler($slackHandler);

// 3. Critical alerts via email
$emailHandler = new NativeMailerHandler(
    'ops@example.com',
    'Critical Alert',
    'alerts@example.com'
);
$emailHandler->setLevel(Logger::CRITICAL);
$logger->pushHandler($emailHandler);

// Now:
$logger->info('User logged in');        // Goes to file only
$logger->error('API failed');           // Goes to file and Slack
$logger->critical('System down');       // Goes to file, Slack, and email
```

### Conditional Handlers

```php
<?php
// Add handlers based on conditions

class LoggerFactory {
    public static function create($env = 'production') {
        $logger = new Logger('app');
        
        if ($env === 'production') {
            // Production: file + alerts
            $logger->pushHandler(
                new RotatingFileHandler('logs/app.log', 30)
            );
            $logger->pushHandler(
                new SlackHandler($webhookUrl, 'errors')
                    ->setLevel(Logger::WARNING)
            );
        } else {
            // Development: stdout + debug info
            $logger->pushHandler(new StreamHandler('php://stdout'));
        }
        
        return $logger;
    }
}
```

---

## Handler Configuration

### Bubble Property

```php
<?php
// Control if log passes to next handler

$logger = new Logger('app');

// Handler 1: File
$fileHandler = new StreamHandler('logs/app.log');
$fileHandler->setBubble(true);  // Pass to next handler
$logger->pushHandler($fileHandler);

// Handler 2: Slack (only errors)
$slackHandler = new SlackHandler($webhookUrl);
$slackHandler->setLevel(Logger::ERROR);
$slackHandler->setBubble(false); // Don't pass further
$logger->pushHandler($slackHandler);

// Result:
// - Info logs: File only
// - Error logs: File and Slack
```

### Formatter Per Handler

```php
<?php
// Each handler can have its own format

use Monolog\Formatters\{
    LineFormatter,
    JsonFormatter,
};

$logger = new Logger('app');

// File: Human readable
$fileHandler = new StreamHandler('logs/app.log');
$fileHandler->setFormatter(
    new LineFormatter("[%datetime%] %level_name%: %message%\n")
);
$logger->pushHandler($fileHandler);

// Slack: JSON with context
$slackHandler = new SlackHandler($webhookUrl);
$slackHandler->setFormatter(new JsonFormatter());
$logger->pushHandler($slackHandler);
```

### Handler Levels

```php
<?php
// Different handlers for different levels

$logger = new Logger('app');

// Debug: Detailed logs only in development
$debugHandler = new StreamHandler('php://stdout');
$debugHandler->setLevel(Logger::DEBUG);
$debugHandler->setLevel(Logger::DEBUG, Logger::DEBUG);
$logger->pushHandler($debugHandler);

// Errors: File log for all errors
$errorHandler = new StreamHandler('logs/errors.log');
$errorHandler->setLevel(Logger::ERROR);
$logger->pushHandler($errorHandler);

// Critical: Alert immediately
$criticalHandler = new SlackHandler($webhookUrl);
$criticalHandler->setLevel(Logger::CRITICAL);
$logger->pushHandler($criticalHandler);
```

---

## Complete Examples

### Example 1: Production Logger

```php
<?php
// Production logging setup

use Monolog\Logger;
use Monolog\Handlers\{
    RotatingFileHandler,
    SlackHandler,
    NativeMailerHandler,
};
use Monolog\Formatters\JsonFormatter;

function createProductionLogger() {
    $logger = new Logger('production');
    
    // 1. All logs to rotating file
    $mainHandler = new RotatingFileHandler(
        'logs/app.log',
        30,  // Keep 30 days
        Logger::INFO
    );
    $mainHandler->setFormatter(new JsonFormatter());
    $logger->pushHandler($mainHandler);
    
    // 2. Errors to rotating file
    $errorHandler = new RotatingFileHandler(
        'logs/errors.log',
        60,
        Logger::ERROR
    );
    $errorHandler->setFormatter(new JsonFormatter());
    $logger->pushHandler($errorHandler);
    
    // 3. Warnings to Slack
    $slackHandler = new SlackHandler(
        'https://hooks.slack.com/services/YOUR/WEBHOOK',
        'warnings'
    );
    $slackHandler->setLevel(Logger::WARNING);
    $slackHandler->setBubble(true);
    $logger->pushHandler($slackHandler);
    
    // 4. Critical alerts via email
    $emailHandler = new NativeMailerHandler(
        'ops@example.com',
        '[CRITICAL] Production Alert',
        'alerts@example.com'
    );
    $emailHandler->setLevel(Logger::CRITICAL);
    $emailHandler->setBubble(false);
    $logger->pushHandler($emailHandler);
    
    return $logger;
}
```

### Example 2: Development Logger

```php
<?php
// Development logging setup

function createDevLogger() {
    $logger = new Logger('development');
    
    // All logs to console
    $handler = new StreamHandler('php://stdout');
    $handler->setFormatter(
        new LineFormatter(
            "%extra.datetime% %level_name% %channel%: %message%\n",
            "H:i:s"
        )
    );
    $logger->pushHandler($handler);
    
    return $logger;
}
```

### Example 3: Multi-Channel Logger

```php
<?php
// Different logs for different parts of application

class MultiChannelLogger {
    private $loggers = [];
    
    public function __construct() {
        $this->setupChannels();
    }
    
    private function setupChannels() {
        // Application logs
        $this->loggers['app'] = $this->createAppLogger();
        
        // Database logs
        $this->loggers['database'] = $this->createDatabaseLogger();
        
        // Security logs
        $this->loggers['security'] = $this->createSecurityLogger();
        
        // Payment logs
        $this->loggers['payments'] = $this->createPaymentLogger();
    }
    
    private function createAppLogger() {
        $logger = new Logger('app');
        $logger->pushHandler(
            new RotatingFileHandler('logs/app.log', 30)
        );
        return $logger;
    }
    
    private function createDatabaseLogger() {
        $logger = new Logger('database');
        $logger->pushHandler(
            new RotatingFileHandler('logs/database.log', 30)
        );
        return $logger;
    }
    
    private function createSecurityLogger() {
        $logger = new Logger('security');
        
        // File log
        $fileHandler = new RotatingFileHandler('logs/security.log', 90);
        $logger->pushHandler($fileHandler);
        
        // Alert on suspicious activity
        $slackHandler = new SlackHandler($webhookUrl, 'security');
        $slackHandler->setLevel(Logger::WARNING);
        $logger->pushHandler($slackHandler);
        
        return $logger;
    }
    
    private function createPaymentLogger() {
        $logger = new Logger('payments');
        
        // All transactions logged
        $fileHandler = new RotatingFileHandler('logs/payments.log', 90);
        $logger->pushHandler($fileHandler);
        
        // Critical payment issues
        $emailHandler = new NativeMailerHandler(
            'finance@example.com',
            'Payment Alert',
            'alerts@example.com'
        );
        $emailHandler->setLevel(Logger::ERROR);
        $logger->pushHandler($emailHandler);
        
        return $logger;
    }
    
    public function getLogger($channel) {
        return $this->loggers[$channel] ?? $this->loggers['app'];
    }
}
```

---

## Key Takeaways

**Handler Checklist:**

1. ✅ Choose appropriate handlers
2. ✅ Set minimum log level
3. ✅ Configure formatters
4. ✅ Use multiple handlers
5. ✅ Control bubble behavior
6. ✅ Organize by channels
7. ✅ Test in development
8. ✅ Monitor in production

---

## See Also

- [Logging Basics](0-logging-basics.md)
- [Logging Libraries](2-logging-library.md)
- [Formatters](11-formatter.md)
