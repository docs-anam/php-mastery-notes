# Test Dependencies and Workflow Testing

## Overview

Test dependencies allow tests to share state and results, enabling workflow testing where multiple tests execute in sequence. This chapter covers when to use dependencies, how to implement them, and best practices for workflow testing.

---

## Table of Contents

1. Understanding Dependencies
2. Basic Dependencies
3. Multiple Dependencies
4. Optional Dependencies
5. When to Use Dependencies
6. When NOT to Use
7. Workflow Testing Patterns
8. Best Practices
9. Complete Examples

---

## Understanding Dependencies

### Concept

```
Test Dependency = One test depends on another test's result

Without Dependencies:
test1: Create user        [Independent]
test2: Update user        [Separate setup]
test3: Delete user        [Separate setup]
Problem: Repeated setup, database queries

With Dependencies:
test1: Create user        [Returns user]
test2: Update user        [Uses test1's user]
test3: Delete user        [Uses test2's user]
Benefit: Shared state, reduced setup
```

### Declaring Dependencies

```php
<?php

use PHPUnit\Framework\Attributes\Depends;

class WorkflowTest extends TestCase {
    
    public function testStep1(): object {
        // Create and return result
        return new stdClass();
    }
    
    #[Depends('testStep1')]
    public function testStep2($result) {
        // Receives result from testStep1
        $this->assertNotNull($result);
    }
}
```

---

## Basic Dependencies

### Simple Dependency

```php
<?php

class UserWorkflowTest extends TestCase {
    
    public function testUserCreation(): User {
        $user = User::create('John', 'john@example.com');
        
        $this->assertNotNull($user->id);
        return $user;
    }
    
    #[Depends('testUserCreation')]
    public function testUserFetch(User $user) {
        $fetched = User::find($user->id);
        
        $this->assertEquals($user->id, $fetched->id);
        $this->assertEquals('John', $fetched->name);
    }
}
```

### Execution Flow

```
1. testUserCreation()
   ↓ Creates user
   ↓ Returns user object
   
2. testUserFetch(user)
   ↓ Receives user from step 1
   ↓ Fetches from database
   ↓ Verifies data

Result:
If test1 passes → test2 runs with user data
If test1 fails → test2 skipped (no data)
```

---

## Multiple Dependencies

### Chaining Dependencies

```php
<?php

public function testStep1(): Order {
    $order = new Order();
    return $order;
}

#[Depends('testStep1')]
public function testStep2(Order $order): Order {
    $order->addItem('SKU123', 2);
    return $order;
}

#[Depends('testStep2')]
public function testStep3(Order $order) {
    $this->assertCount(1, $order->getItems());
}
```

### Receiving Multiple Results

```php
<?php

public function testCreateUser(): User {
    return User::create('John', 'john@example.com');
}

public function testCreateProduct(): Product {
    return Product::create('Widget', 19.99);
}

#[Depends('testCreateUser')]
#[Depends('testCreateProduct')]
public function testCreateOrder(User $user, Product $product): Order {
    $order = Order::create($user, $product);
    
    $this->assertNotNull($order->id);
    return $order;
}

#[Depends('testCreateOrder')]
public function testFulfillOrder(Order $order) {
    $order->fulfill();
    $this->assertTrue($order->isFulfilled());
}
```

---

## Optional Dependencies

### Soft Dependencies

```php
<?php

#[Depends('testSetup', false)]  // false = optional
public function testWithOptionalDependency($result) {
    // Runs even if testSetup fails
    // Receives null if testSetup failed
}

#[Depends('testSetup', true)]  // true = required (default)
public function testWithRequiredDependency($result) {
    // Skipped if testSetup fails
    // Only runs if testSetup passed
}
```

---

## When to Use Dependencies

### Good Use Cases

```
✓ User registration → login → account update
✓ Create order → add items → process
✓ Create database record → fetch → update → delete
✓ Setup expensive resource → use in multiple tests
✓ Sequential workflow validation

Benefits:
- Single setup for multiple tests
- Reduced database queries
- Tests real-world workflows
- Cleaner code (no repeated setup)
```

### Example: E-commerce Workflow

```php
<?php

public function testUserRegistration(): User {
    // Register user
    $user = User::register('john@example.com', 'password');
    $this->assertNotNull($user->id);
    return $user;
}

#[Depends('testUserRegistration')]
public function testUserLogin(User $user): AuthToken {
    // Login
    $token = Auth::login($user->email, 'password');
    $this->assertNotNull($token);
    return $token;
}

#[Depends('testUserLogin')]
public function testBrowsingProducts(AuthToken $token): array {
    // Browse products
    $products = Product::all();
    $this->assertCount(5, $products);
    return $products;
}

#[Depends('testUserRegistration')]
#[Depends('testBrowsingProducts')]
public function testAddToCart(User $user, array $products) {
    // Add product to cart
    $cart = Cart::create($user);
    $cart->addProduct($products[0]);
    $this->assertCount(1, $cart->getItems());
}
```

---

## When NOT to Use Dependencies

### Bad Use Cases

```
✗ Don't use for:
- Independent unit tests
- Tests that could fail independently
- Complex interdependencies
- Hard to debug failures
- When tests should be parallel

Example BAD:
#[Depends('testAddition')]
public function testSubtraction($result) {
    // No reason to depend on testAddition
    // Each test should be independent
}
```

