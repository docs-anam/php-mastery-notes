<?php
// Logical Operators in PHP

/*
Logical operators are used to combine conditional statements.
They return boolean values (true or false) based on the logic applied.

1. AND (&&, and)
    - Returns true if both operands are true.
    - Example:
*/
$a = true;
$b = false;
$result = $a && $b; // false

/*
2. OR (||, or)
    - Returns true if at least one operand is true.
    - Example:
*/
$result = $a || $b; // true

/*
3. NOT (!)
    - Returns true if the operand is false, and false if the operand is true.
    - Example:
*/
$result = !$a; // false

/*
4. XOR (xor)
    - Returns true if either operand is true, but not both.
    - Example:
*/
$result = $a xor $b; // true

/*
Operator Precedence:
- '&&' has higher precedence than 'and'
- '||' has higher precedence than 'or'
- Use parentheses to clarify complex expressions.

Examples:
*/
$x = true;
$y = false;
$z = true;

echo ($x && $y) ? 'true' : 'false'; // false
echo ($x || $y) ? 'true' : 'false'; // true
echo (!$y) ? 'true' : 'false';      // true
echo ($x xor $z) ? 'true' : 'false';// false

/*
Summary Table:
| Operator | Name    | Example   | Result      |
|----------|---------|-----------|-------------|
| &&       | And     | $a && $b  | true if both true |
| and      | And     | $a and $b | true if both true |
| ||       | Or      | $a || $b  | true if either true |
| or       | Or      | $a or $b  | true if either true |
| !        | Not     | !$a       | true if $a is false |
| xor      | Xor     | $a xor $b | true if only one is true |
*/
?>