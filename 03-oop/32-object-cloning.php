<?php
/**
 * Object Cloning in PHP OOP - Detailed Summary
 *
 * 1. What is Object Cloning?
 *    - Object cloning is the process of creating a copy of an existing object.
 *    - In PHP, this is done using the `clone` keyword.
 *
 * 2. Shallow Copy vs Deep Copy:
 *    - By default, PHP performs a shallow copy: object properties are copied as-is.
 *    - If a property is an object, only the reference is copied (not the actual object).
 *    - Deep copy requires custom logic to clone nested objects.
 *
 * 3. The __clone() Magic Method:
 *    - When an object is cloned, PHP will call its `__clone()` method (if defined).
 *    - Use `__clone()` to modify properties or perform deep copying.
 *
 * 4. Example:
 */

class Address {
    public $city;
    function __construct($city) {
        $this->city = $city;
    }
}

class Person {
    public $name;
    public $address;
    function __construct($name, Address $address) {
        $this->name = $name;
        $this->address = $address;
    }
    // Custom clone to deep copy the address
    public function __clone() {
        $this->address = clone $this->address;
    }
}

$address1 = new Address("New York");
$person1 = new Person("Alice", $address1);

// Shallow copy (default behavior)
$person2 = clone $person1;
$person2->name = "Bob";
$person2->address->city = "Los Angeles";

echo $person1->name . " lives in " . $person1->address->city . PHP_EOL; // Alice lives in New York
echo $person2->name . " lives in " . $person2->address->city . PHP_EOL; // Bob lives in Los Angeles

/**
 * 5. Key Points:
 *    - Use `clone` to copy objects.
 *    - Implement `__clone()` for custom or deep copy logic.
 *    - Be careful with objects containing other objects (nested objects).
 *    - Cloning does not call the constructor.
 */
?>