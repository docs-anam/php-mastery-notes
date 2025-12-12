<?php
/**
 * User Registration Controller Test Case
 * 
 * This test case verifies the functionality of the UserController's
 * user registration methods, including validation, successful registration,
 * and handling of duplicate user entries.
 * 
 * 1. Create the test file in tests/Controller/UserControllerTest.php.
 * 
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Controller {
 *     
 *    use PHPUnit\Framework\TestCase;
 *    use Mukhoiran\LoginManagement\Config\Database;
 *    use Mukhoiran\LoginManagement\Repository\UserRepository;
 *    use Mukhoiran\LoginManagement\Domain\User;
 *
 *    class UserControllerTest extends TestCase
 *    {
 *        private UserController $userController;
 *        private UserRepository $userRepository;
 *
 *        protected function setUp(): void
 *        {
 *            $this->userController = new UserController();
 *
 *            $this->userRepository = new UserRepository(Database::getConnection());
 *            $this->userRepository->deleteAll();
 *
 *            putenv("mode=test");
 *        }
 *
 *        public function testRegister()
 *        {
 *            $this->userController->register();
 *
 *            $this->expectOutputRegex("[Register]");
 *            $this->expectOutputRegex("[Username]");
 *            $this->expectOutputRegex("[Password]");
 *            $this->expectOutputRegex("[Email]");
 *            $this->expectOutputRegex("[Register new User]");
 *        }
 *
 *        public function testPostRegisterSuccess()
 *        {
 *            $_POST['username'] = 'anam';
 *            $_POST['email'] = 'anam@example.com';
 *            $_POST['password'] = 'anam';
 *
 *            $this->userController->postRegister();
 *
 *            $this->expectOutputString("");
 *        }
 *
 *        public function testPostRegisterValidationError()
 *        {
 *            $_POST['username'] = '';
 *            $_POST['email'] = 'anam@example.com';
 *            $_POST['password'] = 'anam';
 *
 *            $this->userController->postRegister();
 *
 *            $this->expectOutputRegex("[Register]");
 *            $this->expectOutputRegex("[Username]");
 *            $this->expectOutputRegex("[Email]");
 *            $this->expectOutputRegex("[Password]");
 *            $this->expectOutputRegex("[Register new User]");
 *        }
 *
 *        public function testPostRegisterDuplicate()
 *        {
 *            $user = new User();
 *            $user->username = "anam";
 *            $user->email = "anam@example.com";
 *            $user->password = "anam";
 *
 *            $this->userRepository->save($user);
 *
 *            $_POST['username'] = 'anam';
 *            $_POST['email'] = 'anam@example.com';
 *            $_POST['password'] = 'anam';
 *
 *            $this->userController->postRegister();
 *
 *            $this->expectOutputRegex("[Register]");
 *            $this->expectOutputRegex("[Username]");
 *            $this->expectOutputRegex("[Email]");
 *            $this->expectOutputRegex("[Password]");
 *            $this->expectOutputRegex("[Register new User]");
 *            $this->expectOutputRegex("[Username already exists]");
 *        }
 *
 *    }
 *
 * }
 * 
 * 2. Run the test using PHPUnit to ensure all test cases pass.
 * vendor/bin/phpunit tests/Controller/UserControllerTest.php
 *
 * Make sure to adjust namespaces and paths according to your project structure.
 */