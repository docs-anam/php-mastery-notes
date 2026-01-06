# Test Fixtures and Setup

## Overview

Fixtures are reusable test data and configuration. They help set up test environments consistently before each test and clean up afterward.

---

## Table of Contents

1. What are Fixtures
2. setUp() and tearDown()
3. setUpBeforeClass()
4. Creating Test Data
5. Fixture Objects
6. Resource Management
7. Complex Fixtures
8. Complete Examples

---

## What are Fixtures

### Definition

```
Fixture = Reusable test setup

Types:
- Object fixtures (test objects)
- Data fixtures (test data)
- File fixtures (temporary files)
- Database fixtures (test database state)

Benefits:
- Reduce setup code duplication
- Consistent test state
- Cleaner tests
- Easier maintenance
```

### Fixture Lifecycle

```
setUp()          ← Runs before EACH test
  test1()
tearDown()       ← Runs after EACH test

setUp()
  test2()
tearDown()

setUpBeforeClass()      ← Runs ONCE before ALL tests
setUp()
  test3()
tearDown()
...
tearDownAfterClass()    ← Runs ONCE after ALL tests
```

---

## setUp() and tearDown()

### Basic Setup

```php
<?php

class UserTest extends TestCase {
    
    protected $user;
    
    protected function setUp(): void {
        // Runs before EACH test
        $this->user = new User();
        $this->user->setName('John');
        $this->user->setEmail('john@example.com');
    }
    
    protected function tearDown(): void {
        // Runs after EACH test
        $this->user = null;
    }
    
    public function testUserName() {
        // $user already set up
        $this->assertEquals('John', $this->user->getName());
    }
    
    public function testUserEmail() {
        // Fresh $user set up
        $this->assertEquals('john@example.com', $this->user->getEmail());
    }
}
```

### Database Setup

```php
<?php

class RepositoryTest extends TestCase {
    
    protected $database;
    protected $repository;
    
    protected function setUp(): void {
        // Create test database connection
        $this->database = new TestDatabase();
        $this->database->connect();
        $this->database->execute("TRUNCATE TABLE users");
        
        $this->repository = new UserRepository($this->database);
    }
    
    protected function tearDown(): void {
        // Cleanup
        $this->database->execute("TRUNCATE TABLE users");
        $this->database->disconnect();
    }
    
    public function testCreateUser() {
        $id = $this->repository->create(['name' => 'John']);
        $this->assertGreaterThan(0, $id);
    }
}
```

### File Setup

```php
<?php

class FileProcessorTest extends TestCase {
    
    protected $tempDir;
    
    protected function setUp(): void {
        // Create temporary directory
        $this->tempDir = sys_get_temp_dir() . '/test_' . uniqid();
        mkdir($this->tempDir);
    }
    
    protected function tearDown(): void {
        // Clean temporary files
        array_map('unlink', glob($this->tempDir . '/*'));
        rmdir($this->tempDir);
    }
    
    public function testProcessFile() {
        $inputFile = $this->tempDir . '/input.txt';
        file_put_contents($inputFile, 'test data');
        
        $processor = new FileProcessor();
        $processor->process($inputFile);
        
        $this->assertFileExists($this->tempDir . '/output.txt');
    }
}
```

---

## setUpBeforeClass() and tearDownAfterClass()

### Class-Level Setup

```php
<?php

class ExpensiveSetupTest extends TestCase {
    
    protected static $sharedResource;
    
    public static function setUpBeforeClass(): void {
        // Runs ONCE before all tests
        // Use for expensive operations
        self::$sharedResource = new ExpensiveObject();
        self::$sharedResource->initialize();
    }
    
    public static function tearDownAfterClass(): void {
        // Runs ONCE after all tests
        self::$sharedResource = null;
    }
    
    public function testOne() {
        // Uses shared resource
        $result = self::$sharedResource->getValue();
        $this->assertNotNull($result);
    }
    
    public function testTwo() {
        // Uses same shared resource
        $result = self::$sharedResource->getValue();
        $this->assertNotNull($result);
    }
}
```

### Use Cases

```
setUpBeforeClass() Use Cases:
- Database creation (once)
- File system setup (once)
- Network connections
- Expensive API calls (once)
- Test database migration

setUp() Use Cases:
- Reset state between tests
- Create fresh objects
- Clear caches
- Initialize test data
- Mock configuration
```

---

## Creating Test Data

### Data Builders

```php
<?php

class UserBuilder {
    private $name = 'John';
    private $email = 'john@example.com';
    private $age = 25;
    
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
    
    public function build() {
        $user = new User();
        $user->setName($this->name);
        $user->setEmail($this->email);
        $user->setAge($this->age);
        return $user;
    }
}

// Usage
public function testUserCreation() {
    $user = (new UserBuilder())
        ->withName('Jane')
        ->withEmail('jane@example.com')
        ->withAge(30)
        ->build();
    
    $this->assertEquals('Jane', $user->getName());
}
```

### Factory Methods

```php
<?php

abstract class TestCase extends PHPUnit\Framework\TestCase {
    
    protected function createUser($overrides = []) {
        $defaults = [
            'name' => 'John',
            'email' => 'john@example.com',
            'age' => 25,
        ];
        
        $data = array_merge($defaults, $overrides);
        
        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setAge($data['age']);
        
        return $user;
    }
    
    protected function createPost($overrides = []) {
        $defaults = [
            'title' => 'Test Post',
            'content' => 'Test content',
        ];
        
        $data = array_merge($defaults, $overrides);
        
        $post = new Post();
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        
        return $post;
    }
}

// Usage
public function testUserAndPost() {
    $user = $this->createUser(['name' => 'Jane']);
    $post = $this->createPost(['title' => 'Custom Title']);
    
    $this->assertEquals('Jane', $user->getName());
    $this->assertEquals('Custom Title', $post->getTitle());
}
```

