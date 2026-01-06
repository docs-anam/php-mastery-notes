<?php
/*
 * Constructor Property Promotion in PHP 8.0
 *
 * Introduced in PHP 8.0, Constructor Property Promotion is a syntax improvement
 * that allows you to declare and initialize class properties directly in the constructor
 * parameter list, reducing boilerplate code and making classes more concise.
 *
 * How it worked before PHP 8.0:
 * -----------------------------
 * class User {
 *     public string $name;
 *     public int $age;
 *
 *     public function __construct(string $name, int $age) {
 *         $this->name = $name;
 *         $this->age = $age;
 *     }
 * }
 *
 * With Constructor Property Promotion (PHP 8.0+):
 * -----------------------------------------------
 * class User {
 *     public function __construct(
 *         public string $name,
 *         public int $age
 *     ) {}
 * }
 *
 * Key Points:
 * -----------
 * 1. Visibility (public, protected, private) is required for promoted properties.
 *    Example: public string $name
 * 2. Type declarations are supported and recommended.
 *    Example: public int $age
 * 3. Default values can be assigned.
 *    Example: public float $price = 0.0
 * 4. Promoted properties are automatically declared and initialized.
 *    No need to declare them separately or assign in the constructor body.
 * 5. Reduces code duplication and improves readability.
 *
 * Example with default value:
 * ---------------------------
 * class Product {
 *     public function __construct(
 *         public string $name,
 *         public float $price = 0.0
 *     ) {}
 * }
 *
 * Limitations:
 * ------------
 * - Only works in constructors (__construct).
 * - Cannot be used for static properties.
 * - Cannot use by-reference (&) parameters.
 * - Cannot use variadic parameters (...$args) as promoted properties.
 *
 * Benefits:
 * ---------
 * - Less boilerplate code.
 * - Cleaner, more maintainable classes.
 * - Encourages use of type declarations and visibility.
 *
 * Additional Details:
 * -------------------
 * - Promoted properties are available as class properties, just like manually declared ones.
 * - You can mix promoted and non-promoted properties in the same constructor.
 * - Doc comments can be added above the constructor for documentation.
 */

// Executable Example 1: Basic Usage
class User {
    public function __construct(
        public string $name,
        public int $age
    ) {}
}

$user = new User("Alice", 30);
echo "User: {$user->name}, Age: {$user->age}\n";

// Executable Example 2: With Default Value
class Product {
    public function __construct(
        public string $name,
        public float $price = 0.0
    ) {}
}

$product = new Product("Book");
echo "Product: {$product->name}, Price: {$product->price}\n";

// Executable Example 3: Mixing Promoted and Non-Promoted Properties
class Order {
    public string $status;

    public function __construct(
        public int $id,
        public float $total
    ) {
        $this->status = "pending";
    }
}

$order = new Order(101, 250.75);
echo "Order ID: {$order->id}, Total: {$order->total}, Status: {$order->status}\n";
?>