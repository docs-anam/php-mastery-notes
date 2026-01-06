# Destructor (__destruct) in PHP

## Table of Contents
1. [Overview](#overview)
2. [Basic Destructor](#basic-destructor)
3. [When Destructors Are Called](#when-destructors-are-called)
4. [Cleanup Operations](#cleanup-operations)
5. [Destructors with Inheritance](#destructors-with-inheritance)
6. [Practical Examples](#practical-examples)
7. [Common Mistakes](#common-mistakes)
8. [Best Practices](#best-practices)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

A destructor is a special method automatically called when an object is no longer needed. It's used to clean up resources like file handles, database connections, and memory. The destructor method is named `__destruct`. Unlike constructors, destructors cannot accept parameters or return values.

**Key Concepts:**
- Destructor is called automatically when object is destroyed
- Named `__destruct` (magic method)
- Used for cleanup/finalization
- No parameters allowed
- No return value
- Called at end of script or when `unset()` is called
- Useful for releasing external resources

---

## Basic Destructor

### Simple Destructor

```php
<?php
class FileHandler {
    private $filename;
    private $handle;
    
    public function __construct($filename) {
        $this->filename = $filename;
        $this->handle = fopen($filename, 'w');
        echo "File opened: $filename\n";
    }
    
    public function write($content) {
        fwrite($this->handle, $content);
    }
    
    // Destructor - automatically called when object is destroyed
    public function __destruct() {
        if (is_resource($this->handle)) {
            fclose($this->handle);
            echo "File closed: $this->filename\n";
        }
    }
}

// Destructor called at end of script
$file = new FileHandler('/tmp/test.txt');
$file->write('Hello, World!');
// Output at end:
// File closed: /tmp/test.txt
?>
```

### Destructor for Resource Cleanup

```php
<?php
class DatabaseConnection {
    private $connection;
    private $host;
    
    public function __construct($host) {
        $this->host = $host;
        $this->connect();
    }
    
    private function connect() {
        // Simulate connection
        $this->connection = true;
        echo "Connected to $this->host\n";
    }
    
    public function query($sql) {
        if ($this->connection) {
            echo "Executing: $sql\n";
        }
    }
    
    public function __destruct() {
        if ($this->connection) {
            $this->connection = false;
            echo "Disconnected from $this->host\n";
        }
    }
}

$db = new DatabaseConnection('localhost');
$db->query('SELECT * FROM users');
// Output at end:
// Disconnected from localhost
?>
```

---

## When Destructors Are Called

### Explicit Unset

```php
<?php
class Logger {
    private $logFile;
    
    public function __construct($file) {
        $this->logFile = fopen($file, 'a');
        echo "Logger initialized\n";
    }
    
    public function log($message) {
        fwrite($this->logFile, date('Y-m-d H:i:s') . ': ' . $message . "\n");
    }
    
    public function __destruct() {
        fclose($this->logFile);
        echo "Logger destroyed\n";
    }
}

$logger = new Logger('/tmp/app.log');
$logger->log('Application started');

// Explicitly destroy the object
unset($logger);
echo "After unset\n";
// Output:
// Logger destroyed
// After unset
?>
```

### End of Script

```php
<?php
class Timer {
    private $startTime;
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
        $this->startTime = microtime(true);
        echo "Timer '$name' started\n";
    }
    
    public function __destruct() {
        $elapsed = (microtime(true) - $this->startTime) * 1000;
        echo "Timer '$this->name' ended. Elapsed: {$elapsed}ms\n";
    }
}

$timer = new Timer('Process');
sleep(1);  // Simulate work
// Destructor automatically called at end of script
?>
```

### Out of Scope

```php
<?php
class Resource {
    private $id;
    
    public function __construct($id) {
        $this->id = $id;
        echo "Resource $id created\n";
    }
    
    public function __destruct() {
        echo "Resource $this->id destroyed\n";
    }
}

function useResource() {
    $resource = new Resource(1);
    echo "Inside function\n";
    // Destructor called when exiting function
}

echo "Before function\n";
useResource();
echo "After function\n";
// Output:
// Before function
// Resource 1 created
// Inside function
// Resource 1 destroyed
// After function
?>
```

---

## Cleanup Operations

### File Operations

```php
<?php
class CsvWriter {
    private $filename;
    private $file;
    private $rowCount = 0;
    
    public function __construct($filename) {
        $this->filename = $filename;
        $this->file = fopen($filename, 'w');
    }
    
    public function writeRow(array $data) {
        fputcsv($this->file, $data);
        $this->rowCount++;
    }
    
    public function __destruct() {
        if (is_resource($this->file)) {
            fclose($this->file);
            echo "CSV file '{$this->filename}' written with {$this->rowCount} rows\n";
        }
    }
}

$csv = new CsvWriter('/tmp/data.csv');
$csv->writeRow(['Name', 'Age', 'Email']);
$csv->writeRow(['John', 30, 'john@example.com']);
$csv->writeRow(['Jane', 28, 'jane@example.com']);
// File automatically closed and flushed
?>
```

### Database Transactions

```php
<?php
class Transaction {
    private $db;
    private $committed = false;
    
    public function __construct($db) {
        $this->db = $db;
        $this->db->beginTransaction();
        echo "Transaction started\n";
    }
    
    public function commit() {
        if (!$this->committed) {
            $this->db->commit();
            $this->committed = true;
            echo "Transaction committed\n";
        }
    }
    
    public function rollback() {
        if (!$this->committed) {
            $this->db->rollback();
            $this->committed = true;
            echo "Transaction rolled back\n";
        }
    }
    
    public function __destruct() {
        // Auto-rollback if not committed
        if (!$this->committed) {
            $this->rollback();
        }
    }
}

// Destructor ensures transaction is rolled back if not committed
// $transaction = new Transaction($db);
// ... operations ...
// Destructor called automatically
?>
```

### Cache Cleanup

```php
<?php
class CacheBuffer {
    private $buffer = [];
    private $maxSize = 1000;
    
    public function set($key, $value) {
        $this->buffer[$key] = $value;
        
        // Clear if buffer exceeds max size
        if (count($this->buffer) > $this->maxSize) {
            reset($this->buffer);
            unset($this->buffer[key($this->buffer)]);
        }
    }
    
    public function get($key) {
        return $this->buffer[$key] ?? null;
    }
    
    public function __destruct() {
        $size = count($this->buffer);
        echo "Cache buffer with $size items cleared\n";
        $this->buffer = [];
    }
}

$cache = new CacheBuffer();
$cache->set('user:1', ['name' => 'John']);
// Destructor clears buffer on exit
?>
```

---

## Destructors with Inheritance

### Parent and Child Destructors

```php
<?php
class Parent1 {
    protected $name;
    
    public function __construct($name) {
        $this->name = $name;
        echo "Parent::__construct($name)\n";
    }
    
    public function __destruct() {
        echo "Parent::__destruct() - Cleaning up $this->name\n";
    }
}

class Child1 extends Parent1 {
    private $value;
    
    public function __construct($name, $value) {
        parent::__construct($name);
        $this->value = $value;
        echo "Child::__construct($value)\n";
    }
    
    public function __destruct() {
        echo "Child::__destruct() - Cleaning up value: $this->value\n";
        parent::__destruct();  // Must explicitly call parent destructor
    }
}

$child = new Child1('Test', 42);
// Output when destroyed:
// Child::__destruct() - Cleaning up value: 42
// Parent::__destruct() - Cleaning up Test
?>
```

### Automatic Parent Destructor Call

```php
<?php
// PHP automatically calls parent destructor if child doesn't define one
class Connection {
    protected $host;
    
    public function __construct($host) {
        $this->host = $host;
    }
    
    public function __destruct() {
        echo "Closing connection to $this->host\n";
    }
}

class SecureConnection extends Connection {
    private $encrypted;
    
    public function __construct($host, $encrypted = true) {
        parent::__construct($host);
        $this->encrypted = $encrypted;
    }
    
    // No destructor defined - parent destructor still called automatically
}

$secure = new SecureConnection('secure.example.com');
// Parent destructor called automatically
?>
```

---

## Practical Examples

### Session Manager

```php
<?php
class SessionManager {
    private $sessionId;
    private $sessionFile;
    private $data = [];
    
    public function __construct() {
        $this->sessionId = session_id() ?: uniqid();
        $this->sessionFile = "/tmp/session_{$this->sessionId}.json";
        $this->load();
        echo "Session started: {$this->sessionId}\n";
    }
    
    private function load() {
        if (file_exists($this->sessionFile)) {
            $this->data = json_decode(file_get_contents($this->sessionFile), true) ?? [];
        }
    }
    
    public function set($key, $value) {
        $this->data[$key] = $value;
    }
    
    public function get($key) {
        return $this->data[$key] ?? null;
    }
    
    public function __destruct() {
        // Save session data when script ends
        file_put_contents($this->sessionFile, json_encode($this->data));
        echo "Session saved and closed: {$this->sessionId}\n";
    }
}

$session = new SessionManager();
$session->set('user_id', 123);
$session->set('username', 'john');
// Session data automatically saved
?>
```

### Lock Manager

```php
<?php
class FileLock {
    private $lockFile;
    private $locked = false;
    
    public function __construct($resource) {
        $this->lockFile = "/tmp/{$resource}.lock";
        $this->lock();
    }
    
    private function lock() {
        // Create lock file
        if (!file_exists($this->lockFile)) {
            touch($this->lockFile);
            $this->locked = true;
            echo "Lock acquired for {$this->lockFile}\n";
        }
    }
    
    public function isLocked() {
        return $this->locked;
    }
    
    public function __destruct() {
        if ($this->locked && file_exists($this->lockFile)) {
            unlink($this->lockFile);
            $this->locked = false;
            echo "Lock released for {$this->lockFile}\n";
        }
    }
}

$lock = new FileLock('critical_resource');
if ($lock->isLocked()) {
    echo "Executing critical section\n";
}
// Lock automatically released
?>
```

---

## Common Mistakes

### 1. Relying on Destructors for Critical Operations

```php
<?php
// ❌ Wrong: Critical operation might not happen
class DataSaver {
    private $data = [];
    
    public function add($item) {
        $this->data[] = $item;
    }
    
    public function __destruct() {
        // Destructors might not be called in some scenarios
        // Also output during shutdown might be ignored
        file_put_contents('data.json', json_encode($this->data));
    }
}

// ✓ Correct: Explicit save
class DataSaver {
    private $data = [];
    
    public function add($item) {
        $this->data[] = $item;
    }
    
    public function save() {
        file_put_contents('data.json', json_encode($this->data));
        echo "Data saved\n";
    }
    
    public function __destruct() {
        // Optional cleanup only
        $this->data = [];
    }
}

$saver = new DataSaver();
$saver->add('item1');
$saver->save();  // Explicitly save
?>
```

### 2. Not Calling Parent Destructor

```php
<?php
// ❌ Wrong: Parent cleanup is skipped
class BaseResource {
    protected $resource;
    
    public function __destruct() {
        echo "Cleaning up base resource\n";
    }
}

class ChildResource extends BaseResource {
    private $child;
    
    public function __destruct() {
        echo "Cleaning up child\n";
        // Parent destructor not called - incomplete cleanup
    }
}

// ✓ Correct: Call parent destructor
class ChildResource extends BaseResource {
    private $child;
    
    public function __destruct() {
        echo "Cleaning up child\n";
        parent::__destruct();  // Explicitly call parent
    }
}
?>
```

### 3. Accessing Partially Destroyed Objects

```php
<?php
// ❌ Wrong: Accessing destroyed objects
class Observer {
    private $handler;
    
    public function __construct($handler) {
        $this->handler = $handler;
    }
    
    public function __destruct() {
        // If $handler is destroyed first, this fails
        $this->handler->notify();  // Might be invalid
    }
}

// ✓ Correct: Check resource validity
class Observer {
    private $handler;
    
    public function __construct($handler) {
        $this->handler = $handler;
    }
    
    public function __destruct() {
        if ($this->handler !== null && is_callable([$this->handler, 'notify'])) {
            $this->handler->notify();
        }
    }
}
?>
```

---

## Best Practices

### 1. Keep Destructors Simple

```php
<?php
// ❌ Wrong: Complex logic in destructor
class Complex {
    public function __destruct() {
        // Complex validation, calculation, etc.
        $result = $this->complexComputation();
        $validated = $this->validate($result);
        $this->handleErrors($validated);
    }
}

// ✓ Correct: Simple cleanup only
class Simple {
    public function cleanup() {
        // Complex logic here
    }
    
    public function __destruct() {
        // Only simple cleanup
        $this->closeConnection();
        $this->flushBuffer();
    }
}
?>
```

### 2. Handle Exceptions in Destructors

```php
<?php
class SafeResource {
    private $resource;
    
    public function __destruct() {
        try {
            if ($this->resource) {
                // Cleanup that might throw
                $this->resource->close();
            }
        } catch (Exception $e) {
            // Log error but don't throw from destructor
            error_log('Cleanup error: ' . $e->getMessage());
        }
    }
}
?>
```

---

## Complete Working Example

```php
<?php
// Database Connection Pool with Cleanup

class DatabasePool {
    private $connections = [];
    private $maxConnections = 10;
    private $poolId;
    
    public function __construct() {
        $this->poolId = uniqid();
        echo "Connection pool created: {$this->poolId}\n";
    }
    
    public function getConnection($id = 0) {
        if (!isset($this->connections[$id]) && count($this->connections) < $this->maxConnections) {
            $this->connections[$id] = new PooledConnection($id);
        }
        return $this->connections[$id] ?? null;
    }
    
    public function closeConnection($id) {
        if (isset($this->connections[$id])) {
            $this->connections[$id]->close();
            unset($this->connections[$id]);
        }
    }
    
    public function getActiveCount() {
        return count($this->connections);
    }
    
    public function __destruct() {
        // Close all remaining connections
        foreach ($this->connections as $id => $connection) {
            $this->closeConnection($id);
        }
        echo "Connection pool destroyed: {$this->poolId}\n";
        echo "Total connections closed: " . count($this->connections) . "\n";
    }
}

class PooledConnection {
    private $id;
    private $connected = true;
    
    public function __construct($id) {
        $this->id = $id;
        echo "  Connection $id opened\n";
    }
    
    public function query($sql) {
        if ($this->connected) {
            echo "  Executing on connection $this->id: $sql\n";
        }
    }
    
    public function close() {
        if ($this->connected) {
            $this->connected = false;
            echo "  Connection $this->id closed\n";
        }
    }
    
    public function __destruct() {
        if ($this->connected) {
            $this->close();
        }
    }
}

// Usage
$pool = new DatabasePool();
$conn1 = $pool->getConnection(1);
$conn2 = $pool->getConnection(2);

$conn1->query('SELECT * FROM users');
$conn2->query('SELECT * FROM products');

$pool->closeConnection(1);

// All remaining connections automatically closed
unset($pool);
?>
```

---

## Cross-References

- **Related Topic: [Constructor](9-constructor.md)** - Object initialization counterpart
- **Related Topic: [Classes](2-class.md)** - Class structure and lifecycle
- **Related Topic: [Magic Methods](34-magic-function.md)** - Other special methods
- **Related Topic: [Exception Handling](38-exception.md)** - Error handling in cleanup
- **Related Topic: [Inheritance Basics](11-inheritance.md)** - Destructor inheritance
