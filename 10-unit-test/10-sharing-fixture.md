# Sharing Fixtures Between Tests

## Overview

Share fixtures across multiple tests through inheritance, composition, and fixtures traits. This reduces duplication and ensures consistent test environments.

---

## Table of Contents

1. Fixture Inheritance
2. Fixture Traits
3. Fixture Composition
4. Base Test Classes
5. Shared State
6. Multiple Inheritance Patterns
7. Complex Sharing
8. Complete Examples

---

## Fixture Inheritance

### Single Inheritance

```php
<?php

// Base test class with common fixtures
abstract class BaseTestCase extends TestCase {
    
    protected $user;
    protected $database;
    
    protected function setUp(): void {
        $this->database = new TestDatabase();
        $this->database->connect();
        
        $this->user = new User();
        $this->user->setName('John');
        $this->user->setEmail('john@example.com');
    }
    
    protected function tearDown(): void {
        $this->database->close();
    }
}

// Child test class inherits fixtures
class UserTest extends BaseTestCase {
    
    public function testUserName() {
        $this->assertEquals('John', $this->user->getName());
    }
    
    public function testUserEmail() {
        $this->assertEquals('john@example.com', $this->user->getEmail());
    }
}

// Another child class, same fixtures
class UserValidationTest extends BaseTestCase {
    
    public function testValidation() {
        $validator = new UserValidator();
        $this->assertTrue($validator->validate($this->user));
    }
}
```

### Extended Setup

```php
<?php

abstract class BaseTestCase extends TestCase {
    protected $database;
    
    protected function setUp(): void {
        $this->database = new TestDatabase();
        $this->database->connect();
    }
}

class UserRepositoryTest extends BaseTestCase {
    
    protected $repository;
    
    protected function setUp(): void {
        parent::setUp();  // Call parent setup first
        
        $this->repository = new UserRepository($this->database);
        $this->repository->create(['name' => 'John']);
    }
    
    public function testFindUser() {
        $user = $this->repository->find(1);
        $this->assertNotNull($user);
    }
}
```

---

## Fixture Traits

### Trait-Based Fixtures

```php
<?php

trait UserFixture {
    
    protected $user;
    
    protected function setupUser() {
        $this->user = new User();
        $this->user->setName('John');
        $this->user->setEmail('john@example.com');
    }
    
    protected function teardownUser() {
        $this->user = null;
    }
}

trait DatabaseFixture {
    
    protected $database;
    
    protected function setupDatabase() {
        $this->database = new TestDatabase();
        $this->database->connect();
    }
    
    protected function teardownDatabase() {
        $this->database->close();
    }
}

class UserRepositoryTest extends TestCase {
    use UserFixture;
    use DatabaseFixture;
    
    protected function setUp(): void {
        $this->setupDatabase();
        $this->setupUser();
    }
    
    protected function tearDown(): void {
        $this->teardownDatabase();
        $this->teardownUser();
    }
    
    public function testUserCreation() {
        $this->assertTrue(true);
    }
}
```

### Trait Inheritance

```php
<?php

// Base traits with common fixtures
trait CommonFixture {
    protected $config;
    
    protected function setupConfig() {
        $this->config = Config::make([
            'env' => 'testing',
            'debug' => true,
        ]);
    }
}

// Extended traits
trait DatabaseFixture {
    use CommonFixture;
    
    protected $database;
    
    protected function setupDatabase() {
        $this->setupConfig();
        $this->database = new Database($this->config);
    }
}

class ApiTest extends TestCase {
    use DatabaseFixture;
    
    protected function setUp(): void {
        $this->setupDatabase();
    }
}
```

---

## Fixture Composition

### Fixture Container

```php
<?php

class TestFixtures {
    
    private $fixtures = [];
    
    public function setUser(User $user) {
        $this->fixtures['user'] = $user;
        return $this;
    }
    
    public function getUser(): User {
        return $this->fixtures['user'] ?? new User();
    }
    
    public function setDatabase($database) {
        $this->fixtures['database'] = $database;
        return $this;
    }
    
    public function getDatabase() {
        return $this->fixtures['database'];
    }
    
    public function reset() {
        $this->fixtures = [];
    }
}

class MyTest extends TestCase {
    
    protected $fixtures;
    
    protected function setUp(): void {
        $this->fixtures = new TestFixtures();
        
        $user = new User();
        $user->setName('John');
        
        $this->fixtures->setUser($user);
        $this->fixtures->setDatabase(new TestDatabase());
    }
    
    public function testWithFixtures() {
        $user = $this->fixtures->getUser();
        $this->assertEquals('John', $user->getName());
    }
}
```

