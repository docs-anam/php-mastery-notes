# Attributes Enhancements

## Overview

Learn about attribute enhancements in PHP 8.2, which provide improved reflection capabilities and more flexible metadata attachment to code elements.

---

## Table of Contents

1. What are Attributes
2. Basic Syntax Review
3. PHP 8.2 Enhancements
4. Using Attributes
5. Reflection and Discovery
6. Framework Integration
7. Best Practices
8. Complete Examples

---

## What are Attributes

### Fundamentals Review

```php
<?php
// Attributes introduced in PHP 8.0
// Enhanced in PHP 8.2 with better reflection support

// Basic syntax: #[AttributeName]
#[Route('/users', 'GET')]
#[Deprecated]
#[RateLimit(requests: 100, window: 3600)]
class UserController
{
    #[Validate(type: 'email')]
    #[Sanitize(filter: FILTER_SANITIZE_EMAIL)]
    public function __construct(
        #[Required]
        private string $email,
    ) {}
}

// Benefits:
// ✓ Metadata attachment
// ✓ Framework integration
// ✓ Reflection-based processing
// ✓ Cleaner code organization
```

---

## Defining Custom Attributes

### Basic Attribute Definition

```php
<?php
// Define custom attribute
#[Attribute]
class Route
{
    public function __construct(
        public string $path,
        public string $method = 'GET',
    ) {}
}

#[Attribute]
class RateLimit
{
    public function __construct(
        public int $requests,
        public int $window,
    ) {}
}

#[Attribute]
class Deprecated
{
    public function __construct(
        public string $reason = '',
        public string $replacement = '',
    ) {}
}

// Usage on classes
#[Route('/api/users', 'GET')]
#[RateLimit(requests: 100, window: 3600)]
class UserController {}

// Usage on methods
class ApiController
{
    #[Route('/users/{id}', 'GET')]
    #[RateLimit(requests: 50, window: 1800)]
    public function getUser(int $id): User {}

    #[Route('/users', 'POST')]
    #[RateLimit(requests: 10, window: 60)]
    public function createUser(array $data): User {}

    #[Deprecated('Use getUserById instead')]
    #[Route('/users/{id}/old', 'GET')]
    public function getOldUser(int $id): User {}
}
```

### Attribute Targets

```php
<?php
// Specify what can be attributed

#[Attribute(Attribute::TARGET_CLASS)]
class OnlyClass {}

#[Attribute(Attribute::TARGET_METHOD)]
class OnlyMethod {}

#[Attribute(Attribute::TARGET_PROPERTY)]
class OnlyProperty {}

#[Attribute(Attribute::TARGET_PARAMETER)]
class OnlyParameter {}

// Multiple targets
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class ClassOrMethod {}

// All targets
#[Attribute(Attribute::TARGET_ALL)]
class Universal {}

// Target constants:
// TARGET_CLASS, TARGET_METHOD, TARGET_FUNCTION,
// TARGET_PARAMETER, TARGET_PROPERTY, TARGET_CONSTANT,
// TARGET_ALL, TARGET_CLASS_CONSTANT, TARGET_ENUM,
// TARGET_ENUM_CASE, TARGET_FUNCTION, TARGET_INHERITED
```

### Repeatable Attributes

```php
<?php
// Allow multiple instances
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Permission
{
    public function __construct(public string $role) {}
}

// Usage with multiple instances
#[Permission('admin')]
#[Permission('moderator')]
#[Permission('user')]
class SecureController {}

// Reflection will return array of attributes
```

---

## PHP 8.2 Enhancements

### Improved Reflection

