<?php
/**
 * 
 * User Password Update Controller
 * 
 * 1. Create updatePassword and postUpdatePassword methods in UserController (app/Controller/UserController.php)
 * 
 * <?php
 * ...
 *     public function updatePassword()
 *     {
 *         $user = $this->sessionService->current();
 * 
 *         View::render('User/password', [
 *             'title' => 'Update User Password',
 *             'user' => $user
 *         ]);
 *     }
 * 
 *     public function postUpdatePassword()
 *     {
 *         $user = $this->sessionService->current();
 * 
 *         $request = new UserPasswordUpdateRequest();
 *         $request->username = $user->username;
 *         $request->oldPassword = $_POST['oldPassword'];
 *         $request->newPassword = $_POST['newPassword'];
 * 
 *         try {
 *             $this->userService->updatePassword($request);
 *             View::redirect('/');
 *         } catch (ValidationException $exception) {
 *             View::render('User/password', [
 *                 'title' => 'Update User Password',
 *                 'user' => $user,
 *                 'error' => $exception->getMessage()
 *             ]);
 *         }
 *     }
 * }
 * ?>
 * 
 * 2. Create password view (app/View/User/password.php)
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
 *             <h1 class="display-4 fw-bold lh-1 mb-3">Password</h1>
 *         </div>
 *         <div class="col-md-10 mx-auto col-lg-5">
 *             <form class="p-4 p-md-5 border rounded-3 bg-light" method="post" action="/users/password">
 *                 <div class="form-floating mb-3">
 *                     <input type="text" class="form-control" id="username" placeholder="username" value="<?= $model['user']->username ?? '' ?>" disabled>
 *                     <label for="username">Username</label>
 *                 </div>
 *                 <div class="form-floating mb-3">
 *                     <input name="oldPassword" type="password" class="form-control" id="oldPassword" placeholder="old password">
 *                     <label for="oldPassword">Old Password</label>
 *                 </div>
 *                 <div class="form-floating mb-3">
 *                     <input name="newPassword" type="password" class="form-control" id="newPassword" placeholder="password">
 *                     <label for="newPassword">New Password</label>
 *                 </div>
 *                 <button class="w-100 btn btn-lg btn-primary" type="submit">Change Password</button>
 *             </form>
 *         </div>
 *     </div>
 * </div>
 * 
 * 3. Add routes for updatePassword and postUpdatePassword in public/index.php (public/index.php)
 * 
 * <?php
 * ...
 * Router::add('GET', '/users/password', UserController::class, 'updatePassword', [MustLoginMiddleware::class]);
 * Router::add('POST', '/users/password', UserController::class, 'postUpdatePassword', [MustLoginMiddleware::class]);
 * ...
 * ?>
 */