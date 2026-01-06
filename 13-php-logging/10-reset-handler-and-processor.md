# Handlers with Reset and Custom Logic

## Overview

Advanced handler configuration including handler reset, stacking multiple handlers, and implementing custom handler logic.

---

## Table of Contents

1. Handler Reset and Management
2. Handler Stacking
3. Custom Handlers
4. Handler Bubbling
5. Conditional Handlers
6. Testing Handlers
7. Complete Examples

---

## Handler Reset and Management

### Removing Handlers

```php
<?php
// Clear handlers

$logger = new Logger('app');
$logger->pushHandler(new StreamHandler('logs/app.log'));
$logger->pushHandler(new StreamHandler('php://stdout'));

// Get all handlers
$handlers = $logger->getHandlers();
// Array with 2 handlers

// Clear all handlers
$logger->reset();
// Now 0 handlers

// Remove specific handler
$logger->popHandler();
// Removes last handler
```

### Handler Stack Management

```php
<?php
// Manage handler stack

class LoggerManager {
    private $logger;
    private $handlers = [];
    
    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }
    
    public function add($name, $handler) {
        $this->handlers[$name] = $handler;
        $this->logger->pushHandler($handler);
    }
    
    public function remove($name) {
        if (isset($this->handlers[$name])) {
            // Remove from logger
            $handlers = $this->logger->getHandlers();
            $key = array_search($this->handlers[$name], $handlers);
            
            if ($key !== false) {
                unset($handlers[$key]);
                $this->logger->reset();
                
                foreach ($handlers as $h) {
                    $this->logger->pushHandler($h);
                }
            }
            
            unset($this->handlers[$name]);
        }
    }
    
    public function getHandler($name) {
        return $this->handlers[$name] ?? null;
    }
    
    public function clear() {
        $this->logger->reset();
        $this->handlers = [];
    }
}

// Usage
$manager = new LoggerManager($logger);
$manager->add('file', new StreamHandler('logs/app.log'));
$manager->add('slack', new SlackHandler($webhookUrl));

// Later, remove Slack handler
$manager->remove('slack');
```

### Dynamic Handler Configuration

```php
<?php
// Add/remove handlers based on conditions

class DynamicLogger {
    private $logger;
    private $env;
    
    public function __construct(Logger $logger, $env) {
        $this->logger = $logger;
        $this->env = $env;
        $this->configureHandlers();
    }
    
    private function configureHandlers() {
        switch ($this->env) {
            case 'development':
                $this->logger->pushHandler(
                    new StreamHandler('php://stdout')
                );
                break;
            
            case 'staging':
                $this->logger->pushHandler(
                    new RotatingFileHandler('logs/staging.log', 30)
                );
                $this->logger->pushHandler(
                    new SlackHandler($webhookUrl)
                        ->setLevel(Logger::WARNING)
                );
                break;
            
            case 'production':
                $this->logger->pushHandler(
                    new RotatingFileHandler('logs/production.log', 90)
                );
                $this->logger->pushHandler(
                    new SlackHandler($webhookUrl)
                        ->setLevel(Logger::ERROR)
                );
                $this->logger->pushHandler(
                    new NativeMailerHandler('ops@example.com')
                        ->setLevel(Logger::CRITICAL)
                );
                break;
        }
    }
    
    public function switchEnvironment($env) {
        $this->logger->reset();
        $this->env = $env;
        $this->configureHandlers();
    }
}
```

---

## Handler Stacking

### Multiple Handlers Same Level

```php
<?php
// Log to multiple destinations

$logger = new Logger('app');

// Both handlers receive all logs
$logger->pushHandler(new StreamHandler('logs/app.log'));
$logger->pushHandler(new StreamHandler('php://stdout'));

$logger->info('Test');
// Writes to both file and stdout
```

### Filtered Handler Stack

```php
<?php
// Different handlers for different levels

$logger = new Logger('app');

// All logs to file
$fileHandler = new RotatingFileHandler('logs/all.log');
$logger->pushHandler($fileHandler);

// Errors only to Slack
$slackHandler = new SlackHandler($webhookUrl);
$slackHandler->setLevel(Logger::ERROR);
$logger->pushHandler($slackHandler);

// Critical only to email
$emailHandler = new NativeMailerHandler('ops@example.com');
$emailHandler->setLevel(Logger::CRITICAL);
$logger->pushHandler($emailHandler);

// Usage
$logger->info('Info only to file');
$logger->error('Error to file and Slack');
$logger->critical('Critical to file, Slack, and email');
```

---

## Custom Handlers

### Basic Custom Handler

