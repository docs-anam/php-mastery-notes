# Incomplete Tests and Skipping Tests

## Overview

Mark tests as incomplete or skip them for various reasons: not implemented yet, require external dependencies, or temporary exclusion.

---

## Table of Contents

1. Incomplete Tests
2. Skipping Tests
3. Skip with Conditions
4. Skip Messages
5. Mark As Incomplete
6. Skip vs Incomplete
7. Complete Examples

---

## Incomplete Tests

### Marking Tests as Incomplete

```php
<?php

public function testFeatureNotYetImplemented() {
    $this->markTestIncomplete('This feature is not yet implemented');
}

// Test runs but doesn't pass/fail
// Shows as incomplete in output
```

### Using Exceptions

```php
public function testIncompleteFeature() {
    $this->markTestIncomplete(
        'New feature coming in version 2.0'
    );
}

// Alternative: throw directly
public function testAnotherIncomplete() {
    throw new \PHPUnit\Framework\IncompleteTestError(
        'Waiting for API documentation'
    );
}
```

### Checking Incomplete Count

```
Tests Run: 10
Incomplete: 3
Passed: 7
```

---

## Skipping Tests

### Basic Skip

```php
<?php

public function testSkippedFeature() {
    $this->markTestSkipped('Skipping for now');
}

// Test is not executed at all
// Still counted but marked as skipped
```

### Skip with Reason

```php
public function testRequiresExtension() {
    if (!extension_loaded('mongodb')) {
        $this->markTestSkipped('MongoDB extension not loaded');
    }
    
    // Test only runs if MongoDB is loaded
}
```

---

## Skip with Conditions

### Skip Based on Configuration

```php
<?php

public function testDatabaseFeature() {
    if (!$this->isDatabaseAvailable()) {
        $this->markTestSkipped('Database not available');
    }
    
    $result = $this->db->query("SELECT * FROM users");
    $this->assertNotEmpty($result);
}

private function isDatabaseAvailable() {
    try {
        return $this->db->ping();
    } catch (Exception $e) {
        return false;
    }
}
```

### Platform-Specific Skip

```php
public function testUnixOnlyFeature() {
    if (PHP_OS_FAMILY !== 'Linux') {
        $this->markTestSkipped('This test requires Linux');
    }
    
    // Test only runs on Linux
}

public function testWindowsOnlyFeature() {
    if (PHP_OS_FAMILY !== 'Windows') {
        $this->markTestSkipped('This test requires Windows');
    }
}
```

### Environment Skip

```php
public function testProductionAPI() {
    if (getenv('ENVIRONMENT') !== 'production') {
        $this->markTestSkipped('Only runs in production');
    }
    
    // Test only runs in production environment
}

public function testExternalService() {
    if (!$this->canReachService('https://api.example.com')) {
        $this->markTestSkipped('External service unreachable');
    }
}
```

---

## Skip Messages

### Meaningful Skip Reasons

```php
public function testAdvancedFeature() {
    $this->markTestSkipped(
        'Requires PHP >= 8.2, you have ' . PHP_VERSION
    );
}

public function testBetaFeature() {
    $this->markTestSkipped(
        'Beta feature, enable with BETA_FEATURES=true'
    );
}

public function testFutureFeature() {
    $this->markTestSkipped(
        'Scheduled for release 2.0.0'
    );
}
```

### Debug Skip Reasons

```
SKIPPED Tests:
testMongoDBFeature: MongoDB extension not loaded
testUnixFeature: This test requires Linux
testBetaAPI: Beta feature, enable with BETA_FEATURES=true
testExternalService: External service unreachable

Skipped: 4/50 tests
```

---

## Mark As Incomplete

### Explicit Incomplete

```php
<?php

public function testNewAlgorithm() {
    $this->markTestIncomplete(
        'Algorithm implementation in progress'
    );
}

public function testRefactoring() {
    $this->markTestIncomplete(
        'Refactoring code, tests need update'
    );
}
```

### Incomplete vs Skip Difference

```php
// INCOMPLETE: Not implemented yet, needs work
public function testNotReady() {
    $this->markTestIncomplete('To be implemented');
}

// SKIPPED: Works, but don't run now
public function testTemporarilyDisabled() {
    $this->markTestSkipped('Temporarily disabled');
}
```

---

## Skip vs Incomplete

### Comparison

```
Incomplete:
- Feature not yet implemented
- Test prepared but code missing
- Development in progress
- Marked with S with red background

Skipped:
- Feature exists but environment missing
- Test conditions not met
- Intentionally not running now
- Marked with S with yellow background
```

### Decision Tree

```
Should test run now?

NO - Is it implemented?
  - NO  → markTestIncomplete()
  - YES → Check environment
    - Ready? → RUN
    - NO   → markTestSkipped()

YES → RUN
```

---

## Complete Examples

### Example 1: Conditional Skips

