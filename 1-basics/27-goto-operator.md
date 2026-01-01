# Goto Operator

```php
// Summary:
// The `goto` operator in PHP provides an unconditional jump to another section in the same file.
// It is used to jump to a label defined elsewhere in the code.
// Labels are declared using a name followed by a colon (e.g., myLabel:).
// `goto` can only jump within the same file and cannot jump into or out of loops or switch statements.

// Example:
echo "Start\n";

$count = 0;

repeat:
$count++;
echo "Count: $count\n";

if ($count < 3) {
    goto repeat; // Jumps back to the 'repeat' label
}

echo "End\n";

/*
Output:
Start
Count: 1
Count: 2
Count: 3
End
*/

// Note: Overusing `goto` can make code harder to read and maintain.
// It is generally recommended to use structured control flow (loops, functions) instead.
```

