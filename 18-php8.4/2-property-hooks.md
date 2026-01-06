# Property Hooks

## Overview

Property hooks are a revolutionary feature in PHP 8.4 that enable computed properties, automatic validation, and lazy initialization without boilerplate getter/setter methods.

---

## Table of Contents

1. Introduction to Property Hooks
2. Basic Syntax
3. Get Hooks
4. Set Hooks
5. Validation Patterns
6. Computed Properties
7. Lazy Initialization
8. Advanced Patterns
9. Best Practices
10. Complete Examples

---

## Introduction to Property Hooks

### What Are Property Hooks?

```php
<?php
// Property hooks allow you to intercept property access

class Temperature
{
    private float $celsius;

    // Hook: Automatically convert to Fahrenheit on read
    public float $fahrenheit {
        get => $this->celsius * 9/5 + 32;
    }

    public function __construct(float $celsius)
    {
        $this->celsius = $celsius;
    }
}

// Usage - looks like a property but is computed
$temp = new Temperature(25);
echo $temp->fahrenheit;  // 77 (computed, not stored)

// Benefits:
// ✓ No getter methods needed
// ✓ Syntax looks like property access
// ✓ Can perform computation
// ✓ Clean and readable
```

### Why Use Property Hooks?

```php
<?php
// Before PHP 8.4 (getter/setter pattern)
class OldStyle
{
    private float $value = 0;

    public function getValue(): float
    {
        return $this->value * 2;
    }

    public function setValue(float $v): void
    {
        $this->value = abs($v);  // Ensure positive
    }
}

$old = new OldStyle();
echo $old->getValue();    // Method call syntax
$old->setValue(50);       // Method call syntax

// After PHP 8.4 (property hooks)
class NewStyle
{
    public float $value {
        get => $this->value * 2;
        set => $this->value = abs($value);
    }
}

$new = new NewStyle();
echo $new->value;        // Property syntax - cleaner!
$new->value = 50;        // Property syntax - cleaner!
```

---

## Basic Syntax

### Property Hook Structure

```php
<?php
class PropertyHookBasics
{
    // Get hook only
    public string $name {
        get => strtoupper($this->name);
    }

    // Set hook only
    public int $count {
        set => $this->count = max(0, $value);
    }

    // Both get and set
    public float $price {
        get => $this->price;
        set => $this->price = max(0.0, $value);
    }
}

// Syntax rules:
// 1. Hooks are defined on public properties
// 2. Hooks use arrow notation =>
// 3. $value is automatic in set hook
// 4. Can access private backing field in hook
// 5. get/set can be in any order
```

### Backing Fields

```php
<?php
// Property hooks can have backing fields

class BackingFieldExample
{
    // Private backing field
    private string $name = '';

    // Public property with hooks backed by private field
    public string $name {
        get => strtoupper($this->name);
        set => $this->name = trim($value);
    }
}

// Or without explicit backing field (auto-backed)
class AutoBackedExample
{
    // PHP automatically creates backing field
    public string $email {
        get => strtolower($this->email);
        set => $this->email = $value;
    }
}
```

---

## Get Hooks

### Simple Get Hooks

```php
<?php
// Get hooks are called when reading property

class PropertyReading
{
    private float $celsius = 20;

    // Convert on read
    public float $fahrenheit {
        get => $this->celsius * 9/5 + 32;
    }

    // Format on read
    public string $temperature {
        get => round($this->celsius, 2) . '°C';
    }

    // Computed from multiple fields
    public string $info {
        get => "Celsius: $this->celsius, Fahrenheit: " . ($this->celsius * 9/5 + 32);
    }
}

// Usage
$prop = new PropertyReading();
echo $prop->fahrenheit;    // 68
echo $prop->temperature;   // 20°C
echo $prop->info;          // Celsius: 20, Fahrenheit: 68
```

### Get Hooks with Computation

```php
<?php
// Get hooks can contain logic

class UserAccount
{
    private array $transactions = [];
    private float $balance = 0;

    // Compute total from transactions
    public float $totalTransactions {
        get => array_sum(array_column($this->transactions, 'amount'));
    }

    // Check derived state
    public bool $isActive {
        get => !empty($this->transactions) && $this->balance > 0;
    }

    // Format for display
    public string $formattedBalance {
        get => '$' . number_format($this->balance, 2);
    }

    public function addTransaction(float $amount): void
    {
        $this->transactions[] = [
            'amount' => $amount,
            'date' => date('Y-m-d'),
        ];
        $this->balance += $amount;
    }
}

// Usage
$account = new UserAccount();
$account->addTransaction(100);
echo $account->totalTransactions;   // 100
echo $account->formattedBalance;    // $100.00
echo $account->isActive ? 'Active' : 'Inactive';  // Active
```

---

## Set Hooks

### Simple Set Hooks

