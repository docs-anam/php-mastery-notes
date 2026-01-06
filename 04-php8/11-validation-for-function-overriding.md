# Validation for Function Overriding

## Overview

PHP 8 enforces stricter validation for function overriding in child classes, ensuring that overridden methods maintain compatible signatures with parent methods, preventing subtle bugs.

---

## Method Signature Rules

```php
<?php
class Parent {
    public function process(int $id): string {
        return "Parent";
    }
}

// ✅ Valid override - compatible signature
class Child extends Parent {
    public function process(int $id): string {
        return "Child";
    }
}

// ❌ Invalid - return type incompatible
class BadChild extends Parent {
    public function process(int $id): int { // Wrong return type!
        return 123;
    }
}
?>
```

---

## Parameter Type Covariance

```php
<?php
// Contravariance - child can accept more general types
class Payment {
    public function process(CreditCard $card): void {}
}

class CardPayment extends Payment {
    // ❌ Wrong - more specific type (contravariance violation)
    public function process(Visa $card): void {}
}

// ✅ Correct - same or more general type
class AnyCardPayment extends Payment {
    public function process(PaymentMethod $card): void {}
}
?>
```

---

## Return Type Covariance

```php
<?php
interface Animal {}
class Dog implements Animal {}
class Cat implements Animal {}

class AnimalFactory {
    public function create(): Animal {
        return new Dog();
    }
}

// ✅ Valid - Dog is more specific than Animal (covariance)
class DogFactory extends AnimalFactory {
    public function create(): Dog {
        return new Dog();
    }
}

// ❌ Invalid - Cat is not Dog
class CatFactory extends AnimalFactory {
    public function create(): Cat {
        return new Cat();
    }
}
?>
```

---

## Visibility Rules

```php
<?php
class Parent {
    protected function process(): void {}
    public function publicMethod(): void {}
}

class Child extends Parent {
    // ❌ Wrong - can't reduce visibility
    private function process(): void {}
    
    // ✅ Correct - can increase visibility
    public function process(): void {}
    
    // ❌ Wrong - can't change public to protected
    protected function publicMethod(): void {}
}
?>
```

---

## Nullability Rules

```php
<?php
class BaseService {
    public function fetch(int $id): ?string {
        return null;
    }
}

// ✅ Valid - returns exact same type
class ChildService extends BaseService {
    public function fetch(int $id): ?string {
        return "value";
    }
}

// ❌ Invalid - stricter return type
class StrictService extends BaseService {
    public function fetch(int $id): string { // Can't remove null
        return "value";
    }
}
?>
```

---

## Static Method Overriding

```php
<?php
class Parent {
    public static function configure(array $config): void {
        echo "Parent configured\n";
    }
}

// ✅ Valid - same signature
class Child extends Parent {
    public static function configure(array $config): void {
        echo "Child configured\n";
    }
}

// ❌ Invalid - signature mismatch
class BadChild extends Parent {
    public static function configure(string $config): void {
        echo "Bad configured\n";
    }
}
?>
```

---

## Constructor Override Validation

```php
<?php
class Base {
    public function __construct(string $name, int $age) {}
}

// ✅ Valid - must accept at least same parameters
class Child extends Base {
    public function __construct(string $name, int $age, string $email = "") {}
}

// ❌ Invalid - missing required parameter
class BadChild extends Base {
    public function __construct(string $name) {} // Missing $age
}
?>
```

---

## Interface Implementation Validation

```php
<?php
interface DataProcessor {
    public function process(array $data): array;
}

// ✅ Valid implementation
class JSONProcessor implements DataProcessor {
    public function process(array $data): array {
        return json_decode(json_encode($data), true);
    }
}

// ❌ Invalid - wrong return type
class CSVProcessor implements DataProcessor {
    public function process(array $data): string { // Should be array
        return implode(',', $data);
    }
}
?>
```

---

## Real-World Examples

### 1. Repository Pattern

```php
<?php
interface Repository {
    public function find(int $id): ?object;
    public function findAll(): array;
    public function save(object $entity): bool;
}

// ✅ Valid implementation
class UserRepository implements Repository {
    public function find(int $id): ?object {
        // Returns User or null
        return null;
    }
    
    public function findAll(): array {
        return [];
    }
    
    public function save(object $entity): bool {
        return true;
    }
}
?>
```

### 2. Handler Inheritance

```php
<?php
class EventHandler {
    public function handle(Event $event): bool {
        return true;
    }
}

// ✅ Valid - covariant return type
class UserEventHandler extends EventHandler {
    public function handle(Event $event): bool {
        if ($event instanceof UserEvent) {
            return $this->handleUser($event);
        }
        return false;
    }
    
    private function handleUser(UserEvent $event): bool {
        return true;
    }
}
?>
```

