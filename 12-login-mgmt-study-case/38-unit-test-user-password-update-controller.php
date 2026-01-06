<?php
/**
 * 
 * Unit Test for User Password Update Controller
 * 
 * 1. Update UserControllerTest to test updatePassword and postUpdatePassword methods (tests/Controller/UserControllerTest.php)
 * <?php
 * ...
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
 * 
 *     public function testUpdatePasswordSuccess()
 *     {
 *         // Siapkan data user awal
 *         $user = new User();
 *         $user->username = "Anam";
 *         $user->password = password_hash("OldPassword", PASSWORD_BCRYPT);
 *         $user->email = "anam@example.com";
 *         $this->userRepository->save($user);
 *
 *         $request = new UserPasswordUpdateRequest();
 *         $request->username = "Anam";
 *         $request->oldPassword = "OldPassword";
 *         $request->newPassword = "NewPassword";
 *
 *         $response = $this->userService->updatePassword($request);
 *
 *         self::assertTrue(password_verify("NewPassword", $response->user->password));
 *     }
 *
 *     public function testUpdatePasswordFailedValidation()
 *     {
 *         $this->expectException(ValidationException::class);
 *         $request = new UserPasswordUpdateRequest();
 *         $request->username = "Anam";
 *         $request->oldPassword = "";
 *         $request->newPassword = "NewPassword";
 *
 *         $this->userService->updatePassword($request);
 *     }
 *
 *     public function testUpdatePasswordUserNotFound()
 *     {
 *         $this->expectException(ValidationException::class);
 *
 *         $request = new UserPasswordUpdateRequest();
 *         $request->username = "NonExistentUser";
 *         $request->oldPassword = "OldPassword";
 *         $request->newPassword = "NewPassword";
 *
 *         $this->userService->updatePassword($request);
 *     }
 *
 *     public function testUpdatePasswordWrongOldPassword()
 *     {
 *         // Siapkan data user awal
 *         $user = new User();
 *         $user->username = "Anam";
 *         $user->password = password_hash("OldPassword", PASSWORD_BCRYPT);
 *         $user->email = "anam@example.com";
 *         $this->userRepository->save($user);
 *         $this->expectException(ValidationException::class);
 *         $request = new UserPasswordUpdateRequest();
 *         $request->username = "Anam";
 *         $request->oldPassword = "WrongOldPassword";
 *         $request->newPassword = "NewPassword";
 *         $this->userService->updatePassword($request);
 *     }
 * ...
 * ?>
 * 
 * 2. Run the test to ensure the updatePassword and postUpdatePassword methods work correctly
 * vendor/bin/phpunit tests/Controller/UserControllerTest.php
 * 
 */