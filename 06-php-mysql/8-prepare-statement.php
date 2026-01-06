<?php
/**
 * Detailed Summary: Prepared Statements in PDO (PHP Data Objects)
 *
 * PDO uses parameter binding to safely execute SQL queries.
 * Unlike mysqli's bind_param('s', ...), PDO uses constants for data types:
 * - PDO::PARAM_STR (string)
 * - PDO::PARAM_INT (integer)
 * - PDO::PARAM_BOOL (boolean)
 * - PDO::PARAM_NULL (null)
 *
 * You can bind parameters using:
 * - bindParam() (by reference, specify type)
 * - bindValue() (by value, specify type)
 * - execute(array) (auto-detects type, but you can cast values)
 */

try {
    $pdo = new PDO('mysql:host=localhost;dbname=testdb', 'username', 'password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- Example: Using bindParam() with explicit types (like bind_param('s', ...)) ---

    // String parameter
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $email = 'user@example.com';
    $stmt->bindParam(':email', $email, PDO::PARAM_STR); // Similar to 's' in bind_param
    $stmt->execute();
    print_r($stmt->fetch(PDO::FETCH_ASSOC));

    // Integer parameter
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
    $id = 1;
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Similar to 'i' in bind_param
    $stmt->execute();
    print_r($stmt->fetch(PDO::FETCH_ASSOC));

    // Boolean parameter
    $stmt = $pdo->prepare('SELECT * FROM users WHERE active = :active');
    $active = true;
    $stmt->bindParam(':active', $active, PDO::PARAM_BOOL); // Similar to 'b' in bind_param
    $stmt->execute();
    print_r($stmt->fetch(PDO::FETCH_ASSOC));

    // Null parameter
    $stmt = $pdo->prepare('SELECT * FROM users WHERE deleted_at IS :deleted');
    $deleted = null;
    $stmt->bindParam(':deleted', $deleted, PDO::PARAM_NULL); // Similar to 's' with null in bind_param
    $stmt->execute();
    print_r($stmt->fetch(PDO::FETCH_ASSOC));

    // --- Using bindValue() with explicit types ---
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->bindValue(':username', 'john', PDO::PARAM_STR);
    $stmt->execute();
    print_r($stmt->fetch(PDO::FETCH_ASSOC));

    // --- Using execute(array) (types auto-detected) ---
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? AND active = ?');
    $stmt->execute([1, true]); // PHP will cast types as needed
    print_r($stmt->fetch(PDO::FETCH_ASSOC));

    // --- UPDATE Example ---
    $stmt = $pdo->prepare('UPDATE users SET name = :name WHERE id = :id');
    $stmt->bindValue(':name', 'Jane Doe', PDO::PARAM_STR);
    $stmt->bindValue(':id', 1, PDO::PARAM_INT);
    $stmt->execute();
    echo "Rows updated: " . $stmt->rowCount() . "\n";

    // --- DELETE Example ---
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([2]);
    echo "Rows deleted: " . $stmt->rowCount() . "\n";

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

/**
 * Notes:
 * - PDO::PARAM_STR is equivalent to 's' in mysqli bind_param.
 * - PDO::PARAM_INT is equivalent to 'i'.
 * - PDO::PARAM_BOOL is equivalent to 'b'.
 * - PDO::PARAM_NULL is used for null values.
 * - Always specify the type for clarity and security.
 */
?>