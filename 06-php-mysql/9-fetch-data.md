# Fetching Data

## Overview

Fetching data is the process of retrieving results from executed queries and making them accessible to your PHP application. PDO offers multiple methods to fetch results in different formats, from simple associative arrays to complex objects.

---

## Table of Contents

1. Basic Fetch Methods
2. Fetch Modes
3. Fetching Single Row
4. Fetching Multiple Rows
5. Fetching Columns
6. Mapping to Objects
7. Handling No Results
8. Performance Considerations
9. Complete Examples

---

## Basic Fetch Methods

### fetch() vs fetchAll()

```php
<?php
// Execute query
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = ?");
$stmt->execute(['active']);

// fetch() - Get one row at a time
while ($row = $stmt->fetch()) {
    echo $row['name'];
}

// vs

// fetchAll() - Get all rows at once
$stmt->execute(['active']);
$rows = $stmt->fetchAll();
foreach ($rows as $row) {
    echo $row['name'];
}
?>
```

### Method Comparison

```php
<?php
// fetch() - Memory efficient for large result sets
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = ?");
$stmt->execute(['active']);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Process one row at a time
    // Memory usage stays low
}

// fetchAll() - Convenient for small result sets
$stmt->execute(['active']);
$allRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
// All rows in memory

// fetchColumn() - Get single column value
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE status = ?");
$stmt->execute(['active']);
$count = $stmt->fetchColumn();
echo "Active users: $count";
?>
```

---

## Fetch Modes

### PDO::FETCH_ASSOC

```php
<?php
// Returns associative array (column names as keys)
$stmt = $pdo->prepare("SELECT id, name, email FROM users");
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo $user['id'];     // Access by column name
echo $user['name'];
echo $user['email'];

// Result:
// Array (
//     [id] => 1
//     [name] => John
//     [email] => john@example.com
// )
?>
```

### PDO::FETCH_NUM

```php
<?php
// Returns numeric array (column index as keys)
$stmt = $pdo->prepare("SELECT id, name, email FROM users");
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_NUM);
echo $user[0];  // id
echo $user[1];  // name
echo $user[2];  // email

// Result:
// Array (
//     [0] => 1
//     [1] => John
//     [2] => john@example.com
// )
?>
```

### PDO::FETCH_BOTH (default)

```php
<?php
// Returns array with both numeric and associative keys
$stmt = $pdo->prepare("SELECT id, name FROM users");
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_BOTH);
echo $user[0];        // Access by index
echo $user['id'];     // Access by column name

// Result:
// Array (
//     [0] => 1
//     [id] => 1
//     [1] => John
//     [name] => John
// )
?>
```

### PDO::FETCH_OBJ

```php
<?php
// Returns object with column names as properties
$stmt = $pdo->prepare("SELECT id, name, email FROM users");
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_OBJ);
echo $user->id;
echo $user->name;
echo $user->email;

// Result: stdClass Object (
//     [id] => 1
//     [name] => John
//     [email] => john@example.com
// )
?>
```

### PDO::FETCH_CLASS

```php
<?php
// Map result to class
class User {
    public $id;
    public $name;
    public $email;
    
    public function getFullInfo() {
        return "$this->name ($this->email)";
    }
}

$stmt = $pdo->prepare("SELECT id, name, email FROM users");
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_CLASS, 'User');
echo $user->getFullInfo();

// Result: John (john@example.com)
?>
```

### Fetch Mode Selection

```php
<?php
// Set default fetch mode for connection
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Now all queries default to associative array
$stmt = $pdo->query("SELECT * FROM users");
$user = $stmt->fetch(); // Returns associative array

// Or specify per query
$stmt = $pdo->query("SELECT * FROM users");
$user = $stmt->fetch(PDO::FETCH_OBJ); // Override default
?>
```

---

## Fetching Single Row

### fetch() Method

```php
<?php
// Fetch single row
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([1]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "Found: " . $user['name'];
} else {
    echo "Not found";
}
?>
```

### fetchColumn() Method

