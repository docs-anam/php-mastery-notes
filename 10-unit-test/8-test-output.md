# Testing Output and Callbacks

## Overview

PHPUnit allows testing of output, console writes, and executing code through callbacks. This covers output buffering, output expectation, and verifying side effects.

---

## Table of Contents

1. Why Test Output
2. expectOutputString()
3. expectOutputRegex()
4. Output Buffering
5. Testing Callbacks
6. Side Effects Testing
7. Complete Examples

---

## Why Test Output

### When to Test Output

```
Output Testing Uses:
- Console commands verification
- Rendered HTML testing
- Error message verification
- Logging output verification
- API response verification

Example:
// Verify command produces correct output
// Ensure HTML rendering is correct
// Check formatted error messages
```

### Output vs Return Values

```php
// Traditional approach (preferred)
public function getData() {
    return ['name' => 'John'];  // Easy to test
}

// Output approach (harder to test)
public function printData() {
    echo "Name: John";  // Need output testing
}

// Best practice: Do both
public function getData() {
    return $data;
}

public function printData() {
    $data = $this->getData();
    echo "Name: " . $data['name'];
}
```

---

## expectOutputString()

### Exact Output Match

```php
<?php

public function testExactOutput() {
    $this->expectOutputString('Hello, World!');
    
    echo 'Hello, World!';
}

// Passes: Output matches exactly
```

### Whitespace Sensitivity

```php
public function testOutputWithWhitespace() {
    $this->expectOutputString("Line 1\nLine 2\n");
    
    echo "Line 1\n";
    echo "Line 2\n";
}

// Case sensitive
// Whitespace matters (spaces, newlines, tabs)
```

### Multiline Output

```php
public function testMultilineOutput() {
    $this->expectOutputString("Name: John\nEmail: john@example.com\n");
    
    $user = ['name' => 'John', 'email' => 'john@example.com'];
    foreach ($user as $key => $value) {
        printf("%s: %s\n", ucfirst($key), $value);
    }
}
```

---

## expectOutputRegex()

### Pattern Matching

```php
<?php

public function testOutputPattern() {
    $this->expectOutputRegex('/Hello, \w+!/');
    
    echo 'Hello, World!';
}

// Pattern must match somewhere in output
```

### Complex Patterns

```php
public function testErrorOutput() {
    $this->expectOutputRegex('/Error: Database connection failed/');
    
    $db = new Database();
    $db->connect('invalid://host');
}

public function testListOutput() {
    $this->expectOutputRegex('/Item 1.*Item 2.*Item 3/s');
    
    $list = ['Item 1', 'Item 2', 'Item 3'];
    foreach ($list as $item) {
        echo $item . "\n";
    }
}
```

### Pattern Flags

```php
// Case insensitive
$this->expectOutputRegex('/hello/i');

// Multiline (. matches newlines)
$this->expectOutputRegex('/start.*end/s');

// Both
$this->expectOutputRegex('/start.*end/is');
```

---

## Output Buffering

### How Output Works

```php
<?php

public function testOutput() {
    // 1. Output buffering starts automatically
    $this->expectOutputString('Test');
    
    // 2. Code runs and produces output
    echo 'Test';
    
    // 3. Output captured and verified
    // 4. Test passes if output matches
}

// No need to manually ob_start() or ob_end_clean()
```

### Multiple Output Calls

```php
public function testMultipleOutputs() {
    $this->expectOutputString('ABCDEF');
    
    echo 'A';
    echo 'B';
    echo 'C';
    echo 'D';
    echo 'E';
    echo 'F';
    
    // All output concatenated: ABCDEF
}
```

### Mixed Output and Return

```php
public function testOutputAndReturn() {
    $this->expectOutputString('Starting...');
    
    $result = myFunction();
    
    $this->assertEquals('expected', $result);
    echo 'Starting...';
}
```

---

## Testing Callbacks

### Callback Execution

```php
<?php

public function testCallbackExecution() {
    $called = false;
    $callback = function() use (&$called) {
        $called = true;
    };
    
    $handler = new Handler();
    $handler->execute($callback);
    
    $this->assertTrue($called);
}
```

### Callback Parameters

```php
public function testCallbackReceivesParameters() {
    $receivedValue = null;
    $callback = function($value) use (&$receivedValue) {
        $receivedValue = $value;
    };
    
    $processor = new Processor();
    $processor->process([1, 2, 3], $callback);
    
    $this->assertEquals(3, $receivedValue);
}
```

### Multiple Callbacks

