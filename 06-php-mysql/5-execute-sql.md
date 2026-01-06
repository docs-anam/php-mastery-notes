# Executing SQL Statements

## Overview

SQL execution is the core of database operations in PHP. You can execute queries using simple methods like `query()` for SELECT statements or `exec()` for INSERT/UPDATE/DELETE, or use prepared statements for better security and flexibility.

---

## Table of Contents

1. Basic SQL Execution Methods
2. Using query() Method
3. Using exec() Method
4. Using Prepared Statements
5. Binding Parameters
6. Getting Results
7. Error Handling
8. Performance Considerations
9. Complete Examples

---

## Basic SQL Execution Methods

### Three Main Approaches

```php
<?php
// 1. Direct query() - for SELECT
$result = $pdo->query("SELECT * FROM users");

// 2. Direct exec() - for INSERT/UPDATE/DELETE
$affected = $pdo->exec("INSERT INTO users (name, email) VALUES ('John', 'john@example.com')");

// 3. Prepared statements - for parameterized queries (RECOMMENDED)
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([1]);
?>
```

---

## Using query() Method

### Basic Usage

```php
<?php
// Simple SELECT query
$result = $pdo->query("SELECT * FROM users");

// Fetch all results
$users = $result->fetchAll();
foreach ($users as $user) {
    echo $user['name'];
}
?>
```

### Fetch Modes

```php
<?php
// PDO::FETCH_ASSOC - Associative array (recommended)
$result = $pdo->query("SELECT * FROM users", PDO::FETCH_ASSOC);
while ($row = $result->fetch()) {
    echo $row['name']; // Access by column name
}

// PDO::FETCH_NUM - Numeric array
$result = $pdo->query("SELECT * FROM users", PDO::FETCH_NUM);
while ($row = $result->fetch()) {
    echo $row[0]; // Access by index
}

// PDO::FETCH_BOTH - Both associative and numeric (default)
$result = $pdo->query("SELECT * FROM users", PDO::FETCH_BOTH);
while ($row = $result->fetch()) {
    echo $row['name'] . " = " . $row[0];
}

// PDO::FETCH_OBJ - Object
$result = $pdo->query("SELECT * FROM users", PDO::FETCH_OBJ);
while ($row = $result->fetch()) {
    echo $row->name;
}

// PDO::FETCH_CLASS - Map to class
$result = $pdo->query("SELECT * FROM users", PDO::FETCH_CLASS, 'User');
?>
```

### Error Handling with query()

```php
<?php
$result = $pdo->query("SELECT * FROM users");
if ($result === false) {
    echo "Query failed";
} else {
    while ($row = $result->fetch()) {
        // Process row
    }
}
?>
```

---

## Using exec() Method

### INSERT Statement

```php
<?php
// Simple insert
$sql = "INSERT INTO users (name, email) VALUES ('John', 'john@example.com')";
$affected = $pdo->exec($sql);

if ($affected > 0) {
    echo "$affected rows inserted";
    $lastId = $pdo->lastInsertId();
    echo "Last insert ID: $lastId";
}
?>
```

### UPDATE Statement

```php
<?php
$sql = "UPDATE users SET email = 'newemail@example.com' WHERE id = 1";
$affected = $pdo->exec($sql);

echo "$affected rows updated";
?>
```

### DELETE Statement

```php
<?php
$sql = "DELETE FROM users WHERE id = 1";
$affected = $pdo->exec($sql);

echo "$affected rows deleted";
?>
```

### Multiple Statements

```php
<?php
// Execute multiple statements at once
$sql = "
    INSERT INTO users (name, email) VALUES ('John', 'john@example.com');
    INSERT INTO users (name, email) VALUES ('Jane', 'jane@example.com');
    UPDATE users SET name = 'Johnny' WHERE id = 1;
";

$affected = $pdo->exec($sql);
echo "$affected rows affected total";
?>
```

### Error Handling with exec()

```php
<?php
try {
    $affected = $pdo->exec($sql);
    echo "$affected rows affected";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    // Get error info
    $errorInfo = $pdo->errorInfo();
    echo "SQL State: " . $errorInfo[0];
    echo "Error Code: " . $errorInfo[1];
    echo "Error Message: " . $errorInfo[2];
}
?>
```

