# Creating Unit Tests with PHPUnit

## Overview

PHPUnit is the standard unit testing framework for PHP. This chapter covers creating your first unit tests, understanding the test structure, and running tests effectively with PHPUnit.

---

## Table of Contents

1. PHPUnit Basics
2. Test Structure
3. TestCase Class
4. Test Methods
5. setUp and tearDown
6. Running Tests
7. Test Organization
8. Naming Conventions
9. Complete Examples

---

## PHPUnit Basics

### What is PHPUnit

```
PHPUnit = Unit testing framework for PHP

Features:
- Test runner
- Assertion library
- Mock objects
- Test fixtures
- Code coverage
- Test organization

Installation:
composer require --dev phpunit/phpunit
```

### Installing PHPUnit

```bash
# Add as development dependency
composer require --dev phpunit/phpunit

# Verify installation
./vendor/bin/phpunit --version
# PHPUnit 10.5.x by Sebastian Bergmann

# Get help
./vendor/bin/phpunit --help
```

### First Test

```bash
# Create test file
mkdir tests
touch tests/CalculatorTest.php

# Run tests
./vendor/bin/phpunit tests/

# Or specific file
./vendor/bin/phpunit tests/CalculatorTest.php
```

---

## Test Structure

### Basic Test File

```php
<?php
// tests/CalculatorTest.php

namespace MyApp\Tests;

use MyApp\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase {
    public function testAddition() {
        $calculator = new Calculator();
        $result = $calculator->add(2, 3);
        
        $this->assertEquals(5, $result);
    }
}
```

### Anatomy of a Test

```php
<?php
class CalculatorTest extends TestCase {
    //                    ↑ Extends TestCase
    
    public function testAddition() {
    //     ↑ Method starts with "test"
        
        // Arrange: Setup
        $calculator = new Calculator();
        
        // Act: Execute
        $result = $calculator->add(2, 3);
        
        // Assert: Verify
        $this->assertEquals(5, $result);
    }
}
```

### AAA Pattern (Arrange-Act-Assert)

```
Arrange:
- Setup test data
- Create objects
- Configure mocks
- Initialize state

Act:
- Execute the code being tested
- Call the method
- Perform the action

Assert:
- Verify results
- Check expected values
- Confirm behavior
```

---

## TestCase Class

### Extending TestCase

```php
<?php

use PHPUnit\Framework\TestCase;

class MyTest extends TestCase {
    // All test methods go here
    // Inherits assertions and helpers
}
```

### What TestCase Provides

```
Assertions:
$this->assertEquals()
$this->assertTrue()
$this->assertFalse()
$this->assertNull()
$this->assertEmpty()

Setup/Teardown:
$this->setUp()
$this->tearDown()

Mocking:
$this->createMock()
$this->createPartialMock()
$this->createStub()

Utilities:
$this->getObjectAttribute()
$this->invoke()
```

---

## Test Methods

### Test Method Rules

```
Rule 1: Must be public
public function testSomething()

Rule 2: Must return void
public function testSomething(): void

Rule 3: Must start with 'test'
public function testAddition()
public function testUserCreation()

Rule 4: No parameters (unless data provider)
public function testAddition()
public function testAdd($a, $b, $expected)  // with data provider

Rule 5: Must contain assertions
public function testSomething() {
    $this->assertEquals(expected, actual);
}
```

### Test Method Naming

```
✓ Good names:
testCalculatorAddsPositiveNumbers()
testUserCanBeCreated()
testInvalidEmailThrowsException()
testDatabaseQueryReturnsResults()
testFileIsReadCorrectly()

✗ Bad names:
test1()
testStuff()
test()
myTest()
Test()
```

### Single Assertion Principle

