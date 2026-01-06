# Anonymous Classes

## Table of Contents
1. [Overview](#overview)
2. [Creating Anonymous Classes](#creating-anonymous-classes)
3. [Implementing Interfaces](#implementing-interfaces)
4. [Extending Classes](#extending-classes)
5. [Use Cases](#use-cases)
6. [Practical Examples](#practical-examples)

---

## Overview

Anonymous classes allow creating classes without naming them explicitly (PHP 7+).

---

## Creating Anonymous Classes

### Basic Syntax

```php
<?php
$object = new class {
    public $name = 'Anonymous';
    
    public function greet() {
        return "Hello from " . $this->name;
    }
};

echo $object->greet();
?>
```

### With Interfaces

```php
<?php
interface Logger {
    public function log(string $message): void;
}

$logger = new class implements Logger {
    public function log(string $message): void {
        echo "[LOG] $message\n";
    }
};

$logger->log('Test message');
?>
```

### Extending Classes

```php
<?php
class Base {
    protected function getMessage(): string {
        return "Base message";
    }
}

$object = new class extends Base {
    public function display(): void {
        echo $this->getMessage();
    }
};

$object->display();
?>
```

---

## Complete Example

```php
<?php
$service = new class {
    public function process(array $data): array {
        return array_map(function($item) {
            return strtoupper($item);
        }, $data);
    }
};

print_r($service->process(['hello', 'world']));
?>
```

---

## Next Steps

→ Learn [static keyword](28-static-keyword.md)  
→ Study [stdClass](29-stdClass.md)
