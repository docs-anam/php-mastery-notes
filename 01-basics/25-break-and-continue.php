<?php
/**
 *
 * break and continue are control flow statements used within loops (for, foreach, while, do-while) and switch statements.
 *
 * break:
 * - Immediately terminates the execution of the current loop or switch statement.
 * - Control moves to the statement following the terminated loop or switch.
 * - Optionally, break can accept a numeric argument to break out of multiple nested loops.
 *
 * continue:
 * - Skips the rest of the current loop iteration and proceeds with the next iteration.
 * - Useful for skipping specific values or conditions within a loop.
 * - Optionally, continue can accept a numeric argument to skip to the next iteration of an outer loop in nested loops.
 *
 * Notes:
 * - break and continue help control loop execution flow for more flexible logic.
 * - Use them judiciously to avoid making code harder to read and maintain.
 */

// Break Example
echo "Break Example\n";
for ($i = 0; $i < 10; $i++) {
    if ($i == 5) {
        break; // Exits the loop when $i is 5
    }
    echo "Number: " . $i . "\n";
}

// Continue Example
echo "Continue Example\n";
for ($i = 0; $i < 10; $i++) {
    if ($i % 2 == 0) {
        continue; // Skips even numbers
    }
    echo "Odd Number: " . $i . "\n";
}
?>
