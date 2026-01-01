# Switch Statement

```php
/*
The switch statement is used to perform different actions based on different conditions.
It is an alternative to using multiple if...elseif...else statements when comparing the same variable to many values.

Syntax:
switch (expression) {
    case value1:
        // code to execute if expression == value1
        break;
    case value2:
        // code to execute if expression == value2
        break;
    ...
    default:
        // code to execute if expression does not match any case
}

- The expression is evaluated once.
- Its value is compared with each case value.
- If a match is found, the corresponding block is executed.
- The break statement prevents code from running into the next case.
- The default case is optional and runs if no match is found.
*/

// Example:
$color = "red";

switch ($color) {
    case "blue":
        echo "Color is blue";
        break;
    case "red":
        echo "Color is red";
        break;
    default:
        echo "Color is neither blue nor red";
}
```

