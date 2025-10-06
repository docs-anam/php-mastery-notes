<?php
/**
 * Path Variables in PHP MVC Frameworks
 *
 * In PHP MVC (Model-View-Controller) architectures, "path variables" refer to dynamic segments within the URL
 * that are mapped to controller actions. These variables enable the passing of data directly through the URL,
 * supporting RESTful routing and dynamic content rendering.
 *
 * Detailed Overview:
 *
 * 1. Purpose:
 *    - Path variables make URLs dynamic and meaningful (e.g., /user/42).
 *    - They allow controllers to receive parameters directly from the URL, facilitating resource identification and manipulation.
 *
 * 2. Mechanism:
 *    - The router component parses the incoming URL and extracts path variables using string manipulation or regular expressions.
 *    - Extracted variables are passed as arguments to the corresponding controller methods.
 *
 * 3. Example Structure:
 *    - URL: /product/view/15
 *      - "product": Controller name.
 *      - "view": Action or method within the controller.
 *      - "15": Path variable (e.g., product ID).
 *
 * 4. Implementation Approaches:
 *    - Custom PHP MVC: Use `$_GET`, `$_SERVER['REQUEST_URI']`, or regex to extract variables.
 *    - Modern Frameworks (Laravel, Symfony): Provide built-in routing and path variable extraction.
 *
 * 5. Routing Logic:
 *    - Parse the URL into segments.
 *    - Match segments to controller and action.
 *    - Extract additional segments as path variables and pass them to the controller.
 *
 * 6. Benefits:
 *    - Produces clean, SEO-friendly URLs.
 *    - Simplifies route management and scalability.
 *    - Decouples URL structure from internal application logic.
 *
 * 7. Security Considerations:
 *    - Always validate and sanitize path variables to prevent security vulnerabilities such as injection attacks.
 *
 * Path variables are fundamental for building flexible, maintainable, and secure web applications using PHP MVC patterns.
 *
 */

/**
 * ########### Implementation the path variable in PHP MVC continuing from the previous explanation and project.
 *
 * 1. Create new unit test for regext testing path variable (tests/RegexTest.php)
 *
 * namespace Mukhoiran\MVCProject\Tests;
 * 
 * use PHPUnit\Framework\TestCase;
 * 
 * class RegexTest extends TestCase
 * {
 *     public function testRegex(): void
 *     {
 *         $path = "/products/12345/categories/abcde";
 *         $pattern = "#^/products/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)$#";
 *
 *         $result = preg_match($pattern, $path, $variables);
 *         $this->assertEquals(1,$result);
 *         array_shift($variables);
 *         var_dump($variables);
 *     }
 * }
 *
 * 
 * 2. Testing regex from unit test (vendor/bin/phpunit tests/RegexTest.php)
 *
 * 3. Modify Router class to support path variable (app/App/Router.php)
 * 
 * ...
 *
 *    public static function run(): void
 *    {
 *        $path = '/';
 *        if (isset($_SERVER['PATH_INFO'])) {
 *            $path = $_SERVER['PATH_INFO'];
 *        }
 *        $method = $_SERVER['REQUEST_METHOD'];
 *
 *        foreach (self::$routes as $route) {
 *            $pattern = "#^" . $route['path'] . "$#";
 *            if ($route['method'] === $method && preg_match($pattern, $path, $variables)) {
 *                // echo "Controller: {$route['controller']} , Function: {$route['function']} \n";
 *
 *                $controller = new $route['controller']();
 *                $function = $route['function'];
 *                // $controller->$function();
 *
 *                array_shift($variables);
 *                call_user_func_array([$controller, $function], $variables);
 *                return;
 *            }
 *        }
 *        http_response_code(404);
 *        echo "Controller Not Found\n";
 *    }
 * ...
 *
 * 4. Create a new controller for handling product-related actions (app/Controller/ProductController.php)
 *
 * namespace Mukhoiran\MVCProject\Controller;
 *
 * class ProductController
 * {
 *     public function categories(string $productId, string $categoryId): void
 *     {
 *         echo "Product ID: $productId, Category ID: $categoryId";
 *     }
 * }
 *
 * 5. Update public/index.php to register routes with path variables (public/index.php)
 *
 * require_once __DIR__ . '/../vendor/autoload.php';
 *
 * use Mukhoiran\MVCProject\App\Router;
 * use Mukhoiran\MVCProject\Controller\ProductController;
 *
 * Router::add('GET', '/products/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)', ProductController::class, 'categories');
 * Router::add('GET', '/', HomeController::class, 'index');
 * Router::add('GET', '/login', LoginController::class, 'login');
 * Router::add('GET', '/register', RegisterController::class, 'register');
 *
 * Router::run();
 *
 * 6. Testing the path variable route
 *    - Start the PHP built-in server: php -S localhost:8000 -t public
 *    - Access the URL: http://localhost:8000/products/12345/categories/abcde
 *    - Expected Output: Product ID: 12345, Category ID: abcde
 * Summary:
 * This implementation demonstrates how to handle path variables in a custom PHP MVC framework.
 * By using regular expressions in the routing mechanism, we can extract dynamic segments from the URL
 * and pass them as parameters to controller methods, enabling more flexible and dynamic web applications.
 */