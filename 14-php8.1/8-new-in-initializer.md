# New in Initializer

## Overview

Learn about the "new in initializer" feature in PHP 8.1, which allows creating objects directly in property initialization and constructor defaults.

---

## Table of Contents

1. What is New in Initializer
2. Basic Syntax
3. Constructor Defaults
4. Dependency Injection
5. vs Alternative Patterns
6. Performance Considerations
7. Real-world Patterns
8. Complete Examples

---

## What is New in Initializer

### Purpose

```php
<?php
// Before PHP 8.1: Manual object initialization

class Service {
    private Logger $logger;
    
    public function __construct(Logger $logger = null) {
        $this->logger = $logger ?? new Logger();
    }
}

// Problem:
// - Verbose null checking
// - Default logic scattered in constructor
// - Multiple ways to initialize

// Solution: New in Initializer (8.1+)
class Service {
    public function __construct(
        private Logger $logger = new Logger(),
    ) {}
}

// Benefits:
// ✓ Cleaner syntax
// ✓ Default visible at property level
// ✓ Type-safe defaults
// ✓ Works with readonly
```

### When to Use

```
Use 'new in initializer' when:
✓ Creating default service instances
✓ Creating configuration objects
✓ Dependency with sensible defaults
✓ Using constructor promotion

Don't use when:
✗ Default depends on other properties
✗ Default needs complex logic
✗ Default should be lazy-loaded
```

---

## Basic Syntax

### Simple Default Objects

```php
<?php
// Create default objects in parameter defaults

class Logger {
    public function log(string $msg): void {
        echo "LOG: $msg\n";
    }
}

class Service {
    public function __construct(
        private Logger $logger = new Logger(),
    ) {}
    
    public function process(): void {
        $this->logger->log('Processing...');
    }
}

// Creates default logger if none provided
$service1 = new Service();  // Uses new Logger()
$service1->process();  // Outputs: LOG: Processing...

// Can override with custom logger
$customLogger = new class {
    public function log(string $msg): void {
        echo "CUSTOM: $msg\n";
    }
};

$service2 = new Service($customLogger);
$service2->process();  // Outputs: CUSTOM: Processing...
```

### Multiple Defaults

```php
<?php
class DatabaseConfig {
    public function __construct(
        public string $host = 'localhost',
        public int $port = 5432,
    ) {}
}

class CacheConfig {
    public function __construct(
        public int $ttl = 3600,
    ) {}
}

class Application {
    public function __construct(
        private DatabaseConfig $db = new DatabaseConfig(),
        private CacheConfig $cache = new CacheConfig(),
    ) {}
}

// Uses all defaults
$app = new Application();

// Override both
$app = new Application(
    new DatabaseConfig(host: 'remote.db.com'),
    new CacheConfig(ttl: 7200)
);

// Override just database
$app = new Application(
    new DatabaseConfig(host: 'remote.db.com')
);
```

---

## Constructor Defaults

### Property Promotion with Defaults

```php
<?php
// Combine constructor promotion with default objects

class Logger {
    public function __construct(
        public string $level = 'info'
    ) {}
}

class Request {
    public function __construct(
        public string $method = 'GET',
    ) {}
}

class Handler {
    public function __construct(
        private Logger $logger = new Logger(),
        private Request $request = new Request(),
    ) {}
}

$handler = new Handler();
// Both $logger and $request are created with their defaults

$handler = new Handler(
    new Logger(level: 'debug')
);
// Custom logger, default request
```

### Nullable with Default

```php
<?php
class Logger {
    public function log(string $msg): void {}
}

class Service {
    public function __construct(
        // Non-nullable with default
        private Logger $required = new Logger(),
        // Nullable - still can provide new object
        private ?Logger $optional = null,
    ) {}
}

$service = new Service();
// $required = new Logger()
// $optional = null

$service = new Service(
    new Logger(),  // Override required
    new Logger()   // Override optional
);
```

---

## Dependency Injection

### Service Container Integration

```php
<?php
interface Container {
    public function get(string $id);
}

class ServiceA {
    // Has own default
}

class ServiceB {
    // Depends on ServiceA
}

class Application {
    public function __construct(
        // These will be dependency-injected,
        // but can work with defaults
        private ServiceA $serviceA = new ServiceA(),
        private ServiceB $serviceB = new ServiceB(),
    ) {}
}

// Manual instantiation works with defaults
$app = new Application();

// Or with DI container
$container = new DIContainer();
$app = new Application(
    $container->get('service_a'),
    $container->get('service_b')
);
```

