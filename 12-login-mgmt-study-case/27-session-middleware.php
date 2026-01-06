<?php
/**
 * 
 * Session Middleware
 * 
 * 1. Create MustLoginMiddleware (app/Middleware/MustLoginMiddleware.php)
 * <?php
 *
 * namespace Mukhoiran\LoginManagement\Middleware;
 * 
 * use Mukhoiran\LoginManagement\App\View;
 * use Mukhoiran\LoginManagement\Config\Database;
 * use Mukhoiran\LoginManagement\Repository\SessionRepository;
 * use Mukhoiran\LoginManagement\Repository\UserRepository;
 * use Mukhoiran\LoginManagement\Service\SessionService;
 * 
 * class MustLoginMiddleware implements Middleware
 * {
 *     private SessionService $sessionService;
 * 
 *     public function __construct()
 *     {
 *         $sessionRepository = new SessionRepository(Database::getConnection());
 *         $userRepository = new UserRepository(Database::getConnection());
 *         $this->sessionService = new SessionService($sessionRepository, $userRepository);
 *     }
 * 
 *     public function before(): void
 *     {
 *         $session = $this->sessionService->current();
 *         if ($session == null) {
 *             View::redirect('/users/login');
 *         }
 *     }
 * }
 * ?>
 * 
 * 2. Create MustNotLoginMiddleware (app/Middleware/MustNotLoginMiddleware.php
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Middleware;
 * 
 * use Mukhoiran\LoginManagement\App\View;
 * use Mukhoiran\LoginManagement\Config\Database;
 * use Mukhoiran\LoginManagement\Repository\SessionRepository;
 * use Mukhoiran\LoginManagement\Repository\UserRepository;
 * use Mukhoiran\LoginManagement\Service\SessionService;
 * 
 * class MustNotLoginMiddleware implements Middleware
 * {
 *     private SessionService $sessionService;
 * 
 *     public function __construct()
 *     {
 *         $sessionRepository = new SessionRepository(Database::getConnection());
 *         $userRepository = new UserRepository(Database::getConnection());
 *         $this->sessionService = new SessionService($sessionRepository, $userRepository);
 *     }
 * 
 *     public function before(): void
 *     {
 *         $session = $this->sessionService->current();
 *         if ($session != null) {
 *             View::redirect('/');
 *         }
 *     }
 * }
 * ?>
 * 
 * 3. Update index.php to use the middleware (public/index.php)
 * <?php
 * 
 * 
 * require_once __DIR__ . '/../vendor/autoload.php';
 * 
 * use Mukhoiran\LoginManagement\App\Router;
 * use Mukhoiran\LoginManagement\Controller\HomeController;
 * use Mukhoiran\LoginManagement\Controller\UserController;
 * use Mukhoiran\LoginManagement\Config\Database;
 * use Mukhoiran\LoginManagement\Middleware\MustLoginMiddleware;
 * use Mukhoiran\LoginManagement\Middleware\MustNotLoginMiddleware;
 * 
 * Database::getConnection("prod");
 * 
 * // Home Controller
 * Router::add('GET', '/', HomeController::class, 'index', []);
 * 
 * // User Controller
 * Router::add('GET', '/users/register', UserController::class, 'register', [MustNotLoginMiddleware::class]);
 * Router::add('POST', '/users/register', UserController::class, 'postRegister', [MustNotLoginMiddleware::class]);
 * Router::add('GET', '/users/login', UserController::class, 'login', [MustNotLoginMiddleware::class]);
 * Router::add('POST', '/users/login', UserController::class, 'postLogin', [MustNotLoginMiddleware::class]);
 * Router::add('GET', '/users/logout', UserController::class, 'logout', [MustLoginMiddleware::class]);
 * Router::run();
 * ?>
 */