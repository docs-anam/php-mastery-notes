# Setting Up Logging in Your Project

## Overview

Initialize and configure logging in your PHP project with proper directory structure, configuration files, and best practices.

---

## Table of Contents

1. Project Structure
2. Directory Setup
3. Configuration Files
4. Bootstrap Process
5. Environment Variables
6. Testing Setup
7. Complete Examples

---

## Project Structure

### Recommended Directory Layout

```
project/
├── src/
│   ├── Application/
│   ├── Domain/
│   └── Infrastructure/
├── config/
│   ├── logging.php
│   ├── app.php
│   └── services.php
├── logs/
│   ├── app.log
│   ├── errors.log
│   ├── security.log
│   └── debug.log
├── bootstrap/
│   ├── container.php
│   └── logging.php
├── .env
├── .env.example
├── composer.json
└── index.php
```

### Logging Directory

```bash
# Create logs directory with proper permissions
mkdir -p logs
chmod 755 logs

# Create log files
touch logs/app.log
touch logs/errors.log
touch logs/security.log

# Set permissions
chmod 644 logs/*.log
```

---

## Directory Setup

### Create Logger Bootstrap

```php
<?php
// bootstrap/logging.php

use Monolog\Logger;
use Monolog\Handlers\{
    RotatingFileHandler,
    StreamHandler,
};
use Monolog\Formatters\JsonFormatter;

function bootstrapLogging() {
    $config = require __DIR__ . '/../config/logging.php';
    $env = getenv('APP_ENV') ?: 'development';
    
    $loggers = [];
    
    foreach ($config['channels'] as $name => $channel) {
        $logger = new Logger($name);
        
        // Add handlers based on configuration
        foreach ($channel['handlers'] as $handlerConfig) {
            $handler = createHandler($handlerConfig, $env);
            
            if (isset($handlerConfig['formatter'])) {
                $formatter = createFormatter($handlerConfig['formatter']);
                $handler->setFormatter($formatter);
            }
            
            $logger->pushHandler($handler);
        }
        
        $loggers[$name] = $logger;
    }
    
    return $loggers;
}

function createHandler($config, $env) {
    switch ($config['type']) {
        case 'rotating_file':
            return new RotatingFileHandler(
                $config['path'],
                $config['days'] ?? 30
            );
        
        case 'stream':
            $stream = $env === 'development' ? 'php://stdout' : 'php://stderr';
            return new StreamHandler($stream);
        
        default:
            throw new Exception("Unknown handler: {$config['type']}");
    }
}

function createFormatter($config) {
    switch ($config['type']) {
        case 'json':
            return new JsonFormatter();
        default:
            return new \Monolog\Formatters\LineFormatter();
    }
}
```

### Service Container Setup

```php
<?php
// bootstrap/container.php

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface {
    private $services = [];
    private $instances = [];
    
    public function register($id, $factory) {
        $this->services[$id] = $factory;
    }
    
    public function get($id) {
        if (!$this->has($id)) {
            throw new Exception("Service not found: $id");
        }
        
        if (!isset($this->instances[$id])) {
            $factory = $this->services[$id];
            $this->instances[$id] = is_callable($factory)
                ? $factory($this)
                : $factory;
        }
        
        return $this->instances[$id];
    }
    
    public function has($id) {
        return isset($this->services[$id]);
    }
}

// Create and configure container
$container = new Container();

$container->register('logger', function($c) {
    $loggers = require __DIR__ . '/logging.php';
    return $loggers['app'];
});

$container->register('logger.security', function($c) {
    $loggers = require __DIR__ . '/logging.php';
    return $loggers['security'];
});

return $container;
```

---

## Configuration Files

### Main Logging Configuration

```php
<?php
// config/logging.php

use Monolog\Logger;

return [
    'default' => 'app',
    
    'channels' => [
        'app' => [
            'handlers' => [
                [
                    'type' => 'rotating_file',
                    'path' => 'logs/app.log',
                    'days' => 30,
                    'formatter' => ['type' => 'json'],
                    'level' => Logger::INFO,
                ],
            ],
        ],
        
        'errors' => [
            'handlers' => [
                [
                    'type' => 'rotating_file',
                    'path' => 'logs/errors.log',
                    'days' => 30,
                    'formatter' => ['type' => 'json'],
                    'level' => Logger::ERROR,
                ],
            ],
        ],
        
        'security' => [
            'handlers' => [
                [
                    'type' => 'rotating_file',
                    'path' => 'logs/security.log',
                    'days' => 90,
                    'formatter' => ['type' => 'json'],
                    'level' => Logger::WARNING,
                ],
            ],
        ],
        
        'debug' => [
            'handlers' => [
                [
                    'type' => 'stream',
                    'level' => Logger::DEBUG,
                    'formatter' => ['type' => 'line'],
                ],
            ],
        ],
    ],
];
```

### Application Configuration

```php
<?php
// config/app.php

return [
    'name' => 'My Application',
    'env' => getenv('APP_ENV') ?: 'development',
    'debug' => getenv('APP_DEBUG') === 'true',
    
    'log' => [
        'channel' => getenv('LOG_CHANNEL') ?: 'app',
        'level' => getenv('LOG_LEVEL') ?: 'info',
        'path' => getenv('LOG_PATH') ?: 'logs',
    ],
];
```

### Service Registration

```php
<?php
// config/services.php

use Psr\Log\LoggerInterface;

return [
    LoggerInterface::class => function($container) {
        return $container->get('logger');
    },
    
    // Database service with logging
    \PDO::class => function($container) {
        $logger = $container->get('logger');
        $logger->info('Initializing database connection');
        
        $pdo = new PDO(
            getenv('DB_DSN'),
            getenv('DB_USER'),
            getenv('DB_PASS')
        );
        
        $logger->info('Database connection established');
        return $pdo;
    },
];
```

