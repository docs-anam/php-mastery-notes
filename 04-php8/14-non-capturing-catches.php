<?php
/**
 * Non-Capturing Catches in PHP 8.0
 *
 * PHP 8.0 introduced the ability to use catch blocks without specifying a variable
 * for the caught exception. This is useful when you want to handle an exception
 * type but do not need to access the exception object itself.
 *
 * Syntax:
 *   catch (ExceptionType) {
 *       // handle exception, but no variable is available
 *   }
 *
 * Benefits:
 * - Cleaner code when the exception variable is not needed.
 * - Avoids unused variable warnings.
 * - Improves readability.
 *
 * Example:
 */

try {
    throw new RuntimeException("Something went wrong!");
} catch (RuntimeException) {
    // Handle the exception, but we don't need the exception object
    echo "Caught a runtime exception.\n";
}

// Traditional way (before PHP 8.0):
try {
    throw new InvalidArgumentException("Invalid argument!");
} catch (InvalidArgumentException $e) {
    // $e is available, but not used
    echo "Caught an invalid argument exception.\n";
}