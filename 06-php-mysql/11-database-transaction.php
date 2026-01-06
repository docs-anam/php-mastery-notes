<?php
/**
 * Database Transactions in PDO (PHP)
 *
 * A database transaction is a sequence of operations performed as a single logical unit of work.
 * Transactions ensure data integrity and consistency, especially in scenarios involving multiple related queries.
 * PDO (PHP Data Objects) provides methods to manage transactions easily.
 *
 * Key Concepts:
 * - Atomicity: All operations in a transaction succeed or none do.
 * - Consistency: The database remains in a valid state before and after the transaction.
 * - Isolation: Transactions are isolated from each other.
 * - Durability: Once committed, changes are permanent.
 *
 * PDO Transaction Methods:
 * - beginTransaction(): Starts a new transaction.
 * - commit(): Commits the current transaction, making all changes permanent.
 * - rollBack(): Rolls back the current transaction, undoing all changes since beginTransaction().
 * - inTransaction(): Checks if a transaction is currently active.
 *
 * Example Usage:
 */

try {
    $pdo = new PDO('mysql:host=localhost;dbname=testdb', 'username', 'password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Start transaction
    $pdo->beginTransaction();

    // Example queries
    $pdo->exec("INSERT INTO accounts (name, balance) VALUES ('Alice', 1000)");
    $pdo->exec("UPDATE accounts SET balance = balance - 100 WHERE name = 'Alice'");
    $pdo->exec("UPDATE accounts SET balance = balance + 100 WHERE name = 'Bob'");

    // Commit transaction
    $pdo->commit();
    echo "Transaction committed successfully.";
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Transaction failed: " . $e->getMessage();
}

/**
 * Best Practices:
 * - Always use transactions for multiple related queries that must succeed or fail together.
 * - Handle exceptions and always roll back on failure.
 * - Keep transactions as short as possible to avoid locking issues.
 */
?>