---

## Using Prepared Statements

### Positional Parameters

```php
<?php
// Define query with placeholders
$sql = "INSERT INTO users (name, email, age) VALUES (?, ?, ?)";

// Prepare statement
$stmt = $pdo->prepare($sql);

// Execute with parameters
$stmt->execute(['John', 'john@example.com', 30]);

echo "Inserted: " . $stmt->rowCount() . " rows";
?>
```

### Named Parameters

```php
<?php
$sql = "INSERT INTO users (name, email, age) VALUES (:name, :email, :age)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':name' => 'John',
    ':email' => 'john@example.com',
    ':age' => 30,
]);

echo "Inserted: " . $stmt->rowCount() . " rows";
?>
```

### Reusing Statements

```php
<?php
$sql = "INSERT INTO users (name, email) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);

// Execute multiple times with different data
$users = [
    ['John', 'john@example.com'],
    ['Jane', 'jane@example.com'],
    ['Bob', 'bob@example.com'],
];

foreach ($users as $user) {
    $stmt->execute($user);
}

echo "All users inserted";
?>
```

---

## Binding Parameters

### Bind by Position

```php
<?php
$sql = "SELECT * FROM users WHERE id = ? AND status = ?";
$stmt = $pdo->prepare($sql);

// Bind individual parameters
$stmt->bindParam(1, $userId);
$stmt->bindParam(2, $status);

$userId = 1;
$status = 'active';

$stmt->execute();
?>
```

### Bind by Name

```php
<?php
$sql = "SELECT * FROM users WHERE id = :id AND status = :status";
$stmt = $pdo->prepare($sql);

// Bind individual parameters
$stmt->bindParam(':id', $userId);
$stmt->bindParam(':status', $status);

$userId = 1;
$status = 'active';

$stmt->execute();
?>
```

### Bind Values Directly

```php
<?php
$sql = "SELECT * FROM users WHERE id = :id AND status = :status";
$stmt = $pdo->prepare($sql);

// Bind values directly
$stmt->bindValue(':id', 1, PDO::PARAM_INT);
$stmt->bindValue(':status', 'active', PDO::PARAM_STR);

$stmt->execute();
?>
```

### Parameter Types

```php
<?php
$stmt = $pdo->prepare("INSERT INTO events (name, price, date, active) VALUES (?, ?, ?, ?)");

$stmt->execute([
    'Conference',           // String (default)
    99.99,                  // Float
    '2024-01-01',          // String (date)
    true,                   // Boolean
]);

// Or with explicit types
$stmt->bindParam(1, $name, PDO::PARAM_STR);
$stmt->bindParam(2, $price, PDO::PARAM_STR); // Or PDO::PARAM_INT
$stmt->bindParam(3, $date, PDO::PARAM_STR);
$stmt->bindParam(4, $active, PDO::PARAM_BOOL);
?>
```

---

## Getting Results

### Fetch Single Row

```php
<?php
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([1]);

// Fetch as associative array
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user) {
    echo $user['name'];
}

// Fetch as object
$user = $stmt->fetch(PDO::FETCH_OBJ);
if ($user) {
    echo $user->name;
}
?>
```

### Fetch All Rows

```php
<?php
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = ?");
$stmt->execute(['active']);

// Fetch all as associative arrays
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $user) {
    echo $user['name'];
}

// Fetch all as objects
$users = $stmt->fetchAll(PDO::FETCH_OBJ);
foreach ($users as $user) {
    echo $user->name;
}

// Fetch all indexed by a column
$users = $stmt->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);
?>
```

### Fetch Column

```php
<?php
$stmt = $pdo->prepare("SELECT name FROM users");
$stmt->execute();

// Fetch single column as array
$names = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
print_r($names); // ['John', 'Jane', 'Bob']

// Fetch single column value
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([1]);
$name = $stmt->fetchColumn();
?>
```

### Row Count

```php
<?php
$stmt = $pdo->prepare("UPDATE users SET status = ? WHERE age > ?");
$stmt->execute(['active', 18]);

$affected = $stmt->rowCount();
echo "$affected users updated";
?>
```

