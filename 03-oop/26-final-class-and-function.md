# Final Classes and Methods

## Table of Contents
1. [Overview](#overview)
2. [Final Keyword](#final-keyword)
3. [Final Classes](#final-classes)
4. [Final Methods](#final-methods)
5. [Use Cases](#use-cases)
6. [Practical Examples](#practical-examples)
7. [Common Mistakes](#common-mistakes)

---

## Overview

The `final` keyword prevents classes from being extended and methods from being overridden.

**When to use:**
- Prevent improper class extension
- Lock method implementation
- Secure sensitive logic

---

## Final Classes

### Preventing Inheritance

```php
<?php
final class User {
    public $name;
}

// ❌ Error
class AdminUser extends User {}

// ✓ Use composition instead
class Admin {
    private User $user;
}
?>
```

---

## Final Methods

### Preventing Override

```php
<?php
class Base {
    final public function criticalOperation(): void {
        echo "Critical";
    }
}

class Child extends Base {
    // ❌ Can't override
    // public function criticalOperation() {}
}
?>
```

---

## Practical Examples

### Security Class

```php
<?php
final class SecurityManager {
    final public function hash(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}

$manager = new SecurityManager();
echo $manager->hash('password123');
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

final class Database {
    private static ?self $instance = null;
    
    final public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    final public function query(string $sql): array {
        return [];
    }
}

$db = Database::getInstance();
?>
```

---

## Next Steps

✅ Understand final keyword  
→ Learn [anonymous classes](27-anonymous-class.md)  
→ Study [static keyword](28-static-keyword.md)
