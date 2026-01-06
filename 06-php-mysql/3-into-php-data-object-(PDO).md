# Accessing MySQL from PHP

## Overview

PHP provides multiple ways to access MySQL databases. The most common methods are MySQLi (MySQL Improved) extension and PDO (PHP Data Objects), both offering object-oriented and procedural interfaces.

---

## Table of Contents

1. MySQLi Extension
2. PDO Extension
3. Comparison: MySQLi vs PDO
4. Basic Connection
5. Error Handling
6. Connection Best Practices
7. Complete Examples

---

## MySQLi Extension

### Object-Oriented Style

```php
<?php
// Create connection
$mysqli = new mysqli("localhost", "root", "password", "database");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Connected successfully!";

// Close connection
$mysqli->close();
?>
```

### Procedural Style

```php
<?php
// Create connection
$conn = mysqli_connect("localhost", "root", "password", "database");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully!";

// Close connection
mysqli_close($conn);
?>
```

---

## PDO Extension

```php
<?php
try {
    // Create connection using DSN
    $pdo = new PDO(
        "mysql:host=localhost;dbname=database;charset=utf8mb4",
        "root",
        "password",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    echo "Connected successfully!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

---

## Comparison: MySQLi vs PDO

| Feature | MySQLi | PDO |
|---------|--------|-----|
| Object-Oriented | Yes | Yes |
| Procedural | Yes | No |
| Database Support | MySQL only | Multiple databases |
| Prepared Statements | Yes | Yes |
| Transactions | Yes | Yes |
| Debugging | Built-in | Built-in |
| Community | Large | Large |

### When to Use MySQLi
- MySQL-only projects
- Familiar with MySQLi syntax
- Need procedural style

### When to Use PDO
- Need multiple database support
- Want consistent API across databases
- Building database-agnostic applications

---

## Basic Connection

### MySQLi OOP

```php
<?php
class Database {
    private $mysqli;
    
    public function __construct($host, $user, $password, $database) {
        $this->mysqli = new mysqli($host, $user, $password, $database);
        
        if ($this->mysqli->connect_error) {
            throw new Exception("Connection failed: " . $this->mysqli->connect_error);
        }
        
        // Set character set
        $this->mysqli->set_charset("utf8mb4");
    }
    
    public function query($sql) {
        return $this->mysqli->query($sql);
    }
    
    public function close() {
        $this->mysqli->close();
    }
}

$db = new Database("localhost", "root", "password", "database");
$result = $db->query("SELECT * FROM users");
$db->close();
?>
```

### PDO Connection

```php
<?php
class Database {
    private $pdo;
    
    public function __construct($host, $user, $password, $database) {
        try {
            $this->pdo = new PDO(
                "mysql:host=$host;dbname=$database;charset=utf8mb4",
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }
    
    public function query($sql) {
        return $this->pdo->query($sql);
    }
}

$db = new Database("localhost", "root", "password", "database");
$result = $db->query("SELECT * FROM users");
?>
```

---

## Error Handling

### MySQLi Error Handling

```php
<?php
$mysqli = new mysqli("localhost", "root", "password", "database");

// Check connection error
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Query error handling
$result = $mysqli->query("SELECT * FROM users");
if (!$result) {
    die("Query failed: " . $mysqli->error);
}

// Procedural style
$conn = mysqli_connect("localhost", "root", "password", "database");
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
```

### PDO Error Handling

```php
<?php
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=database",
        "root",
        "password",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]
    );
    
    $result = $pdo->query("SELECT * FROM users");
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    // In production, log this instead
    error_log($e->getMessage());
}
?>
```

---

## Connection Best Practices

### 1. Use Configuration Files

```php
<?php
// config/database.php
return [
    'host' => 'localhost',
    'user' => 'app_user',
    'password' => getenv('DB_PASSWORD'),
    'database' => 'app_database',
];

// In your app
$config = require 'config/database.php';
$pdo = new PDO(
    "mysql:host={$config['host']};dbname={$config['database']}",
    $config['user'],
    $config['password']
);
?>
```

### 2. Use Environment Variables

```php
<?php
// .env file
DB_HOST=localhost
DB_USER=app_user
DB_PASSWORD=secure_password
DB_NAME=app_database

// Connect using env variables
$pdo = new PDO(
    "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASSWORD')
);
?>
```

### 3. Create Connection Wrapper

```php
<?php
class DatabaseConnection {
    private static $instance;
    private $pdo;
    
    private function __construct() {
        $this->pdo = new PDO(
            "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME') . ";charset=utf8mb4",
            getenv('DB_USER'),
            getenv('DB_PASSWORD'),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => false,
            ]
        );
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
}

$db = DatabaseConnection::getInstance()->getConnection();
?>
```

### 4. Set Appropriate Attributes

```php
<?php
$pdo = new PDO(
    "mysql:host=localhost;dbname=database;charset=utf8mb4",
    "root",
    "password",
    [
        // Report errors as exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        
        // Default fetch mode
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        
        // Don't use persistent connections in web apps
        PDO::ATTR_PERSISTENT => false,
        
        // Don't emulate prepared statements
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);
?>
```

---

## Testing Connections

```php
<?php
function testMySQLiConnection() {
    try {
        $mysqli = new mysqli("localhost", "root", "password", "database");
        if ($mysqli->connect_error) {
            return false;
        }
        $mysqli->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function testPDOConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=localhost;dbname=database",
            "root",
            "password"
        );
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

if (testMySQLiConnection()) {
    echo "MySQLi connection successful\n";
}

if (testPDOConnection()) {
    echo "PDO connection successful\n";
}
?>
```

---

## Common Connection Issues

### Issue: "Access Denied"

```php
<?php
// Check credentials
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=database",
        "username",
        "password"
    );
} catch (PDOException $e) {
    // Likely wrong username/password
    echo "Check your credentials";
}
?>
```

### Issue: "Can't Connect to Server"

```php
<?php
// Check host is accessible
function isServerUp($host, $port = 3306, $timeout = 5) {
    $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if (is_resource($connection)) {
        fclose($connection);
        return true;
    }
    return false;
}

if (!isServerUp('localhost')) {
    echo "MySQL server is not running";
}
?>
```

---

## Complete Example

```php
<?php
class DatabaseManager {
    private $pdo;
    
    public function __construct($config = []) {
        $defaults = [
            'host' => getenv('DB_HOST') ?: 'localhost',
            'database' => getenv('DB_NAME') ?: 'database',
            'user' => getenv('DB_USER') ?: 'root',
            'password' => getenv('DB_PASSWORD') ?: '',
        ];
        
        $config = array_merge($defaults, $config);
        
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
            
            $this->pdo = new PDO(
                $dsn,
                $config['user'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function isConnected() {
        try {
            $this->pdo->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

// Usage
try {
    $db = new DatabaseManager();
    
    if ($db->isConnected()) {
        echo "Connected successfully!";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

---

## See Also

- Documentation: [MySQLi](https://www.php.net/manual/en/book.mysqli.php)
- Documentation: [PDO](https://www.php.net/manual/en/book.pdo.php)
- Related: [Database Connection](4-database-connection.md), [SQL Execution](5-execute-sql.md)
