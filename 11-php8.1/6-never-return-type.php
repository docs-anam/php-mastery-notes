<?php

/**
 * Never Return Type in PHP 8.1
 * 
 * The 'never' return type was introduced in PHP 8.1 to indicate that a function
 * will never return to the caller. This means the function will either:
 * 1. Throw an exception
 * 2. Call exit() or die()
 * 3. Enter an infinite loop
 * 
 * Key Points:
 * - 'never' is a bottom type - it indicates the function terminates execution
 * - Functions with 'never' must not have a return statement (even without a value)
 * - Functions with 'never' must not have an implicit return
 * - Useful for helper functions that always throw exceptions or exit
 * - Improves static analysis and type safety
 * - Cannot be used in combination with other return types
 */

// Example 1: Function that always throws an exception
function redirect(string $url): never
{
    header("Location: $url");
    exit();
}

// Example 2: Error handling function
function handleError(string $message): never
{
    throw new Exception($message);
}

// Example 3: Termination function
function terminateScript(int $code = 0): never
{
    exit($code);
}

// Example 4: Invalid - will cause error (cannot return)
// function invalidNever(): never
// {
//     return; // Error: never-returning function must not return
// }

// Example 5: Using never in error handlers
function notFound(): never
{
    http_response_code(404);
    echo "Page not found";
    exit();
}

// Example 6: Practical use case
class Router
{
    public function abort(int $code = 404): never
    {
        http_response_code($code);
        echo "Error: $code";
        die();
    }
}

// Usage example
try {
    $age = -5;
    
    if ($age < 0) {
        handleError("Age cannot be negative");
    }
    
    echo "Age is valid: $age";
} catch (Exception $e) {
    echo "Caught exception: " . $e->getMessage();
}

echo "\n\n";

// Benefits:
// 1. Better IDE support and autocomplete
// 2. Improved static analysis
// 3. Clearer intent in code
// 4. Prevents accidental returns
// 5. Type system can make better assumptions

?>