```php
public function testCallbackOrder() {
    $order = [];
    
    $callback1 = function() use (&$order) {
        $order[] = 1;
    };
    
    $callback2 = function() use (&$order) {
        $order[] = 2;
    };
    
    $handler = new Handler();
    $handler->on('event1', $callback1);
    $handler->on('event2', $callback2);
    $handler->trigger('event1');
    $handler->trigger('event2');
    
    $this->assertEquals([1, 2], $order);
}
```

---

## Side Effects Testing

### State Changes

```php
<?php

class FileProcessorTest extends TestCase {
    
    public function testFileCreation() {
        $processor = new FileProcessor();
        $processor->processFile();
        
        // Verify side effect: file created
        $this->assertFileExists('output.txt');
        
        // Cleanup
        unlink('output.txt');
    }
    
    public function testDatabaseUpdate() {
        $processor = new Processor();
        $processor->updateDatabase();
        
        // Verify side effect: database changed
        $result = $this->db->query("SELECT COUNT(*) FROM records");
        $this->assertEquals(5, $result[0]['count']);
    }
}
```

### Output + Side Effects

```php
public function testProcessingOutput() {
    $this->expectOutputString("Processing...\nDone!\n");
    
    $processor = new Processor();
    $processor->process();
    
    // Also verify side effects
    $this->assertFileExists('output.txt');
}
```

---

## Complete Examples

### Example 1: Command Output

```php
<?php

class CommandTest extends TestCase {
    
    public function testHelpCommand() {
        $this->expectOutputRegex('/Usage: command \[options\]/');
        
        $command = new Command();
        $command->run(['--help']);
    }
    
    public function testVersionCommand() {
        $this->expectOutputString('Version 1.0.0');
        
        $command = new Command();
        $command->run(['--version']);
    }
    
    public function testListCommand() {
        $this->expectOutputRegex('/Item 1.*Item 2.*Item 3/s');
        
        $command = new ListCommand();
        $command->run([]);
    }
}
```

### Example 2: Formatter Output

```php
<?php

class FormatterTest extends TestCase {
    
    public function testCsvFormat() {
        $this->expectOutputString("name,email,age\njohn,john@test.com,25\n");
        
        $data = [
            ['name' => 'john', 'email' => 'john@test.com', 'age' => 25]
        ];
        
        $formatter = new CsvFormatter();
        $formatter->format($data);
    }
    
    public function testJsonFormat() {
        $expected = '{"items":[{"id":1,"name":"Item 1"}]}';
        $this->expectOutputString($expected);
        
        $data = ['items' => [['id' => 1, 'name' => 'Item 1']]];
        
        $formatter = new JsonFormatter();
        $formatter->format($data);
    }
    
    public function testTableFormat() {
        $this->expectOutputRegex('/\+----\+-----\+.*\| id \| name \|/s');
        
        $data = [['id' => 1, 'name' => 'John']];
        
        $formatter = new TableFormatter();
        $formatter->format($data);
    }
}
```

### Example 3: Logger Output

```php
<?php

class LoggerTest extends TestCase {
    
    public function testInfoLog() {
        $this->expectOutputRegex('/\[INFO\] User created successfully/');
        
        $logger = new Logger();
        $logger->info('User created successfully');
    }
    
    public function testErrorLog() {
        $this->expectOutputRegex('/\[ERROR\] Database connection failed/');
        
        $logger = new Logger();
        $logger->error('Database connection failed');
    }
    
    public function testWarningLog() {
        $this->expectOutputRegex('/\[WARNING\] Cache is stale/');
        
        $logger = new Logger();
        $logger->warning('Cache is stale');
    }
    
    public function testMultipleLogs() {
        $this->expectOutputRegex('/\[INFO\] Started.*\[INFO\] Completed/s');
        
        $logger = new Logger();
        $logger->info('Started');
        $logger->info('Processing');
        $logger->info('Completed');
    }
}
```

---

## Key Takeaways

**Output Testing Checklist:**

1. ✅ Use expectOutputString() for exact matches
2. ✅ Use expectOutputRegex() for pattern matching
3. ✅ Remember output is case-sensitive
4. ✅ Consider whitespace and newlines
5. ✅ Test output + assertions together
6. ✅ Verify callback execution
7. ✅ Test side effects separately
8. ✅ Clean up any created files/data
9. ✅ Use regex flags for flexibility
10. ✅ Avoid output in production code when possible

---

## See Also

- [Creating Unit Tests](2-create-unit-test.md)
- [Assertions](3-assertions.md)
- [Test Fixtures](9-fixture.md)
