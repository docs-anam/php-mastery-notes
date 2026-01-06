# Models and Data Access

## Overview

Create models to handle data access, business logic, and database interactions in a clean, maintainable way.

---

## Table of Contents

1. What are Models
2. Basic Model
3. Query Methods
4. Relationships
5. Validation
6. Model Methods
7. Complete Examples

---

## What are Models

### Purpose

```
Model = Data and Business Logic

Responsibilities:
- Database operations
- Data validation
- Business rules
- Data transformations
- Relationships
```

### Model Structure

```php
<?php

class User {
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];
    
    protected $db;
    
    public function __construct() {
        $this->db = new Database();
    }
}
```

---

## Basic Model

### CRUD Operations

```php
<?php

class Product {
    
    protected $table = 'products';
    protected $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // CREATE
    public function create($data) {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        
        $query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        
        return $this->db->execute($query, array_values($data));
    }
    
    // READ
    public function find($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $result = $this->db->query($query, [$id]);
        
        return $result ? $result[0] : null;
    }
    
    public function all() {
        $query = "SELECT * FROM {$this->table}";
        return $this->db->query($query);
    }
    
    public function where($column, $operator, $value) {
        $query = "SELECT * FROM {$this->table} WHERE $column $operator ?";
        return $this->db->query($query, [$value]);
    }
    
    // UPDATE
    public function update($id, $data) {
        $sets = implode(',', array_map(fn($k) => "$k=?", array_keys($data)));
        
        $query = "UPDATE {$this->table} SET $sets WHERE id=?";
        
        return $this->db->execute(
            $query,
            [...array_values($data), $id]
        );
    }
    
    // DELETE
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id=?";
        return $this->db->execute($query, [$id]);
    }
}
```

---

## Query Methods

### Fluent Query Builder

```php
<?php

class QueryBuilder {
    
    private $table;
    private $wheres = [];
    private $bindings = [];
    private $selects = ['*'];
    private $limit_value = null;
    private $offset_value = 0;
    
    public function __construct($table) {
        $this->table = $table;
    }
    
    public function select($columns) {
        $this->selects = is_array($columns) ? $columns : func_get_args();
        return $this;
    }
    
    public function where($column, $operator = '=', $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->wheres[] = "$column $operator ?";
        $this->bindings[] = $value;
        
        return $this;
    }
    
    public function limit($limit) {
        $this->limit_value = $limit;
        return $this;
    }
    
    public function offset($offset) {
        $this->offset_value = $offset;
        return $this;
    }
    
    public function get() {
        return $this->execute();
    }
    
    public function first() {
        $results = $this->limit(1)->get();
        return $results ? $results[0] : null;
    }
    
    public function execute() {
        $query = "SELECT " . implode(',', $this->selects) . " FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $query .= " WHERE " . implode(" AND ", $this->wheres);
        }
        
        if ($this->limit_value) {
            $query .= " LIMIT {$this->limit_value}";
        }
        
        if ($this->offset_value) {
            $query .= " OFFSET {$this->offset_value}";
        }
        
        return Database::query($query, $this->bindings);
    }
}

// Usage
public function find($id) {
    return (new QueryBuilder('users'))
        ->where('id', $id)
        ->first();
}

public function search($term) {
    return (new QueryBuilder('products'))
        ->where('name', 'LIKE', "%$term%")
        ->limit(10)
        ->get();
}
```

---

## Relationships

### One-to-Many

```php
<?php

class User {
    
    public function posts() {
        $posts = $this->db->query(
            "SELECT * FROM posts WHERE user_id = ?",
            [$this->id]
        );
        
        return array_map(fn($p) => new Post($p), $posts);
    }
}

class Post {
    
    public function author() {
        return $this->db->query(
            "SELECT * FROM users WHERE id = ?",
            [$this->user_id]
        )[0];
    }
}

// Usage
$user = User::find(1);
foreach ($user->posts() as $post) {
    echo $post->title;
}
```

### Many-to-Many

```php
<?php

class User {
    
    public function roles() {
        $roleIds = $this->db->query(
            "SELECT role_id FROM user_roles WHERE user_id = ?",
            [$this->id]
        );
        
        $ids = array_column($roleIds, 'role_id');
        
        return $this->db->query(
            "SELECT * FROM roles WHERE id IN (" . implode(',', $ids) . ")"
        );
    }
}

// Usage
$user = User::find(1);
foreach ($user->roles() as $role) {
    echo $role->name;
}
```

---

## Validation

### Model Validation

