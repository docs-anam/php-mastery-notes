# Prepared Statements

## Overview

Prepared statements are a mechanism for safely executing SQL queries. They separate SQL logic from data, preventing SQL injection attacks and improving performance by allowing the database to optimize query execution.

---

## Table of Contents

1. What are Prepared Statements?
2. How They Work
3. Positional Parameters
4. Named Parameters
5. Binding Parameters
6. Error Handling
7. Performance Benefits
8. Complete Examples

---

## What are Prepared Statements?

Prepared statements (also called parameterized queries) are SQL queries with placeholders for data values. The SQL logic is sent to the database separately from the data.

### Basic Concept

```php
<?php
// Traditional approach (VULNERABLE)
$email = $_POST['email'];
$query = "SELECT * FROM users WHERE email = '$email'";
// Attacker can inject SQL code

// Prepared statement approach (SAFE)
$query = "SELECT * FROM users WHERE email = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$email]);
// Attacker's input is treated as data, not SQL
?>
```

### Key Benefits

1. **Security**: Prevents SQL injection attacks
2. **Performance**: Queries can be cached and reused
3. **Clarity**: Separates SQL logic from data
4. **Type Handling**: Database handles type conversion
5. **Reliability**: Reduces parsing overhead

---

## How They Work

### Execution Flow

```
1. PHP Application sends query template with placeholders
2. Database prepares/compiles the query
3. Application sends data values separately
4. Database executes compiled query with data
5. Results returned to application
```

### Process Visualization

```php
<?php
// Step 1: Prepare (send query structure)
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND status = ?");

// Step 2: Execute (send data separately)
$stmt->execute([123, 'active']);

// Step 3: Fetch results
$users = $stmt->fetchAll();

// The database never sees the data as SQL code
?>
```

---

## Positional Parameters

### Question Mark Placeholders

```php
<?php
// Define placeholders as question marks
$sql = "INSERT INTO users (name, email, age) VALUES (?, ?, ?)";

// Prepare statement
$stmt = $pdo->prepare($sql);

// Execute with values in same order
$stmt->execute(['John', 'john@example.com', 30]);
?>
```

### Multiple Values with Same Column

```php
<?php
// Select multiple records
$sql = "SELECT * FROM users WHERE id = ? OR id = ? OR id = ?";
$stmt = $pdo->prepare($sql);

// Values correspond to placeholders in order
$stmt->execute([1, 5, 10]);
$users = $stmt->fetchAll();
?>
```

### Counting Placeholders

```php
<?php
// When building dynamic queries, track parameters
$placeholders = [];
$params = [];

$sql = "INSERT INTO products (name, price, category) VALUES ";
$values = [];

foreach ($productData as $product) {
    $values[] = "(?, ?, ?)";
    $params[] = $product['name'];
    $params[] = $product['price'];
    $params[] = $product['category'];
}

$sql .= implode(", ", $values);

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
?>
```

---

## Named Parameters

### Named Placeholders

```php
<?php
// Use column names as placeholders
$sql = "INSERT INTO users (name, email, age) VALUES (:name, :email, :age)";

// Prepare statement
$stmt = $pdo->prepare($sql);

// Execute with named array
$stmt->execute([
    ':name' => 'John',
    ':email' => 'john@example.com',
    ':age' => 30,
]);
?>
```

### Named Parameters Benefits

```php
<?php
// More readable
$sql = "SELECT * FROM orders 
         WHERE user_id = :user_id 
         AND status = :status 
         AND total > :min_total";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':user_id' => 123,
    ':status' => 'completed',
    ':min_total' => 100,
]);

// Clear what each parameter represents!
?>
```

### Reusing Named Parameters

```php
<?php
// Use same parameter in multiple places
$sql = "SELECT * FROM users 
         WHERE email = :email 
         OR recovery_email = :email";

$stmt = $pdo->prepare($sql);

// Only need to provide value once
$stmt->execute([
    ':email' => 'john@example.com',
]);

// The email is used in both places
?>
```

