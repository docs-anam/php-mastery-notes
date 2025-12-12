<?php

namespace Mukhoiran\LoginManagement\Middleware;

use PHPUnit\Framework\TestCase;
use Mukhoiran\LoginManagement\Config\Database;
use Mukhoiran\LoginManagement\Domain\Session;
use Mukhoiran\LoginManagement\Domain\User;
use Mukhoiran\LoginManagement\Repository\SessionRepository;
use Mukhoiran\LoginManagement\Repository\UserRepository;
use Mukhoiran\LoginManagement\Service\SessionService;

class MustLoginMiddlewareTest extends TestCase
{
    private MustLoginMiddleware $middleware;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->middleware = new MustLoginMiddleware();
        putenv("mode=test");

        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testBeforeGuest()
    {
        $this->middleware->before();
        $this->expectOutputString("");
    }

    public function testBeforeLogin()
    {
        $user = new User();
        $user->username = "anam";
        $user->password = "anam";
        $user->email = "anam@example.com";
        $this->userRepository->save($user);

        $session = new Session();
        $session->session_token = uniqid();
        $session->username = $user->username;
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->session_token;

        $this->middleware->before();
        $this->expectOutputString("");
    }
}