<?php
// Summary: The $this Keyword in PHP OOP

/*
 * In PHP Object-Oriented Programming (OOP), the $this keyword is used within a class to refer to the current object instance.
 * It allows access to the object's properties and methods from within the class's own methods.
 *
 * Key Points:
 * - $this is only available inside class methods.
 * - Use $this->property to access an object's property.
 * - Use $this->method() to call another method of the same object.
 *
 * Example:
 */

class Car {
    public $color;

    public function setColor($color) {
        $this->color = $color; // $this refers to the current object
    }

    public function getColor() {
        return $this->color;
    }
}

$myCar = new Car();
$myCar->setColor('red');
echo $myCar->getColor(); // Outputs: red

/*
 * In the example above, $this is used to set and get the color property of the Car object.
 */
?>