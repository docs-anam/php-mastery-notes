<?php
/**
 * Object Iteration in PHP OOP - Detailed Summary
 *
 * In PHP, objects can be iterated using foreach, but the behavior depends on how the class is defined.
 *
 * 1. Default Object Iteration:
 *    - By default, foreach iterates over all public properties of an object.
 *    - Private and protected properties are not accessible during iteration.
 */

// Example: Default Object Iteration
class User {
    public $name = "Alice";
    public $age = 30;
    private $password = "secret";
}
$user = new User();
echo "Default Object Iteration:\n";
foreach ($user as $key => $value) {
    echo "$key => $value\n"; // Iterates only $name and $age
}
echo "\n";

/**
 * 2. Custom Iteration with Iterator Interface:
 *    - To customize iteration, implement the Iterator or IteratorAggregate interface.
 *    - Iterator requires five methods: current(), key(), next(), rewind(), valid().
 *    - IteratorAggregate requires getIterator() which returns an object implementing Traversable.
 */

// Example: Iterator
class MyCollection implements Iterator {
    private $items = [];
    private $position = 0;

    public function __construct($items) {
        $this->items = $items;
    }
    public function current() { return $this->items[$this->position]; }
    public function key() { return $this->position; }
    public function next() { ++$this->position; }
    public function rewind() { $this->position = 0; }
    public function valid() { return isset($this->items[$this->position]); }
}
$collection = new MyCollection(['apple', 'banana', 'cherry']);
echo "Custom Iteration with Iterator:\n";
foreach ($collection as $key => $value) {
    echo "$key => $value\n";
}
echo "\n";

// Example: IteratorAggregate
class MyAggregate implements IteratorAggregate {
    private $items = [];
    public function __construct($items) { $this->items = $items; }
    public function getIterator() { return new ArrayIterator($this->items); }
}
$aggregate = new MyAggregate(['dog', 'cat', 'bird']);
echo "Custom Iteration with IteratorAggregate:\n";
foreach ($aggregate as $key => $value) {
    echo "$key => $value\n";
}
echo "\n";

/**
 * 3. Traversable Interface:
 *    - Traversable is a marker interface; you cannot implement it directly.
 *    - It's used internally to check if an object can be iterated.
 *
 * 4. Benefits:
 *    - Encapsulate iteration logic.
 *    - Control which data is exposed during iteration.
 *    - Useful for collections, data structures, and custom objects.
 *
 * 5. Tips:
 *    - Use IteratorAggregate for simple cases (delegates to another iterator).
 *    - Use Iterator for full control over iteration.
 *    - foreach works with any object implementing Traversable.
 */
?>