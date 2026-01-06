<?php
// Summary:
// This sample demonstrates how to connect to a MySQL database using PDO in PHP,
// execute a SELECT query, and fetch results.

// Database credentials
$host = '127.0.0.1';
$db   = 'sample_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Data Source Name
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Example query: fetch all users
    $stmt = $pdo->query('SELECT id, name, email FROM customers');

    foreach ($stmt as $row) {
        echo "ID: {$row['id']} | Name: {$row['name']} | Email: {$row['email']}".PHP_EOL;
    }

} catch (PDOException $e) {
    // Handle connection or query error
    echo "Database error: " . $e->getMessage();
}
?>