```php
<?php

class User {
    
    protected $rules = [
        'name' => 'required|string|min:3',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
    ];
    
    public function validate($data) {
        $errors = [];
        
        foreach ($this->rules as $field => $rules) {
            $rules = explode('|', $rules);
            
            foreach ($rules as $rule) {
                if (strpos($rule, ':') !== false) {
                    [$rule, $value] = explode(':', $rule);
                    $error = $this->validateRule($field, $rule, $data[$field] ?? null, $value);
                } else {
                    $error = $this->validateRule($field, $rule, $data[$field] ?? null);
                }
                
                if ($error) {
                    $errors[$field] = $error;
                    break;
                }
            }
        }
        
        return $errors;
    }
    
    private function validateRule($field, $rule, $value, $param = null) {
        switch ($rule) {
            case 'required':
                return empty($value) ? "$field is required" : null;
            
            case 'string':
                return !is_string($value) ? "$field must be string" : null;
            
            case 'email':
                return !filter_var($value, FILTER_VALIDATE_EMAIL) ? "$field must be valid email" : null;
            
            case 'min':
                return strlen($value) < $param ? "$field must be at least $param characters" : null;
            
            case 'unique':
                $count = count($this->where($field, '=', $value));
                return $count > 0 ? "$field must be unique" : null;
            
            default:
                return null;
        }
    }
}

// Usage
public function store($data) {
    $errors = (new User())->validate($data);
    
    if (!empty($errors)) {
        return response()->back()->withErrors($errors);
    }
    
    User::create($data);
}
```

---

## Model Methods

### Eloquent-like Methods

```php
<?php

class Model {
    
    protected static $instances = [];
    
    public static function all() {
        return (new static)->query("SELECT * FROM " . static::table());
    }
    
    public static function find($id) {
        $results = (new static)->query(
            "SELECT * FROM " . static::table() . " WHERE id = ?",
            [$id]
        );
        
        return $results ? $results[0] : null;
    }
    
    public static function create($data) {
        $instance = new static($data);
        $instance->save();
        return $instance;
    }
    
    public function save() {
        if (isset($this->id)) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }
    
    public function delete() {
        return Database::execute(
            "DELETE FROM " . static::table() . " WHERE id = ?",
            [$this->id]
        );
    }
    
    protected static function table() {
        $class = strtolower(class_basename(static::class));
        return $class . 's';  // Pluralize
    }
}

// Usage
$user = User::find(1);
$user->name = 'John Doe';
$user->save();

User::create(['name' => 'Jane', 'email' => 'jane@example.com']);
```

---

## Complete Examples

### Example 1: User Model

```php
<?php

class User {
    
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];
    
    protected $db;
    private $attributes = [];
    
    public function __construct($attributes = []) {
        $this->db = new Database();
        $this->attributes = $attributes;
    }
    
    public function __get($key) {
        return $this->attributes[$key] ?? null;
    }
    
    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }
    
    public static function all() {
        return (new self)->db->query("SELECT * FROM users");
    }
    
    public static function find($id) {
        $result = (new self)->db->query(
            "SELECT * FROM users WHERE id = ?",
            [$id]
        );
        
        return $result ? new self($result[0]) : null;
    }
    
    public function create($data) {
        $this->db->execute(
            "INSERT INTO {$this->table} (name, email, password) VALUES (?,?,?)",
            [$data['name'], $data['email'], password_hash($data['password'], PASSWORD_BCRYPT)]
        );
    }
    
    public function posts() {
        $results = $this->db->query(
            "SELECT * FROM posts WHERE user_id = ?",
            [$this->id]
        );
        
        return array_map(fn($p) => new Post($p), $results);
    }
}
```

### Example 2: Product Model with Scopes

```php
<?php

class Product {
    
    public static function active() {
        return Database::query(
            "SELECT * FROM products WHERE active = 1"
        );
    }
    
    public static function expensive() {
        return Database::query(
            "SELECT * FROM products WHERE price > 100"
        );
    }
    
    public static function byCategory($category) {
        return Database::query(
            "SELECT * FROM products WHERE category = ?",
            [$category]
        );
    }
    
    public static function search($term) {
        return Database::query(
            "SELECT * FROM products WHERE name LIKE ? OR description LIKE ?",
            ["%$term%", "%$term%"]
        );
    }
}

// Usage
$products = Product::active();
$expensive = Product::expensive();
$electronics = Product::byCategory('electronics');
```

---

## Key Takeaways

**Model Checklist:**

1. ✅ Define table and fillable properties
2. ✅ Implement CRUD operations
3. ✅ Use parameterized queries
4. ✅ Add validation logic
5. ✅ Define relationships
6. ✅ Create query methods
7. ✅ Use fluent interfaces
8. ✅ Implement scopes
9. ✅ Handle data transformations
10. ✅ Test model behavior independently

---

## See Also

- [MVC Basics](0-mvc-basics.md)
- [Controllers](6-controller.md)
- [Views](9-view.md)
