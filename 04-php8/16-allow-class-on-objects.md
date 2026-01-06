# Get Class for Objects

## Overview

`get_class()` returns the fully qualified name of a class, and in PHP 8, `get_class()` on objects is more consistent and useful for type checking and dynamic class operations.

---

## Basic Usage

```php
<?php
class User {}
class Admin extends User {}

$user = new User();
$admin = new Admin();

echo get_class($user); // User
echo get_class($admin); // Admin

// Can also be called without argument on $this
class Article {
    public function getClassName(): string {
        return get_class(); // Returns Article
    }
}
?>
```

---

## Fully Qualified Names

```php
<?php
namespace App\Models;

class User {}

$user = new User();
echo get_class($user); // App\Models\User

// With namespace separator
$fqn = '\\' . get_class($user); // \App\Models\User
?>
```

---

## Type Checking

```php
<?php
interface PaymentGateway {}
class StripeGateway implements PaymentGateway {}
class PayPalGateway implements PaymentGateway {}

$gateway = new StripeGateway();

// Check exact class
if (get_class($gateway) === 'StripeGateway') {
    echo "Using Stripe\n";
}

// Check if instance of class
if ($gateway instanceof PaymentGateway) {
    echo "Valid payment gateway\n";
}
?>
```

---

## Dynamic Class Operations

```php
<?php
class Loader {
    public function load(object $entity): void {
        $className = get_class($entity);
        $reflection = new ReflectionClass($className);
        
        echo "Loading class: " . $reflection->getShortName() . "\n";
        echo "Namespace: " . $reflection->getNamespaceName() . "\n";
    }
}

$loader = new Loader();
$loader->load(new DateTime()); // Loading class: DateTime
?>
```

---

## Registry Pattern

```php
<?php
class ServiceRegistry {
    private array $services = [];
    
    public function register(object $service): void {
        $className = get_class($service);
        $this->services[$className] = $service;
    }
    
    public function get(string $className): ?object {
        return $this->services[$className] ?? null;
    }
}

$registry = new ServiceRegistry();
$registry->register(new Database());
$registry->register(new Logger());

// Later retrieve by class name
$db = $registry->get('Database');
?>
```

---

## Factory Pattern

```php
<?php
class Factory {
    private array $resolvers = [];
    
    public function registerResolver(string $class, callable $resolver): void {
        $this->resolvers[$class] = $resolver;
    }
    
    public function create(object $blueprint): object {
        $class = get_class($blueprint);
        $resolver = $this->resolvers[$class] ?? null;
        
        if (!$resolver) {
            throw new RuntimeException("No resolver for $class");
        }
        
        return $resolver($blueprint);
    }
}

class ProductFactory extends Factory {
    public function __construct() {
        $this->registerResolver('ShirtBlueprint', fn($bp) => new Shirt($bp));
    }
}
?>
```

---

## Real-World Examples

### 1. Serialization

```php
<?php
class Serializer {
    public function serialize(object $object): array {
        return [
            '__class__' => get_class($object),
            'data' => get_object_vars($object)
        ];
    }
    
    public function deserialize(array $data): object {
        $className = $data['__class__'];
        $object = new $className();
        
        foreach ($data['data'] as $property => $value) {
            $object->$property = $value;
        }
        
        return $object;
    }
}
?>
```

### 2. Event Dispatcher

```php
<?php
class EventDispatcher {
    private array $handlers = [];
    
    public function dispatch(object $event): void {
        $eventClass = get_class($event);
        
        if (!isset($this->handlers[$eventClass])) {
            return;
        }
        
        foreach ($this->handlers[$eventClass] as $handler) {
            $handler($event);
        }
    }
    
    public function listen(string $eventClass, callable $handler): void {
        $this->handlers[$eventClass][] = $handler;
    }
}

class UserCreatedEvent {
    public function __construct(public int $userId) {}
}

$dispatcher = new EventDispatcher();
$dispatcher->listen(UserCreatedEvent::class, function($event) {
    echo "User {$event->userId} created\n";
});

$dispatcher->dispatch(new UserCreatedEvent(123));
?>
```

### 3. Repository Pattern

```php
<?php
class Repository {
    protected string $model;
    
    public function __construct(object $modelInstance) {
        $this->model = get_class($modelInstance);
    }
    
    public function find(int $id): ?object {
        // Use model class name to build query
        $tableName = strtolower($this->model);
        return $this->db->query("SELECT * FROM $tableName WHERE id = ?", [$id]);
    }
}

class User {}
$userRepo = new Repository(new User());
$user = $userRepo->find(1); // Queries 'user' table
?>
```

### 4. Logger with Class Tracking

