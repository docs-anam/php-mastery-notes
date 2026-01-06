# SQL Injection Prevention

## Overview

SQL injection is one of the most common and dangerous web application vulnerabilities. It occurs when user input is directly inserted into SQL queries without proper validation or escaping. This guide shows how to prevent SQL injection attacks.

---

## Table of Contents

1. What is SQL Injection?
2. How SQL Injection Works
3. Common Attack Scenarios
4. Prevention Techniques
5. Prepared Statements
6. Input Validation
7. Error Handling Security
8. Complete Examples

---

## What is SQL Injection?

SQL injection is a security vulnerability where an attacker inserts malicious SQL code into an application's input, allowing them to:

- Extract sensitive data
- Modify or delete database records
- Bypass authentication
- Execute arbitrary commands

### Example Vulnerable Code

```php
<?php
// VULNERABLE - Do NOT use this!
$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
$result = $pdo->query($sql);

if ($result->rowCount() > 0) {
    echo "Login successful!";
}
?>
```

### Attack Example

```
Email: admin@example.com' --
Password: anything

Resulting SQL:
SELECT * FROM users WHERE email = 'admin@example.com' --' AND password = 'anything'

The -- comments out the rest, logging in without password!
```

---

## How SQL Injection Works

### String Concatenation Attack

```php
<?php
// Vulnerable code
$userId = $_GET['id'];
$query = "SELECT * FROM users WHERE id = $userId";
// URL: ?id=1 OR 1=1 results in: SELECT * FROM users WHERE id = 1 OR 1=1

// This returns all users instead of just one!
?>
```

### Quote Escape Attack

```php
<?php
// Vulnerable code
$email = $_POST['email'];
$query = "SELECT * FROM users WHERE email = '$email'";

// Input: ' OR '1'='1
// Resulting: SELECT * FROM users WHERE email = '' OR '1'='1'
// This returns all users!

// Input: '; DROP TABLE users; --
// This could delete the entire table!
?>
```

### UNION-Based Attack

```php
<?php
// Vulnerable code
$product_id = $_GET['id'];
$query = "SELECT name, price FROM products WHERE id = $product_id";

// Malicious input: 1 UNION SELECT username, password FROM users
// Retrieves usernames and passwords!
?>
```

---

## Common Attack Scenarios

### Authentication Bypass

```php
<?php
// Vulnerable login
$email = $_POST['email']; // "admin@example.com' --"
$password = $_POST['password'];

// Original intended query:
// SELECT * FROM users WHERE email = 'admin@example.com' AND password = 'hashed_password'

// Actual query executed:
// SELECT * FROM users WHERE email = 'admin@example.com' --' AND password = '...'
// Comment removes password check!

// PREVENTION: Use prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
$stmt->execute([$email, password_hash($password, PASSWORD_BCRYPT)]);
?>
```

### Data Extraction

```php
<?php
// Vulnerable product search
$search = $_POST['search']; // "' UNION SELECT username, password FROM users --"

// Original: SELECT * FROM products WHERE name LIKE 'search_term'
// Malicious: SELECT * FROM products WHERE name LIKE '' UNION SELECT username, password FROM users --'

// PREVENTION: Use prepared statements
$stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ?");
$stmt->execute(["%$search%"]);
?>
```

### Data Destruction

```php
<?php
// Vulnerable delete
$id = $_GET['id']; // "1; DROP TABLE users; --"

// Original: DELETE FROM users WHERE id = 1
// Malicious: DELETE FROM users WHERE id = 1; DROP TABLE users; --

// PREVENTION: Use prepared statements AND transactions
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
}
?>
```

---

## Prevention Techniques

### 1. Use Prepared Statements (PRIMARY DEFENSE)

```php
<?php
// GOOD - Parameterized query
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND id = ?");
$stmt->execute([$email, $id]);

// The database driver handles escaping automatically
// User input is never interpreted as SQL code
?>
```

### 2. Use Parameterized Queries with Named Parameters

```php
<?php
// GOOD - Named parameters
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND status = :status");
$stmt->execute([
    ':email' => $email,
    ':status' => $status,
]);

// This is safer and more readable
?>
```

### 3. Input Validation