```php
<?php
// Fetch single column value from first row
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([1]);

$email = $stmt->fetchColumn();
echo "Email: $email";

// Fetch specific column
$stmt = $pdo->prepare("SELECT id, email, name FROM users WHERE id = ?");
$stmt->execute([1]);

$email = $stmt->fetchColumn(1); // Get 2nd column (index 1)
echo "Email: $email";
?>
```

### Check if Result Exists

```php
<?php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute(['test@example.com']);

if ($stmt->rowCount() > 0) {
    $user = $stmt->fetch();
    echo "User found";
} else {
    echo "User not found";
}
?>
```

---

## Fetching Multiple Rows

### fetchAll() Method

```php
<?php
// Fetch all rows as associative arrays
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = ?");
$stmt->execute(['active']);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    echo $user['name'];
}
?>
```

### Iterator Pattern

```php
<?php
// Use foreach directly on statement
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = ?");
$stmt->execute(['active']);

// Set fetch mode before iteration
$stmt->setFetchMode(PDO::FETCH_ASSOC);

foreach ($stmt as $user) {
    echo $user['name'];
}
?>
```

### Limit Results

```php
<?php
// Fetch first 10 rows
$stmt = $pdo->prepare("SELECT * FROM users LIMIT 10");
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Retrieved " . count($users) . " users";
?>
```

### Fetch with Loop

```php
<?php
// Process large result sets efficiently
$stmt = $pdo->prepare("SELECT * FROM large_table");
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Process one row at a time
    // Memory stays low
    processRow($row);
}
?>
```

---

## Fetching Columns

### Single Column from All Rows

```php
<?php
// Get all user IDs
$stmt = $pdo->prepare("SELECT id FROM users");
$stmt->execute();

$ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
// Result: [1, 2, 3, 4, 5]

foreach ($ids as $id) {
    echo "ID: $id";
}
?>
```

### Specific Column Index

```php
<?php
// Get 2nd column from all rows
$stmt = $pdo->prepare("SELECT id, email, name FROM users");
$stmt->execute();

// Column index 1 = email
$emails = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
// Result: ['john@example.com', 'jane@example.com', ...]
?>
```

### Create Key-Value Array

```php
<?php
// Create array with first column as key, second as value
$stmt = $pdo->prepare("SELECT id, name FROM users");
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
// Result: [1 => 'John', 2 => 'Jane', 3 => 'Bob']

foreach ($users as $id => $name) {
    echo "$id => $name";
}
?>
```

### Group by Column

```php
<?php
// Group results by first column
$stmt = $pdo->prepare("
    SELECT status, id, name FROM users
");
$stmt->execute();

$usersByStatus = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
// Result:
// [
//     'active' => [
//         ['id' => 1, 'name' => 'John'],
//         ['id' => 2, 'name' => 'Jane']
//     ],
//     'inactive' => [
//         ['id' => 3, 'name' => 'Bob']
//     ]
// ]
?>
```

---

## Mapping to Objects

### Simple Object Mapping

```php
<?php
class User {
    public $id;
    public $name;
    public $email;
}

$stmt = $pdo->prepare("SELECT id, name, email FROM users");
$stmt->execute();

// Fetch as User objects
$users = $stmt->fetchAll(PDO::FETCH_CLASS, 'User');

foreach ($users as $user) {
    echo $user->name;
    echo $user->email;
}
?>
```

### Object with Constructor

```php
<?php
class Product {
    public $id;
    public $name;
    public $price;
    
    public function __construct() {
        // Constructor called before properties are set
    }
    
    public function getDiscountedPrice($percent) {
        return $this->price * (1 - $percent / 100);
    }
}

$stmt = $pdo->prepare("SELECT id, name, price FROM products");
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_CLASS, 'Product');

foreach ($products as $product) {
    echo $product->name . ": " . $product->getDiscountedPrice(10);
}
?>
```

### Object with Custom Initialization