### Unit Test Independence

```
Unit Tests SHOULD be:
- Independent
- Isolated
- No shared state
- Can run in any order
- Can run in parallel

Only use dependencies for:
- Integration tests
- Workflow tests
- Sequential operations
```

---

## Workflow Testing Patterns

### Pattern 1: Sequential State

```php
<?php

public function testCreateRecord(): Record {
    $record = Record::create(['name' => 'Test']);
    $this->assertNotNull($record->id);
    return $record;
}

#[Depends('testCreateRecord')]
public function testUpdateRecord(Record $record): Record {
    $record->name = 'Updated';
    $record->save();
    return $record;
}

#[Depends('testUpdateRecord')]
public function testDeleteRecord(Record $record) {
    $record->delete();
    $this->assertNull(Record::find($record->id));
}
```

### Pattern 2: Building Complex State

```php
<?php

public function testCreateCompany(): Company {
    return Company::create('ACME Corp');
}

#[Depends('testCreateCompany')]
public function testAddDepartment(Company $company): Department {
    $dept = $company->addDepartment('Engineering');
    return $dept;
}

#[Depends('testAddDepartment')]
public function testHireEmployee(Department $dept): Employee {
    $emp = $dept->hire('John', 'john@acme.com');
    return $emp;
}

#[Depends('testHireEmployee')]
public function testPromoteEmployee(Employee $emp) {
    $emp->promote('Senior Engineer');
    $this->assertEquals('Senior Engineer', $emp->title);
}
```

---

## Best Practices

### Practices

```
✓ DO:
- Use for integration tests
- Use for workflow testing
- Return meaningful objects
- Document dependencies
- Test complete workflows
- Keep chains short

✗ DON'T:
- Use for unit tests
- Make overly complex chains
- Ignore failed dependencies
- Duplicate test logic
- Use for parallelizable tests
- Make tests interdependent
```

### Naming Dependencies

```
Good naming:
#[Depends('testUserRegistration')]
#[Depends('testOrderCreation')]
#[Depends('testPaymentProcessing')]

These names are clear and self-documenting
```

### Handling Failures

```
If test1 fails:
- test2 (depends on test1) is SKIPPED
- Shows as "S" in output
- Not counted as failure
- Prevents cascading failures

Output:
...S..
3 pass, 1 skipped
```

---

## Complete Examples

### Example 1: User Management Workflow

```php
<?php

class UserWorkflowTest extends TestCase {
    
    public function testUserRegistration(): User {
        $user = User::register(
            'john@example.com',
            'password123'
        );
        
        $this->assertNotNull($user->id);
        return $user;
    }
    
    #[Depends('testUserRegistration')]
    public function testUserActivation(User $user): User {
        $user->activate();
        
        $this->assertTrue($user->isActive());
        return $user;
    }
    
    #[Depends('testUserActivation')]
    public function testUserProfileUpdate(User $user): User {
        $user->updateProfile([
            'name' => 'John Doe',
            'phone' => '555-1234',
        ]);
        
        $this->assertEquals('John Doe', $user->name);
        return $user;
    }
    
    #[Depends('testUserProfileUpdate')]
    public function testUserAccountDeletion(User $user) {
        $userId = $user->id;
        $user->delete();
        
        $this->assertNull(User::find($userId));
    }
}
```

### Example 2: Order Processing Workflow

```php
<?php

class OrderWorkflowTest extends TestCase {
    
    public function testOrderCreation(): Order {
        $order = Order::create();
        $this->assertNotNull($order->id);
        return $order;
    }
    
    #[Depends('testOrderCreation')]
    public function testAddOrderItems(Order $order): Order {
        $order->addItem('SKU001', 2, 29.99);
        $order->addItem('SKU002', 1, 49.99);
        
        $this->assertCount(2, $order->getItems());
        return $order;
    }
    
    #[Depends('testAddOrderItems')]
    public function testCalculateOrderTotal(Order $order): Order {
        $total = $order->calculateTotal();
        
        $this->assertEquals(109.97, $total);
        return $order;
    }
    
    #[Depends('testCalculateOrderTotal')]
    public function testProcessPayment(Order $order): Order {
        $payment = $order->processPayment();
        
        $this->assertTrue($payment->isApproved());
        return $order;
    }
    
    #[Depends('testProcessPayment')]
    public function testShipOrder(Order $order) {
        $order->ship();
        
        $this->assertEquals('shipped', $order->status);
    }
}
```

---

## Key Takeaways

**Dependencies Checklist:**

1. ✅ Use #[Depends] attribute to declare dependencies
2. ✅ Return objects from test to pass to next
3. ✅ Use for integration/workflow tests
4. ✅ Avoid for unit tests (keep independent)
5. ✅ Keep dependency chains short
6. ✅ Tests skip if dependency fails
7. ✅ Document expected workflow
8. ✅ Use meaningful test names
9. ✅ Consider parallel testing implications
10. ✅ Test complete user workflows

---

## See Also

- [Creating Unit Tests](2-create-unit-test.md)
- [Fixtures](9-fixture.md)
- [Attributes](4-attributes.md)
