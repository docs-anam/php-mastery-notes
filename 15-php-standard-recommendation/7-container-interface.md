# PSR-11: Container Interface

## Overview

Learn about PSR-11, the standardized dependency injection container interface that enables loose coupling and testability in PHP applications.

---

## Table of Contents

1. What is PSR-11
2. Core Concepts
3. Service Container
4. Dependency Injection
5. Service Providers
6. Implementation
7. Common Patterns
8. Real-world Examples
9. Complete Examples

---

## What is PSR-11

### Purpose

```php
<?php
// Before PSR-11: Manual dependency management

class UserService
{
    private PDO $pdo;
    private Logger $logger;
    private CachePool $cache;

    public function __construct()
    {
        // Hardcoded dependencies - difficult to test!
        $this->pdo = new PDO('mysql:host=localhost', 'user', 'pass');
        $this->logger = new FileLogger('logs/app.log');
        $this->cache = new FilesystemCache('cache/');
    }

    public function getUser(int $id): User
    {
        // Can't easily mock dependencies for testing
        // Hard to swap implementations
        // Tightly coupled to implementations
    }
}

// Solution: PSR-11 Container

use Psr\Container\ContainerInterface;

class UserService
{
    public function __construct(
        private PDO $pdo,
        private Logger $logger,
        private CachePool $cache,
    ) {}

    public function getUser(int $id): User
    {
        // Dependencies injected!
        // Easy to mock for testing
        // Loosely coupled to interfaces
    }
}

// Usage with container
$container = new Container();

$userService = $container->get(UserService::class);

// Benefits:
// ✓ Loose coupling
// ✓ Testability
// ✓ Flexibility
// ✓ Reusability
```

### Key Interfaces

```php
<?php
// ContainerInterface
// - get(string): object
// - has(string): bool

// Both methods use service identifiers:
// - Class names (fully qualified)
// - Interface names
// - Custom string IDs
```

---

## Core Concepts

### Container

```php
<?php
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

interface ContainerInterface
{
    /**
     * Finds an entry of the container by its identifier and returns it
     *
     * @throws NotFoundExceptionInterface  No entry was found for this identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     */
    public function get(string $id): mixed;

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     */
    public function has(string $id): bool;
}
```

### Service Definition

```php
<?php
// Services are defined by:

// 1. Class name (automatic)
$container->get(UserService::class);

// 2. Interface (bound)
$container->bind(LoggerInterface::class, FileLogger::class);
$container->get(LoggerInterface::class);

// 3. Service ID (custom)
$container->set('database.primary', $pdo);
$container->get('database.primary');

// 4. Factory (callable)
$container->set('config', function($container) {
    return new Config('config.php');
});
$container->get('config');
```

---

## Service Container

### Definition Styles

```php
<?php
// Style 1: Direct instantiation
$container = new Container();
$container->set('logger', new FileLogger('app.log'));

// Style 2: Lazy initialization (factory)
$container->set('database', function(ContainerInterface $c) {
    return new PDO(
        'mysql:host=localhost;dbname=app',
        'user',
        'password'
    );
});

// Style 3: Singleton (shared)
$container->singleton('config', function() {
    return new Config('config.php');
});

// Style 4: Binding interfaces
$container->bind(LoggerInterface::class, FileLogger::class);

// Style 5: Auto-wiring (automatic)
$container->auto(UserService::class, function(ContainerInterface $c) {
    return new UserService(
        $c->get(PDO::class),
        $c->get(LoggerInterface::class),
    );
});
```

### Retrieving Services

```php
<?php
use Psr\Container\ContainerInterface;

// Get service
$logger = $container->get(LoggerInterface::class);

// Check if exists
if ($container->has('config')) {
    $config = $container->get('config');
}

// Safe get with fallback
$timeout = $container->has('timeout') ? $container->get('timeout') : 30;
```

---

## Dependency Injection

### Constructor Injection

```php
<?php
class UserRepository
{
    public function __construct(
        private PDO $pdo,
        private CacheInterface $cache,
        private LoggerInterface $logger,
    ) {}

    public function findById(int $id): ?User
    {
        $cacheKey = "user_{$id}";

        // Check cache
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        // Query database
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = User::hydrate($row);
            $this->cache->set($cacheKey, $user, 3600);
            return $user;
        }

        return null;
    }
}

// Container setup
$container->bind(PDO::class, function() {
    return new PDO(
        'mysql:host=localhost;dbname=app',
        'user',
        'password'
    );
});

$container->singleton(CacheInterface::class, FilesystemCache::class);
$container->singleton(LoggerInterface::class, FileLogger::class);

$repository = $container->get(UserRepository::class);
```

