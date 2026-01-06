# PHP Standard Recommendations (PSR) - Complete Guide

## Table of Contents
1. [What are PSR Standards?](#what-are-psr-standards)
2. [PSR History & Governance](#psr-history--governance)
3. [Core PSRs Explained](#core-psrs-explained)
4. [Accepted PSRs](#accepted-psrs)
5. [Deprecated PSRs](#deprecated-psrs)
6. [Implementing PSRs](#implementing-psrs)
7. [Real-World Impact](#real-world-impact)
8. [Learning Path](#learning-path)

---

## What are PSR Standards?

**PHP Standard Recommendations (PSRs)** are specifications created by the **PHP Framework Interop Group (now PHP-FIG)** to ensure consistency and interoperability between PHP projects and frameworks.

### Why Standards Matter

**Without Standards:**
```php
// Different frameworks do things differently
// Creating - impossible to switch frameworks
// Hard to collaborate on open source
// Vendor lock-in
// Code duplication
```

**With PSRs:**
```php
// Everyone follows same patterns
// Easy to switch between projects
// Open source contributors can contribute anywhere
// No vendor lock-in
// Share code between projects
```

### Benefits

| Benefit | Explanation |
|---------|-------------|
| **Interoperability** | Libraries work together seamlessly |
| **Consistency** | Same coding style everywhere |
| **Collaboration** | Easy to contribute to any PHP project |
| **Maintainability** | Code easier to understand and maintain |
| **Adoption** | Widely adopted by modern frameworks |

---

## PSR History & Governance

### The PHP-FIG

**Founded:** 2009  
**Members:** Laravel, Symfony, Zend, WordPress, Drupal, Magento, etc.  
**Mission:** Create interoperable standards for PHP

### PSR Lifecycle

```
Draft → Proposed → Accepted → Superseded (optional)

Draft     → Early discussion
Proposed  → Ready for implementation  
Accepted  → Official standard
Superseded → Replaced by newer PSR
```

### Timeline of Major PSRs

| Year | PSR | Name | Status |
|------|-----|------|--------|
| 2009 | 0-1 | Basic coding standards | Superseded |
| 2012 | 2-3 | Coding style, autoloading | Accepted |
| 2013 | 4 | Autoloading improvements | Accepted |
| 2014 | 6-7 | Caching, HTTP message | Accepted |
| 2015 | 11-13 | Container, HTTP handlers | Accepted |
| 2017 | 15-17 | HTTP middleware, server | Accepted |
| 2020+ | 19+ | Modern async, attributes | In Progress |

---

## Core PSRs Explained

### PSR-0: Autoloading Standard (⚠️ Deprecated)

**Superseded by:** PSR-4

Basic autoloading using class namespaces and file paths.

```php
// PSR-0 Example (Don't use)
// Namespace: Vendor\Package\ClassName
// File: Vendor/Package/ClassName.php
```

---

### PSR-1: Basic Coding Standard

**Status:** Accepted  
**Applies to:** All PHP code  
**Key Rules:**

```php
<?php
// 1. Use <?php opening tag, NOT <? or <?=
declare(strict_types=1);  // Recommended

// 2. Classes, methods MUST use StudlyCaps
class MyClass {
    public function myMethod() {}
}

// 3. Constants MUST use uppercase with underscores
const MAX_USERS = 100;
const MIN_PASSWORD_LENGTH = 8;

// 4. Files MUST use only UTF-8 without BOM
// 5. Files MUST NOT have trailing whitespace
// 6. Lines SHOULD NOT exceed 120 characters
```

**Practical Checklist:**
- ✅ Use `<?php` tag
- ✅ Use `declare(strict_types=1);` at top of file
- ✅ Class names: `PascalCase`
- ✅ Method names: `camelCase`
- ✅ Constant names: `UPPER_CASE`
- ✅ Property names: `camelCase`

---

### PSR-2: Coding Style Guide (⚠️ Superseded by PSR-12)

**Original Status:** Accepted  
**Superseded by:** PSR-12  
**Modern Replacement:** PSR-12

PSR-2 specified indentation, line breaks, and spacing. See PSR-12 for current standards.

---

### PSR-3: Logger Interface

**Status:** Accepted  
**Purpose:** Standardize logging across applications  
**Framework:** Monolog implements this

```php
<?php
use Psr\Log\LoggerInterface;

interface LoggerInterface {
    public function emergency(string $message, array $context = []): void;
    public function alert(string $message, array $context = []): void;
    public function critical(string $message, array $context = []): void;
    public function error(string $message, array $context = []): void;
    public function warning(string $message, array $context = []): void;
    public function notice(string $message, array $context = []): void;
    public function info(string $message, array $context = []): void;
    public function debug(string $message, array $context = []): void;
    public function log(mixed $level, string $message, array $context = []): void;
}
```

**Usage:**

```php
<?php
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handlers\StreamHandler;

// Any PSR-3 compatible logger
function processOrder(LoggerInterface $logger, $order) {
    $logger->info('Processing order', ['order_id' => $order->id]);
    
    try {
        // Process...
        $logger->info('Order processed successfully');
    } catch (Exception $e) {
        $logger->error('Order processing failed', [
            'error' => $e->getMessage(),
            'order_id' => $order->id,
        ]);
    }
}

// Works with ANY PSR-3 logger
$monolog = new Logger('app');
$monolog->pushHandler(new StreamHandler('app.log'));
processOrder($monolog, $order);
```

**Benefits:**
- Use any logging library
- Easy to switch loggers
- Code not tied to specific logging framework

---

### PSR-4: Autoloading Standard

**Status:** Accepted  
**Current Standard:** Yes (replaces PSR-0)  
**Used by:** Composer (mandatory)

Maps PHP namespaces to file paths automatically.

```php
<?php
// File: src/Http/Request.php
namespace App\Http;

class Request {
    // ...
}

// Composer automatically loads it:
// src/ is mapped to App\
// So App\Http\Request is in src/Http/Request.php
```

**composer.json Configuration:**

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/",
            "Tests\\": "tests/"
        }
    }
}
```

**Mapping Rules:**

```
Namespace: App\Http\Request
Mapping: App\ => src/
Result: src/Http/Request.php

Namespace: App\Models\User
Mapping: App\ => src/
Result: src/Models/User.php
```

---

### PSR-6: Caching Interface

**Status:** Accepted  
**Purpose:** Standardize caching across applications

```php
<?php
use Psr\Cache\CacheItemPoolInterface;

interface CacheItemPoolInterface {
    public function getItem(string $key): CacheItemInterface;
    public function getItems(array $keys = []): iterable;
    public function hasItem(string $key): bool;
    public function clear(): bool;
    public function deleteItem(string $key): bool;
    public function deleteItems(array $keys): bool;
    public function save(CacheItemInterface $item): bool;
    public function saveDeferred(CacheItemInterface $item): bool;
    public function commit(): bool;
}
```

**Real Usage:**

```php
<?php
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

function getUserData(CacheItemPoolInterface $cache, int $userId): array {
    $cacheKey = "user_{$userId}";
    
    // Try to get from cache
    $item = $cache->getItem($cacheKey);
    if ($item->isHit()) {
        return $item->get();
    }
    
    // Not in cache, fetch from database
    $userData = fetchUserFromDatabase($userId);
    
    // Cache it for 1 hour
    $item->set($userData);
    $item->expiresAfter(3600);
    $cache->save($item);
    
    return $userData;
}

// Works with ANY PSR-6 cache (Redis, Memcached, File, etc.)
$cache = new RedisAdapter(/* ... */);
$user = getUserData($cache, 123);
```

---

### PSR-7: HTTP Message Interface

**Status:** Accepted  
**Purpose:** Standard for HTTP requests and responses

```php
<?php
use Psr\Http\Message\{
    RequestInterface,
    ResponseInterface,
    ServerRequestInterface,
    StreamInterface,
    UploadedFileInterface,
};

// Represents HTTP message (request or response)
interface MessageInterface {
    public function getProtocolVersion(): string;
    public function getHeaders(): array;
    public function getHeader(string $name): array;
    public function getBody(): StreamInterface;
    public function withBody(StreamInterface $body): static;
}

// Server request (from client)
interface ServerRequestInterface extends MessageInterface {
    public function getMethod(): string;
    public function getUri(): UriInterface;
    public function getQueryParams(): array;
    public function getParsedBody(): null|array|object;
    public function getUploadedFiles(): array;
}

// Response (to client)
interface ResponseInterface extends MessageInterface {
    public function getStatusCode(): int;
    public function getReasonPhrase(): string;
}
```

**Usage in Middleware:**

```php
<?php
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class AuthenticationMiddleware {
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        $token = $request->getHeader('Authorization')[0] ?? null;
        
        if (!$this->isValidToken($token)) {
            return $response
                ->withStatus(401)
                ->withBody(/* error message */);
        }
        
        // Valid token, proceed
        return $next($request);
    }
}
```

---

### PSR-11: Container Interface

**Status:** Accepted  
**Purpose:** Standardize dependency injection containers

```php
<?php
use Psr\Container\ContainerInterface;

interface ContainerInterface {
    public function get(string $id): mixed;
    public function has(string $id): bool;
}
```

**Implementation Example:**

```php
<?php
use Psr\Container\ContainerInterface;

class DIContainer implements ContainerInterface {
    private array $definitions = [];
    private array $instances = [];
    
    public function set(string $id, callable|object $definition): void {
        $this->definitions[$id] = $definition;
    }
    
    public function get(string $id): mixed {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }
        
        if (!isset($this->definitions[$id])) {
            throw new Exception("Service '$id' not found");
        }
        
        $definition = $this->definitions[$id];
        
        if (is_callable($definition)) {
            $instance = $definition($this);
        } else {
            $instance = $definition;
        }
        
        $this->instances[$id] = $instance;
        return $instance;
    }
    
    public function has(string $id): bool {
        return isset($this->definitions[$id]);
    }
}
```

**Usage:**

```php
<?php
$container = new DIContainer();

// Register services
$container->set('database', function(ContainerInterface $c) {
    return new Database('localhost', 'db', 'user', 'pass');
});

$container->set('user.repository', function(ContainerInterface $c) {
    return new UserRepository($c->get('database'));
});

// Get service
$userRepository = $container->get('user.repository');
```

---

### PSR-12: Extended Coding Style Guide

**Status:** Accepted  
**Replaces:** PSR-2  
**Current Standard:** Yes

Comprehensive coding style specification:

```php
<?php
declare(strict_types=1);

namespace App\Http;

use Psr\Log\LoggerInterface;

/**
 * HTTP Request Handler
 */
class RequestHandler {
    private string $method;
    private array $headers;
    
    /**
     * Handle incoming request
     * 
     * @param string $method HTTP method
     * @param array $headers HTTP headers
     * @param LoggerInterface $logger Logger instance
     * @return array Response data
     */
    public function handle(
        string $method,
        array $headers,
        LoggerInterface $logger,
    ): array {
        // Method body
        if ($method === 'GET') {
            return $this->handleGet($headers);
        } elseif ($method === 'POST') {
            return $this->handlePost($headers);
        } else {
            $logger->warning('Unsupported method', ['method' => $method]);
            return ['error' => 'Method not allowed'];
        }
    }
    
    private function handleGet(array $headers): array {
        // Implementation
        return [];
    }
    
    private function handlePost(array $headers): array {
        // Implementation
        return [];
    }
}
```

**Key PSR-12 Rules:**

```php
<?php
// 1. 4 spaces indentation (NOT tabs)
// 2. Lines MUST NOT exceed 120 characters
// 3. Class constants: UPPER_CASE
// 4. Properties: camelCase
// 5. Methods: camelCase
// 6. Functions: camelCase
// 7. Use type hints (PHP 7+)
// 8. Declare return types
// 9. One blank line between methods
// 10. Opening braces on same line
```

---

### PSR-15: HTTP Server Request Handler Interface

**Status:** Accepted  
**Purpose:** Middleware and request handler patterns

```php
<?php
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

// Handles request, returns response
interface RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface;
}

// Processes request, delegates to handler
interface MiddlewareInterface {
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface;
}
```

**Middleware Pipeline Example:**

```php
<?php
class AuthMiddleware implements MiddlewareInterface {
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Check authentication
        if (!$this->isAuthenticated($request)) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        // Proceed to next middleware/handler
        return $handler->handle($request);
    }
}

class LoggingMiddleware implements MiddlewareInterface {
    public function __construct(private LoggerInterface $logger) {}
    
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $this->logger->info('Incoming request', [
            'method' => $request->getMethod(),
            'path' => $request->getUri()->getPath(),
        ]);
        
        $response = $handler->handle($request);
        
        $this->logger->info('Response sent', [
            'status' => $response->getStatusCode(),
        ]);
        
        return $response;
    }
}
```

---

### PSR-18: HTTP Client Interface

**Status:** Accepted  
**Purpose:** Standard HTTP client implementation

```php
<?php
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ClientInterface {
    public function sendRequest(RequestInterface $request): ResponseInterface;
}
```

**Usage:**

```php
<?php
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class ApiClient {
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
    ) {}
    
    public function fetchUser(int $userId): array {
        $request = $this->requestFactory
            ->createRequest('GET', "https://api.example.com/users/$userId");
        
        $response = $this->httpClient->sendRequest($request);
        
        return json_decode((string)$response->getBody(), true);
    }
}
```

---

## Accepted PSRs Summary Table

| PSR | Name | Status | Purpose |
|-----|------|--------|---------|
| **0** | Autoloading | ⚠️ Superseded | Basic autoloading (use PSR-4) |
| **1** | Basic Coding | ✅ Accepted | Code formatting basics |
| **2** | Coding Style | ⚠️ Superseded | Use PSR-12 instead |
| **3** | Logger | ✅ Accepted | Logging interface |
| **4** | Autoloading | ✅ Accepted | Modern autoloading (Composer) |
| **5** | PHPDoc | 📋 Draft | PHPDoc standards |
| **6** | Cache | ✅ Accepted | Caching interface |
| **7** | HTTP Message | ✅ Accepted | HTTP request/response |
| **8** | Huggable Interface | ❌ Withdrawn | Proposed but rejected |
| **9** | Security Policy | ✅ Accepted | Vulnerability disclosure |
| **10** | Security Reporting | ✅ Accepted | Report handling |
| **11** | Container | ✅ Accepted | Dependency injection |
| **12** | Extended Coding | ✅ Accepted | Complete style guide |
| **13** | Hypermedia Links | ✅ Accepted | Link handling |
| **14** | Clock | ✅ Accepted | Time representation |
| **15** | HTTP Handlers | ✅ Accepted | Middleware pattern |
| **16** | Simple Cache | ✅ Accepted | Simple cache API |
| **17** | HTTP Factories | ✅ Accepted | Factory interfaces |
| **18** | HTTP Client | ✅ Accepted | Client interface |
| **19** | PHPDoc Tags | 📋 Draft | Tag standards |
| **20** | Clock Interface | ✅ Accepted | Testable clock |

---

## Deprecated PSRs

### PSR-0: Autoloading (Use PSR-4)

**Status:** ⚠️ **DEPRECATED** - Do not use  
**Reason:** Replaced by PSR-4  
**What changed:** Namespace mapping more flexible

### PSR-2: Coding Style (Use PSR-12)

**Status:** ⚠️ **DEPRECATED** - Do not use  
**Reason:** Superseded by PSR-12  
**What changed:** More comprehensive, clearer rules

---

## Implementing PSRs

### Step 1: Enable PSR-4 Autoloading

```json
{
    "name": "company/project",
    "autoload": {
        "psr-4": {
            "App\\": "src/",
            "Tests\\": "tests/"
        }
    }
}
```

### Step 2: Use PSR-3 Logger

```php
<?php
use Psr\Log\LoggerInterface;

