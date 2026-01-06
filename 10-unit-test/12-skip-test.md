# Test Stubs and Test Doubles

## Overview

Stubs are objects that provide predefined responses without performing actual behavior. They replace real objects during testing.

---

## Table of Contents

1. What are Stubs
2. Creating Stubs
3. Stub vs Mock
4. Predefined Responses
5. Stub Inheritance
6. Complex Stubs
7. Complete Examples

---

## What are Stubs

### Definition

```
Stub = Fake object returning predefined values

Purpose:
- Replace external dependencies
- Provide consistent test data
- Avoid slow operations
- Control behavior predictably

When to Use:
- External API calls
- Database queries
- File system operations
- Time-based operations
```

### Stub Types

```php
// Manual stub
class UserRepositoryStub implements UserRepository {
    public function findById($id) {
        return new User(['id' => 1, 'name' => 'John']);
    }
}

// Created by PHPUnit
$stub = $this->createStub(UserRepository::class);

// Configured stub (mock-like)
$stub = $this->createMock(UserRepository::class);
```

---

## Creating Stubs

### Manual Stub Creation

```php
<?php

interface UserRepository {
    public function findById($id);
    public function save(User $user);
}

class UserRepositoryStub implements UserRepository {
    
    public function findById($id) {
        return new User(['id' => $id, 'name' => 'John']);
    }
    
    public function save(User $user) {
        // Do nothing
    }
}

// Usage
public function testUserService() {
    $repository = new UserRepositoryStub();
    $service = new UserService($repository);
    
    $user = $service->getUser(1);
    $this->assertEquals('John', $user->getName());
}
```

### Using createStub()

```php
<?php

public function testWithStub() {
    $repository = $this->createStub(UserRepository::class);
    
    // Configure stub behavior
    $repository->method('findById')
        ->willReturn(new User(['id' => 1, 'name' => 'John']));
    
    $service = new UserService($repository);
    $user = $service->getUser(1);
    $this->assertEquals('John', $user->getName());
}
```

---

## Stub vs Mock

### Stub

```php
// Stub: Returns predefined value
$repository = $this->createStub(UserRepository::class);
$repository->method('findById')->willReturn($user);

$service = new UserService($repository);
$result = $service->getUser(1);

// No assertion on stub behavior
// Only care about return value
```

### Mock

```php
// Mock: Verifies behavior was called
$repository = $this->createMock(UserRepository::class);
$repository->expects($this->once())
    ->method('findById')
    ->with(1)
    ->willReturn($user);

$service = new UserService($repository);
$result = $service->getUser(1);

// Asserts findById was called exactly once with argument 1
```

### Key Difference

```
Stub:
- Provides predefined responses
- No behavior verification
- Used for replacements
- Simple, lightweight

Mock:
- Provides predefined responses
- Verifies method calls
- Used for behavior verification
- More complex, assertive
```

---

## Predefined Responses

### Return Values

```php
<?php

public function testStubReturnValue() {
    $stub = $this->createStub(Calculator::class);
    
    $stub->method('add')->willReturn(5);
    
    $result = $stub->add(2, 3);
    $this->assertEquals(5, $result);
}
```

### Return Different Values

```php
public function testStubMultipleReturns() {
    $stub = $this->createStub(Iterator::class);
    
    $stub->method('current')
        ->willReturnOnConsecutiveCalls(1, 2, 3);
    
    $this->assertEquals(1, $stub->current());
    $this->assertEquals(2, $stub->current());
    $this->assertEquals(3, $stub->current());
}
```

### Return Based on Arguments

```php
public function testStubReturnByArgument() {
    $stub = $this->createStub(UserRepository::class);
    
    $stub->method('findById')
        ->willReturnMap([
            [1, new User(['id' => 1, 'name' => 'John'])],
            [2, new User(['id' => 2, 'name' => 'Jane'])],
            [3, null],
        ]);
    
    $this->assertEquals('John', $stub->findById(1)->getName());
    $this->assertEquals('Jane', $stub->findById(2)->getName());
    $this->assertNull($stub->findById(3));
}
```

### Return Exceptions

```php
public function testStubThrowsException() {
    $stub = $this->createStub(Database::class);
    
    $stub->method('query')
        ->will($this->throwException(
            new DatabaseException('Connection failed')
        ));
    
    $this->expectException(DatabaseException::class);
    $stub->query('SELECT * FROM users');
}
```

---

## Stub Inheritance

### Extending Stub Classes

