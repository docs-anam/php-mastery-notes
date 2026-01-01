# Reference

```php
/**
 * PHP References - In-Depth Summary
 *
 * 1. What is a Reference?
 *    - A reference in PHP means two variables point to the same memory location.
 *    - Changing one variable changes the other.
 *    - References are aliases, not pointers (unlike C/C++).
 *
 * 2. Creating References
 *    - Use the & (ampersand) operator to assign by reference.
 *      Example:
 *          $a = 5;
 *          $b = &$a; // $b references $a
 *          $b = 10;  // $a is now 10
 *
 * 3. Reference Assignment Rules
 *    - Only variables can be referenced, not function results or literals.
 *      Example:
 *          $x = 1;
 *          $y = &$x;
 *          $y++; // $x is now 2
 *
 * 4. Breaking References
 *    - Use unset() to break a reference.
 *      Example:
 *          unset($y); // $x still exists, $y is removed
 *
 * 5. References in Functions
 *    - Pass by reference:
 *          function addOne(&$num) { $num++; }
 *          $a = 5;
 *          addOne($a); // $a is now 6
 *    - Return by reference:
 *          function &getValue(&$arr) { return $arr['key']; }
 *
 * 6. References and Objects
 *    - Since PHP 5, objects are always assigned and passed by reference by default.
 *
 * 7. References in Arrays
 *    - You can store references in arrays:
 *          $a = 1;
 *          $arr = array(&$a);
 *          $arr[0]++; // $a is now 2
 *
 * 8. Use Cases
 *    - Efficient memory usage for large data structures.
 *    - Modifying variables inside functions.
 *    - Creating aliases for configuration or global variables.
 *
 * 9. Cautions
 *    - Overusing references can make code harder to understand and debug.
 *    - Use references only when necessary.
 *
 * 10. Example:
 */
$a = 10;
$b = &$a;
$b = 20;
echo $a; // Outputs 20

function increment(&$value) {
    $value++;
}
increment($a);
echo $a; // Outputs 21
```