---

## Binding Parameters

### Individual Parameter Binding

```php
<?php
$sql = "SELECT * FROM users WHERE id = ? AND status = ?";
$stmt = $pdo->prepare($sql);

// Bind parameters individually
$stmt->bindParam(1, $userId);
$stmt->bindParam(2, $status);

// Set values
$userId = 123;
$status = 'active';

// Execute
$stmt->execute();
?>
```

### Named Parameter Binding

```php
<?php
$sql = "SELECT * FROM orders WHERE user_id = :user_id AND status = :status";
$stmt = $pdo->prepare($sql);

// Bind named parameters
$stmt->bindParam(':user_id', $userId);
$stmt->bindParam(':status', $status);

// Set values
$userId = 123;
$status = 'pending';

// Execute
$stmt->execute();
?>
```

### Binding by Type

```php
<?php
$sql = "INSERT INTO events (title, start_date, attendees, active) VALUES (?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);

// Bind with explicit type
$title = "Conference";
$date = "2024-06-15";
$attendees = 500;
$active = true;

$stmt->bindParam(1, $title, PDO::PARAM_STR);
$stmt->bindParam(2, $date, PDO::PARAM_STR);
$stmt->bindParam(3, $attendees, PDO::PARAM_INT);
$stmt->bindParam(4, $active, PDO::PARAM_BOOL);

$stmt->execute();
?>
```

### Parameter Type Options

```php
<?php
// PDO::PARAM_STR - String
$stmt->bindParam(1, $value, PDO::PARAM_STR);

// PDO::PARAM_INT - Integer
$stmt->bindParam(1, $value, PDO::PARAM_INT);

// PDO::PARAM_BOOL - Boolean
$stmt->bindParam(1, $value, PDO::PARAM_BOOL);

// PDO::PARAM_NULL - NULL value
$stmt->bindParam(1, $value, PDO::PARAM_NULL);

// PDO::PARAM_LOB - Large object (binary data)
$stmt->bindParam(1, $value, PDO::PARAM_LOB);
?>
```

### Binding Values (immutable)

```php
<?php
$sql = "SELECT * FROM users WHERE id = ? AND email = ?";
$stmt = $pdo->prepare($sql);

// bindValue (value is copied, doesn't change after)
$stmt->bindValue(1, 123, PDO::PARAM_INT);
$stmt->bindValue(2, 'john@example.com', PDO::PARAM_STR);

$stmt->execute();

// Even if variables change, bound values stay the same
$userId = 456;
$email = 'jane@example.com';
// Query still uses 123 and john@example.com
?>
```

---

## Error Handling

### Exception Handling with Prepared Statements

```php
<?php
try {
    $sql = "INSERT INTO users (email, name) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    
    // This will throw exception if duplicate email
    $stmt->execute([$email, $name]);
    
    echo "User inserted: " . $pdo->lastInsertId();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    
    // Get error details
    $errorInfo = $e->errorInfo;
    echo "SQL State: " . $errorInfo[0];
    echo "Error Code: " . $errorInfo[1];
}
?>
```

### Checking Execution Status

```php
<?php
$sql = "UPDATE users SET status = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);

$result = $stmt->execute(['active', 123]);

if ($result) {
    echo "Rows affected: " . $stmt->rowCount();
} else {
    $errorInfo = $stmt->errorInfo();
    echo "Error: " . $errorInfo[2];
}
?>
```

### Debugging Prepared Statements

```php
<?php
// Check prepared statement
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");

if ($stmt === false) {
    $errorInfo = $pdo->errorInfo();
    echo "Prepare failed: " . $errorInfo[2];
} else {
    $result = $stmt->execute([123]);
    
    if ($result === false) {
        $errorInfo = $stmt->errorInfo();
        echo "Execute failed: " . $errorInfo[2];
    }
}
?>
```

---

## Performance Benefits

### Query Caching