class UserService {
    public function __construct(
        private LoggerInterface $logger,
    ) {}
    
    public function create(string $email): User {
        $this->logger->info('Creating user', ['email' => $email]);
        // ...
    }
}
```

### Step 3: Use PSR-11 Container

```php
<?php
use Psr\Container\ContainerInterface;

$container->set('user.service', function(ContainerInterface $c) {
    return new UserService(
        logger: $c->get('logger'),
        repository: $c->get('user.repository'),
    );
});

$userService = $container->get('user.service');
```

### Step 4: Follow PSR-12 Coding Style

```php
<?php
declare(strict_types=1);

namespace App;

class Example {
    private string $value;
    
    public function __construct(string $value) {
        $this->value = $value;
    }
    
    public function getValue(): string {
        return $this->value;
    }
}
```

### Step 5: Use PSR-7 HTTP Messages

```php
<?php
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Controller {
    public function handle(ServerRequestInterface $request): ResponseInterface {
        $data = json_encode(['status' => 'ok']);
        
        return new JsonResponse($data, 200);
    }
}
```

---

## Real-World Impact

### Before PSRs (PHP 5)

```php
<?php
// Different frameworks did things completely differently
// WordPress routing
if ($_GET['page'] === 'users') {
    require_once 'pages/users.php';
}

