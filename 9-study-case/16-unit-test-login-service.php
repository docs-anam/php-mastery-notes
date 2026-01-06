<?php
/**
 * Unit Test for User Login Service
 * 
 * 1. Update the UserServiceTest to include tests for the login method (tests/Service/UserServiceTest.php)
 * 
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Service;
 * 
 * ...
 * use Mukhoiran\LoginManagement\Model\UserLoginRequest;
 * ...
 * 
 * class UserServiceTest extends TestCase
 * {
 *  ...
 * 
 *     public function testLoginNotFound()
 *    {
 *        $this->expectException(ValidationException::class);
 *
 *        $request = new UserLoginRequest();
 *        $request->username = "NotFound";
 *        $request->password = "NotFound";
 *
 *        $this->userService->login($request);
 *    }
 *
 *    public function testLoginWrongPassword()
 *    {
 *        $user = new User();
 *        $user->username = "Anam";
 *        $user->password = password_hash("Confidential", PASSWORD_BCRYPT);
 *        $user->email = "anam@example.com";
 *        $this->userRepository->save($user);
 *
 *        $this->expectException(ValidationException::class);
 *
 *        $request = new UserLoginRequest();
 *        $request->username = "Anam";
 *        $request->password = "WrongPassword";
 *
 *        $this->userService->login($request);
 *    }
 *
 *    public function testLoginSuccess()
 *    {
 *        $user = new User();
 *        $user->username = "Anam";
 *        $user->password = password_hash("Confidential", PASSWORD_BCRYPT);
 *        $user->email = "anam@example.com";
 *        $this->userRepository->save($user);
 *
 *        $request = new UserLoginRequest();
 *        $request->username = "Anam";
 *        $request->password = "Confidential";
 *        $response = $this->userService->login($request);
 *        self::assertEquals($user->username, $response->user->username);
 *        self::assertEquals($user->password, $response->user->password);
 *        self::assertEquals($user->email, $response->user->email);
 *    }
 * }
 * ?>
 * 
 * 2. Run the test using PHPUnit to ensure all test cases pass.
 * vendor/bin/phpunit tests/Service/UserServiceTest.php
 * 
 */ 