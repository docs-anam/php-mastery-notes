# Testing Exceptions and Error Handling

## Overview

Testing exceptions ensures your code handles errors correctly. PHPUnit provides methods to verify that specific exceptions are thrown under expected conditions.

---

## Table of Contents

1. Why Test Exceptions
2. Using expectException()
3. Exception Message Testing
4. Exception Code Testing
5. Custom Exceptions
6. Multiple Exception Testing
7. Exception Order Testing
8. Complete Examples

---

## Why Test Exceptions

### Importance

```
Benefits of Exception Testing:
- Verify error handling works
- Ensure correct exception type
- Check error messages
- Test fallback behavior
- Prevent silent failures
- Document expected failures

Example Scenario:
// Good: Test catches invalid input
// Bad: Code silently fails
```

### Types of Errors to Test

```php
// InvalidArgumentException
// LogicException
// RuntimeException
// Custom exceptions
// Multiple exception scenarios
```

---

## Using expectException()

### Basic Exception Testing

```php
<?php

public function testDivisionByZero() {
    $this->expectException(DivisionByZeroError::class);
    
    $calc = new Calculator();
    $result = $calc->divide(10, 0);
}

// Test passes if divide(10, 0) throws DivisionByZeroError
// Test fails if no exception is thrown
```

### Named Exception Classes

```php
<?php

use InvalidArgumentException;
use RuntimeException;
use LogicException;

class ProcessorTest extends TestCase {
    
    public function testNegativeValueException() {
        $this->expectException(InvalidArgumentException::class);
        
        $processor = new Processor();
        $processor->process(-5);  // Should throw
    }
    
    public function testEmptyInputException() {
        $this->expectException(LogicException::class);
        
        $processor = new Processor();
        $processor->process('');  // Should throw
    }
}
```

---

## Exception Message Testing

### Testing Exception Message

```php
<?php

public function testExceptionMessage() {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('User not found');
    
    $user = new User();
    $user->findById(-1);  // Should throw with specific message
}
```

### Exception Message Pattern

```php
public function testExceptionMessagePattern() {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessageMatches('/User \d+ not found/');
    
    $user = new User();
    $user->findById(999);  // Message: "User 999 not found"
}
```

### Message Verification

```php
// Exact match
$this->expectExceptionMessage('User not found');

// Pattern match
$this->expectExceptionMessageMatches('/^User \d+ not found$/');

// Substring match
$this->expectExceptionMessage('User');
```

---

## Exception Code Testing

### Testing Exception Code

```php
<?php

public function testExceptionCode() {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionCode(400);
    
    $handler = new ErrorHandler();
    $handler->validate(-5);  // Should throw with code 400
}
```

### Multiple Code Testing

```php
public function testNegativeValueCode() {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionCode(ERR_NEGATIVE_VALUE);  // Define constant
    
    $validator = new NumberValidator();
    $validator->validate(-5);
}

public function testEmptyValueCode() {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionCode(ERR_EMPTY_VALUE);
    
    $validator = new NumberValidator();
    $validator->validate('');
}
```

---

## Custom Exceptions

### Creating Custom Exceptions

```php
<?php

class UserNotFoundException extends Exception {
    public function __construct($userId) {
        parent::__construct("User {$userId} not found", 404);
    }
}

class InvalidOperationException extends Exception {
    public function __construct($message = '') {
        parent::__construct($message, 403);
    }
}
```

### Testing Custom Exceptions

```php
<?php

class UserServiceTest extends TestCase {
    
    public function testUserNotFound() {
        $this->expectException(UserNotFoundException::class);
        
        $service = new UserService();
        $service->getUser(9999);  // Should throw UserNotFoundException
    }
    
    public function testInvalidOperation() {
        $this->expectException(InvalidOperationException::class);
        $this->expectExceptionMessage('Admin access required');
        
        $service = new UserService();
        $service->deleteUser(1, $regularUser);
    }
}
```

---

## Multiple Exception Testing

### Different Conditions

```php
<?php

class RepositoryTest extends TestCase {
    
    public function testConnectionException() {
        $this->expectException(DatabaseException::class);
        
        $repo = new UserRepository();
        $repo->connect('invalid://host');
    }
    
    public function testQueryException() {
        $this->expectException(QueryException::class);
        
        $repo = new UserRepository();
        $repo->query('INVALID SQL');
    }
    
    public function testDeserializationException() {
        $this->expectException(JsonException::class);
        
        $repo = new UserRepository();
        $repo->parseJson('invalid json');
    }
}
```

