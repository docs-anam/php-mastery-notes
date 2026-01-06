# References in PHP

## Table of Contents
1. [Overview](#overview)
2. [Creating References](#creating-references)
3. [Pass by Reference](#pass-by-reference)
4. [Return by Reference](#return-by-reference)
5. [Practical Examples](#practical-examples)
6. [Common Mistakes](#common-mistakes)
7. [Advanced Topics](#advanced-topics)

---

## Overview

A reference is an alias to the same variable (same memory location).

**Key points:**
- Reference uses `&` symbol
- Both names point to same data
- Changing one changes the other
- NOT a pointer (like C)

---

## Creating References

### Basic Reference

```php
<?php
$original = 10;
$reference = &$original;  // Create reference

echo $original;   // 10
echo $reference;  // 10

// Modify via original
$original = 20;
echo $reference;  // 20 (automatically updated)

// Modify via reference
$reference = 30;
echo $original;   // 30 (automatically updated)
?>
```

### String Reference

```php
<?php
$name = "John";
$alias = &$name;

$alias = "Jane";
echo $name;  // Jane (both updated)

// They're the same variable
var_dump($name === $alias);  // true
var_dump($name);
var_dump($alias);  // Same value
?>
```

### Array Reference

```php
<?php
$array = [1, 2, 3];
$ref = &$array;

$ref[] = 4;
print_r($array);  // [1, 2, 3, 4]

// Both are same
$ref[0] = 10;
echo $array[0];  // 10
?>
```

### Unsetting References

```php
<?php
$var = "original";
$ref = &$var;

// Unset the reference
unset($ref);

echo $var;  // original (still exists)
// $ref is destroyed, but $var remains

// Unset original
unset($var);
// Both gone now
?>
```

---

## Pass by Reference

### Basic Function Reference

```php
<?php
function increment(&$value) {
    $value++;  // Modifies passed variable
}

$num = 5;
increment($num);
echo $num;  // 6 (modified)

// vs. normal parameter
function incrementNormal($value) {
    $value++;
}

$num = 5;
incrementNormal($num);
echo $num;  // 5 (unchanged)
?>
```

### Practical Example

```php
<?php
function processArray(&$array) {
    // Remove empty elements
    foreach ($array as $key => $value) {
        if (empty($value)) {
            unset($array[$key]);
        }
    }
}

$data = ['a' => 1, 'b' => 0, 'c' => 'text', 'd' => null];
processArray($data);

print_r($data);
// [a => 1, c => text]
?>
```

### Multiple Parameters

```php
<?php
function swap(&$a, &$b) {
    $temp = $a;
    $a = $b;
    $b = $temp;
}

$x = 5;
$y = 10;

swap($x, $y);

echo "$x, $y";  // 10, 5 (swapped)
?>
```

### Reference in Array

```php
<?php
$array = [];

function addItem(&$arr) {
    $arr['new'] = 'value';
}

addItem($array);
print_r($array);  // ['new' => 'value']

// Useful for building data structures
?>
```

---

## Return by Reference

### Returning Reference

```php
<?php
$value = 10;

function &getValue() {
    global $value;
    return $value;  // Return reference
}

$ref = &getValue();
$ref = 20;

echo $value;  // 20 (modified)
?>
```

### Returning Array Element Reference

```php
<?php
$array = ['name' => 'John', 'age' => 30];

function &getElement(&$arr, $key) {
    return $arr[$key];  // Return reference
}

$elem = &getElement($array, 'name');
$elem = 'Jane';

echo $array['name'];  // Jane (modified)
?>
```

### Object Property Reference

```php
<?php
class User {
    private $data = [];
    
    public function &getData() {
        return $this->data;  // Reference
    }
}

$user = new User();
$data = &$user->getData();
$data['name'] = 'John';

// Modified original object
?>
```

---

## Practical Examples

### Configuration Manager

```php
<?php
class Config {
    private static $config = [];
    
    public static function &get($key) {
        return self::$config[$key];  // Return reference
    }
    
    public static function set($key, $value) {
        self::$config[$key] = $value;
    }
}

Config::set('debug', false);

// Modify by reference
$debug = &Config::get('debug');
$debug = true;

// Check change
Config::set('debug', false);
$debug = &Config::get('debug');
echo $debug;  // true (was modified)
?>
```

### Database Record

```php
<?php
class Record {
    private $data = [];
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function &getField($field) {
        return $this->data[$field];  // Modify field directly
    }
}

$record = new Record(['name' => 'John', 'age' => 30]);

// Modify field directly via reference
$name = &$record->getField('name');
$name = 'Jane';

echo $record->getField('name');  // Jane
?>
```

### Iterator Pattern

```php
<?php
class Collection {
    private $items = [];
    
    public function add(&$item) {
        $this->items[] = &$item;  // Store reference
    }
    
    public function getItems() {
        return $this->items;
    }
}

$a = 1;
$b = 2;
$c = 3;

$collection = new Collection();
$collection->add($a);
$collection->add($b);
$collection->add($c);

// Modify original
$a = 10;
$b = 20;

// References in collection updated
foreach ($collection->getItems() as $item) {
    echo $item . " ";  // 10 20 3
}
?>
```

### Memory Efficiency

```php
<?php
// Without reference (copy)
$large_array = range(1, 1000000);

function processCopy($arr) {  // Creates copy
    // Memory heavy
}

// With reference (no copy)
function processRef(&$arr) {  // No copy
    // Memory efficient
    foreach ($arr as &$value) {
        $value = $value * 2;
    }
}

processRef($large_array);  // More efficient
?>
```

---

## Common Mistakes

### 1. Using References Unnecessarily

```php
<?php
// ❌ Bad: Reference not needed
function getPriceRef(&$item) {
    return $item['price'];  // Just reading
}

// ✓ Good: Only pass by reference if modifying
function getPrice($item) {
    return $item['price'];
}

function updatePrice(&$item, $newPrice) {
    $item['price'] = $newPrice;  // Modifying
}
?>
```

### 2. Forgetting Reference Creates Alias

```php
<?php
// ❌ Mistake: Thinking reference is separate
$original = 'John';
$ref = &$original;

$ref = 'Jane';

// Both changed (they're same variable!)
echo $original;  // Jane
echo $ref;       // Jane

// This isn't like copying
?>
```

### 3. Reference in Loop

```php
<?php
// ❌ Problem: Reference persists after loop
$array = [1, 2, 3];

foreach ($array as &$item) {
    $item = $item * 2;
}

$other = 10;
$item = &$array[1];  // Last item from loop still referenced
echo $array[1];  // Can be affected by other code

// ✓ Solution: Unset after loop
foreach ($array as &$item) {
    $item = $item * 2;
}
unset($item);  // Break reference
?>
```

### 4. Reference to Temporary Value

```php
<?php
// ❌ Dangerous: Reference to temporary
function &getTemp() {
    $temp = 'temporary';
    return $temp;  // Variable destroyed after function returns!
}

$ref = &getTemp();
// $ref points to destroyed variable (undefined behavior)

// ✓ Correct: Return reference to persistent data
function &getPersistent() {
    global $persistent;
    return $persistent;
}
?>
```

### 5. Modifying Array During Iteration

```php
<?php
// ❌ Problem: Modifying via reference in foreach
$array = ['a', 'b', 'c'];

foreach ($array as &$item) {
    $array[] = $item;  // Adding during iteration with reference
    // Can cause infinite loop or unexpected behavior
}

// ✓ Solution: Create new array
$array = ['a', 'b', 'c'];
$new = [];

foreach ($array as $item) {
    $new[] = $item;
    $new[] = $item;
}

$array = $new;
?>
```

---

## Advanced Topics

### References and Type Declarations

```php
<?php
declare(strict_types=1);

function processInt(int &$value): void {
    $value = $value * 2;
}

$num = 5;
processInt($num);
echo $num;  // 10 (type-safe reference)

// Type mismatch
$str = "hello";
processInt($str);  // TypeError: expects int
?>
```

### Reference to Object Property

```php
<?php
class User {
    public $name = 'John';
    public $age = 30;
}

$user = new User();

// Create reference to property
$name = &$user->name;
$name = 'Jane';

echo $user->name;  // Jane (modified)

// Note: Objects are already "reference-like"
$user2 = $user;  // Both point to same object
$user2->name = 'Bob';
echo $user->name;  // Bob (same object)
?>
```

### Reference in Global Scope

```php
<?php
$global = 10;

function test() {
    global $global;  // Reference to global
    global $global;  // Same as above
    
    $global = 20;
}

test();
echo $global;  // 20
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class DataStore {
    private $data = [];
    
    public function set(string $key, &$value): void {
        $this->data[$key] = &$value;  // Store reference
    }
    
    public function &get(string $key) {
        if (!isset($this->data[$key])) {
            $this->data[$key] = null;
        }
        return $this->data[$key];  // Return reference
    }
    
    public function increment(string $key): void {
        $ref = &$this->get($key);
        if (is_int($ref)) {
            $ref++;
        }
    }
}

// Usage
$store = new DataStore();

$counter = 0;
$store->set('counter', $counter);

$store->increment('counter');
$store->increment('counter');

echo $counter;  // 2 (modified via reference)

// Direct modification
$val = &$store->get('counter');
$val = 100;

echo $counter;  // 100
?>
```

---

## Summary

| Concept | Purpose | Use Case |
|---------|---------|----------|
| Reference | Create alias | Share data without copying |
| Pass by reference | Modify argument | Swap values, modify collections |
| Return by reference | Return mutable data | Lazy loading, proxies |
| Unset reference | Remove alias | Break reference chain |

---

## Next Steps

✅ Understand references  
→ Study [variable scope](31-variable-scope.md)  
→ Learn [functions](28-functions.md)  
→ Explore [OOP](../03-oop/)
