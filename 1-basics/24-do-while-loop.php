<?php
/*
A do-while loop in PHP is a control structure that executes a block of code at least once,
and then repeatedly executes the block as long as a specified condition is true.

Syntax:
do {
    // code to be executed
} while (condition);

Key Points:
- The code block inside the do-while loop always runs at least once, even if the condition is false initially.
- The condition is evaluated after the code block is executed.

Example:
*/

$count = 1;
do {
    echo "Count is: $count\n";
    $count++;
} while ($count <= 3);

// Output:
// Count is: 1
// Count is: 2
// Count is: 3
?>