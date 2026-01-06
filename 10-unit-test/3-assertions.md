# Assertions in PHPUnit

## Overview

Assertions are the heart of testing. They verify that code behaves as expected by comparing actual results with expected values. PHPUnit provides a comprehensive set of assertion methods for testing different conditions.

---

## Table of Contents

1. What are Assertions
2. Basic Assertions
3. Type Assertions
4. Comparison Assertions
5. Exception Assertions
6. Object Assertions
7. Array Assertions
8. String Assertions
9. Complete Examples

---

## What are Assertions

### Definition

```
Assertion = Verification that a condition is true

Syntax:
$this->assertSomething($actual, $expected);

If assertion is true:   Test passes ✓
If assertion is false:  Test fails ✗

Example:
$result = 2 + 3;
$this->assertEquals(5, $result);
// Assertion passes if result equals 5
```

### Assertion Structure

```
$this->assertEquals(
    $expected,    // What we expect
    $actual,      // What we got
    $message      // Optional: failure message
);
```

---

## Basic Assertions

### assertTrue and assertFalse

```php
// Assert condition is true
$this->assertTrue(true);
$this->assertTrue(1 == 1);
$this->assertTrue($user->isActive());

// Assert condition is false
$this->assertFalse(false);
$this->assertFalse(1 == 2);
$this->assertFalse($user->isDeleted());

// With message
$this->assertTrue($valid, 'Email should be valid');
$this->assertFalse($deleted, 'User should not be deleted');
```

### assertEquals

```php
// Assert values are equal
$this->assertEquals(5, 2 + 3);
$this->assertEquals('John', $user->getName());
$this->assertEquals([1, 2, 3], $array);

// With message
$this->assertEquals(
    10,
    $calculator->add(5, 5),
    'Calculator should correctly add numbers'
);

// Comparison:
// Loose comparison (==)
// So 5 == '5' passes
```

### assertSame

```php
// Assert values are identical (strict comparison)
$this->assertSame(5, 5);          // ✓ Pass
$this->assertSame(5, '5');        // ✗ Fail (different types)
$this->assertSame(true, 1);       // ✗ Fail (different types)

// Objects
$obj1 = new User();
$obj2 = $obj1;
$this->assertSame($obj1, $obj2);  // ✓ Pass (same instance)

$obj3 = new User();
$this->assertSame($obj1, $obj3);  // ✗ Fail (different instances)
```

### assertNull and assertNotNull

```php
// Assert value is null
$this->assertNull(null);
$this->assertNull($user->getMiddleName()); // if null

// Assert value is not null
$this->assertNotNull(5);
$this->assertNotNull($user->getId());
$this->assertNotNull($result);
```

---

## Type Assertions

### assertIsArray

```php
// Assert value is array
$this->assertIsArray([1, 2, 3]);
$this->assertIsArray([]);
$this->assertIsArray($getData());

// Assert is not array
$this->assertIsNotArray('string');
$this->assertIsNotArray(123);
```

### assertIsString

```php
// Assert value is string
$this->assertIsString('hello');
$this->assertIsString($getName());

// Assert not string
$this->assertIsNotString(123);
$this->assertIsNotString(true);
```

### Other Type Assertions

```php
$this->assertIsInt(5);
$this->assertIsNotInt('5');

$this->assertIsFloat(5.5);
$this->assertIsNotFloat('5.5');

$this->assertIsBool(true);
$this->assertIsNotBool(1);

$this->assertIsNumeric(5);
$this->assertIsNumeric('5');
$this->assertIsNumeric(5.5);

$this->assertIsObject($obj);
$this->assertIsNotObject('string');

$this->assertIsCallable(function() {});
```

### assertInstanceOf

```php
// Assert object is instance of class
$user = new User();
$this->assertInstanceOf(User::class, $user);
$this->assertInstanceOf(UserInterface::class, $user);

// Assert not instance
$this->assertNotInstanceOf(Admin::class, $user);
```

---

## Comparison Assertions

### assertGreaterThan

```php
// Assert actual > expected
$this->assertGreaterThan(5, 10);   // ✓ 10 > 5
$this->assertGreaterThan(5, 5);    // ✗ 5 not > 5

// With variable
$score = 95;
$this->assertGreaterThan(90, $score);
```

### assertGreaterThanOrEqual

```php
$this->assertGreaterThanOrEqual(5, 10);  // ✓ 10 >= 5
$this->assertGreaterThanOrEqual(5, 5);   // ✓ 5 >= 5
$this->assertGreaterThanOrEqual(5, 3);   // ✗ 3 not >= 5
```

### assertLessThan and assertLessThanOrEqual

```php
// Assert actual < expected
$this->assertLessThan(10, 5);      // ✓ 5 < 10
$this->assertLessThan(10, 10);     // ✗ 10 not < 10

// Assert actual <= expected
$this->assertLessThanOrEqual(10, 5);   // ✓ 5 <= 10
$this->assertLessThanOrEqual(10, 10);  // ✓ 10 <= 10
```

### assertCount

```php
// Assert array/collection has count
$this->assertCount(3, [1, 2, 3]);
$this->assertCount(0, []);
$this->assertCount(1, ['item']);

// Assert not count
$this->assertNotCount(5, [1, 2, 3]);
```

---

## Exception Assertions

### expectException

```php
// Assert exception is thrown
public function testInvalidEmailThrowsException() {
    $this->expectException(InvalidArgumentException::class);
    
    $user = new User();
    $user->setEmail('invalid-email');  // Throws exception
}
```

