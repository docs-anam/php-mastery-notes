<?php
/**
 * Summary: stdClass in PHP OOP
 *
 * - stdClass is PHP's generic empty class, often used to create simple objects.
 * - It is part of PHP's standard library and does not require any import.
 * - You can create an instance of stdClass using (object) casting or new stdClass().
 * - stdClass objects are useful for dynamic property assignment and as return types for functions.
 * - Properties can be added or removed at runtime.
 * - stdClass does not have any methods or predefined properties.
 * - It is commonly used when decoding JSON into objects (json_decode with $assoc = false).
 * - stdClass is not intended for complex OOP; for that, define your own classes.
 *
 * Example usage:
 */

$obj = new stdClass();
$obj->name = "Alice";
$obj->age = 30;

// Dynamic property assignment
$obj->city = "New York";

// Casting an array to object
$array = ['foo' => 'bar'];
$obj2 = (object) $array;

// Decoding JSON to stdClass
$json = '{"a":1,"b":2}';
$obj3 = json_decode($json);

// Convert stdClass object to array
$arrayFromObj = (array) $obj;
$arrayFromObj3 = (array) $obj3;

var_dump($obj, $obj2, $obj3, $arrayFromObj, $arrayFromObj3);