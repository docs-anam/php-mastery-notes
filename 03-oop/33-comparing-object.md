# Comparing Objects

## Overview

Compare objects for equality or identity using different operators.

---

## Equality vs Identity

```php
<?php
class User {
    public $id;
    public $name;
}

$user1 = new User();
$user1->id = 1;
$user1->name = 'John';

$user2 = new User();
$user2->id = 1;
$user2->name = 'John';

// == (equality) - same properties
var_dump($user1 == $user2);   // true

// === (identity) - same object instance
var_dump($user1 === $user2);  // false
?>
```

---

## Reference Equality

```php
<?php
$obj1 = new stdClass();
$obj2 = $obj1;

var_dump($obj1 === $obj2);  // true (same reference)

$obj3 = clone $obj1;
var_dump($obj1 === $obj3);  // false (different instance)
?>
```

---

## Custom Comparison

```php
<?php
class Entity {
    public $id;
    
    public function equals(Entity $other): bool {
        return $this->id === $other->id;
    }
}

$e1 = new Entity();
$e1->id = 1;

$e2 = new Entity();
$e2->id = 1;

echo $e1->equals($e2) ? 'Equal' : 'Not equal';
?>
```

---

## Next Steps

→ Learn [magic methods](34-magic-function.md)  
→ Study [overloading](35-overloading.md)
