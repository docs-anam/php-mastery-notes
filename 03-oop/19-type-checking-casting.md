# Type Checking and Type Casting in PHP

## Table of Contents
1. [Overview](#overview)
2. [Type Checking Basics](#type-checking-basics)
3. [Type Casting](#type-casting)
4. [Instanceof Operator](#instanceof-operator)
5. [Type Declarations](#type-declarations)
6. [Type Validation](#type-validation)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

Type checking and casting allow you to verify and convert variable types in PHP. Type checking ensures data conforms to expected types, preventing runtime errors. Type casting converts a value from one type to another. Modern PHP (8+) emphasizes strict typing with scalar type declarations for safer, more maintainable code.

**Key Concepts:**
- Verify variable types before use
- Convert between compatible types
- Use strict type declarations in modern PHP
- Check object instance types with instanceof
- Type coercion vs explicit casting
- Safer code through type validation

---

## Type Checking Basics

### Built-in Type Functions

```php
<?php
// gettype() - returns type as string
echo gettype(42) . "\n";          // integer
echo gettype(3.14) . "\n";        // double
echo gettype("hello") . "\n";     // string
echo gettype([1, 2, 3]) . "\n";   // array
echo gettype(true) . "\n";        // boolean

// is_* functions - check specific types
$value = "123";
echo is_string($value) ? "String\n" : "";     // String
echo is_int($value) ? "Integer\n" : "";       // (nothing)
echo is_array([1, 2]) ? "Array\n" : "";       // Array
echo is_bool(true) ? "Boolean\n" : "";        // Boolean
echo is_numeric("123") ? "Numeric\n" : "";    // Numeric
echo is_object(new stdClass()) ? "Object\n" : "";  // Object

// Null checking
$var = null;
echo is_null($var) ? "Is null\n" : "";        // Is null
echo isset($var) ? "Isset\n" : "";            // (nothing)
echo empty($var) ? "Empty\n" : "";            // Empty
?>
```

### Type Checking Patterns

```php
<?php
// Safe type checking before operations
function processValue($value) {
    if (is_int($value)) {
        return $value * 2;
    } elseif (is_string($value)) {
        return strtoupper($value);
    } elseif (is_array($value)) {
        return count($value);
    } else {
        return null;
    }
}

echo processValue(5) . "\n";           // 10
echo processValue("hello") . "\n";     // HELLO
echo processValue([1, 2, 3]) . "\n";   // 3

// Array key existence checking
function getArrayValue($array, $key, $default = null) {
    return array_key_exists($key, $array) ? $array[$key] : $default;
}

$data = ['name' => 'John', 'age' => 30];
echo getArrayValue($data, 'name') . "\n";      // John
echo getArrayValue($data, 'email') . "\n";     // (empty)
echo getArrayValue($data, 'email', 'N/A') . "\n";  // N/A
?>
```

---

## Type Casting

### Explicit Type Casting

```php
<?php
// Cast to specific types
$value = "123";

// (int) or (integer)
$int = (int)$value;
echo $int . " - " . gettype($int) . "\n";  // 123 - integer

// (float) or (double)
$float = (float)$value;
echo $float . " - " . gettype($float) . "\n";  // 123 - double

// (string)
$string = (string)123;
echo $string . " - " . gettype($string) . "\n";  // 123 - string

// (bool) or (boolean)
$bool = (bool)"";  // Empty string casts to false
echo ($bool ? "true" : "false") . "\n";  // false

$bool = (bool)"0";  // String "0" casts to false
echo ($bool ? "true" : "false") . "\n";  // false

$bool = (bool)"anything";
echo ($bool ? "true" : "false") . "\n";  // true

// (array)
$array = (array)"hello";
print_r($array);  // Array ( [0] => hello )

$obj = new stdClass();
$obj->name = "John";
$array = (array)$obj;
print_r($array);  // Array ( [name] => John )

// (object)
$obj = (object)['name' => 'John', 'age' => 30];
echo $obj->name . "\n";  // John
?>
```

### Type Juggling

```php
<?php
// PHP automatically converts types (implicit casting)
$result = "10" + 5;      // String + int = 15 (int)
echo $result . "\n";     // 15

$result = "10.5" + 2.5;  // String + float = 13 (float)
echo $result . "\n";     // 13

$result = "hello" + 5;   // String + int = 5 (can't convert "hello")
echo $result . "\n";     // 5

// Comparison with loose vs strict equality
var_dump("10" == 10);    // bool(true) - loose comparison
var_dump("10" === 10);   // bool(false) - strict comparison

// Type coercion in if statements
if ("0") {
    echo "0 is true\n";
} else {
    echo "0 is false\n";  // This runs - "0" coerces to false
}

if ("false") {
    echo "string 'false' is true\n";  // This runs - non-empty string
}
?>
```

---

## Instanceof Operator

### Checking Object Type

```php
<?php
class Vehicle {}
class Car extends Vehicle {}
class Truck extends Vehicle {}

$car = new Car();
$truck = new Truck();
$vehicle = new Vehicle();

// Check if object is instance of class
echo ($car instanceof Car) ? "Is Car\n" : "";      // Is Car
echo ($car instanceof Vehicle) ? "Is Vehicle\n" : "";  // Is Vehicle
echo ($car instanceof Truck) ? "Is Truck\n" : "";      // (nothing)

// Polymorphic handling
function describeVehicle($vehicle) {
    if ($vehicle instanceof Car) {
        echo "This is a car\n";
    } elseif ($vehicle instanceof Truck) {
        echo "This is a truck\n";
    } elseif ($vehicle instanceof Vehicle) {
        echo "This is a generic vehicle\n";
    } else {
        echo "Unknown vehicle type\n";
    }
}

describeVehicle($car);
describeVehicle($truck);
describeVehicle($vehicle);
?>
```

### Interface and Trait Checking

```php
<?php
interface Serializable {
    public function serialize();
}

interface Comparable {
    public function compare($other);
}

class User implements Serializable, Comparable {
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function serialize() {
        return json_encode(['name' => $this->name]);
    }
    
    public function compare($other) {
        return strcmp($this->name, $other->name);
    }
}

$user = new User("John");

// Check interface implementation
echo ($user instanceof Serializable) ? "Implements Serializable\n" : "";   // Yes
echo ($user instanceof Comparable) ? "Implements Comparable\n" : "";       // Yes
echo ($user instanceof stdClass) ? "Is stdClass\n" : "";                  // No

// Check against multiple interfaces
function handleObject($obj) {
    if ($obj instanceof Serializable) {
        return $obj->serialize();
    }
    return null;
}

echo handleObject($user);  // {"name":"John"}
?>
```

---

## Type Declarations

### Function Parameter Type Hints

```php
<?php
// Declare parameter types - PHP 7+
function add(int $a, int $b): int {
    return $a + $b;
}

echo add(5, 3) . "\n";           // 8
echo add(5.7, 3.2) . "\n";       // 8 (floats cast to int)

// Nullable types
function greet(?string $name): string {
    $name = $name ?? "Guest";
    return "Hello, $name!";
}

echo greet("John") . "\n";       // Hello, John!
echo greet(null) . "\n";         // Hello, Guest!

// Union types (PHP 8.0+)
function processValue(int|string $value): string {
    if (is_int($value)) {
        return "Number: $value";
    }
    return "String: $value";
}

echo processValue(42) . "\n";         // Number: 42
echo processValue("hello") . "\n";    // String: hello

// Mixed type (accepts any type)
function handleAny(mixed $value): string {
    return "Received: " . gettype($value);
}

echo handleAny(123) . "\n";
echo handleAny("test") . "\n";
?>
```

### Class Property Types

```php
<?php
// Typed properties (PHP 7.4+)
class Product {
    private int $id;
    private string $name;
    private float $price;
    private ?string $description;  // Nullable
    private array $tags = [];
    
    public function __construct(int $id, string $name, float $price) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->description = null;
    }
    
    public function setDescription(?string $desc) {
        $this->description = $desc;
    }
    
    public function getInfo(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description
        ];
    }
}

$product = new Product(1, "Laptop", 999.99);
$product->setDescription("High-performance laptop");

print_r($product->getInfo());

// Type error (PHP would throw TypeError)
// $product->setDescription(123);  // Can't assign int to string
?>
```

### Return Type Declarations

```php
<?php
// Specify return type
function divide(float $a, float $b): float {
    if ($b == 0) {
        throw new Exception("Division by zero");
    }
    return $a / $b;
}

echo divide(10, 2) . "\n";  // 5

// Boolean return type
function isEven(int $n): bool {
    return $n % 2 === 0;
}

echo isEven(4) ? "Even\n" : "Odd\n";

// Array return type
function getUsers(): array {
    return [
        ['id' => 1, 'name' => 'John'],
        ['id' => 2, 'name' => 'Jane']
    ];
}

print_r(getUsers());

// Void return type
function logMessage(string $message): void {
    echo "[LOG] $message\n";
}

logMessage("Application started");  // Void function
?>
```

---

## Type Validation

### Custom Validation Functions

```php
<?php
// Validate email format
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

echo isValidEmail("john@example.com") ? "Valid\n" : "Invalid\n";    // Valid
echo isValidEmail("invalid-email") ? "Valid\n" : "Invalid\n";       // Invalid

// Validate URL format
function isValidUrl(string $url): bool {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

echo isValidUrl("https://example.com") ? "Valid\n" : "Invalid\n";   // Valid
echo isValidUrl("not a url") ? "Valid\n" : "Invalid\n";             // Invalid

// Validate IP address
function isValidIp(string $ip): bool {
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

echo isValidIp("192.168.1.1") ? "Valid\n" : "Invalid\n";    // Valid
echo isValidIp("999.999.999.999") ? "Valid\n" : "Invalid\n"; // Invalid

// Type and range validation
function isValidAge(int $age): bool {
    return $age >= 0 && $age <= 150;
}

function isValidPercentage(float $value): bool {
    return $value >= 0 && $value <= 100;
}
?>
```

### Data Validation Class

```php
<?php
class Validator {
    private $errors = [];
    
    public function validate(array $data, array $rules): bool {
        $this->errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            if (strpos($rule, 'required') !== false && empty($value)) {
                $this->errors[$field] = "$field is required";
                continue;
            }
            
            if (strpos($rule, 'email') !== false) {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field] = "$field must be valid email";
                }
            }
            
            if (strpos($rule, 'int') !== false) {
                if (!is_int($value) && !is_numeric($value)) {
                    $this->errors[$field] = "$field must be integer";
                }
            }
            
            if (strpos($rule, 'string') !== false) {
                if (!is_string($value)) {
                    $this->errors[$field] = "$field must be string";
                }
            }
        }
        
        return count($this->errors) === 0;
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
}

// Usage
$validator = new Validator();
$data = [
    'email' => 'john@example.com',
    'age' => 25,
    'name' => 'John'
];

$rules = [
    'email' => 'required|email',
    'age' => 'required|int',
    'name' => 'required|string'
];

if ($validator->validate($data, $rules)) {
    echo "Data is valid\n";
} else {
    print_r($validator->getErrors());
}
?>
```

---

## Practical Examples

### Type-Safe Data Converter

```php
<?php
class DataConverter {
    public static function toInt($value, $default = 0): int {
        if (is_int($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int)$value;
        }
        return $default;
    }
    
    public static function toString($value, $default = ""): string {
        if (is_string($value)) {
            return $value;
        }
        if (is_object($value) && method_exists($value, '__toString')) {
            return (string)$value;
        }
        if (is_array($value)) {
            return json_encode($value);
        }
        return (string)$value ?: $default;
    }
    
    public static function toArray($value, $default = []): array {
        if (is_array($value)) {
            return $value;
        }
        if (is_object($value)) {
            return (array)$value;
        }
        return $default;
    }
    
    public static function toBool($value, $default = false): bool {
        if (is_bool($value)) {
            return $value;
        }
        if (is_string($value)) {
            return in_array(strtolower($value), ['true', '1', 'yes', 'on']);
        }
        return (bool)$value;
    }
}

// Usage
echo DataConverter::toInt("42") . "\n";         // 42
echo DataConverter::toString(123) . "\n";       // 123
print_r(DataConverter::toArray(['a' => 1]));    // Array
echo DataConverter::toBool("yes") ? "true" : "false" . "\n";  // true
?>
```

---

## Common Mistakes

### 1. Not Using Type Hints

```php
<?php
// ❌ Wrong: No type hints - unclear what's expected
function calculate($a, $b) {
    return $a + $b;  // Works but loses type safety
}

// ✓ Correct: Use type hints
function calculate(int $a, int $b): int {
    return $a + $b;
}
?>
```

### 2. Ignoring Null Values

```php
<?php
// ❌ Wrong: Doesn't handle null
function getLength($value): int {
    return strlen($value);  // Error if value is null
}

// ✓ Correct: Handle null explicitly
function getLength(?string $value): int {
    if ($value === null) {
        return 0;
    }
    return strlen($value);
}
?>
```

### 3. Loose vs Strict Comparison

```php
<?php
// ❌ Wrong: Loose comparison causes bugs
if ($value == "0") {  // Also matches 0, false, etc.
    // Unexpected behavior
}

// ✓ Correct: Strict comparison
if ($value === "0") {  // Only matches string "0"
    // Predictable behavior
}
?>
```

---

## Complete Working Example

```php
<?php
// User Registration System

class UserValidator {
    private $errors = [];
    
    public function validate(array $data): bool {
        $this->errors = [];
        
        if (!$this->isValidEmail($data['email'] ?? null)) {
            $this->errors[] = "Invalid email format";
        }
        
        if (!$this->isValidPassword($data['password'] ?? null)) {
            $this->errors[] = "Password must be 8+ characters";
        }
        
        if (!$this->isValidName($data['name'] ?? null)) {
            $this->errors[] = "Invalid name";
        }
        
        return count($this->errors) === 0;
    }
    
    private function isValidEmail(?string $email): bool {
        return is_string($email) && 
               filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    private function isValidPassword(?string $password): bool {
        return is_string($password) && strlen($password) >= 8;
    }
    
    private function isValidName(?string $name): bool {
        return is_string($name) && strlen($name) >= 2;
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
}

class User {
    private int $id;
    private string $email;
    private string $name;
    private bool $verified;
    
    public function __construct(int $id, string $email, string $name) {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->verified = false;
    }
    
    public static function fromArray(array $data): ?User {
        if (!isset($data['id'], $data['email'], $data['name'])) {
            return null;
        }
        
        return new User(
            (int)$data['id'],
            (string)$data['email'],
            (string)$data['name']
        );
    }
    
    public function getInfo(): array {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'verified' => $this->verified
        ];
    }
}

class UserManager {
    private UserValidator $validator;
    private array $users = [];
    
    public function __construct() {
        $this->validator = new UserValidator();
    }
    
    public function register(array $data): ?User {
        if (!$this->validator->validate($data)) {
            echo "Validation errors:\n";
            foreach ($this->validator->getErrors() as $error) {
                echo "- $error\n";
            }
            return null;
        }
        
        $user = User::fromArray([
            'id' => count($this->users) + 1,
            'email' => $data['email'],
            'name' => $data['name']
        ]);
        
        if ($user instanceof User) {
            $this->users[] = $user;
            return $user;
        }
        
        return null;
    }
    
    public function getUser(int $id): ?User {
        foreach ($this->users as $user) {
            if ($user instanceof User) {
                $info = $user->getInfo();
                if ($info['id'] === $id) {
                    return $user;
                }
            }
        }
        return null;
    }
}

// Usage
$manager = new UserManager();

$result = $manager->register([
    'email' => 'john@example.com',
    'password' => 'securepass123',
    'name' => 'John Doe'
]);

if ($result instanceof User) {
    print_r($result->getInfo());
}
?>
```

---

## Cross-References

- **Related Topic: [Type Declarations](19-type-checking-casting.md)** - Modern PHP typing
- **Related Topic: [Polymorphism](18-polymorphism.md)** - Type-based behavior
- **Related Topic: [Instanceof Operator](18-polymorphism.md)** - Type checking
- **Related Topic: [Properties](4-properties.md)** - Typed properties
- **Related Topic: [Methods](5-function.md)** - Method type hints