// Laravel routing
Route::get('/users', 'UserController@index');

// Symfony routing
$collection->add('users', new Route('/users'));

// Impossible to share code!
// Had to rewrite everything for different framework
```

### After PSRs (PHP 8+)

```php
<?php
declare(strict_types=1);

// Same patterns everywhere
// PSR-4 Autoloading - works in all frameworks
namespace App\Controllers;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

class UserController {
    public function __construct(
        private LoggerInterface $logger,
        private ContainerInterface $container,
    ) {}
    
    public function index(ServerRequestInterface $request): ResponseInterface {
        // Same code works in Laravel, Symfony, Slim, etc.
    }
}

// Can use same library in any framework!
$userService = $container->get('user.service');
```

### Framework Adoption

✅ **All modern frameworks support PSRs:**
- Laravel
- Symfony
- Slim
- Zend Framework
- WordPress (modern)
- Drupal (modern)
- CakePHP
- CodeIgniter

---

## Learning Path

Master PHP Standards systematically:

1. **PSR-1** - Basic coding standards
2. **PSR-4** - Autoloading (with Composer)
3. **PSR-12** - Extended coding style
4. **PSR-3** - Logging interface
5. **PSR-7** - HTTP messages
6. **PSR-11** - Dependency injection
7. **PSR-15** - HTTP middleware
8. **PSR-6** - Caching interface
9. **PSR-18** - HTTP client
10. **PSR-14** - Event dispatcher
11. **PSR-20** - Clock interface
12. **PSR-13** - Hypermedia links

---

## Best Practices

### ✅ DO

```php
<?php
declare(strict_types=1);

