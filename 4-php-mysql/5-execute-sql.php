<?php
// Database connection settings
$host = '127.0.0.1';
$db   = 'sample_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Set up DSN and options
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// Create PDO instance
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Create table if not exists
$createTableSql = "
    CREATE TABLE IF NOT EXISTS customers (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE
    )
";
$pdo->exec($createTableSql);

// Insert data using execute()
$sql = "INSERT INTO customers (name, email) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);
$name = 'Alex Hong';
$email = 'alex@example.com';

if ($stmt->execute([$name, $email])) {
    echo "Customer inserted successfully.<br>";
} else {
    echo "Insert failed.<br>";
}

// Select data using execute()
$sql = "SELECT * FROM customers WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);
$cust = $stmt->fetch();

if ($cust) {
    echo "User found: " . $cust['name'];
} else {
    echo "User not found.";
}
?>