### Property Injection

```php
<?php
// Less preferred, but supported

class EventListener
{
    public Logger $logger;

    public function handle(Event $event): void
    {
        $this->logger->info('Event: ' . $event->getName());
    }
}

// Container configuration
$container->set('listener', function($c) {
    $listener = new EventListener();
    $listener->logger = $c->get(LoggerInterface::class);
    return $listener;
});
```

### Method Injection

```php
<?php
// Inject into methods

class RequestHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Request injected via method parameter
        $data = $request->getParsedBody();
        return new Response(200, [], json_encode($data));
    }
}

// Use with middleware/resolver
$handler = $container->get(RequestHandler::class);
$response = $handler->handle($request);
```

---

## Service Providers

### Provider Pattern

```php
<?php
use Psr\Container\ContainerInterface;

interface ServiceProvider
{
    public function register(ContainerInterface $container): void;
}

class DatabaseServiceProvider implements ServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->singleton(PDO::class, function(ContainerInterface $c) {
            $config = $c->get('config');
            return new PDO(
                $config['database']['dsn'],
                $config['database']['user'],
                $config['database']['password']
            );
        });
    }
}

class LoggingServiceProvider implements ServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->singleton(LoggerInterface::class, function(ContainerInterface $c) {
            $config = $c->get('config');
            return new FileLogger($config['logging']['path']);
        });
    }
}

class CachingServiceProvider implements ServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->singleton(CacheInterface::class, function(ContainerInterface $c) {
            $config = $c->get('config');

            return match ($config['cache']['driver']) {
                'redis' => new RedisCache($c->get('redis')),
                'file' => new FilesystemCache('cache/'),
                default => new ArrayCache(),
            };
        });
    }
}
```

### Bootstrap

```php
<?php
// bootstrap.php

require_once 'vendor/autoload.php';

use App\Providers\DatabaseServiceProvider;
use App\Providers\LoggingServiceProvider;
use App\Providers\CachingServiceProvider;

$container = new Container();

// Register configuration
$container->singleton('config', function() {
    return require 'config.php';
});

// Register service providers
$providers = [
    new DatabaseServiceProvider(),
    new LoggingServiceProvider(),
    new CachingServiceProvider(),
];

foreach ($providers as $provider) {
    $provider->register($container);
}

return $container;
```

---

## Implementation

### Simple Container

```php
<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

class SimpleContainer implements ContainerInterface
{
    private array $services = [];
    private array $resolved = [];

    public function set(string $id, mixed $value): void
    {
        $this->services[$id] = $value;
        unset($this->resolved[$id]);  // Invalidate cache
    }

    public function singleton(string $id, mixed $value): void
    {
        $this->set($id, $value);
    }

    public function bind(string $abstract, string $concrete): void
    {
        $this->set($abstract, $concrete);
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Service '{$id}' not found in container");
        }

        // Check if already resolved
        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        $service = $this->services[$id];

        // If it's a factory (callable)
        if (is_callable($service)) {
            $instance = $service($this);
            $this->resolved[$id] = $instance;
            return $instance;
        }

        // If it's a class name, instantiate
        if (is_string($service) && class_exists($service)) {
            $instance = new $service();
            $this->resolved[$id] = $instance;
            return $instance;
        }

        // Return as-is
        $this->resolved[$id] = $service;
        return $service;
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}

class NotFoundException extends Exception implements NotFoundExceptionInterface {}
class ContainerException extends Exception implements ContainerExceptionInterface {}
```

---

## Common Patterns

### Service Locator Pattern

```php
<?php
class ServiceLocator
{
    public function __construct(private ContainerInterface $container) {}

    public function getDatabase(): PDO
    {
        return $this->container->get(PDO::class);
    }

    public function getLogger(): LoggerInterface
    {
        return $this->container->get(LoggerInterface::class);
    }

    public function getCache(): CacheInterface
    {
        return $this->container->get(CacheInterface::class);
    }
}

// Usage
$locator = new ServiceLocator($container);
$db = $locator->getDatabase();
$log = $locator->getLogger();
```

