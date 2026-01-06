# Advanced Testing Patterns and Best Practices

## Overview

Advanced patterns for testing complex scenarios, organizing large test suites, and maintaining test quality at scale.

---

## Table of Contents

1. Testing Patterns
2. Property-Based Testing
3. Snapshot Testing
4. Builder Pattern in Tests
5. Test Factories
6. Common Pitfalls
7. Advanced Scenarios
8. Complete Examples

---

## Testing Patterns

### Arrange-Act-Assert (AAA)

```php
<?php

public function testUserCreation() {
    // ARRANGE: Set up test data
    $name = 'John Doe';
    $email = 'john@example.com';
    $repository = new InMemoryUserRepository();
    
    // ACT: Execute code under test
    $user = new User($name, $email);
    $repository->save($user);
    
    // ASSERT: Verify results
    $saved = $repository->findByEmail($email);
    $this->assertEquals($name, $saved->getName());
}
```

### Given-When-Then (BDD)

```php
<?php

public function testUserCanLoginWithValidCredentials() {
    // GIVEN: User exists in system
    $user = new User('john@example.com', 'password123');
    $this->userRepository->save($user);
    
    // WHEN: User attempts to login
    $result = $this->authService->login('john@example.com', 'password123');
    
    // THEN: User is authenticated
    $this->assertTrue($result['success']);
    $this->assertNotNull($result['token']);
}
```

### Test Case Templates

```php
<?php

abstract class RepositoryTestCase extends TestCase {
    
    protected $repository;
    abstract protected function createRepository();
    
    protected function setUp(): void {
        $this->repository = $this->createRepository();
    }
    
    // Template tests that all repositories must pass
    public function testCreate() {
        $entity = $this->createTestEntity();
        $id = $this->repository->create($entity);
        $this->assertGreaterThan(0, $id);
    }
    
    public function testRead() {
        $entity = $this->createTestEntity();
        $this->repository->create($entity);
        $found = $this->repository->findById(1);
        $this->assertNotNull($found);
    }
    
    public function testUpdate() {
        // Template test
    }
    
    public function testDelete() {
        // Template test
    }
    
    abstract protected function createTestEntity();
}
```

---

## Property-Based Testing

### Concept

```php
<?php

public function testAdditionIsCommutative() {
    // For any two numbers: a + b = b + a
    $calculator = new Calculator();
    
    for ($i = 0; $i < 100; $i++) {
        $a = rand(0, 1000);
        $b = rand(0, 1000);
        
        $result1 = $calculator->add($a, $b);
        $result2 = $calculator->add($b, $a);
        
        $this->assertEquals($result1, $result2);
    }
}

public function testStringConcatenationLength() {
    // Length of concat = sum of input lengths
    for ($i = 0; $i < 100; $i++) {
        $str1 = $this->generateString(rand(1, 50));
        $str2 = $this->generateString(rand(1, 50));
        
        $concat = $str1 . $str2;
        
        $this->assertEquals(
            strlen($str1) + strlen($str2),
            strlen($concat)
        );
    }
}

private function generateString($length) {
    $chars = 'abcdefghijklmnopqrstuvwxyz';
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $str;
}
```

---

## Snapshot Testing

### Concept

```php
<?php

public function testFormattedOutput() {
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'created' => '2024-01-01',
    ];
    
    $formatter = new UserFormatter();
    $output = $formatter->format($data);
    
    // Compare against saved snapshot
    $this->assertStringEqualsFile(
        __DIR__ . '/snapshots/formatted_user.txt',
        $output
    );
}

public function testGeneratedHTML() {
    $user = new User('Jane', 'jane@example.com');
    $renderer = new UserRenderer();
    $html = $renderer->render($user);
    
    // Update snapshot if intentional
    // Run: phpunit --update-snapshots
    $this->assertStringEqualsFile(
        __DIR__ . '/snapshots/user_card.html',
        $html
    );
}
```

---

## Builder Pattern in Tests

### Test Data Builder

```php
<?php

class UserBuilder {
    private $name = 'John';
    private $email = 'john@example.com';
    private $age = 25;
    private $active = true;
    
    public function withName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function withEmail($email) {
        $this->email = $email;
        return $this;
    }
    
    public function withAge($age) {
        $this->age = $age;
        return $this;
    }
    
    public function inactive() {
        $this->active = false;
        return $this;
    }
    
    public function build() {
        $user = new User();
        $user->setName($this->name);
        $user->setEmail($this->email);
        $user->setAge($this->age);
        $user->setActive($this->active);
        return $user;
    }
}

// Usage
public function testAdultUser() {
    $user = (new UserBuilder())
        ->withAge(30)
        ->build();
    
    $this->assertTrue($user->isAdult());
}

public function testInactiveUser() {
    $user = (new UserBuilder())
        ->inactive()
        ->build();
    
    $this->assertFalse($user->isActive());
}
```

---

## Test Factories

### Factory Pattern

