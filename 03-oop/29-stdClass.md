# stdClass

## Overview

`stdClass` is PHP's generic empty class for creating dynamic objects.

---

## Creating stdClass Objects

```php
<?php
$obj = new stdClass();
$obj->name = 'John';
$obj->age = 30;
$obj->email = 'john@example.com';

echo $obj->name;
?>
```

---

## Converting Array to Object

```php
<?php
$array = ['name' => 'Jane', 'age' => 28];
$obj = (object)$array;

echo $obj->name;  // Jane
echo $obj->age;   // 28
?>
```

---

## Converting Object to Array

```php
<?php
$obj = new stdClass();
$obj->id = 1;
$obj->name = 'Test';

$array = (array)$obj;
print_r($array);
?>
```

---

## Complete Example

```php
<?php
$data = new stdClass();
$data->users = [];
$data->settings = (object)['theme' => 'dark', 'lang' => 'en'];

$user = new stdClass();
$user->id = 1;
$user->name = 'John';
$data->users[] = $user;

print_r($data);
?>
```

---

## Next Steps

→ Learn [object iteration](30-object-iteration.md)  
→ Study [generators](31-generator.md)
