<?php
/*
Auto Increment in PDO PHP - Detailed Summary

1. What is Auto Increment?
- Auto increment is a database feature that automatically generates a unique integer for a column (commonly the primary key) when a new row is inserted.
- Used to uniquely identify each record, typically in an 'id' column.

2. How to Define Auto Increment in MySQL?
- In your table schema, define a column as INT and add AUTO_INCREMENT.
    Example:
        CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL
        );

3. Connecting to MySQL with PDO:
- Use PDO for secure and flexible database access.

4. Inserting Data (without specifying the auto-increment column):
- Omit the 'id' column in your INSERT statement; MySQL will auto-generate it.

5. Retrieving the Last Inserted ID:
- Use $pdo->lastInsertId() to get the value of the auto-increment column for the last inserted row.

6. Full Executable Example:
*/

try {
        // 1. Connect to the database
        $pdo = new PDO('mysql:host=localhost;dbname=testdb;charset=utf8mb4', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2. Create table (if not exists)
        $pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(100) NOT NULL
                )
        ");

        // 3. Insert a new user (auto-increment id)
        $stmt = $pdo->prepare("INSERT INTO users (name) VALUES (:name)");
        $stmt->execute(['name' => 'Alice']);

        // 4. Get the last inserted id
        $lastId = $pdo->lastInsertId();

        // 5. Output the result
        echo "Inserted user ID: " . $lastId . PHP_EOL;

        // 6. Fetch and display all users
        $stmt = $pdo->query("SELECT * FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "All users:\n";
        foreach ($users as $user) {
                echo "ID: {$user['id']}, Name: {$user['name']}\n";
        }

} catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
}

/*
Notes:
- Only one AUTO_INCREMENT column per table.
- The column must be indexed (usually PRIMARY KEY).
- AUTO_INCREMENT values increase automatically; you can reset with ALTER TABLE users AUTO_INCREMENT = 1;
- Use prepared statements to prevent SQL injection.
*/
?>