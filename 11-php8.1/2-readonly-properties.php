<?php

/**
 * READONLY PROPERTIES IN PHP 8.1
 * 
 * Readonly properties were introduced in PHP 8.1 to create immutable properties
 * that can only be initialized once and cannot be modified afterward.
 * 
 * KEY FEATURES:
 * - Can only be written once (during object construction)
 * - Cannot be modified after initialization
 * - Must have a type declaration
 * - Can only be used on typed properties
 * - Cannot have a default value (except for promoted properties)
 * - Uninitialized readonly properties cannot be read
 * - Can be used with property promotion in constructors
 * 
 * BENEFITS:
 * - Provides immutability at the property level
 * - Prevents accidental modification of critical data
 * - Better data integrity and predictability
 * - Self-documenting code
 */

// Example 1: Basic readonly property
class User
{
    public readonly string $username;
    public readonly int $id;

    public function __construct(string $username, int $id)
    {
        $this->username = $username; // Can be set once
        $this->id = $id;
    }
}

$user = new User('john_doe', 123);
echo $user->username; // Works: john_doe
// $user->username = 'jane_doe'; // Fatal Error: Cannot modify readonly property

// Example 2: Constructor Property Promotion with readonly
class Product
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly string $sku
    ) {}
}

$product = new Product('Laptop', 999.99, 'LAP-001');
echo $product->name; // Works: Laptop
// $product->price = 799.99; // Fatal Error: Cannot modify readonly property

// Example 3: Readonly with different visibility levels
class BankAccount
{
    private readonly string $accountNumber;
    protected readonly float $balance;

    public function __construct(string $accountNumber, float $initialBalance)
    {
        $this->accountNumber = $accountNumber;
        $this->balance = $initialBalance;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }
}

// Example 4: Readonly properties in inheritance
class Animal
{
    public function __construct(
        public readonly string $species
    ) {}

    public function move(): void
    {
        // Base implementation
        echo "The animal is moving";
    }
}

class Dog extends Animal
{
    public function __construct(
        string $species,
        public readonly string $breed
    ) {
        parent::__construct($species);
    }

    public function move(): void
    {
        // Implementation of the move method
        echo "The dog is walking";
    }
}

$dog = new Dog('Canine', 'Golden Retriever');
echo $dog->species . ' - ' . $dog->breed;

// Example 5: Common use case - Value Objects
class Email
{
    public function __construct(
        public readonly string $address
    ) {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address');
        }
    }
}

$email = new Email('user@example.com');
// $email->address = 'hacker@evil.com'; // Fatal Error: Cannot modify

// Example 6: DateTime immutability pattern
class Timestamp
{
    public readonly DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }
}

$timestamp = new Timestamp();
echo $timestamp->createdAt->format('Y-m-d H:i:s');

/**
 * IMPORTANT NOTES:
 * 
 * 1. Readonly properties can ONLY be initialized once from the scope where they are declared
 * 2. Cannot be unset() after initialization
 * 3. Cannot clone and modify readonly properties
 * 4. Arrays and objects stored in readonly properties can still be modified internally
 *    (the reference is readonly, not the contents)
 * 5. Static properties cannot be readonly
 * 6. Uninitialized readonly properties will throw an Error when accessed
 */

// Example 7: Readonly with arrays (reference is readonly, not contents)
class Configuration
{
    public function __construct(
        public readonly array $settings
    ) {}
}

$config = new Configuration(['debug' => true]);
// $config->settings = ['debug' => false]; // Fatal Error
$config->settings['debug'] = false; // This WORKS - modifying array contents
echo $config->settings['debug']; // false

// To prevent this, use deep immutability patterns or readonly classes (PHP 8.2)

?>