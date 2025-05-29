<?php
// Summary: Properties in OOP PHP (Detailed, with Nullable Properties)

/**
 * In Object-Oriented Programming (OOP) with PHP, properties are variables that belong to a class.
 * They are used to store the state or data of an object.
 * 
 * Key points:
 * - Properties are declared inside a class using visibility keywords: public, protected, or private.
 * - Public properties can be accessed from anywhere.
 * - Protected properties can be accessed within the class and its subclasses.
 * - Private properties can only be accessed within the class itself.
 * - Properties can have default values, including null (nullable properties).
 * - Since PHP 7.4, you can declare property types (e.g., string, int, ?string for nullable).
 * - Access to properties can be controlled using getter and setter methods.
 * - Nullable properties allow a property to hold either a value of its type or null.
 */

class Example {
    // Typed properties with default values
    public string $publicProperty = "I am public";
    protected string $protectedProperty = "I am protected";
    private string $privateProperty = "I am private";

    // Nullable properties (can be string or null)
    public ?string $nullablePublic = null;
    protected ?int $nullableProtected = null;
    private ?float $nullablePrivate = null;

    // Getter for private property
    public function getPrivateProperty(): string {
        return $this->privateProperty;
    }

    // Setter for private property
    public function setPrivateProperty(string $value): void {
        $this->privateProperty = $value;
    }

    // Getter for nullable private property
    public function getNullablePrivate(): ?float {
        return $this->nullablePrivate;
    }

    // Setter for nullable private property
    public function setNullablePrivate(?float $value): void {
        $this->nullablePrivate = $value;
    }
}

$obj = new Example();
echo $obj->publicProperty . PHP_EOL; // Accessible
echo $obj->getPrivateProperty() . PHP_EOL; // Access private property via getter

// The following lines will cause errors due to visibility restrictions:
// echo $obj->protectedProperty . PHP_EOL; // Fatal error: Cannot access protected property
// echo $obj->privateProperty . PHP_EOL;   // Fatal error: Cannot access private property

// Nullable property usage
$obj->nullablePublic = "Now I have a value";
echo $obj->nullablePublic . PHP_EOL; // Outputs: Now I have a value

$obj->nullablePublic = null;
var_dump($obj->nullablePublic); // Outputs: NULL

// Access private property via getter/setter
echo $obj->getPrivateProperty() . PHP_EOL;
$obj->setPrivateProperty("Changed private value");
echo $obj->getPrivateProperty() . PHP_EOL;

// Access nullable private property via getter/setter
var_dump($obj->getNullablePrivate()); // NULL
$obj->setNullablePrivate(3.14);
var_dump($obj->getNullablePrivate()); // float(3.14)