# Data Providers for Parameterized Testing

## Overview

Data providers allow you to run the same test with different input values without duplicating code. This enables parameterized testing where a single test method executes multiple times with different data sets.

---

## Table of Contents

1. What are Data Providers
2. Creating Data Providers
3. Using Data Providers
4. Data Format
5. Named Datasets
6. Multiple Data Providers
7. Data Provider Inheritance
8. Complex Scenarios
9. Complete Examples

---

## What are Data Providers

### Definition

```
Data Provider = Method that provides test data

Benefit:
- Run same test with multiple inputs
- No code duplication
- Clear test variations
- Better coverage
- Easier maintenance

Example:
Single test method
Multiple datasets
Run multiple times
```

### Without Data Provider

```php
<?php

public function testAddPositive() {
    $this->assertEquals(5, 2 + 3);
}

public function testAddNegative() {
    $this->assertEquals(-1, -2 + 1);
}

public function testAddZero() {
    $this->assertEquals(3, 0 + 3);
}

public function testAddDecimal() {
    $this->assertEquals(4.5, 2.5 + 2);
}

// Code duplication
// Hard to maintain
```

### With Data Provider

```php
<?php

public static function additionProvider(): array {
    return [
        'positive' => [2, 3, 5],
        'negative' => [-2, 1, -1],
        'zero' => [0, 3, 3],
        'decimal' => [2.5, 2, 4.5],
    ];
}

#[DataProvider('additionProvider')]
public function testAddition($a, $b, $expected) {
    $this->assertEquals($expected, $a + $b);
}

// Single test
// Multiple datasets
// Clean and maintainable
```

---

## Creating Data Providers

### Basic Data Provider

```php
<?php

public static function validEmailsProvider(): array {
    return [
        'simple' => ['user@example.com'],
        'subdomain' => ['user@mail.example.com'],
        'numbers' => ['user123@example.com'],
        'dash' => ['user-name@example.com'],
    ];
}

public static function invalidEmailsProvider(): array {
    return [
        'no-at' => ['userexample.com'],
        'no-domain' => ['user@'],
        'no-user' => ['@example.com'],
        'spaces' => ['user @example.com'],
    ];
}
```

### Array Format

```php
public static function dataProvider(): array {
    return [
        // Format 1: Indexed array
        [1, 2, 3],  // test($a=1, $b=2, $c=3)
        [4, 5, 9],  // test($a=4, $b=5, $c=9)
        
        // Format 2: Named datasets
        'dataset1' => [1, 2, 3],
        'dataset2' => [4, 5, 9],
        
        // Format 3: Objects
        [new DateTime('2024-01-01'), true],
        [new DateTime('2025-12-31'), true],
    ];
}
```

---

## Using Data Providers

### Applying Data Provider

```php
<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase {
    
    public static function additionProvider(): array {
        return [
            [2, 3, 5],
            [0, 5, 5],
            [-1, -2, -3],
            [1.5, 2.5, 4.0],
        ];
    }
    
    #[DataProvider('additionProvider')]
    public function testAddition($a, $b, $expected) {
        $calc = new Calculator();
        $result = $calc->add($a, $b);
        $this->assertEquals($expected, $result);
    }
}
```

### Test Execution

```
Running testAddition with data set 0: [2, 3, 5]
Running testAddition with data set 1: [0, 5, 5]
Running testAddition with data set 2: [-1, -2, -3]
Running testAddition with data set 3: [1.5, 2.5, 4.0]

4 tests, 4 assertions
```

---

## Data Format

### Simple Values

```php
public static function provider(): array {
    return [
        // Single value
        [5],
        [10],
        ['text'],
        
        // Multiple values
        [5, 10, 15],
        ['a', 'b', 'ab'],
    ];
}
```

### Objects

```php
public static function userProvider(): array {
    return [
        [new User('John', 'john@example.com')],
        [new User('Jane', 'jane@example.com')],
    ];
}
```

### Arrays

```php
public static function arrayProvider(): array {
    return [
        [['a', 'b', 'c']],
        [['x', 'y', 'z']],
        [['user' => 'John', 'email' => 'john@example.com']],
    ];
}
```

### Generators

```php
public static function provider(): Generator {
    for ($i = 1; $i <= 100; $i++) {
        yield [$i, $i * 2];
    }
}

// Generates 100 datasets dynamically
```

---

## Named Datasets

### Meaningful Names

```php
public static function calculationProvider(): array {
    return [
        'basic addition' => [2, 3, 5],
        'negative numbers' => [-5, -3, -8],
        'decimal values' => [1.5, 2.5, 4.0],
        'zero handling' => [0, 5, 5],
        'large numbers' => [1000000, 2000000, 3000000],
    ];
}

#[DataProvider('calculationProvider')]
public function testCalculation($a, $b, $expected) {
    // Test runs with each dataset
}
```

