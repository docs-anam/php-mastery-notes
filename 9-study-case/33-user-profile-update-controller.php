<?php
/**
 * 
 * User Profile Update Controller Methods
 * 
 * 1. Add updateProfile and postUpdateProfile methods to UserController (app/Controller/UserController.php)
 * 
 * <?php
 * 
 * ...
 *     public function updateProfile()
 *     {
 *         $user = $this->sessionService->current();
 * 
 *         View::render('User/profile', [
 *             'title' => 'Update User Profile',
 *             'user' => $user
 *         ]);
 *     }
 *
 *     public function postUpdateProfile()
 *     {
 *         $user = $this->sessionService->current();
 *
 *         $request = new UserProfileUpdateRequest();
 *         $request->username = $user->username;
 *         $request->email = $_POST['email'];
 *
 *         try {
 *             $this->userService->updateProfile($request);
 *             View::redirect('/');
 *         } catch (ValidationException $exception) {
 *             View::render('User/profile', [
 *                 'title' => 'Update User Profile',
 *                 'user' => $user,
 *                 'error' => $exception->getMessage()
 *             ]);
 *         }
 *     }
 * ...
 * ?>
 * 
 * 2. Create profile view (app/View/User/profile.php)
 * 
 * <div class="container col-xl-10 col-xxl-8 px-4 py-5">
 *     <?php if(isset($model['error'])) { ?>
 *         <div class="row">
 *             <div class="alert alert-danger" role="alert">
 *                 <?= $model['error'] ?>
 *             </div>
 *         </div>
 *     <?php } ?>
 *     <div class="row align-items-center g-lg-5 py-5">
 *         <div class="col-lg-7 text-center text-lg-start">
 *             <h1 class="display-4 fw-bold lh-1 mb-3">Profile</h1>
 *         </div>
 *         <div class="col-md-10 mx-auto col-lg-5">
 *             <form class="p-4 p-md-5 border rounded-3 bg-light" method="post" action="/users/profile">
 *                 <div class="form-floating mb-3">
 *                     <input type="text" class="form-control" id="username" placeholder="username" value="<?= htmlspecialchars($model['user']->username) ?? '' ?>" disabled>
 *                     <label for="username">Username</label>
 *                 </div>
 *                 <div class="form-floating mb-3">
 *                     <input name="email" type="text" class="form-control" id="email" placeholder="email" value="<?= htmlspecialchars($model['user']->email) ?? '' ?>">
 *                     <label for="email">Email</label>
 *                 </div>
 *                 <button class="w-100 btn btn-lg btn-primary" type="submit">Update Profile</button>
 *             </form>
 *         </div>
 *     </div>
 * </div>
 * 
 * 4. Add routes to index.php (public/index.php)
 * 
 * <?php
 * ...
 * Router::add('GET', '/users/profile', UserController::class, 'updateProfile', [MustLoginMiddleware::class]);
 * Router::add('POST', '/users/profile', UserController::class, 'postUpdateProfile', [MustLoginMiddleware::class]);
 * ...
 * ?>
 */