### Factory Integration

```php
<?php
class Factory {
    public static function createLogger(): Logger {
        return new Logger(
            level: getenv('LOG_LEVEL') ?: 'info'
        );
    }
}

class Service {
    // Cannot use factory result as default
    // public function __construct(
    //     private Logger $logger = Factory::createLogger(),
    // ) {}
    // This doesn't work - only 'new' expressions
    
    // Must use conditional logic
    public function __construct(
        private Logger $logger = new Logger()
    ) {}
}

// For factory integration, keep the old pattern
class ServiceWithFactory {
    private Logger $logger;
    
    public function __construct(Logger $logger = null) {
        $this->logger = $logger ?? Factory::createLogger();
    }
}
```

---

## vs Alternative Patterns

### vs Null Coalescing

```php
<?php
// Old pattern
class ServiceOld {
    private Logger $logger;
    
    public function __construct(Logger $logger = null) {
        $this->logger = $logger ?? new Logger();
    }
}

// New pattern (8.1+)
class ServiceNew {
    public function __construct(
        private Logger $logger = new Logger(),
    ) {}
}

// Both work, new syntax is cleaner
$old = new ServiceOld();  // Uses ?? new Logger()
$new = new ServiceNew();  // Uses = new Logger()
```

### vs Lazy Loading

```php
<?php
// Lazy loading pattern
class ServiceLazy {
    private ?Logger $logger = null;
    
    public function getLogger(): Logger {
        return $this->logger ??= new Logger();
    }
}

// Eager initialization pattern (8.1+)
class ServiceEager {
    public function __construct(
        private Logger $logger = new Logger(),
    ) {}
}

// Lazy: Logger created only when first accessed
// Eager: Logger created at construction time

// Choose based on needs
```

### vs Setter Injection

```php
<?php
// Setter injection
class ServiceSetter {
    private Logger $logger;
    
    public function setLogger(Logger $logger): void {
        $this->logger = $logger;
    }
}

// Constructor with default
class ServiceConstructor {
    public function __construct(
        private Logger $logger = new Logger(),
    ) {}
}

// Constructor is cleaner and guarantees initialization
```

---

## Performance Considerations

### Instantiation Timing

```php
<?php
// Default object created at construction time
class Service {
    public function __construct(
        private Logger $logger = new Logger(),
    ) {}
}

$service = new Service();  // Logger instantiated here

// vs lazy loading
class LazyService {
    private Logger $logger;
    
    public function getLogger(): Logger {
        return $this->logger ??= new Logger();
    }
}

$service = new LazyService();  // Logger NOT created yet
$logger = $service->getLogger();  // Logger instantiated here

// Eager: Always pays construction cost
// Lazy: Pays cost only if used
```

### Memory Impact

```php
<?php
// Creating many instances with default objects
class Request {
    public function __construct(
        public DateTime $now = new DateTime(),  // Created each time!
    ) {}
}

// This creates a new DateTime for EACH Request instance
$requests = array_map(fn() => new Request(), range(1, 1000));
// Creates 1000 DateTime objects

// Better approach for expensive objects
class RequestOptimized {
    private ?DateTime $now = null;
    
    public function __construct(DateTime $now = null) {
        $this->now = $now ?? new DateTime();
    }
}

// Or use shared factory
class RequestFactory {
    private static ?DateTime $now = null;
    
    public static function create(): Request {
        return new Request(self::$now ??= new DateTime());
    }
}
```

---

## Real-world Patterns

### Configuration Objects

```php
<?php
class DatabaseConfig {
    public function __construct(
        public string $host = 'localhost',
        public int $port = 5432,
        public string $database = 'app',
        public string $charset = 'utf8mb4',
    ) {}
}

class AppConfig {
    public function __construct(
        public DatabaseConfig $db = new DatabaseConfig(),
        public string $appName = 'MyApp',
        public bool $debug = false,
    ) {}
}

// Simple usage with all defaults
$config1 = new AppConfig();

// Override some settings
$config2 = new AppConfig(
    new DatabaseConfig(
        host: getenv('DB_HOST'),
        database: getenv('DB_NAME')
    ),
    debug: getenv('APP_DEBUG') === 'true'
);
```

### Service Layer

```php
<?php
class UserRepository {
    public function __construct(
        private PDO $pdo,
    ) {}
}

class UserService {
    public function __construct(
        private UserRepository $repository = new UserRepository(
            pdo: new PDO('sqlite::memory:')
        ),
    ) {}
}

// Works with default in-memory database
$service = new UserService();

// Override with production database
$service = new UserService(
    new UserRepository(
        new PDO('mysql:host=localhost;dbname=app')
    )
);
```