```php
<?php

abstract class UserRepositoryStub implements UserRepository {
    
    public function findById($id) {
        return new User(['id' => $id, 'name' => 'John']);
    }
    
    public function save(User $user) {
        // Default: do nothing
    }
}

// Custom stub for specific test
class CustomUserRepositoryStub extends UserRepositoryStub {
    
    public function findById($id) {
        if ($id === 999) {
            return null;  // Override: user not found
        }
        return parent::findById($id);
    }
}

// Usage
public function testUserNotFound() {
    $repository = new CustomUserRepositoryStub();
    $user = $repository->findById(999);
    $this->assertNull($user);
}
```

---

## Complex Stubs

### Builder Pattern Stubs

```php
<?php

class UserRepositoryStubBuilder {
    
    private $users = [];
    
    public function withUser($id, $name) {
        $this->users[$id] = new User(['id' => $id, 'name' => $name]);
        return $this;
    }
    
    public function build() {
        $stub = $this->createStub(UserRepository::class);
        
        $stub->method('findById')
            ->willReturnCallback(function($id) {
                return $this->users[$id] ?? null;
            });
        
        return $stub;
    }
}

// Usage
public function testWithComplexData() {
    $repository = (new UserRepositoryStubBuilder())
        ->withUser(1, 'John')
        ->withUser(2, 'Jane')
        ->build();
    
    $this->assertEquals('John', $repository->findById(1)->getName());
    $this->assertEquals('Jane', $repository->findById(2)->getName());
}
```

---

## Complete Examples

### Example 1: API Stub

```php
<?php

class WeatherApiStub implements WeatherApi {
    
    public function getCurrentTemperature($city) {
        return [
            'city' => $city,
            'temperature' => 25,
            'condition' => 'Sunny',
        ];
    }
    
    public function getForecast($city, $days) {
        return array_fill(0, $days, [
            'temperature' => 25,
            'condition' => 'Sunny',
        ]);
    }
}

class WeatherServiceTest extends TestCase {
    
    public function testGetCurrentWeather() {
        $api = new WeatherApiStub();
        $service = new WeatherService($api);
        
        $weather = $service->getCurrentWeather('London');
        
        $this->assertEquals('London', $weather['city']);
        $this->assertEquals(25, $weather['temperature']);
    }
    
    public function testGetForecast() {
        $api = new WeatherApiStub();
        $service = new WeatherService($api);
        
        $forecast = $service->getForecast('London', 7);
        
        $this->assertCount(7, $forecast);
        $this->assertEquals(25, $forecast[0]['temperature']);
    }
}
```

### Example 2: Database Stub

```php
<?php

class DatabaseStub implements Database {
    
    private $tables = [];
    
    public function __construct() {
        $this->tables['users'] = [
            ['id' => 1, 'name' => 'John', 'email' => 'john@test.com'],
            ['id' => 2, 'name' => 'Jane', 'email' => 'jane@test.com'],
        ];
    }
    
    public function find($table, $id) {
        $rows = array_filter(
            $this->tables[$table] ?? [],
            function($row) use ($id) {
                return $row['id'] === $id;
            }
        );
        return reset($rows) ?: null;
    }
    
    public function all($table) {
        return $this->tables[$table] ?? [];
    }
}

class RepositoryTest extends TestCase {
    
    public function testFindUser() {
        $db = new DatabaseStub();
        $repository = new UserRepository($db);
        
        $user = $repository->findById(1);
        
        $this->assertEquals('John', $user['name']);
    }
}
```

### Example 3: File System Stub

```php
<?php

class FileSystemStub implements FileSystem {
    
    private $files = [];
    
    public function __construct() {
        $this->files['/config.json'] = '{"debug": true}';
        $this->files['/data.csv'] = "id,name\n1,John\n2,Jane";
    }
    
    public function exists($path) {
        return isset($this->files[$path]);
    }
    
    public function read($path) {
        if (!$this->exists($path)) {
            throw new FileNotFoundException($path);
        }
        return $this->files[$path];
    }
    
    public function write($path, $content) {
        $this->files[$path] = $content;
    }
}

class ConfigLoaderTest extends TestCase {
    
    public function testLoadConfiguration() {
        $fs = new FileSystemStub();
        $loader = new ConfigLoader($fs);
        
        $config = $loader->load('/config.json');
        
        $this->assertTrue($config['debug']);
    }
}
```

---

## Key Takeaways

**Stub Checklist:**

1. ✅ Use stubs to replace external dependencies
2. ✅ Create simple, focused stubs
3. ✅ Use createStub() for convenience
4. ✅ Configure predefined responses
5. ✅ Return values based on arguments
6. ✅ Use inheritance for stub variants
7. ✅ Keep stubs simple and readable
8. ✅ Don't verify behavior on stubs
9. ✅ Use mocks when verifying behavior
10. ✅ Document stub limitations

---

## See Also

- [Mock Objects](14-mock-object.md)
- [Creating Unit Tests](2-create-unit-test.md)
- [Test Fixtures](9-fixture.md)
