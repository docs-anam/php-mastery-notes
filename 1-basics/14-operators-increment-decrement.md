# Operators Increment Decrement

```php
// Increment and Decrement Operators in PHP

// Increment Operators
// ++$var (Pre-increment): Increments $var by one, then returns $var.
$a = 5;
echo ++$a; // Outputs 6

// $var++ (Post-increment): Returns $var, then increments $var by one.
$b = 5;
echo $b++; // Outputs 5
echo $b;   // Outputs 6

// Decrement Operators
// --$var (Pre-decrement): Decrements $var by one, then returns $var.
$c = 5;
echo --$c; // Outputs 4

// $var-- (Post-decrement): Returns $var, then decrements $var by one.
$d = 5;
echo $d--; // Outputs 5
echo $d;   // Outputs 4

/*
Summary:
- Pre-increment (++$var) and pre-decrement (--$var) change the variable before its value is used in an expression.
- Post-increment ($var++) and post-decrement ($var--) use the variable's value first, then change it.
- These operators only work with variables, not with values or expressions directly.
*/
```