```php
<?php
// PHP 8.2 provides better attribute access

#[Attribute]
class Cacheable
{
    public function __construct(
        public int $ttl = 3600,
        public bool $shared = false,
    ) {}
}

class ProductService
{
    #[Cacheable(ttl: 7200)]
    public function getProduct(int $id): Product
    {
        // Retrieve product
    }
}

// Access attributes via Reflection
$reflection = new ReflectionClass(ProductService::class);
$method = $reflection->getMethod('getProduct');

// Get all attributes
$attributes = $method->getAttributes();

// Get specific attribute
$cacheAttrs = $method->getAttributes(Cacheable::class);

if (count($cacheAttrs) > 0) {
    $cacheAttr = $cacheAttrs[0]->newInstance();
    echo "TTL: " . $cacheAttr->ttl;  // 7200
}
```

### Attribute Discovery

```php
<?php
// Discover and process attributes

#[Attribute(Attribute::TARGET_METHOD)]
class Handler {}

#[Attribute(Attribute::TARGET_METHOD)]
class Validation
{
    public function __construct(public array $rules = []) {}
}

class RequestHandler
{
    #[Handler]
    #[Validation(['email' => 'required', 'password' => 'required|min:8'])]
    public function login(string $email, string $password): bool
    {
        return true;
    }
}

// Discover handlers
class Router
{
    public function discoverHandlers(object $controller): array
    {
        $reflection = new ReflectionClass($controller);
        $handlers = [];

        foreach ($reflection->getMethods() as $method) {
            if ($method->getAttributes(Handler::class)) {
                $handlers[$method->getName()] = $method;
            }
        }

        return $handlers;
    }

    public function getValidationRules(object $controller, string $method): array
    {
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod($method);

        $attrs = $method->getAttributes(Validation::class);

        if (empty($attrs)) {
            return [];
        }

        return $attrs[0]->newInstance()->rules;
    }
}

// Usage
$handler = new RequestHandler();
$router = new Router();

$handlers = $router->discoverHandlers($handler);
$rules = $router->getValidationRules($handler, 'login');
```

---

## Practical Applications

### Validation Attributes

```php
<?php
#[Attribute(Attribute::TARGET_PROPERTY)]
class Required {}

#[Attribute(Attribute::TARGET_PROPERTY)]
class Email {}

#[Attribute(Attribute::TARGET_PROPERTY)]
class MinLength
{
    public function __construct(public int $length) {}
}

#[Attribute(Attribute::TARGET_PROPERTY)]
class MaxLength
{
    public function __construct(public int $length) {}
}

#[Attribute(Attribute::TARGET_PROPERTY)]
class Pattern
{
    public function __construct(public string $regex) {}
}

class UserDTO
{
    #[Required]
    #[Email]
    private string $email;

    #[Required]
    #[MinLength(3)]
    #[MaxLength(50)]
    private string $name;

    #[Pattern('/^[0-9]{10,}$/')]
    private string $phone;

    public function __construct(array $data)
    {
        $this->email = $data['email'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->phone = $data['phone'] ?? '';
    }

    public static function validate(array $data): array
    {
        $errors = [];
        $reflection = new ReflectionClass(self::class);

        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes();

            foreach ($attributes as $attr) {
                $attrInstance = $attr->newInstance();

                if ($attr->getName() === Required::class) {
                    if (empty($data[$property->getName()])) {
                        $errors[$property->getName()] = 'Required field';
                    }
                }
            }
        }

        return $errors;
    }
}

// Usage
$data = ['email' => '', 'name' => 'J', 'phone' => '123'];
$errors = UserDTO::validate($data);
// ['email' => 'Required field', 'name' => 'Must be at least 3 characters', ...]
```

### Route Mapping

