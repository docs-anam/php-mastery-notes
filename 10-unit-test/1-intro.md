# PHP Unit Testing - Software Quality & TDD

## Table of Contents
1. [Overview](#overview)
2. [Testing Pyramid](#testing-pyramid)
3. [PHPUnit Framework](#phpunit-framework)
4. [Test-Driven Development](#test-driven-development)
5. [Core Concepts](#core-concepts)
6. [Testing Strategies](#testing-strategies)
7. [Learning Path](#learning-path)
8. [Best Practices](#best-practices)
9. [Prerequisites](#prerequisites)

---

## Overview

Software testing is critical for building reliable applications. This section covers:
- Unit testing with PHPUnit
- Writing testable code
- Test-driven development (TDD)
- Mocking and test doubles
- Test coverage and metrics

### Why Testing Matters

**Without Tests:**
```
Make change → Hope it works → Break something → Long debugging
```

**With Tests:**
```
Make change → Run tests → Know immediately if broken → Fix
```

Tests give you confidence to refactor and extend code without fear.

## Testing Pyramid

Different types of tests serve different purposes:

```
                  ▲
               E2E Tests
            (Slow, Expensive)
            /                \
           /   Integration    \
          /      Tests        \
         /    (Medium Speed)   \
        /                       \
       /    Unit Tests           \
      /  (Fast, Focused)         \
     ▼___________________________▼
```

### Unit Tests
- **What**: Test individual functions/methods
- **Speed**: Very fast (milliseconds)
- **Scope**: Single unit of code
- **Isolation**: Mock dependencies
- **Example**: `calculatePrice(100, 0.2)` returns `80`

### Integration Tests
- **What**: Test multiple components together
- **Speed**: Slower than unit tests
- **Scope**: Multiple classes/systems
- **Isolation**: Use real dependencies
- **Example**: Database + User class working together

### End-to-End (E2E) Tests
- **What**: Test complete user workflows
- **Speed**: Slow (seconds to minutes)
- **Scope**: Entire application
- **Tools**: Selenium, Cypress, Puppeteer
- **Example**: Login flow → Browse products → Checkout

### Best Practice
- **70% Unit Tests** - Fast feedback
- **20% Integration** - Verify components work together
- **10% E2E** - Verify critical workflows

## PHPUnit Framework

### What is PHPUnit?

PHPUnit is the standard PHP testing framework. It provides:
- Test case classes
- Assertions (checks)
- Test runners
- Coverage reports
- Mock objects

### Installation

```bash
# Via Composer (recommended)
composer require --dev phpunit/phpunit

# Check installation
vendor/bin/phpunit --version
```

### Basic Structure

```php
<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase {
    private Calculator $calculator;
    
    protected function setUp(): void {
        $this->calculator = new Calculator();
    }
    
    public function testAddition(): void {
        $result = $this->calculator->add(2, 3);
        $this->assertEquals(5, $result);
    }
    
    public function testSubtraction(): void {
        $result = $this->calculator->subtract(5, 3);
        $this->assertEquals(2, $result);
    }
}
```

### Running Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run specific file
vendor/bin/phpunit tests/CalculatorTest.php

# Run with coverage report
vendor/bin/phpunit --coverage-html coverage/

# Watch mode (re-run on file changes)
vendor/bin/phpunit --watch
```

## Test-Driven Development (TDD)

### The TDD Cycle

TDD follows a specific workflow:

```
1. RED - Write test (it fails)
   └─ No implementation yet
   
2. GREEN - Write minimal code to pass
   └─ Make test pass quickly
   
3. REFACTOR - Improve code
   └─ Maintain test passing
   
4. Repeat
```

### Example: TDD Workflow

**Step 1: Write Test (RED)**
```php
public function testEmailValidation(): void {
    $validator = new EmailValidator();
    
    // Test not written yet - this will fail
    $this->assertTrue($validator->isValid('user@example.com'));
    $this->assertFalse($validator->isValid('invalid-email'));
}
```

**Step 2: Write Minimal Code (GREEN)**
```php
class EmailValidator {
    public function isValid(string $email): bool {
        // Minimal implementation - just pass the test
        return str_contains($email, '@');
    }
}
```

**Step 3: Refactor (REFACTOR)**
```php
class EmailValidator {
    public function isValid(string $email): bool {
        // Better implementation
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
```

### TDD Benefits

✅ **Better Design**: Writing tests forces you to think about API
✅ **Less Debugging**: Tests catch bugs early
✅ **Refactoring Confidence**: Change code safely
✅ **Documentation**: Tests show how to use code
✅ **Coverage**: High test coverage by nature

## Core Concepts

### Assertions

Assertions verify expected behavior:

```php
// Equality
$this->assertEquals(5, 2 + 3);
$this->assertSame(5, 5);  // Strict equality

// Boolean
$this->assertTrue($isActive);
$this->assertFalse($isDeleted);

// Null
$this->assertNull($result);
$this->assertNotNull($user);

// Arrays
$this->assertArrayHasKey('name', $user);
$this->assertContains('admin', $roles);
$this->assertEmpty($errors);

// Strings
$this->assertStringContains('foo', 'foobar');
$this->assertMatchesRegularExpression('/pattern/', 'text');

// Objects
$this->assertInstanceOf(User::class, $user);
$this->assertObjectHasAttribute('name', $user);

// Exceptions
$this->expectException(InvalidArgumentException::class);
$this->expectExceptionMessage('Invalid input');
```

### Test Structure (AAA Pattern)

```php
public function testUserCreation(): void {
    // ARRANGE: Set up test data
    $name = "John";
    $email = "john@example.com";
    
    // ACT: Execute the code being tested
    $user = new User($name, $email);
    
    // ASSERT: Verify results
    $this->assertEquals($name, $user->getName());
    $this->assertEquals($email, $user->getEmail());
}
```

### Fixtures (Setup & Teardown)

Test data that gets reused:

```php
class UserTest extends TestCase {
    private User $user;
    private Database $db;
    
    protected function setUp(): void {
        // Before each test
        $this->db = new Database();
        $this->user = new User("John", "john@example.com");
    }
    
    protected function tearDown(): void {
        // After each test
        $this->db->closeConnection();
    }
    
    public function testUserExists(): void {
        $this->assertTrue($this->user->exists());
    }
}
```

### Data Providers

Run same test with different data:

```php
#[DataProvider('provideEmails')]
public function testEmailValidation(string $email, bool $expected): void {
    $validator = new EmailValidator();
    $this->assertEquals($expected, $validator->isValid($email));
}

public static function provideEmails(): array {
    return [
        'valid email' => ['user@example.com', true],
        'missing @' => ['userexample.com', false],
        'empty' => ['', false],
        'spaces' => ['user @example.com', false],
    ];
}
```

### Mocks (Test Doubles)

Replace dependencies for isolation:

```php
public function testOrderProcessing(): void {
    // Create mock for PaymentGateway
    $paymentGateway = $this->createMock(PaymentGateway::class);
    
    // Define behavior
    $paymentGateway->method('charge')
        ->with(100)
        ->willReturn(true);
    
    // Use in test
    $order = new Order($paymentGateway);
    $result = $order->process(100);
    
    // Verify interaction
    $paymentGateway->expects($this->once())
        ->method('charge')
        ->with(100);
}
```

## Testing Strategies

### What to Test

**✅ Test:**
- Business logic
- Calculations and transformations
- Error conditions
- Edge cases
- Public API

**❌ Don't Test:**
- Framework code (Laravel, Symfony)
- Third-party libraries
- Getter/setter methods
- Database drivers

### Test Naming

Use descriptive names:

```php
// ❌ Bad
public function test1() {}

// ✅ Good
public function testCalculateDiscountAppliesPercentageCorrectly() {}
public function testUserRegistrationFailsWithInvalidEmail() {}
public function testOrderProcessingThrowsExceptionWhenPaymentFails() {}
```

### Code Coverage

Measure how much code is tested:

```bash
# Generate coverage report
vendor/bin/phpunit --coverage-html coverage/

# Check coverage percentage
vendor/bin/phpunit --coverage-text
```

**Target Coverage:**
- **80%+**: Good for most projects
- **100%**: Not necessary or practical
- **Below 50%**: Risk of undetected bugs

## Learning Path

Master testing progressively:

1. **[Testing Fundamentals](0-software-testing.md)** - Why test
2. **[PHPUnit Introduction](1-intro.md)** - Set up framework
3. **[Writing Tests](2-create-unit-test.md)** - First test
4. **[Assertions](3-assertions.md)** - Verifying behavior
5. **[Fixtures](9-fixture.md)** - Test data setup
6. **[Data Providers](6-data-provider.md)** - Parameterized tests
7. **[Exceptions](7-test-exception.md)** - Testing errors
8. **[Mocks](14-mock-object.md)** - Test isolation
9. **[Stubs](13-stub.md)** - Fake dependencies
10. **[Configuration](15-configuration.php)** - PHPUnit setup
11. **[Test Suites](16-test-suite.md)** - Organizing tests

## Best Practices

### 1. One Assertion Per Test (Or Few Related)
```php
// ✅ Good
public function testValidEmailIsAccepted(): void {
    $this->assertTrue($validator->isValid('user@example.com'));
}

public function testInvalidEmailIsRejected(): void {
    $this->assertFalse($validator->isValid('invalid'));
}

// ❌ Not great - multiple concerns
public function testValidation(): void {
    $this->assertTrue($validator->isValid('user@example.com'));
    $this->assertFalse($validator->isValid('invalid'));
    // ... 10 more assertions
}
```

### 2. Test Behavior, Not Implementation
```php
// ❌ Bad - tests implementation details
public function testCalculatorHasAddMethod(): void {
    $this->assertTrue(method_exists($calculator, 'add'));
}

// ✅ Good - tests behavior
public function testAdditionWorks(): void {
    $result = $calculator->add(2, 3);
    $this->assertEquals(5, $result);
}
```

### 3. Use Descriptive Assertions
```php
// ❌ Vague
$this->assertTrue($user->status === 'active');

// ✅ Clear
$this->assertTrue($user->isActive());
$this->assertEquals('active', $user->getStatus());
```

### 4. Keep Tests Independent
```php
// ❌ Bad - tests depend on each other
public function testCreateUser(): void {
    $user = new User("John");
    $this->assertTrue($user->exists());
}

public function testUpdateUser(): void {
    // Depends on previous test
    $user = $this->getLastCreatedUser();
}

// ✅ Good - each test is independent
public function testCreateUser(): void {
    $user = new User("John");
    $this->assertTrue($user->exists());
}

public function testUpdateUser(): void {
    $user = new User("John");  // Create fresh
    $user->update(['name' => 'Jane']);
    $this->assertEquals('Jane', $user->getName());
}
```

## Prerequisites

Before unit testing:

✅ **Required:**
- PHP OOP (classes, interfaces, inheritance)
- Understanding of public/private methods
- Basic familiarity with command line

✅ **Helpful:**
- Some TDD experience
- Understanding of design patterns
- Knowledge of dependency injection

## Quick Start

```bash
# 1. Install PHPUnit
composer require --dev phpunit/phpunit

# 2. Create test file
touch tests/CalculatorTest.php

# 3. Write test
# class CalculatorTest extends TestCase { ... }

# 4. Run tests
vendor/bin/phpunit
```

## Common Mistakes

❌ **Testing private methods**
```php
// Don't - they're not part of public API
$this->calculator->privateMethod();
```

❌ **Incomplete test names**
```php
// Bad
public function test() {}

// Good
public function testCalculatorReturnsCorrectSum() {}
```

❌ **Tightly coupled tests**
```php
// Bad - creates database
$user = new User();
$user->save();  // Real database call

// Good - mocks database
$db = $this->createMock(Database::class);
$user = new User($db);
```

## Resources

- **PHPUnit Manual**: [phpunit.de/manual](https://phpunit.de/manual/current/en/index.html)
- **TDD Resources**: [martinfowler.com/bliki/TestDrivenDevelopment.html](https://martinfowler.com/bliki/TestDrivenDevelopment.html)
- **Testing Best Practices**: [phpunit.de/best-practices.html](https://phpunit.de/best-practices.html)
- **Mocking Tutorial**: [phpunit.de/manual/current/en/test-doubles.html](https://phpunit.de/manual/current/en/test-doubles.html)
