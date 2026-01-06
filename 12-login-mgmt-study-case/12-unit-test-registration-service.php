<?php
/**
 * User Registration Service Unit Test
 *
 * 1. Create a unit test for the UserService (tests/Service/UserServiceTest.php) to ensure user registration functionality works as expected.
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Service;
 * 
 * use PHPUnit\Framework\TestCase;
 * use Mukhoiran\LoginManagement\Config\Database;
 * use Mukhoiran\LoginManagement\Domain\User;
 * use Mukhoiran\LoginManagement\Exception\ValidationException;
 * use Mukhoiran\LoginManagement\Model\UserRegisterRequest;
 * use Mukhoiran\LoginManagement\Repository\UserRepository;
 * 
 * class UserServiceTest extends TestCase
 * {
 *     private UserService $userService;
 *     private UserRepository $userRepository;
 * 
 *     protected function setUp():void
 *     {
 *         $connection = Database::getConnection();
 *         $this->userRepository = new UserRepository($connection);
 *         $this->userService = new UserService($this->userRepository);
 *
 *         $this->userRepository->deleteAll();
 *     }
 * 
 *     public function testRegisterSuccess()
 *     {
 *         $request = new UserRegisterRequest();
 *         $request->username = "Anam";
 *         $request->password = "Confidential";
 *         $request->email = "anam@example.com";
 * 
 *         $response = $this->userService->register($request);
 * 
 *         self::assertEquals($request->username, $response->user->username);
 *         self::assertNotEquals($request->password, $response->user->password);
 *         self::assertTrue(password_verify($request->password, $response->user->password));
 *         self::assertEquals($request->email, $response->user->email);
 *     }
 * 
 *     public function testRegisterFailed()
 *     {
 *         $this->expectException(ValidationException::class);
 * 
 *         $request = new UserRegisterRequest();
 *         $request->username = "";
 *         $request->password = "";
 *         $request->email = "";
 *
 *         $this->userService->register($request);
 *     }
 * 
 *     public function testRegisterDuplicate()
 *     {
 *         $user = new User();
 *         $user->username = "Anam";
 *         $user->password = "Confidential";
 *         $user->email = "anam@example.com";
 * 
 *         $this->userRepository->save($user);
 *
 *         $this->expectException(ValidationException::class);
 *
 *         $request = new UserRegisterRequest();
 *         $request->username = "Anam";
 *         $request->password = "Confidential";
 *         $request->email = "anam@example.com";
 * 
 *         $this->userService->register($request);
 *     }
 * 
 * }
 * 
 * 2. Run the tests using PHPUnit.
 *   vendor/bin/phpunit tests/Service/UserServiceTest.php
 * 
 * 3. Verify that the tests pass, confirming the user registration service works correctly.
 * This ensures that the user registration functionality is reliable and ready for use in the login management system
 * */