```php
<?php
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        public string $path,
        public string $method = 'GET',
        public array $middleware = [],
    ) {}
}

#[Attribute(Attribute::TARGET_METHOD)]
class Auth
{
    public function __construct(public array $roles = []) {}
}

class ApiController
{
    #[Route('/users', 'GET')]
    public function listUsers(): array
    {
        return [];
    }

    #[Route('/users/{id}', 'GET')]
    public function getUser(int $id): User
    {
        return new User();
    }

    #[Route('/users', 'POST')]
    #[Auth(['admin', 'moderator'])]
    public function createUser(array $data): User
    {
        return new User();
    }

    #[Route('/users/{id}', 'PUT')]
    #[Auth(['admin'])]
    public function updateUser(int $id, array $data): User
    {
        return new User();
    }
}

class RouteRegistry
{
    public function registerRoutes(object $controller): array
    {
        $routes = [];
        $reflection = new ReflectionClass($controller);

        foreach ($reflection->getMethods() as $method) {
            $attrs = $method->getAttributes(Route::class);

            foreach ($attrs as $attr) {
                $route = $attr->newInstance();
                $authAttrs = $method->getAttributes(Auth::class);
                $requiredRoles = [];

                if (!empty($authAttrs)) {
                    $requiredRoles = $authAttrs[0]->newInstance()->roles;
                }

                $routes[] = [
                    'method' => $method->getName(),
                    'path' => $route->path,
                    'httpMethod' => $route->method,
                    'roles' => $requiredRoles,
                    'callable' => [$controller, $method->getName()],
                ];
            }
        }

        return $routes;
    }
}

// Usage
$controller = new ApiController();
$registry = new RouteRegistry();
$routes = $registry->registerRoutes($controller);

foreach ($routes as $route) {
    echo "{$route['httpMethod']} {$route['path']} -> {$route['method']}\n";
}
```

### Cache Configuration

```php
<?php
#[Attribute(Attribute::TARGET_METHOD)]
class Cacheable
{
    public function __construct(
        public string $key = '',
        public int $ttl = 3600,
        public bool $invalidateOn = false,
    ) {}
}

#[Attribute(Attribute::TARGET_METHOD)]
class CacheInvalidate
{
    public function __construct(public string $pattern = '') {}
}

class ProductRepository
{
    #[Cacheable(key: 'product_{id}', ttl: 7200)]
    public function findById(int $id): ?Product
    {
        // Query database
        return new Product();
    }

    #[Cacheable(key: 'products_all', ttl: 3600)]
    public function findAll(): array
    {
        // Query all products
        return [];
    }

    #[CacheInvalidate(pattern: 'product_*')]
    #[CacheInvalidate(pattern: 'products_*')]
    public function update(int $id, array $data): Product
    {
        // Update product
        return new Product();
    }
}

class CacheManager
{
    private array $cache = [];

    public function executeWithCache(callable $callback, $object, $method): mixed
    {
        $reflection = new ReflectionMethod($object::class, $method);
        $cacheAttrs = $reflection->getAttributes(Cacheable::class);

        if (empty($cacheAttrs)) {
            return call_user_func($callback);
        }

        $cacheAttr = $cacheAttrs[0]->newInstance();
        $cacheKey = $cacheAttr->key;

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $result = call_user_func($callback);
        $this->cache[$cacheKey] = $result;

        return $result;
    }
}
```

---

## Best Practices

**Attribute Usage Guidelines:**

```php
<?php
// ✓ GOOD: Clear, focused attributes
#[Attribute]
class JsonField
{
    public function __construct(public string $serializedName = '') {}
}

// ✓ GOOD: Repeatable when needed
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Tag
{
    public function __construct(public string $name) {}
}

// ✓ GOOD: Type-specific targets
#[Attribute(Attribute::TARGET_METHOD)]
class CacheableMethod {}

// ❌ AVOID: Overly complex attributes
#[Attribute]
class BadAttribute
{
    public function __construct(
        public string $option1,
        public string $option2,
        public string $option3,
        // Many more...
    ) {}
}

// ❌ AVOID: Attributes with side effects
#[Attribute]
class BadSideEffect
{
    public function __construct()
    {
        // Don't do this - attributes shouldn't have side effects
        file_put_contents('/tmp/log', 'attribute created');
    }
}

// ✓ GOOD: Document attribute usage
/**
 * @\Attribute
 * 
 * Marks a method as cacheable with automatic TTL management.
 * 
 * Example:
 *   #[Cacheable(ttl: 3600)]
 *   public function expensiveOperation(): Result
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Cacheable
{
    public function __construct(public int $ttl = 3600) {}
}
```

