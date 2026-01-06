# Static Keyword

## Overview

The `static` keyword defines properties and methods that belong to the class, not instances.

---

## Static Properties

```php
<?php
class Counter {
    public static $count = 0;
    
    public static function increment(): int {
        return ++self::$count;
    }
}

echo Counter::increment();  // 1
echo Counter::increment();  // 2
?>
```

---

## Static Methods

```php
<?php
class Math {
    public static function add(int $a, int $b): int {
        return $a + $b;
    }
}

echo Math::add(5, 3);  // 8
?>
```

---

## Late Static Binding

```php
<?php
class Base {
    public static function who(): string {
        return 'Base';
    }
    
    public static function test(): string {
        return static::who();
    }
}

class Child extends Base {
    public static function who(): string {
        return 'Child';
    }
}

echo Child::test();  // Child
?>
```

---

## Complete Example

```php
<?php
class Database {
    private static ?self $instance = null;
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

$db1 = Database::getInstance();
$db2 = Database::getInstance();
var_dump($db1 === $db2);  // true
?>
```

---

## Next Steps

→ Learn [stdClass](29-stdClass.md)  
→ Study [object iteration](30-object-iteration.md)
