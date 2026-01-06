<?php

/**
 * ENUMERATIONS IN PHP 8.1
 * 
 * Enums (Enumerations) were introduced in PHP 8.1 as a way to define a type
 * that has a fixed set of possible values.
 * 
 * KEY FEATURES:
 * - Type-safe way to define a set of named values
 * - Can be "Pure" (Backed) or "Backed" (with scalar values)
 * - Support methods, static methods, and implement interfaces
 * - Enum cases are objects (instances of the enum)
 */

// ===== BASIC ENUM (PURE ENUM) =====
enum Status
{
    case Pending;
    case Approved;
    case Rejected;
}

// Usage
$orderStatus = Status::Pending;

// Type checking
function processOrder(Status $status): string
{
    return match($status) {
        Status::Pending => 'Order is pending',
        Status::Approved => 'Order approved',
        Status::Rejected => 'Order rejected',
    };
}

// ===== BACKED ENUM (with scalar values) =====
enum Priority: int
{
    case Low = 1;
    case Medium = 2;
    case High = 3;
    case Critical = 4;
}

// String-backed enum
enum Role: string
{
    case Admin = 'admin';
    case Editor = 'editor';
    case Viewer = 'viewer';
}

// Get the backing value
$priority = Priority::High;
echo $priority->value; // 3

// Create from backing value
$role = Role::from('admin'); // Returns Role::Admin
$roleOrNull = Role::tryFrom('invalid'); // Returns null if not found

// ===== ENUM WITH METHODS =====
enum Color: string
{
    case Red = '#FF0000';
    case Green = '#00FF00';
    case Blue = '#0000FF';

    // Instance methods
    public function getLabel(): string
    {
        return match($this) {
            self::Red => 'Red Color',
            self::Green => 'Green Color',
            self::Blue => 'Blue Color',
        };
    }

    // Static methods
    public static function random(): self
    {
        $cases = self::cases();
        return $cases[array_rand($cases)];
    }
}

$color = Color::Red;
echo $color->getLabel(); // "Red Color"
echo $color->value; // "#FF0000"

// ===== ENUM WITH INTERFACES =====
interface Colorable
{
    public function toRGB(): array;
}

enum BasicColor: string implements Colorable
{
    case White = 'white';
    case Black = 'black';

    public function toRGB(): array
    {
        return match($this) {
            self::White => [255, 255, 255],
            self::Black => [0, 0, 0],
        };
    }
}

// ===== ENUM BUILT-IN METHODS =====
// cases() - returns all enum cases
$allStatuses = Status::cases(); // [Status::Pending, Status::Approved, Status::Rejected]

// from() - creates enum from backing value (throws error if not found)
$role = Role::from('admin');

// tryFrom() - creates enum from backing value (returns null if not found)
$role = Role::tryFrom('manager'); // null

// name property - gets the case name
echo Status::Pending->name; // "Pending"

// ===== CONSTANTS IN ENUMS =====
enum HttpStatus: int
{
    case OK = 200;
    case NotFound = 404;
    case ServerError = 500;

    public const DEFAULT = self::OK;
}

// ===== PRACTICAL EXAMPLE =====
enum PaymentMethod: string
{
    case CreditCard = 'credit_card';
    case PayPal = 'paypal';
    case BankTransfer = 'bank_transfer';
    case Cash = 'cash';

    public function getFee(): float
    {
        return match($this) {
            self::CreditCard => 2.9,
            self::PayPal => 3.5,
            self::BankTransfer => 1.0,
            self::Cash => 0.0,
        };
    }

    public function isOnline(): bool
    {
        return match($this) {
            self::CreditCard, self::PayPal => true,
            self::BankTransfer, self::Cash => false,
        };
    }
}

$payment = PaymentMethod::CreditCard;
echo $payment->getFee(); // 2.9
echo $payment->isOnline() ? 'Online' : 'Offline'; // "Online"

/**
 * BENEFITS:
 * - Type safety and better IDE support
 * - No magic strings or constants scattered across code
 * - Works perfectly with match expressions
 * - Can contain business logic related to the enum values
 * - Cannot be extended (final by default)
 * 
 * LIMITATIONS:
 * - Cannot contain properties (only constants and methods)
 * - Cannot be instantiated with 'new'
 * - Cannot extend other classes
 * - Backed enums can only use int or string
 */

?>