```php
<?php
// Create custom handler

use Monolog\Handler\AbstractProcessingHandler;

class DatabaseHandler extends AbstractProcessingHandler {
    private $pdo;
    private $table;
    
    public function __construct($pdo, $table, $level = Logger::DEBUG) {
        parent::__construct($level);
        $this->pdo = $pdo;
        $this->table = $table;
    }
    
    protected function write(LogRecord $record): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO {$this->table} (level, message, context, created_at)
             VALUES (?, ?, ?, NOW())"
        );
        
        $stmt->execute([
            $record->level->value,
            $record->message,
            json_encode($record->context),
        ]);
    }
}

// Usage
$handler = new DatabaseHandler($pdo, 'logs');
$logger->pushHandler($handler);
$logger->info('Logged to database');
```

### Handler with State

```php
<?php
// Handler that maintains state

class BufferingHandler extends AbstractHandler {
    private $buffer = [];
    private $maxSize = 100;
    
    protected function write(LogRecord $record): void {
        $this->buffer[] = $record;
        
        // Flush if buffer full
        if (count($this->buffer) >= $this->maxSize) {
            $this->flush();
        }
    }
    
    public function flush(): void {
        if (empty($this->buffer)) {
            return;
        }
        
        // Process buffered records
        foreach ($this->buffer as $record) {
            $this->sendToRemoteService($record);
        }
        
        $this->buffer = [];
    }
    
    private function sendToRemoteService($record) {
        // Send to remote logging service
        // Batch operations for efficiency
    }
}

// Usage
$handler = new BufferingHandler();
$handler->maxSize = 50;  // Flush every 50 logs
$logger->pushHandler($handler);
```

### Async Handler

```php
<?php
// Non-blocking handler using queue

use Redis;

class AsyncQueueHandler extends AbstractHandler {
    private $redis;
    private $queue;
    
    public function __construct(Redis $redis, $queue = 'logs') {
        parent::__construct();
        $this->redis = $redis;
        $this->queue = $queue;
    }
    
    protected function write(LogRecord $record): void {
        // Queue instead of processing immediately
        $this->redis->lpush(
            $this->queue,
            json_encode($record)
        );
    }
}

// In separate worker process
class LogWorker {
    public function process() {
        while (true) {
            $log = $this->redis->brpop(['logs'], 0);
            
            if ($log) {
                $record = json_decode($log[1], true);
                $this->handleLog($record);
            }
        }
    }
}
```

---

## Handler Bubbling

### Bubble Configuration

```php
<?php
// Control whether log continues to next handler

$logger = new Logger('app');

// Handler 1: File (bubble = true)
$fileHandler = new StreamHandler('logs/app.log');
$fileHandler->setBubble(true);
$logger->pushHandler($fileHandler);

// Handler 2: Slack (bubble = false)
$slackHandler = new SlackHandler($webhookUrl);
$slackHandler->setLevel(Logger::ERROR);
$slackHandler->setBubble(false);  // Stop here if matched
$logger->pushHandler($slackHandler);

// Handler 3: Email (bubble = true)
$emailHandler = new NativeMailerHandler('ops@example.com');
$emailHandler->setLevel(Logger::CRITICAL);
$emailHandler->setBubble(true);
$logger->pushHandler($emailHandler);

// Info: File only (bubble = true, next handler level too high)
// Error: File (bubble = true) + Slack (bubble = false, stops)
// Critical: File (bubble = true) + Email (bubble = true)
```

---

## Conditional Handlers

### Level-Based Routing

```php
<?php
// Route logs by level

class SmartLogger {
    private $logger;
    
    public function __construct(Logger $logger) {
        $this->logger = $logger;
        $this->setupHandlers();
    }
    
    private function setupHandlers() {
        // Debug/Info to local file
        $debugHandler = new RotatingFileHandler('logs/debug.log');
        $debugHandler->setLevel(Logger::DEBUG);
        $debugHandler->setLevel(Logger::INFO, Logger::DEBUG);
        $this->logger->pushHandler($debugHandler);
        
        // Warnings to warnings file and Slack
        $warningHandler = new RotatingFileHandler('logs/warnings.log');
        $warningHandler->setLevel(Logger::WARNING);
        $this->logger->pushHandler($warningHandler);
        
        $slackHandler = new SlackHandler($webhookUrl);
        $slackHandler->setLevel(Logger::WARNING);
        $this->logger->pushHandler($slackHandler);
        
        // Errors to errors file only
        $errorHandler = new RotatingFileHandler('logs/errors.log');
        $errorHandler->setLevel(Logger::ERROR);
        $errorHandler->setLevel(Logger::CRITICAL, Logger::ERROR);
        $this->logger->pushHandler($errorHandler);
        
        // Critical to email
        $emailHandler = new NativeMailerHandler('ops@example.com');
        $emailHandler->setLevel(Logger::CRITICAL);
        $this->logger->pushHandler($emailHandler);
    }
}
```

---

## Testing Handlers

### Test Handler

