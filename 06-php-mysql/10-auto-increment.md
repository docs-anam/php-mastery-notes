# Auto-Increment Fields

## Overview

Auto-increment fields automatically generate unique numeric identifiers for each new record. They're essential for primary keys and ensuring data integrity. Understanding how they work in PHP and MySQL is crucial for effective database design.

---

## Table of Contents

1. What are Auto-Increment Fields?
2. Basic Implementation
3. Retrieving Last Insert ID
4. Custom Auto-Increment Values
5. Auto-Increment Behavior
6. Best Practices
7. Troubleshooting
8. Complete Examples

---

## What are Auto-Increment Fields?

Auto-increment is a feature that automatically assigns an incrementing number to each new record. Typically used for primary keys.

### Characteristics

```php
<?php
// When you create a table with auto-increment:
// CREATE TABLE users (
//     id INT PRIMARY KEY AUTO_INCREMENT,
//     name VARCHAR(255),
//     email VARCHAR(255)
// );

// Each INSERT automatically gets next ID
// First record: id = 1
// Second record: id = 2
// Third record: id = 3
// etc.
?>
```

### Why Use Auto-Increment?

1. **Unique Identifiers**: Guaranteed unique values
2. **Automatic Assignment**: No manual ID management
3. **Primary Key**: Perfect for primary keys
4. **Foreign Keys**: Easy to reference in other tables
5. **Data Integrity**: Prevents duplicate IDs

---

## Basic Implementation

### Creating Table with Auto-Increment

```php
<?php
// MySQL - Create table with auto-increment
$sql = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$pdo->exec($sql);
?>
```

### Inserting Records

```php
<?php
// Insert without specifying ID
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->execute(['John Doe', 'john@example.com']);

// Database automatically assigns id = 1

// Insert another record
$stmt->execute(['Jane Doe', 'jane@example.com']);
// Database automatically assigns id = 2
?>
```

### Retrieving Last Insert ID

```php
<?php
// After INSERT, get the generated ID
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->execute(['John', 'john@example.com']);

$lastId = $pdo->lastInsertId();
echo "User created with ID: $lastId";

// Returns: User created with ID: 1
?>
```

---

## Retrieving Last Insert ID

### Using lastInsertId()

```php
<?php
// Simple usage
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->execute(['John', 'john@example.com']);

$userId = $pdo->lastInsertId();

// Now you can use the ID
$stmt = $pdo->prepare("INSERT INTO profiles (user_id, bio) VALUES (?, ?)");
$stmt->execute([$userId, 'User bio']);
?>
```

### lastInsertId() with Sequences

```php
<?php
// For some databases (PostgreSQL), specify sequence name
$id = $pdo->lastInsertId('table_name_seq');

// PDO auto-detects for MySQL
$id = $pdo->lastInsertId(); // Works for MySQL
?>
```

### Check if Insert Successful

```php
<?php
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$result = $stmt->execute(['John', 'john@example.com']);

if ($result) {
    $userId = $pdo->lastInsertId();
    echo "User created: $userId";
} else {
    echo "Insert failed";
}
?>
```

### Bulk Insert with IDs

```php
<?php
// Insert multiple records and retrieve all IDs
$users = [
    ['John', 'john@example.com'],
    ['Jane', 'jane@example.com'],
    ['Bob', 'bob@example.com'],
];

$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$userIds = [];

foreach ($users as $user) {
    $stmt->execute($user);
    $userIds[] = $pdo->lastInsertId();
}

print_r($userIds); // [1, 2, 3]
?>
```

---

## Custom Auto-Increment Values

### Set Starting Value

```php
<?php
// Set auto-increment to start at 1000
$pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1000");

// Next insert will get ID 1000, then 1001, 1002, etc.
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->execute(['John', 'john@example.com']);

$id = $pdo->lastInsertId();
echo "ID: $id"; // Output: ID: 1000
?>
```

### Get Current Auto-Increment Value

```php
<?php
// Get next auto-increment value
$stmt = $pdo->query("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_NAME='users'");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$nextId = $result['AUTO_INCREMENT'];

echo "Next ID will be: $nextId";
?>
```

