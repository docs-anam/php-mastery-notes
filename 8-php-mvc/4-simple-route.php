<?php
/**
 * Simple PHP MVC Routing Implementation
 *
 * This script demonstrates a basic approach to routing in a PHP MVC application.
 * It is intended to be used as the entry point (e.g., public/index.php) for your project.
 *
 * Steps to Implement Routing:
 *
 * 1. Project Structure:
 *    - Organize your files as follows:
 *      public/index.php         // Entry point for all requests
 *      app/View/                // Directory for view files (e.g., index.php, about.php)
 *      app/Controller/          // (Optional) Directory for controller files
 *      app/Model/               // (Optional) Directory for model files
 *
 * 2. Determine Requested Path:
 *    - Use the $_SERVER['PATH_INFO'] variable to get the requested route from the URL.
 *    - If no path is provided, default to '/index' to serve the homepage.
 *
 * 3. Map Path to View File:
 *    - Concatenate the requested path to the app/View directory.
 *    - Require the corresponding PHP file to render the view.
 *    - Example: '/about' maps to app/View/about.php
 *
 * 4. Create View Files:
 *    - Add PHP files in app/View/ for each route you want to support.
 *    - Each file should contain the HTML/PHP code for its respective page.
 *
 * 5. Error Handling (Recommended):
 *    - Before requiring the view file, check if it exists using file_exists().
 *    - If the file does not exist, display a custom 404 error page.
 *
 * 6. Extending Routing (Optional):
 *    - For more complex applications, use controllers to handle logic.
 *    - Parse the path to determine which controller and action to invoke.
 *    - Example: '/user/profile' could map to UserController::profile()
 *
 * Security Note:
 *    - Always validate and sanitize the requested path to prevent directory traversal or unauthorized file access.
 *
 * Example Usage:
 *    - Accessing http://localhost/index.php/about will render app/View/about.php
 *    - Accessing http://localhost/index.php (with no PATH_INFO) will render app/View/index.php
 *
 * This approach provides a simple foundation for routing in PHP MVC applications and can be extended for more advanced use cases.
 */

/**
 * Implementation:
 * This script acts as a simple router for a PHP MVC application.
 * It determines the requested path from the URL, constructs the corresponding view file path,
 * and includes the view file if it exists. If the view file does not exist, it returns a 404 error.
 *
 * Steps:
 * 1. Retrieve the requested path from the URL using PATH_INFO, defaulting to '/index' if not set.
 * 2. Build the full path to the view file under the app/View directory.
 * 3. Check if the view file exists:
 *    - If yes, include and render the view file.
 *    - If no, send a 404 HTTP response and display a "Not Found" message.
 */

$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/index';
$viewFile = __DIR__ . '/../app/View' . $path . '.php';

if (file_exists($viewFile)) {
    require $viewFile;
} else {
    http_response_code(404);
    echo "<h1>404 Not Found</h1><p>The requested page was not found.</p>";
}