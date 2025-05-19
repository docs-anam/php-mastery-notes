<?php
/*
The 'for' loop in PHP is used to execute a block of code a specific number of times.
It consists of three parts: initialization, condition, and increment/decrement.

Syntax:
for (initialization; condition; increment) {
    // code to be executed
}

Example:
Print numbers from 1 to 5:
*/

for ($i = 1; $i <= 5; $i++) {
    echo $i . "\n";
}

/*
- Initialization: executed once at the beginning (e.g., $i = 1)
- Condition: evaluated before each iteration; loop continues if true (e.g., $i <= 5)
- Increment/Decrement: executed after each iteration (e.g., $i++)

The 'for' loop is ideal when the number of iterations is known.
*/
?>