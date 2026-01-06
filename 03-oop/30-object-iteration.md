# Object Iteration

## Overview

Iterate over object properties using foreach and other iteration techniques.

---

## Iterating Properties

```php
<?php
$obj = new stdClass();
$obj->name = 'John';
$obj->age = 30;
$obj->email = 'john@example.com';

foreach ($obj as $key => $value) {
    echo "$key: $value\n";
}
?>
```

---

## Iterator Interface

```php
<?php
class MyIterator implements Iterator {
    private $position = 0;
    private $items = ['a', 'b', 'c'];
    
    public function rewind(): void {
        $this->position = 0;
    }
    
    public function current(): mixed {
        return $this->items[$this->position];
    }
    
    public function key(): mixed {
        return $this->position;
    }
    
    public function next(): void {
        ++$this->position;
    }
    
    public function valid(): bool {
        return isset($this->items[$this->position]);
    }
}

$iterator = new MyIterator();
foreach ($iterator as $key => $value) {
    echo "$key => $value\n";
}
?>
```

---

## IteratorAggregate

```php
<?php
class Collection implements IteratorAggregate {
    private $items = ['item1', 'item2', 'item3'];
    
    public function getIterator(): ArrayIterator {
        return new ArrayIterator($this->items);
    }
}

$collection = new Collection();
foreach ($collection as $item) {
    echo $item . "\n";
}
?>
```

---

## Next Steps

→ Learn [generators](31-generator.md)  
→ Study [object cloning](32-object-cloning.md)