### Reset Auto-Increment

```php
<?php
// Reset to start over from 1
$pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1");

// Or reset to next highest existing ID
$stmt = $pdo->query("SELECT MAX(id) FROM users");
$maxId = $stmt->fetchColumn();

if ($maxId) {
    $pdo->exec("ALTER TABLE users AUTO_INCREMENT = " . ($maxId + 1));
}
?>
```

---

## Auto-Increment Behavior

### Incrementing on Each Insert

```php
<?php
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");

// Each insert increments automatically
for ($i = 1; $i <= 5; $i++) {
    $stmt->execute(["User$i", "user$i@example.com"]);
    echo "Created user ID: " . $pdo->lastInsertId() . "\n";
}

// Output:
// Created user ID: 1
// Created user ID: 2
// Created user ID: 3
// Created user ID: 4
// Created user ID: 5
?>
```

### Skipped IDs (Normal Behavior)

```php
<?php
// If you explicitly insert an ID
$stmt = $pdo->prepare("INSERT INTO users (id, name, email) VALUES (?, ?, ?)");
$stmt->execute([100, 'John', 'john@example.com']);

// Next auto-increment will be 101
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->execute(['Jane', 'jane@example.com']);

echo $pdo->lastInsertId(); // Output: 101
?>
```

### Transactions and Auto-Increment

```php
<?php
try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    $stmt->execute(['John', 'john@example.com']);
    $userId = $pdo->lastInsertId();
    
    // Use the ID for related inserts
    $stmt = $pdo->prepare("INSERT INTO profiles (user_id, bio) VALUES (?, ?)");
    $stmt->execute([$userId, 'Bio']);
    
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Transaction failed: " . $e->getMessage();
}
?>
```

---

## Best Practices

### 1. Always Use Prepared Statements

```php
<?php
// GOOD
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->execute(['John', 'john@example.com']);
$id = $pdo->lastInsertId();

// BAD - Never do this
$id = $pdo->exec("INSERT INTO users (name, email) VALUES ('John', 'john@example.com')");
?>
```

### 2. Retrieve ID Immediately After Insert

```php
<?php
// RIGHT - Get ID immediately
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->execute(['John', 'john@example.com']);
$userId = $pdo->lastInsertId();

// Don't wait or make other queries first
?>
```

### 3. Use in Related Inserts

```php
<?php
// Insert user, get ID, use for profile
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->execute(['John', 'john@example.com']);
$userId = $pdo->lastInsertId();

$stmt = $pdo->prepare("INSERT INTO profiles (user_id, bio) VALUES (?, ?)");
$stmt->execute([$userId, 'User bio']);
?>
```

### 4. Use Transactions for Related Operations

```php
<?php
try {
    $pdo->beginTransaction();
    
    // Create user
    $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    $stmt->execute(['John', 'john@example.com']);
    $userId = $pdo->lastInsertId();
    
    // Create profile
    $stmt = $pdo->prepare("INSERT INTO profiles (user_id, bio) VALUES (?, ?)");
    $stmt->execute([$userId, 'Bio']);
    
    // Create preferences
    $stmt = $pdo->prepare("INSERT INTO preferences (user_id, theme) VALUES (?, ?)");
    $stmt->execute([$userId, 'dark']);
    
    $pdo->commit();
    echo "User created successfully";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
```

### 5. Handle Duplicate Key Errors

```php
<?php
try {
    $stmt = $pdo->prepare("INSERT INTO users (email, name) VALUES (?, ?)");
    $stmt->execute(['john@example.com', 'John']);
    
    $userId = $pdo->lastInsertId();
} catch (PDOException $e) {
    if ($e->getCode() == '23000') { // Duplicate key error
        echo "Email already exists";
    } else {
        throw $e;
    }
}
?>
```

---

## Troubleshooting

### lastInsertId() Returns 0

