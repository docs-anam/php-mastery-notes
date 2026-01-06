<?php
/**
 * 
 * Unit Test for User Logout Controller
 * 
 * 1. Update UserControllerTest to test logout method (tests/Controller/UserControllerTest.php)
 * 
 * <?php
 * ...
 *  use Mukhoiran\LoginManagement\Domain\Session;
 *  use Mukhoiran\LoginManagement\Repository\SessionRepository;
 *  use Mukhoiran\LoginManagement\Controller\UserController;
 *  use Mukhoiran\LoginManagement\Service\SessionService;
 *  
 *  ...
 *      private SessionRepository $sessionRepository;
 * 
 *         protected function setUp(): void
 *         {
 *              $this->userController = new UserController();
 *
 *              $this->sessionRepository = new SessionRepository(Database::getConnection());
 *              $this->sessionRepository->deleteAll();
 *
 *              $this->userRepository = new UserRepository(Database::getConnection());
 *              $this->userRepository->deleteAll();
 *
 *              putenv("mode=test");
 *        }
 *        ...
 * 
 *        public function testLogout()
 *        {
 *            $user = new User();
 *            $user->username = "anam";
 *            $user->email = "anam@example.com";
 *            $user->password = password_hash("anam", PASSWORD_BCRYPT);
 *            $this->userRepository->save($user);
 *   
 *            $session = new Session();
 *            $session->session_token = uniqid();
 *            $session->username = $user->username;
 *            $this->sessionRepository->save($session);
 *
 *            $_COOKIE[SessionService::$COOKIE_NAME] = $session->session_token;
 *            $this->userController->logout();
 *
 *            $this->expectOutputString("");
 *        }
 * ?>
 * 
 */