```php
<?php

class DatabaseTestSuite extends TestCase {
    
    private function isDatabaseConfigured() {
        return !empty(getenv('DB_HOST'));
    }
    
    private function canConnectDatabase() {
        try {
            $db = new Database(getenv('DB_HOST'));
            return $db->ping();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function testDatabaseConnection() {
        if (!$this->isDatabaseConfigured()) {
            $this->markTestSkipped(
                'Database not configured. ' .
                'Set DB_HOST environment variable'
            );
        }
        
        if (!$this->canConnectDatabase()) {
            $this->markTestSkipped(
                'Cannot connect to database at ' . 
                getenv('DB_HOST')
            );
        }
        
        // Real test
        $db = new Database(getenv('DB_HOST'));
        $this->assertTrue($db->ping());
    }
    
    public function testDatabaseMigration() {
        if (!$this->isDatabaseConfigured()) {
            $this->markTestSkipped('Database not configured');
        }
        
        // Test database migrations
        $migrator = new Migrator(getenv('DB_HOST'));
        $result = $migrator->migrate();
        $this->assertTrue($result);
    }
}
```

### Example 2: Feature Flags

```php
<?php

class FeatureTests extends TestCase {
    
    private function isFeatureEnabled($feature) {
        return getenv("FEATURE_$feature") === 'true';
    }
    
    public function testBetaFeature() {
        if (!$this->isFeatureEnabled('BETA')) {
            $this->markTestSkipped('Beta feature not enabled');
        }
        
        $feature = new BetaFeature();
        $this->assertTrue($feature->isWorking());
    }
    
    public function testExperimentalAPI() {
        if (!$this->isFeatureEnabled('EXPERIMENTAL_API')) {
            $this->markTestSkipped('Experimental API not enabled');
        }
        
        $api = new ExperimentalAPI();
        $result = $api->call('/users');
        $this->assertIsArray($result);
    }
    
    public function testDeprecatedFunction() {
        if (!$this->isFeatureEnabled('ALLOW_DEPRECATED')) {
            $this->markTestSkipped('Deprecated features disabled');
        }
        
        $result = deprecatedFunction();
        $this->assertNotNull($result);
    }
}
```

### Example 3: Extension Requirements

```php
<?php

class ExtensionDependentTests extends TestCase {
    
    private function assertExtensionLoaded($ext) {
        if (!extension_loaded($ext)) {
            $this->markTestSkipped(
                "Extension '$ext' is not loaded. " .
                "Install it to run this test."
            );
        }
    }
    
    public function testJsonEncoding() {
        $this->assertExtensionLoaded('json');
        
        $data = ['name' => 'John'];
        $json = json_encode($data);
        $this->assertIsString($json);
    }
    
    public function testDatabaseExtension() {
        $this->assertExtensionLoaded('pdo_mysql');
        
        $pdo = new PDO('mysql:host=localhost;dbname=test');
        $this->assertNotNull($pdo);
    }
    
    public function testCurlRequests() {
        $this->assertExtensionLoaded('curl');
        
        $ch = curl_init();
        $this->assertIsResource($ch);
        curl_close($ch);
    }
    
    public function testImageProcessing() {
        $this->assertExtensionLoaded('gd');
        
        $image = imagecreate(100, 100);
        $this->assertIsResource($image);
        imagedestroy($image);
    }
}
```

### Example 4: Development Status

```php
<?php

class DevelopmentPhases extends TestCase {
    
    public function testImplementedFeature() {
        // Normal test, runs
        $calculator = new Calculator();
        $this->assertEquals(5, $calculator->add(2, 3));
    }
    
    public function testPlanningPhaseFeature() {
        // Not started, needs implementation
        $this->markTestIncomplete(
            'Feature in planning phase. ' .
            'Will implement in sprint 5'
        );
    }
    
    public function testInProgressFeature() {
        // Actively being developed
        $this->markTestIncomplete(
            'Currently implementing. ' .
            'Expected completion: tomorrow'
        );
    }
    
    public function testReviewPhaseFeature() {
        // Implemented but under code review
        $this->markTestIncomplete(
            'Under code review. ' .
            'PR #123 pending approval'
        );
    }
    
    public function testExternalServiceFeature() {
        // Depends on external service
        if (!$this->canReachExternalAPI()) {
            $this->markTestSkipped(
                'External API service not available'
            );
        }
        
        // Test external integration
        $result = $this->externalAPI->call('/data');
        $this->assertNotEmpty($result);
    }
    
    private function canReachExternalAPI() {
        try {
            $ch = curl_init('https://api.example.com/health');
            curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return $code === 200;
        } catch (Exception $e) {
            return false;
        }
    }
}
```

---

## Key Takeaways

**Incomplete/Skip Checklist:**

1. ✅ Use markTestIncomplete() for unimplemented features
2. ✅ Use markTestSkipped() for environment issues
3. ✅ Provide meaningful skip/incomplete reasons
4. ✅ Skip based on extensions, configuration
5. ✅ Skip for platform-specific tests
6. ✅ Document why tests are skipped
7. ✅ Monitor incomplete/skipped counts
8. ✅ Use feature flags for conditional tests
9. ✅ Clean up incomplete tests regularly
10. ✅ Clearly communicate skip reasons to team

---

## See Also

- [Creating Unit Tests](2-create-unit-test.md)
- [Attributes](4-attributes.md)
- [Test Dependencies](5-test-dependency.md)
