# PHP Data Objects - PDO Introduction

## Overview

PDO (PHP Data Objects) is a lightweight, consistent interface for accessing multiple databases in PHP. It provides a data-access abstraction layer, allowing you to work with different database systems using the same code.

---

## Table of Contents

1. What is PDO?
2. Why Use PDO?
3. Supported Databases
4. Installation and Setup
5. PDO Drivers
6. Basic PDO Usage
7. Advantages Over Direct Database Extensions
8. PDO Architecture
9. Best Practices

---

## What is PDO?

PDO stands for PHP Data Objects. It's a PHP extension that defines a lightweight, consistent interface for accessing databases.

### Key Characteristics

- **Database Agnostic**: Write once, work with any supported database
- **Prepared Statements**: Built-in protection against SQL injection
- **Object-Oriented**: Modern, clean API
- **Error Handling**: Exceptions for better error management
- **Transactions**: Full support for database transactions
- **Metadata**: Retrieve database and column information

---

## Why Use PDO?

### 1. Database Independence

```php
<?php
// Switch databases without changing code

// MySQL
$pdo = new PDO("mysql:host=localhost;dbname=mydb", "user", "pass");

// PostgreSQL
$pdo = new PDO("pgsql:host=localhost;dbname=mydb", "user", "pass");

// SQLite
$pdo = new PDO("sqlite:/path/to/database.db");

// All subsequent code remains the same!
?>
```

### 2. Prepared Statements

```php
<?php
// Automatically prevents SQL injection
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
?>
```

### 3. Consistent API

```php
<?php
// Same methods work across all databases
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>
```

### 4. Better Error Handling

```php
<?php
try {
    $pdo->exec("INVALID SQL");
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
```

---

## Supported Databases

PDO supports many databases through different drivers:

| Database | DSN Prefix | Notes |
|----------|-----------|-------|
| MySQL | mysql: | Most common, fully featured |
| PostgreSQL | pgsql: | Excellent support |
| SQLite | sqlite: | File-based, no server needed |
| Oracle | oci: | Enterprise database |
| Microsoft SQL Server | sqlsrv: | Windows/Azure support |
| IBM DB2 | ibm: | Enterprise database |
| Firebird | firebird: | Open source |
| Sybase | sybase: | Legacy support |

---

## Installation and Setup

### Verify PDO Installation

```bash
# Check if PDO is installed
php -m | grep PDO

# Check specific drivers
php -i | grep PDO
```

### Enable PDO (php.ini)

```ini
; Linux/macOS
extension=pdo.so
extension=pdo_mysql.so

; Windows
extension=pdo.dll
extension=pdo_mysql.dll
```

### Install Missing Drivers (macOS with Homebrew)

```bash
# Install PDO and MySQL driver
brew install php
brew install php-mysql

# Restart PHP-FPM
brew services restart php-fpm
```

### Install Missing Drivers (Ubuntu/Debian)

```bash
# Install PDO and MySQL driver
sudo apt-get install php-pdo
sudo apt-get install php-mysql

# Restart PHP
sudo systemctl restart php-fpm
```

---

## PDO Drivers

### MySQL Driver

```php
<?php
$pdo = new PDO(
    "mysql:host=localhost;dbname=mydb;charset=utf8mb4;port=3306",
    "user",
    "password"
);
?>
```

### PostgreSQL Driver

```php
<?php
$pdo = new PDO(
    "pgsql:host=localhost;dbname=mydb;port=5432",
    "user",
    "password"
);
?>
```

### SQLite Driver

```php
<?php
// File-based database
$pdo = new PDO("sqlite:/path/to/database.db");

// In-memory database
$pdo = new PDO("sqlite::memory:");
?>
```

### List Available Drivers

```php
<?php
echo "Available PDO drivers:\n";
print_r(PDO::getAvailableDrivers());
// Output:
// Array
// (
//     [0] => mysql
//     [1] => pgsql
//     [2] => sqlite
// )
?>
```

---

## Basic PDO Usage

### Simple Connection

```php
<?php
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=mydb",
        "root",
        "password"
    );
    echo "Connected!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

### Query Execution

```php
<?php
// Using query() for simple queries
$result = $pdo->query("SELECT * FROM users");
foreach ($result as $row) {
    echo $row['name'];
}

// Using prepare() and execute() for parameterized queries
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([1]);
$user = $stmt->fetch();
?>
```

### Insert, Update, Delete

```php
<?php
// Insert
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->execute(["John", "john@example.com"]);

// Update
$stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
$stmt->execute(["Jane", 1]);

// Delete
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([1]);

echo $pdo->lastInsertId(); // Get last insert ID
echo $stmt->rowCount();     // Affected rows
?>
```

---

## Advantages Over Direct Database Extensions

### MySQLi vs PDO

```php
<?php
// MySQLi - Only for MySQL
$mysqli = new mysqli("localhost", "root", "password", "database");
// Can't use with PostgreSQL without completely rewriting

