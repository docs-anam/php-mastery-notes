# Test Suites and Test Organization

## Overview

Organize tests into logical suites for better structure, execution control, and reporting. Proper organization improves maintainability and clarity.

---

## Table of Contents

1. What are Test Suites
2. Test Suite Structure
3. Creating Test Suites
4. Running Specific Suites
5. Organizing Tests
6. Suite Organization Patterns
7. Complete Examples

---

## What are Test Suites

### Definition

```
Test Suite = Collection of related tests

Purpose:
- Group related tests
- Control test execution
- Generate separate reports
- Organize testing strategy

Types:
- Unit tests
- Integration tests
- Feature tests
- Performance tests
```

### Suite Hierarchy

```
All Tests
├── Unit Tests
│   ├── Model Tests
│   ├── Service Tests
│   └── Repository Tests
├── Integration Tests
│   ├── Database Tests
│   └── API Tests
└── Feature Tests
    ├── Auth Tests
    └── User Tests
```

---

## Test Suite Structure

### Configuration Structure

```xml
<testsuites>
    <testsuite name="Unit">
        <directory>tests/Unit</directory>
    </testsuite>
    
    <testsuite name="Integration">
        <directory>tests/Integration</directory>
    </testsuite>
    
    <testsuite name="Feature">
        <directory>tests/Feature</directory>
    </testsuite>
</testsuites>
```

### File Organization

```
tests/
├── bootstrap.php
├── Unit/
│   ├── Models/
│   │   └── UserTest.php
│   ├── Services/
│   │   └── AuthServiceTest.php
│   └── Repositories/
│       └── UserRepositoryTest.php
├── Integration/
│   ├── DatabaseTest.php
│   └── ApiTest.php
└── Feature/
    ├── AuthTest.php
    └── UserTest.php
```

---

## Creating Test Suites

### Single Directory Suite

```xml
<testsuites>
    <testsuite name="Unit">
        <directory>tests/Unit</directory>
    </testsuite>
</testsuites>
```

### Multiple Directories

```xml
<testsuites>
    <testsuite name="Core">
        <directory>tests/Unit</directory>
        <directory>tests/Integration</directory>
    </testsuite>
</testsuites>
```

### File Pattern Matching

```xml
<testsuites>
    <testsuite name="Unit">
        <directory suffix="Test.php">tests/Unit</directory>
    </testsuite>
    
    <testsuite name="Integration">
        <file>tests/Integration/DatabaseTest.php</file>
        <file>tests/Integration/ApiTest.php</file>
    </testsuite>
</testsuites>
```

### Excluding Files

```xml
<testsuites>
    <testsuite name="All">
        <directory suffix="Test.php">tests</directory>
        <exclude>tests/Performance</exclude>
        <exclude>tests/Experimental</exclude>
    </testsuite>
</testsuites>
```

---

## Running Specific Suites

### Command Line Execution

```bash
# Run all tests
phpunit

# Run specific suite
phpunit --testsuite Unit

# Run multiple suites
phpunit --testsuite Unit --testsuite Integration

# Run single file
phpunit tests/Unit/Models/UserTest.php

# Run with filter
phpunit --filter UserTest
```

### Using Composer Scripts

```json
{
    "scripts": {
        "test": "phpunit",
        "test:unit": "phpunit --testsuite Unit",
        "test:integration": "phpunit --testsuite Integration",
        "test:coverage": "phpunit --coverage-html coverage"
    }
}
```

---

## Organizing Tests

### By Layer

```xml
<testsuites>
    <!-- Controllers/Request Handlers -->
    <testsuite name="Presentation">
        <directory>tests/Presentation</directory>
    </testsuite>
    
    <!-- Services/Business Logic -->
    <testsuite name="Application">
        <directory>tests/Application</directory>
    </testsuite>
    
    <!-- Domain/Models -->
    <testsuite name="Domain">
        <directory>tests/Domain</directory>
    </testsuite>
    
    <!-- Data Access -->
    <testsuite name="Infrastructure">
        <directory>tests/Infrastructure</directory>
    </testsuite>
</testsuites>
```

### By Feature

```xml
<testsuites>
    <testsuite name="Authentication">
        <directory>tests/Features/Auth</directory>
    </testsuite>
    
    <testsuite name="Users">
        <directory>tests/Features/Users</directory>
    </testsuite>
    
    <testsuite name="Posts">
        <directory>tests/Features/Posts</directory>
    </testsuite>
    
    <testsuite name="Comments">
        <directory>tests/Features/Comments</directory>
    </testsuite>
</testsuites>
```

### By Type

```xml
<testsuites>
    <testsuite name="Unit">
        <directory>tests/Unit</directory>
    </testsuite>
    
    <testsuite name="Integration">
        <directory>tests/Integration</directory>
    </testsuite>
    
    <testsuite name="Acceptance">
        <directory>tests/Acceptance</directory>
    </testsuite>
    
    <testsuite name="Performance">
        <directory>tests/Performance</directory>
    </testsuite>
</testsuites>
```

---

## Suite Organization Patterns

### Development Suite (Fast)

