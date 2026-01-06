<?php
/**
 * 
 * Unit Test for User Profile Update Controller
 * 
 * 1. Update UserControllerTest to test updateProfile and postUpdateProfile methods (tests/Controller/UserControllerTest.php)
 * 
 * <?php
 * ...
 *         public function testUpdateProfile()
 *         {
 *             $user = new User();
 *             $user->username = "Anam";
 *             $user->email = "anam@example.com";
 *             $user->password = password_hash("anam", PASSWORD_BCRYPT);
 *             $this->userRepository->save($user);
 * 
 *             $session = new Session();
 *             $session->session_token = uniqid();
 *             $session->username = $user->username;
 *             $this->sessionRepository->save($session);
 * 
 *             $_COOKIE[SessionService::$COOKIE_NAME] = $session->session_token;
 *             $this->userController->updateProfile();
 *             $this->expectOutputRegex("[Profile]");
 *             $this->expectOutputRegex("[Username]");
 *             $this->expectOutputRegex("[Anam]");
 *             $this->expectOutputRegex("[Email]");
 *             $this->expectOutputRegex("[anam@example.com]");
 *         }
 * 
 *         public function testPostUpdateProfileSuccess()
 *         {
 *             $user = new User();
 *             $user->username = "anam";
 *             $user->email = "anam@example.com";
 *             $user->password = password_hash("anam", PASSWORD_BCRYPT);
 *             $this->userRepository->save($user);
 * 
 *             $session = new Session();
 *             $session->session_token = uniqid();
 *             $session->username = $user->username;
 *             $this->sessionRepository->save($session);
 * 
 *             $_COOKIE[SessionService::$COOKIE_NAME] = $session->session_token;
 * 
 *             $_POST['email'] = 'khoirul@example.com';
 *             $this->userController->postUpdateProfile();
 * 
 *             $this->expectOutputString("");
 * 
 *             $result = $this->userRepository->findById("anam");
 *             self::assertEquals("khoirul@example.com", $result->email);
 *         }
 * 
 *         public function testPostUpdateProfileValidationError()
 *         {
 *             $user = new User();
 *             $user->username = "anam";
 *             $user->email = "anam@example.com";
 *             $user->password = password_hash("anam", PASSWORD_BCRYPT);
 *             $this->userRepository->save($user);
 * 
 *             $session = new Session();
 *             $session->session_token = uniqid();
 *             $session->username = $user->username;
 *             $this->sessionRepository->save($session);
 * 
 *             $_COOKIE[SessionService::$COOKIE_NAME] = $session->session_token;
 * 
 *             $_POST['email'] = '';
 *             $this->userController->postUpdateProfile();
 * 
 *             $this->expectOutputRegex("[Profile]");
 *             $this->expectOutputRegex("[Username]");
 *             $this->expectOutputRegex("[anam]");
 *             $this->expectOutputRegex("[Name]");
 *             $this->expectOutputRegex("[Username and Email can not be empty]");
 *         }
 * ...
 * 
 * 2. Run the test to ensure the updateProfile and postUpdateProfile methods work correctly
 * vendor/bin/phpunit tests/Controller/UserControllerTest.php
 * 
 */