---

## Error Handling

### Exception Handling

```php
<?php
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([1]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    // Log the error
    error_log($e->getMessage());
}
?>
```

### Error Information

```php
<?php
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $pdo->exec("INVALID SQL");
} catch (PDOException $e) {
    // Get detailed error info
    $errorInfo = $pdo->errorInfo();
    
    echo "SQLSTATE: " . $errorInfo[0];  // Error code
    echo "Driver Code: " . $errorInfo[1]; // Driver-specific error code
    echo "Driver Message: " . $errorInfo[2]; // Error message
}
?>
```

### Silent Mode

```php
<?php
// Disable error reporting
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

$stmt = $pdo->prepare("INVALID SQL");
if ($stmt === false) {
    $errorInfo = $pdo->errorInfo();
    echo "Error: " . $errorInfo[2];
}
?>
```

---

## Performance Considerations

### Use Prepared Statements

```php
<?php
// SLOWER - Parsing query each time
for ($i = 0; $i < 1000; $i++) {
    $pdo->exec("INSERT INTO logs (message) VALUES ('Log $i')");
}

// FASTER - Prepare once, execute many times
$stmt = $pdo->prepare("INSERT INTO logs (message) VALUES (?)");
for ($i = 0; $i < 1000; $i++) {
    $stmt->execute(["Log $i"]);
}
?>
```

### Batch Inserts

```php
<?php
// Single multi-value insert is faster
$sql = "INSERT INTO users (name, email) VALUES " .
       "('John', 'john@example.com'), " .
       "('Jane', 'jane@example.com'), " .
       "('Bob', 'bob@example.com')";

$pdo->exec($sql);

// Or use prepared statement with loop
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
foreach ($users as $user) {
    $stmt->execute([$user['name'], $user['email']]);
}
?>
```

### Use Transactions for Multiple Statements

```php
<?php
try {
    $pdo->beginTransaction();
    
    // Multiple operations
    $stmt1 = $pdo->prepare("INSERT INTO accounts (user_id, amount) VALUES (?, ?)");
    $stmt1->execute([1, 100]);
    
    $stmt2 = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
    $stmt2->execute([100, 1]);
    
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Transaction failed: " . $e->getMessage();
}
?>
```

---

## Complete Examples

### User Registration

```php
<?php
class UserService {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function register($name, $email, $password) {
        try {
            $sql = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt->execute([$name, $email, $hashedPassword]);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Registration failed: " . $e->getMessage());
        }
    }
    
    public function getUserById($id) {
        $sql = "SELECT id, name, email, created_at FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAllUsers() {
        $sql = "SELECT id, name, email FROM users ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Usage
$db = new PDO("mysql:host=localhost;dbname=myapp", "root", "");
$userService = new UserService($db);

$userId = $userService->register('John', 'john@example.com', 'password123');
$user = $userService->getUserById($userId);
print_r($user);
?>
```

### Batch Operations

```php
<?php
class BatchOperations {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function insertMultipleUsers($users) {
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
            
            foreach ($users as $user) {
                $stmt->execute([$user['name'], $user['email']]);
            }
            
            $this->pdo->commit();
            return count($users);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Batch insert failed: " . $e->getMessage());
        }
    }
    
    public function updateMultipleUsers($updates) {
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
            
            foreach ($updates as $id => $status) {
                $stmt->execute([$status, $id]);
            }
            
            $this->pdo->commit();
            return $stmt->rowCount();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Batch update failed: " . $e->getMessage());
        }
    }
}

// Usage
$db = new PDO("mysql:host=localhost;dbname=myapp", "root", "");
$batch = new BatchOperations($db);

$users = [
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => 'Jane', 'email' => 'jane@example.com'],
];

$batch->insertMultipleUsers($users);
?>
```

---

## See Also

- [Accessing MySQL from PHP](3-access-mysql.md)
- [PDO Introduction](4-database-connection.md)
- [Query SQL Statements](6-query-sql.md)
- [SQL Injection Prevention](7-sql-injection.md)
- [Prepared Statements](8-prepare-statement.md)