```
✓ GOOD: One focused assertion
public function testUserCanBeCreated() {
    $user = User::create('John', 'john@example.com');
    $this->assertNotNull($user->id);
}

✗ BAD: Multiple unrelated assertions
public function testUser() {
    $user = User::create('John', 'john@example.com');
    $this->assertNotNull($user->id);
    $this->assertEquals('John', $user->name);
    $this->assertTrue($user->isActive());
    // Too many concerns
}
```

---

## setUp and tearDown

### setUp Method

```php
<?php

class CalculatorTest extends TestCase {
    private $calculator;
    
    protected function setUp(): void {
        // Runs BEFORE each test
        $this->calculator = new Calculator();
    }
    
    public function testAddition() {
        // $calculator is ready to use
        $result = $this->calculator->add(2, 3);
        $this->assertEquals(5, $result);
    }
    
    public function testSubtraction() {
        // Fresh $calculator instance
        $result = $this->calculator->subtract(5, 3);
        $this->assertEquals(2, $result);
    }
}
```

### tearDown Method

```php
<?php

class DatabaseTest extends TestCase {
    private $connection;
    
    protected function setUp(): void {
        // Connect to test database
        $this->connection = new Database('test');
    }
    
    protected function tearDown(): void {
        // Cleanup after each test
        $this->connection->close();
        // Or rollback transaction
    }
    
    public function testInsert() {
        $this->connection->insert('users', ['name' => 'John']);
        $this->assertTrue(true);
    }
}
```

### Execution Order

```
Before Test 1:    setUp()
Run Test 1:       testAddition()
After Test 1:     tearDown()

Before Test 2:    setUp()
Run Test 2:       testSubtraction()
After Test 2:     tearDown()

Before Test 3:    setUp()
Run Test 3:       testMultiplication()
After Test 3:     tearDown()

Each test gets fresh state
No pollution between tests
```

### setUpBeforeClass / tearDownAfterClass

```php
<?php

class ExpensiveSetupTest extends TestCase {
    private static $database;
    
    public static function setUpBeforeClass(): void {
        // Runs ONCE before all tests in class
        // Expensive operation: connect to database
        self::$database = new Database();
    }
    
    public static function tearDownAfterClass(): void {
        // Runs ONCE after all tests in class
        self::$database->disconnect();
    }
    
    public function testQuery1() {
        // Uses shared $database
    }
    
    public function testQuery2() {
        // Uses same $database
    }
}
```

---

## Running Tests

### Basic Test Execution

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific directory
./vendor/bin/phpunit tests/

# Run specific file
./vendor/bin/phpunit tests/CalculatorTest.php

# Run specific test
./vendor/bin/phpunit tests/CalculatorTest.php::testAddition
```

### Test Output

```bash
$ ./vendor/bin/phpunit tests/

PHPUnit 10.5.0 by Sebastian Bergmann

Running 5 tests...

.....                               5 / 5 (100%)

Time: 00:00.123

OK (5 tests, 5 assertions)

Legend:
. = Pass
F = Fail
E = Error
S = Skip
```

### Useful Options

```bash
# Verbose output
./vendor/bin/phpunit --verbose
# Shows each test individually

# Stop on first failure
./vendor/bin/phpunit --stop-on-failure

# Display coverage
./vendor/bin/phpunit --coverage-text

# HTML coverage report
./vendor/bin/phpunit --coverage-html coverage/

# Filter tests
./vendor/bin/phpunit --filter testAdd
# Runs only tests matching "testAdd"

# Repeat tests
./vendor/bin/phpunit --repeat 10
# Run tests 10 times
```

---

## Test Organization

### Directory Structure

```
project/
├── src/
│   ├── Calculator.php
│   ├── User.php
│   └── Database.php
├── tests/
│   ├── CalculatorTest.php
│   ├── UserTest.php
│   ├── DatabaseTest.php
│   ├── Unit/
│   ├── Feature/
│   ├── Integration/
│   └── bootstrap.php
├── phpunit.xml
└── composer.json
```

### Test by Type

```
tests/
├── Unit/                 Unit tests (isolated)
│   ├── CalculatorTest.php
│   └── UserTest.php
├── Integration/         Integration tests
│   ├── DatabaseTest.php
│   └── PaymentTest.php
├── Feature/            Feature tests
│   ├── UserRegistrationTest.php
│   └── CheckoutTest.php
└── bootstrap.php
```

### Test Namespacing

```php
<?php
// tests/Unit/CalculatorTest.php

