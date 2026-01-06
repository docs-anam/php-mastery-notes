# PSR-15: HTTP Handlers and Middleware

## Overview

Learn about PSR-15, the standardized middleware and request handler interface that enables pluggable HTTP processing pipelines for framework-agnostic applications.

---

## Table of Contents

1. What is PSR-15
2. Core Concepts
3. Request Handlers
4. Middleware
5. Pipeline Pattern
6. Implementation
7. Common Patterns
8. Real-world Examples
9. Complete Examples

---

## What is PSR-15

### Purpose

```php
<?php
// Before PSR-15: Framework-specific middleware

// Laravel middleware
class CheckAdmin
{
    public function handle($request, Closure $next)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect('/');
        }
        return $next($request);
    }
}

// Symfony middleware
class CheckAdminMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        if (!$this->security->getUser()?->hasRole('ADMIN')) {
            throw new AccessDeniedException();
        }
        return $handler->handle($request);
    }
}

// Problems:
// - Framework-specific
// - Can't reuse across frameworks
// - Incompatible interfaces
// - Vendor lock-in

// Solution: PSR-15 (standardized middleware)

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class CheckAdminMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        if (!$this->hasAdminPermission($request)) {
            return new Response(403);
        }
        return $handler->handle($request);
    }
}

// Benefits:
// ✓ Framework agnostic
// ✓ Reusable middleware
// ✓ Standard interface
// ✓ Composable pipelines
```

### Key Interfaces

```php
<?php
// RequestHandlerInterface
// - handle(ServerRequestInterface): ResponseInterface

// MiddlewareInterface
// - process(ServerRequestInterface, RequestHandlerInterface): ResponseInterface
```

---

## Core Concepts

### Request Processing Pipeline

```
Request
   ↓
[Middleware 1: Logging] ────→ Continue
   ↓
[Middleware 2: Authentication] ────→ Continue
   ↓
[Middleware 3: Authorization] ────→ Continue OR Return 403
   ↓
[Request Handler] ────→ Process
   ↓
Response ← Response
   ↑
   ← Logging
   ↑
   ← Modified by middleware
   ↑
```

### Middleware Responsibilities

```
1. Pre-processing (before handler)
   - Validate request
   - Check permissions
   - Parse headers
   - Modify request

2. Delegation
   - Call next handler/middleware
   - Receive response

3. Post-processing (after handler)
   - Add headers
   - Log response
   - Transform response
```

---

## Request Handlers

### RequestHandlerInterface

```php
<?php
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RequestHandlerInterface
{
    /**
     * Handle the request and return a response
     */
    public function handle(ServerRequestInterface $request): ResponseInterface;
}

// Implementation
class ApplicationHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        // Route the request
        return match ([$method, $path]) {
            ['GET', '/users'] => $this->listUsers($request),
            ['POST', '/users'] => $this->createUser($request),
            ['GET', '/users/{id}'] => $this->getUser($request),
            default => new Response(404),
        };
    }

    private function listUsers(ServerRequestInterface $request): ResponseInterface
    {
        $users = [['id' => 1, 'name' => 'John']];
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($users)
        );
    }

    private function createUser(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        // Create user...
        return new Response(201);
    }

    private function getUser(ServerRequestInterface $request): ResponseInterface
    {
        // Get user...
        return new Response(200);
    }
}
```

---

## Middleware

### MiddlewareInterface

```php
<?php
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface MiddlewareInterface
{
    /**
     * Process a request and return a response
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface;
}
```

### Common Middleware Examples

