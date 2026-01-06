<?php
/**
 * SQL Injection: Detailed Summary and Executable Example (with PDO)
 *
 * What is SQL Injection?
 * ----------------------
 * SQL Injection is a critical security vulnerability that occurs when an attacker can manipulate SQL queries by injecting malicious input into application code.
 * This typically happens when user input is directly included in SQL statements without proper validation or escaping.
 *
 * How Does SQL Injection Work?
 * ----------------------------
 * - The attacker provides specially crafted input.
 * - If the application concatenates this input into a SQL query, the attacker can alter the query's logic.
 * - This can lead to unauthorized data access, data modification, or even deletion of data.
 *
 * Example of Vulnerable Code:
 * ---------------------------
 * Suppose you have a login form that takes a username and password:
 */
if (isset($_GET['username']) && isset($_GET['password'])) {
    $username = $_GET['username']; //e.g 'admin' --
    $password = $_GET['password'];

    // Vulnerable code: Directly concatenating user input into SQL query
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=testdb', 'root', 'root');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $pdo->query($sql);

        if ($result && $result->rowCount() > 0) {
            echo "Login successful (vulnerable code).";
        } else {
            echo "Login failed (vulnerable code).";
        }
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
/**
 * If an attacker enters the following as username:
 *   admin' -- 
 * and anything as password, the query becomes:
 *   SELECT * FROM users WHERE username = 'admin' -- ' AND password = '...'
 * The double dash (--) comments out the rest of the query, allowing login as 'admin' without a password.
 *
 * Consequences of SQL Injection:
 * ------------------------------
 * - Unauthorized access to sensitive data
 * - Data modification or deletion
 * - Bypassing authentication
 * - Gaining administrative privileges
 * - Full database compromise
 *
 * How to Prevent SQL Injection:
 * -----------------------------
 * 1. Use Prepared Statements and Parameterized Queries (PDO or MySQLi)
 * 2. Validate and sanitize all user inputs
 * 3. Use least privilege database accounts
 * 4. Regularly update and patch software
 * 5. Employ web application firewalls
 *
 * Example of Secure Code (using PDO Prepared Statements):
 */
if (isset($_GET['username']) && isset($_GET['password'])) {
    $username = $_GET['username']; //e.g 'admin' --
    $password = $_GET['password'];

    // Secure code: Using prepared statements with PDO
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=testdb', 'root', 'root');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $result = $stmt->fetchAll();

        if ($result && count($result) > 0) {
            echo "Login successful (secure code).";
        } else {
            echo "Login failed (secure code).";
        }
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
/**
 * Summary:
 * --------
 * - Never concatenate user input directly into SQL queries.
 * - Always use prepared statements to prevent SQL injection.
 * - Treat all user input as untrusted.
 */
?>