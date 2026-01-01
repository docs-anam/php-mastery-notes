# While Loop

```php
/*
A while loop in PHP is used to execute a block of code repeatedly as long as a specified condition is true.

Syntax:
while (condition) {
    // code to be executed
}

- The condition is evaluated before each iteration.
- If the condition is true, the code block runs.
- If the condition is false at the start, the loop does not execute at all.
- Be careful to update variables inside the loop to avoid infinite loops.

Example:
*/

$count = 1;
while ($count <= 5) {
    echo "Count is: $count\n";
    $count++; // Increment to eventually break the loop
}

/*
Output:
Count is: 1
Count is: 2
Count is: 3
Count is: 4
Count is: 5

Use Cases:
- Reading data until a condition is met
- Waiting for user input
- Processing items in a collection when the number of items is unknown

Tips:
- Always ensure the loop condition will eventually become false.
- Use break to exit the loop early if needed.
*/
```