```php
<?php
class Logger {
    public function log(object $context, string $message): void {
        $className = get_class($context);
        $shortName = substr($className, strrpos($className, '\\') + 1);
        
        echo "[$shortName] $message\n";
    }
}

class UserService {
    private Logger $logger;
    
    public function __construct() {
        $this->logger = new Logger();
    }
    
    public function create($data): void {
        $this->logger->log($this, "Creating user");
        // Implementation
    }
}

(new UserService())->create([]); // [UserService] Creating user
?>
```

### 5. Cache with Type Aware Keys

```php
<?php
class TypedCache {
    private array $cache = [];
    
    public function set(object $object, mixed $data): void {
        $key = get_class($object) . ':' . spl_object_id($object);
        $this->cache[$key] = $data;
    }
    
    public function get(object $object): mixed {
        $key = get_class($object) . ':' . spl_object_id($object);
        return $this->cache[$key] ?? null;
    }
}

$user = new User();
$cache = new TypedCache();
$cache->set($user, ['cached' => true]);
var_dump($cache->get($user)); // ['cached' => true]
?>
```

---

## Reflection Integration

```php
<?php
class Inspector {
    public function inspect(object $object): void {
        $className = get_class($object);
        $reflection = new ReflectionClass($className);
        
        echo "Class: " . $reflection->getShortName() . "\n";
        echo "Namespace: " . $reflection->getNamespaceName() . "\n";
        echo "Abstract: " . ($reflection->isAbstract() ? 'Yes' : 'No') . "\n";
        echo "Implements: " . implode(', ', $reflection->getInterfaceNames()) . "\n";
        
        echo "Methods:\n";
        foreach ($reflection->getMethods() as $method) {
            echo "  - " . $method->getName() . "\n";
        }
    }
}

$inspector = new Inspector();
$inspector->inspect(new DateTime());
?>
```

---

## Best Practices

### 1. Use get_class() for Class Operations

```php
<?php
// ✅ Good - clear intent
$className = get_class($object);
$reflection = new ReflectionClass($className);

// ✅ Good - registry lookup
$service = $registry->get(get_class($object));

// ❌ Avoid - use get_class() instead
$className = explode('\\', get_called_class());
?>
```

### 2. Handle Namespaces Properly

```php
<?php
function getClassName(object $object): string {
    $full = get_class($object);
    return substr($full, strrpos($full, '\\') + 1);
}

echo getClassName(new App\Models\User()); // User
?>
```

### 3. Use for Polymorphism

```php
<?php
// ✅ Good - handling different classes
$handler = match(get_class($event)) {
    UserCreated::class => new UserCreatedHandler(),
    UserDeleted::class => new UserDeletedHandler(),
    default => throw new UnknownEventException()
};

$handler->handle($event);
?>
```

---

## Common Mistakes

### 1. String Comparison Issues

```php
<?php
// ❌ Wrong - namespace matters
if (get_class($user) === 'User') {} // False if namespaced

// ✅ Correct - use fully qualified name or instanceof
if (get_class($user) === 'App\\Models\\User') {}
if ($user instanceof User) {}
?>
```

### 2. Forgetting Namespace Separator

```php
<?php
// ❌ Wrong - missing backslash
$className = get_class($object); // App\Models\User
// Concatenating path: 'path/' . $className . '.php'
// Results in: 'path/App\Models\User.php' (wrong separator)

// ✅ Correct - normalize path
$path = str_replace('\\', '/', get_class($object));
// Results in: 'path/App/Models/User.php'
?>
```

---

## Complete Example

```php
<?php
class ObjectManager {
    private array $registry = [];
    
    public function register(object $object, string $alias = ''): void {
        $className = get_class($object);
        $key = $alias ?: $className;
        
        $this->registry[$key] = [
            'class' => $className,
            'instance' => $object,
            'created' => time()
        ];
    }
    
    public function get(string $alias): ?object {
        return $this->registry[$alias]['instance'] ?? null;
    }
    
    public function getByClass(string $className): array {
        $matches = [];
        
        foreach ($this->registry as $alias => $item) {
            if ($item['class'] === $className || is_a($item['instance'], $className)) {
                $matches[$alias] = $item['instance'];
            }
        }
        
        return $matches;
    }
    
    public function listRegistered(): array {
        $result = [];
        
        foreach ($this->registry as $alias => $item) {
            $result[$alias] = [
                'class' => $item['class'],
                'created' => date('Y-m-d H:i:s', $item['created'])
            ];
        }
        
        return $result;
    }
}

// Usage
$manager = new ObjectManager();
$manager->register(new PDO('sqlite::memory:'), 'database');
$manager->register(new Logger(), 'logger');

var_dump($manager->getByClass('Logger'));
print_r($manager->listRegistered());
?>
```

---

## See Also

- Documentation: [get_class()](https://www.php.net/manual/en/function.get-class.php)
- Related: [Reflection](../03-oop/40-reflection.md), [instanceof](../03-oop/15-type-checking-casting.md)