namespace MyApp\Tests\Unit;

use MyApp\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase {
    public function testAddition() {
        $calc = new Calculator();
        $result = $calc->add(2, 3);
        $this->assertEquals(5, $result);
    }
}
```

---

## Naming Conventions

### Class Naming

```
Source class:      Calculator
Test class:        CalculatorTest

Source class:      User
Test class:        UserTest

Source namespace:  MyApp\Service\PaymentProcessor
Test namespace:    MyApp\Tests\Service\PaymentProcessorTest
```

### File Naming

```
Source file:       src/Calculator.php
Test file:         tests/Unit/CalculatorTest.php

Source file:       src/Service/UserService.php
Test file:         tests/Unit/Service/UserServiceTest.php
```

### Test Method Naming

```
Format: testWhatItDoes()

✓ testCalculatorAddsPositiveNumbers()
✓ testUserCanBeCreatedWithValidEmail()
✓ testInvalidEmailThrowsException()
✓ testDatabaseQueryReturnsCorrectResults()

Or with underscores:
✓ test_calculator_adds_positive_numbers()
✓ test_user_can_be_created()

Pick one style and be consistent
```

---

## Complete Examples

### Example 1: Basic Test

```php
<?php
// tests/Unit/CalculatorTest.php

namespace MyApp\Tests\Unit;

use MyApp\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase {
    private $calculator;
    
    protected function setUp(): void {
        $this->calculator = new Calculator();
    }
    
    public function testAddition() {
        $result = $this->calculator->add(5, 3);
        $this->assertEquals(8, $result);
    }
    
    public function testSubtraction() {
        $result = $this->calculator->subtract(10, 3);
        $this->assertEquals(7, $result);
    }
    
    public function testMultiplication() {
        $result = $this->calculator->multiply(4, 3);
        $this->assertEquals(12, $result);
    }
}
```

### Example 2: Test with Setup/Teardown

```php
<?php
// tests/Feature/UserTest.php

namespace MyApp\Tests\Feature;

use MyApp\User;
use MyApp\Database;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {
    private $database;
    
    protected function setUp(): void {
        $this->database = new Database(':memory:');
        $this->database->createTable('users');
    }
    
    protected function tearDown(): void {
        $this->database->dropTable('users');
        $this->database->disconnect();
    }
    
    public function testUserCanBeCreated() {
        $user = User::create($this->database, 'John', 'john@example.com');
        
        $this->assertNotNull($user->id);
        $this->assertEquals('John', $user->name);
    }
    
    public function testUserCanBeFetched() {
        User::create($this->database, 'Jane', 'jane@example.com');
        $user = User::findByEmail($this->database, 'jane@example.com');
        
        $this->assertEquals('Jane', $user->name);
    }
}
```

---

## Key Takeaways

**Test Creation Checklist:**

1. ✅ Extend PHPUnit\Framework\TestCase
2. ✅ Create test methods starting with 'test'
3. ✅ Use setUp for test preparation
4. ✅ Use tearDown for cleanup
5. ✅ Follow Arrange-Act-Assert pattern
6. ✅ Name tests clearly (describe what they test)
7. ✅ One assertion per test (generally)
8. ✅ Keep tests isolated from each other
9. ✅ Run tests frequently
10. ✅ Watch test output for failures

---

## See Also

- [Software Testing Fundamentals](0-software-testing.md)
- [Assertions](3-assertions.md)
- [Test Fixtures](9-fixture.md)
