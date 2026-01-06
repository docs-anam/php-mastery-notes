<?php
/**
 * Represents a boolean value in PHP.
 *
 * In PHP, the boolean data type can have only two possible values: true or false.
 * Booleans are commonly used for conditional testing and logical operations.
 * PHP automatically converts certain values to boolean when needed:
 *   - Values considered FALSE: false, 0, 0.0, "", "0", [], and null.
 *   - All other values are considered TRUE.
 *
 * @var bool $variableName Description of the boolean variable.
 */

// Example:
$isLoggedIn = true;

if ($isLoggedIn) {
    echo "User is logged in.";
} else {
    echo "User is not logged in.";
}