```php
<?php
// Logging middleware
class LoggingMiddleware implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $start = microtime(true);

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath()
        );

        $response = $handler->handle($request);

        $elapsed = microtime(true) - $start;
        $this->logger->info("Response: " . $response->getStatusCode() . " ({$elapsed}ms)");

        return $response;
    }
}

// Authentication middleware
class AuthenticationMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $token = $request->getHeaderLine('Authorization');

        if (!$token || !$this->validateToken($token)) {
            return new Response(401, [], 'Unauthorized');
        }

        $userId = $this->getUserFromToken($token);

        // Add user to request attributes
        $request = $request->withAttribute('user_id', $userId);

        return $handler->handle($request);
    }

    private function validateToken(string $token): bool
    {
        return true;  // Validate JWT, etc
    }

    private function getUserFromToken(string $token): int
    {
        return 123;
    }
}

// CORS middleware
class CorsMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Handle CORS preflight
        if ($request->getMethod() === 'OPTIONS') {
            return new Response(200)
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        $response = $handler->handle($request);

        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
    }
}

// Exception handling middleware
class ErrorHandlingMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            return new Response(
                500,
                ['Content-Type' => 'application/json'],
                json_encode(['error' => 'Internal Server Error'])
            );
        }
    }
}
```

---

## Pipeline Pattern

### Middleware Pipeline

```php
<?php
// Process request through middleware chain

class MiddlewarePipeline implements RequestHandlerInterface
{
    private int $position = 0;

    /**
     * @param MiddlewareInterface[] $middleware
     */
    public function __construct(
        private array $middleware,
        private RequestHandlerInterface $handler,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!isset($this->middleware[$this->position])) {
            // All middleware processed, call handler
            return $this->handler->handle($request);
        }

        $middleware = $this->middleware[$this->position];
        $this->position++;

        return $middleware->process($request, $this);
    }
}

// Usage
$middleware = [
    new LoggingMiddleware($logger),
    new AuthenticationMiddleware(),
    new CorsMiddleware(),
];

$pipeline = new MiddlewarePipeline($middleware, $handler);
$response = $pipeline->handle($request);
```

### Alternative: Dispatcher

```php
<?php
class MiddlewareDispatcher implements RequestHandlerInterface
{
    /**
     * @param MiddlewareInterface[] $middleware
     */
    public function __construct(
        private array $middleware,
        private RequestHandlerInterface $handler,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->dispatch($request, 0);
    }

    private function dispatch(
        ServerRequestInterface $request,
        int $index
    ): ResponseInterface {
        if ($index >= count($this->middleware)) {
            return $this->handler->handle($request);
        }

        $middleware = $this->middleware[$index];

        $next = new class($this, $index) implements RequestHandlerInterface {
            public function __construct(
                private MiddlewareDispatcher $dispatcher,
                private int $nextIndex,
            ) {}

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->dispatcher->dispatch($request, $this->nextIndex + 1);
            }
        };

        return $middleware->process($request, $next);
    }
}
```

---

## Implementation

### Full Middleware System

```php
<?php
declare(strict_types=1);

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;

// Define middleware
class LoggingMiddleware implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $this->logger->debug(
            $request->getMethod() . ' ' . $request->getUri()->getPath()
        );

        return $handler->handle($request);
    }
}

class AuthMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $token = $request->getHeaderLine('Authorization');

        if (!$token) {
            return new Response(401);
        }

        return $handler->handle($request);
    }
}

// Middleware pipeline
class Pipeline implements RequestHandlerInterface
{
    private int $position = 0;

    /**
     * @param MiddlewareInterface[] $middleware
     */
    public function __construct(
        private array $middleware,
        private RequestHandlerInterface $handler,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!isset($this->middleware[$this->position])) {
            return $this->handler->handle($request);
        }

        $middleware = $this->middleware[$this->position];
        $this->position++;

        return $middleware->process($request, $this);
    }
}

// Application handler
class AppHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(['message' => 'Hello'])
        );
    }
}

// Usage
$logger = new FileLogger('app.log');

$middleware = [
    new LoggingMiddleware($logger),
    new AuthMiddleware(),
];

$handler = new AppHandler();
$pipeline = new Pipeline($middleware, $handler);

// Handle request
$response = $pipeline->handle($request);
```