```php
<?php
// Capture logs in tests

use Monolog\Handler\TestHandler;

class LoggerTest {
    public function testUserCreation() {
        $handler = new TestHandler();
        $logger = new Logger('test');
        $logger->pushHandler($handler);
        
        // Code under test
        $service = new UserService($logger);
        $user = $service->create(['email' => 'test@example.com']);
        
        // Assertions
        $this->assertTrue($handler->hasInfoRecords());
        $this->assertTrue($handler->hasRecord(Logger::INFO));
        
        $records = $handler->getRecords();
        $this->assertCount(1, $records);
        $this->assertEquals('User created', $records[0]->message);
    }
}
```

### Mock Handler

```php
<?php
// Use mock handler in tests

class MockLoggingHandler extends AbstractHandler {
    private $logs = [];
    
    protected function write(LogRecord $record): void {
        $this->logs[] = $record;
    }
    
    public function getLogs() {
        return $this->logs;
    }
    
    public function getMessages() {
        return array_map(fn($r) => $r->message, $this->logs);
    }
    
    public function clear() {
        $this->logs = [];
    }
}

// Usage in tests
$mockHandler = new MockLoggingHandler();
$logger->pushHandler($mockHandler);

$service->doSomething();

$logs = $mockHandler->getLogs();
$this->assertCount(3, $logs);
$this->assertContains('Started', $mockHandler->getMessages());
```

---

## Complete Examples

### Example 1: Production Logger with Reset

```php
<?php
// Production logger with reconfiguration capability

class ProductionLogger {
    private $logger;
    private $handlers = [];
    
    public function __construct() {
        $this->logger = new Logger('production');
        $this->setupHandlers();
    }
    
    private function setupHandlers() {
        // File logging
        $fileHandler = new RotatingFileHandler('logs/app.log', 30);
        $this->handlers['file'] = $fileHandler;
        $this->logger->pushHandler($fileHandler);
        
        // Error file
        $errorHandler = new RotatingFileHandler('logs/errors.log', 90);
        $errorHandler->setLevel(Logger::ERROR);
        $this->handlers['errors'] = $errorHandler;
        $this->logger->pushHandler($errorHandler);
        
        // Slack alerts
        $slackHandler = new SlackHandler(
            getenv('SLACK_WEBHOOK'),
            'alerts'
        );
        $slackHandler->setLevel(Logger::WARNING);
        $this->handlers['slack'] = $slackHandler;
        $this->logger->pushHandler($slackHandler);
    }
    
    public function emergencyMode() {
        // Reduce load during emergency
        $this->logger->reset();
        
        // File logging only
        $handler = new StreamHandler('logs/emergency.log');
        $handler->setLevel(Logger::ERROR);
        $this->logger->pushHandler($handler);
        
        $this->logger->alert('Emergency mode activated');
    }
    
    public function normalMode() {
        // Restore normal logging
        $this->logger->reset();
        $this->setupHandlers();
        $this->logger->info('Normal mode restored');
    }
    
    public function getLogger() {
        return $this->logger;
    }
}
```

### Example 2: Multi-Tenant Logger

```php
<?php
// Separate logs per tenant

class MultiTenantLogger {
    private $loggers = [];
    
    public function getLoggerForTenant($tenantId) {
        if (!isset($this->loggers[$tenantId])) {
            $logger = new Logger("tenant_{$tenantId}");
            
            // Tenant-specific file
            $handler = new RotatingFileHandler(
                "logs/tenant_{$tenantId}.log",
                30
            );
            $logger->pushHandler($handler);
            
            // Tenant errors to separate Slack channel
            $slackHandler = new SlackHandler(
                "https://hooks.slack.com/services/TENANT_{$tenantId}",
                'tenant-errors'
            );
            $slackHandler->setLevel(Logger::ERROR);
            $logger->pushHandler($slackHandler);
            
            $this->loggers[$tenantId] = $logger;
        }
        
        return $this->loggers[$tenantId];
    }
}

// Usage
$multiLogger = new MultiTenantLogger();

$tenantALogger = $multiLogger->getLoggerForTenant('tenant_a');
$tenantBLogger = $multiLogger->getLoggerForTenant('tenant_b');

$tenantALogger->info('Tenant A event');
$tenantBLogger->info('Tenant B event');
// Each logged to separate file
```

---

## Key Takeaways

**Handler Management Checklist:**

1. ✅ Stack multiple handlers
2. ✅ Configure handler levels
3. ✅ Control bubbling
4. ✅ Use custom handlers
5. ✅ Reset handlers dynamically
6. ✅ Test with mock handlers
7. ✅ Handle errors gracefully
8. ✅ Monitor handler performance

---

## See Also

- [Handlers Basics](5-handler.md)
- [Logging Libraries](2-logging-library.md)
- [Formatters](11-formatter.md)
