# Operators Comparison

```php
// Comparison Operators in PHP

/*
Comparison operators are used to compare two values. They return a boolean value: true or false.

Here are the main comparison operators in PHP:

1. Equal (==)
    - Checks if the values of two operands are equal, after type juggling.
    - Example:
*/
$a = 5;
$b = '5';
var_dump($a == $b); // true

/*
2. Identical (===)
    - Checks if the values and types of two operands are equal.
*/
var_dump($a === $b); // false

/*
3. Not Equal (!= or <>)
    - Checks if the values of two operands are not equal, after type juggling.
*/
var_dump($a != $b); // false
var_dump($a <> $b); // false

/*
4. Not Identical (!==)
    - Checks if the values or types are not equal.
*/
var_dump($a !== $b); // true

/*
5. Greater Than (>)
    - Checks if the left operand is greater than the right.
*/
var_dump($a > 3); // true

/*
6. Less Than (<)
    - Checks if the left operand is less than the right.
*/
var_dump($a < 10); // true

/*
7. Greater Than or Equal To (>=)
    - Checks if the left operand is greater than or equal to the right.
*/
var_dump($a >= 5); // true

/*
8. Less Than or Equal To (<=)
    - Checks if the left operand is less than or equal to the right.
*/
var_dump($a <= 5); // true

/*
9. Spaceship Operator (<=>)
    - Returns -1, 0, or 1 when $a is less than, equal to, or greater than $b.
    - Introduced in PHP 7.
*/
var_dump($a <=> 7); // int(-1)
var_dump($a <=> 5); // int(0)
var_dump($a <=> 3); // int(1)

/*
Summary:
- Use == and != for value comparison (with type juggling).
- Use === and !== for strict comparison (value and type).
- Use <, >, <=, >= for numeric or string comparisons.
- Use <=> for combined comparison (useful in sorting).
*/
```

