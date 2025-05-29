<?php
/**
 * PHP Generators in OOP - Detailed Summary
 *
 * 1. What is a Generator?
 *    - A generator is a special type of iterator introduced in PHP 5.5.
 *    - It allows you to iterate through a set of data without creating an array in memory.
 *    - Generators use the `yield` keyword to return values one at a time.
 *
 * 2. How Generators Work:
 *    - When a generator function is called, it returns an object that implements the Iterator interface.
 *    - Each time the generator is iterated (e.g., in a foreach loop), execution resumes from the last `yield`.
 *    - This allows for efficient memory usage, especially with large datasets.
 *
 * 3. Syntax Example:
 */
function numbers() {
    for ($i = 0; $i < 5; $i++) {
        yield $i;
    }
}
echo "Simple generator:\n";
foreach (numbers() as $num) {
    echo $num . "\n";
}

/**
 * 4. Generators in OOP:
 *    - Generators can be used as methods in classes.
 *    - Useful for implementing custom iterators or lazy-loading data.
 */
class RangeGenerator {
    private $start;
    private $end;
    public function __construct($start, $end) {
        $this->start = $start;
        $this->end = $end;
    }
    public function getRange() {
        for ($i = $this->start; $i <= $this->end; $i++) {
            yield $i;
        }
    }
}
echo "\nGenerator in OOP:\n";
$range = new RangeGenerator(1, 3);
foreach ($range->getRange() as $num) {
    echo $num . "\n";
}

/**
 * 5. Key Features:
 *    - `yield` can return key-value pairs: `yield $key => $value;`
 *    - Generators can accept values sent with `$gen->send($value);`
 *    - Can be used to implement coroutines (advanced usage).
 *
 * 6. Advantages:
 *    - Memory efficiency: No need to build large arrays.
 *    - Simpler code for iterators.
 *    - Useful for processing streams, files, or large datasets.
 *
 * 7. Limitations:
 *    - Generators are forward-only; you cannot rewind them.
 *    - Once a generator is closed, it cannot be reused.
 *
 * 8. When to Use:
 *    - When you need to iterate over large or infinite data sets.
 *    - When you want to implement custom iterators in OOP.
 */
?>