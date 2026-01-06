# Attributes for Test Configuration

## Overview

PHPUnit attributes (formerly annotations) provide metadata about tests through PHP 8 attributes. They control test execution, group tests, manage dependencies, and configure test behavior without intrusive code comments.

---

## Table of Contents

1. Introduction to Attributes
2. Test Grouping with #[Group]
3. Test Dependencies with #[Depends]
4. Covering Code with #[CoversClass]
5. Data Providers with #[DataProvider]
6. Test Size with #[Small|Medium|Large]
7. Before/After Methods
8. Other Useful Attributes
9. Complete Examples

---

## Introduction to Attributes

### What are Attributes

```
Attributes = Metadata attached to test methods/classes

Syntax:
#[AttributeName]
#[AttributeName('value')]

Example:
#[CoversClass(Calculator::class)]
public function testAddition() { }

Benefits:
- Declarative
- Structured
- Type-safe (PHP 8+)
- No magic strings
```

### Legacy Annotations

```
Old way (annotations/docblocks):
/**
 * @covers Calculator::add
 * @group math
 */
public function testAddition() { }

New way (attributes):
#[CoversClass(Calculator::class)]
#[Group('math')]
public function testAddition() { }
```

---

## Test Grouping with #[Group]

### Grouping Tests

```php
<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('math')]
class CalculatorTest extends TestCase {
    
    #[Group('addition')]
    public function testAddition() {
        $this->assertEquals(5, 2 + 3);
    }
    
    #[Group('subtraction')]
    public function testSubtraction() {
        $this->assertEquals(1, 3 - 2);
    }
}

#[Group('integration')]
class DatabaseTest extends TestCase {
    public function testConnection() {
        // ...
    }
}
```

### Running Groups

```bash
# Run tests in specific group
./vendor/bin/phpunit --group math

# Run multiple groups
./vendor/bin/phpunit --group math,integration

# Exclude group
./vendor/bin/phpunit --exclude-group slow

# List available groups
./vendor/bin/phpunit --list-groups
```

### Common Groups

```
#[Group('unit')]         Unit tests
#[Group('integration')]  Integration tests
#[Group('slow')]         Slow tests
#[Group('fast')]         Fast tests
#[Group('database')]     Database tests
#[Group('external')]     External services
#[Group('feature-x')]    Feature tests
```

---

## Test Dependencies with #[Depends]

### Running Tests in Order

```php
<?php

use PHPUnit\Framework\Attributes\Depends;

class UserTest extends TestCase {
    
    public function testUserCanBeCreated(): User {
        $user = User::create('John', 'john@example.com');
        $this->assertNotNull($user->id);
        return $user;  // Return for dependent tests
    }
    
    #[Depends('testUserCanBeCreated')]
    public function testUserCanBeUpdated(User $user) {
        $user->name = 'Jane';
        $user->save();
        
        $this->assertEquals('Jane', $user->name);
    }
    
    #[Depends('testUserCanBeUpdated')]
    public function testUserCanBeDeleted(User $user) {
        $user->delete();
        $this->assertNull(User::find($user->id));
    }
}
```

### How Dependencies Work

```
Execution Order:
1. testUserCanBeCreated()
   - Creates user
   - Returns user object

2. testUserCanBeUpdated(user)
   - Receives user from testUserCanBeCreated
   - Updates user
   - Continues

3. testUserCanBeDeleted(user)
   - Receives updated user
   - Deletes user

Benefits:
- Tests share state
- Reduces setup/teardown
- Reduces database queries
- Tests workflow

Warning:
- Can make tests less independent
- If one fails, others skip
- Use sparingly
```

### Multiple Dependencies

```php
<?php

public function testA(): object {
    return new stdClass();
}

public function testB(): object {
    return new stdClass();
}

#[Depends('testA')]
#[Depends('testB')]
public function testC($a, $b) {
    // Receives results from testA and testB
    $this->assertNotNull($a);
    $this->assertNotNull($b);
}
```

---

## Covering Code with #[CoversClass]

### Declaring Coverage

```php
<?php

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Calculator::class)]
class CalculatorTest extends TestCase {
    
    public function testAddition() {
        $calc = new Calculator();
        $result = $calc->add(2, 3);
        $this->assertEquals(5, $result);
    }
    
    public function testSubtraction() {
        $calc = new Calculator();
        $result = $calc->subtract(5, 2);
        $this->assertEquals(3, $result);
    }
}
```

### Method Coverage

```php
<?php

use PHPUnit\Framework\Attributes\CoversMethod;

class UserTest extends TestCase {
    
    #[CoversMethod(User::class, 'getName')]
    public function testGetName() {
        $user = new User('John');
        $this->assertEquals('John', $user->getName());
    }
    
    #[CoversMethod(User::class, 'setName')]
    public function testSetName() {
        $user = new User('John');
        $user->setName('Jane');
        $this->assertEquals('Jane', $user->getName());
    }
}
```

### Benefits

```
Declares intent:
- Shows what you're testing
- Documents coverage
- Helps organize tests

Code coverage reports:
- More accurate reporting
- Identifies gaps
- Tracks covered code
```

