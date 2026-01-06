# PHPUnit Configuration

## Overview

PHPUnit configuration through phpunit.xml controls test behavior, settings, coverage analysis, and test suite organization.

---

## Table of Contents

1. phpunit.xml Structure
2. Test Suite Configuration
3. Coverage Settings
4. Bootstrap and Autoloading
5. Output Configuration
6. PHP Settings
7. Extensions
8. Complete Examples

---

## phpunit.xml Structure

### Basic Configuration

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns="https://schema.phpunit.de/9.5"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="https://schema.phpunit.de/9.5 https://schema.phpunit.de/9.5/phpunit.xsd"
         colors="true"
         beStrictAboutTestsThatDoNotTestAnything="true"
         beStrictAboutOutputDuringTests="true"
         beStrictAboutTestSize="true">
    
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

### Root Element Attributes

```xml
<!-- Display colors -->
colors="true"

<!-- Fail if test doesn't run assertions -->
beStrictAboutTestsThatDoNotTestAnything="true"

<!-- Fail if test produces output -->
beStrictAboutOutputDuringTests="true"

<!-- Fail if test size inconsistency -->
beStrictAboutTestSize="true"

<!-- Require version -->
requireCoverageMetric="true"

<!-- Stop on first failure -->
stopOnFailure="true"

<!-- Process isolation -->
processIsolation="true"

<!-- Parallel execution -->
parallel="true"

<!-- Number of processes -->
processesCount="4"

<!-- Fail on risky test -->
failOnRisky="true"

<!-- Fail on deprecation -->
failOnDeprecation="true"
```

---

## Test Suite Configuration

### Multiple Test Suites

```xml
<phpunit>
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
        
        <testsuite name="All">
            <directory>tests</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

### Running Specific Suite

```bash
# Run specific suite
phpunit --testsuite Unit

# Run all suites
phpunit
```

### Include/Exclude Files

```xml
<testsuite name="Unit">
    <directory suffix="Test.php">tests/Unit</directory>
    <exclude>tests/Unit/SkipTest.php</exclude>
</testsuite>
```

---

## Coverage Settings

### Code Coverage Configuration

```xml
<coverage processUncoveredFiles="true">
    <include>
        <directory suffix=".php">src</directory>
    </include>
    
    <exclude>
        <directory>src/Migrations</directory>
        <directory>src/Stubs</directory>
    </exclude>
</coverage>
```

### Coverage Reports

```xml
<coverage>
    <!-- HTML Report -->
    <report>
        <html outputDirectory="coverage/html" />
    </report>
    
    <!-- Text Report -->
    <report>
        <text outputFile="coverage.txt" />
    </report>
    
    <!-- Clover Format -->
    <report>
        <clover outputFile="coverage.xml" />
    </report>
    
    <!-- PHP Format -->
    <report>
        <php outputFile="coverage.php" />
    </report>
</coverage>
```

### Coverage Thresholds

```xml
<coverage>
    <report>
        <html outputDirectory="coverage" />
    </report>
    
    <!-- Fail if coverage below threshold -->
    <report>
        <text outputFile="php://stdout" lowUpperBound="50" highLowerBound="80" />
    </report>
</coverage>
```

### Running Coverage

```bash
# Generate HTML report
phpunit --coverage-html coverage

# Generate text report
phpunit --coverage-text

# With threshold
phpunit --coverage-html coverage --coverage-clover coverage.xml
```

---

## Bootstrap and Autoloading

### Bootstrap File

```xml
<phpunit bootstrap="tests/bootstrap.php">
    <!-- Runs before any tests -->
</phpunit>
```

### Bootstrap Example

```php
<?php
// tests/bootstrap.php

// Define constants
define('TESTING', true);

// Setup error handling
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Load Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Setup database
$database = require __DIR__ . '/fixtures/database.php';

// Setup environment
putenv('APP_ENV=testing');
```

---

## Output Configuration

### Display Options

```xml
<phpunit colors="true"
         verbose="true"
         stopOnFailure="true"
         failOnWarning="true"
         failOnRisky="true">
</phpunit>
```

### Output Formats

```bash
# Default (text)
phpunit

# Verbose
phpunit --verbose

# TAP (Test Anything Protocol)
phpunit --tap