### Testing Same Exception Different Conditions

```php
public function testInvalidEmailFormat() {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Invalid email format');
    
    $validator = new EmailValidator();
    $validator->validate('not-an-email');
}

public function testEmptyEmail() {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Email cannot be empty');
    
    $validator = new EmailValidator();
    $validator->validate('');
}

public function testLongEmail() {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Email too long');
    
    $validator = new EmailValidator();
    $validator->validate('a' . str_repeat('b', 300) . '@test.com');
}
```

---

## Exception Order Testing

### Sequential Exceptions

```php
<?php

public function testFirstStepException() {
    $this->expectException(InvalidArgumentException::class);
    
    $processor = new Processor();
    $processor->step1();  // Should throw first
}

public function testSecondStepException() {
    $this->expectException(RuntimeException::class);
    
    $processor = new Processor();
    $processor->step1();
    $processor->step2();  // Should throw second
}

public function testThirdStepException() {
    $this->expectException(LogicException::class);
    
    $processor = new Processor();
    $processor->step1();
    $processor->step2();
    $processor->step3();  // Should throw third
}
```

---

## Complete Examples

### Example 1: User Validation

```php
<?php

class UserValidatorTest extends TestCase {
    
    public function testEmptyNameException() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Name cannot be empty');
        
        $validator = new UserValidator();
        $validator->validate(['name' => '', 'email' => 'user@example.com']);
    }
    
    public function testInvalidEmailException() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        $validator = new UserValidator();
        $validator->validate(['name' => 'John', 'email' => 'not-email']);
    }
    
    public function testInvalidAgeException() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Age must be between 18 and 120');
        
        $validator = new UserValidator();
        $validator->validate(['name' => 'John', 'email' => 'john@test.com', 'age' => 15]);
    }
    
    public function testValidUser() {
        $validator = new UserValidator();
        $result = $validator->validate(['name' => 'John', 'email' => 'john@test.com', 'age' => 25]);
        $this->assertTrue($result);
    }
}
```

### Example 2: File Operations

```php
<?php

class FileHandlerTest extends TestCase {
    
    public function testFileNotFoundException() {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionCode(404);
        
        $handler = new FileHandler();
        $handler->read('/path/to/nonexistent/file.txt');
    }
    
    public function testPermissionDeniedException() {
        $this->expectException(PermissionDeniedException::class);
        $this->expectExceptionCode(403);
        
        $handler = new FileHandler();
        $handler->write('/protected/directory/file.txt', 'content');
    }
    
    public function testInvalidPathException() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid file path');
        
        $handler = new FileHandler();
        $handler->read('../../../etc/passwd');
    }
    
    public function testReadSuccess() {
        $handler = new FileHandler();
        $content = $handler->read(__FILE__);
        $this->assertNotEmpty($content);
    }
}
```

### Example 3: API Requests

```php
<?php

class ApiClientTest extends TestCase {
    
    public function testConnectionTimeoutException() {
        $this->expectException(ConnectionTimeoutException::class);
        
        $client = new ApiClient('http://invalid-timeout-api.local');
        $client->get('/users');
    }
    
    public function testUnauthorizedException() {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionCode(401);
        
        $client = new ApiClient('http://api.example.com');
        $client->setToken(null);
        $client->get('/admin/users');
    }
    
    public function testNotFoundException() {
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);
        
        $client = new ApiClient('http://api.example.com');
        $client->get('/users/999999');
    }
    
    public function testServerErrorException() {
        $this->expectException(ServerErrorException::class);
        $this->expectExceptionCode(500);
        
        $client = new ApiClient('http://api.example.com');
        $client->get('/broken-endpoint');
    }
}
```

---

## Key Takeaways

**Exception Testing Checklist:**

1. ✅ Use expectException() for basic testing
2. ✅ Test exception message with expectExceptionMessage()
3. ✅ Test exception code if relevant
4. ✅ Test custom exception types
5. ✅ Test multiple exception scenarios
6. ✅ Use pattern matching for flexible message testing
7. ✅ Document expected error conditions
8. ✅ Test both success and failure paths
9. ✅ Verify correct exception types
10. ✅ Test exception hierarchy if using custom exceptions

---

## See Also

- [Creating Unit Tests](2-create-unit-test.md)
- [Assertions](3-assertions.md)
- [Test Structure](2-create-unit-test.md)
