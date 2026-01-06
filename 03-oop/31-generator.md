# Generators

## Overview

Generators provide a simple way to implement iterators using a function.

---

## Basic Generator

```php
<?php
function countTo($n) {
    for ($i = 1; $i <= $n; $i++) {
        yield $i;
    }
}

foreach (countTo(5) as $number) {
    echo $number . "\n";
}
?>
```

---

## Generator with Keys

```php
<?php
function generatePairs() {
    yield 'a' => 1;
    yield 'b' => 2;
    yield 'c' => 3;
}

foreach (generatePairs() as $key => $value) {
    echo "$key => $value\n";
}
?>
```

---

## Generator Send

```php
<?php
function echo Generator() {
    $value = yield 'First';
    echo "Received: $value\n";
    
    yield 'Second';
}

$gen = generator();
echo $gen->current() . "\n";
$gen->send('Sent Value');
?>
```

---

## Next Steps

→ Learn [object cloning](32-object-cloning.md)  
→ Study [comparing objects](33-comparing-object.md)