```php
<?php
// Prepare once, execute many times
// Database caches the prepared query

$stmt = $pdo->prepare("INSERT INTO logs (user_id, action, timestamp) VALUES (?, ?, NOW())");

// Insert 10,000 log entries
for ($i = 0; $i < 10000; $i++) {
    $stmt->execute([$userId, "action_$i"]);
}

// Much faster than parsing query 10,000 times
?>
```

### Reusing Prepared Statements

```php
<?php
// Create statement once
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");

// Use it multiple times
$users = [];
foreach ($emailList as $email) {
    $stmt->execute([$email]);
    $users[$email] = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
```

### Performance Comparison

```php
<?php
// Slower - Parse query each time
$start = microtime(true);
for ($i = 0; $i < 1000; $i++) {
    $email = $emails[$i];
    $result = $pdo->query("SELECT * FROM users WHERE email = '$email'");
}
$timeWithoutPrepared = microtime(true) - $start;

// Faster - Prepare once
$start = microtime(true);
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
for ($i = 0; $i < 1000; $i++) {
    $stmt->execute([$emails[$i]]);
}
$timeWithPrepared = microtime(true) - $start;

echo "Without prepared: {$timeWithoutPrepared}s\n";
echo "With prepared: {$timeWithPrepared}s\n";
// Prepared statements are typically 2-3x faster
?>
```

---

## Complete Examples

### User CRUD Operations

```php
<?php
class UserRepository {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    // CREATE
    public function create($name, $email, $password) {
        $sql = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt->execute([$name, $email, $hashedPassword]);
        
        return $this->pdo->lastInsertId();
    }
    
    // READ
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // UPDATE
    public function update($id, $name, $email) {
        $sql = "UPDATE users SET name = ?, email = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$name, $email, $id]);
    }
    
    // DELETE
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$id]);
    }
    
    // BULK OPERATIONS
    public function createMultiple($users) {
        try {
            $this->pdo->beginTransaction();
            
            $sql = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($users as $user) {
                $hashedPassword = password_hash($user['password'], PASSWORD_BCRYPT);
                $stmt->execute([$user['name'], $user['email'], $hashedPassword]);
            }
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}

// Usage
$repo = new UserRepository($pdo);

// Create
$userId = $repo->create('John', 'john@example.com', 'password123');

// Read
$user = $repo->findById($userId);

// Update
$repo->update($userId, 'John Updated', 'newemail@example.com');

// Delete
$repo->delete($userId);
?>
```

### Dynamic Query Builder

```php
<?php
class QueryBuilder {
    private $pdo;
    private $table;
    private $where = [];
    private $params = [];
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function table($table) {
        $this->table = $table;
        return $this;
    }
    
    public function where($column, $operator, $value) {
        $this->where[] = "$column $operator ?";
        $this->params[] = $value;
        return $this;
    }
    
    public function andWhere($column, $operator, $value) {
        return $this->where($column, $operator, $value);
    }
    
    public function get() {
        $sql = "SELECT * FROM {$this->table}";
        
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(" AND ", $this->where);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Usage
$builder = new QueryBuilder($pdo);

$results = $builder
    ->table('users')
    ->where('age', '>=', 18)
    ->andWhere('status', '=', 'active')
    ->andWhere('email', 'LIKE', '%@gmail.com')
    ->get();
?>
```

---

## Best Practices

1. **Always use prepared statements** for parameterized queries
2. **Use positional parameters** for simple queries
3. **Use named parameters** for complex queries
4. **Never concatenate user input** into SQL
5. **Bind parameters explicitly** when needed
6. **Handle exceptions** properly
7. **Validate input** before executing
8. **Use transactions** for related operations
9. **Reuse prepared statements** when executing multiple times
10. **Test thoroughly** with malicious input

---

## See Also

- [SQL Injection Prevention](7-sql-injection.md)
- [Executing SQL Statements](5-execute-sql.md)
- [Querying SQL Statements](6-query-sql.md)
- [Database Transactions](11-database-transaction.md)
