# Database Transactions

## Overview

Database transactions ensure data consistency and integrity by grouping multiple SQL statements into atomic units. Either all statements execute successfully or none do, preventing partial updates that could corrupt data.

---

## Table of Contents

1. Transaction Basics
2. ACID Properties
3. Commit and Rollback
4. Isolation Levels
5. Handling Errors
6. Real-World Scenarios
7. Best Practices
8. Complete Examples

---

## Transaction Basics

### What is a Transaction?

A transaction is a sequence of database operations that either all succeed or all fail together.

```php
<?php
// Without transaction - DANGEROUS
$pdo->exec("UPDATE accounts SET balance = balance - 100 WHERE id = 1");
// If server crashes here, money is lost!
$pdo->exec("UPDATE accounts SET balance = balance + 100 WHERE id = 2");

// With transaction - SAFE
try {
    $pdo->beginTransaction();
    
    $pdo->exec("UPDATE accounts SET balance = balance - 100 WHERE id = 1");
    $pdo->exec("UPDATE accounts SET balance = balance + 100 WHERE id = 2");
    
    $pdo->commit(); // All succeed
} catch (Exception $e) {
    $pdo->rollBack(); // All fail
    echo "Transfer failed: " . $e->getMessage();
}
?>
```

### Transaction States

```
┌─────────────────────────────────┐
│      BEGIN TRANSACTION          │
└────────────┬────────────────────┘
             │
    ┌────────▼────────┐
    │  Execute SQL    │
    └────────┬────────┘
             │
    ┌────────▼────────┐
    │  Check Errors   │
    └────────┬────────┘
             │
      ┌──────┴──────┐
      │             │
  ┌───▼──┐    ┌────▼─┐
  │COMMIT│    │ROLLBACK
  └──────┘    └───────┘
```

---

## ACID Properties

### Atomicity

```php
<?php
// All or nothing - Either both updates happen or neither
try {
    $pdo->beginTransaction();
    
    // Update A
    $stmt1 = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
    $stmt1->execute([100, 1]);
    
    // Update B
    $stmt2 = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
    $stmt2->execute([100, 2]);
    
    $pdo->commit(); // Both succeed
} catch (Exception $e) {
    $pdo->rollBack(); // Neither happens
}
?>
```

### Consistency

```php
<?php
// Database moves from valid state to valid state
try {
    $pdo->beginTransaction();
    
    // Before transaction: Total = 1000
    // Update 1: Total becomes 900 (temporarily invalid if sees only this)
    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - 100 WHERE id = 1");
    $stmt->execute();
    
    // Update 2: Total becomes 1000 again (valid)
    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + 100 WHERE id = 2");
    $stmt->execute();
    
    // After transaction: Total = 1000 (valid)
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
}
?>
```

### Isolation

```php
<?php
// Concurrent transactions don't interfere
// Connection 1
$pdo1->beginTransaction();
$pdo1->prepare("UPDATE accounts SET balance = balance - 100 WHERE id = 1")->execute();
// Connection 2 doesn't see this yet

$pdo1->commit(); // Now connection 2 can see it

// Connection 2
$pdo2->query("SELECT * FROM accounts WHERE id = 1"); // Sees updated value
?>
```

### Durability

```php
<?php
// Once committed, data persists even after crashes
$pdo->beginTransaction();

$stmt = $pdo->prepare("INSERT INTO important_data (data) VALUES (?)");
$stmt->execute(['critical data']);

$pdo->commit(); // Committed to disk

// Even if server crashes immediately, data is safe
?>
```

---

## Commit and Rollback

### Basic Commit

