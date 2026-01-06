# Processors and Log Enrichment

## Overview

Learn how to use processors to automatically enrich logs with additional information like request IDs, environment context, and performance metrics.

---

## Table of Contents

1. What are Processors
2. Built-in Processors
3. Creating Custom Processors
4. Processor Pipeline
5. Common Processors
6. Performance Impact
7. Complete Examples

---

## What are Processors

### Purpose

```
Processor = Automatically add data to logs

Before Processor:
{
  "message": "User logged in",
  "context": {"user_id": 123}
}

After Processor:
{
  "message": "User logged in",
  "context": {"user_id": 123},
  "extra": {
    "request_id": "req_abc123",
    "memory": "25.5MB",
    "timestamp": 1704541845
  }
}
```

### Processor Stack

```
Log Entry
  ↓
Processor 1 (add request ID)
  ↓
Processor 2 (add memory info)
  ↓
Processor 3 (add user context)
  ↓
Handler (formatted and output)
```

---

## Built-in Processors

### Introspection Processor

```php
<?php
// Add file/line information

use Monolog\Handlers\StreamHandler;
use Monolog\Processors\IntrospectionProcessor;

$logger = new Logger('app');
$handler = new StreamHandler('php://stdout');

// Add file and line where log was called
$processor = new IntrospectionProcessor(
    Logger::DEBUG,  // Minimum level
    ['vendor']      // Paths to skip
);

$logger->pushProcessor($processor);
$logger->pushHandler($handler);

// Output will include:
// "extra": {
//   "file": "src/UserService.php",
//   "line": 45,
//   "function": "register",
//   "class": "UserService"
// }
```

### Process ID Processor

```php
<?php
// Add process information

use Monolog\Processors\ProcessorIdProcessor;

$logger = new Logger('app');

// Add PID and memory usage
$processor = new ProcessorIdProcessor();
$logger->pushProcessor($processor);

// Output includes:
// "extra": {
//   "process_id": 12345,
//   "memory_usage": 26214400
// }
```

### UIDProcessor

```php
<?php
// Add unique identifier

use Monolog\Processors\UidProcessor;

$logger = new Logger('app');

// Add unique ID to all logs
$processor = new UidProcessor();
$logger->pushProcessor($processor);

// First log:
// "extra": {"uid": "abc123def456"}

// All logs in same request get same UID
```

### Git Processor

```php
<?php
// Add git branch/commit info

use Monolog\Processors\GitProcessor;

$logger = new Logger('app');

$processor = new GitProcessor(base_path('.git'));
$logger->pushProcessor($processor);

// Output includes:
// "extra": {
//   "git": {
//     "branch": "main",
//     "commit": "abc123def456"
//   }
// }
```

---

## Creating Custom Processors

### Basic Processor

```php
<?php
// Simple custom processor

class RequestIdProcessor {
    private $requestId;
    
    public function __construct() {
        $this->requestId = uniqid('req_', true);
    }
    
    public function __invoke(array $record) {
        // Add to extra field
        $record['extra']['request_id'] = $this->requestId;
        return $record;
    }
}

// Usage
$logger = new Logger('app');
$logger->pushProcessor(new RequestIdProcessor());
$logger->pushHandler(new StreamHandler('php://stdout'));

$logger->info('User logged in');
// Automatically includes request_id in extra
```

### Processor with State

```php
<?php
// Processor that maintains state

class MemoryPeakProcessor {
    private $memoryPeak = 0;
    
    public function __invoke(array $record) {
        $current = memory_get_peak_usage(true);
        $this->memoryPeak = max($this->memoryPeak, $current);
        
        $record['extra']['memory_peak_mb'] = round(
            $this->memoryPeak / 1024 / 1024,
            2
        );
        
        return $record;
    }
}

// Usage
$processor = new MemoryPeakProcessor();
$logger->pushProcessor($processor);

// Each log includes peak memory usage
```

### Conditional Processor

```php
<?php
// Processor that conditionally adds data

class EnvironmentProcessor {
    public function __invoke(array $record) {
        // Only add in development
        if (getenv('APP_ENV') === 'development') {
            $record['extra']['environment'] = 'development';
            $record['extra']['debug_backtrace'] = debug_backtrace();
        } else {
            $record['extra']['environment'] = 'production';
        }
        
        return $record;
    }
}
```

