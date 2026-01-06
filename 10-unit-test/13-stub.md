# Mock Objects and Behavior Verification

## Overview

Mock objects verify that specific methods are called with expected arguments. They combine stub functionality with behavior assertions.

---

## Table of Contents

1. What are Mocks
2. Creating Mocks
3. Expecting Method Calls
4. Argument Verification
5. Expectation Builders
6. Multiple Expectations
7. Complete Examples

---

## What are Mocks

### Definition

```
Mock = Stub + Behavior Verification

Purpose:
- Replace dependencies
- Verify methods are called
- Verify arguments
- Verify call counts

When to Use:
- Need to verify method calls
- Testing interactions
- Ensuring side effects occur
- Validating call sequences
```

### Mock vs Stub

```php
// STUB: Only cares about return values
$stub = $this->createStub(UserRepository::class);
$stub->method('save')->willReturn(true);
// No verification of calls

// MOCK: Verifies method calls too
$mock = $this->createMock(UserRepository::class);
$mock->expects($this->once())->method('save');
// Asserts 'save' was called exactly once
```

---

## Creating Mocks

### Basic Mock

```php
<?php

public function testUserServiceSavesUser() {
    $repository = $this->createMock(UserRepository::class);
    
    // Expect save to be called
    $repository->expects($this->once())
        ->method('save');
    
    $service = new UserService($repository);
    $user = new User(['name' => 'John']);
    $service->saveUser($user);
    
    // Mock automatically verifies 'save' was called once
}
```

### Configuring Return and Expectations

```php
public function testFindAndSaveUser() {
    $repository = $this->createMock(UserRepository::class);
    
    $user = new User(['id' => 1, 'name' => 'John']);
    
    // Expect to be called, return value
    $repository->expects($this->once())
        ->method('findById')
        ->willReturn($user);
    
    $service = new UserService($repository);
    $found = $service->getUser(1);
    
    $this->assertEquals('John', $found->getName());
}
```

---

## Expecting Method Calls

### Call Counts

```php
<?php

// Called exactly once
$mock->expects($this->once())->method('save');

// Called exactly twice
$mock->expects($this->exactly(2))->method('delete');

// Called at least once
$mock->expects($this->atLeastOnce())->method('update');

// Called at least twice
$mock->expects($this->atLeast(2))->method('get');

// Never called
$mock->expects($this->never())->method('drop');

// Called any number of times
$mock->expects($this->any())->method('log');
```

### Verification Example

```php
public function testLoggingBehavior() {
    $logger = $this->createMock(Logger::class);
    
    $logger->expects($this->once())->method('info');
    $logger->expects($this->never())->method('error');
    
    $processor = new Processor($logger);
    $processor->process(['data']);
    
    // Mock verifies: info called once, error never called
}
```

---

## Argument Verification

### Exact Argument Matching

```php
<?php

public function testSaveWithSpecificData() {
    $repository = $this->createMock(UserRepository::class);
    
    $repository->expects($this->once())
        ->method('save')
        ->with($this->equalTo('John'), $this->equalTo('john@test.com'));
    
    $service = new UserService($repository);
    $service->saveUser('John', 'john@test.com');
}
```

### Argument Matchers

```php
// Any argument
->with($this->anything());

// String matching
->with($this->stringContains('test'));
->with($this->stringStartsWith('user_'));
->with($this->stringEndsWith('.txt'));

// Number matching
->with($this->greaterThan(0));
->with($this->lessThan(100));
->with($this->isType('string'));

// Array matching
->with($this->arrayHasKey('name'));
->with($this->contains('item'));

// Object matching
->with($this->isInstanceOf(User::class));
```

### Variable Arguments

```php
public function testWithMultipleArguments() {
    $mock = $this->createMock(Logger::class);
    
    $mock->expects($this->once())
        ->method('log')
        ->with(
            $this->equalTo('error'),      // First arg: exact match
            $this->stringContains('fail'), // Second arg: contains
            $this->isType('int')           // Third arg: type check
        );
    
    // Call with matching arguments
    $logger = $mock;
    $logger->log('error', 'Connection failed', 500);
}
```

---

## Expectation Builders

### Method Chaining

```php
<?php

public function testExpectationChain() {
    $mock = $this->createMock(Service::class);
    
    $mock->expects($this->once())
        ->method('process')
        ->with($this->equalTo('data'))
        ->willReturn(['success' => true]);
    
    $result = $mock->process('data');
    $this->assertTrue($result['success']);
}
```

### Multiple Expectations

```php
public function testMultipleMethodCalls() {
    $repository = $this->createMock(Repository::class);
    
    $repository->expects($this->once())
        ->method('begin');
    
    $repository->expects($this->exactly(3))
        ->method('save');
    
    $repository->expects($this->once())
        ->method('commit');
    
    // Service uses repository
    $service = new Service($repository);
    $service->saveMultiple([1, 2, 3]);
}
```

### Sequential Calls

