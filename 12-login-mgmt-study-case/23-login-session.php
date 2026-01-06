<?php
/**
 * Login Session
 * 
 * 1. Update UserController to use SessionService to create session after successful login (app/Controller/UserController.php)
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Controller;
 * 
 * use Mukhoiran\LoginManagement\Config\Database;
 * use Mukhoiran\LoginManagement\App\View;
 * use Mukhoiran\LoginManagement\Exception\ValidationException;
 * use Mukhoiran\LoginManagement\Model\UserRegisterRequest;
 * use Mukhoiran\LoginManagement\Repository\UserRepository;
 * use Mukhoiran\LoginManagement\Service\UserService;
 * use Mukhoiran\LoginManagement\Model\UserLoginRequest;
 * use Mukhoiran\LoginManagement\Service\SessionService;
 * use Mukhoiran\LoginManagement\Repository\SessionRepository;
 * 
 * class UserController
 * {
 *     private UserService $userService;
 *     private SessionService $sessionService;
 * 
 *     public function __construct()
 *     {
 *         $connection = Database::getConnection();
 *         $userRepository = new UserRepository($connection);
 *         $this->userService = new UserService($userRepository);
 *
 *         $sessionRepository = new SessionRepository($connection);
 *         $this->sessionService = new SessionService($sessionRepository, $userRepository);
 *     }
 * 
 *     public function register()
 *     {
 *         View::render('User/register', [
 *             'title' => 'Register new User'
 *         ]);
 *     }
 * 
 *     public function postRegister()
 *     {
 *         $request = new UserRegisterRequest();
 *         $request->username = $_POST['username'];
 *         $request->email = $_POST['email'];
 *         $request->password = $_POST['password'];
 *
 *         try {
 *             $this->userService->register($request);
 *             View::redirect('/users/login');
 *         } catch (ValidationException $exception) {
 *             View::render('User/register', [
 *                 'title' => 'Register new User',
 *                 'error' => $exception->getMessage()
 *             ]);
 *         }
 *     }
 *
 *     public function login()
 *     {
 *         View::render('User/login', [
 *             "title" => "Login user"
 *         ]);
 *     }
 * 
 *     public function postLogin()
 *     {
 *         $request = new UserLoginRequest();
 *         $request->username = $_POST['username'];
 *         $request->password = $_POST['password'];
 *
 *         try {
 *             $response = $this->userService->login($request);
 *             $this->sessionService->create($response->user->username);
 *             // Handle successful login, e.g., redirect or set session
 *             View::redirect('/');
 *         } catch (ValidationException $exception) {
 *             View::render('User/login', [
 *                 "title" => "Login user",
 *                 "error" => $exception->getMessage()
 *             ]);
 *         }
 *     }
 * }
 * ?>
 * 
 * 2. Update HomeController to check current user session and show dashboard if logged in (app/Controller/HomeController.php)
 * 
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Controller;
 * 
 * use Mukhoiran\LoginManagement\App\View;
 * use Mukhoiran\LoginManagement\Config\Database;
 * use Mukhoiran\LoginManagement\Repository\SessionRepository;
 * use Mukhoiran\LoginManagement\Repository\UserRepository;
 * use Mukhoiran\LoginManagement\Service\SessionService;
 * 
 * class HomeController
 * {
 *     private SessionService $sessionService;
 * 
 *     public function __construct()
 *     {
 *         $connection = Database::getConnection();
 *         $sessionRepository = new SessionRepository($connection);
 *         $userRepository = new UserRepository($connection);
 *         $this->sessionService = new SessionService($sessionRepository, $userRepository);
 *     }
 * 
 *     public function index(): void
 *     {
 *         $user = $this->sessionService->current();
 *         if($user != null) {
 *             $model = [
 *                 'title' => 'Login Management Home',
 *                 'user' => $user->username
 *             ];
 *             View::render('Home/dashboard', $model);
 *             return;
 *         }else {
 *             View::redirect('/users/login',[
 *                 'title' => 'Login Management Home'
 *             ]);
 *         }
 *     }
 * }
 * 
 * 3. Create dashboard page or any other page to redirect after successful login. (app/View/Home/dashboard.php)
 * 
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Controller;
 * 
 * use Mukhoiran\LoginManagement\App\View;
 * use Mukhoiran\LoginManagement\Config\Database;
 * use Mukhoiran\LoginManagement\Repository\SessionRepository;
 * use Mukhoiran\LoginManagement\Repository\UserRepository;
 * use Mukhoiran\LoginManagement\Service\SessionService;
 * 
 * class HomeController
 * {
 *     private SessionService $sessionService;
 * 
 *     public function __construct()
 *     {
 *         $connection = Database::getConnection();
 *         $sessionRepository = new SessionRepository($connection);
 *         $userRepository = new UserRepository($connection);
 *         $this->sessionService = new SessionService($sessionRepository, $userRepository);
 *     }
 *
 *     public function index(): void
 *     {
 *         $user = $this->sessionService->current();
 *         if($user != null) {
 *             $model = [
 *                 'title' => 'Login Management Home',
 *                 'user' => $user->username
 *             ];
 *             View::render('Home/dashboard', $model);
 *             return;
 *         }else {
 *             View::redirect('/users/login',[
 *                 'title' => 'Login Management Home'
 *             ]);
 *         }
 *     }
 * }
 */