<?php
/*
Summary: Getter and Setter in PHP OOP

In Object-Oriented Programming (OOP), properties of a class are often declared as private or protected to enforce encapsulation. To access or modify these properties from outside the class, we use special methods called getters and setters.

1. Getter:
    - A getter is a public method used to retrieve the value of a private/protected property.
    - Naming convention: getPropertyName()
    - Example:
          public function getName() {
                return $this->name;
          }

2. Setter:
    - A setter is a public method used to set or update the value of a private/protected property.
    - Naming convention: setPropertyName($value)
    - Example:
          public function setName($name) {
                $this->name = $name;
          }

3. Benefits:
    - Encapsulation: Direct access to properties is restricted.
    - Validation: Setters can include validation logic before assigning values.
    - Flexibility: Internal implementation can change without affecting external code.

4. Example:
*/

class Person {
    private $name;

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        if (is_string($name) && strlen($name) > 0) {
            $this->name = $name;
        }
    }
}

// Example usage:
$person = new Person();
$person->setName("Alice");
echo $person->getName(); // Outputs: Alice

/*
5. Magic Methods:
    - PHP provides __get() and __set() magic methods for dynamic property access, but explicit getters and setters are preferred for clarity and validation.

In summary, getters and setters are essential for controlling access to class properties, ensuring data integrity, and maintaining encapsulation in PHP OOP.
*/
?>