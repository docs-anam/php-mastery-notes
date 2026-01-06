use Mukhoiran\MVCProject\App\Router;

<?php
/**
 * Summary: Route in PHP MVC
 *
 * In PHP MVC (Model-View-Controller) architecture, a Route is a mechanism that maps HTTP requests (URLs) to specific controller actions. It acts as a bridge between the user's request and the application's response logic.
 *
 * Detailed Explanation:
 * 1. Purpose:
 *    - Routes define how URLs are interpreted by the application.
 *    - They determine which controller and method should handle a given request.
 *
 * 2. How Routing Works:
 *    - When a user accesses a URL, the routing system parses the URL.
 *    - It matches the URL pattern to a predefined route.
 *    - The route specifies the controller and action (method) to execute.
 *
 * 3. Example Route Definition:
 *    - In many PHP frameworks (like Laravel, Symfony, or custom MVC), routes are defined in a configuration file or using code.
 *    - Example:
 *      $routes = [
 *          '/users' => 'UserController@index',
 *          '/users/create' => 'UserController@create',
 *          '/posts/{id}' => 'PostController@show',
 *      ];
 *
 * 4. Dynamic Parameters:
 *    - Routes can include dynamic segments (e.g., /posts/{id}) to capture variables from the URL.
 *    - The routing system extracts these parameters and passes them to the controller.
 *
 * 5. Benefits:
 *    - Clean URLs: Routes enable user-friendly and SEO-friendly URLs.
 *    - Separation of Concerns: Routing keeps URL handling separate from business logic.
 *    - Flexibility: Easily add, modify, or remove routes without changing core logic.
 *
 * 6. Implementation:
 *    - Custom MVC frameworks often implement a Router class.
 *    - The Router matches incoming requests to routes and invokes the appropriate controller.
 *
 * 7. Example Router Class (Simplified):
 *      class Router {
 *          protected $routes = [];
 *
 *          public function add($pattern, $callback) {
 *              $this->routes[$pattern] = $callback;
 *          }
 *
 *          public function dispatch($uri) {
 *              foreach ($this->routes as $pattern => $callback) {
 *                  if ($pattern === $uri) {
 *                      list($controller, $action) = explode('@', $callback);
 *                      (new $controller)->$action();
 *                      return;
 *                  }
 *              }
 *              // Handle 404 Not Found
 *          }
 *      }
 *
 * In summary, Route in PHP MVC is a core concept that enables mapping URLs to controllers, supporting clean architecture and maintainable code.
 */

/**
 * ########### Implementation the routing system in PHP MVC continuing from the previous explanation and project.
 * 
 * 1. Create a Router class to handle routing logic (app/App/Router.php)
 *
 * namespace Mukhoiran\MVCProject\App;
 *
 * class Router
 * {
 *     private static array $routes = [];
 *     public static function add(string $method, string $path, string $controller, string $function)
 *     {
 *         self::$routes[] = [
 *             'method' => $method,
 *             'path' => $path,
 *             'controller' => $controller,
 *             'function' => $function
 *         ];
 *     }
 *     public static function run(): void
 *     {
 *         $path = '/';
 *         if (isset($_SERVER['PATH_INFO'])) {
 *             $path = $_SERVER['PATH_INFO'];
 *         }
 *         $method = $_SERVER['REQUEST_METHOD'];
 *
 *         foreach (self::$routes as $route) {
 *             if ($route['method'] === $method && $route['path'] === $path) {
 *                 echo "Controller: {$route['controller']} , Function: {$route['function']} \n";
 *                 return;
 *             }
 *         }
 *         http_response_code(404);
 *         echo "Controller Not Found\n";
 *     }
 * }

 * 2. Update public/index.php to use the Router
 *
 * require_once __DIR__ . '/../vendor/autoload.php';
 * use Mukhoiran\MVCProject\App\Router;
 * use Mukhoiran\MVCProject\Controller\HomeController;
 *
 * Router::add('GET', '/', HomeController::class, 'index');
 * Router::add('GET', '/login', LoginController::class, 'login');
 * Router::add('GET', '/register', RegisterController::class, 'register');
 *
 * Router::run();

 * 3. Now, when you access different URLs, the Router will determine which controller and function to call based on the defined routes.
 *
 * Example usage:
 * - Accessing http://localhost/index.php/ will output: Controller: HomeController , Function: index
 * - Accessing http://localhost/index.php/login will output: Controller: LoginController , Function: login
 * - Accessing http://localhost/index.php/register will output: Controller: RegisterController , Function: register
 * - Accessing an undefined route like http://localhost/index.php/unknown will output: Controller Not Found
 *
 * This is a basic implementation of routing in a PHP MVC application.
 * In a real-world scenario, you would replace the echo statements with actual controller instantiation and method calls.
 */