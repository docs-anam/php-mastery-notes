# PHP & MySQL - Database Integration & Data Persistence

## Table of Contents
1. [Overview](#overview)
2. [Relational Databases](#relational-databases)
3. [MySQL Overview](#mysql-overview)
4. [PHP Database Connection Methods](#php-database-connection-methods)
5. [Core Concepts](#core-concepts)
6. [Architecture](#architecture)
7. [Learning Path](#learning-path)
8. [Best Practices](#best-practices)
9. [Prerequisites](#prerequisites)

---

## Overview

This section covers integrating PHP with MySQL/MariaDB databases. You'll learn how to:
- Connect to databases from PHP
- Execute SQL queries
- Retrieve and process results
- Prevent security vulnerabilities
- Use advanced features (transactions, prepared statements)

## Relational Databases

### What is a Relational Database?

A relational database stores data in **tables** (like spreadsheets) with relationships between them.

### Key Concepts

**Table** (Collection of data)
```
Users Table:
┌────┬───────┬─────────────────┐
│ id │ name  │ email           │
├────┼───────┼─────────────────┤
│  1 │ Alice │ alice@example.com│
│  2 │ Bob   │ bob@example.com  │
│  3 │ Carol │ carol@example.com│
└────┴───────┴─────────────────┘
```

**Record** (One row)
```
Alice's record:
id: 1, name: Alice, email: alice@example.com
```

**Field** (One column)
```
The 'name' field contains: Alice, Bob, Carol
```

### SQL (Structured Query Language)

Standard language for database operations:

```sql
-- CREATE: Add new table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100)
);

-- INSERT: Add data
INSERT INTO users (name, email) 
VALUES ('Alice', 'alice@example.com');

-- SELECT: Retrieve data
SELECT * FROM users WHERE id = 1;

-- UPDATE: Modify data
UPDATE users SET email = 'newemail@example.com' WHERE id = 1;

-- DELETE: Remove data
DELETE FROM users WHERE id = 1;
```

## MySQL Overview

### What is MySQL?

MySQL is a popular open-source relational database system. MariaDB is a compatible fork.

### Key Features

- **Open Source**: Free to use
- **ACID Compliant**: Reliable transactions
- **Scalable**: Handles large datasets
- **Secure**: Authentication and user privileges
- **Fast**: Optimized for web applications
- **Reliable**: Data persistence and backup

### MySQL vs MariaDB

| Aspect | MySQL | MariaDB |
|--------|-------|---------|
| Creator | Oracle | Open-source community |
| License | GPL | GPL |
| Cost | Free | Free |
| Development | Commercial focus | Community focus |
| Compatibility | Standard | Drop-in MySQL replacement |

**Both work the same for our purposes.**

## PHP Database Connection Methods

### 1. MySQLi (MySQL Improved)

Object-oriented and procedural interface:

```php
// Connect
$mysqli = new mysqli("localhost", "user", "password", "database");

// Query
$result = $mysqli->query("SELECT * FROM users");

// Fetch
while ($row = $result->fetch_assoc()) {
    echo $row['name'];
}
```

**Pros**: MySQLi-specific features, object-oriented
**Cons**: Only for MySQL

### 2. PDO (PHP Data Objects)

Universal interface for multiple databases:

```php
// Connect to MySQL
$pdo = new PDO("mysql:host=localhost;dbname=database", "user", "password");

// Query
$result = $pdo->query("SELECT * FROM users");

// Fetch
foreach ($result as $row) {
    echo $row['name'];
}
```

**Pros**: Works with MySQL, PostgreSQL, SQLite, etc.
**Cons**: More setup

### 3. Comparison

| Feature | MySQLi | PDO |
|---------|--------|-----|
| **Database Support** | MySQL only | Multiple |
| **API Style** | OOP + Procedural | OOP only |
| **Prepared Statements** | Yes | Yes |
| **Transaction Support** | Yes | Yes |
| **Portability** | Low | High |
| **Learning Curve** | Easier | Moderate |

**Recommendation**: Use **PDO** for new projects (more portable)

## Core Concepts

### Connection

Establishing communication with database:

```php
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=myapp",
        "root",
        "password"
    );
    // Connection successful
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
```

### Query Execution

Running SQL commands:

```php
// Non-parameterized (vulnerable to SQL injection!)
$result = $pdo->query("SELECT * FROM users WHERE id = 1");

// Prepared (safe)
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([1]);
```

### Result Fetching

Getting data from queries:

```php
// Fetch one row as array
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo $row['name'];

// Fetch all rows
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    echo $row['name'];
}

// Fetch as object
$user = $stmt->fetch(PDO::FETCH_OBJ);
echo $user->name;
```

### Prepared Statements

Separate SQL structure from data (prevents injection):

```php
// Define query with placeholders
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");

// Execute with actual values
$stmt->execute([$_POST['name'], $_POST['email']]);

// Named placeholders (more readable)
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
$stmt->execute([
    ':name' => $_POST['name'],
    ':email' => $_POST['email']
]);
```

### Transactions

Group operations - all succeed or all fail:

```php
try {
    $pdo->beginTransaction();
    
    // Operation 1
    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
    $stmt->execute([100, 1]);
    
    // Operation 2  
    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
    $stmt->execute([100, 2]);
    
    $pdo->commit();  // All succeeded
} catch (Exception $e) {
    $pdo->rollBack();  // All rolled back
    echo "Transaction failed: " . $e->getMessage();
}
```

## Architecture

### Typical Web Database Flow

```
User Request
    │
    ▼
┌─────────────────────┐
│  PHP Application    │
├─────────────────────┤
│ • Process request   │
│ • Validate input    │
│ • Prepare query     │
└──────────┬──────────┘
           │
           ▼ TCP Connection (port 3306)
┌─────────────────────┐
│  MySQL Server       │
├─────────────────────┤
│ • Parse SQL         │
│ • Execute query     │
│ • Fetch results     │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  Database Files     │
│  (Hard Drive)       │
└─────────────────────┘
           │
           ▼
    Return Results
           │
           ▼
      User Response
```

### Data Lifecycle

```
1. CREATE: Insert new data
   INSERT INTO users (name) VALUES ('Alice')

2. READ: Retrieve data
   SELECT * FROM users

3. UPDATE: Modify existing data
   UPDATE users SET name = 'Alicia' WHERE id = 1

4. DELETE: Remove data
   DELETE FROM users WHERE id = 1
```

## Learning Path

Master database integration progressively:

1. **[Installation](1-install.md)** - Set up MySQL/MariaDB
2. **[Connecting](2-access-mysql.md)** - Create database connections
3. **[PDO Introduction](3-into-php-data-object-(PDO).md)** - Why use PDO
4. **[Connection Methods](4-database-connection.md)** - Different ways to connect
5. **[SQL Execution](5-execute-sql.md)** - Running SQL commands
6. **[Queries](6-query-sql.md)** - SELECT, INSERT, UPDATE, DELETE
7. **[SQL Injection](7-sql-injection.md)** - Security vulnerabilities
8. **[Prepared Statements](8-prepare-statement.md)** - Safe queries
9. **[Fetching Data](9-fetch-data.md)** - Retrieving results
10. **[Auto-increment](10-auto-increament.md)** - Primary keys
11. **[Transactions](11-database-transaction.md)** - Multi-operation safety
12. **[Repository Pattern](12-repository-pattern.md)** - Organizing database code

## Best Practices

### 1. Always Use Prepared Statements

```php
// ❌ DON'T
$query = "SELECT * FROM users WHERE email = '" . $_POST['email'] . "'";

// ✅ DO
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$_POST['email']]);
```

### 2. Validate Input

```php
// Validate email format
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
    echo "Invalid email";
}
```

### 3. Use Transactions for Related Operations

```php
// Transfer money - both operations or neither
$pdo->beginTransaction();
try {
    // Debit account
    // Credit account
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
}
```

### 4. Handle Errors Gracefully

```php
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
} catch (PDOException $e) {
    // Log error securely
    error_log($e->getMessage());
    // Show generic message to user
    echo "Database error occurred";
}
```

### 5. Close Large Result Sets

```php
$stmt = $pdo->query("SELECT * FROM large_table");
while ($row = $stmt->fetch()) {
    process($row);
}
unset($stmt);  // Free resources
```

## Prerequisites

Before starting database integration:

✅ **Technical:**
- PHP basics (variables, arrays, functions, OOP)
- Web fundamentals (HTTP, forms, sessions)

✅ **Tools:**
- PHP installed
- MySQL/MariaDB installed
- Text editor (VS Code)

✅ **Knowledge:**
- Basic SQL understanding (CREATE, SELECT, INSERT, UPDATE, DELETE)
- Understanding of tables and relationships

## Quick Start

```php
// 1. Connect
$pdo = new PDO("mysql:host=localhost;dbname=myapp", "root", "");

// 2. Prepare
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");

// 3. Execute
$stmt->execute([$email]);

// 4. Fetch
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 5. Use
if ($user) {
    echo "Welcome, " . $user['name'];
}
```

## Common Pitfalls

❌ **Don't**: Connect in every function
```php
// Bad - inefficient
function getUser() {
    $pdo = new PDO(...);  // Connect each time
}
```

✅ **Do**: Reuse connections
```php
// Good - connect once, reuse
$pdo = new PDO(...);
function getUser($pdo) {
    // Use existing connection
}
```

---

❌ **Don't**: Hardcode credentials
```php
// Bad - security risk
$pdo = new PDO("mysql:host=localhost;dbname=db", "root", "password123");
```

✅ **Do**: Use config files
```php
// Good - in .env or config file
$config = require 'config/database.php';
$pdo = new PDO($config['dsn'], $config['user'], $config['password']);
```

## Resources

- **PDO Manual**: [php.net/manual/en/book.pdo.php](https://www.php.net/manual/en/book.pdo.php)
- **MySQL Manual**: [dev.mysql.com/doc](https://dev.mysql.com/doc/)
- **SQL Tutorial**: [W3Schools SQL](https://www.w3schools.com/sql/)
- **OWASP SQL Injection**: [owasp.org/www-community/attacks/SQL_Injection](https://owasp.org/www-community/attacks/SQL_Injection)
