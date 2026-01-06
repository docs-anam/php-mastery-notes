<?php
/**
 * User Login Controller
 * 
 * 1. Update UserController to include login method (app/Controller/UserController.php)
 * 
 * <?php
 * 
 * ...
 *    public function login()
 *    {
 *        View::render('User/login', [
 *            "title" => "Login user"
 *        ]);
 *    }
 *
 *    public function postLogin()
 *    {
 *        $request = new UserLoginRequest();
 *        $request->username = $_POST['username'];
 *        $request->password = $_POST['password'];
 *    
 *        try {
 *            $response = $this->userService->login($request);
 *            // Handle successful login, e.g., redirect or set session
 *            View::redirect('/');
 *        } catch (ValidationException $exception) {
 *            View::render('User/login', [
 *                "title" => "Login user",
 *                "error" => $exception->getMessage()
 *            ]);
 *        }
 *    }
 * ...
 * ?>
 * 
 * 2. Update routing to include POST /users/login (public/index.php) 
 * 
 * <?php
 * 
 * ...
 * Router::add('GET', '/users/login', UserController::class, 'login', []);
 * Router::add('POST', '/users/login', UserController::class, 'postLogin', []);
 * ...
 * ?>
 * 
 */