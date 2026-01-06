# Attributes

## Overview

Attributes (formerly Annotations) allow adding structured metadata to classes, properties, methods, and parameters, enabling introspection and configuration without docblocks or XML files.

---

## Basic Attributes

```php
<?php
#[Route('/api/users')]
#[Authenticated]
class UserController {
    #[Route(path: '/api/users/{id}', method: 'GET')]
    public function getUser(#[Parameter] int $id): array {
        return ['id' => $id];
    }
}

// Reading attributes
$reflection = new ReflectionClass('UserController');
$attributes = $reflection->getAttributes();

foreach ($attributes as $attribute) {
    echo $attribute->getName(); // Route, Authenticated
}
?>
```

---

## Defining Custom Attributes

```php
<?php
#[Attribute]
class Route {
    public function __construct(
        public string $path,
        public string $method = 'GET'
    ) {}
}

#[Attribute]
class Required {
    public function __construct(
        public string $message = "This field is required"
    ) {}
}

#[Route('/api/products', method: 'POST')]
class ProductController {
    #[Required]
    private string $name;
}
?>
```

---

## Attribute Targets

```php
<?php
// Target class only
#[Attribute(Attribute::TARGET_CLASS)]
class Entity {}

// Target method only
#[Attribute(Attribute::TARGET_METHOD)]
class Cache {}

// Target property only
#[Attribute(Attribute::TARGET_PROPERTY)]
class Serializable {}

// Multiple targets
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Deprecated {}

// All targets
#[Attribute(Attribute::TARGET_ALL)]
class Documentation {}
?>
```

---

## Reading Attributes with Reflection

```php
<?php
#[Attribute]
class Validate {
    public function __construct(
        public string $type,
        public bool $required = true
    ) {}
}

class User {
    #[Validate(type: 'email', required: true)]
    private string $email;
    
    #[Validate(type: 'string', required: true)]
    private string $name;
}

// Read attributes
$reflection = new ReflectionClass('User');
foreach ($reflection->getProperties() as $property) {
    $attrs = $property->getAttributes('Validate');
    
    foreach ($attrs as $attr) {
        $validate = $attr->newInstance();
        echo "Property: " . $property->getName() . "\n";
        echo "Type: " . $validate->type . "\n";
        echo "Required: " . ($validate->required ? "yes" : "no") . "\n";
    }
}
?>
```

---

## Practical Use Cases

### 1. Validation Framework

```php
<?php
#[Attribute]
class Rule {
    public function __construct(
        public string $name,
        public array $params = []
    ) {}
}

class RegisterForm {
    #[Rule('required')]
    #[Rule('email')]
    public string $email = '';
    
    #[Rule('required')]
    #[Rule('min', ['length' => 8])]
    public string $password = '';
    
    #[Rule('required')]
    #[Rule('min', ['length' => 3])]
    public string $username = '';
}

class Validator {
    public function validate(object $object): array {
        $errors = [];
        $reflection = new ReflectionClass($object);
        
        foreach ($reflection->getProperties() as $property) {
            $rules = $property->getAttributes('Rule');
            
            foreach ($rules as $rule) {
                $ruleInstance = $rule->newInstance();
                if (!$this->checkRule($ruleInstance->name, $property->getValue($object))) {
                    $errors[$property->getName()] = "Validation failed";
                }
            }
        }
        
        return $errors;
    }
    
    private function checkRule(string $rule, $value): bool {
        return match($rule) {
            'required' => !empty($value),
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL),
            default => true
        };
    }
}
?>
```

### 2. ORM Attributes

```php
<?php
#[Attribute]
class Table {
    public function __construct(public string $name) {}
}

#[Attribute]
class Column {
    public function __construct(
        public string $name,
        public string $type = 'string',
        public bool $nullable = false,
        public bool $primary = false
    ) {}
}

#[Table('users')]
class User {
    #[Column('id', type: 'int', primary: true)]
    public int $id;
    
    #[Column('email', type: 'string')]
    public string $email;
    
    #[Column('age', type: 'int', nullable: true)]
    public ?int $age;
}

class TableBuilder {
    public function buildCreateTable(string $className): string {
        $reflection = new ReflectionClass($className);
        $tableAttr = $reflection->getAttributes('Table')[0];
        $table = $tableAttr->newInstance();
        
        $sql = "CREATE TABLE " . $table->name . " (\n";
        
        $columns = [];
        foreach ($reflection->getProperties() as $property) {
            $attrs = $property->getAttributes('Column');
            if ($attrs) {
                $column = $attrs[0]->newInstance();
                $col = "  " . $column->name . " " . $column->type;
                if ($column->primary) $col .= " PRIMARY KEY";
                if (!$column->nullable) $col .= " NOT NULL";
                $columns[] = $col;
            }
        }
        
        $sql .= implode(",\n", $columns) . "\n)";
        return $sql;
    }
}

$builder = new TableBuilder();
echo $builder->buildCreateTable('User');
?>
```

