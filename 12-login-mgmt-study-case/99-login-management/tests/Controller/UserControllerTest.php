<?php

namespace Mukhoiran\LoginManagement\Controller {

    use PHPUnit\Framework\TestCase;
    use Mukhoiran\LoginManagement\Config\Database;
    use Mukhoiran\LoginManagement\Repository\UserRepository;
    use Mukhoiran\LoginManagement\Domain\User;
    use Mukhoiran\LoginManagement\Domain\Session;
    use Mukhoiran\LoginManagement\Repository\SessionRepository;
    use Mukhoiran\LoginManagement\Controller\UserController;
    use Mukhoiran\LoginManagement\Service\SessionService;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->userController = new UserController();

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }

        public function testRegister()
        {
            $this->userController->register();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Username]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Email]");
            $this->expectOutputRegex("[Register new User]");
        }

        public function testPostRegisterSuccess()
        {
            $_POST['username'] = 'anam';
            $_POST['email'] = 'anam@example.com';
            $_POST['password'] = 'anam';

            $this->userController->postRegister();

            $this->expectOutputString("");
        }

        public function testPostRegisterValidationError()
        {
            $_POST['username'] = '';
            $_POST['email'] = 'anam@example.com';
            $_POST['password'] = 'anam';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Username]");
            $this->expectOutputRegex("[Email]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
        }

        public function testPostRegisterDuplicate()
        {
            $user = new User();
            $user->username = "anam";
            $user->email = "anam@example.com";
            $user->password = "anam";

            $this->userRepository->save($user);

            $_POST['username'] = 'anam';
            $_POST['email'] = 'anam@example.com';
            $_POST['password'] = 'anam';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Username]");
            $this->expectOutputRegex("[Email]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[Username already exists]");
        }

        public function testLogin()
        {
            $this->userController->login();

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Username]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Login]");
        }

        public function testLoginSuccess()
        {
            $user = new User();
            $user->username = "anam";
            $user->password = password_hash("anam", PASSWORD_BCRYPT);
            $user->email = "anam@example.com";
            $this->userRepository->save($user);

            $_POST['username'] = 'anam';
            $_POST['password'] = 'anam';

            $this->userController->postLogin();

            $this->expectOutputString("");
        }

        public function testLoginValidationError()
        {
            $_POST['username'] = '';
            $_POST['password'] = 'anam';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Username]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Login]");
        }

        public function testLoginNotFound()
        {
            $_POST['username'] = 'notfound';
            $_POST['password'] = 'notfound';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Username]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Username or password is incorrect]");
        }

        public function testLoginWrongPassword()
        {
            $user = new User();
            $user->username = "anam";
            $user->password = password_hash("anam", PASSWORD_BCRYPT);
            $user->email = "anam@example.com";
            $this->userRepository->save($user);

            $_POST['username'] = 'anam';
            $_POST['password'] = 'wrongpassword';
            
            $this->userController->postLogin();
            
            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Username]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Username or password is incorrect]");
        }

        public function testLogout()
        {
            $user = new User();
            $user->username = "anam";
            $user->email = "anam@example.com";
            $user->password = password_hash("anam", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->session_token = uniqid();
            $session->username = $user->username;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->session_token;
            $this->userController->logout();

            $this->expectOutputString("");
        }

        public function testUpdateProfile()
        {
            $user = new User();
            $user->username = "Anam";
            $user->email = "anam@example.com";
            $user->password = password_hash("anam", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->session_token = uniqid();
            $session->username = $user->username;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->session_token;
            $this->userController->updateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Username]");
            $this->expectOutputRegex("[Anam]");
            $this->expectOutputRegex("[Email]");
            $this->expectOutputRegex("[anam@example.com]");
        }

        public function testPostUpdateProfileSuccess()
        {
            $user = new User();
            $user->username = "anam";
            $user->email = "anam@example.com";
            $user->password = password_hash("anam", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->session_token = uniqid();
            $session->username = $user->username;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->session_token;

            $_POST['email'] = 'khoirul@example.com';
            $this->userController->postUpdateProfile();

            $this->expectOutputString("");

            $result = $this->userRepository->findById("anam");
            self::assertEquals("khoirul@example.com", $result->email);
        }

        public function testPostUpdateProfileValidationError()
        {
            $user = new User();
            $user->username = "anam";
            $user->email = "anam@example.com";
            $user->password = password_hash("anam", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->session_token = uniqid();
            $session->username = $user->username;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->session_token;

            $_POST['email'] = '';
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Username]");
            $this->expectOutputRegex("[anam]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Username and Email can not be empty]");
        }

        public function testUpdatePassword()
        {
            $user = new User();
            $user->username = "Anam";
            $user->email = "anam@example.com";
            $user->password = password_hash("anam", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->session_token = uniqid();
            $session->username = $user->username;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->session_token;
            $this->userController->updatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Username]");
            $this->expectOutputRegex("[Anam]");
        }
        
        public function testPostUpdatePasswordSuccess()
        {
            $user = new User();
            $user->username = "Anam";
            $user->password = password_hash("OldPassword", PASSWORD_BCRYPT);
            $user->email = "anam@example.com";
            $this->userRepository->save($user);

            $session = new Session();
            $session->session_token = uniqid();
            $session->username = $user->username;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->session_token;

            $_POST['oldPassword'] = 'OldPassword';
            $_POST['newPassword'] = 'NewPassword';
            $this->userController->postUpdatePassword();

            $this->expectOutputString("");

            $result = $this->userRepository->findById("Anam");
            self::assertTrue(password_verify("NewPassword", $result->password));
        }

        public function testPostUpdatePasswordValidationError()
        {
            $user = new User();
            $user->username = "Anam";
            $user->password = password_hash("OldPassword", PASSWORD_BCRYPT);
            $user->email = "anam@example.com";
            $this->userRepository->save($user);

            $session = new Session();
            $session->session_token = uniqid();
            $session->username = $user->username;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->session_token;
            $_POST['oldPassword'] = '';
            $_POST['newPassword'] = 'NewPassword';
         
            $this->userController->postUpdatePassword();
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Username]");
            $this->expectOutputRegex("[Anam]");
            $this->expectOutputRegex("[Username, Old Password, and New Password can not be empty]");
        }

        public function testPostUpdatePasswordWrongOldPassword()
        {
            $user = new User();
            $user->username = "Anam";
            $user->password = password_hash("OldPassword", PASSWORD_BCRYPT);
            $user->email = "anam@example.com";
            $this->userRepository->save($user);

            $session = new Session();
            $session->session_token = uniqid();
            $session->username = $user->username;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->session_token;
            $_POST['oldPassword'] = 'WrongOldPassword';
            $_POST['newPassword'] = 'NewPassword';

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Username]");
            $this->expectOutputRegex("[Anam]");
            $this->expectOutputRegex("[Old password is incorrect]");
        }
    }
}