### Test Output with Names

```
testCalculation (basic addition) [2, 3, 5] ... ok
testCalculation (negative numbers) [-5, -3, -8] ... ok
testCalculation (decimal values) [1.5, 2.5, 4.0] ... ok
testCalculation (zero handling) [0, 5, 5] ... ok
testCalculation (large numbers) [1000000, 2000000, 3000000] ... ok
```

---

## Multiple Data Providers

### Single Test, Multiple Providers

```php
<?php

public static function emailProvider(): array {
    return [
        ['valid@example.com'],
        ['another.email@test.com'],
    ];
}

public static function nameProvider(): array {
    return [
        ['John'],
        ['Jane'],
    ];
}

#[DataProvider('emailProvider')]
#[DataProvider('nameProvider')]
public function testUserData($data) {
    // Runs with all combinations
}

// Runs 4 times:
// 1. valid@example.com
// 2. another.email@test.com
// 3. John
// 4. Jane
```

---

## Data Provider Inheritance

### Parent Data Providers

```php
<?php

abstract class BaseTest extends TestCase {
    public static function basicDataProvider(): array {
        return [
            [1],
            [2],
            [3],
        ];
    }
}

class ChildTest extends BaseTest {
    #[DataProvider('basicDataProvider')]
    public function testWithInheritedData($value) {
        $this->assertTrue($value > 0);
    }
    
    public static function additionalDataProvider(): array {
        return [
            [4],
            [5],
        ];
    }
    
    #[DataProvider('additionalDataProvider')]
    public function testWithOwnData($value) {
        $this->assertTrue($value > 3);
    }
}
```

---

## Complex Scenarios

### CSV Data Provider

```php
public static function csvProvider(): array {
    $data = [];
    if (($handle = fopen('tests/data.csv', 'r')) !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            $data[] = $row;
        }
        fclose($handle);
    }
    return $data;
}
```

### Database Data Provider

```php
public static function databaseProvider(): array {
    $db = new Database();
    $rows = $db->query("SELECT id, name, email FROM users");
    
    $data = [];
    foreach ($rows as $row) {
        $data[$row['id']] = [$row['name'], $row['email']];
    }
    return $data;
}
```

---

## Complete Examples

### Example 1: Validation Testing

```php
<?php

class ValidatorTest extends TestCase {
    
    public static function validEmailsProvider(): array {
        return [
            'simple' => ['user@example.com'],
            'subdomain' => ['user@mail.example.com'],
            'plus' => ['user+tag@example.com'],
            'numbers' => ['user123@example456.com'],
        ];
    }
    
    #[DataProvider('validEmailsProvider')]
    public function testValidEmail($email) {
        $validator = new EmailValidator();
        $this->assertTrue($validator->isValid($email));
    }
    
    public static function invalidEmailsProvider(): array {
        return [
            'no-at' => ['userexample.com'],
            'no-domain' => ['user@'],
            'double-at' => ['user@@example.com'],
            'spaces' => ['user @example.com'],
        ];
    }
    
    #[DataProvider('invalidEmailsProvider')]
    public function testInvalidEmail($email) {
        $validator = new EmailValidator();
        $this->assertFalse($validator->isValid($email));
    }
}
```

### Example 2: Math Operations

```php
<?php

class CalculatorTest extends TestCase {
    
    public static function additionProvider(): array {
        return [
            'positive' => [5, 3, 8],
            'negative' => [-5, -3, -8],
            'mixed' => [5, -3, 2],
            'zero' => [0, 5, 5],
            'decimal' => [2.5, 3.5, 6.0],
        ];
    }
    
    #[DataProvider('additionProvider')]
    public function testAddition($a, $b, $expected) {
        $calc = new Calculator();
        $this->assertEquals($expected, $calc->add($a, $b));
    }
    
    public static function divisionProvider(): array {
        return [
            [10, 2, 5],
            [15, 3, 5],
            [100, 4, 25],
        ];
    }
    
    #[DataProvider('divisionProvider')]
    public function testDivision($a, $b, $expected) {
        $calc = new Calculator();
        $this->assertEquals($expected, $calc->divide($a, $b));
    }
}
```

---

## Key Takeaways

**Data Provider Checklist:**

1. ✅ Create public static method returning array
2. ✅ Use #[DataProvider] attribute
3. ✅ Test runs for each dataset
4. ✅ Use named datasets for clarity
5. ✅ Return array of arrays (or Generator)
6. ✅ Test parameters match data
7. ✅ One assertion per test generally
8. ✅ Use for multiple input variations
9. ✅ Avoid duplicating test logic
10. ✅ Document expected data format

---

## See Also

- [Creating Unit Tests](2-create-unit-test.md)
- [Assertions](3-assertions.md)
- [Attributes](4-attributes.md)
