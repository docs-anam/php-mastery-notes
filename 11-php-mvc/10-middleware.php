<?php
/*
 * Middleware in PHP MVC Frameworks - Detailed Summary
 *
 * 1. Definition:
 *    - Middleware is a software layer that sits between the HTTP request and the application's response.
 *    - It processes requests before they reach controllers and can also process responses before they are sent to the client.
 *
 * 2. Purpose:
 *    - Middleware is used to handle cross-cutting concerns such as authentication, authorization, logging, input validation, CORS, and more.
 *    - It helps keep controllers clean by separating these concerns from business logic.
 *
 * 3. How Middleware Works:
 *    - When a request is received, it passes through a stack of middleware components.
 *    - Each middleware can inspect, modify, or reject the request.
 *    - After processing, the request is passed to the next middleware or the controller.
 *    - The response can also be processed by middleware before being sent back.
 *
 * 4. Common Use Cases:
 *    - Authentication: Check if the user is logged in.
 *    - Authorization: Verify user permissions.
 *    - Logging: Record request details.
 *    - Input Validation: Ensure request data is valid.
 *    - CORS: Set headers for cross-origin requests.
 *    - Rate Limiting: Prevent abuse by limiting requests.
 *
 * 5. Implementation Patterns:
 *    - Middleware is often implemented as classes with a handle() or process() method.
 *    - Middleware can be global (applied to all requests) or route-specific.
 *    - In frameworks like Laravel, middleware is registered in a stack and executed in order.
 *
 * 6. Example (Simple Middleware Class):
 *    class AuthMiddleware {
 *         public function handle($request, $next) {
 *             if (!isset($_SESSION['user'])) {
 *                 header('Location: /login');
 *                 exit;
 *             }
 *             return $next($request);
 *         }
 *    }
 *
 * 7. Integration in MVC:
 *    - Middleware is typically invoked in the front controller or router before dispatching to controllers.
 *    - Some frameworks allow chaining multiple middleware for a single route.
 *
 * 8. Benefits:
 *    - Promotes separation of concerns.
 *    - Improves code reusability and maintainability.
 *    - Makes it easier to add or remove features without touching core logic.
 *
 * 9. Popular PHP Frameworks Supporting Middleware:
 *    - Laravel
 *    - Symfony (via Event Listeners)
 *    - Slim
 *    - Zend Expressive
 *
 * 10. Summary:
 *     - Middleware is a powerful concept in PHP MVC frameworks for handling request/response processing.
 *     - It enables modular, reusable, and maintainable code for common web application concerns.
 */

/** ########### Implementation the middleware in PHP MVC continuing from the previous explanation and project.
 * 1. Create Middleware folder and interface to define middleware contract (app/Middleware/Middleware.php)
 *
 * namespace Mukhoiran\MVCProject\Middleware;
 *
 * interface Middleware
 * {
 *    public function before(): void;
 * }
 *
 * 2. Create a sample middleware class to implement the Middleware interface (app/Middleware/AuthMiddleware.php)
 *
 * namespace Mukhoiran\MVCProject\Middleware;
 *
 * class AuthMiddleware implements Middleware
 * {
 *    public function before(): void
 *    {
 *        session_start();
 *        if (!isset($_SESSION['user'])) {
 *            header('Location: /login');
 *            exit();
 *        }
 *    }
 * }
 * 
 * 3. Update Router class to support middleware (app/App/Router.php)
 * ...
 *   public static function add(string $method, string $path, string $controller, string $function, array $middleware = []): void
 * ...
 *         self::$routes[] = [
 *             'method' => $method,
 *             'path' => $path,
 *             'controller' => $controller,
 *             'function' => $function,
 *             'middleware' => $middleware
 *         ];
 * ...
 *    public static function run(): void
 *    {
 *        $path = '/';
 *        if (isset($_SERVER['PATH_INFO'])) {
 *            $path = $_SERVER['PATH_INFO'];
 *        }
 *        $method = $_SERVER['REQUEST_METHOD'];
 *        foreach (self::$routes as $route) {
 *            $pattern = "#^" . $route['path'] . "$#";
 *            if ($route['method'] === $method && preg_match($pattern, $path, $variables)) {
 *                // echo "Controller: {$route['controller']} , Function: {$route['function']} \n";
 *                foreach ($route['middleware'] as $middleware) {
 *                    $middlewareInstance = new $middleware();
 *                    $middlewareInstance->before();
 *                }
 *                $controller = new $route['controller']();
 *                $function = $route['function'];
 *                // $controller->$function();
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
 * 4. Update public/index.php to register routes with middleware (public/index.php)
 * 
 * require_once __DIR__ . '/../vendor/autoload.php';
 * 
 * use Mukhoiran\MVCProject\App\Router;
 * use Mukhoiran\MVCProject\Controller\HomeController;
 * use Mukhoiran\MVCProject\Middleware\AuthMiddleware;
 * 
 * Router::add('GET', '/', HomeController::class, 'index');
 * Router::add('GET', '/hello', HomeController::class, 'hello', [AuthMiddleware::class]);
 * Router::add('GET', '/world', HomeController::class, 'world', [AuthMiddleware::class]);
 * Router::run();
 * 
 * 5. Testing the middleware by accessing protected routes without authentication
 *    - Accessing /hello or /world without a valid session should redirect to /login
 *    - After setting a session variable (e.g., $_SESSION['user'] = 'admin';), accessing /hello or /world should work normally.
 * 
 * 6. Summary
 *    - Middleware provides a clean way to handle cross-cutting concerns in an MVC framework.
 *    - It helps keep controllers focused on business logic while middleware manages tasks like authentication.
 *    - This implementation can be extended with more middleware for logging, input validation, etc.
 * Next Steps
 *    - Implement additional middleware as needed (e.g., LoggingMiddleware, CorsMiddleware).
 *    - Explore chaining multiple middleware for complex request processing.
 *    - Consider adding after() methods for response processing if needed.
 *    - Test thoroughly to ensure middleware behaves as expected in various scenarios.
 *    - Document middleware usage for future developers working on the project.
 * This implementation demonstrates how to integrate middleware into a custom PHP MVC framework,
 * enhancing its capabilities for handling common web application concerns.
 * Continuing from the previous explanation and project, we will now implement view handling in our PHP MVC framework.   
 * */   