### 3. Factory Pattern

```php
<?php
abstract class Factory {
    abstract public function create(): Product;
}

// ✅ Valid - covariant return type
class ConcreteFactory extends Factory {
    public function create(): ConcreteProduct {
        return new ConcreteProduct();
    }
}

interface Product {}
class ConcreteProduct implements Product {}
?>
```

---

## Checking Signature Compatibility

```php
<?php
class SignatureValidator {
    public function validate(string $parentClass, string $methodName, string $childClass): array {
        $parentReflection = new ReflectionMethod($parentClass, $methodName);
        $childReflection = new ReflectionMethod($childClass, $methodName);
        
        $errors = [];
        
        // Check return type
        if ($parentReflection->getReturnType() !== $childReflection->getReturnType()) {
            $errors[] = "Return type mismatch";
        }
        
        // Check visibility
        if (!$this->compatibleVisibility($parentReflection, $childReflection)) {
            $errors[] = "Visibility incompatible";
        }
        
        // Check parameters
        $parentParams = $parentReflection->getParameters();
        $childParams = $childReflection->getParameters();
        
        if (count($parentParams) > count($childParams)) {
            $errors[] = "Missing required parameters";
        }
        
        return $errors;
    }
    
    private function compatibleVisibility(ReflectionMethod $parent, ReflectionMethod $child): bool {
        if ($parent->isPublic()) {
            return $child->isPublic();
        } elseif ($parent->isProtected()) {
            return $child->isProtected() || $child->isPublic();
        }
        return true;
    }
}
?>
```

---

## Best Practices

### 1. Use Interfaces for Contracts

```php
<?php
// ✅ Clear contract prevents override violations
interface Handler {
    public function handle(Request $request): Response;
}

class UserHandler implements Handler {
    public function handle(Request $request): Response {
        return new Response();
    }
}
?>
```

### 2. Document Override Rules

```php
<?php
/**
 * Base service class
 *
 * Child classes should:
 * - Maintain exact method signatures
 * - Return compatible types
 * - Never reduce visibility
 */
class BaseService {
    protected function execute(array $params): array {
        return [];
    }
}
?>
```

### 3. Use Type Hints

```php
<?php
// ✅ Type hints prevent override violations
class Parent {
    public function process(int $id, string $name): array {
        return [];
    }
}

class Child extends Parent {
    public function process(int $id, string $name): array {
        return ['id' => $id, 'name' => $name];
    }
}
?>
```

---

## Common Mistakes

### 1. Changing Return Type

```php
<?php
// ❌ Wrong - incompatible return type
class Parent {
    public function getValue(): int {
        return 42;
    }
}

class Child extends Parent {
    public function getValue(): string { // Error!
        return "42";
    }
}
?>
```

### 2. Reducing Visibility

```php
<?php
// ❌ Wrong - visibility reduced
class Parent {
    public function doSomething(): void {}
}

class Child extends Parent {
    private function doSomething(): void {} // Error!
}
?>
```

### 3. Adding Required Parameters

```php
<?php
// ❌ Wrong - added required parameter
class Parent {
    public function process(): void {}
}

class Child extends Parent {
    public function process(int $id): void {} // Error!
}

// ✅ Correct - add optional parameter
class GoodChild extends Parent {
    public function process(int $id = 0): void {}
}
?>
```

---

## Complete Example

```php
<?php
// Well-designed inheritance hierarchy

interface PaymentGateway {
    public function charge(float $amount, string $currency): PaymentResult;
    public function refund(string $transactionId, float $amount): PaymentResult;
}

class PaymentResult {
    public function __construct(
        public bool $success,
        public string $message,
        public ?string $transactionId = null
    ) {}
}

// ✅ Valid - maintains interface contract
class StripeGateway implements PaymentGateway {
    public function charge(float $amount, string $currency): PaymentResult {
        // Implementation
        return new PaymentResult(true, "Charged successfully", "txn_123");
    }
    
    public function refund(string $transactionId, float $amount): PaymentResult {
        // Implementation
        return new PaymentResult(true, "Refunded successfully");
    }
}

// ✅ Valid - covariant return type
class EnhancedStripeGateway extends StripeGateway {
    private array $metadata = [];
    
    public function charge(float $amount, string $currency): PaymentResult {
        $result = parent::charge($amount, $currency);
        $this->metadata['last_charge'] = $result->transactionId;
        return $result;
    }
}
?>
```

---

## See Also

- Documentation: [Inheritance](../03-oop/8-inheritance.md)
- Related: [Constructor Property Promotion](3-constructor-property-promotion.md), [Union Types](5-union-types.md)
