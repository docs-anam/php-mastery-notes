# Querying SQL Statements

## Overview

Querying is the art of retrieving data from your database efficiently. This includes selecting data, filtering results, sorting, and processing the returned data in PHP. Effective querying skills are crucial for building performant applications.

---

## Table of Contents

1. Basic SELECT Queries
2. WHERE Clause Filtering
3. Sorting and Limiting Results
4. Joins and Relationships
5. Aggregation Functions
6. GROUP BY and HAVING
7. Processing Query Results
8. Common Query Patterns
9. Performance Optimization

---

## Basic SELECT Queries

### Simple Selection

```php
<?php
// Select all records
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Select specific columns
$stmt = $pdo->query("SELECT id, name, email FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Select with alias
$stmt = $pdo->query("SELECT id, name as full_name, email as contact_email FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

### Prepared Select Queries

```php
<?php
// With positional parameters
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([1]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// With named parameters
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute([':email' => 'john@example.com']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Multiple parameters
$stmt = $pdo->prepare("SELECT * FROM users WHERE age > ? AND status = ?");
$stmt->execute([18, 'active']);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

---

## WHERE Clause Filtering

### Comparison Operators

```php
<?php
// Equal
$stmt = $pdo->prepare("SELECT * FROM users WHERE age = ?");
$stmt->execute([25]);

// Not equal
$stmt = $pdo->prepare("SELECT * FROM users WHERE status != ?");
$stmt->execute(['inactive']);

// Greater than / Less than
$stmt = $pdo->prepare("SELECT * FROM users WHERE age > ? AND age < ?");
$stmt->execute([18, 65]);

// Greater than or equal / Less than or equal
$stmt = $pdo->prepare("SELECT * FROM users WHERE age >= ? OR age <= ?");
$stmt->execute([65, 18]);
?>
```

### LIKE Pattern Matching

```php
<?php
// Search for name starting with 'J'
$stmt = $pdo->prepare("SELECT * FROM users WHERE name LIKE ?");
$stmt->execute(['J%']);

// Search for email containing 'gmail'
$stmt = $pdo->prepare("SELECT * FROM users WHERE email LIKE ?");
$stmt->execute(['%gmail%']);

// Search for name ending with 'son'
$stmt = $pdo->prepare("SELECT * FROM users WHERE name LIKE ?");
$stmt->execute(['%son']);

// Case-insensitive search
$stmt = $pdo->prepare("SELECT * FROM users WHERE name LIKE ?");
$stmt->execute(['%john%']);
?>
```

### IN and NOT IN

```php
<?php
// IN clause
$status = ['active', 'pending', 'approved'];
$placeholders = implode(',', array_fill(0, count($status), '?'));
$stmt = $pdo->prepare("SELECT * FROM users WHERE status IN ($placeholders)");
$stmt->execute($status);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// NOT IN clause
$stmt = $pdo->prepare("SELECT * FROM users WHERE status NOT IN (?, ?)");
$stmt->execute(['inactive', 'deleted']);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

### BETWEEN

```php
<?php
// Between dates
$stmt = $pdo->prepare("SELECT * FROM orders WHERE created_at BETWEEN ? AND ?");
$stmt->execute(['2024-01-01', '2024-12-31']);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Between numbers
$stmt = $pdo->prepare("SELECT * FROM products WHERE price BETWEEN ? AND ?");
$stmt->execute([10, 100]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

### Complex WHERE Conditions

```php
<?php
// AND, OR combinations
$stmt = $pdo->prepare("
    SELECT * FROM users 
    WHERE (age > ? AND status = ?) 
    OR (age < ? AND premium = ?)
");
$stmt->execute([18, 'active', 18, 1]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// IS NULL / IS NOT NULL
$stmt = $pdo->prepare("SELECT * FROM users WHERE deleted_at IS NULL");
$stmt->execute();
$activeUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM users WHERE deleted_at IS NOT NULL");
$stmt->execute();
$deletedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

---

## Sorting and Limiting Results

### ORDER BY

```php
<?php
// Sort ascending (default)
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sort descending
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Multiple sort columns
$stmt = $pdo->query("SELECT * FROM users ORDER BY status ASC, created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sort by column position
$stmt = $pdo->query("SELECT id, name, email FROM users ORDER BY 2 ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

### LIMIT and OFFSET

```php
<?php
// Get first 10 records
$stmt = $pdo->query("SELECT * FROM users LIMIT 10");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get 10 records starting from position 5
$stmt = $pdo->query("SELECT * FROM users LIMIT 10 OFFSET 5");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Alternative syntax
$stmt = $pdo->query("SELECT * FROM users LIMIT 5, 10");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination
$page = 2;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute([$perPage, $offset]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

---

## Joins and Relationships

### INNER JOIN

```php
<?php
// Get users with their orders
$stmt = $pdo->query("
    SELECT users.id, users.name, orders.id as order_id, orders.total
    FROM users
    INNER JOIN orders ON users.id = orders.user_id
");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// With conditions
$stmt = $pdo->prepare("
    SELECT users.name, orders.total
    FROM users
    INNER JOIN orders ON users.id = orders.user_id
    WHERE orders.status = ?
");
$stmt->execute(['completed']);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

### LEFT JOIN

```php
<?php
// Get all users, including those without orders
$stmt = $pdo->query("
    SELECT users.name, COUNT(orders.id) as order_count
    FROM users
    LEFT JOIN orders ON users.id = orders.user_id
    GROUP BY users.id
");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

### Multiple Joins

```php
<?php
$stmt = $pdo->query("
    SELECT 
        users.name,
        orders.id as order_id,
        order_items.product_id,
        products.name as product_name
    FROM users
    INNER JOIN orders ON users.id = orders.user_id
    INNER JOIN order_items ON orders.id = order_items.order_id
    INNER JOIN products ON order_items.product_id = products.id
    ORDER BY orders.created_at DESC
");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

---

## Aggregation Functions

### COUNT, SUM, AVG, MIN, MAX

```php
<?php
// Count all users
$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo $result['total_users'];

// Sum order totals
$stmt = $pdo->query("SELECT SUM(total) as total_revenue FROM orders");
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Average order value
$stmt = $pdo->prepare("SELECT AVG(total) as average_order FROM orders WHERE status = ?");
$stmt->execute(['completed']);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Minimum and maximum prices
$stmt = $pdo->query("SELECT MIN(price) as min_price, MAX(price) as max_price FROM products");
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Multiple aggregates
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_orders,
        SUM(total) as total_revenue,
        AVG(total) as average_order,
        MIN(total) as min_order,
        MAX(total) as max_order
    FROM orders
    WHERE status = 'completed'
");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
?>
```

---

## GROUP BY and HAVING

### Basic GROUP BY

```php
<?php
// Sales by product
$stmt = $pdo->query("
    SELECT product_id, COUNT(*) as sales_count, SUM(amount) as total_sales
    FROM order_items
    GROUP BY product_id
    ORDER BY total_sales DESC
");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Users by status
$stmt = $pdo->query("
    SELECT status, COUNT(*) as count
    FROM users
    GROUP BY status
");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

### GROUP BY with HAVING

```php
<?php
// Products sold more than 10 times
$stmt = $pdo->prepare("
    SELECT product_id, COUNT(*) as sales_count
    FROM order_items
    GROUP BY product_id
    HAVING COUNT(*) > ?
    ORDER BY sales_count DESC
");
$stmt->execute([10]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Users with more than 5 orders
$stmt = $pdo->prepare("
    SELECT user_id, COUNT(*) as order_count, SUM(total) as total_spent
    FROM orders
    GROUP BY user_id
    HAVING COUNT(*) > ? AND SUM(total) > ?
    ORDER BY total_spent DESC
");
$stmt->execute([5, 1000]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

---

## Processing Query Results

### Fetch Methods

```php
<?php
$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();

// Fetch single row
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch all rows
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch single column value
$name = $stmt->fetchColumn(0);

// Iterate through results
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['name'];
}
?>
```

### Map to Objects

```php
<?php
class User {
    public $id;
    public $name;
    public $email;
    public $created_at;
}

$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();

// Fetch as objects
$users = $stmt->fetchAll(PDO::FETCH_CLASS, 'User');

foreach ($users as $user) {
    echo $user->name;
}
?>
```

### Custom Processing

```php
<?php
$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();

$usersByStatus = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $status = $row['status'];
    if (!isset($usersByStatus[$status])) {
        $usersByStatus[$status] = [];
    }
    $usersByStatus[$status][] = $row;
}

print_r($usersByStatus);
?>
```

---

## Common Query Patterns

### Pagination

```php
<?php
function getPaginatedUsers($pdo, $page = 1, $perPage = 20) {
    $offset = ($page - 1) * $perPage;
    
    // Get total count
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $result['total'];
    
    // Get paginated results
    $stmt = $pdo->prepare("
        SELECT * FROM users 
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$perPage, $offset]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'users' => $users,
        'total' => $total,
        'page' => $page,
        'perPage' => $perPage,
        'totalPages' => ceil($total / $perPage),
    ];
}

$result = getPaginatedUsers($pdo, 2, 10);
echo "Page {$result['page']} of {$result['totalPages']}";
?>
```

### Search

```php
<?php
function searchUsers($pdo, $query) {
    $searchTerm = "%$query%";
    
    $stmt = $pdo->prepare("
        SELECT * FROM users
        WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?
        ORDER BY name ASC
    ");
    
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$results = searchUsers($pdo, 'john');
?>
```

### Filtering with Multiple Conditions

```php
<?php
function getFilteredUsers($pdo, $filters = []) {
    $sql = "SELECT * FROM users WHERE 1=1";
    $params = [];
    
    if (isset($filters['status'])) {
        $sql .= " AND status = ?";
        $params[] = $filters['status'];
    }
    
    if (isset($filters['age_min'])) {
        $sql .= " AND age >= ?";
        $params[] = $filters['age_min'];
    }
    
    if (isset($filters['age_max'])) {
        $sql .= " AND age <= ?";
        $params[] = $filters['age_max'];
    }
    
    if (isset($filters['search'])) {
        $sql .= " AND (name LIKE ? OR email LIKE ?)";
        $searchTerm = "%{$filters['search']}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$filters = [
    'status' => 'active',
    'age_min' => 18,
    'age_max' => 65,
    'search' => 'john',
];

$users = getFilteredUsers($pdo, $filters);
?>
```

---

## Performance Optimization

### Use Indexes

```php
<?php
// Create indexes for frequently searched columns
$pdo->exec("CREATE INDEX idx_users_email ON users(email)");
$pdo->exec("CREATE INDEX idx_orders_user_id ON orders(user_id)");
$pdo->exec("CREATE INDEX idx_orders_status ON orders(status)");

// These queries will now be faster
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
?>
```

### Select Only Needed Columns

```php
<?php
// BAD - Selecting unnecessary data
$stmt = $pdo->query("SELECT * FROM users");

// GOOD - Select only needed columns
$stmt = $pdo->query("SELECT id, name, email FROM users");
?>
```

### Avoid N+1 Queries

```php
<?php
// BAD - N+1 query problem
$stmt = $pdo->query("SELECT * FROM users LIMIT 10");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    // This runs a query for each user!
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $user['orders'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// GOOD - Single query with JOIN
$stmt = $pdo->query("
    SELECT users.*, orders.id as order_id, orders.total
    FROM users
    LEFT JOIN orders ON users.id = orders.user_id
    LIMIT 10
");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

---

## See Also

- [SQL Execution](5-execute-sql.md)
- [SQL Injection Prevention](7-sql-injection.md)
- [Prepared Statements](8-prepare-statement.md)
- [Database Transactions](11-database-transaction.md)
