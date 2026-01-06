<?php
// Accessing MySQL in Terminal and Creating a Sample Database

// 1. Open Terminal
// - On macOS, open the Terminal app.

// 2. Start MySQL Server
// - If using MAMP, ensure MySQL server is running via the MAMP control panel.

// 3. Access MySQL
// - Enter the following command (default MAMP credentials):
//     /Applications/MAMP/Library/bin/mysql -u root -p
// - When prompted, enter the password (default is 'root' for MAMP).

// 4. MySQL Prompt
// - You should see the MySQL prompt: mysql>

// 5. Create a Sample Database
// - Run:
//     CREATE DATABASE sample_db;
// - To use the new database:
//     USE sample_db;

// 6. Create a Sample Table
// - Example:
//     CREATE TABLE users (
//         id INT AUTO_INCREMENT PRIMARY KEY,
//         name VARCHAR(100),
//         email VARCHAR(100)
//     );

// 7. Insert Sample Data
// - Example:
//     INSERT INTO users (name, email) VALUES ('Alice', 'alice@example.com');
//     INSERT INTO users (name, email) VALUES ('Bob', 'bob@example.com');

// 8. View Data
// - Example:
//     SELECT * FROM users;

// 9. Exit MySQL
// - Type:
//     exit

// Summary:
// You access MySQL via terminal using the 'mysql' command, create a database with 'CREATE DATABASE', select it with 'USE', create tables, insert data, and query as needed.
?>
