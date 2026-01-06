# PHP Reflection API

## Overview

Reflection API enables inspecting classes, methods, properties, and functions at runtime for introspection and dynamic manipulation.

---

## ReflectionClass Basics

```php
<?php
class User {
    public string $name;
    private string $email;
    
    public function __construct(string $name, string $email) {
        $this->name = $name;
        $this->email = $email;
    }
    
    public function getName(): string {
        return $this->name;
    }
}

$reflection = new ReflectionClass('User');

// Get class name
echo $reflection->getName(); // User

// Get methods
foreach ($reflection->getMethods() as $method) {
    echo $method->getName() . "\n";
}

// Get properties
foreach ($reflection->getProperties() as $property) {
    echo $property->getName() . "\n";
}
?>
```

---

## ReflectionMethod

```php
<?php
class Product {
    public function calculate(float $price, float $tax): float {
        return $price + ($price * $tax);
    }
}

$reflection = new ReflectionMethod('Product', 'calculate');

// Get parameters
foreach ($reflection->getParameters() as $param) {
    echo "Parameter: " . $param->getName() . "\n";
    echo "Type: " . $param->getType() . "\n";
}

// Check if public
if ($reflection->isPublic()) {
    echo "Method is public\n";
}

// Invoke method
$object = new Product();
$result = $reflection->invoke($object, 100, 0.1);
echo $result; // 110
?>
```

---

## ReflectionProperty

```php
<?php
class Article {
    private string $title;
    public int $views;
    protected array $tags;
    
    public function __construct() {
        $this->title = "PHP";
        $this->views = 100;
        $this->tags = ['web', 'backend'];
    }
}

$reflection = new ReflectionClass('Article');

foreach ($reflection->getProperties() as $property) {
    echo "Name: " . $property->getName() . "\n";
    
    if ($property->isPublic()) {
        echo "Visibility: Public\n";
    } elseif ($property->isPrivate()) {
        echo "Visibility: Private\n";
    } elseif ($property->isProtected()) {
        echo "Visibility: Protected\n";
    }
    
    // Get property type
    if ($property->hasType()) {
        echo "Type: " . $property->getType() . "\n";
    }
}
?>
```

---

## ReflectionParameter

```php
<?php
class Logger {
    public function log(string $message, int $level = 1): void {
        echo "[$level] $message\n";
    }
}

$method = new ReflectionMethod('Logger', 'log');

foreach ($method->getParameters() as $param) {
    echo "Name: " . $param->getName() . "\n";
    echo "Type: " . $param->getType() . "\n";
    
    if ($param->isDefaultValueAvailable()) {
        echo "Default: " . $param->getDefaultValue() . "\n";
    }
}
?>
```

---

## Dynamic Object Creation

```php
<?php
class Factory {
    public static function create(string $className, array $args = []): object {
        $reflection = new ReflectionClass($className);
        return $reflection->newInstanceArgs($args);
    }
}

class Product {
    public function __construct(string $name, float $price) {
        echo "Created: $name ($price)\n";
    }
}

$product = Factory::create('Product', ['Laptop', 999.99]);
?>
```

---

## Practical Use Cases

### 1. Dependency Injection Container

```php
<?php
class Container {
    private array $bindings = [];
    
    public function register(string $name, callable $resolver): void {
        $this->bindings[$name] = $resolver;
    }
    
    public function resolve(string $name): object {
        return $this->bindings[$name]();
    }
    
    public function autowire(string $className): object {
        $reflection = new ReflectionClass($className);
        $constructor = $reflection->getConstructor();
        
        if (!$constructor) {
            return new $className();
        }
        
        $params = [];
        foreach ($constructor->getParameters() as $param) {
            $paramType = $param->getType();
            if ($paramType) {
                $params[] = $this->resolve($paramType->getName());
            }
        }
        
        return $reflection->newInstanceArgs($params);
    }
}
?>
```

---

### 2. Serialization

