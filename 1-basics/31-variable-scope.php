<?php
// PHP Variable Scope: Detailed Explanation with Examples

/*
Variable scope determines where a variable can be accessed in your code.
PHP has four main types of variable scope:

1. Local Scope
2. Global Scope
3. Static Scope
4. Function Parameter Scope
*/

// 1. Local Scope
// Variables declared inside a function are only accessible within that function.
function localScopeExample() {
    $localVar = "I'm local!";
    echo $localVar; // Output: I'm local!
}
localScopeExample();
// echo $localVar; // Error: Undefined variable (not accessible outside the function)

echo "\n";

// 2. Global Scope
// Variables declared outside any function are global.
// They are not accessible inside functions unless you use 'global' or $GLOBALS.
$globalVar = "I'm global!";

function globalScopeExample() {
    // echo $globalVar; // Undefined inside function
    global $globalVar; // Import global variable into local scope
    echo $globalVar; // Output: I'm global!
}
globalScopeExample();

echo "\n";

// Accessing global variables using $GLOBALS array
$anotherGlobal = 42;
function globalsArrayExample() {
    echo $GLOBALS['anotherGlobal']; // Output: 42
}
globalsArrayExample();

echo "\n";

// 3. Static Scope
// Static variables inside functions retain their value between function calls.
function staticScopeExample() {
    static $count = 0; // Initialized only once
    $count++;
    echo $count . " ";
}
staticScopeExample(); // Output: 1
staticScopeExample(); // Output: 2
staticScopeExample(); // Output: 3

echo "\n";

// 4. Function Parameter Scope
// Function parameters act as local variables within the function.
function parameterScopeExample($param) {
    echo $param;
}
parameterScopeExample("I'm a parameter!"); // Output: I'm a parameter!

echo "\n";

// Demonstrating variable shadowing
$shadowVar = "Global value";
function shadowingExample() {
    $shadowVar = "Local value";
    echo $shadowVar; // Output: Local value (local variable shadows global)
}
shadowingExample();
echo "\n";
echo $shadowVar; // Output: Global value

echo "\n";

// Using global keyword to modify global variable inside function
$counter = 0;
function incrementCounter() {
    global $counter;
    $counter++;
}
incrementCounter();
incrementCounter();
echo $counter; // Output: 2

/*
Summary:
- Local variables exist only within the function where they are declared.
- Global variables exist outside functions and require 'global' or $GLOBALS to be accessed inside functions.
- Static variables inside functions keep their value between calls, unlike regular local variables.
- Function parameters are local to the function and initialized with the argument value.
- Variable shadowing occurs when a local variable has the same name as a global variable.
*/
?>