# Testdox (documentation)
phpunit --testdox

# JSON
phpunit --log-json report.json
```

---

## PHP Settings

### Configure PHP

```xml
<php>
    <!-- Constants -->
    <const name="APP_ENV" value="testing" />
    <const name="DATABASE_URL" value="sqlite::memory:" />
    
    <!-- Variables -->
    <var name="foo" value="bar" />
    
    <!-- Environment variables -->
    <env name="TESTING" value="true" />
    <env name="DEBUG" value="true" />
    
    <!-- INI settings -->
    <ini name="display_errors" value="1" />
    <ini name="error_reporting" value="-1" />
    <ini name="memory_limit" value="256M" />
    <ini name="date.timezone" value="UTC" />
</php>
```

### Accessing Configuration

```php
<?php

public function testConstants() {
    // Access defined constants
    $env = APP_ENV;  // "testing"
    $db = DATABASE_URL;  // "sqlite::memory:"
}

public function testEnvironment() {
    // Access environment variables
    $testing = getenv('TESTING');  // "true"
}
```

---

## Extensions

### Using Extensions

```xml
<extensions>
    <!-- Database extension -->
    <extension class="PHPUnit\Extension\Database\Extension" />
    
    <!-- Listeners -->
    <listeners>
        <listener class="CustomListener" file="tests/Listeners/CustomListener.php">
            <arguments>
                <argument name="param">value</argument>
            </arguments>
        </listener>
    </listeners>
</extensions>
```

---

## Complete Examples

### Example 1: Complete Configuration

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns="https://schema.phpunit.de/9.5"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="https://schema.phpunit.de/9.5 https://schema.phpunit.de/9.5/phpunit.xsd"
         colors="true"
         verbose="true"
         beStrictAboutTestsThatDoNotTestAnything="true"
         beStrictAboutOutputDuringTests="true"
         failOnRisky="true"
         bootstrap="tests/bootstrap.php">
    
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory suffix="Test.php">tests/Integration</directory>
        </testsuite>
    </testsuites>
    
    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">src</directory>
        </include>
        <exclude>
            <directory>src/Migrations</directory>
        </exclude>
        <report>
            <html outputDirectory="coverage/html" />
            <clover outputFile="coverage.xml" />
        </report>
    </coverage>
    
    <php>
        <const name="APP_ENV" value="testing" />
        <env name="DATABASE_URL" value="sqlite::memory:" />
        <ini name="display_errors" value="1" />
        <ini name="date.timezone" value="UTC" />
    </php>
</phpunit>
```

### Example 2: Development Configuration

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit colors="true"
         verbose="true"
         stopOnFailure="true"
         bootstrap="tests/bootstrap.php">
    
    <testsuites>
        <testsuite name="Quick">
            <directory suffix="Test.php">tests/Unit</directory>
        </testsuite>
    </testsuites>
    
    <php>
        <env name="TESTING" value="true" />
    </php>
</phpunit>
```

### Example 3: CI/CD Configuration

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit beStrictAboutTestsThatDoNotTestAnything="true"
         beStrictAboutOutputDuringTests="true"
         failOnRisky="true"
         failOnWarning="true"
         colors="false"
         bootstrap="tests/bootstrap.php">
    
    <testsuites>
        <testsuite name="All">
            <directory suffix="Test.php">tests</directory>
        </testsuite>
    </testsuites>
    
    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">src</directory>
        </include>
        <exclude>
            <directory>src/Console</directory>
        </exclude>
        <report>
            <clover outputFile="coverage.xml" />
        </report>
    </coverage>
    
    <php>
        <ini name="error_reporting" value="-1" />
    </php>
</phpunit>
```

---

## Key Takeaways

**Configuration Checklist:**

1. ✅ Create phpunit.xml in project root
2. ✅ Configure test suites
3. ✅ Set bootstrap file
4. ✅ Configure code coverage
5. ✅ Set PHP environment variables
6. ✅ Define constants for tests
7. ✅ Configure strict mode
8. ✅ Set database connection
9. ✅ Configure output format
10. ✅ Document custom configuration

---

## See Also

- [Creating Unit Tests](2-create-unit-test.md)
- [Test Suites](16-test-suite.md)
- [Attributes](4-attributes.md)