```php
public function testCallSequence() {
    $mock = $this->createMock(Process::class);
    
    // First call
    $mock->expects($this->at(0))
        ->method('initialize');
    
    // Second call
    $mock->expects($this->at(1))
        ->method('execute');
    
    // Third call
    $mock->expects($this->at(2))
        ->method('finalize');
    
    $process = new Workflow($mock);
    $process->run();
}
```

---

## Complete Examples

### Example 1: Notification Service

```php
<?php

class NotificationServiceTest extends TestCase {
    
    public function testSendNotification() {
        $emailService = $this->createMock(EmailService::class);
        $smsService = $this->createMock(SmsService::class);
        
        // Expect email to be sent
        $emailService->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('user@example.com'),
                $this->stringContains('notification')
            );
        
        // SMS should not be sent
        $smsService->expects($this->never())
            ->method('send');
        
        $notifier = new Notifier($emailService, $smsService);
        $notifier->notifyViaEmail('user@example.com', 'Test notification');
    }
    
    public function testSendMultipleNotifications() {
        $emailService = $this->createMock(EmailService::class);
        
        // Expect 3 emails
        $emailService->expects($this->exactly(3))
            ->method('send');
        
        $notifier = new Notifier($emailService);
        $notifier->notifyAll([
            'user1@test.com',
            'user2@test.com',
            'user3@test.com',
        ], 'Announcement');
    }
}
```

### Example 2: Payment Processing

```php
<?php

class PaymentProcessorTest extends TestCase {
    
    public function testSuccessfulPayment() {
        $gateway = $this->createMock(PaymentGateway::class);
        $logger = $this->createMock(Logger::class);
        
        // Payment gateway returns success
        $gateway->expects($this->once())
            ->method('charge')
            ->with($this->equalTo(100))
            ->willReturn(['status' => 'success', 'id' => 'txn123']);
        
        // Logger records success
        $logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Payment successful'));
        
        $processor = new PaymentProcessor($gateway, $logger);
        $result = $processor->process(100);
        
        $this->assertTrue($result['success']);
    }
    
    public function testFailedPayment() {
        $gateway = $this->createMock(PaymentGateway::class);
        $logger = $this->createMock(Logger::class);
        
        // Gateway fails
        $gateway->expects($this->once())
            ->method('charge')
            ->willThrowException(new PaymentException('Card declined'));
        
        // Logger records error
        $logger->expects($this->once())
            ->method('error');
        
        $processor = new PaymentProcessor($gateway, $logger);
        
        $this->expectException(PaymentException::class);
        $processor->process(100);
    }
}
```

### Example 3: Database Transaction

```php
<?php

class TransactionHandlerTest extends TestCase {
    
    public function testTransactionCommit() {
        $connection = $this->createMock(DatabaseConnection::class);
        
        // Expect transaction lifecycle
        $connection->expects($this->at(0))
            ->method('beginTransaction');
        
        $connection->expects($this->once())
            ->method('execute')
            ->with($this->stringContains('INSERT'));
        
        $connection->expects($this->at(2))
            ->method('commit');
        
        $transaction = new Transaction($connection);
        $transaction->execute('INSERT INTO users VALUES (1)');
    }
    
    public function testTransactionRollback() {
        $connection = $this->createMock(DatabaseConnection::class);
        
        // Begin transaction
        $connection->expects($this->once())
            ->method('beginTransaction');
        
        // Query fails
        $connection->expects($this->once())
            ->method('execute')
            ->willThrowException(new DatabaseException('Query failed'));
        
        // Rollback on error
        $connection->expects($this->once())
            ->method('rollback');
        
        $transaction = new Transaction($connection);
        
        $this->expectException(DatabaseException::class);
        $transaction->execute('INVALID QUERY');
    }
}
```

### Example 4: Event Handling

```php
<?php

class EventDispatcherTest extends TestCase {
    
    public function testEventListenerCalled() {
        $listener1 = $this->createMock(EventListener::class);
        $listener2 = $this->createMock(EventListener::class);
        
        // Both listeners expect to be called
        $listener1->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(UserCreatedEvent::class));
        
        $listener2->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(UserCreatedEvent::class));
        
        $dispatcher = new EventDispatcher();
        $dispatcher->subscribe('user.created', $listener1);
        $dispatcher->subscribe('user.created', $listener2);
        
        $dispatcher->dispatch(new UserCreatedEvent(1));
    }
}
```

---

## Key Takeaways

**Mock Checklist:**

1. ✅ Use mocks for behavior verification
2. ✅ Expect specific method calls
3. ✅ Verify call counts (once, exactly, atLeast)
4. ✅ Match arguments precisely
5. ✅ Use argument matchers for flexibility
6. ✅ Combine expectations with return values
7. ✅ Verify call sequences with at()
8. ✅ Test interactions between objects
9. ✅ Don't over-mock (keep tests simple)
10. ✅ Use stubs for values, mocks for behavior

---

## See Also

- [Test Stubs](12-skip-test.md)
- [Creating Unit Tests](2-create-unit-test.md)
- [Test Fixtures](9-fixture.md)
