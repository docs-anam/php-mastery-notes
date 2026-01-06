# Magic Methods and Functions

## Overview

Magic methods are special methods that are automatically called by PHP in certain situations.

---

## __construct and __destruct

```php
<?php
class Database {
    public function __construct() {
        echo "Connection opened\n";
    }
    
    public function __destruct() {
        echo "Connection closed\n";
    }
}

$db = new Database();
unset($db);
?>
```

---

## __toString

```php
<?php
class User {
    public $name;
    
    public function __toString(): string {
        return "User: {$this->name}";
    }
}

$user = new User();
$user->name = 'John';

echo $user;  // User: John
?>
```

---

## __get and __set

```php
<?php
class DynamicObject {
    private $data = [];
    
    public function __get($name) {
        return $this->data[$name] ?? null;
    }
    
    public function __set($name, $value) {
        $this->data[$name] = $value;
    }
}

$obj = new DynamicObject();
$obj->name = 'John';
echo $obj->name;
?>
```

---

## __call and __callStatic

```php
<?php
class Handler {
    public function __call($name, $args) {
        echo "Called: $name\n";
    }
    
    public static function __callStatic($name, $args) {
        echo "Static called: $name\n";
    }
}

$h = new Handler();
$h->anyMethod();         // Called: anyMethod
Handler::staticMethod(); // Static called: staticMethod
?>
```

---

## __invoke

```php
<?php
class Multiplier {
    private $factor;
    
    public function __construct($factor) {
        $this->factor = $factor;
    }
    
    public function __invoke($value) {
        return $value * $this->factor;
    }
}

$times3 = new Multiplier(3);
echo $times3(5);  // 15
?>
```

---

## __isset and __unset

```php
<?php
class LazyLoader {
    private $data = [];
    
    public function __isset($name) {
        return isset($this->data[$name]);
    }
    
    public function __unset($name) {
        unset($this->data[$name]);
    }
}
?>
```

---

## Next Steps

→ Learn [overloading](35-overloading.md)  
→ Study [variance](36-covariance-and-contravariance.md)
