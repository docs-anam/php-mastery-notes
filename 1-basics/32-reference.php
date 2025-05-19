<?php
/**
 * PHP References - Detailed Summary
 *
 * 1. What is a Reference?
 *    - In PHP, a reference allows two variables to point to the same content.
 *    - Changing one variable will affect the other.
 *    - References are not pointers (like in C), but aliases.
 *
 * 2. Creating References
 *    - Use the & (ampersand) operator.
 *    - Example:
 *        $a = 5;
 *        $b = &$a; // $b is a reference to $a
 *        $b = 10;  // Now $a is also 10
 *
 * 3. Reference Assignment
 *    - Only variables can be referenced, not function results or literals.
 *    - Example:
 *        $x = 1;
 *        $y = &$x;
 *        $y++; // $x is now 2
 *
 * 4. Unsetting References
 *    - Use unset() to break the reference.
 *    - Example:
 *        unset($y); // $x still exists, but $y is removed
 *
 * 5. References in Functions
 *    - Pass by reference:
 *        function addOne(&$num) { $num++; }
 *        $a = 5;
 *        addOne($a); // $a is now 6
 *    - Returning by reference:
 *        function &getValue(&$arr) { return $arr['key']; }
 *
 * 6. References with Objects
 *    - Since PHP 5, objects are always passed by reference by default.
 *
 * 7. References in Arrays
 *    - You can store references in arrays:
 *        $a = 1;
 *        $arr = array(&$a);
 *        $arr[0]++; // $a is now 2
 *
 * 8. Use Cases
 *    - Efficient memory usage for large data.
 *    - Modifying variables inside functions.
 *    - Creating aliases for configuration or global variables.
 *
 * 9. Cautions
 *    - Overusing references can make code harder to read and debug.
 *    - Avoid using references unless necessary.
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
?>