```php
<?php
try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    $stmt->execute(['John', 'john@example.com']);
    
    // Successful - commit changes
    $pdo->commit();
    echo "User inserted successfully";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

### Basic Rollback

```php
<?php
try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([1]);
    
    // Check if we actually want to delete
    if ($stmt->rowCount() > 0) {
        // Undo the delete
        $pdo->rollBack();
        echo "Delete cancelled";
    } else {
        $pdo->commit();
    }
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
```

### Manual Rollback on Error

```php
<?php
try {
    $pdo->beginTransaction();
    
    // Insert operation
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
    $stmt->execute([1, 100]);
    $orderId = $pdo->lastInsertId();
    
    // If next operation fails, rollback entire transaction
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id) VALUES (?, ?)");
    $stmt->execute([$orderId, 999]); // May fail if product doesn't exist
    
    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Order creation failed, all changes reverted";
}
?>
```

---

## Isolation Levels

### Setting Isolation Level

```php
<?php
// Set isolation level
$pdo->exec("SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
$pdo->beginTransaction();
// ... operations
$pdo->commit();

// Or before transaction
$pdo->exec("SET TRANSACTION ISOLATION LEVEL SERIALIZABLE");
$pdo->beginTransaction();
// ... operations
$pdo->commit();
?>
```

### Isolation Levels

| Level | Dirty Reads | Non-Repeatable Reads | Phantom Reads | Performance |
|-------|------------|----------------------|--------------|------------|
| READ UNCOMMITTED | Yes | Yes | Yes | Fastest |
| READ COMMITTED | No | Yes | Yes | Good |
| REPEATABLE READ | No | No | Yes | Good |
| SERIALIZABLE | No | No | No | Slowest |

### Read Uncommitted (Avoid)

```php
<?php
// Can read uncommitted changes from other transactions
$pdo->exec("SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
$pdo->beginTransaction();

// This might return partially updated data
$stmt = $pdo->query("SELECT SUM(amount) FROM accounts");
$total = $stmt->fetchColumn();

$pdo->commit();
?>
```

### Read Committed (Recommended for Most)

```php
<?php
// Only read committed changes
$pdo->exec("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
$pdo->beginTransaction();

// Reads only finalized data
$stmt = $pdo->query("SELECT * FROM users WHERE status = 'active'");
$users = $stmt->fetchAll();

$pdo->commit();
?>
```

### Repeatable Read

```php
<?php
// Same query returns same results throughout transaction
$pdo->exec("SET TRANSACTION ISOLATION LEVEL REPEATABLE READ");
$pdo->beginTransaction();

$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$count1 = $stmt->fetchColumn(); // 100

// Other transaction inserts order
// But we still see 100

$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$count2 = $stmt->fetchColumn(); // Still 100

$pdo->commit();
?>
```

### Serializable

```php
<?php
// Complete isolation from other transactions
// Slowest but safest
$pdo->exec("SET TRANSACTION ISOLATION LEVEL SERIALIZABLE");
$pdo->beginTransaction();

$stmt = $pdo->query("SELECT * FROM accounts WHERE id = 1");
$account = $stmt->fetch();

// Other transaction cannot modify this account until we commit
$pdo->commit();
?>
```

---

## Handling Errors

### Try-Catch with Rollback

```php
<?php
try {
    $pdo->beginTransaction();
    
    // Operation 1
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, type) VALUES (?, ?, ?)");
    $stmt->execute([1, 100, 'deposit']);
    
    // Operation 2 - might fail
    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
    $stmt->execute([100, 1]);
    
    $pdo->commit();
    echo "Transaction successful";
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Transaction failed: " . $e->getMessage());
    echo "An error occurred. Please try again.";
}
?>
```

### Catching Specific Errors

```php
<?php
try {
    $pdo->beginTransaction();
    
    // Insert duplicate key - will fail
    $stmt = $pdo->prepare("INSERT INTO users (email, name) VALUES (?, ?)");
    $stmt->execute(['john@example.com', 'John']);
    
    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    
    // Check error code
    if ($e->getCode() == '23000') {
        echo "Email already exists";
    } else {
        echo "Database error: " . $e->getMessage();
    }
}
?>
```

### Savepoints (If Supported)

```php
<?php
try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("INSERT INTO users (name) VALUES (?)");
    $stmt->execute(['John']);
    $userId = $pdo->lastInsertId();
    
    // Create savepoint
    $pdo->exec("SAVEPOINT sp1");
    
    // Risky operation
    try {
        $stmt = $pdo->prepare("INSERT INTO profiles (user_id, premium) VALUES (?, ?)");
        $stmt->execute([$userId, true]);
    } catch (Exception $e) {
        // Rollback to savepoint, not entire transaction
        $pdo->exec("ROLLBACK TO SAVEPOINT sp1");
        echo "Profile creation skipped";
    }
    
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
}
?>
```

---

## Real-World Scenarios

### Money Transfer

```php
<?php
function transferMoney($pdo, $fromAccountId, $toAccountId, $amount) {
    try {
        $pdo->beginTransaction();
        
        // Debit from account
        $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$amount, $fromAccountId]);
        
        if ($stmt->rowCount() !== 1) {
            throw new Exception("Source account not found");
        }
        
        // Credit to account
        $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $toAccountId]);
        
        if ($stmt->rowCount() !== 1) {
            throw new Exception("Target account not found");
        }
        
        // Log transaction
        $stmt = $pdo->prepare("INSERT INTO transaction_log (from_id, to_id, amount, status, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$fromAccountId, $toAccountId, $amount, 'completed']);
        
        $pdo->commit();
        return ['success' => true, 'message' => 'Transfer successful'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Usage
$result = transferMoney($pdo, 1, 2, 100);
?>
```

### Order with Items

```php
<?php
function createOrder($pdo, $userId, $items) {
    try {
        $pdo->beginTransaction();
        
        // Calculate total
        $total = array_sum(array_column($items, 'price'));
        
        // Create order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, status, created_at) VALUES (?, ?, 'pending', NOW())");
        $stmt->execute([$userId, $total]);
        $orderId = $pdo->lastInsertId();
        
        // Add order items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        
        foreach ($items as $item) {
            $stmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
            
            // Update inventory
            $updateStmt = $pdo->prepare("UPDATE inventory SET quantity = quantity - ? WHERE product_id = ?");
            $updateStmt->execute([$item['quantity'], $item['product_id']]);
        }
        
        // Create payment record
        $stmt = $pdo->prepare("INSERT INTO payments (order_id, amount, status, created_at) VALUES (?, ?, 'pending', NOW())");
        $stmt->execute([$orderId, $total]);
        
        $pdo->commit();
        return ['success' => true, 'order_id' => $orderId];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Usage
$items = [
    ['product_id' => 1, 'quantity' => 2, 'price' => 29.99],
    ['product_id' => 2, 'quantity' => 1, 'price' => 99.99],
];

$result = createOrder($pdo, 123, $items);
?>
```

### User Registration with Validation

```php
<?php
function registerUser($pdo, $email, $name, $password) {
    try {
        $pdo->beginTransaction();
        
        // Check email doesn't exist
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("Email already registered");
        }
        
        // Create user
        $stmt = $pdo->prepare("INSERT INTO users (email, name, password_hash, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$email, $name, password_hash($password, PASSWORD_BCRYPT)]);
        $userId = $pdo->lastInsertId();
        
        // Create user profile
        $stmt = $pdo->prepare("INSERT INTO profiles (user_id, bio) VALUES (?, ?)");
        $stmt->execute([$userId, '']);
        
        // Create welcome email record
        $stmt = $pdo->prepare("INSERT INTO email_queue (user_id, type, status) VALUES (?, 'welcome', 'pending')");
        $stmt->execute([$userId]);
        
        // Create audit log
        $stmt = $pdo->prepare("INSERT INTO audit_log (user_id, action, details, created_at) VALUES (?, 'register', ?, NOW())");
        $stmt->execute([$userId, json_encode(['email' => $email, 'name' => $name])]);
        
        $pdo->commit();
        return ['success' => true, 'user_id' => $userId];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Usage
$result = registerUser($pdo, 'john@example.com', 'John Doe', 'password123');
?>
```

---

## Best Practices

### 1. Keep Transactions Short

```php
<?php
// GOOD - Quick transaction
$pdo->beginTransaction();
$stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
$stmt->execute(['active', 1]);
$pdo->commit();

// BAD - Long transaction (don't do heavy processing inside)
$pdo->beginTransaction();
// ... slow API calls ...
// ... complex calculations ...
$stmt = $pdo->prepare("INSERT INTO results VALUES (?)");
$stmt->execute([$result]);
$pdo->commit();
?>
```

### 2. Use Prepared Statements

```php
<?php
// GOOD
$pdo->beginTransaction();
$stmt = $pdo->prepare("INSERT INTO users (name) VALUES (?)");
$stmt->execute([$name]);
$pdo->commit();

// BAD - SQL injection risk
$pdo->beginTransaction();
$pdo->exec("INSERT INTO users (name) VALUES ('$name')");
$pdo->commit();
?>
```

### 3. Handle Errors Properly

```php
<?php
// GOOD
try {
    $pdo->beginTransaction();
    // ... operations
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    error_log($e->getMessage());
    throw $e; // Re-throw if needed
}

// BAD - Silent failures
$pdo->beginTransaction();
// ... operations that might fail ...
$pdo->commit();
?>
```

### 4. Avoid Deadlocks

```php
<?php
// GOOD - Consistent ordering
try {
    $pdo->beginTransaction();
    
    // Always lock lower ID first
    $id1 = min($account1, $account2);
    $id2 = max($account1, $account2);
    
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ? FOR UPDATE");
    $stmt->execute([$id1]);
    $stmt->execute([$id2]);
    
    // Update operations...
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
}
?>
```

### 5. Test Edge Cases

```php
<?php
// Test what happens when operations fail
class TransactionTest {
    public function testRollbackOnError() {
        // Verify nothing is inserted if error occurs mid-transaction
    }
    
    public function testInsufficientFunds() {
        // Verify transfer fails if source doesn't have enough balance
    }
    
    public function testConcurrency() {
        // Verify no race conditions with concurrent transactions
    }
}
?>
```

---

## See Also

- [Executing SQL Statements](5-execute-sql.md)
- [Prepared Statements](8-prepare-statement.md)
- [Auto-Increment Fields](10-auto-increment.md)