```xml
<testsuites>
    <testsuite name="Dev">
        <directory suffix="Test.php">tests/Unit</directory>
        <exclude>tests/Integration</exclude>
        <exclude>tests/Performance</exclude>
    </testsuite>
</testsuites>
```

### CI/CD Suite (Comprehensive)

```xml
<testsuites>
    <testsuite name="CI">
        <directory suffix="Test.php">tests</directory>
        <exclude>tests/Performance</exclude>
        <exclude>tests/Manual</exclude>
    </testsuite>
</testsuites>
```

### Smoke Test Suite (Critical)

```xml
<testsuites>
    <testsuite name="Smoke">
        <file>tests/Unit/Models/UserTest.php</file>
        <file>tests/Unit/Services/AuthServiceTest.php</file>
        <file>tests/Integration/DatabaseTest.php</file>
    </testsuite>
</testsuites>
```

---

## Complete Examples

### Example 1: Complete Organization

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="tests/bootstrap.php"
         colors="true"
         verbose="true">
    
    <testsuites>
        <!-- Unit Tests -->
        <testsuite name="Unit">
            <directory suffix="Test.php">tests/Unit</directory>
        </testsuite>
        
        <!-- Integration Tests -->
        <testsuite name="Integration">
            <directory suffix="Test.php">tests/Integration</directory>
        </testsuite>
        
        <!-- Feature Tests -->
        <testsuite name="Feature">
            <directory suffix="Test.php">tests/Feature</directory>
        </testsuite>
        
        <!-- Combined Suite -->
        <testsuite name="All">
            <directory suffix="Test.php">tests</directory>
        </testsuite>
    </testsuites>
    
    <coverage>
        <include>
            <directory suffix=".php">src</directory>
        </include>
    </coverage>
</phpunit>
```

### Example 2: Feature-Based Organization

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="tests/bootstrap.php">
    
    <testsuites>
        <!-- User Management -->
        <testsuite name="Users">
            <directory>tests/Users</directory>
        </testsuite>
        
        <!-- Post Management -->
        <testsuite name="Posts">
            <directory>tests/Posts</directory>
        </testsuite>
        
        <!-- Comments -->
        <testsuite name="Comments">
            <directory>tests/Comments</directory>
        </testsuite>
        
        <!-- All Tests -->
        <testsuite name="All">
            <directory>tests</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

### Example 3: Layer-Based Organization

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="tests/bootstrap.php">
    
    <testsuites>
        <!-- API/HTTP Layer -->
        <testsuite name="API">
            <directory suffix="Test.php">tests/Http</directory>
        </testsuite>
        
        <!-- Application/Service Layer -->
        <testsuite name="Services">
            <directory suffix="Test.php">tests/Services</directory>
        </testsuite>
        
        <!-- Domain/Model Layer -->
        <testsuite name="Models">
            <directory suffix="Test.php">tests/Models</directory>
        </testsuite>
        
        <!-- Data/Repository Layer -->
        <testsuite name="Repositories">
            <directory suffix="Test.php">tests/Repositories</directory>
        </testsuite>
        
        <!-- Integration Tests -->
        <testsuite name="Integration">
            <directory suffix="Test.php">tests/Integration</directory>
        </testsuite>
        
        <!-- Everything -->
        <testsuite name="All">
            <directory suffix="Test.php">tests</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

### Example 4: Speed-Based Suites

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="tests/bootstrap.php">
    
    <testsuites>
        <!-- Fast tests - Run during development -->
        <testsuite name="Fast">
            <directory suffix="Test.php">tests/Unit</directory>
        </testsuite>
        
        <!-- Medium speed - Run before commit -->
        <testsuite name="Medium">
            <directory suffix="Test.php">tests/Unit</directory>
            <directory suffix="Test.php">tests/Feature</directory>
        </testsuite>
        
        <!-- All tests - Run in CI/CD -->
        <testsuite name="Full">
            <directory suffix="Test.php">tests</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

---

## Running Suite Examples

```bash
# Fast unit tests during development
composer run test:fast
# or
phpunit --testsuite Unit

# Medium speed tests before commit
composer run test:medium
# or
phpunit --testsuite Unit --testsuite Feature

# Full test suite in CI
composer run test:full
# or
phpunit

# Specific feature tests
phpunit --testsuite Users

# Multiple suites
phpunit --testsuite Unit --testsuite Integration

# With coverage
phpunit --testsuite All --coverage-html coverage
```

---

## Key Takeaways

**Test Suite Checklist:**

1. ✅ Organize tests into logical suites
2. ✅ Create suites by layer, feature, or type
3. ✅ Use meaningful suite names
4. ✅ Configure phpunit.xml properly
5. ✅ Separate fast and slow tests
6. ✅ Run suites independently
7. ✅ Document suite purposes
8. ✅ Use composer scripts for common commands
9. ✅ Exclude slow/experimental tests from main
10. ✅ Run appropriate suite for context

---

## See Also

- [PHPUnit Configuration](14-mock-object.md)
- [Creating Unit Tests](2-create-unit-test.md)
- [Test Dependencies](5-test-dependency.md)
