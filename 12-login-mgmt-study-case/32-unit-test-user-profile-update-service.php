<?php
/**
 * 
 * Unit Test for User Profile Update Service
 * 
 * 1. Update UserServiceTest to test updateProfile method (tests/Service/UserServiceTest.php)
 * 
 * <?php
 * ...
 * use Mukhoiran\LoginManagement\Model\UserRegisterRequest;
 * use Mukhoiran\LoginManagement\Repository\UserRepository;
 * use Mukhoiran\LoginManagement\Model\UserLoginRequest;
 * use Mukhoiran\LoginManagement\Model\UserProfileUpdateRequest;
 * use Mukhoiran\LoginManagement\Model\UserProfileUpdateResponse;
 * 
 * class UserServiceTest extends TestCase
 * {
 *     ...
 * 
 *     public function testUpdateProfileSuccess()
 *     {
 *         // Siapkan data user awal
 *         $user = new User();
 *         $user->username = "Anam";
 *         $user->password = password_hash("Confidential", PASSWORD_BCRYPT);
 *         $user->email = "anam@example.com";
 *         $this->userRepository->save($user);
 *
 *         $request = new UserProfileUpdateRequest();
 *         $request->username = "Anam";
 *         $request->email = "newemail@example.com";
 *
 *         $response = $this->userService->updateProfile($request);
 *
 *         self::assertEquals($request->email, $response->user->email);
 *     }
 *     
 *     public function testUpdateProfileFailed()
 *     {
 *         $this->expectException(ValidationException::class);
 * 
 *         $request = new UserProfileUpdateRequest();
 *         $request->username = "";
 *         $request->email = "invalidemail";
 *
 *         $this->userService->updateProfile($request);
 *     }
 * 
 *     public function testUpdateProfileUserNotFound()
 *     {
 *         $this->expectException(ValidationException::class);
 * 
 *         $request = new UserProfileUpdateRequest();
 *         $request->username = "NonExistentUser";
 *         $request->email = "nonexistent@example.com";
 *         $this->userService->updateProfile($request);
 *     }
 * }
 * ?>
 * 
 * 2. Run the test to ensure the updateProfile method works correctly
 * vendor/bin/phpunit tests/Service/UserServiceTest.php
 * 
 */