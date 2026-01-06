<?php

namespace Mukhoiran\LoginManagement\Tests\Controller;

use Mukhoiran\LoginManagement\Controller\HomeController;
use Mukhoiran\LoginManagement\App\View;
use Mukhoiran\LoginManagement\Config\Database;
use Mukhoiran\LoginManagement\Repository\SessionRepository;
use Mukhoiran\LoginManagement\Repository\UserRepository;
use Mukhoiran\LoginManagement\Service\SessionService;
use Mukhoiran\LoginManagement\Domain\User;
use Mukhoiran\LoginManagement\Domain\Session;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp():void
    {
        $this->homeController = new HomeController();
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testGuest()
    {
        $this->homeController->index();
        
        $this->expectOutputString("");
    }

    public function testUserLogin()
    {
        $user = new User();
        $user->username = "Anam";
        $user->email = "anam@example.com";
        $user->password = "Anam";
        $this->userRepository->save($user);

        $session = new Session();
        $session->session_token = uniqid();
        $session->username = $user->username;
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->session_token;
        
        $this->expectOutputRegex("[Hello, Anam]");
        
        $this->homeController->index();
    }

}