<?php
// Arrays in PHP

// An array is a data structure that can hold multiple values under a single variable.
// There are three types of arrays in PHP:
// 1. Indexed arrays - Arrays with a numeric index
// 2. Associative arrays - Arrays with named keys
// 3. Multidimensional arrays - Arrays containing one or more arrays

// Example of an indexed array:
$fruits = array("Apple", "Banana", "Orange");

// Example of an associative array:
$person = array("name" => "John", "age" => 30, "city" => "New York");

// Example of a multidimensional array:
$contacts = array(
    array("name" => "Alice", "phone" => "123456"),
    array("name" => "Bob", "phone" => "654321")
);

// Accessing array elements:
echo $fruits[0]; // Output: Apple
echo $person["name"]; // Output: John
echo $contacts[1]["phone"]; // Output: 654321

// Adding elements to an array:
$fruits[] = "Grapes"; // Adds "Grapes" to the end of $fruits

// Modifying elements:
$person["age"] = 31; // Changes age to 31

// Removing elements:
unset($fruits[1]); // Removes "Banana" from $fruits

// Re-indexing an array:
$fruits = array_values($fruits); // Re-indexes $fruits after unset

// Common array functions in PHP:

// count() - Get the number of elements in an array
echo count($fruits); // Output: 3

// array_push() - Add one or more elements to the end of an array
array_push($fruits, "Mango");
print_r($fruits);

// array_pop() - Remove the last element of an array
array_pop($fruits);
print_r($fruits);

// array_unshift() - Add elements to the beginning of an array
array_unshift($fruits, "Pineapple");
print_r($fruits);

// array_shift() - Remove the first element of an array
array_shift($fruits);
print_r($fruits);

// array_keys() - Return all the keys of an array
$keys = array_keys($person);
print_r($keys);

// array_values() - Return all the values of an array
$values = array_values($person);
print_r($values);

// in_array() - Checks if a value exists in an array
if (in_array("Banana", $fruits)) {
    echo "Banana is in the fruits array.";
} else {
    echo "Banana is not in the fruits array.";
}

// array_search() - Search for a value and return its key/index
$index = array_search("Apple", $fruits);
echo $index; // Output: 0

// array_merge() - Merge two or more arrays
$more_fruits = array("Peach", "Plum");
$all_fruits = array_merge($fruits, $more_fruits);
print_r($all_fruits);

// array_slice() - Extract a portion of an array
$sliced = array_slice($all_fruits, 1, 2);
print_r($sliced);

// sort() - Sort an indexed array in ascending order
sort($fruits);
print_r($fruits);

// asort() - Sort an associative array by value
asort($person);
print_r($person);

// ksort() - Sort an associative array by key
ksort($person);
print_r($person);

// foreach - Loop through an array
foreach ($person as $key => $value) {
    echo "$key: $value\n";
}

// More useful functions: array_reverse(), array_unique(), array_filter(), array_map(), array_reduce(), etc.
?>