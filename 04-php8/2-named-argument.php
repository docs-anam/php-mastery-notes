<?php
/**
 * Named Arguments in PHP 8.0 - Detailed Summary
 *
 * PHP 8.0 introduced named arguments, allowing you to pass arguments to a function
 * based on the parameter name, rather than just their position.
 *
 * Key Features:
 * 1. Arguments can be passed in any order by specifying the parameter name.
 * 2. Improves code readability, especially for functions with many optional parameters.
 * 3. Works with both user-defined and built-in functions.
 * 4. Can be mixed with positional arguments, but named arguments must come after positional ones.
 * 5. Skipping optional parameters is easier, as you can specify only the ones you want.
 *
 * Syntax Example:
 */

function createUser($name, $email, $role = 'user', $active = true) {
    echo "Name: $name, Email: $email, Role: $role, Active: " . ($active ? 'Yes' : 'No') . PHP_EOL;
}

// Using positional arguments (PHP < 8.0)
createUser('Alice', 'alice@example.com', 'admin', false);

// Using named arguments (PHP 8.0+)
createUser(
    name: 'Bob',
    email: 'bob@example.com',
    active: false, // You can skip 'role' and use default
);

// Mixing positional and named arguments
createUser('Charlie', email: 'charlie@example.com');

// Named arguments can be in any order
createUser(
    email: 'dave@example.com',
    name: 'Dave',
    role: 'editor'
);

/**
 * Restrictions:
 * - Named arguments cannot be used with parameters declared as ...$args (variadic).
 * - Named arguments must follow positional arguments in the call.
 * - Parameter names are part of the function signature; renaming parameters is a breaking change.
 *
 * Benefits:
 * - Increases clarity and maintainability.
 * - Reduces errors when using functions with many optional parameters.
 * - Makes code self-documenting.
 */
?>