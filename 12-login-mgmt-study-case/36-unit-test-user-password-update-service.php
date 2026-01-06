<?php
/**
 * 
 * Unit Test for User Password Update Service
 * 
 * 1. Update UserServiceTest to test updatePassword method (tests/Service/UserServiceTest.php)
 * 
 * <?php
 * ...
 * use Mukhoiran\LoginManagement\Model\UserPasswordUpdateRequest;
 * ...
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
 * 
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
 * 2. Run the test to ensure the updatePassword method works correctly
 * vendor/bin/phpunit tests/Service/UserServiceTest.php
 */ 