---

## Fixture Objects

### Test Fixtures Class

```php
<?php

class Fixtures {
    
    public static function validUser() {
        return [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 25,
        ];
    }
    
    public static function validPost() {
        return [
            'title' => 'Test Post',
            'content' => 'Test content',
            'author_id' => 1,
        ];
    }
    
    public static function invalidUser() {
        return [
            'name' => '',
            'email' => 'not-email',
            'age' => -5,
        ];
    }
}

// Usage
public function testValidUser() {
    $data = Fixtures::validUser();
    $validator = new UserValidator();
    $this->assertTrue($validator->validate($data));
}

public function testInvalidUser() {
    $data = Fixtures::invalidUser();
    $validator = new UserValidator();
    $this->assertFalse($validator->validate($data));
}
```

---

## Resource Management

### Proper Cleanup

```php
<?php

class ResourceTest extends TestCase {
    
    protected $resource;
    
    protected function setUp(): void {
        $this->resource = fopen('php://memory', 'r+');
    }
    
    protected function tearDown(): void {
        if (is_resource($this->resource)) {
            fclose($this->resource);
        }
    }
}
```

### Exception Safety

```php
<?php

class SafeFixtureTest extends TestCase {
    
    protected $connection;
    
    protected function setUp(): void {
        $this->connection = new DatabaseConnection();
        $this->connection->connect();
    }
    
    protected function tearDown(): void {
        try {
            $this->connection->rollback();
            $this->connection->disconnect();
        } catch (Exception $e) {
            // Handle teardown failures
        }
    }
}
```

---

## Complex Fixtures

### Multiple Related Objects

```php
<?php

class E2ETest extends TestCase {
    
    protected $user;
    protected $post;
    protected $comments;
    
    protected function setUp(): void {
        $this->user = $this->createUser();
        $this->post = $this->createPost(['author_id' => $this->user->getId()]);
        $this->comments = [
            $this->createComment(['post_id' => $this->post->getId()]),
            $this->createComment(['post_id' => $this->post->getId()]),
        ];
    }
    
    private function createUser() {
        $user = new User();
        $user->setName('John');
        $user->save();
        return $user;
    }
    
    private function createPost($overrides = []) {
        $defaults = ['title' => 'Test', 'content' => 'Test'];
        $post = new Post(array_merge($defaults, $overrides));
        $post->save();
        return $post;
    }
    
    private function createComment($overrides = []) {
        $defaults = ['text' => 'Comment'];
        $comment = new Comment(array_merge($defaults, $overrides));
        $comment->save();
        return $comment;
    }
}
```

---

## Complete Examples

### Example 1: User Repository Tests

```php
<?php

class UserRepositoryTest extends TestCase {
    
    protected $repository;
    protected $database;
    
    protected function setUp(): void {
        $this->database = new InMemoryDatabase();
        $this->repository = new UserRepository($this->database);
        
        // Add test data
        $this->repository->create(['name' => 'John', 'email' => 'john@test.com']);
        $this->repository->create(['name' => 'Jane', 'email' => 'jane@test.com']);
    }
    
    public function testFindUser() {
        $user = $this->repository->findByEmail('john@test.com');
        $this->assertEquals('John', $user->name);
    }
    
    public function testUpdateUser() {
        $user = $this->repository->findByEmail('john@test.com');
        $user->name = 'Jonathan';
        $this->repository->update($user);
        
        $updated = $this->repository->findByEmail('john@test.com');
        $this->assertEquals('Jonathan', $updated->name);
    }
    
    public function testDeleteUser() {
        $this->repository->deleteByEmail('john@test.com');
        $user = $this->repository->findByEmail('john@test.com');
        $this->assertNull($user);
    }
}
```

### Example 2: API Tests

```php
<?php

class ApiClientTest extends TestCase {
    
    protected $apiClient;
    protected $httpMock;
    
    protected function setUp(): void {
        $this->httpMock = new HttpClientMock();
        $this->apiClient = new ApiClient($this->httpMock);
    }
    
    public function testGetUser() {
        $this->httpMock->expect('GET', '/users/1')
            ->willReturn(200, ['id' => 1, 'name' => 'John']);
        
        $user = $this->apiClient->getUser(1);
        $this->assertEquals('John', $user['name']);
    }
    
    public function testCreateUser() {
        $this->httpMock->expect('POST', '/users')
            ->willReturn(201, ['id' => 1, 'name' => 'Jane']);
        
        $user = $this->apiClient->createUser(['name' => 'Jane']);
        $this->assertEquals(1, $user['id']);
    }
}
```

---

## Key Takeaways

**Fixture Checklist:**

1. ✅ Use setUp() for per-test setup
2. ✅ Use tearDown() for cleanup
3. ✅ Use setUpBeforeClass() for expensive setup
4. ✅ Create reusable test data
5. ✅ Use builders for complex data
6. ✅ Clean up resources properly
7. ✅ Keep fixtures simple and focused
8. ✅ Document fixture dependencies
9. ✅ Isolate tests with fresh fixtures
10. ✅ Handle exceptions in tearDown()

---

## See Also

- [Creating Unit Tests](2-create-unit-test.md)
- [Sharing Fixtures](10-sharing-fixture.md)
- [Test Dependencies](5-test-dependency.md)