---

## Bootstrap Process

### Entry Point

```php
<?php
// public/index.php

define('BASE_PATH', dirname(__DIR__));

// Load environment variables
if (file_exists(BASE_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
}

// Create container
$container = require BASE_PATH . '/bootstrap/container.php';

// Get logger
$logger = $container->get('logger');

$logger->info('Application started', [
    'env' => getenv('APP_ENV'),
    'debug' => getenv('APP_DEBUG'),
    'uri' => $_SERVER['REQUEST_URI'],
]);

try {
    // Application logic
    $app = new Application($container);
    $response = $app->handle($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    
    http_response_code($response->status);
    echo $response->body;
    
    $logger->info('Request completed', ['status' => $response->status]);
} catch (Exception $e) {
    $logger->critical('Unhandled exception', [
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);
    
    http_response_code(500);
    echo 'Server Error';
}
```

---

## Environment Variables

### .env Configuration

```bash
# .env

APP_NAME="My Application"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=app
LOG_LEVEL=debug
LOG_PATH=logs

DB_HOST=localhost
DB_PORT=5432
DB_NAME=app_db
DB_USER=app
DB_PASS=secret
```

### .env.example

```bash
# .env.example (committed to version control)

APP_NAME="My Application"
APP_ENV=local
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=app
LOG_LEVEL=info
LOG_PATH=logs

DB_HOST=localhost
DB_PORT=5432
DB_NAME=app_db
DB_USER=app
DB_PASS=
```

### Loading Environment Variables

```php
<?php
// bootstrap/env.php

class Environment {
    private static $variables = [];
    
    public static function load($path) {
        if (!file_exists($path)) {
            throw new Exception(".env file not found: $path");
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos($line, '#') === 0) {
                continue;
            }
            
            if (strpos($line, '=') === false) {
                continue;
            }
            
            [$key, $value] = explode('=', $line, 2);
            
            $key = trim($key);
            $value = trim($value, ' "\'');
            
            self::$variables[$key] = $value;
            putenv("$key=$value");
        }
    }
    
    public static function get($key, $default = null) {
        return self::$variables[$key] ?? $default;
    }
}
```

---

## Testing Setup

### Test Logging Configuration

```php
<?php
// config/logging.test.php

use Monolog\Logger;
use Monolog\Handlers\StreamHandler;

return [
    'default' => 'testing',
    
    'channels' => [
        'testing' => [
            'handlers' => [
                [
                    'type' => 'stream',
                    'level' => Logger::DEBUG,
                    'path' => 'php://memory', // Log to memory in tests
                ],
            ],
        ],
    ],
];
```

### Logger for Tests

```php
<?php
// tests/TestCase.php

use PHPUnit\Framework\TestCase;
use Monolog\Logger;
use Monolog\Handlers\StreamHandler;

class LoggerTestCase extends TestCase {
    protected $logger;
    protected $logStream;
    
    protected function setUp(): void {
        parent::setUp();
        
        $this->logStream = fopen('php://memory', 'w+');
        $this->logger = new Logger('test');
        
        $handler = new StreamHandler($this->logStream);
        $this->logger->pushHandler($handler);
    }
    
    protected function getLogOutput() {
        rewind($this->logStream);
        return stream_get_contents($this->logStream);
    }
}
```

---

## Complete Examples

### Example 1: Complete Bootstrap

```php
<?php
// bootstrap/app.php

define('BASE_PATH', dirname(__DIR__));

// 1. Load environment
require_once BASE_PATH . '/bootstrap/env.php';
Environment::load(BASE_PATH . '/.env');

// 2. Create container
$container = new Container();

// 3. Register services
foreach (glob(BASE_PATH . '/config/services/*.php') as $file) {
    $services = require $file;
    foreach ($services as $id => $factory) {
        $container->register($id, $factory);
    }
}

// 4. Initialize logging
$logger = $container->get('logger');
$logger->info('Application bootstrap complete');

return $container;
```

### Example 2: Application Class with Logging

```php
<?php
// src/Application.php

use Psr\Log\LoggerInterface;

class Application {
    public function __construct(
        private ContainerInterface $container,
        private LoggerInterface $logger
    ) {
        $this->logger->debug('Initializing application');
    }
    
    public function run() {
        try {
            $this->logger->info('Request started', [
                'method' => $_SERVER['REQUEST_METHOD'],
                'path' => $_SERVER['REQUEST_URI'],
            ]);
            
            // Route and dispatch request
            $response = $this->dispatch();
            
            $this->logger->info('Request completed', [
                'status' => $response->statusCode,
            ]);
            
            return $response;
        } catch (HttpException $e) {
            $this->logger->warning('HTTP exception', [
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            throw $e;
        } catch (Exception $e) {
            $this->logger->critical('Unhandled exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            throw $e;
        }
    }
    
    private function dispatch() {
        // Application logic
    }
}
```

---

## Key Takeaways

**Setup Checklist:**

1. ✅ Create logs directory
2. ✅ Write configuration files
3. ✅ Set up bootstrap process
4. ✅ Configure environment variables
5. ✅ Initialize container
6. ✅ Register logger services
7. ✅ Test in development
8. ✅ Verify in production

---

## See Also

- [Logging Basics](0-logging-basics.md)
- [Logging Libraries](2-logging-library.md)
- [Handlers](5-handler.md)
