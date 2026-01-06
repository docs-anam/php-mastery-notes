# Popular Logging Libraries and Frameworks

## Overview

Explore industry-standard logging libraries for PHP including Monolog, PSR-3, and other popular solutions.

---

## Table of Contents

1. PSR-3 Standard
2. Monolog Library
3. Comparing Libraries
4. Installation and Setup
5. Choosing a Library
6. Integration Examples
7. Complete Examples

---

## PSR-3 Standard

### PSR-3 Logger Interface

```php
<?php
// PSR-3: Logger Interface

interface LoggerInterface {
    public function debug($message, array $context = []);
    public function info($message, array $context = []);
    public function notice($message, array $context = []);
    public function warning($message, array $context = []);
    public function error($message, array $context = []);
    public function critical($message, array $context = []);
    public function alert($message, array $context = []);
    public function emergency($message, array $context = []);
    public function log($level, $message, array $context = []);
}
```

### Benefits of PSR-3

```
✓ Standard interface
✓ Library agnostic
✓ Easy to swap implementations
✓ Type-safe
✓ Well documented
✓ Industry standard
✓ Works with frameworks
```

### Using PSR-3

```php
<?php
// Accept logger as dependency
class UserController {
    public function __construct(
        private LoggerInterface $logger
    ) {}
    
    public function store($request) {
        try {
            $this->logger->info('Creating user', ['email' => $request->email]);
            $user = User::create($request->all());
            $this->logger->info('User created', ['user_id' => $user->id]);
        } catch (Exception $e) {
            $this->logger->error('Failed to create user', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

// Easy to swap logger
$logger = new MonologLogger();
// or
$logger = new CustomLogger();
// Code works with both
```

---

## Monolog Library

### What is Monolog

```
Monolog = Popular PSR-3 logging library for PHP

Features:
- Sends logs to files, sockets, inboxes, databases, web services
- Multiple handlers and formatters
- Processors for data enrichment
- Record stacking
- Flexible and extensible
```

### Installation

```bash
# Install via Composer
composer require monolog/monolog

# Or specific version
composer require "monolog/monolog:^2.0"
```

### Basic Usage

```php
<?php
use Monolog\Logger;
use Monolog\Handlers\StreamHandler;

// Create logger
$logger = new Logger('my-app');

// Add handler (where logs go)
$logger->pushHandler(new StreamHandler('php://stdout'));

// Log messages
$logger->info('Application started');
$logger->error('Something went wrong', ['error' => 'Details']);
```

### Common Handlers

```php
<?php
use Monolog\Logger;
use Monolog\Handlers\{
    StreamHandler,
    FileHandler,
    RotatingFileHandler,
    SlackHandler,
    ErrorLogHandler,
};

$logger = new Logger('app');

// Stream (stdout/stderr)
$logger->pushHandler(new StreamHandler('php://stdout'));

// Single file
$logger->pushHandler(new FileHandler('logs/app.log'));

// Rotating file (by size, daily, etc.)
$logger->pushHandler(new RotatingFileHandler('logs/app.log', 30));

// Error log (system error log)
$logger->pushHandler(new ErrorLogHandler());

// Slack notifications
$logger->pushHandler(new SlackHandler(
    'https://hooks.slack.com/...',
    'general',
    'App Logger',
    true,
    null,
    Logger::WARNING
));
```

### Formatters

```php
<?php
use Monolog\Logger;
use Monolog\Handlers\FileHandler;
use Monolog\Formatters\{
    LineFormatter,
    JsonFormatter,
};

$logger = new Logger('app');
$handler = new FileHandler('logs/app.log');

// Line format
$formatter = new LineFormatter(
    "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
    "Y-m-d H:i:s"
);
$handler->setFormatter($formatter);

// or JSON format
$formatter = new JsonFormatter();
$handler->setFormatter($formatter);

$logger->pushHandler($handler);

// Output
// Line: [2025-01-06 14:30:45] app.INFO: Order created {"order_id":123}
// JSON: {"message":"Order created","context":{"order_id":123},...}
```

---

## Comparing Libraries

### Monolog

```
Pros:
✓ Most popular
✓ Many handlers
✓ Well documented
✓ PSR-3 compliant
✓ Good performance
✓ Active development

Cons:
✗ Larger codebase
✗ Learning curve
✗ Overkill for simple needs

Use for: Production applications
```

### PSR Logger

```php
<?php
// Minimal PSR-3 implementation

class SimpleLogger implements LoggerInterface {
    public function info($message, array $context = []) {
        echo "[INFO] $message " . json_encode($context) . "\n";
    }
    
    public function error($message, array $context = []) {
        echo "[ERROR] $message " . json_encode($context) . "\n";
    }
    
    // Implement other methods...
}
```

```
Pros:
✓ Simple
✓ No dependencies
✓ Fast
✓ Easy to understand

Cons:
✗ Limited features
✗ No handlers
✗ No formatters
✗ Manual implementation

Use for: Simple applications
```

### Syslog Logging

```php
<?php
// Use system syslog
openlog("myapp", LOG_PID | LOG_PERROR, LOG_LOCAL0);
syslog(LOG_INFO, "Application started");
syslog(LOG_ERR, "An error occurred");
closelog();
```

```
Pros:
✓ Built-in
✓ System integration
✓ Works with rsyslog
✓ No dependencies

Cons:
✗ Limited features
✗ Unix/Linux only
✗ Less flexible

Use for: System services
```

---

## Installation and Setup

### Composer Installation