---

## Base Test Classes

### Abstract Base Class

```php
<?php

abstract class ApplicationTestCase extends TestCase {
    
    protected $app;
    protected $database;
    protected $cache;
    
    protected function setUp(): void {
        $this->app = new Application();
        $this->app->configure(['env' => 'testing']);
        
        $this->database = $this->app->getDatabase();
        $this->cache = $this->app->getCache();
        
        $this->seedDatabase();
    }
    
    protected function tearDown(): void {
        $this->database->rollback();
        $this->cache->flush();
    }
    
    protected function seedDatabase() {
        // Common test data
        $this->database->insert('users', [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane'],
        ]);
    }
}

class UserServiceTest extends ApplicationTestCase {
    
    public function testGetUser() {
        $service = new UserService($this->database);
        $user = $service->getUser(1);
        $this->assertEquals('John', $user['name']);
    }
}
```

### Hierarchy of Base Classes

```php
<?php

// Level 1: Basic setup
abstract class BaseTestCase extends TestCase {
    protected $config;
    
    protected function setUp(): void {
        $this->config = new Config(['env' => 'testing']);
    }
}

// Level 2: Add database
abstract class DatabaseTestCase extends BaseTestCase {
    protected $database;
    
    protected function setUp(): void {
        parent::setUp();
        $this->database = new Database($this->config);
    }
}

// Level 3: Add application
abstract class ApplicationTestCase extends DatabaseTestCase {
    protected $app;
    
    protected function setUp(): void {
        parent::setUp();
        $this->app = new Application($this->config, $this->database);
    }
}

// Usage: Tests inherit from appropriate level
class IntegrationTest extends ApplicationTestCase {
    public function testFullApplication() {
        // Has: config, database, app
    }
}
```

---

## Shared State

### Class-Level Fixtures

```php
<?php

class ExpensiveSetupTest extends TestCase {
    
    protected static $database;
    protected static $fixtures;
    
    public static function setUpBeforeClass(): void {
        // Setup once for all tests
        self::$database = new TestDatabase();
        self::$database->connect();
        
        self::$fixtures = [
            'user1' => self::$database->insert('users', ['name' => 'John']),
            'user2' => self::$database->insert('users', ['name' => 'Jane']),
        ];
    }
    
    public static function tearDownAfterClass(): void {
        self::$database->close();
    }
    
    // All tests share same fixtures
    public function testUserOne() {
        $id = self::$fixtures['user1'];
        $user = self::$database->find('users', $id);
        $this->assertEquals('John', $user['name']);
    }
    
    public function testUserTwo() {
        $id = self::$fixtures['user2'];
        $user = self::$database->find('users', $id);
        $this->assertEquals('Jane', $user['name']);
    }
}
```

### Transaction-Based Isolation

```php
<?php

abstract class TransactionalTestCase extends TestCase {
    
    protected static $database;
    
    public static function setUpBeforeClass(): void {
        self::$database = new Database();
        self::$database->connect();
    }
    
    protected function setUp(): void {
        self::$database->beginTransaction();
    }
    
    protected function tearDown(): void {
        // Rollback after each test for isolation
        self::$database->rollback();
    }
    
    // Each test starts fresh but database is only connected once
    public function testOne() {
        self::$database->insert('table', ['data']);
        // Modified by test
    }
    
    public function testTwo() {
        // Rollback from testOne, fresh state
        $count = self::$database->count('table');
        $this->assertEquals(0, $count);
    }
}
```

---

## Multiple Inheritance Patterns

### Combining Traits and Inheritance

```php
<?php

trait LogFixture {
    protected $logger;
    
    protected function setupLogger() {
        $this->logger = new Logger();
    }
}

trait CacheFixture {
    protected $cache;
    
    protected function setupCache() {
        $this->cache = new Cache();
    }
}

abstract class IntegrationTestCase extends TestCase {
    use LogFixture, CacheFixture;
    
    protected $database;
    
    protected function setUp(): void {
        $this->setupLogger();
        $this->setupCache();
        $this->database = new Database();
    }
}

class FullStackTest extends IntegrationTestCase {
    public function testWithAllFixtures() {
        // Has: logger, cache, database
    }
}
```