---

## Processor Pipeline

### Multiple Processors

```php
<?php
// Chain processors

$logger = new Logger('app');

// 1. Add request ID
$logger->pushProcessor(new RequestIdProcessor());

// 2. Add memory info
$logger->pushProcessor(new MemoryPeakProcessor());

// 3. Add environment
$logger->pushProcessor(new EnvironmentProcessor());

// 4. Add git info
$logger->pushProcessor(new GitProcessor());

$logger->pushHandler(new StreamHandler('php://stdout'));

// Log gets enriched by all processors
$logger->info('User action');
```

### Processor Ordering

```php
<?php
// Order matters - processors run in sequence

$logger = new Logger('app');

// Order 1: Add user first
$logger->pushProcessor(new UserContextProcessor());

// Order 2: Then add request data
$logger->pushProcessor(new RequestContextProcessor());

// Result:
// "extra": {
//   "user_id": 123,
//   "request_id": "abc",
//   "ip": "192.168.1.1"
// }
```

---

## Common Processors

### User Context Processor

```php
<?php
// Add current user information

class UserContextProcessor {
    public function __invoke(array $record) {
        if (isset($_SESSION['user_id'])) {
            $record['extra']['user_id'] = $_SESSION['user_id'];
            $record['extra']['username'] = $_SESSION['username'] ?? null;
        }
        
        return $record;
    }
}
```

### Request Metadata Processor

```php
<?php
// Add HTTP request information

class RequestMetadataProcessor {
    public function __invoke(array $record) {
        $record['extra']['request'] = [
            'method' => $_SERVER['REQUEST_METHOD'] ?? null,
            'uri' => $_SERVER['REQUEST_URI'] ?? null,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ];
        
        return $record;
    }
}
```

### Performance Processor

```php
<?php
// Add performance metrics

class PerformanceProcessor {
    private $startTime;
    
    public function __construct() {
        $this->startTime = microtime(true);
    }
    
    public function __invoke(array $record) {
        $duration = microtime(true) - $this->startTime;
        
        $record['extra']['performance'] = [
            'duration_seconds' => $duration,
            'memory_mb' => round(memory_get_usage() / 1024 / 1024, 2),
            'memory_peak_mb' => round(memory_get_peak_usage() / 1024 / 1024, 2),
        ];
        
        return $record;
    }
}
```

### Database Query Processor

```php
<?php
// Log database queries

class DatabaseQueryProcessor {
    private $queries = [];
    
    public function addQuery($sql, $duration) {
        $this->queries[] = [
            'sql' => $sql,
            'duration_ms' => round($duration * 1000, 2),
        ];
    }
    
    public function __invoke(array $record) {
        $record['extra']['database'] = [
            'queries_executed' => count($this->queries),
            'total_time_ms' => array_sum(
                array_column($this->queries, 'duration_ms')
            ),
            'queries' => $this->queries,
        ];
        
        return $record;
    }
}
```

---

## Performance Impact

### Processor Cost

```php
<?php
// Processors can impact performance

// Expensive processor
class ExpensiveProcessor {
    public function __invoke(array $record) {
        // This runs on EVERY log
        $record['extra']['analysis'] = $this->complexAnalysis();
        return $record;
    }
}

// Better approach
class ConditionalExpensiveProcessor {
    public function __invoke(array $record) {
        // Only run expensive analysis for errors
        if ($record['level'] >= Logger::ERROR) {
            $record['extra']['analysis'] = $this->complexAnalysis();
        }
        return $record;
    }
}
```

### Processor Caching

```php
<?php
// Cache processor results

class CachedContextProcessor {
    private $cache = [];
    private $cacheTTL = 60;
    private $cacheTime = 0;
    
    public function __invoke(array $record) {
        $now = time();
        
        // Refresh cache every 60 seconds
        if ($now - $this->cacheTime > $this->cacheTTL) {
            $this->cache = $this->buildContext();
            $this->cacheTime = $now;
        }
        
        $record['extra'] = array_merge($record['extra'], $this->cache);
        return $record;
    }
    
    private function buildContext() {
        // Expensive operation
        return [
            'hostname' => gethostname(),
            'php_version' => PHP_VERSION,
        ];
    }
}
```