```bash
# Install Monolog
composer require monolog/monolog

# Verify installation
composer show monolog/monolog
```

### Basic Configuration

```php
<?php
// config/logging.php

use Monolog\Logger;
use Monolog\Handlers\{
    StreamHandler,
    RotatingFileHandler,
};
use Monolog\Formatters\JsonFormatter;

return [
    'default' => 'single',
    
    'channels' => [
        'single' => [
            'driver' => 'single',
            'path' => 'logs/laravel.log',
        ],
        
        'daily' => [
            'driver' => 'daily',
            'path' => 'logs/laravel.log',
            'days' => 14,
        ],
        
        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],
    ],
];
```

### Factory Function

```php
<?php
// Create logger factory

class LoggerFactory {
    private static $loggers = [];
    
    public static function create($name = 'app') {
        if (isset(self::$loggers[$name])) {
            return self::$loggers[$name];
        }
        
        $logger = new Logger($name);
        
        // Add handlers based on environment
        if (getenv('APP_ENV') === 'production') {
            $logger->pushHandler(
                new RotatingFileHandler("logs/$name.log", 30)
            );
        } else {
            $logger->pushHandler(
                new StreamHandler('php://stdout')
            );
        }
        
        self::$loggers[$name] = $logger;
        return $logger;
    }
}

// Usage
$logger = LoggerFactory::create('app');
$logger->info('Application started');
```

---

## Choosing a Library

### Decision Matrix

```
Simple Application?
  YES → Use built-in logging
  NO → Use Monolog

Production Environment?
  YES → Use Monolog with multiple handlers
  NO → Use simple logger

Need Log Aggregation?
  YES → Use Monolog with Elasticsearch/Splunk
  NO → Use simple file logging

Team Size?
  Large → Use Monolog (standardized)
  Small → Can use simple logger
```

---

## Integration Examples

### Framework Integration

```php
<?php
// Laravel
class MyService {
    public function __construct(
        private \Psr\Log\LoggerInterface $logger
    ) {}
    
    public function process() {
        $this->logger->info('Processing started');
    }
}

// Symfony
class MyService {
    public function __construct(
        #[Autowire(service: 'logger')]
        private LoggerInterface $logger
    ) {}
}
```

### Service Container

```php
<?php
// Using a service container

class Container {
    private $services = [];
    
    public function register($name, $factory) {
        $this->services[$name] = $factory;
    }
    
    public function get($name) {
        if (isset($this->services[$name])) {
            return call_user_func($this->services[$name], $this);
        }
        return null;
    }
}

// Setup
$container = new Container();

$container->register('logger', function($c) {
    $logger = new Logger('app');
    $logger->pushHandler(
        new RotatingFileHandler('logs/app.log')
    );
    return $logger;
});

// Usage
$logger = $container->get('logger');
$logger->info('Event logged');
```

---

## Complete Examples

### Example 1: Multi-Handler Logger

```php
<?php
// Logger with multiple handlers

use Monolog\Logger;
use Monolog\Handlers\{
    StreamHandler,
    RotatingFileHandler,
    SlackHandler,
};
use Monolog\Formatters\JsonFormatter;

function createAppLogger() {
    $logger = new Logger('production-app');
    
    // Critical errors to Slack
    $slackHandler = new SlackHandler(
        'https://hooks.slack.com/services/YOUR/WEBHOOK',
        'critical',
        null,
        true,
        null,
        Logger::CRITICAL
    );
    $logger->pushHandler($slackHandler);
    
    // All errors to rotating file
    $fileHandler = new RotatingFileHandler('logs/errors.log', 30);
    $fileHandler->setFormatter(new JsonFormatter());
    $logger->pushHandler($fileHandler);
    
    // Debug to stdout in development
    if (getenv('APP_DEBUG')) {
        $streamHandler = new StreamHandler('php://stdout');
        $logger->pushHandler($streamHandler);
    }
    
    return $logger;
}

// Usage
$logger = createAppLogger();
$logger->critical('Payment processor down', ['service' => 'stripe']);
```

### Example 2: Channel-Based Logging

```php
<?php
// Different loggers for different parts

class LoggerManager {
    private $loggers = [];
    
    public function channel($name) {
        if (!isset($this->loggers[$name])) {
            $logger = new Logger($name);
            
            // Each channel logs to separate file
            $handler = new RotatingFileHandler(
                "logs/$name.log",
                30
            );
            
            $handler->setFormatter(new JsonFormatter());
            $logger->pushHandler($handler);
            
            $this->loggers[$name] = $logger;
        }
        
        return $this->loggers[$name];
    }
}

// Usage
$logManager = new LoggerManager();

$logManager->channel('payments')->info('Payment processed', [
    'amount' => 99.99,
    'method' => 'credit_card',
]);

$logManager->channel('security')->warning('Failed login attempt', [
    'ip' => '192.168.1.1',
]);

$logManager->channel('database')->info('Query executed', [
    'query' => 'SELECT...',
    'duration' => 45,
]);
```

---

## Key Takeaways

**Logging Library Checklist:**

1. ✅ Use PSR-3 compliant library
2. ✅ Choose appropriate handlers
3. ✅ Configure formatters
4. ✅ Set up log rotation
5. ✅ Define log channels
6. ✅ Handle errors gracefully
7. ✅ Test logging in development
8. ✅ Monitor in production

---

## See Also

- [Logging Basics](0-logging-basics.md)
- [Handlers](5-handler.md)
- [Processors](9-processor.md)
