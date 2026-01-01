# For Each Loop

```php
// Summary:
// The foreach loop in PHP is used to iterate over arrays. It provides an easy way to access each element in an array without using a counter variable. 
// There are two main syntaxes: one for getting only the values, and another for getting both keys and values.

// Example 1: Iterating over values
$colors = ["red", "green", "blue"];

echo "Colors:\n";
foreach ($colors as $color) {
    echo $color . "\n";
}

// Example 2: Iterating over keys and values (associative array)
$person = [
    "name" => "Alice",
    "age" => 30,
    "city" => "New York"
];

echo "\nPerson Details:\n";
foreach ($person as $key => $value) {
    echo ucfirst($key) . ": " . $value . "\n";
}

// Example 3: Modifying array values inside foreach (by reference)
$numbers = [1, 2, 3];
foreach ($numbers as &$num) {
    $num *= 2; // Double each number
}
unset($num); // Always unset reference variable after use

echo "\nDoubled Numbers:\n";
print_r($numbers);

/*
Output:
Colors:
red
green
blue

Person Details:
Name: Alice
Age: 30
City: New York

Doubled Numbers:
Array
(
    [0] => 2
    [1] => 4
    [2] => 6
)
*/
```

