# Operators Array

```php
// PHP Array Operators

/*
1. Union (+)
    - Combines two arrays.
    - If a key exists in both arrays, the value from the first array is used.
*/
$a = ["a" => "apple", "b" => "banana"];
$b = ["a" => "pear", "c" => "cherry"];
$result = $a + $b; // ["a" => "apple", "b" => "banana", "c" => "cherry"]

/*
2. Equality (==)
    - Returns true if both arrays have the same key/value pairs, regardless of order.
*/
$a = ["a" => "apple", "b" => "banana"];
$b = ["b" => "banana", "a" => "apple"];
$isEqual = ($a == $b); // true

/*
3. Identity (===)
    - Returns true if both arrays have the same key/value pairs in the same order and of the same types.
*/
$a = ["a" => "apple", "b" => "banana"];
$b = ["b" => "banana", "a" => "apple"];
$isIdentical = ($a === $b); // false

/*
4. Inequality (!= or <>)
    - Returns true if arrays are not equal.
*/
$a = ["a" => "apple"];
$b = ["a" => "pear"];
$isNotEqual = ($a != $b); // true

/*
5. Non-identity (!==)
    - Returns true if arrays are not identical.
*/
$a = ["a" => "apple", "b" => "banana"];
$b = ["b" => "banana", "a" => "apple"];
$isNotIdentical = ($a !== $b); // true

/*
Summary Table:
Operator   | Name         | Example         | Result
----------------------------------------------------------
+          | Union        | $a + $b         | Union of $a and $b
==         | Equality     | $a == $b        | true if equal
===        | Identity     | $a === $b       | true if identical
!= or <>   | Inequality   | $a != $b        | true if not equal
!==        | Non-identity | $a !== $b       | true if not identical
*/
```