---

## Common Patterns

### Routing with Middleware

```php
<?php
class Router implements RequestHandlerInterface
{
    private array $routes = [];

    public function route(string $method, string $path, RequestHandlerInterface $handler): void
    {
        $key = "$method $path";
        $this->routes[$key] = $handler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        $key = "$method $path";

        if (!isset($this->routes[$key])) {
            return new Response(404);
        }

        return $this->routes[$key]->handle($request);
    }
}

// Usage
$router = new Router();

$router->route('GET', '/users', new ListUsersHandler());
$router->route('POST', '/users', new CreateUserHandler());
$router->route('GET', '/users/{id}', new GetUserHandler());

$middleware = [
    new LoggingMiddleware($logger),
    new AuthMiddleware(),
];

$pipeline = new Pipeline($middleware, $router);
```

### Conditional Middleware

```php
<?php
class ConditionalMiddleware implements MiddlewareInterface
{
    public function __construct(
        private MiddlewareInterface $middleware,
        private callable $condition,
    ) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        if (($this->condition)($request)) {
            return $this->middleware->process($request, $handler);
        }

        return $handler->handle($request);
    }
}

// Usage
$middleware = new ConditionalMiddleware(
    new AdminOnlyMiddleware(),
    fn($req) => str_starts_with($req->getUri()->getPath(), '/admin')
);
```

---

## Real-world Examples

### Web Application Handler

```php
<?php
class WebApplication implements RequestHandlerInterface
{
    private Pipeline $pipeline;
    private Router $router;

    public function __construct(
        private LoggerInterface $logger,
        private ContainerInterface $container,
    ) {
        $this->setupRouter();
        $this->setupMiddleware();
    }

    private function setupRouter(): void
    {
        $this->router = new Router();

        $this->router->route('GET', '/', new HomePageHandler());
        $this->router->route('GET', '/users', new ListUsersHandler());
        $this->router->route('GET', '/users/{id}', new GetUserHandler());
        $this->router->route('POST', '/users', new CreateUserHandler());
        $this->router->route('POST', '/login', new LoginHandler());
    }

    private function setupMiddleware(): void
    {
        $middleware = [
            new LoggingMiddleware($this->logger),
            new SessionMiddleware(),
            new CsrfProtectionMiddleware(),
        ];

        $this->pipeline = new Pipeline($middleware, $this->router);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->pipeline->handle($request);
    }
}

// Handler implementations
class ListUsersHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $users = [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane'],
        ];

        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($users)
        );
    }
}

class GetUserHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');

        $user = ['id' => $id, 'name' => 'John'];

        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($user)
        );
    }
}
```

---

## Complete Examples

### Full Application Bootstrap

```php
<?php
// bootstrap.php
require_once 'vendor/autoload.php';

use GuzzleHttp\Psr7\ServerRequest;

// Create application
$logger = new FileLogger('logs/app.log');
$container = new Container();

$app = new WebApplication($logger, $container);

// Get request from globals
$request = ServerRequest::fromGlobals();

// Handle request
$response = $app->handle($request);

// Send response
http_response_code($response->getStatusCode());

foreach ($response->getHeaders() as $name => $values) {
    header("$name: " . implode(', ', $values));
}

echo $response->getBody();
```

---

## Key Takeaways

**PSR-15 Middleware Checklist:**

1. ✅ Implement MiddlewareInterface
2. ✅ Delegate to next handler
3. ✅ Can pre-process request
4. ✅ Can post-process response
5. ✅ Add attributes via withAttribute()
6. ✅ Use pipeline to compose middleware
7. ✅ Keep middleware focused
8. ✅ Test middleware behavior

---

## See Also

- [PSR Overview](0-psr-overview.md)
- [HTTP Message Interface (PSR-7)](6-http-message-interface.md)
- [Container Interface (PSR-11)](7-container-interface.md)