```php
<?php
// Problem: Not inserting with auto-increment column
$stmt = $pdo->prepare("INSERT INTO users (id, name) VALUES (?, ?)");
$stmt->execute([null, 'John']); // null doesn't trigger auto-increment
$id = $pdo->lastInsertId(); // Returns 0

// Solution: Don't include id column or omit with proper NULL
$stmt = $pdo->prepare("INSERT INTO users (name) VALUES (?)");
$stmt->execute(['John']);
$id = $pdo->lastInsertId(); // Returns correct ID
?>
```

### ID Not Incrementing

```php
<?php
// Check table structure
$stmt = $pdo->query("SHOW CREATE TABLE users");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo $result['Create Table'];

// Look for AUTO_INCREMENT in output
// If missing, update column:
$pdo->exec("ALTER TABLE users MODIFY id INT AUTO_INCREMENT");
?>
```

### Multiple Inserts Getting Same ID

```php
<?php
// This should never happen with proper implementation
// But if using string concatenation instead of prepared statements:

// WRONG - Security issue and ID problem
$query = "INSERT INTO users (name) VALUES ('$name')";
$pdo->exec($query); // May not work correctly

// RIGHT - Use prepared statements
$stmt = $pdo->prepare("INSERT INTO users (name) VALUES (?)");
$stmt->execute([$name]);
$id = $pdo->lastInsertId();
?>
```

---

## Complete Examples

### User Registration with Auto-Increment

```php
<?php
class UserService {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function register($name, $email, $password) {
        try {
            $this->pdo->beginTransaction();
            
            // Insert user
            $stmt = $this->pdo->prepare("
                INSERT INTO users (name, email, password_hash, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt->execute([$name, $email, $hashedPassword]);
            
            // Get user ID
            $userId = $this->pdo->lastInsertId();
            
            // Create user profile
            $stmt = $this->pdo->prepare("
                INSERT INTO user_profiles (user_id, bio, avatar_url) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$userId, '', 'default.jpg']);
            
            // Create user preferences
            $stmt = $this->pdo->prepare("
                INSERT INTO user_preferences (user_id, theme, notifications_enabled) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$userId, 'light', true]);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'user_id' => $userId,
                'message' => 'User registered successfully'
            ];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            
            return [
                'success' => false,
                'message' => $e->getCode() == '23000' ? 'Email already exists' : 'Registration failed'
            ];
        }
    }
}

// Usage
$userService = new UserService($pdo);
$result = $userService->register('John', 'john@example.com', 'password123');

if ($result['success']) {
    echo "User ID: " . $result['user_id'];
}
?>
```

### Batch Insert with IDs

```php
<?php
class ProductService {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function importProducts($products) {
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("
                INSERT INTO products (name, price, category_id, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            
            $importedIds = [];
            
            foreach ($products as $product) {
                $stmt->execute([
                    $product['name'],
                    $product['price'],
                    $product['category_id'],
                ]);
                
                $productId = $this->pdo->lastInsertId();
                $importedIds[] = $productId;
                
                // Create related records
                if (isset($product['tags'])) {
                    $this->attachTags($productId, $product['tags']);
                }
            }
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'imported_count' => count($importedIds),
                'product_ids' => $importedIds,
            ];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            
            return [
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ];
        }
    }
    
    private function attachTags($productId, $tags) {
        $stmt = $this->pdo->prepare("
            INSERT INTO product_tags (product_id, tag) 
            VALUES (?, ?)
        ");
        
        foreach ($tags as $tag) {
            $stmt->execute([$productId, $tag]);
        }
    }
}

// Usage
$productService = new ProductService($pdo);
$products = [
    ['name' => 'Laptop', 'price' => 999, 'category_id' => 1, 'tags' => ['electronics', 'computers']],
    ['name' => 'Mouse', 'price' => 29, 'category_id' => 1, 'tags' => ['electronics', 'accessories']],
];

$result = $productService->importProducts($products);
echo "Imported {$result['imported_count']} products";
?>
```

---

## See Also

- [Executing SQL Statements](5-execute-sql.md)
- [Database Transactions](11-database-transaction.md)
- [Fetching Data](9-fetch-data.md)
