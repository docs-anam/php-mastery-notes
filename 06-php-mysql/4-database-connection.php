<?php
/*
Summary: Database Connection with PDO in PHP

PDO (PHP Data Objects) is a database access layer providing a uniform method of access to multiple databases in PHP. It offers a data-access abstraction layer, which means you can use the same functions to issue SQL commands and fetch data, regardless of which database type you are using.

Key Points:
1. PDO supports multiple databases (MySQL, PostgreSQL, SQLite, etc.).
2. It provides a secure way to connect to databases using prepared statements, which help prevent SQL injection.
3. PDO allows for error handling using exceptions.

Basic Steps to Connect to a Database with PDO:

1. Create a new PDO instance:
    $connection = new PDO($dsn, $username, $password, $options);

    - $dsn (Data Source Name): Specifies the database type, host, database name, and charset.
    - $username: Database username.
    - $password: Database password.
    - $options: Optional array of driver-specific connection options.

2. Handle connection errors using try-catch:
    Use try-catch blocks to catch PDOException if the connection fails.

3. Set error mode:
    It's recommended to set the error mode to PDO::ERRMODE_EXCEPTION for better error handling.

Example:
*/
// Step 1: Define database connection parameters
$host = '127.0.0.1';            // Database server host (usually 'localhost' or 127.0.0.1)
$db   = 'sample_db';   // Name of your database
$user = 'root';        // Your database username
$pass = '';        // Your database password
$charset = 'utf8mb4';           // Character set for the connection

// Step 2: Create the DSN (Data Source Name) string
// Format: "mysql:host=HOST;dbname=DBNAME;charset=CHARSET"
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Step 3: Set PDO options for better security and error handling
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch results as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
];

// Step 4: Attempt to create a PDO instance (connect to the database)
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Database connection successful!<br>";

} catch (PDOException $e) {
    // Step 5: Handle connection errors
    echo "Database connection failed: " . $e->getMessage();
    // Optionally, log the error message instead of displaying it
}

// Step 6: Close the connection (explicitly)
// Setting the PDO object to null closes the connection
$pdo = null;
echo "Database connection closed.<br>";

/** Advantages of PDO:
 * - Database driver independence.
 * - Support for prepared statements and transactions.
 * - Better security against SQL injection.
 
 * Best Practices:
 * - Always use prepared statements for queries with user input.
 * - Handle exceptions properly.
 * - Close the connection by setting the PDO object to null when done.
*/
?>