```php
<?php
// Set hooks are called when writing property

class PropertyWriting
{
    public int $age {
        set => $this->age = max(0, min(150, $value));  // Constrain to 0-150
    }

    public string $username {
        set => $this->username = strtolower(trim($value));
    }

    public array $tags {
        set => $this->tags = array_unique(array_filter($value));
    }
}

// Usage
$prop = new PropertyWriting();
$prop->age = 200;           // Constrained to 150
echo $prop->age;            // 150

$prop->username = "  JOHN  ";
echo $prop->username;       // john

$prop->tags = ['php', 'php', 'web'];
print_r($prop->tags);       // ['php', 'web']
```

### Validation in Set Hooks

```php
<?php
// Set hooks can validate input

class ValidatedProperties
{
    public string $email {
        set => $this->email = $this->validateEmail($value);
    }

    public string $phone {
        set => $this->phone = $this->validatePhone($value);
    }

    public int $age {
        set {
            if ($value < 0 || $value > 150) {
                throw new InvalidArgumentException('Invalid age');
            }
            $this->age = $value;
        }
    }

    private function validateEmail(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email');
        }
        return strtolower($email);
    }

    private function validatePhone(string $phone): string
    {
        $clean = preg_replace('/\D/', '', $phone);
        if (strlen($clean) < 10) {
            throw new InvalidArgumentException('Invalid phone');
        }
        return $clean;
    }
}

// Usage
$props = new ValidatedProperties();
$props->email = "JOHN@EXAMPLE.COM";
echo $props->email;  // john@example.com

try {
    $props->email = "invalid";
} catch (InvalidArgumentException $e) {
    echo "Error: " . $e->getMessage();
}
```

---

## Computed Properties

### Full Get and Set Hooks

```php
<?php
// Both get and set for computed properties

class Rectangle
{
    private float $width = 1;
    private float $height = 1;

    // Area is computed from width and height
    public float $area {
        get => $this->width * $this->height;
        set {
            // Set area by adjusting width, keeping height
            $this->width = $value / $this->height;
        }
    }

    // Perimeter is computed
    public float $perimeter {
        get => 2 * ($this->width + $this->height);
    }

    // Diagonal is computed
    public float $diagonal {
        get => sqrt($this->width ** 2 + $this->height ** 2);
    }

    public function __construct(float $width, float $height)
    {
        $this->width = $width;
        $this->height = $height;
    }
}

// Usage
$rect = new Rectangle(10, 20);
echo $rect->area;       // 200
echo $rect->perimeter;  // 60
echo $rect->diagonal;   // ~22.36

$rect->area = 500;  // Adjust width to make area 500
echo $rect->width;      // 25 (500 / 20)
```

### Complex Computed Properties

```php
<?php
// Computed properties based on object state

class ShoppingCart
{
    private array $items = [];

    // Total price computed from items
    public float $total {
        get => array_reduce(
            $this->items,
            fn($carry, $item) => $carry + ($item['price'] * $item['quantity']),
            0
        );
    }

    // Item count computed
    public int $itemCount {
        get => array_sum(array_column($this->items, 'quantity', null));
    }

    // Average price per item
    public float $averagePrice {
        get => $this->itemCount > 0 ? $this->total / $this->itemCount : 0;
    }

    // Display summary
    public string $summary {
        get => sprintf(
            '%d items for $%.2f (avg: $%.2f)',
            $this->itemCount,
            $this->total,
            $this->averagePrice
        );
    }

    public function addItem(string $name, float $price, int $quantity = 1): void
    {
        $this->items[] = compact('name', 'price', 'quantity');
    }
}

// Usage
$cart = new ShoppingCart();
$cart->addItem('Book', 15.00, 2);
$cart->addItem('Pen', 2.50, 5);
echo $cart->summary;  // 7 items for $37.50 (avg: $5.36)
```

---

## Lazy Initialization

### Load-on-Demand Properties

```php
<?php
// Use get hook for lazy loading

class UserProfile
{
    private int $userId;
    private ?array $profileData = null;

    // Lazily load profile data on first access
    public array $profile {
        get {
            if ($this->profileData === null) {
                $this->profileData = $this->loadProfileData();
            }
            return $this->profileData;
        }
    }

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    private function loadProfileData(): array
    {
        // Simulate database query
        return [
            'id' => $this->userId,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'preferences' => [],
        ];
    }
}

// Usage
$user = new UserProfile(1);
// Profile not loaded yet

// First access loads data
$profile = $user->profile;  // Queries database

// Second access uses cache
$name = $user->profile['name'];  // No query
```

### Cache with Expiration

```php
<?php
// Lazy loading with expiration

class CachedProperty
{
    private ?array $data = null;
    private ?int $cacheTime = null;
    private int $cacheDuration = 3600;

    public array $cachedData {
        get {
            $now = time();

            if ($this->data === null ||
                ($this->cacheTime !== null && $now - $this->cacheTime > $this->cacheDuration))
            {
                $this->data = $this->fetchData();
                $this->cacheTime = $now;
            }

            return $this->data;
        }
    }

    private function fetchData(): array
    {
        // Expensive operation
        return [];
    }

    public function clearCache(): void
    {
        $this->data = null;
        $this->cacheTime = null;
    }
}
```

---

## Advanced Patterns

### Property Chains