```php
<?php
class Serializer {
    public function serialize(object $object): array {
        $reflection = new ReflectionClass($object);
        $data = [];
        
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $data[$property->getName()] = $property->getValue($object);
        }
        
        return $data;
    }
    
    public function deserialize(array $data, string $className): object {
        $reflection = new ReflectionClass($className);
        $object = $reflection->newInstanceWithoutConstructor();
        
        foreach ($data as $name => $value) {
            $property = $reflection->getProperty($name);
            $property->setAccessible(true);
            $property->setValue($object, $value);
        }
        
        return $object;
    }
}
?>
```

---

### 3. Method Documentation Scanner

```php
<?php
class DocScanner {
    public function getMethodDocs(string $className, string $methodName): array {
        $reflection = new ReflectionMethod($className, $methodName);
        $doc = $reflection->getDocComment();
        
        if (!$doc) {
            return [];
        }
        
        // Parse @param, @return, @throws
        $docs = [];
        preg_match_all('/@param\s+(\S+)\s+(\$\w+)/', $doc, $params);
        preg_match_all('/@return\s+(\S+)/', $doc, $returns);
        
        return [
            'params' => array_combine($params[2], $params[1]),
            'return' => $returns[1][0] ?? null
        ];
    }
}
?>
```

---

### 4. Validation Framework

```php
<?php
class Validator {
    public function validate(object $object): array {
        $reflection = new ReflectionClass($object);
        $errors = [];
        
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            
            // Check type
            if ($property->hasType()) {
                $type = $property->getType();
                if (!$this->matchesType($value, $type)) {
                    $errors[$property->getName()] = "Invalid type";
                }
            }
        }
        
        return $errors;
    }
    
    private function matchesType($value, $type): bool {
        $typeName = (string)$type;
        return match($typeName) {
            'string' => is_string($value),
            'int' => is_int($value),
            'float' => is_float($value),
            'bool' => is_bool($value),
            'array' => is_array($value),
            default => $value instanceof $typeName,
        };
    }
}
?>
```

---

## Common Mistakes

### 1. Not Checking Type Before Using It

```php
<?php
// ❌ Wrong - may throw error
$param = new ReflectionParameter('function', 0);
$type = $param->getType();
echo $type->getName(); // Error if no type!

// ✅ Correct
if ($param->hasType()) {
    echo $param->getType()->getName();
}
?>
```

### 2. Forgetting setAccessible() for Private Properties

```php
<?php
class Secret {
    private string $password = "secret123";
}

$reflection = new ReflectionClass('Secret');
$property = $reflection->getProperty('password');

// ❌ Wrong - access denied
// echo $property->getValue(new Secret());

// ✅ Correct
$property->setAccessible(true);
echo $property->getValue(new Secret()); // secret123
?>
```

---

## Best Practices

1. **Cache Reflections** - Don't recreate ReflectionClass repeatedly
2. **Handle Exceptions** - Reflection throws ReflectionException
3. **Security** - Be careful exposing private data via reflection
4. **Performance** - Cache reflection results in production

---

## Complete Example

```php
<?php
class ORM {
    public function insert(object $object): bool {
        $reflection = new ReflectionClass($object);
        $table = strtolower($reflection->getShortName());
        
        $columns = [];
        $values = [];
        
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $columns[] = $property->getName();
            $values[] = "'" . $property->getValue($object) . "'";
        }
        
        $sql = "INSERT INTO $table (" . implode(', ', $columns) . 
               ") VALUES (" . implode(', ', $values) . ")";
        
        echo $sql;
        return true;
    }
}

class User {
    public string $name = "John";
    public string $email = "john@example.com";
}

$orm = new ORM();
$orm->insert(new User());
// INSERT INTO user (name, email) VALUES ('John', 'john@example.com')
?>
```

---

## See Also

- Documentation: [Reflection API](https://www.php.net/manual/en/book.reflection.php)
- Related: [Exception](38-exception.md), [Magic Functions](34-magic-function.md)
