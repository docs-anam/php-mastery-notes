<?php
/**
 * Implementation of view template with dynamic title
 * 
 * 1. Update public/index.php to call HomeController for the root path and remove unused routes.
 * 
 * <?php
 *
 * require_once __DIR__ . '/../vendor/autoload.php';
 *
 * use Mukhoiran\LoginManagement\App\Router;
 * use Mukhoiran\LoginManagement\Controller\HomeController;
 *
 * // Home Controller
 * Router::add('GET', '/', HomeController::class, 'index', []);
 *
 * Router::run();
 * 
 * 2. Update HomeController to set a dynamic title in the model.
 *
 * <?php
 *
 * namespace Mukhoiran\LoginManagement\Controller;
 *
 * use Mukhoiran\LoginManagement\App\View;
 *
 * class HomeController
 * {
 *     public function index(): void
 *     {
 *         $model = [
 *             'title' => 'Login Management Home'
 *         ];
 *
 *         View::render('Home/index', $model);
 *     }
 * }
 *
 * ?>
 * 
 * 3. Update App/View to render templates with header and footer.
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\App;
 * 
 * class View
 * {
 *     public static function render(string $view, $model)
 *     {
 *         require __DIR__ . '/../View/header.php';
 *         require __DIR__ . '/../View/' . $view . '.php';
 *         require __DIR__ . '/../View/footer.php';
 *     }
 * 
 *     public static function redirect(string $url)
 *     {
 *         header("Location: $url");
 *         if (getenv("mode") != "test") {
 *             exit();
 *         }
 *     }
 * }
 * ?>
 * 
 * 4. Create View/header.php from template to use dynamic title from model.
 * 5. Create View/footer.php from template for consistent footer across pages.
 * 6. Create View/Home/index.php for the home page content.
 * 7. Test the implementation by accessing the root URL and verifying the dynamic title and content display correctly.
 * This enhances the user experience by providing a consistent layout and dynamic content rendering.
 * 
 * Note: Delete any unused files to keep the codebase clean.
 */