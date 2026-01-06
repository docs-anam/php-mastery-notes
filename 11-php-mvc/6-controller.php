<?php
/**
 * Controller in PHP MVC - Detailed Summary
 *
 * Definition:
 * - In the MVC (Model-View-Controller) architecture, the Controller acts as an intermediary between the Model (data/business logic) and the View (presentation/UI).
 * - It handles user input, processes requests, interacts with the Model, and determines which View to render.
 *
 * Responsibilities:
 * - Receives HTTP requests (GET, POST, etc.) from the client.
 * - Parses and validates input data.
 * - Calls appropriate Model methods to fetch, update, or delete data.
 * - Decides which View to display based on the outcome of Model operations.
 * - Passes data from the Model to the View for rendering.
 *
 * Structure:
 * - Typically, each Controller is a PHP class, with methods (actions) corresponding to different application functionalities (e.g., show, edit, delete).
 * - Controllers are often grouped by resource (e.g., UserController, ProductController).
 *
 * Example Workflow:
 * - User submits a form to update profile.
 * - Controller receives the POST request, validates input.
 * - Controller calls Model to update user data.
 * - Controller redirects to a View showing success or error.
 *
 * Benefits:
 * - Separation of concerns: Keeps business logic, presentation, and request handling distinct.
 * - Easier maintenance and testing.
 * - Promotes code reusability and scalability.
 *
 * Example Controller Skeleton:
 *   class UserController {
 *        public function show($id) {
 *             $user = UserModel::find($id);
 *             include 'views/user/show.php';
 *        }
 *
 *        public function update($id) {
 *             // Validate input
 *             // Update user via Model
 *             // Redirect or render View
 *        }
 *   }
 *
 * Integration:
 * - Controllers are invoked by a front controller (e.g., index.php) based on routing rules.
 * - They communicate with Models and Views, but do not contain business logic or HTML markup directly.
 *
 * Summary:
 * Controllers are a core part of PHP MVC applications, managing the flow of data and user interaction, ensuring a clean separation between logic and presentation.
 */

/**
 * ########### Implementation the controller in PHP MVC continuing from the previous explanation and project.
 *
 * 1. Create a Controller folder and class to handle routing logic (app/App/Controller/HomeController.php)
 *
 * namespace Mukhoiran\MVCProject\Controller;
 *
 * class HomeController
 * {
 *     public function index(): void
 *     {
 *         echo "HomeController.index()";
 *     }
 *
 *     public function hello(): void
 *     {
 *         echo "HomeController.hello()";
 *     }
 *
 *    public function world(): void
 *    {
 *        echo "HomeController.world()";
 *    }
 *
 *    public function about(): void
 *    {
 *        echo "HomeController.about()";
 *    }
 * }
 *

 * 2. Update Router class to instantiate and call controller methods (app/App/Router.php)
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
 *            if ($route['method'] === $method && $route['path'] === $path) {
 *                // echo "Controller: {$route['controller']} , Function: {$route['function']} \n";
 *
 *                $function = $route['function'];
 *                $controller = new $route['controller']();
 *                $controller->$function();
 *
 *                return;
 *            }
 *        }
 *        http_response_code(404);
 *        echo "Controller Not Found\n";
 *    }
 * ...
 *
 *
 * 3. Update public/index.php to register routes with controllers (public/index.php)
 *
 * require_once __DIR__ . '/../vendor/autoload.php';
 *
 * use Mukhoiran\MVCProject\App\Router;
 * use Mukhoiran\MVCProject\Controller\HomeController;
 *
 * Router::add('GET', '/', HomeController::class, 'index');
 * Router::add('GET', '/login', LoginController::class, 'login');
 * Router::add('GET', '/register', RegisterController::class, 'register');
 *
 * Router::run();
 *
 * 4. Now, when you access different URLs, the Router will determine which controller and function to call based on the defined routes.
 *   For example:
 *  - Accessing '/' will invoke HomeController.index()
 * - Accessing '/login' will invoke LoginController.login()
 * - Accessing '/register' will invoke RegisterController.register()
 * This setup allows for a clean separation of routing logic and controller actions, adhering to the MVC architecture.
 */