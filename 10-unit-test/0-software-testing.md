# Software Testing Fundamentals

## Overview

Software testing is a critical aspect of development that ensures your code works correctly, handles edge cases, and prevents bugs. This chapter covers testing fundamentals, types of testing, and why testing matters for professional PHP development.

---

## Table of Contents

1. What is Software Testing
2. Types of Testing
3. Testing Pyramid
4. Benefits of Testing
5. Test Coverage
6. Testing Terminology
7. Test-Driven Development (TDD)
8. Common Testing Challenges
9. Complete Examples

---

## What is Software Testing

### Definition

```
Software Testing = Process of verifying that code works as expected

Goals:
1. Find bugs before production
2. Ensure code quality
3. Prevent regressions
4. Document expected behavior
5. Increase confidence

Testing answers:
✓ Does code do what it should?
✓ Does code handle errors?
✓ Does code work with edge cases?
✓ Does code break other code?
✓ Is code maintainable?
```

### Manual vs Automated Testing

```
Manual Testing:
- Tester runs code by hand
- Click buttons, check results
- Slow and repetitive
- Prone to human error
- Not scalable
- Good for exploratory testing

Automated Testing:
- Tests run automatically
- Code tests code
- Fast and repeatable
- Consistent results
- Scalable
- Catches regressions
- Professional standard
```

### Why Testing Matters

```
Without Tests:
- Find bugs late (expensive)
- Regressions on updates
- Fear of changing code
- Low code quality
- High maintenance costs
- Developer stress

With Tests:
- Find bugs early (cheap)
- Catch regressions
- Refactor with confidence
- Better code quality
- Lower maintenance
- Developer happiness
```

---

## Types of Testing

### Unit Testing

```
Definition: Test individual units (functions, methods, classes)

Scope: Small, isolated
Coverage: Single responsibility
Speed: Very fast
Tool: PHPUnit

Example:
Test Calculator::add() method
Input: add(2, 3)
Expected: 5
```

### Integration Testing

```
Definition: Test multiple units working together

Scope: Medium, multiple components
Coverage: Interactions between units
Speed: Slower than unit
Tool: PHPUnit, custom setup

Example:
Test User creation with database
Input: createUser('John', 'john@example.com')
Expected: User saved to database
```

### Functional Testing

```
Definition: Test complete features from user perspective

Scope: Large, full workflows
Coverage: Complete user journey
Speed: Slower
Tool: Laravel Dusk, Selenium, Cypress

Example:
Test user registration workflow
Input: Fill form, click submit
Expected: User account created, logged in
```

### Acceptance Testing

```
Definition: Test if software meets business requirements

Scope: Large, complete features
Coverage: Business requirements
Speed: Slowest
Tool: Gherkin, Behat, Cucumber

Example:
Test checkout process
Input: Select products, enter shipping
Expected: Order placed, confirmation sent
```

### Performance Testing

```
Definition: Test speed, load handling, resource usage

Types:
- Load testing: Multiple concurrent users
- Stress testing: Beyond capacity
- Benchmark: Speed measurements

Tool: Apache JMeter, Locust

Example:
Can system handle 10,000 concurrent users?
```

### Security Testing

```
Definition: Test for vulnerabilities and security issues

Tests:
- SQL injection
- XSS attacks
- CSRF protection
- Authentication
- Authorization

Tool: OWASP ZAP, Burp Suite
```

---

## Testing Pyramid

### The Pyramid Structure

```
                  /\
                 /  \
                /E2E \        End-to-End Tests
               /Tests \       Slow, broad, expensive
              /________\
             /          \
            / Integration \   Integration Tests
           /    Tests     \   Medium speed
          /________________\
         /                  \
        /    Unit Tests      \  Unit Tests
       /     (Fast & Cheap)   \ Fast, focused
      /____________________________\

Ideal Distribution:
- 70% Unit Tests (fast, cheap)
- 20% Integration Tests (medium)
- 10% E2E Tests (slow, expensive)

Why:
Unit tests are:
- Cheapest to write
- Fastest to run
- Easiest to debug
- Best ROI

E2E tests are:
- Expensive to write
- Slowest to run
- Hard to debug
- But test real scenarios
```

---

## Benefits of Testing

### Confidence in Code

```
✓ Code works as expected
✓ Changes don't break things
✓ Refactoring is safe
✓ Deployments are safe
✓ Sleep better at night
```

### Faster Development

```
✓ Bugs caught early (cheaper to fix)
✓ Less debugging time
✓ Less manual testing
✓ Faster iterations
✓ Better code design
```

### Better Design

```
✓ Testable code is better code
✓ Smaller, focused functions
✓ Loose coupling
✓ Clear responsibilities
✓ Easier to understand
✓ Easier to maintain
```

### Documentation

```
✓ Tests show how to use code
✓ Examples of correct usage
✓ Living documentation
✓ Always up-to-date
✓ Catches documentation bugs
```

### Regression Prevention

```
✓ Changes don't break existing code
✓ Old bugs stay fixed
✓ Feature requests don't introduce regressions
✓ Safe refactoring
```

---

## Test Coverage

### What is Coverage

```
Test Coverage = Percentage of code executed by tests

Formula:
Coverage = (Lines executed by tests / Total lines) × 100

Example:
- Total lines: 100
- Lines tested: 80
- Coverage: 80%
```

### Coverage Goals