// PDO - Works with any database
$pdo = new PDO("mysql:host=localhost;dbname=database", "root", "password");
// Switch to PostgreSQL by just changing the DSN
// $pdo = new PDO("pgsql:host=localhost;dbname=database", "root", "password");
?>
```

### Prepared Statements

```php
<?php
// MySQLi OOP
$stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();

// PDO - Simpler
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
?>
```

### Transactions

```php
<?php
// Both support transactions, but PDO is more intuitive
try {
    $pdo->beginTransaction();
    
    $stmt1 = $pdo->prepare("UPDATE accounts SET balance = balance - 100 WHERE id = 1");
    $stmt1->execute();
    
    $stmt2 = $pdo->prepare("UPDATE accounts SET balance = balance + 100 WHERE id = 2");
    $stmt2->execute();
    
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Transaction failed: " . $e->getMessage();
}
?>
```

---

## PDO Architecture

### Components

```
┌─────────────────────────────────────────┐
│        Your PHP Application             │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│      PDO (Data Access Layer)            │
├─────────────────────────────────────────┤
│  - Connection Management                │
│  - Statement Preparation                │
│  - Error Handling                       │
│  - Transaction Control                  │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│    PDO Drivers (Database Specific)      │
├─────────────────────────────────────────┤
│  - PDO_MySQL                            │
│  - PDO_PostgreSQL                       │
│  - PDO_SQLite                           │
│  - etc.                                 │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│      Database Servers                   │
├─────────────────────────────────────────┤
│  - MySQL                                │
│  - PostgreSQL                           │
│  - SQLite                               │
│  - etc.                                 │
└─────────────────────────────────────────┘
```

### Data Flow

```php
<?php
// 1. Application creates PDO connection
$pdo = new PDO("mysql:...", "user", "pass");

// 2. Application prepares statement
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");

// 3. Application executes with parameters
$stmt->execute([123]);

// 4. PDO driver sends query to database
// 5. Database executes and returns results
// 6. PDO driver receives and formats results
// 7. Application fetches results
while ($row = $stmt->fetch()) {
    // Process row
}
?>
```

---

## Best Practices

### 1. Use Prepared Statements

```php
<?php
// GOOD - Protected from SQL injection
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

// BAD - Vulnerable to SQL injection
$result = $pdo->query("SELECT * FROM users WHERE email = '$email'");
?>
```

### 2. Set Error Mode to Exception

```php
<?php
$pdo = new PDO(
    "mysql:host=localhost;dbname=database",
    "user",
    "password",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
?>
```

### 3. Use Named Parameters

```php
<?php
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
$stmt->execute([
    ':name' => $name,
    ':email' => $email,
]);
?>
```

### 4. Use Try-Catch Blocks

```php
<?php
try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    // Log error
    error_log($e->getMessage());
    // Show user-friendly message
    die("Database connection failed");
}
?>
```

### 5. Close Connections Properly

```php
<?php
// Unset PDO object to close connection
$pdo = null;

// Or in a class
public function closeConnection() {
    $this->pdo = null;
}
?>
```

---

## Configuration Best Practices

```php
<?php
// database-config.php
return new PDO(
    "mysql:host=" . getenv('DB_HOST') . 
    ";dbname=" . getenv('DB_NAME') . 
    ";charset=utf8mb4",
    getenv('DB_USER'),
    getenv('DB_PASSWORD'),
    [
        // Report errors as exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        
        // Use associative arrays
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        
        // Don't use persistent connections in web apps
        PDO::ATTR_PERSISTENT => false,
        
        // Don't emulate prepared statements
        PDO::ATTR_EMULATE_PREPARES => false,
        
        // Connection timeout
        PDO::ATTR_TIMEOUT => 5,
    ]
);
?>
```

---

## Complete Example

```php
<?php
class PDODatabase {
    private $pdo;
    private $config;
    
    public function __construct($config = []) {
        $this->config = array_merge([
            'host' => 'localhost',
            'database' => 'myapp',
            'user' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
        ], $config);
        
        $this->connect();
    }
    
    private function connect() {
        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                $this->config['host'],
                $this->config['database'],
                $this->config['charset']
            );
            
            $this->pdo = new PDO(
                $dsn,
                $this->config['user'],
                $this->config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }
    
    public function query($sql) {
        return $this->pdo->query($sql);
    }
    
    public function beginTransaction() {
        $this->pdo->beginTransaction();
    }
    
    public function commit() {
        $this->pdo->commit();
    }
    
    public function rollBack() {
        $this->pdo->rollBack();
    }
    
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}

// Usage
$db = new PDODatabase([
    'host' => 'localhost',
    'database' => 'myapp',
    'user' => 'app_user',
    'password' => 'secure_password',
]);

// Execute query
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([1]);
$user = $stmt->fetch();

print_r($user);
?>
```

---

## See Also

- [MySQL Installation](2-install.md)
- [Accessing MySQL from PHP](3-access-mysql.md)
- [Database Connection Management](5-database-connection.md)
- [SQL Execution](6-execute-sql.md)
