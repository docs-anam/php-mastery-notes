# Covariance and Contravariance

## Overview

Variance rules define type compatibility when overriding methods in inheritance.

---

## Covariance (Return Types)

```php
<?php
class Animal {}
class Dog extends Animal {}

class AnimalShelter {
    public function getAnimal(): Animal {
        return new Animal();
    }
}

class DogShelter extends AnimalShelter {
    public function getAnimal(): Dog {
        return new Dog();
    }
}
?>
```

---

## Contravariance (Parameter Types)

```php
<?php
class Logger {
    public function log(Dog $dog): void {
        echo "Logging dog\n";
    }
}

class AnimalLogger extends Logger {
    public function log(Animal $animal): void {
        echo "Logging animal\n";
    }
}
?>
```

---

## PHP 8+ Type Features

```php
<?php
class Base {
    public function process(Animal $animal): Dog {
        return new Dog();
    }
}

class Child extends Base {
    public function process(Dog $dog): Animal {
        return new Animal();
    }
}
?>
```

---

## Next Steps

→ Learn [DateTime](37-DateTime.md)  
→ Study [exceptions](38-exception.md)