### 3. Caching Attributes

```php
<?php
#[Attribute]
class Cache {
    public function __construct(
        public int $ttl = 3600,
        public ?string $key = null
    ) {}
}

class Repository {
    private array $cache = [];
    
    #[Cache(ttl: 3600, key: 'user_{id}')]
    public function getUserById(int $id): array {
        return ['id' => $id, 'name' => 'User ' . $id];
    }
    
    public function getCachedResult(string $method, array $args): mixed {
        $reflection = new ReflectionMethod($this::class, $method);
        $cacheAttrs = $reflection->getAttributes('Cache');
        
        if (!$cacheAttrs) {
            return $this->$method(...$args);
        }
        
        $cache = $cacheAttrs[0]->newInstance();
        $cacheKey = $cache->key ?? $method;
        
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }
        
        $result = $this->$method(...$args);
        $this->cache[$cacheKey] = $result;
        
        return $result;
    }
}
?>
```

---

## Best Practices

1. **Use for Configuration** - Metadata and configuration
2. **Repeatable Attributes** - Allow multiple attribute applications
3. **Type Hints** - Always include proper type hints
4. **Documentation** - Document what each attribute does

```php
<?php
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
#[Attribute(Attribute::IS_REPEATABLE)]
class Middleware {
    public function __construct(
        public string $name,
        public array $options = []
    ) {}
}
?>
```

---

## Common Mistakes

### 1. Not Targeting Correctly

```php
<?php
// ❌ Wrong - attribute can't be used on this target
#[Attribute(Attribute::TARGET_CLASS)]
class Rule {}

class Field {
    #[Rule]  // Error! Rule targets only classes
    public string $name;
}

// ✅ Correct
#[Attribute(Attribute::TARGET_PROPERTY)]
class Rule {}
?>
```

### 2. Repeatable Confusion

```php
<?php
// ❌ Wrong - not marked repeatable
#[Attribute]
class Tag {
    public function __construct(public string $name) {}
}

class Post {
    #[Tag('php')]
    #[Tag('web')]  // Error if not repeatable
    public string $content;
}

// ✅ Correct
#[Attribute(Attribute::IS_REPEATABLE)]
class Tag {
    public function __construct(public string $name) {}
}
?>
```

---

## Complete Example

```php
<?php
#[Attribute]
class OpenAPI {
    public function __construct(
        public string $summary,
        public string $description = "",
        public int $status = 200
    ) {}
}

#[Attribute]
class Param {
    public function __construct(
        public string $name,
        public string $type,
        public bool $required = true
    ) {}
}

class APIDocumentation {
    #[OpenAPI(
        summary: 'Get user by ID',
        description: 'Retrieves a specific user',
        status: 200
    )]
    public function getUser(
        #[Param('id', 'integer')] int $id
    ): array {
        return ['id' => $id];
    }
    
    public static function generateOpenAPI(): string {
        $reflection = new ReflectionClass(self::class);
        $methods = $reflection->getMethods();
        
        $spec = [];
        foreach ($methods as $method) {
            $apiAttrs = $method->getAttributes('OpenAPI');
            
            if ($apiAttrs) {
                $api = $apiAttrs[0]->newInstance();
                $spec[$method->getName()] = [
                    'summary' => $api->summary,
                    'description' => $api->description,
                    'status' => $api->status
                ];
            }
        }
        
        return json_encode($spec, JSON_PRETTY_PRINT);
    }
}

echo APIDocumentation::generateOpenAPI();
?>
```

---

## See Also

- Documentation: [Attributes](https://www.php.net/manual/en/language.attributes.php)
- Related: [Reflection](../03-oop/40-reflection.md), [Named Arguments](2-named-argument.md)