---

## Complete Examples

### Example 1: Comprehensive Processor Setup

```php
<?php
// Complete processor pipeline

class LoggerFactory {
    public static function create($env = 'production') {
        $logger = new Logger('app');
        
        // Add processors (in order)
        // 1. User context
        $logger->pushProcessor(new UserContextProcessor());
        
        // 2. Request metadata (depends on user)
        $logger->pushProcessor(new RequestMetadataProcessor());
        
        // 3. Performance metrics
        $logger->pushProcessor(new PerformanceProcessor());
        
        // 4. Git information (static)
        if ($env === 'production') {
            $logger->pushProcessor(new GitProcessor());
        }
        
        // 5. Environment-specific
        $logger->pushProcessor(new EnvironmentProcessor());
        
        // Add handlers
        $handler = new RotatingFileHandler('logs/app.log');
        $handler->setFormatter(new JsonFormatter());
        $logger->pushHandler($handler);
        
        return $logger;
    }
}

// Usage
$logger = LoggerFactory::create('production');
$logger->info('User logged in');
// Enriched with: user_id, request metadata, performance, git info
```

### Example 2: Tracing Processor

```php
<?php
// Trace requests through application

class TracingProcessor {
    private static $traceId;
    private static $spanStack = [];
    
    public static function startTrace() {
        self::$traceId = uniqid('trace_', true);
        self::$spanStack = [];
    }
    
    public static function startSpan($name) {
        $span = [
            'name' => $name,
            'start' => microtime(true),
        ];
        array_push(self::$spanStack, $span);
    }
    
    public static function endSpan() {
        if (!empty(self::$spanStack)) {
            $span = array_pop(self::$spanStack);
            $span['duration'] = microtime(true) - $span['start'];
            return $span;
        }
    }
    
    public function __invoke(array $record) {
        $record['extra']['trace_id'] = self::$traceId;
        $record['extra']['span_depth'] = count(self::$spanStack);
        
        if (!empty(self::$spanStack)) {
            $current = end(self::$spanStack);
            $record['extra']['current_span'] = $current['name'];
        }
        
        return $record;
    }
}

// Usage
TracingProcessor::startTrace();

TracingProcessor::startSpan('api_request');
$logger->info('Processing request');

TracingProcessor::startSpan('database_query');
$logger->debug('Executing query');
TracingProcessor::endSpan();

$logger->info('Request completed');
TracingProcessor::endSpan();

// All logs have trace_id and span info
```

### Example 3: Business Metrics Processor

```php
<?php
// Track business metrics

class BusinessMetricsProcessor {
    private $metrics = [
        'users_created' => 0,
        'orders_processed' => 0,
        'total_revenue' => 0,
        'errors_count' => 0,
    ];
    
    public function recordMetric($name, $value = 1) {
        if (isset($this->metrics[$name])) {
            $this->metrics[$name] += $value;
        }
    }
    
    public function __invoke(array $record) {
        // Add current metrics to logs
        $record['extra']['metrics'] = $this->metrics;
        
        // Update metrics based on log level
        if ($record['level'] >= Logger::ERROR) {
            $this->metrics['errors_count']++;
        }
        
        return $record;
    }
}

// Usage
$metrics = new BusinessMetricsProcessor();
$logger->pushProcessor($metrics);

// Record metrics
$metrics->recordMetric('users_created', 1);
$logger->info('User created');

$metrics->recordMetric('orders_processed', 1);
$metrics->recordMetric('total_revenue', 99.99);
$logger->info('Order processed');

// Logs include: {
//   "metrics": {
//     "users_created": 1,
//     "orders_processed": 1,
//     "total_revenue": 99.99,
//     "errors_count": 0
//   }
// }
```

---

## Key Takeaways

**Processor Checklist:**

1. ✅ Add processors for automatic enrichment
2. ✅ Use built-in processors when available
3. ✅ Create custom processors for specific needs
4. ✅ Order processors carefully
5. ✅ Consider performance impact
6. ✅ Cache expensive computations
7. ✅ Include context for correlation
8. ✅ Test processors in development

---

## See Also

- [Logging Basics](0-logging-basics.md)
- [Handlers](5-handler.md)
- [Formatters](11-formatter.md)
