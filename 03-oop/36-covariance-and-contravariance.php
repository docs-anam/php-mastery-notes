<?php
/**
 * Covariance and Contravariance in PHP OOP (Detailed Explanation)
 *
 * Covariance and contravariance are advanced concepts in object-oriented programming
 * that relate to how method return types and parameter types can be specialized
 * in child classes when overriding parent methods.
 *
 * 1. Covariance (Return Types)
 *    - Covariance allows a child class to override a parent method and specify a more specific (derived) return type.
 *    - This means the return type in the child method can be a subclass of the return type declared in the parent.
 *    - Supported in PHP 7.4+.
 *
 *    Example:
 *      - Parent method returns a base class.
 *      - Child method returns a subclass (more specific).
 */

// Covariance Example
class Animal {
    public function speak() {
        return "Some generic animal sound";
    }
}

class Dog extends Animal {
    public function speak() {
        return "Woof!";
    }
}

class AnimalShelter {
    // Parent method returns Animal
    public function getAnimal(): Animal {
        return new Animal();
    }
}

class DogShelter extends AnimalShelter {
    // Child method returns Dog (subclass of Animal) - Covariant return type
    public function getAnimal(): Dog {
        return new Dog();
    }
}

// Test Covariance
$animalShelter = new AnimalShelter();
$animal = $animalShelter->getAnimal();
echo "AnimalShelter gives: " . $animal->speak() . PHP_EOL;

$dogShelter = new DogShelter();
$dog = $dogShelter->getAnimal();
echo "DogShelter gives: " . $dog->speak() . PHP_EOL;


/**
 * 2. Contravariance (Parameter Types)
 *    - Contravariance allows a child class to override a parent method and accept a more general (base) parameter type.
 *    - This means the parameter type in the child method can be a superclass of the parameter type declared in the parent.
 *    - Supported in PHP 7.4+.
 *
 *    Example:
 *      - Parent method accepts a subclass as parameter.
 *      - Child method accepts a superclass (more general).
 */

// Contravariance Example
class Food {
    public function getType() {
        return "Generic food";
    }
}

class DogFood extends Food {
    public function getType() {
        return "Dog food";
    }
}

class Animal2 {
    // Parent method accepts DogFood (specific)
    public function eat(DogFood $food) {
        echo "Animal eats: " . $food->getType() . PHP_EOL;
    }
}

class Dog2 extends Animal2 {
    // Child method accepts Food (more general) - Contravariant parameter type
    public function eat(Food $food) {
        echo "Dog eats: " . $food->getType() . PHP_EOL;
    }
}

// Test Contravariance
$animal2 = new Animal2();
$animal2->eat(new DogFood()); // OK
// $animal2->eat(new Food()); // Error: Argument must be of type DogFood

$dog2 = new Dog2();
$dog2->eat(new DogFood()); // OK
$dog2->eat(new Food());    // OK

/**
 * 3. Why Use Them?
 *    - Covariance and contravariance improve type safety and flexibility in OOP.
 *    - They allow for more precise type declarations in inheritance hierarchies.
 *    - Covariance: Enables returning more specific types in child classes.
 *    - Contravariance: Enables accepting more general types in child classes.
 *
 * 4. PHP Version Support
 *    - Both covariance and contravariance are supported starting from PHP 7.4.
 *
 * 5. Summary Table:
 *    | Concept        | Applies To   | Child Method Can Use         |
 *    |--------------- |-------------|------------------------------|
 *    | Covariance     | Return type  | More specific (subclass)     |
 *    | Contravariance | Parameter    | More general (superclass)    |
 */