---

## Complex Sharing

### Fixture with Dependencies

```php
<?php

class FixtureFactory {
    
    private $fixtures = [];
    
    public function user($overrides = []) {
        if (!isset($this->fixtures['user'])) {
            $user = new User(array_merge([
                'name' => 'John',
                'email' => 'john@example.com',
            ], $overrides));
            $this->fixtures['user'] = $user;
        }
        return $this->fixtures['user'];
    }
    
    public function post($overrides = []) {
        $data = array_merge([
            'title' => 'Test Post',
            'author_id' => $this->user()->id,
        ], $overrides);
        
        return new Post($data);
    }
    
    public function reset() {
        $this->fixtures = [];
    }
}

abstract class FactoryTestCase extends TestCase {
    
    protected $factory;
    
    protected function setUp(): void {
        $this->factory = new FixtureFactory();
    }
    
    protected function tearDown(): void {
        $this->factory->reset();
    }
}

class PostTest extends FactoryTestCase {
    
    public function testPostWithAuthor() {
        $user = $this->factory->user(['name' => 'Jane']);
        $post = $this->factory->post(['author_id' => $user->id]);
        
        $this->assertEquals($user->id, $post->author_id);
    }
}
```

---

## Complete Examples

### Example 1: Shared Database Fixture

```php
<?php

abstract class RepositoryTestCase extends TestCase {
    
    protected static $database;
    protected $repository;
    
    public static function setUpBeforeClass(): void {
        self::$database = new InMemoryDatabase();
        self::$database->createSchema();
    }
    
    protected function setUp(): void {
        self::$database->beginTransaction();
    }
    
    protected function tearDown(): void {
        self::$database->rollback();
    }
    
    protected function seedUser($name = 'John') {
        return self::$database->insert('users', ['name' => $name]);
    }
}

class UserRepositoryTest extends RepositoryTestCase {
    
    protected function setUp(): void {
        parent::setUp();
        $this->repository = new UserRepository(self::$database);
    }
    
    public function testFindUser() {
        $id = $this->seedUser('Jane');
        $user = $this->repository->findById($id);
        $this->assertEquals('Jane', $user->name);
    }
}

class PostRepositoryTest extends RepositoryTestCase {
    
    protected function setUp(): void {
        parent::setUp();
        $this->repository = new PostRepository(self::$database);
    }
    
    public function testCreatePost() {
        $userId = $this->seedUser();
        $id = $this->repository->create(['user_id' => $userId, 'title' => 'Test']);
        $this->assertGreaterThan(0, $id);
    }
}
```

### Example 2: Fixture Trait Stack

```php
<?php

trait ConfigurationFixture {
    protected function setupConfiguration() {
        return Config::create(['env' => 'test']);
    }
}

trait DatabaseFixture {
    use ConfigurationFixture;
    
    protected function setupDatabase() {
        $config = $this->setupConfiguration();
        return new Database($config);
    }
}

trait ServiceFixture {
    use DatabaseFixture;
    
    protected function setupServices() {
        $database = $this->setupDatabase();
        return [
            'user_service' => new UserService($database),
            'post_service' => new PostService($database),
        ];
    }
}

class ServiceIntegrationTest extends TestCase {
    use ServiceFixture;
    
    protected $services;
    
    protected function setUp(): void {
        $this->services = $this->setupServices();
    }
    
    public function testServices() {
        // Both services ready to use
        $user = $this->services['user_service']->getUser(1);
        $posts = $this->services['post_service']->getUserPosts($user->id);
        $this->assertIsArray($posts);
    }
}
```

---

## Key Takeaways

**Fixture Sharing Checklist:**

1. ✅ Use inheritance for common fixtures
2. ✅ Use traits for reusable fixture code
3. ✅ Create base test classes for hierarchy
4. ✅ Share expensive setup via setUpBeforeClass()
5. ✅ Use transactions for test isolation
6. ✅ Create fixture factories for complex data
7. ✅ Document fixture dependencies
8. ✅ Use composition for flexibility
9. ✅ Reset shared state between tests
10. ✅ Balance DRY with test clarity

---

## See Also

- [Test Fixtures](9-fixture.md)
- [Creating Unit Tests](2-create-unit-test.md)
- [Test Dependencies](5-test-dependency.md)