```
100% coverage is ideal but:
- Usually not needed
- Time-consuming to achieve
- Some code is trivial (getters/setters)
- Some code is hard to test
- Diminishing returns

Realistic Goals:
- 80-90%: Excellent
- 60-80%: Good
- 40-60%: Fair
- <40%: Poor
```

### Measuring Coverage

```bash
# With PHPUnit
./vendor/bin/phpunit --coverage-html coverage/

# Generates HTML report
# Shows which lines are covered
# Shows which lines are missed
```

---

## Testing Terminology

### Common Terms

```
Test Case: Individual test (one method)
Test Suite: Collection of test cases
Test Fixture: Setup and teardown
Assertion: Check if condition is true
Mock Object: Fake object for testing
Stub: Simplified version of object
Spy: Records what was called
```

### Test States

```
PASS: Test passed (assertions true)
FAIL: Test failed (assertion false)
ERROR: Test error (exception thrown)
SKIP: Test skipped (intentionally)
INCOMPLETE: Test incomplete (marked as such)
```

### Test Naming

```
✓ Good names:
testUserCanBeCreated()
testInvalidEmailThrowsException()
testCalculatorAddsCorrectly()

✗ Bad names:
test1()
testStuff()
test()
```

---

## Test-Driven Development (TDD)

### TDD Cycle

```
1. RED: Write failing test
   - Test doesn't pass yet
   - Code doesn't exist
   - Clarifies requirements

2. GREEN: Write code to pass
   - Minimal code needed
   - Make test pass
   - Don't over-engineer

3. REFACTOR: Improve code
   - Keep tests passing
   - Clean up code
   - Optimize
   - Maintain quality

Repeat for each feature
```

### TDD Benefits

```
✓ Code designed for testability
✓ Tests catch bugs early
✓ Cleaner code
✓ Better design
✓ Fewer bugs
✓ More confidence
```

### TDD in Practice

```php
// 1. RED: Write failing test
class CalculatorTest extends TestCase {
    public function testAddition() {
        $calc = new Calculator();
        $result = $calc->add(2, 3);
        $this->assertEquals(5, $result);
    }
}

// Test fails (Calculator doesn't exist)

// 2. GREEN: Write minimal code
class Calculator {
    public function add($a, $b) {
        return $a + $b;
    }
}

// Test passes

// 3. REFACTOR: Improve
class Calculator {
    /**
     * Add two numbers
     */
    public function add($a, $b) {
        $this->validateNumbers($a, $b);
        return $a + $b;
    }
    
    private function validateNumbers($a, $b) {
        if (!is_numeric($a) || !is_numeric($b)) {
            throw new InvalidArgumentException('Numbers required');
        }
    }
}

// Test still passes
```

---

## Common Testing Challenges

### Challenge 1: Legacy Code

```
Problem: Old code wasn't tested
Solution:
- Start with new features
- Write tests for changed code
- Gradually add tests
- Don't rewrite everything
- Use characterization tests
```

### Challenge 2: Slow Tests

```
Problem: Tests take too long
Causes:
- Database queries
- File I/O
- Network calls
- Sleep statements

Solutions:
- Mock external dependencies
- Use in-memory database
- Cache data
- Parallelize tests
```

### Challenge 3: Flaky Tests

```
Problem: Tests sometimes pass, sometimes fail
Causes:
- Race conditions
- Time-dependent code
- External dependencies
- Random data

Solutions:
- Isolate from external systems
- Use controlled data
- Control time (mock DateTime)
- Avoid sleeps
```

### Challenge 4: Hard to Test Code

```
Problem: Code is hard to test
Causes:
- Tight coupling
- Global state
- Hidden dependencies
- Mixed concerns

Solutions:
- Dependency injection
- Avoid global variables
- Separate concerns
- Smaller functions
```

---

## Complete Examples

### Example 1: Simple Unit Test

```php
<?php

namespace MyApp\Tests;

use MyApp\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase {
    private $calculator;
    
    protected function setUp(): void {
        $this->calculator = new Calculator();
    }
    
    public function testAddition() {
        $result = $this->calculator->add(2, 3);
        $this->assertEquals(5, $result);
    }
    
    public function testSubtraction() {
        $result = $this->calculator->subtract(5, 3);
        $this->assertEquals(2, $result);
    }
}
```

### Example 2: Testing with Mocks

```php
<?php

class PaymentProcessorTest extends TestCase {
    public function testProcessPayment() {
        // Create mock
        $gateway = $this->createMock(PaymentGateway::class);
        
        // Expect call
        $gateway->expects($this->once())
            ->method('charge')
            ->with($this->equalTo(100.00))
            ->willReturn(true);
        
        // Test
        $processor = new PaymentProcessor($gateway);
        $result = $processor->processPayment(100.00);
        
        $this->assertTrue($result);
    }
}
```

---

## Key Takeaways

**Testing Fundamentals Checklist:**

1. ✅ Understand testing importance
2. ✅ Know different test types
3. ✅ Follow testing pyramid (70% unit, 20% integration, 10% E2E)
4. ✅ Write clear test names
5. ✅ Aim for 80-90% coverage
6. ✅ Use mocks for external dependencies
7. ✅ Keep tests fast and isolated
8. ✅ Run tests frequently
9. ✅ Fix failing tests immediately
10. ✅ Consider TDD for new features

---

## See Also

- [Creating Unit Tests](2-create-unit-test.md)
- [Assertions](3-assertions.md)
- [Test Fixtures](9-fixture.md)