---

## Data Providers with #[DataProvider]

### Using Data Provider Attribute

```php
<?php

use PHPUnit\Framework\Attributes\DataProvider;

class CalculatorTest extends TestCase {
    
    public static function additionProvider(): array {
        return [
            'positive' => [2, 3, 5],
            'negative' => [-1, -2, -3],
            'zero' => [0, 5, 5],
            'decimal' => [1.5, 2.5, 4.0],
        ];
    }
    
    #[DataProvider('additionProvider')]
    public function testAddition($a, $b, $expected) {
        $calc = new Calculator();
        $result = $calc->add($a, $b);
        $this->assertEquals($expected, $result);
    }
}

// Runs testAddition 4 times with different data
```

---

## Test Size with #[Small|Medium|Large]

### Marking Test Size

```php
<?php

use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\Large;

#[Small]
class FastUnitTest extends TestCase {
    // Fast unit tests (< 100ms)
}

#[Medium]
class DatabaseTest extends TestCase {
    // Slower tests (< 1s)
}

#[Large]
class E2ETest extends TestCase {
    // Very slow tests (> 1s)
}
```

### Running by Size

```bash
# Run only small tests
./vendor/bin/phpunit --testdox-text-small

# Run excluding large tests
./vendor/bin/phpunit --exclude-large

# Only medium and large
./vendor/bin/phpunit --testdox-text-medium
./vendor/bin/phpunit --testdox-text-large
```

---

## Before/After Methods

### Setup/Teardown Attributes

```php
<?php

use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\BeforeClass;
use PHPUnit\Framework\Attributes\AfterClass;

class TestExample extends TestCase {
    
    #[Before]
    public function setup(): void {
        // Runs before each test (like setUp)
    }
    
    #[After]
    public function cleanup(): void {
        // Runs after each test (like tearDown)
    }
    
    #[BeforeClass]
    public static function setUpClass(): void {
        // Runs once before all tests
    }
    
    #[AfterClass]
    public static function tearDownClass(): void {
        // Runs once after all tests
    }
}
```

---

## Other Useful Attributes

### #[BackupGlobals]

```php
<?php

use PHPUnit\Framework\Attributes\BackupGlobals;

#[BackupGlobals(true)]
class GlobalTest extends TestCase {
    public function testGlobal() {
        $_SERVER['TEST'] = 'value';
        // Automatically restored after test
    }
}
```

### #[PreserveGlobalState]

```php
<?php

use PHPUnit\Framework\Attributes\PreserveGlobalState;

#[PreserveGlobalState(false)]
class TestWithoutGlobalState extends TestCase {
    // Won't inherit parent process state
}
```

### #[RunInSeparateProcess]

```php
<?php

use PHPUnit\Framework\Attributes\RunInSeparateProcess;

#[RunInSeparateProcess]
class TestRunning extends TestCase {
    // Runs in separate PHP process
    public function testIsolated() {
        // Complete isolation
    }
}
```

---

## Complete Examples

### Example 1: Grouped Tests

```php
<?php

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('math')]
class CalculatorTest extends TestCase {
    
    #[Group('addition')]
    public function testPositiveAddition() {
        $this->assertEquals(5, 2 + 3);
    }
    
    #[Group('addition')]
    public function testNegativeAddition() {
        $this->assertEquals(-1, -2 + 1);
    }
    
    #[Group('multiplication')]
    public function testMultiplication() {
        $this->assertEquals(6, 2 * 3);
    }
}

// Run with: ./vendor/bin/phpunit --group addition
```

### Example 2: Dependencies

```php
<?php

use PHPUnit\Framework\Attributes\Depends;

class OrderTest extends TestCase {
    
    public function testOrderCanBeCreated(): Order {
        $order = new Order();
        $order->addItem('SKU123', 2);
        
        $this->assertCount(1, $order->getItems());
        return $order;
    }
    
    #[Depends('testOrderCanBeCreated')]
    public function testOrderCanBeProcessed(Order $order) {
        $order->process();
        
        $this->assertTrue($order->isProcessed());
    }
    
    #[Depends('testOrderCanBeProcessed')]
    public function testOrderCanBeShipped(Order $order) {
        $order->ship();
        
        $this->assertTrue($order->isShipped());
    }
}
```

---

## Key Takeaways

**Attributes Checklist:**

1. ✅ Use #[Group] to organize tests
2. ✅ Use #[CoversClass] to declare coverage
3. ✅ Use #[DataProvider] for parameterized tests
4. ✅ Use #[Depends] for test workflows
5. ✅ Use #[Small|Medium|Large] for test sizing
6. ✅ Use #[Before/#After] for setup/teardown
7. ✅ Use #[RunInSeparateProcess] when needed
8. ✅ Declare coverage intent clearly
9. ✅ Keep attributes semantic
10. ✅ Run tests by group for organization

---

## See Also

- [Creating Unit Tests](2-create-unit-test.md)
- [Data Providers](6-data-provider.md)
- [Test Dependencies](5-test-dependency.md)