---

## Complete Examples

### Example 1: Logger with Configuration

```php
<?php
class LogConfig {
    public function __construct(
        public string $level = 'info',
        public string $format = 'text',
        public string $path = '/var/log/app.log',
    ) {}
}

class Logger {
    public function __construct(
        private LogConfig $config = new LogConfig(),
    ) {}
    
    public function log(string $message): void {
        $timestamp = date('Y-m-d H:i:s');
        $formatted = "[$timestamp] {$this->config->level}: $message";
        
        if ($this->config->format === 'json') {
            $formatted = json_encode([
                'timestamp' => $timestamp,
                'level' => $this->config->level,
                'message' => $message,
            ]);
        }
        
        file_put_contents($this->config->path, $formatted . "\n", FILE_APPEND);
    }
}

class Application {
    public function __construct(
        private Logger $logger = new Logger(),
    ) {}
    
    public function run(): void {
        $this->logger->log('Application started');
    }
}

// Default usage
$app = new Application();
$app->run();

// With custom config
$app = new Application(
    new Logger(
        new LogConfig(
            level: 'debug',
            format: 'json',
            path: './logs/app.json'
        )
    )
);
$app->run();
```

### Example 2: Email Service

```php
<?php
class EmailConfig {
    public function __construct(
        public string $driver = 'smtp',
        public string $host = 'smtp.mailtrap.io',
        public int $port = 465,
        public bool $encryption = true,
    ) {}
}

class MailQueue {
    public function __construct(
        public string $handler = 'database',
    ) {}
}

class EmailService {
    public function __construct(
        private EmailConfig $config = new EmailConfig(),
        private MailQueue $queue = new MailQueue(),
    ) {}
    
    public function send(string $to, string $subject, string $body): bool {
        // Store in queue
        $this->storeInQueue($to, $subject, $body);
        
        // Send if immediate
        if ($this->queue->handler === 'sync') {
            return $this->sendNow($to, $subject, $body);
        }
        
        return true;
    }
    
    private function storeInQueue(string $to, string $subject, string $body): void {
        // Store for later processing
    }
    
    private function sendNow(string $to, string $subject, string $body): bool {
        // Send email using SMTP config
        return true;
    }
}

// Development: Use defaults
$email = new EmailService();

// Production: Override with real settings
$email = new EmailService(
    new EmailConfig(
        driver: 'smtp',
        host: getenv('MAIL_HOST'),
        port: (int)getenv('MAIL_PORT'),
        encryption: true
    ),
    new MailQueue(handler: 'async')
);
```

### Example 3: Cache System

```php
<?php
class CacheDriver {
    public function get(string $key): mixed {
        return null;
    }
    
    public function put(string $key, mixed $value, int $ttl): void {}
}

class ArrayDriver extends CacheDriver {
    private array $store = [];
    
    public function get(string $key): mixed {
        return $this->store[$key] ?? null;
    }
    
    public function put(string $key, mixed $value, int $ttl): void {
        $this->store[$key] = $value;
    }
}

class CacheManager {
    public function __construct(
        private CacheDriver $driver = new ArrayDriver(),
        private int $defaultTtl = 3600,
    ) {}
    
    public function remember(string $key, callable $callback): mixed {
        if ($cached = $this->driver->get($key)) {
            return $cached;
        }
        
        $value = $callback();
        $this->driver->put($key, $value, $this->defaultTtl);
        
        return $value;
    }
}

// Development: Uses in-memory array cache
$cache = new CacheManager();

$users = $cache->remember('users', function() {
    return ['John', 'Jane', 'Bob'];
});

// Production: Would use Redis or Memcached
// $cache = new CacheManager(
//     new RedisDriver(),
//     defaultTtl: 7200
// );
```

---

## Key Takeaways

**New in Initializer Checklist:**

1. ✅ Use for sensible defaults
2. ✅ Combine with constructor promotion
3. ✅ Works with readonly properties
4. ✅ Cleaner than null coalescing
5. ✅ Only for 'new' expressions
6. ✅ Object created at construction time
7. ✅ Can override in constructor
8. ✅ Document default behavior

---

## See Also

- [Constructor Property Promotion (PHP 8.0)](../04-php8/constructor-property-promotion.md)
- [Readonly Properties](3-readonly-properties.md)
- [Enumerations](2-enumerations.md)
- [Dependency Injection Patterns](../11-php-mvc/dependency-injection.md)