```php
<?php

class TestFactory {
    
    public function user($overrides = []) {
        $data = array_merge([
            'name' => 'John',
            'email' => 'john@example.com',
            'age' => 25,
        ], $overrides);
        
        $user = new User($data);
        $user->save();
        
        return $user;
    }
    
    public function post($overrides = []) {
        $author = $this->user();
        
        $data = array_merge([
            'title' => 'Test Post',
            'content' => 'Test content',
            'author_id' => $author->id,
        ], $overrides);
        
        $post = new Post($data);
        $post->save();
        
        return $post;
    }
    
    public function users($count, $overrides = []) {
        $users = [];
        for ($i = 0; $i < $count; $i++) {
            $users[] = $this->user($overrides);
        }
        return $users;
    }
}

// Usage
public function testMultipleUsers() {
    $factory = new TestFactory();
    
    $users = $factory->users(5);
    $this->assertCount(5, $users);
}
```

---

## Common Pitfalls

### Over-Mocking

```php
// BAD: Too many mocks
public function testBad() {
    $dep1 = $this->createMock(Dependency1::class);
    $dep2 = $this->createMock(Dependency2::class);
    $dep3 = $this->createMock(Dependency3::class);
    
    // Tests implementation, not behavior
    $service = new Service($dep1, $dep2, $dep3);
}

// GOOD: Real dependencies where possible
public function testGood() {
    $service = new Service(
        new RealDependency(),  // Real if fast/simple
        $this->createMock(SlowDependency::class)  // Mock only slow deps
    );
}
```

### Assertion Blindness

```php
// BAD: No assertions
public function testBad() {
    $result = $this->calculator->add(2, 3);
    // What should it return?
}

// GOOD: Clear assertions
public function testGood() {
    $result = $this->calculator->add(2, 3);
    $this->assertEquals(5, $result);
}
```

### Unclear Test Names

```php
// BAD: Unclear
public function testUser() { }
public function test1() { }
public function testAdd() { }

// GOOD: Descriptive
public function testUserCanLoginWithValidCredentials() { }
public function testAddTwoPositiveNumbersReturnsSum() { }
public function testExceptionThrownWhenAddingNullValue() { }
```

---

## Advanced Scenarios

### Testing Abstract Classes

```php
<?php

abstract class AbstractValidator {
    abstract public function validate($data);
}

public function testAbstractValidator() {
    // Create anonymous concrete implementation
    $validator = new class extends AbstractValidator {
        public function validate($data) {
            return strlen($data) > 0;
        }
    };
    
    $this->assertTrue($validator->validate('test'));
}
```

### Testing Traits

```php
<?php

trait LoggableTrait {
    public function log($message) {
        return "LOG: $message";
    }
}

public function testTrait() {
    $class = new class {
        use LoggableTrait;
    };
    
    $result = $class->log('test');
    $this->assertEquals('LOG: test', $result);
}
```

### Testing Static Methods

```php
<?php

public function testStaticMethod() {
    $result = Math::add(2, 3);
    $this->assertEquals(5, $result);
}
```

---

## Complete Examples

### Example 1: Complex Service Test

```php
<?php

class OrderServiceTest extends TestCase {
    
    private $repository;
    private $payment;
    private $notification;
    private $service;
    
    protected function setUp(): void {
        $this->repository = new InMemoryOrderRepository();
        $this->payment = $this->createMock(PaymentGateway::class);
        $this->notification = $this->createMock(NotificationService::class);
        
        $this->service = new OrderService(
            $this->repository,
            $this->payment,
            $this->notification
        );
    }
    
    public function testCompleteOrder() {
        // ARRANGE
        $order = (new OrderBuilder())
            ->withItems([
                ['name' => 'Widget', 'price' => 29.99],
                ['name' => 'Gadget', 'price' => 49.99],
            ])
            ->build();
        
        $this->payment->expects($this->once())
            ->method('charge')
            ->with($this->equalTo(79.98))
            ->willReturn(['status' => 'success']);
        
        $this->notification->expects($this->once())
            ->method('send');
        
        // ACT
        $result = $this->service->complete($order);
        
        // ASSERT
        $this->assertTrue($result['success']);
        $saved = $this->repository->find($order->id);
        $this->assertEquals('completed', $saved->status);
    }
}
```

### Example 2: Data-Driven Test

```php
<?php

class ValidationTest extends TestCase {
    
    public static function validEmailsProvider() {
        return [
            ['simple@example.com'],
            ['user+tag@example.co.uk'],
            ['test_user.name@example.com'],
        ];
    }
    
    public static function invalidEmailsProvider() {
        return [
            ['invalid'],
            ['@example.com'],
            ['user@'],
            ['user @example.com'],
        ];
    }
    
    #[DataProvider('validEmailsProvider')]
    public function testValidEmail($email) {
        $validator = new EmailValidator();
        $this->assertTrue($validator->isValid($email));
    }
    
    #[DataProvider('invalidEmailsProvider')]
    public function testInvalidEmail($email) {
        $validator = new EmailValidator();
        $this->assertFalse($validator->isValid($email));
    }
}
```

---

## Key Takeaways

**Advanced Testing Checklist:**

1. ✅ Use AAA pattern consistently
2. ✅ Avoid over-mocking
3. ✅ Write clear test names
4. ✅ Use test builders for complex data
5. ✅ Use factories for repeated setup
6. ✅ Test behavior, not implementation
7. ✅ Keep tests simple and focused
8. ✅ Use property-based testing for invariants
9. ✅ Document why tests exist
10. ✅ Refactor tests as you refactor code

---

## See Also

- [Creating Unit Tests](2-create-unit-test.md)
- [Mock Objects](13-stub.md)
- [Test Fixtures](9-fixture.md)