```php
<?php
class Order {
    private $id;
    private $userId;
    private $total;
    private $items;
    
    public function setId($id) { $this->id = $id; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function setTotal($total) { $this->total = $total; }
    
    public function loadItems($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$this->id]);
        $this->items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$stmt = $pdo->prepare("SELECT id, user_id as userId, total FROM orders");
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_CLASS, 'Order');

// Note: Property names must match column names (after AS renaming)
?>
```

---

## Handling No Results

### Check Row Count

```php
<?php
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([999]);

if ($stmt->rowCount() === 0) {
    echo "No user found";
} else {
    $user = $stmt->fetch();
}
?>
```

### Check Fetch Result

```php
<?php
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([999]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user === false) {
    echo "No user found";
} else {
    echo "Found: " . $user['name'];
}
?>
```

### Handle Empty Results

```php
<?php
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = ?");
$stmt->execute(['archived']);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($users)) {
    echo "No archived users found";
} else {
    echo "Found " . count($users) . " users";
}
?>
```

---

## Performance Considerations

### Memory vs Speed

```php
<?php
// For small result sets (< 100 rows)
$stmt = $pdo->query("SELECT * FROM users LIMIT 50");
$users = $stmt->fetchAll(); // Load all at once

// For large result sets (> 1000 rows)
$stmt = $pdo->query("SELECT * FROM large_table");

while ($row = $stmt->fetch()) {
    // Process incrementally
    // Memory usage stays constant
}
?>
```

### Batch Processing

```php
<?php
// Process in batches
$offset = 0;
$batchSize = 1000;

while (true) {
    $stmt = $pdo->prepare("SELECT * FROM large_table LIMIT ? OFFSET ?");
    $stmt->execute([$batchSize, $offset]);
    
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($rows)) {
        break; // No more rows
    }
    
    // Process batch
    processBatch($rows);
    
    $offset += $batchSize;
}
?>
```

### Lazy Loading

```php
<?php
class UserRepository {
    private $pdo;
    private $stmt;
    private $currentRow;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function fetchLarge() {
        $this->stmt = $this->pdo->query("SELECT * FROM large_table");
        return $this;
    }
    
    public function current() {
        return $this->currentRow;
    }
    
    public function next() {
        $this->currentRow = $this->stmt->fetch(PDO::FETCH_ASSOC);
        return $this->currentRow !== false;
    }
    
    public function process(callable $callback) {
        while ($this->next()) {
            $callback($this->current());
        }
    }
}

// Usage
$repo = new UserRepository($pdo);
$repo->fetchLarge()->process(function($row) {
    echo $row['name'] . "\n";
});
?>
```

---

## Complete Examples

### Paginated Results with Fetch

```php
<?php
class PaginatedResults {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function get($page = 1, $perPage = 20, $table = 'users') {
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $countStmt = $this->pdo->query("SELECT COUNT(*) FROM $table");
        $total = $countStmt->fetchColumn();
        
        // Get paginated data
        $dataStmt = $this->pdo->prepare(
            "SELECT * FROM $table ORDER BY id DESC LIMIT ? OFFSET ?"
        );
        $dataStmt->execute([$perPage, $offset]);
        $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'data' => $data,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'pages' => ceil($total / $perPage),
        ];
    }
}

// Usage
$paginator = new PaginatedResults($pdo);
$result = $paginator->get(1, 10);

echo "Page {$result['page']} of {$result['pages']}\n";
foreach ($result['data'] as $row) {
    echo $row['name'] . "\n";
}
?>
```

### Stream Large File to Browser

```php
<?php
class DataExporter {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function exportCSV($table) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="export.csv"');
        
        $output = fopen('php://output', 'w');
        
        $stmt = $this->pdo->query("SELECT * FROM $table");
        
        // Write header
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, array_keys($row));
            fputcsv($output, $row);
        }
        
        // Write data rows
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row);
        }
        
        fclose($output);
    }
}

// Usage
$exporter = new DataExporter($pdo);
$exporter->exportCSV('users');
?>
```

---

## See Also

- [Querying SQL Statements](6-query-sql.md)
- [Executing SQL Statements](5-execute-sql.md)
- [Prepared Statements](8-prepare-statement.md)
