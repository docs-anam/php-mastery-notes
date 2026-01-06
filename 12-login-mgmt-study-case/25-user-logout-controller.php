<?php
/**
 * User Logout Controller
 * 
 * 1. Create logout method in UserController (app/Controller/UserController.php)
 * <?php
 * 
 * ...
 *     public function logout()
 *     {
 *         $this->sessionService->destroy();
 *         View::redirect('/');
 *     }
 * ...
 * 
 * 2. Add route for logout in public/index.php (public/index.php)
 * <?php
 * ...
 * // User Controller
 * Router::add('GET', '/users/logout', UserController::class, 'logout', []);
 * ...
 * ?>
 * 
 * 3. Now, when a user accesses the /users/logout URL, their session will be destroyed, and they will be redirected to the home page.
 * This ensures that the user is logged out of the application.
 */