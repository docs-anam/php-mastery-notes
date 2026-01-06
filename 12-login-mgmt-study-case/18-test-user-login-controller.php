<?php
/**
 * Unit Test for User Login Controller
 * 
 * 1. Update the UserControllerTest to include tests for the login method (tests/Controller/UserControllerTest.php)
 * 
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Controller;
 * 
 * ...
 *        public function testLogin()
 *        {
 *            $this->userController->login();
 *
 *            $this->expectOutputRegex("[Login]");
 *            $this->expectOutputRegex("[Username]");
 *            $this->expectOutputRegex("[Password]");
 *            $this->expectOutputRegex("[Login]");
 *        }
 * 
 *        public function testLoginSuccess()
 *        {
 *            $user = new User();
 *            $user->username = "anam";
 *            $user->password = password_hash("anam", PASSWORD_BCRYPT);
 *            $user->email = "anam@example.com";
 *            $this->userRepository->save($user);
 *
 *            $_POST['username'] = 'anam';
 *            $_POST['password'] = 'anam';
 *
 *            $this->userController->postLogin();
 *
 *            $this->expectOutputString("");
 *        }
 *
 *        public function testLoginValidationError()
 *        {
 *            $_POST['username'] = '';
 *            $_POST['password'] = 'anam';
 *
 *            $this->userController->postLogin();
 *
 *            $this->expectOutputRegex("[Login]");
 *            $this->expectOutputRegex("[Username]");
 *            $this->expectOutputRegex("[Password]");
 *            $this->expectOutputRegex("[Login]");
 *        }
 *
 *        public function testLoginNotFound()
 *        {
 *            $_POST['username'] = 'notfound';
 *            $_POST['password'] = 'notfound';
 *
 *            $this->userController->postLogin();
 *
 *            $this->expectOutputRegex("[Login]");
 *            $this->expectOutputRegex("[Username]");
 *            $this->expectOutputRegex("[Password]");
 *            $this->expectOutputRegex("[Login]");
 *            $this->expectOutputRegex("[Username or password is incorrect]");
 *        }
 *
 *        public function testLoginWrongPassword()
 *        {
 *            $user = new User();
 *            $user->username = "anam";
 *            $user->password = password_hash("anam", PASSWORD_BCRYPT);
 *            $user->email = "anam@example.com";
 *            $this->userRepository->save($user);
 *            $_POST['username'] = 'anam';
 *            $_POST['password'] = 'wrongpassword';
 *            
 *            $this->userController->postLogin();
 *            
 *            $this->expectOutputRegex("[Login]");
 *            $this->expectOutputRegex("[Username]");
 *            $this->expectOutputRegex("[Password]");
 *            $this->expectOutputRegex("[Login]");
 *            $this->expectOutputRegex("[Username or password is incorrect]");
 *        }
 * ...
 * ?> 
 * 
 * 2. Run the test using PHPUnit to ensure all test cases pass.
 * vendor/bin/phpunit tests/Controller/UserControllerTest.php
 * 
 */ 