### expectExceptionMessage

```php
// Assert specific exception message
public function testExceptionMessage() {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Invalid email format');
    
    $user = new User();
    $user->setEmail('bad');
}
```

### expectExceptionCode

```php
// Assert exception code
public function testExceptionCode() {
    $this->expectException(ErrorException::class);
    $this->expectExceptionCode(1);
    
    throw new ErrorException('Error', 1);
}
```

---

## Object Assertions

### assertObjectHasAttribute

```php
// Assert object has property
$user = new User();
$user->name = 'John';

$this->assertObjectHasAttribute('name', $user);
$this->assertObjectHasAttribute('email', $user);

// Assert doesn't have
$this->assertObjectNotHasAttribute('age', $user);
```

### assertObjectHasProperty (PHP 8.3+)

```php
// Modern way to check properties
$user = new User();
$user->name = 'John';

$this->assertObjectHasProperty('name', $user);
$this->assertObjectNotHasProperty('age', $user);
```

---

## Array Assertions

### assertArrayHasKey

```php
// Assert array has key
$array = ['name' => 'John', 'email' => 'john@example.com'];

$this->assertArrayHasKey('name', $array);
$this->assertArrayHasKey('email', $array);

// Assert doesn't have key
$this->assertArrayNotHasKey('age', $array);
```

### assertContains

```php
// Assert array contains value
$array = ['apple', 'banana', 'orange'];

$this->assertContains('apple', $array);
$this->assertContains('banana', $array);

// Assert doesn't contain
$this->assertNotContains('grape', $array);

// Strict check
$this->assertContains('1', [1, 2, 3], true);  // ✗ Different types
$this->assertContains(1, [1, 2, 3], true);   // ✓ Same type
```

### assertEqualsCanonicalizing

```php
// Assert arrays equal (ignore order)
$this->assertEqualsCanonicalizing(
    [1, 2, 3],
    [3, 2, 1]  // Different order but same content
);
```

---

## String Assertions

### assertStringContainsString

```php
// Assert string contains substring
$string = 'Hello, World!';

$this->assertStringContainsString('World', $string);
$this->assertStringContainsString('Hello', $string);

// Case-sensitive
$this->assertStringContainsString('world', $string);  // ✗ Fail (case sensitive)
```

### assertStringStartsWith and EndsWith

```php
// Assert string starts with
$this->assertStringStartsWith('Hello', 'Hello, World!');
$this->assertStringStartsWith('H', 'Hello');

// Assert string ends with
$this->assertStringEndsWith('!', 'Hello!');
$this->assertStringEndsWith('World!', 'Hello, World!');
```

### assertMatchesRegularExpression

```php
// Assert string matches regex
$this->assertMatchesRegularExpression(
    '/^[\w\.-]+@[\w\.-]+\.\w+$/',
    'user@example.com'
);

// Assert doesn't match
$this->assertDoesNotMatchRegularExpression(
    '/^[\d]+$/',
    'abc123'
);
```

---

## Complete Examples

### Example 1: Basic Assertions

```php
<?php

class CalculatorTest extends TestCase {
    private $calculator;
    
    protected function setUp(): void {
        $this->calculator = new Calculator();
    }
    
    public function testAddition() {
        $result = $this->calculator->add(5, 3);
        
        $this->assertEquals(8, $result);
        $this->assertSame(8, $result);
        $this->assertGreaterThan(7, $result);
        $this->assertGreaterThanOrEqual(8, $result);
        $this->assertLessThan(9, $result);
        $this->assertLessThanOrEqual(8, $result);
    }
}
```

### Example 2: Exception Assertions

```php
<?php

class UserTest extends TestCase {
    public function testInvalidEmailThrowsException() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        $user = new User();
        $user->setEmail('invalid');
    }
    
    public function testNegativeSalaryThrowsException() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(100);
        
        $employee = new Employee();
        $employee->setSalary(-1000);
    }
}
```

### Example 3: Array Assertions

```php
<?php

class ArrayTest extends TestCase {
    public function testArrayOperations() {
        $array = ['apple', 'banana', 'orange'];
        
        $this->assertIsArray($array);
        $this->assertCount(3, $array);
        $this->assertContains('apple', $array);
        $this->assertNotContains('grape', $array);
    }
    
    public function testArrayStructure() {
        $user = ['name' => 'John', 'email' => 'john@example.com'];
        
        $this->assertArrayHasKey('name', $user);
        $this->assertArrayHasKey('email', $user);
        $this->assertArrayNotHasKey('age', $user);
    }
}
```

---

## Key Takeaways

**Assertion Usage Checklist:**

1. ✅ Use assertTrue/assertFalse for boolean conditions
2. ✅ Use assertEquals for value comparison
3. ✅ Use assertSame for strict comparison
4. ✅ Use assertNull/assertNotNull for null checks
5. ✅ Use assertInstanceOf for type checking
6. ✅ Use assertArrayHasKey for array checks
7. ✅ Use assertContains for array/string contains
8. ✅ Use expectException for exception testing
9. ✅ Use assertStringContainsString for substrings
10. ✅ Pick appropriate assertion for clarity

---

## See Also

- [Creating Unit Tests](2-create-unit-test.md)
- [Software Testing Fundamentals](0-software-testing.md)
- [Test Exceptions](7-test-exception.md)