```php
<?php
// Validate data type
$id = (int) $_GET['id']; // Convert to integer

// Validate against whitelist
$validStatuses = ['active', 'inactive', 'pending'];
$status = $_POST['status'];

if (!in_array($status, $validStatuses)) {
    die("Invalid status");
}

// After validation, use prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND status = ?");
$stmt->execute([$id, $status]);
?>
```

### 4. Never Use String Concatenation

```php
<?php
// VULNERABLE - Never do this!
$query = "SELECT * FROM users WHERE id = " . $_GET['id'];

// SAFE - Use prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_GET['id']]);
?>
```

### 5. Least Privilege Database Users

```php
<?php
// Create limited permission user for web application
// In MySQL console:
// CREATE USER 'webapp'@'localhost' IDENTIFIED BY 'password';
// GRANT SELECT, INSERT, UPDATE ON database.* TO 'webapp'@'localhost';

// Don't use root account in application!
$pdo = new PDO(
    "mysql:host=localhost;dbname=database",
    "webapp",      // Limited user
    "password"
);
?>
```

---

## Prepared Statements

### Positional Parameters

```php
<?php
// Define query with placeholders
$stmt = $pdo->prepare("
    SELECT * FROM users 
    WHERE email = ? AND status = ? AND age > ?
");

// Execute with parameters
// The database driver properly escapes values
$stmt->execute([$email, $status, $age]);

// Result is safe regardless of user input
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

### Named Parameters

```php
<?php
$stmt = $pdo->prepare("
    SELECT * FROM users 
    WHERE email = :email AND status = :status AND age > :age
");

// More readable and less error-prone
$stmt->execute([
    ':email' => $email,
    ':status' => $status,
    ':age' => $age,
]);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

### Dynamic Query Building Safely

```php
<?php
// Safe dynamic query building
$filters = [];
$params = [];

$sql = "SELECT * FROM users WHERE 1=1";

if (isset($_POST['email'])) {
    $sql .= " AND email = ?";
    $params[] = $_POST['email'];
}

if (isset($_POST['status'])) {
    $sql .= " AND status = ?";
    $params[] = $_POST['status'];
}

if (isset($_POST['age_min'])) {
    $sql .= " AND age >= ?";
    $params[] = $_POST['age_min'];
}

// Use prepared statement with dynamically built query
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

---

## Input Validation

### Type Casting

```php
<?php
// Integer validation
$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);

// Float validation
$price = (float) $_POST['price'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE price = ?");
$stmt->execute([$price]);

// Boolean validation
$active = (bool) $_POST['active'];
$stmt = $pdo->prepare("SELECT * FROM items WHERE active = ?");
$stmt->execute([$active]);
?>
```

### Whitelist Validation

```php
<?php
class QueryValidator {
    private $allowedColumns = ['id', 'name', 'email', 'created_at'];
    private $allowedDirections = ['ASC', 'DESC'];
    private $allowedStatuses = ['active', 'inactive', 'pending'];
    
    public function validateSortColumn($column) {
        if (!in_array($column, $this->allowedColumns)) {
            throw new Exception("Invalid column");
        }
        return $column;
    }
    
    public function validateSortDirection($direction) {
        $direction = strtoupper($direction);
        if (!in_array($direction, $this->allowedDirections)) {
            throw new Exception("Invalid direction");
        }
        return $direction;
    }
    
    public function validateStatus($status) {
        if (!in_array($status, $this->allowedStatuses)) {
            throw new Exception("Invalid status");
        }
        return $status;
    }
}

$validator = new QueryValidator();

// Safe sorting
$column = $validator->validateSortColumn($_GET['sort_by']);
$direction = $validator->validateSortDirection($_GET['sort_dir']);

$stmt = $pdo->prepare("SELECT * FROM users ORDER BY $column $direction");
$stmt->execute();
?>
```

### Email and URL Validation

```php
<?php
// Email validation
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if ($email === false) {
    die("Invalid email");
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

// URL validation
$url = filter_var($_POST['website'], FILTER_VALIDATE_URL);
if ($url === false) {
    die("Invalid URL");
}

// IP validation
$ip = filter_var($_POST['ip_address'], FILTER_VALIDATE_IP);
if ($ip === false) {
    die("Invalid IP");
}
?>
```

---

## Error Handling Security

### Don't Expose Database Errors

```php
<?php
// VULNERABLE - Exposes database structure
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $result = $pdo->query("SELECT * FROM users WHERE id = " . $_GET['id']);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage()); // Exposes table names, columns!
}

