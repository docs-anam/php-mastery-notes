<?php
/**
 * Detailed Summary: Query Parameters in PHP
 *
 * What are Query Parameters?
 * --------------------------
 * Query parameters are key-value pairs appended to a URL after a question mark (?).
 * They are used to send data to the server, typically via HTTP GET requests.
 *
 * Example URL:
 *   http://example.com/page.php?name=John&age=25
 *   - 'name' and 'age' are query parameters.
 *   - Multiple parameters are separated by '&'.
 *
 * How PHP Handles Query Parameters:
 * ---------------------------------
 * PHP automatically parses query parameters and stores them in the global $_GET array.
 * Each key in $_GET corresponds to a parameter name, and its value is the parameter's value.
 *
 * Example:
 *   // URL: page.php?name=John&age=25
 *   $name = $_GET['name']; // "John"
 *   $age = $_GET['age'];   // "25"
 *
 * Important Notes:
 * ----------------
 * - All values in $_GET are strings. Type conversion may be needed.
 * - Always validate and sanitize input from $_GET to prevent security issues (e.g., XSS, SQL Injection).
 * - If a parameter is missing, accessing it directly may cause a notice. Use isset() to check.
 * - You can pass arrays as query parameters using square brackets:
 *     // URL: page.php?colors[]=red&colors[]=blue
 *     $colors = $_GET['colors']; // ['red', 'blue']
 *
 * Security Best Practices:
 * ------------------------
 * - Use htmlspecialchars() to prevent XSS when outputting user data.
 * - Use filter_var() or similar functions to validate and sanitize input.
 * - Never trust user input directly in SQL queries; use prepared statements.
 *
 * Executable Sample:
 * ------------------
 * This sample demonstrates how to access, validate, and display query parameters.
 * Try visiting: http://localhost/php-mastery-notes/5-web/10-query-parameter.php?name=John&age=25&colors[]=red&colors[]=blue
 */

function get_param($key, $default = null) {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

// Access and sanitize 'name'
$name = htmlspecialchars(get_param('name', 'Guest'));

// Access and validate 'age' as integer
$age = get_param('age');
if ($age !== null && filter_var($age, FILTER_VALIDATE_INT) !== false) {
    $age = (int)$age;
} else {
    $age = 'Unknown';
}

// Access 'colors' as array
$colors = get_param('colors', []);
if (!is_array($colors)) {
    $colors = [$colors];
}
$colors = array_map('htmlspecialchars', $colors);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Query Parameter Example</title>
</head>
<body>
    <h1>Query Parameter Example</h1>
    <p><strong>Name:</strong> <?php echo $name; ?></p>
    <p><strong>Age:</strong> <?php echo $age; ?></p>
    <p><strong>Colors:</strong>
        <?php if (!empty($colors)): ?>
            <ul>
                <?php foreach ($colors as $color): ?>
                    <li><?php echo $color; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            None
        <?php endif; ?>
    </p>
    <hr>
    <h2>Try it yourself</h2>
    <form method="get">
        <label>Name: <input type="text" name="name"></label><br>
        <label>Age: <input type="number" name="age"></label><br>
        <label>Colors: <input type="text" name="colors[]"></label>
        <input type="text" name="colors[]">
        <input type="text" name="colors[]"><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>