```php
<?php
// Chain multiple properties

class ChainedProperties
{
    private string $firstName = '';
    private string $lastName = '';
    private string $emailLocal = '';

    // Full name computed from first and last
    public string $fullName {
        get => trim($this->firstName . ' ' . $this->lastName);
        set {
            $parts = explode(' ', $value, 2);
            $this->firstName = $parts[0] ?? '';
            $this->lastName = $parts[1] ?? '';
        }
    }

    // Email computed from full name
    public string $email {
        get => strtolower(str_replace(' ', '.', $this->fullName)) . '@' . $this->emailLocal;
        set {
            $parts = explode('@', $value, 2);
            $this->firstName = '';
            $this->lastName = '';
            $this->emailLocal = $parts[1] ?? '';
        }
    }
}

// Usage
$user = new ChainedProperties();
$user->fullName = 'John Doe';
echo $user->email;  // john.doe@
```

### State-Based Properties

```php
<?php
// Properties that behave differently based on state

class StatefulEntity
{
    private string $status = 'draft';
    private array $data = [];

    public string $status {
        get => $this->status;
        set {
            if (!in_array($value, ['draft', 'published', 'archived'])) {
                throw new InvalidArgumentException('Invalid status');
            }
            $this->status = $value;
        }
    }

    // Behavior depends on status
    public string $displayContent {
        get => match($this->status) {
            'draft' => '[DRAFT] ' . $this->data['title'] ?? 'Untitled',
            'published' => $this->data['title'] ?? 'Untitled',
            'archived' => '[ARCHIVED] ' . $this->data['title'] ?? 'Untitled',
        };
    }

    // Can only modify if in draft
    public array $content {
        get => $this->data;
        set {
            if ($this->status !== 'draft') {
                throw new RuntimeException('Cannot modify non-draft content');
            }
            $this->data = $value;
        }
    }
}
```

---

## Best Practices

### Property Hook Guidelines

```php
<?php
// ✓ DO: Use hooks for simple transformations
class GoodExample1
{
    public string $email {
        set => $this->email = strtolower(trim($value));
    }
}

// ✓ DO: Use hooks for computed properties
class GoodExample2
{
    private float $width = 1;
    private float $height = 1;

    public float $area {
        get => $this->width * $this->height;
    }
}

// ✗ DON'T: Put complex logic in hooks
class BadExample1
{
    public string $name {
        set {
            // Too much logic!
            if (strlen($value) < 3) {
                throw new Exception('Name too short');
            }
            // ... 20 more lines ...
            $this->name = $value;
        }
    }
}

// ✗ DON'T: Use hooks for side effects
class BadExample2
{
    public string $value {
        set {
            // Don't do this!
            // $this->saveToDatabase($value);
            // $this->sendNotification($value);
            $this->value = $value;
        }
    }
}

// ✓ DO: Separate complex logic into methods
class GoodExample3
{
    public string $name {
        set => $this->name = $this->validateAndProcessName($value);
    }

    private function validateAndProcessName(string $value): string
    {
        if (strlen($value) < 3) {
            throw new Exception('Name too short');
        }
        return ucfirst(strtolower(trim($value)));
    }
}
```

---

## Complete Examples

### Full User Class with Hooks

```php
<?php
declare(strict_types=1);

namespace App;

class User
{
    private string $firstName = '';
    private string $lastName = '';
    private string $email = '';
    private int $age = 0;
    private array $roles = [];

    // Computed full name with get/set
    public string $fullName {
        get => trim($this->firstName . ' ' . $this->lastName);
        set {
            $parts = explode(' ', $value, 2);
            $this->firstName = trim($parts[0] ?? '');
            $this->lastName = trim($parts[1] ?? '');
        }
    }

    // Validated email
    public string $email {
        get => $this->email;
        set => $this->email = $this->validateEmail($value);
    }

    // Constrained age
    public int $age {
        get => $this->age;
        set => $this->age = max(0, min(150, $value));
    }

    // Display name (computed)
    public string $displayName {
        get => !empty($this->firstName) ? $this->firstName : $this->email;
    }

    // Admin check
    public bool $isAdmin {
        get => in_array('admin', $this->roles);
    }

    // Role count
    public int $roleCount {
        get => count($this->roles);
    }

    public function addRole(string $role): void
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    private function validateEmail(string $email): string
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email');
        }
        return $email;
    }

    public function toArray(): array
    {
        return [
            'fullName' => $this->fullName,
            'email' => $this->email,
            'age' => $this->age,
            'displayName' => $this->displayName,
            'isAdmin' => $this->isAdmin,
            'roleCount' => $this->roleCount,
        ];
    }
}

// Usage
$user = new User();
$user->fullName = 'John Doe';
$user->email = 'JOHN@EXAMPLE.COM';
$user->age = 25;
$user->addRole('admin');

echo $user->displayName;  // John
echo $user->roleCount;    // 1
echo $user->isAdmin ? 'Admin' : 'User';  // Admin

print_r($user->toArray());
```

---

## See Also

- [PHP 8.4 Overview](0-php8.4-overview.md)
- [Asymmetric Visibility](3-asymmetric-visibility.md)
- [Class Constant Visibility](4-class-constant-visibility.md)
- [Type System Improvements](5-type-system.md)
