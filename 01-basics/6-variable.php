<?php
// Variables in PHP

// 1. Declaration: Variables start with a $ sign, followed by the name.
$name = "Alice";
$age = 25;

// 2. Naming rules:
//    - Must start with a letter or underscore (_)
//    - Cannot start with a number
//    - Case-sensitive ($name and $Name are different)

// 3. Types: PHP is loosely typed. Variables can hold any type of value.
$number = 10;         // Integer
$price = 19.99;       // Float
$isActive = true;     // Boolean
$colors = ["red", "blue"]; // Array

// 4. Variable interpolation in strings:
echo "Hello, $name!"; // Outputs: Hello, Alice!

// 5. Variable variables:
//    - You can use the value of one variable as the name of another variable.
$foo = "bar";
$$foo = "baz"; // Creates $bar = "baz"
echo $bar;     // Outputs: baz

// 6. Unset a variable:
unset($age);

// 7. Check if variable is set:
if (isset($name)) {
    echo "$name is set";
}
?>