// SECURE - Hide database errors from users
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
} catch (PDOException $e) {
    // Log error securely
    error_log("Database error: " . $e->getMessage());
    // Show generic message to user
    die("An error occurred. Please try again.");
}
?>
```

### Logging Suspicious Activity

```php
<?php
class SecurityLogger {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function logSuspiciousQuery($input, $query) {
        // Log potential SQL injection attempts
        $sql = "INSERT INTO security_logs (input, query, ip, timestamp) VALUES (?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            substr($input, 0, 255),
            substr($query, 0, 500),
            $_SERVER['REMOTE_ADDR'],
        ]);
    }
    
    public function isSuspicious($input) {
        $suspicious = ['UNION', 'SELECT', 'DROP', 'DELETE', '--', '/*', '*/'];
        
        foreach ($suspicious as $pattern) {
            if (stripos($input, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }
}

$logger = new SecurityLogger($pdo);
$userInput = $_GET['search'];

if ($logger->isSuspicious($userInput)) {
    $logger->logSuspiciousQuery($userInput, "Search query");
    die("Invalid input");
}
?>
```

---

## Complete Examples

### Secure Login Function

```php
<?php
class AuthenticationService {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function login($email, $password) {
        // Validate email format
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return false;
        }
        
        // Use prepared statement
        $stmt = $this->pdo->prepare("
            SELECT id, name, password_hash FROM users 
            WHERE email = ? AND deleted_at IS NULL
        ");
        
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return false; // User not found
        }
        
        // Verify password hash
        if (password_verify($password, $user['password_hash'])) {
            return [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $email,
            ];
        }
        
        return false; // Invalid password
    }
    
    public function register($name, $email, $password) {
        // Validate input
        if (strlen($name) < 2 || strlen($name) > 255) {
            throw new Exception("Invalid name");
        }
        
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            throw new Exception("Invalid email");
        }
        
        if (strlen($password) < 8) {
            throw new Exception("Password too short");
        }
        
        // Check email doesn't exist
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("Email already registered");
        }
        
        // Hash password and insert
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, password_hash, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        
        $stmt->execute([$name, $email, $hashedPassword]);
        return $this->pdo->lastInsertId();
    }
}

// Usage
$auth = new AuthenticationService($pdo);

try {
    $userId = $auth->register('John Doe', 'john@example.com', 'securePass123');
    echo "User registered: $userId";
    
    $user = $auth->login('john@example.com', 'securePass123');
    if ($user) {
        echo "Login successful: " . $user['name'];
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

### Secure Search Function

```php
<?php
class SearchService {
    private $pdo;
    private $maxResults = 100;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function searchProducts($query, $limit = 20) {
        // Limit search term length
        $query = substr(trim($query), 0, 255);
        
        if (strlen($query) < 2) {
            return [];
        }
        
        // Remove special characters
        $query = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $query);
        
        // Use prepared statement with LIKE
        $searchTerm = "%$query%";
        $stmt = $this->pdo->prepare("
            SELECT id, name, description, price 
            FROM products 
            WHERE (name LIKE ? OR description LIKE ?)
            AND active = 1
            LIMIT ?
        ");
        
        $limit = min((int) $limit, $this->maxResults);
        $stmt->execute([$searchTerm, $searchTerm, $limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Usage
$search = new SearchService($pdo);
$results = $search->searchProducts($_GET['q']);
?>
```

---

## Security Checklist

- ✅ Always use prepared statements
- ✅ Never concatenate user input into queries
- ✅ Validate and sanitize all input
- ✅ Use whitelist validation when possible
- ✅ Handle errors securely (don't expose database details)
- ✅ Use limited privilege database users
- ✅ Enable error logging and monitoring
- ✅ Keep PHP and database software updated
- ✅ Use HTTPS for data transmission
- ✅ Hash passwords with bcrypt or Argon2

---

## See Also

- [Query SQL Statements](6-query-sql.md)
- [Prepared Statements](8-prepare-statement.md)
- [Database Connection](4-database-connection.md)
