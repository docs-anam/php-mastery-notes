<?php
/**
 * Summary: PHP Data Objects (PDO)
 *
 * PDO (PHP Data Objects) is a database access layer providing a uniform method of access to multiple databases in PHP.
 * It offers a data-access abstraction layer, which means you can use the same functions to issue SQL commands and fetch data,
 * regardless of which database type you are using (MySQL, PostgreSQL, SQLite, etc.).
 *
 * Key Features:
 * 1. Database Abstraction: PDO allows you to switch databases with minimal code changes.
 * 2. Prepared Statements: PDO supports prepared statements, which help prevent SQL injection attacks and improve performance.
 * 3. Error Handling: PDO provides robust error handling using exceptions.
 * 4. Multiple Fetch Modes: Fetch data as associative arrays, objects, or both.
 * 5. Transactions: PDO supports database transactions for reliable data manipulation.
 *
 * Basic Usage Example:
 */

// 1. Create a new PDO instance (connect to database)
$dsn = 'mysql:host=localhost;dbname=testdb;charset=utf8mb4';
$username = 'dbuser';
$password = 'dbpass';

try {
    $pdo = new PDO($dsn, $username, $password);
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Prepare and execute a query
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => 'user@example.com']);

    // 3. Fetch results
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    print_r($user);

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

/**
 * Best Practices:
 * - Always use prepared statements to prevent SQL injection.
 * - Handle exceptions for robust error management.
 * - Use transactions for multiple related queries.
 *
 * PDO is the recommended way to interact with databases in modern PHP applications.
 */
?>