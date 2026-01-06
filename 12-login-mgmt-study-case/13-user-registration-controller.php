<?php

/**
 * User Registration Controller
 *
 * This controller handles user registration requests.
 * 
 * 1. Create a new file controller/UserRegistrationController.php to handle user registration.
 * 
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
 * 
 * 
 * class UserController
 * {
 *     private UserService $userService;
 * 
 *     public function __construct()
 *     {
 *         $connection = Database::getConnection();
 *         $userRepository = new UserRepository($connection);
 *         $this->userService = new UserService($userRepository);
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
 * }
 * 
 * 2. Update routing configuration to map (public/index.php) registration URLs to the UserController methods.
 * 
 * <?php
 * 
 * require_once __DIR__ . '/../vendor/autoload.php';
 * 
 * use Mukhoiran\LoginManagement\App\Router;
 * use Mukhoiran\LoginManagement\Controller\HomeController;
 * use Mukhoiran\LoginManagement\Controller\UserController;
 * use Mukhoiran\LoginManagement\Config\Database;
 * 
 * Database::getConnection("prod");
 * 
 * // Home Controller
 * Router::add('GET', '/', HomeController::class, 'index', []);
 * 
 * // User Controller
 * Router::add('GET', '/users/register', UserController::class, 'register', []);
 * Router::add('POST', '/users/register', UserController::class, 'postRegister', []);
 * Router::add('GET', '/users/login', UserController::class, 'login', []);
 * Router::run();
 * 
 * 3. Create View templates for user registration and login (View/User/register.php and View/User/login.php).
 * 
 * View/User/login.php
 * <div class="container col-xl-10 col-xxl-8 px-4 py-5">
 * 
 *     <?php if(isset($model['error'])) { ?>
 *        <div class="row">
 *        <div class="row">
 *            <div class="alert alert-danger" role="alert">
 *                <?= $model['error'] ?>
 *            </div>
 *        </div>
 *    <?php } ?>
 *    
 *    <div class="row align-items-center g-lg-5 py-5">
 *        <div class="col-lg-7 text-center text-lg-start">
 *            <h1 class="display-4 fw-bold lh-1 mb-3">Login</h1>
 *        </div>
 *        <div class="col-md-10 mx-auto col-lg-5">
 *            <form class="p-4 p-md-5 border rounded-3 bg-light" method="post" action="/users/login">
 *                <div class="form-floating mb-3">
 *                    <input name="username" type="text" class="form-control" id="username" placeholder="username" value="<?= $_POST['username'] ?? '' ?>">
 *                    <label for="username">Username</label>
 *                </div>
 *                <div class="form-floating mb-3">
 *                    <input name="password" type="password" class="form-control" id="password" placeholder="password">
 *                    <label for="password">Password</label>
 *                </div>
 *                <button class="w-100 btn btn-lg btn-primary" type="submit">Sign On</button>
 *            </form>
 *        </div>
 *    </div>
 * </div>
 * 
 * View/User/register.php
 * <div class="container col-xl-10 col-xxl-8 px-4 py-5">
 *    <?php if(isset($model['error'])) { ?>
 *        <div class="row">
 *            <div class="alert alert-danger" role="alert">
 *                <?= $model['error'] ?>
 *            </div>
 *        </div>
 *    <?php } ?>
 *    <div class="row align-items-center g-lg-5 py-5">
 *        <div class="col-lg-7 text-center text-lg-start">
 *            <h1 class="display-4 fw-bold lh-1 mb-3">Register</h1>
 *        </div>
 *        <div class="col-md-10 mx-auto col-lg-5">
 *            <form class="p-4 p-md-5 border rounded-3 bg-light" method="post" action="/users/register">
 *                <div class="form-floating mb-3">
 *                    <input name="username" type="text" class="form-control" id="username" placeholder="id" value="<?= $_POST['username'] ?? '' ?>">
 *                    <label for="username">Username</label>
 *                </div>
 *                <div class="form-floating mb-3">
 *                    <input name="email" type="text" class="form-control" id="email" placeholder="email" value="<?= $_POST['email'] ?? '' ?>">
 *                    <label for="email">Email</label>
 *                </div>
 *                <div class="form-floating mb-3">
 *                    <input name="password" type="password" class="form-control" id="password" placeholder="password">
 *                   <label for="password">Password</label>
 *                </div>
 *                <button class="w-100 btn btn-lg btn-primary" type="submit">Register</button>
 *            </form>
 *        </div>
 *    </div>
 * </div>
 */