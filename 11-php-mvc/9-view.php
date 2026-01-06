<?php
/*
 * PHP MVC View Documentation
 *
 * Summary:
 *   In the PHP MVC (Model-View-Controller) architecture, the "View" is responsible for presenting data to the user.
 *   It separates the user interface from business logic, making code easier to maintain and scale.
 *
 * Purpose:
 *   - The View displays data provided by the Controller.
 *   - Contains HTML, CSS, and minimal PHP for rendering dynamic content.
 *   - Views should avoid direct database access or business logic.
 *
 * Structure:
 *   - Typically stored in a "views" directory.
 *   - Each View corresponds to a specific page or component (e.g., home.php, user-list.php).
 *   - Can use templates and partials for reusable UI elements.
 *
 * Data Flow:
 *   - The Controller gathers data from the Model.
 *   - The Controller passes this data to the View, often as associative arrays or objects.
 *   - The View uses PHP to embed dynamic data into HTML.
 *
 * Example Usage:
 *   // Controller
 *   $users = $userModel->getAllUsers();
 *   include 'views/user-list.php';
 *
 *   // View (views/user-list.php)
 *   <ul>
 *     <?php foreach ($users as $user): ?>
 *        <li><?= htmlspecialchars($user['name']) ?></li>
 *     <?php endforeach; ?>
 *   </ul>
 *
 * Best Practices:
 *   - Keep logic in Views minimal (only for presentation).
 *   - Escape output to prevent XSS vulnerabilities.
 *   - Use template engines (like Twig or Blade) for cleaner syntax and separation.
 *
 * Summary:
 *   The View in PHP MVC is the presentation layer, focusing on rendering data and UI,
 *   while keeping logic and data handling in Controllers and Models.
 */

/** ########### Implementation the view in PHP MVC continuing from the previous explanation and project.
 * 1. Create a View folder and file to handle the presentation logic (app/views/home/index.php
 *   <html>
 *  <head>
 *   <title><?= htmlspecialchars($model['title']) ?></title>
 * </head>
 * <body>
 *  <h1><?= htmlspecialchars($model['title']) ?></h1>
 * <p><?= htmlspecialchars($model['content']) ?></p>
 * </body>
 * </html>
 * 2. Modify the HomeController to pass data to the view (app/App/Controller/HomeController.php)
 * ...
 *  public function index(): void
 *  {
 *      $model = [
 *          'title' => 'Home Page',
 *          'content' => 'Welcome to the Home Page!'
 *      ];
 *      include __DIR__ . '/../../views/home/index.php';
 *  }
 * ...
 * 3. (Optional) Create a View class to handle rendering (app/App/View.php)
 * namespace Mukhoiran\MVCProject\App;
 * class View
 * {
 *     public static function render(string $view, array $model = []): void
 *     {
 *         $viewPath = __DIR__ . "/../views/{$view}.php";
 *         if (file_exists($viewPath)) {
 *             extract($model);
 *             include $viewPath;
 *         } else {
 *             http_response_code(404);
 *             include __DIR__ . '/../views/404.php';
 *         }
 *     }
 * }
 * 4. Update the HomeController to use the View class (app/App/Controller/HomeController.php)
 * ...
 *  use Mukhoiran\MVCProject\App\View;
 * ...
 *  public function index(): void
 *  {
 *      $model = [
 *          'title' => 'Home Page',
 *          'content' => 'Welcome to the Home Page!'
 *      ];
 *      View::render('home/index', $model);
 *  }
 * ...
 * 5. Create a 404 view for handling not found errors (app/views/404.php)
 * <html>
 * <head>
 *   <title>404 Not Found</title>
 * </head>
 * <body>
 *   <h1>404 Not Found</h1>
 *   <p>The page you are looking for does not exist.</p>
 * </body>
 * </html>
 * This implementation demonstrates how to handle views in a custom PHP MVC framework.
 * By separating the presentation logic into views and using a View class for rendering,
 * we maintain a clean separation of concerns, making the application easier to manage and scale.
 */ 