// 1. Use PSR-4 autoloading
namespace App\Services;

// 2. Type hint parameters
public function process(
    LoggerInterface $logger,
    ContainerInterface $container,
): ResponseInterface {}

// 3. Follow PSR-12 coding style
class MyClass {
    private string $value;
}

// 4. Use PSR interfaces
function log(LoggerInterface $logger): void {
    $logger->info('Message');
}
```

### ❌ DON'T

```php
<?php
// 1. Don't hard-code dependencies
$logger = new MonologLogger();  // Wrong!

// 2. Don't ignore type hints
public function process($data) {}  // Missing types

// 3. Don't use deprecated standards
// PSR-0 or PSR-2

// 4. Don't create custom interfaces
// Use PSR interfaces instead
```

---

## Quick Reference

### Essential PSRs for Daily Use

```php
<?php
// 1. PSR-4: Autoloading (mandatory with Composer)
composer dump-autoload

// 2. PSR-3: Logging (use LoggerInterface)
$logger->info('message', ['context' => 'data']);

// 3. PSR-12: Coding style (use linter)
vendor/bin/phpcs --standard=PSR12 src/

// 4. PSR-7: HTTP messages (use interfaces)
ServerRequestInterface $request,
ResponseInterface $response,

// 5. PSR-11: Container (use ContainerInterface)
$service = $container->get('service.id');
```

### Checking Compliance

```bash
# Install tools
composer require --dev phpstan squizlabs/php_codesniffer

# Check PSR-12 compliance
./vendor/bin/phpcs --standard=PSR12 src/

# Check types and errors
./vendor/bin/phpstan analyze src/
```

---

## Resources

- **Official PHP-FIG**: https://www.php-fig.org/
- **All PSRs**: https://www.php-fig.org/psr/
- **PSR Roadmap**: https://github.com/php-fig/fig-standards
- **Composer**: https://getcomposer.org/
- **Monolog (PSR-3)**: https://github.com/Seldaek/monolog