### Factory with Container

```php
<?php
class RepositoryFactory
{
    public function __construct(private ContainerInterface $container) {}

    public function create(string $entity): RepositoryInterface
    {
        $class = "App\\Repositories\\{$entity}Repository";

        return new $class(
            $this->container->get(PDO::class),
            $this->container->get(CacheInterface::class),
        );
    }
}

// Usage
$factory = $container->get(RepositoryFactory::class);
$userRepository = $factory->create('User');
$articleRepository = $factory->create('Article');
```

---

## Real-world Examples

### Application Bootstrap

```php
<?php
// Application container setup

class Application
{
    public function __construct(private ContainerInterface $container) {}

    public static function bootstrap(): self
    {
        $container = new SimpleContainer();

        // Load configuration
        $container->set('config', function() {
            return require 'config/app.php';
        });

        // Register database
        $container->set(PDO::class, function($c) {
            $config = $c->get('config');
            return new PDO(
                $config['database']['dsn'],
                $config['database']['user'],
                $config['database']['password']
            );
        });

        // Register cache
        $container->singleton(CacheInterface::class, function($c) {
            return new FilesystemCache('cache/');
        });

        // Register logger
        $container->singleton(LoggerInterface::class, function($c) {
            return new FileLogger('logs/app.log');
        });

        // Register repositories
        $container->set(UserRepository::class, function($c) {
            return new UserRepository(
                $c->get(PDO::class),
                $c->get(CacheInterface::class),
            );
        });

        // Register services
        $container->set(UserService::class, function($c) {
            return new UserService(
                $c->get(UserRepository::class),
                $c->get(LoggerInterface::class),
            );
        });

        return new self($container);
    }

    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }
}

// Usage
$app = Application::bootstrap();
$userService = $app->get(UserService::class);
```

---

## Complete Examples

### Full Application Setup

```php
<?php
// config/app.php
return [
    'database' => [
        'driver' => 'mysql',
        'host' => getenv('DB_HOST', 'localhost'),
        'database' => getenv('DB_NAME', 'app'),
        'user' => getenv('DB_USER', 'root'),
        'password' => getenv('DB_PASS', ''),
    ],
    'cache' => [
        'driver' => 'file',
        'path' => 'cache/',
    ],
    'logging' => [
        'path' => 'logs/app.log',
    ],
];

// Application service provider
class AppServiceProvider implements ServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        // Load configuration first
        $container->set('config', require 'config/app.php');

        // Database connection
        $container->singleton(PDO::class, function($c) {
            $config = $c->get('config')['database'];
            return new PDO(
                sprintf(
                    '%s:host=%s;dbname=%s;charset=utf8',
                    $config['driver'],
                    $config['host'],
                    $config['database']
                ),
                $config['user'],
                $config['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        });

        // Cache
        $container->singleton(CacheInterface::class, function($c) {
            $config = $c->get('config')['cache'];
            return new FilesystemCache($config['path']);
        });

        // Logger
        $container->singleton(LoggerInterface::class, function($c) {
            $config = $c->get('config')['logging'];
            return new FileLogger($config['path']);
        });

        // Repositories
        $container->set(UserRepository::class, function($c) {
            return new UserRepository(
                $c->get(PDO::class),
                $c->get(CacheInterface::class),
                $c->get(LoggerInterface::class),
            );
        });

        // Services
        $container->set(UserService::class, function($c) {
            return new UserService($c->get(UserRepository::class));
        });
    }
}

// bootstrap.php
$container = new SimpleContainer();
(new AppServiceProvider())->register($container);

// Usage
$userService = $container->get(UserService::class);
```

---

## Key Takeaways

**PSR-11 Container Checklist:**

1. ✅ Define services in container
2. ✅ Use dependency injection
3. ✅ Leverage service providers
4. ✅ Use singletons for expensive services
5. ✅ Avoid service locator anti-pattern
6. ✅ Test container configuration
7. ✅ Document service dependencies
8. ✅ Use type hints for IDE support

---

## See Also

- [PSR Overview](0-psr-overview.md)
- [Autoloading Standard (PSR-4)](4-autoloading-standard.md)
- [Event Dispatcher (PSR-14)](10-event-dispatcher.md)
