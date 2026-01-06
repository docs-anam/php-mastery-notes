<?php
// Database connection settings
$host = 'localhost';
$db   = 'your_database_name';
$user = 'your_username';
$pass = 'your_password';
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
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Prepare and execute
    $stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE status = :status');
    $status = 'active';
    $stmt->execute(['status' => $status]);

    // 1. Fetch all as associative arrays (default)
    echo "<h3>FETCH_ASSOC (default):</h3>";
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $user) {
        echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}<br>";
    }

    // 2. Fetch all as numeric arrays
    $stmt->execute(['status' => $status]); // Re-execute for fresh result set
    echo "<h3>FETCH_NUM:</h3>";
    $users = $stmt->fetchAll(PDO::FETCH_NUM);
    foreach ($users as $user) {
        echo "ID: {$user[0]}, Name: {$user[1]}, Email: {$user[2]}<br>";
    }

    // 3. Fetch all as objects
    $stmt->execute(['status' => $status]);
    echo "<h3>FETCH_OBJ:</h3>";
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    foreach ($users as $user) {
        echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}<br>";
    }

    // 4. Fetch a single row (first row only)
    $stmt->execute(['status' => $status]);
    echo "<h3>fetch() - single row (ASSOC):</h3>";
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}<br>";
    }

    // 5. Fetch a single column (e.g., just the name)
    $stmt->execute(['status' => $status]);
    echo "<h3>FETCH_COLUMN (names only):</h3>";
    while ($name = $stmt->fetchColumn(1)) { // 1 = second column (name)
        echo "Name: $name<br>";
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>