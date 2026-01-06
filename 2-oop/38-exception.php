<?php
/**
 * PHP OOP Exception Handling - Detailed Summary
 *
 * 1. What is an Exception?
 *    - An exception is an object that describes an error or unexpected behavior in a program.
 *    - In PHP, exceptions are used to handle errors gracefully in object-oriented code.
 *
 * 2. Exception Class Hierarchy:
 *    - The base class for all exceptions is Exception.
 *    - You can extend Exception to create custom exception types.
 *
 * 3. Throwing Exceptions:
 *    - Use the `throw` keyword to throw an exception.
 *    - Example: throw new Exception("Error message");
 *
 * 4. Catching Exceptions:
 *    - Use try-catch blocks to handle exceptions.
 *    - Syntax:
 *      try {
 *          // code that may throw an exception
 *      } catch (Exception $e) {
 *          // handle exception
 *      }
 *
 * 5. Exception Methods:
 *    - getMessage(): Returns the exception message.
 *    - getCode(): Returns the exception code.
 *    - getFile(): Returns the filename where the exception was created.
 *    - getLine(): Returns the line number where the exception was created.
 *    - getTrace(): Returns the stack trace as an array.
 *    - getTraceAsString(): Returns the stack trace as a string.
 *
 * 6. Custom Exceptions:
 *    - You can create your own exception classes by extending Exception.
 *    - Example:
 *      class MyException extends Exception {}
 *
 * 7. Multiple Catch Blocks:
 *    - You can catch different exception types separately.
 *      try {
 *          // code
 *      } catch (TypeAException $e) {
 *          // handle TypeAException
 *      } catch (Exception $e) {
 *          // handle other exceptions
 *      }
 *
 * 8. Finally Block:
 *    - The finally block executes regardless of whether an exception was thrown or not.
 *    - Syntax:
 *      try {
 *          // code
 *      } catch (Exception $e) {
 *          // handle exception
 *      } finally {
 *          // always executed
 *      }
 *
 * 9. Best Practices:
 *    - Use exceptions for exceptional conditions, not for regular control flow.
 *    - Always catch exceptions at the appropriate level.
 *    - Provide meaningful messages and codes in your exceptions.
 *    - Clean up resources in the finally block if needed.
 *
 * 10. Example:
 */

class DivisionByZeroException extends Exception {}

function divide($a, $b) {
    if ($b == 0) {
        throw new DivisionByZeroException("Cannot divide by zero.");
    }
    return $a / $b;
}

try {
    echo divide(10, 0);
} catch (DivisionByZeroException $e) {
    echo "Custom Exception: " . $e->getMessage();
} catch (Exception $e) {
    echo "General Exception: " . $e->getMessage();
} finally {
    echo "\nExecution completed.";
}
?>