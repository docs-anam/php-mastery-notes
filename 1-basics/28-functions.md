# Functions

```php
// ===============================
// PHP Functions: Detailed Summary
// ===============================
//
// What is a Function?
// -------------------
// A function is a reusable block of code that performs a specific task.
// Functions help you:
//   - Organize code into logical sections
//   - Avoid code repetition (DRY principle)
//   - Improve readability and maintainability
//   - Encapsulate logic for reuse
//
// Types of Functions in PHP:
//   1. Built-in functions (e.g., strlen(), array_merge())
//   2. User-defined functions (your own functions)
//   3. Anonymous functions (closures)
//   4. Arrow functions (PHP 7.4+)
//   5. Recursive functions (functions that call themselves)
//
// Function Syntax:
// ----------------
// function functionName([type $param1 = default, ...]): returnType {
//     // function body
//     return $value; // optional
// }
//
// Naming Rules:
//   - Function names are case-insensitive
//   - Must start with a letter or underscore, followed by letters, numbers, or underscores
//
// ===============================
// Examples and Explanations
// ===============================

// 1. Simple function without parameters
function sayHello() {
    echo "Hello, World!";
}
sayHello(); // Output: Hello, World!

// 2. Function with parameters and return value
function add($a, $b) {
    return $a + $b;
}
$result = add(5, 3);
echo "5 + 3 = $result"; // Output: 5 + 3 = 8

// 3. Function with default parameter value
function greet($name = "Guest") {
    echo "Welcome, $name!";
}
greet("Alice"); // Output: Welcome, Alice!
greet();        // Output: Welcome, Guest!

// 4. Function with type declarations (PHP 7+)
function multiply(float $x, float $y): float {
    return $x * $y;
}
echo "2.5 * 4 = " . multiply(2.5, 4); // Output: 2.5 * 4 = 10

// 5. Function with variable number of arguments (variadic functions)
function sumAll(...$numbers) {
    return array_sum($numbers);
}
echo "Sum: " . sumAll(1, 2, 3, 4, 5); // Output: Sum: 15

// 6. Anonymous functions (Closures)
$square = function($n) {
    return $n * $n;
};
echo "Square of 6: " . $square(6); // Output: Square of 6: 36

// 7. Passing arguments by reference
function increment(&$value) {
    $value++;
}
$num = 10;
increment($num);
echo "Incremented value: $num"; // Output: Incremented value: 11

// 8. Recursive function (calls itself)
function factorial($n) {
    if ($n <= 1) return 1;
    return $n * factorial($n - 1);
}
echo "Factorial of 5: " . factorial(5); // Output: Factorial of 5: 120

// 9. Arrow functions (PHP 7.4+)
$double = fn($x) => $x * 2;
echo "Double of 7: " . $double(7); // Output: Double of 7: 14


// 10. Function with strict types (optional, PHP 7+)
function subtract(int $a, int $b): int {
    return $a - $b;
}
echo "10 - 3 = " . subtract(10, 3); // Output: 10 - 3 = 7

// 11. Function with multiple return values (using arrays)
function minMax($arr) {
    return [min($arr), max($arr)];
}
list($min, $max) = minMax([3, 7, 2, 9]);
echo "Min: $min, Max: $max"; // Output: Min: 2, Max: 9

// 12. Nested functions (function inside another function)
function outer() {
    function inner() {
        echo "Inner function called!";
    }
    inner();
}
outer(); // Output: Inner function called!

// 13. Using global variables inside functions
$globalVar = 5;
function useGlobal() {
    global $globalVar;
    $globalVar += 10;
}
echo "Global variable: $globalVar"; // Output: Global variable: 15

// 14. Static variables in functions
function counter() {
    static $count = 0;
    $count++;
    return $count;
}
echo "Counter: " . counter(); // Output: Counter: 1
echo "Counter: " . counter(); // Output: Counter: 2

// 15. Function existence check
if (function_exists('add')) {
    echo "Function 'add' exists!";
}

// 16. Function using call_user_func for callbacks
// Allows you to execute a callback function with arguments dynamically.
function runCallback($callback, ...$args) {
    return call_user_func($callback, ...$args);
}

// Example usage:
function greetUser($name) {
    return "Hello, $name!";
}
echo runCallback('greetUser', 'Bob'); // Output: Hello, Bob!
```

