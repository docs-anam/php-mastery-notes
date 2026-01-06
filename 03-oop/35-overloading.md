# Overloading

## Overview

Overloading allows accessing inaccessible properties and methods dynamically.

---

## Property Overloading

```php
<?php
class PropertyOverloading {
    private $data = [];
    
    public function __get($name) {
        return $this->data[$name] ?? "Property not found: $name";
    }
    
    public function __set($name, $value) {
        $this->data[$name] = $value;
    }
}

$obj = new PropertyOverloading();
$obj->email = 'test@example.com';
echo $obj->email;
?>
```

---

## Method Overloading

```php
<?php
class MethodOverloading {
    public function __call($name, $args) {
        echo "Called: $name with " . count($args) . " arguments\n";
    }
    
    public static function __callStatic($name, $args) {
        echo "Static: $name\n";
    }
}

$obj = new MethodOverloading();
$obj->save('data', 'value');
MethodOverloading::delete();
?>
```

---

## Complete Example

```php
<?php
class FlexibleModel {
    private $attributes = [];
    
    public function __get($key) {
        return $this->attributes[$key] ?? null;
    }
    
    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }
    
    public function __call($method, $args) {
        if (str_starts_with($method, 'get')) {
            $key = lcfirst(substr($method, 3));
            return $this->attributes[$key] ?? null;
        }
    }
}

$model = new FlexibleModel();
$model->id = 1;
$model->name = 'Test';
echo $model->getId();
?>
```

---

## Next Steps

→ Learn [variance](36-covariance-and-contravariance.md)  
→ Study [DateTime](37-DateTime.md)