---

## Complete Examples

### Full Framework Integration

```php
<?php
declare(strict_types=1);

namespace App\Framework;

// Attribute definitions
#[Attribute(Attribute::TARGET_CLASS)]
class Entity
{
    public function __construct(public string $table = '') {}
}

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(public string $name = '', public string $type = 'VARCHAR') {}
}

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        public string $path,
        public string $method = 'GET'
    ) {}
}

#[Attribute(Attribute::TARGET_METHOD)]
class Auth
{
    public function __construct(public array $roles = []) {}
}

// Model with attributes
#[Entity(table: 'users')]
class User
{
    #[Column(name: 'id', type: 'INT')]
    private int $id;

    #[Column(name: 'email', type: 'VARCHAR')]
    private string $email;

    #[Column(name: 'name', type: 'VARCHAR')]
    private string $name;

    public function __construct(int $id, string $email, string $name)
    {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
    }
}

// Controller with attributes
class UserController
{
    #[Route('/users', 'GET')]
    public function list(): array
    {
        return [];
    }

    #[Route('/users/{id}', 'GET')]
    public function show(int $id): User
    {
        return new User(1, 'john@example.com', 'John');
    }

    #[Route('/users', 'POST')]
    #[Auth(['admin'])]
    public function create(array $data): User
    {
        return new User(2, $data['email'], $data['name']);
    }
}

// Reflection-based framework
class ORM
{
    public function getTableName(object $entity): string
    {
        $reflection = new ReflectionClass($entity);
        $attrs = $reflection->getAttributes(Entity::class);

        if (empty($attrs)) {
            return strtolower($reflection->getShortName()) . 's';
        }

        $entity = $attrs[0]->newInstance();
        return $entity->table ?: strtolower($reflection->getShortName()) . 's';
    }

    public function getColumns(object $entity): array
    {
        $columns = [];
        $reflection = new ReflectionClass($entity);

        foreach ($reflection->getProperties() as $property) {
            $attrs = $property->getAttributes(Column::class);

            if (!empty($attrs)) {
                $col = $attrs[0]->newInstance();
                $columns[$property->getName()] = [
                    'name' => $col->name ?: $property->getName(),
                    'type' => $col->type,
                ];
            }
        }

        return $columns;
    }
}

class Router
{
    public function discoverRoutes(object $controller): array
    {
        $routes = [];
        $reflection = new ReflectionClass($controller);

        foreach ($reflection->getMethods() as $method) {
            $routeAttrs = $method->getAttributes(Route::class);

            if (!empty($routeAttrs)) {
                $route = $routeAttrs[0]->newInstance();
                $authAttrs = $method->getAttributes(Auth::class);
                $roles = [];

                if (!empty($authAttrs)) {
                    $roles = $authAttrs[0]->newInstance()->roles;
                }

                $routes[$route->method . ' ' . $route->path] = [
                    'handler' => [$controller, $method->getName()],
                    'roles' => $roles,
                ];
            }
        }

        return $routes;
    }
}

// Usage
$user = new User(1, 'john@example.com', 'John');
$orm = new ORM();

echo "Table: " . $orm->getTableName($user);
print_r($orm->getColumns($user));

$controller = new UserController();
$router = new Router();
$routes = $router->discoverRoutes($controller);

foreach ($routes as $pattern => $route) {
    echo "$pattern -> {$route['roles'][0] ?? 'public'}\n";
}
```

---

## See Also

- [PHP 8.2 Overview](0-php8.2-overview.md)
- [DNF Types](3-dnf-types.md)
- [Readonly Classes](2-readonly-classes.md)
